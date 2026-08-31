<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Vue calculée : où en est-on dans une absence en cours ?
     *
     * Rien à voir avec le solde — ici on ne regarde ni octrois ni
     * quota, seulement la progression d'une absence précise entre sa
     * date de début, sa date de fin et aujourd'hui.
     *
     * Ne retient que les absences en cours à cette date : celles
     * terminées ou pas encore commencées n'y figurent pas.
     */
    public function up(): void
    {
        DB::statement("
            CREATE VIEW ongoing_absences AS
            SELECT
                a.id AS absence_id,
                ce.employee_id,
                a.absence_type_id,
                a.start_date,
                a.end_date,
                a.duration_days AS total_days,
                DATEDIFF(CURDATE(), a.start_date) + 1 AS elapsed_days,
                DATEDIFF(a.end_date, CURDATE()) AS remaining_days,
                DATE_ADD(a.end_date, INTERVAL 1 DAY) AS return_date
            FROM absences a
            INNER JOIN career_events ce ON ce.id = a.id
            WHERE a.duration_days IS NOT NULL
              AND a.start_date <= CURDATE()
              AND a.end_date >= CURDATE()
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS ongoing_absences');
    }
};
