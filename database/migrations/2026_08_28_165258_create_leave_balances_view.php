<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Vue calculée, jamais écrite. Une ligne par employé et par type
     * de CONGÉ (is_calendar_based = true) — les permissions n'ont pas
     * de solde, leur droit naît à l'événement.
     *
     * ─────────────────────────────────────────────────────────────
     * LES COLONNES : UN TRIO, PUIS DU DÉTAIL
     *
     * Trois colonnes se lisent ensemble et s'additionnent, quel que
     * soit le régime du type :
     *
     *   acquired_days − consumed_days = balance
     *
     *   acquired_days   ce à quoi l'employé a droit AUJOURD'HUI.
     *                   Pour un type accumulé : les octrois encore
     *                   valides. Les octrois expirés n'y sont pas.
     *   consumed_days   ce qu'il a pris SUR CE DROIT-LÀ. Absences du
     *                   type et excédents imputés depuis les autres.
     *   balance         ce qui reste utilisable.
     *
     * Les quatre suivantes sont du détail de période. Elles ne
     * rentrent PAS dans la soustraction ci-dessus et portent toutes
     * le suffixe _this_year pour qu'on ne les confonde pas :
     *
     *   consumed_this_year      pris depuis le début de l'année
     *                           d'acquisition en cours.
     *   imputed_this_year       reçu des autres types cette année.
     *   transferred_this_year   envoyé vers le congé accumulé.
     *   expired_this_year       perdu faute d'avoir été pris.
     *
     * Pourquoi consumed_days diffère de consumed_this_year : un
     * employé qui a pris 12 jours dont 10 sur des octrois désormais
     * expirés n'a entamé que 2 jours de son droit actuel. Les deux
     * chiffres sont vrais, ils répondent à deux questions.
     *
     * ─────────────────────────────────────────────────────────────
     * TROIS RÉGIMES
     *
     *   accrual_rate_per_month > 0   le droit vient des LeaveGrant.
     *   quota_basis = per_occurrence quota_days × occurrences de l'année.
     *   quota_basis = per_period     quota fixe sur l'année.
     *
     * Sur les deux régimes non accumulés, GREATEST(quota, consommé)
     * absorbe la générosité du RH : un excédent non déductible élève
     * l'acquis au niveau du consommé plutôt que de creuser le solde.
     * L'excédent déductible part dans deducted_days et se retrouve en
     * imputed_this_year sur le congé accumulé.
     *
     * Les types non accumulés se lisent sur l'année d'acquisition en
     * cours, ancrée sur la date d'effet du contrat actif et bornée des
     * DEUX côtés : une absence posée pour la période suivante ne doit
     * pas grever la période courante.
     *
     * ─────────────────────────────────────────────────────────────
     * LE CONGÉ ACCUMULÉ : TROIS CUMULS, PAS D'APPARIEMENT
     *
     * Un octroi vit selon sa date d'expiration, pas selon son année.
     * En expiration_mode = per_year avec délai de grâce, deux
     * générations sont valides en même temps pendant toute la durée du
     * délai, et une absence posée dans ce chevauchement épuise la plus
     * ancienne puis déborde sur la suivante.
     *
     * Reconstituer cet appariement absence par octroi est impossible
     * sans une table de liaison. Il n'est pas nécessaire : seul le
     * total perdu à l'expiration manque pour boucler le calcul.
     *
     *   balance = total octroyé − total consommé − perte cumulée
     *
     * Et la perte cumulée s'obtient sans récursion. À chaque date
     * d'expiration e, la perte de l'instant vaut l'offre morte à cette
     * date moins la demande déjà servie moins ce qui était déjà perdu.
     * Une récurrence de la forme L(e) = MAX(L(e−1), offre − demande)
     * n'est rien d'autre qu'un maximum glissant :
     *
     *   L(e) = GREATEST(0, MAX sur e' ≤ e de (offre_morte(e') − demande(e')))
     *
     * Le débordement du délai de grâce s'annule de lui-même : les
     * absences de la fenêtre de chevauchement figurent dans demande(e),
     * donc elles empêchent la perte de se déclencher à tort sur les
     * jours qu'elles ont réellement consommés.
     *
     * consumed_days se déduit ensuite du solde plutôt que d'être
     * compté à part : c'est la seule façon de garantir que la
     * soustraction affichée tombe juste.
     *
     * ─────────────────────────────────────────────────────────────
     * Requiert MySQL 8.0 (CTE + fonctions de fenêtrage).
     */
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS leave_balances');

        DB::statement("
            CREATE VIEW leave_balances AS
            WITH

            anchor AS (
                SELECT
                    c.employee_id,
                    CASE
                        WHEN DATE_ADD(c.cs, INTERVAL (YEAR(CURDATE()) - YEAR(c.cs)) YEAR) > CURDATE()
                            THEN DATE_ADD(c.cs, INTERVAL (YEAR(CURDATE()) - YEAR(c.cs) - 1) YEAR)
                        ELSE DATE_ADD(c.cs, INTERVAL (YEAR(CURDATE()) - YEAR(c.cs)) YEAR)
                    END AS year_start
                FROM (
                    SELECT employee_id, MAX(start_date) AS cs
                    FROM contracts
                    WHERE status = 'Active'
                    GROUP BY employee_id
                ) AS c
            ),

            accruing AS (
                SELECT id FROM absence_types WHERE accrual_rate_per_month > 0
            ),

            -- Tout ce qui puise dans le réservoir accumulé, daté :
            -- les absences du type lui-même, et les excédents prélevés
            -- depuis les autres types.
            demand AS (
                SELECT
                    ce.employee_id,
                    acc.id AS absence_type_id,
                    a.start_date AS demand_date,
                    SUM(
                        CASE WHEN a.absence_type_id =  acc.id THEN COALESCE(a.duration_days, 0) ELSE 0 END
                      + CASE WHEN a.absence_type_id <> acc.id THEN COALESCE(a.deducted_days, 0) ELSE 0 END
                    ) AS days
                FROM absences a
                INNER JOIN career_events ce ON ce.id = a.id
                CROSS JOIN accruing acc
                WHERE a.duration_days IS NOT NULL
                GROUP BY ce.employee_id, acc.id, a.start_date
            ),

            -- Une ligne par date d'expiration réelle : offre morte à
            -- cette date, demande servie jusque-là.
            expiry_state AS (
                SELECT
                    ev.employee_id,
                    ev.absence_type_id,
                    ev.e,
                    (
                        SELECT COALESCE(SUM(g.granted_days), 0)
                        FROM leave_grants g
                        WHERE g.employee_id     = ev.employee_id
                          AND g.absence_type_id = ev.absence_type_id
                          AND g.expiration_date IS NOT NULL
                          AND g.expiration_date <= ev.e
                    ) AS supply_dead,
                    (
                        SELECT COALESCE(SUM(d.days), 0)
                        FROM demand d
                        WHERE d.employee_id     = ev.employee_id
                          AND d.absence_type_id = ev.absence_type_id
                          AND d.demand_date     <= ev.e
                    ) AS demand_served
                FROM (
                    SELECT DISTINCT employee_id, absence_type_id, expiration_date AS e
                    FROM leave_grants
                    WHERE expiration_date IS NOT NULL
                ) AS ev
            ),

            -- Perte cumulée : maximum glissant de (offre morte − demande).
            -- Non décroissante par construction, ce qui autorise le MAX
            -- conditionnel plus bas pour lire sa valeur à une date donnée.
            loss AS (
                SELECT
                    employee_id,
                    absence_type_id,
                    e,
                    GREATEST(0, MAX(supply_dead - demand_served) OVER (
                        PARTITION BY employee_id, absence_type_id
                        ORDER BY e
                        ROWS UNBOUNDED PRECEDING
                    )) AS lost_cumulative
                FROM expiry_state
            ),

            loss_summary AS (
                SELECT
                    l.employee_id,
                    l.absence_type_id,
                    COALESCE(MAX(CASE WHEN l.e <= CURDATE()     THEN l.lost_cumulative END), 0) AS lost_to_date,
                    COALESCE(MAX(CASE WHEN l.e <  an.year_start THEN l.lost_cumulative END), 0) AS lost_before_year
                FROM loss l
                INNER JOIN anchor an ON an.employee_id = l.employee_id
                GROUP BY l.employee_id, l.absence_type_id
            ),

            grants_total AS (
                SELECT
                    employee_id,
                    absence_type_id,
                    SUM(granted_days) AS granted_ever,
                    SUM(CASE
                        WHEN expiration_date IS NULL OR expiration_date >= CURDATE()
                        THEN granted_days ELSE 0
                    END) AS granted_alive
                FROM leave_grants
                GROUP BY employee_id, absence_type_id
            ),

            demand_total AS (
                SELECT employee_id, absence_type_id, SUM(days) AS consumed_ever
                FROM demand
                GROUP BY employee_id, absence_type_id
            ),

            -- Consommation de l'année en cours : sert aux types non
            -- accumulés pour le calcul, et à tous pour l'affichage.
            year_stats AS (
                SELECT
                    ce.employee_id,
                    a.absence_type_id,
                    COUNT(*) AS occurrence_count,
                    SUM(a.duration_days) AS consumed_year,
                    SUM(a.deducted_days) AS transferred
                FROM absences a
                INNER JOIN career_events ce ON ce.id = a.id
                INNER JOIN anchor an ON an.employee_id = ce.employee_id
                WHERE a.duration_days IS NOT NULL
                  AND a.start_date >= an.year_start
                  AND a.start_date <  DATE_ADD(an.year_start, INTERVAL 1 YEAR)
                GROUP BY ce.employee_id, a.absence_type_id
            ),

            year_imputed AS (
                SELECT
                    ce.employee_id,
                    SUM(a.deducted_days) AS imputed_year
                FROM absences a
                INNER JOIN career_events ce ON ce.id = a.id
                INNER JOIN anchor an ON an.employee_id = ce.employee_id
                WHERE a.deducted_days > 0
                  AND a.start_date >= an.year_start
                  AND a.start_date <  DATE_ADD(an.year_start, INTERVAL 1 YEAR)
                GROUP BY ce.employee_id
            )

            SELECT
                e.id  AS employee_id,
                at.id AS absence_type_id,

                CASE
                    WHEN at.quota_basis = 'per_occurrence'
                        THEN at.quota_days * GREATEST(COALESCE(ys.occurrence_count, 0), 1)
                    ELSE at.quota_days
                END AS quota_days,

                COALESCE(ys.occurrence_count, 0) AS occurrence_count,

                /* ── LE TRIO QUI S'ADDITIONNE ────────────────────── */

                CASE
                    WHEN at.accrual_rate_per_month > 0
                        THEN COALESCE(gt.granted_alive, 0)
                    WHEN at.quota_basis = 'per_occurrence'
                        THEN GREATEST(
                            at.quota_days * GREATEST(COALESCE(ys.occurrence_count, 0), 1),
                            COALESCE(ys.consumed_year, 0)
                        )
                    ELSE GREATEST(at.quota_days, COALESCE(ys.consumed_year, 0))
                END AS acquired_days,

                -- Déduit du solde plutôt que compté à part : c'est ce
                -- qui garantit que la soustraction tombe juste.
                CASE
                    WHEN at.accrual_rate_per_month > 0
                        THEN COALESCE(gt.granted_alive, 0)
                             - (COALESCE(gt.granted_ever, 0)
                                - COALESCE(dt.consumed_ever, 0)
                                - COALESCE(ls.lost_to_date, 0))
                    ELSE COALESCE(ys.consumed_year, 0)
                END AS consumed_days,

                CASE
                    WHEN at.accrual_rate_per_month > 0
                        THEN COALESCE(gt.granted_ever, 0)
                             - COALESCE(dt.consumed_ever, 0)
                             - COALESCE(ls.lost_to_date, 0)
                    WHEN at.quota_basis = 'per_occurrence'
                        THEN GREATEST(
                                at.quota_days * GREATEST(COALESCE(ys.occurrence_count, 0), 1),
                                COALESCE(ys.consumed_year, 0)
                             ) - COALESCE(ys.consumed_year, 0)
                    ELSE GREATEST(at.quota_days, COALESCE(ys.consumed_year, 0))
                         - COALESCE(ys.consumed_year, 0)
                END AS balance,

                /* ── LE DÉTAIL DE LA PÉRIODE ─────────────────────── */

                COALESCE(ys.consumed_year, 0) AS consumed_this_year,

                CASE
                    WHEN at.accrual_rate_per_month > 0 THEN COALESCE(yi.imputed_year, 0)
                    ELSE 0
                END AS imputed_this_year,

                COALESCE(ys.transferred, 0) AS transferred_this_year,

                CASE
                    WHEN at.accrual_rate_per_month > 0
                        THEN GREATEST(0, COALESCE(ls.lost_to_date, 0) - COALESCE(ls.lost_before_year, 0))
                    ELSE 0
                END AS expired_this_year

            FROM employes e
            CROSS JOIN absence_types at
            INNER JOIN anchor anc ON anc.employee_id = e.id

            LEFT JOIN grants_total gt
                   ON gt.employee_id = e.id AND gt.absence_type_id = at.id
            LEFT JOIN demand_total dt
                   ON dt.employee_id = e.id AND dt.absence_type_id = at.id
            LEFT JOIN loss_summary ls
                   ON ls.employee_id = e.id AND ls.absence_type_id = at.id
            LEFT JOIN year_stats ys
                   ON ys.employee_id = e.id AND ys.absence_type_id = at.id
            LEFT JOIN year_imputed yi
                   ON yi.employee_id = e.id AND at.accrual_rate_per_month > 0

            WHERE at.is_calendar_based = 1
              AND (at.gender_restriction = 'none' OR at.gender_restriction = e.gender)
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS leave_balances');
    }
};
