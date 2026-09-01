<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Jours fériés togolais 2026.
     *
     * is_recurring distingue ce qui pourra être généré automatiquement
     * pour les années suivantes de ce qui devra être ressaisi :
     *
     *   true  — date fixe, même jour/mois chaque année
     *   false — date mobile : soit calculable (Pâques et ses dérivés),
     *           soit annoncée par observation lunaire (fêtes musulmanes),
     *           auquel cas seule une saisie manuelle est possible.
     */
    public function run(): void
    {
        $holidays = [
            // Fixes — générables d'année en année
            ['2026-01-01', "Jour de l'An", true],
            ['2026-01-13', 'Jour de la Libération', true],
            ['2026-04-27', "Fête de l'Indépendance", true],
            ['2026-05-01', 'Fête du Travail', true],
            ['2026-06-21', 'Journée des Martyrs', true],
            ['2026-08-15', 'Assomption', true],
            ['2026-11-01', 'Toussaint', true],
            ['2026-12-25', 'Noël', true],

            // Mobiles calculables — dérivées de Pâques
            ['2026-04-06', 'Lundi de Pâques', false],
            ['2026-05-14', 'Ascension', false],
            ['2026-05-25', 'Lundi de Pentecôte', false],

            // Mobiles lunaires — annoncées par observation, jamais
            // calculables : à ressaisir chaque année.
            ['2026-03-20', 'Aïd el-Fitr (Korité)', false],
            ['2026-05-27', 'Aïd el-Kébir (Tabaski)', false],
            ['2026-08-26', 'Mouloud', false],
        ];

        foreach ($holidays as [$date, $label, $isRecurring]) {
            Holiday::updateOrCreate(
                ['country_code' => 'TG', 'date' => $date],
                ['label' => $label, 'is_recurring' => $isRecurring]
            );
        }
    }
}
