<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Vue calculée, jamais écrite. Une ligne par occupation du
     * calendrier, acquise ou seulement demandée :
     *
     *   entry_status = 'confirmed'  une Absence existe, donc validée :
     *                               le poste sera vide, c'est acquis.
     *   entry_status = 'pending'    une AbsenceRequest attend une
     *                               décision — celle qu'on arbitre, ou
     *                               une concurrente sur la même période.
     *
     * Aucun doublon : une demande approuvée devient une Absence et
     * quitte le statut pending. Les demandes rejetées n'apparaissent
     * nulle part.
     *
     * Les permissions horaires sont écartées. Quelques heures dans une
     * journée ne retirent personne de l'effectif, et les compter comme
     * une absence d'un jour fausserait l'arbitrage.
     *
     * Aucune date n'est filtrée ici : c'est l'appelant qui borne sa
     * fenêtre, avec start_date <= :to AND end_date >= :from. Une
     * absence commencée avant la fenêtre et finie après occupe bien le
     * calendrier, elle doit rester visible.
     *
     * unit_id est NULL si l'employé n'a aucune affectation courante :
     * la ligne reste visible et le NULL signale le trou plutôt que de
     * l'effacer.
     *
     * Dépend de la vue current_assignments.
     */
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS unit_absence_schedule');

        DB::statement("
            CREATE VIEW unit_absence_schedule AS

            SELECT
                CONCAT('absence-', a.id) AS entry_key,
                'confirmed'              AS entry_status,
                a.id                     AS absence_id,
                a.absence_request_id     AS request_id,
                ce.employee_id,
                ca.unit_id,
                ca.position_id,
                a.absence_type_id,
                t.code                   AS absence_type_code,
                t.label                  AS absence_type_label,
                a.start_date,
                a.end_date,
                a.duration_days,
                NULL                     AS reason,
                NULL                     AS submitted_at
            FROM absences a
            INNER JOIN career_events ce ON ce.id = a.id
            INNER JOIN absence_types t  ON t.id = a.absence_type_id
            LEFT JOIN current_assignments ca ON ca.employee_id = ce.employee_id
            WHERE t.duration_unit <> 'hour'
              AND a.duration_days IS NOT NULL

            UNION ALL

            SELECT
                CONCAT('request-', r.id),
                'pending',
                NULL,
                r.id,
                r.employee_id,
                ca.unit_id,
                ca.position_id,
                r.absence_type_id,
                t.code,
                t.label,
                r.requested_start_date,
                r.requested_end_date,
                r.requested_days,
                r.reason,
                r.created_at
            FROM absence_requests r
            INNER JOIN absence_types t ON t.id = r.absence_type_id
            LEFT JOIN current_assignments ca ON ca.employee_id = r.employee_id
            WHERE r.status = 'pending'
              AND t.duration_unit <> 'hour'
              AND r.requested_days IS NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS unit_absence_schedule');
    }
};
