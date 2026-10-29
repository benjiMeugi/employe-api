<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Vue calculée, jamais écrite. Une ligne par employé : son
     * affectation du jour.
     *
     * Elle existe pour n'écrire qu'une fois la règle « quelle est
     * l'affectation courante », dont dépendent unit_absence_schedule
     * et unit_staffing. Ces deux vues la référencent au lieu de la
     * recopier.
     *
     * Un employé peut porter plusieurs affectations valides si les
     * dates se chevauchent — une saisie qu'aucune contrainte
     * n'empêche aujourd'hui. Le ROW_NUMBER retient alors la plus
     * récente plutôt que d'en dupliquer les lignes.
     *
     * Seuls les employés sous contrat actif figurent ici : une
     * affectation qui survit à un contrat terminé ne doit compter ni
     * dans l'effectif, ni dans le calendrier.
     *
     * ATTENTION : les vues qui en dépendent cassent si on la
     * supprime. L'ordre des migrations les protège, mais un DROP
     * manuel les laisserait pointer dans le vide.
     *
     * Requiert MySQL 8.0 (fonctions de fenêtrage).
     */
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS current_assignments');

        DB::statement("
            CREATE VIEW current_assignments AS
            SELECT employee_id, unit_id, position_id, start_date
            FROM (
                SELECT
                    ce.employee_id,
                    asg.unit_id,
                    asg.position_id,
                    asg.start_date,
                    ROW_NUMBER() OVER (
                        PARTITION BY ce.employee_id
                        ORDER BY asg.start_date DESC, asg.id DESC
                    ) AS rn
                FROM assignments asg
                INNER JOIN career_events ce ON ce.id = asg.id
                WHERE asg.start_date <= CURDATE()
                  AND (asg.end_date IS NULL OR asg.end_date >= CURDATE())
                  AND EXISTS (
                      SELECT 1 FROM contracts c
                      WHERE c.employee_id = ce.employee_id
                        AND c.status = 'Active'
                  )
            ) AS ranked
            WHERE rn = 1
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS current_assignments');
    }
};
