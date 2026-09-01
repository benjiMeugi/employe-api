<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Vue calculée, jamais écrite. L'effectif de chaque unité active :
     * les employés sous contrat actif qui y sont affectés aujourd'hui.
     *
     * C'est le dénominateur auquel comparer le nombre d'absents.
     * Aucun effectif minimum n'est stocké : le RH décide sur pièces.
     *
     * Le LEFT JOIN garde les unités sans personne, avec headcount à 0.
     * Une unité vide est une information, pas une ligne à masquer.
     *
     * La vue est plate. Pour agréger une division et ses services,
     * remonter units.parent_id avec une CTE récursive au moment de la
     * requête.
     *
     * Dépend de la vue current_assignments.
     */
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS unit_staffing');

        DB::statement("
            CREATE VIEW unit_staffing AS
            SELECT
                u.id       AS unit_id,
                u.code     AS unit_code,
                u.label    AS unit_label,
                u.parent_id,
                COUNT(ca.employee_id) AS headcount
            FROM units u
            LEFT JOIN current_assignments ca ON ca.unit_id = u.id
            WHERE u.is_active = 1
            GROUP BY u.id, u.code, u.label, u.parent_id
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS unit_staffing');
    }
};
