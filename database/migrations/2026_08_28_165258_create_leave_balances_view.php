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
     * Tout est borné à l'année d'acquisition en cours, ancrée sur la
     * date d'effet du contrat actif : les absences des années passées
     * ne polluent ni le consommé ni le compteur d'occurrences.
     *
     * Les octrois ne sont PAS filtrés par année : c'est leur date
     * d'expiration qui décide de leur validité. Pendant le délai de
     * grâce, ceux de l'année précédente comptent encore.
     */
    public function up(): void
    {
        DB::statement("
            CREATE VIEW leave_balances AS
            SELECT
                e.id AS employee_id,
                at.id AS absence_type_id,

                CASE
                    WHEN at.quota_basis = 'per_occurrence'
                        THEN at.quota_days * GREATEST(COALESCE(cons.occurrence_count, 0), 1)
                    ELSE at.quota_days
                END AS quota_days,

                COALESCE(cons.occurrence_count, 0) AS occurrence_count,

                CASE
                    WHEN at.accrual_rate_per_month > 0
                        THEN COALESCE(acq.granted, 0)
                    WHEN at.quota_basis = 'per_occurrence'
                        THEN GREATEST(
                            at.quota_days * GREATEST(COALESCE(cons.occurrence_count, 0), 1),
                            COALESCE(cons.consumed_own, 0)
                        )
                    ELSE GREATEST(at.quota_days, COALESCE(cons.consumed_own, 0))
                END AS acquired_days,

                COALESCE(cons.consumed_own, 0) AS consumed_own_type,

                CASE
                    WHEN at.accrual_rate_per_month > 0 THEN COALESCE(imp.imputed, 0)
                    ELSE 0
                END AS consumed_from_other_types,

                COALESCE(cons.consumed_own, 0)
                    + CASE
                        WHEN at.accrual_rate_per_month > 0 THEN COALESCE(imp.imputed, 0)
                        ELSE 0
                      END AS consumed_total,

                COALESCE(cons.transferred, 0) AS transferred_to_annual_leave,
                COALESCE(expd.expired, 0) AS expired_days,

                CASE
                    WHEN at.accrual_rate_per_month > 0
                        THEN COALESCE(acq.granted, 0)
                             - COALESCE(cons.consumed_own, 0)
                             - COALESCE(imp.imputed, 0)
                    WHEN at.quota_basis = 'per_occurrence'
                        THEN GREATEST(
                                at.quota_days * GREATEST(COALESCE(cons.occurrence_count, 0), 1),
                                COALESCE(cons.consumed_own, 0)
                             ) - COALESCE(cons.consumed_own, 0)
                    ELSE GREATEST(at.quota_days, COALESCE(cons.consumed_own, 0))
                         - COALESCE(cons.consumed_own, 0)
                END AS balance

            FROM employes e
            CROSS JOIN absence_types at

            LEFT JOIN (
                SELECT
                    x.employee_id,
                    CASE
                        WHEN DATE_ADD(x.cs, INTERVAL (YEAR(CURDATE()) - YEAR(x.cs)) YEAR) > CURDATE()
                            THEN DATE_ADD(x.cs, INTERVAL (YEAR(CURDATE()) - YEAR(x.cs) - 1) YEAR)
                        ELSE DATE_ADD(x.cs, INTERVAL (YEAR(CURDATE()) - YEAR(x.cs)) YEAR)
                    END AS year_start
                FROM (
                    SELECT employee_id, MAX(start_date) AS cs
                    FROM contracts
                    WHERE status = 'Active'
                    GROUP BY employee_id
                ) AS x
            ) AS anc ON anc.employee_id = e.id

            LEFT JOIN (
                SELECT employee_id, absence_type_id, SUM(granted_days) AS granted
                FROM leave_grants
                WHERE expiration_date IS NULL OR expiration_date >= CURDATE()
                GROUP BY employee_id, absence_type_id
            ) AS acq ON acq.employee_id = e.id AND acq.absence_type_id = at.id

            LEFT JOIN (
                SELECT employee_id, absence_type_id, SUM(granted_days) AS expired
                FROM leave_grants
                WHERE expiration_date IS NOT NULL AND expiration_date < CURDATE()
                GROUP BY employee_id, absence_type_id
            ) AS expd ON expd.employee_id = e.id AND expd.absence_type_id = at.id

            LEFT JOIN (
                SELECT
                    ce.employee_id,
                    a.absence_type_id,
                    COUNT(*) AS occurrence_count,
                    SUM(a.duration_days) AS consumed_own,
                    SUM(a.deducted_days) AS transferred
                FROM absences a
                INNER JOIN career_events ce ON ce.id = a.id
                INNER JOIN (
                    SELECT
                        y.employee_id,
                        CASE
                            WHEN DATE_ADD(y.cs, INTERVAL (YEAR(CURDATE()) - YEAR(y.cs)) YEAR) > CURDATE()
                                THEN DATE_ADD(y.cs, INTERVAL (YEAR(CURDATE()) - YEAR(y.cs) - 1) YEAR)
                            ELSE DATE_ADD(y.cs, INTERVAL (YEAR(CURDATE()) - YEAR(y.cs)) YEAR)
                        END AS year_start
                    FROM (
                        SELECT employee_id, MAX(start_date) AS cs
                        FROM contracts
                        WHERE status = 'Active'
                        GROUP BY employee_id
                    ) AS y
                ) AS a1 ON a1.employee_id = ce.employee_id
                WHERE a.duration_days IS NOT NULL
                  AND a.start_date >= a1.year_start
                GROUP BY ce.employee_id, a.absence_type_id
            ) AS cons ON cons.employee_id = e.id AND cons.absence_type_id = at.id

            LEFT JOIN (
                SELECT ce.employee_id, SUM(a.deducted_days) AS imputed
                FROM absences a
                INNER JOIN career_events ce ON ce.id = a.id
                INNER JOIN (
                    SELECT
                        z.employee_id,
                        CASE
                            WHEN DATE_ADD(z.cs, INTERVAL (YEAR(CURDATE()) - YEAR(z.cs)) YEAR) > CURDATE()
                                THEN DATE_ADD(z.cs, INTERVAL (YEAR(CURDATE()) - YEAR(z.cs) - 1) YEAR)
                            ELSE DATE_ADD(z.cs, INTERVAL (YEAR(CURDATE()) - YEAR(z.cs)) YEAR)
                        END AS year_start
                    FROM (
                        SELECT employee_id, MAX(start_date) AS cs
                        FROM contracts
                        WHERE status = 'Active'
                        GROUP BY employee_id
                    ) AS z
                ) AS a2 ON a2.employee_id = ce.employee_id
                WHERE a.deducted_days > 0
                  AND a.start_date >= a2.year_start
                GROUP BY ce.employee_id
            ) AS imp ON imp.employee_id = e.id AND at.accrual_rate_per_month > 0

            WHERE at.is_calendar_based = 1
              AND anc.year_start IS NOT NULL
              AND (at.gender_restriction = 'none' OR at.gender_restriction = e.gender)
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS leave_balances');
    }
};
