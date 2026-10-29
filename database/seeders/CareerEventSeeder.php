<?php

namespace Database\Seeders;

use App\Models\Dismissal;
use App\Models\Employe;
use App\Models\Position;
use App\Models\Promotion;
use App\Models\Retirement;
use App\Models\Sanction;
use Illuminate\Database\Seeder;

class CareerEventSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employe::query()
            ->orderBy('id')
            ->take(8)
            ->get();

        $positions = Position::all();

        if ($employees->isEmpty() || $positions->count() < 2) {
            $this->command->warn(
                'CareerEventSeeder: employés ou positions introuvables.'
            );

            return;
        }

        /*
         * ============================================================
         * PROMOTIONS
         * ============================================================
         *
         * Pour chaque employé sélectionné :
         *
         * ancienne position
         *        ↓
         * nouvelle position = position actuelle de l'employé
         *
         * Cela permet de conserver une histoire cohérente.
         */

        foreach ($employees->take(5) as $employee) {

            $currentPosition = $employee->position;

            if (! $currentPosition) {
                continue;
            }

            $previousPosition = $positions
                ->where('id', '!=', $currentPosition->id)
                ->random();

            Promotion::create([
                'employee_id' => $employee->id,
                'event_date' => now()->subMonths(rand(6, 24)),
                'previous_position_id' => $previousPosition->id,
                'new_position_id' => $currentPosition->id,
                'previous_classification_id' => $previousPosition->classification_id,
                'new_classification_id' => $currentPosition->classification_id,
                'reason' => 'Évolution de carrière',
            ]);
        }

        /*
         * ============================================================
         * SANCTIONS
         * ============================================================
         */

        foreach ($employees->take(4) as $employee) {

            Sanction::create([
                'employee_id' => $employee->id,
                'event_date' => now()->subMonths(rand(1, 12)),
                'sanction_type' => collect([
                    'Warning',
                    'Suspension',
                    'Demotion',
                ])->random(),
                'reason' => 'Retards répétés',
                'duration_days' => rand(1, 5),
            ]);
        }

        /*
         * ============================================================
         * RETRAITE
         * ============================================================
         *
         * Un seul employé.
         */

        if ($employees->count() >= 1) {

            $employee = $employees[0];

            Retirement::create([
                'employee_id' => $employee->id,
                'event_date' => now()->subMonths(2),
                'effective_date' => now()->addMonth(),
                'reason' => 'Départ à la retraite',
            ]);
        }

        /*
         * ============================================================
         * LICENCIEMENT
         * ============================================================
         *
         * Employé différent de celui de la retraite.
         */

        if ($employees->count() >= 2) {

            $employee = $employees[1];

            $eventDate = now()->subMonths(3);

            Dismissal::create([
                'employee_id' => $employee->id,
                'event_date' => $eventDate,
                'reason' => 'Faute grave',
                'effective_date' => $eventDate,
                'severance_pay' => 500000,
                'notice_days' => 0,
            ]);
        }

        $this->command->info(
            'Événements de carrière créés avec succès.'
        );
    }
}
