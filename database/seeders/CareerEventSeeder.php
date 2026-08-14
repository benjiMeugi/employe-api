<?php

namespace Database\Seeders;

use App\Models\Dismissal;
use App\Models\Employe;
use App\Models\Position;
use App\Models\Promotion;
use App\Models\Retirement;
use App\Models\Sanction;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class CareerEventSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employe::inRandomOrder()->take(5)->get();
        $positions = Position::inRandomOrder()->take(3)->get();
        $units = Unit::inRandomOrder()->take(3)->get();

        if ($employees->isEmpty() || $positions->isEmpty() || $units->isEmpty()) {
            $this->command->warn(
                'CareerEventSeeder: employés/postes/unités introuvables — ' .
                'assure-toi que EmployeSeeder, PositionSeeder et UnitSeeder tournent avant celui-ci.'
            );
            return;
        }

        foreach ($employees as $employee) {
            // Le trait BelongsToCareerEvent crée automatiquement la ligne
            // career_events correspondante — plus besoin de le faire à la main.
            Promotion::create([
                'employee_id' => $employee->id,
                'event_date' => now()->subMonths(rand(1, 24)),
                'previous_position_id' => $positions->random()->id,
                'new_position_id' => $positions->random()->id,
                'previous_classification_id' => null,
                'new_classification_id' => null,
                'reason' => 'Évolution de carrière',
            ]);

            // Une sanction, pas systématique
            if (rand(0, 1)) {
                Sanction::create([
                    'employee_id' => $employee->id,
                    'event_date' => now()->subMonths(rand(1, 12)),
                    'sanction_type' => collect(['Warning', 'Suspension', 'Demotion'])->random(),
                    'reason' => 'Retards répétés',
                    'duration_days' => rand(1, 5),
                ]);
            }
        }

        // Une retraite et un licenciement, sur deux employés distincts
        if ($employees->count() >= 2) {
            Retirement::create([
                'employee_id' => $employees[0]->id,
                'event_date' => now()->subMonths(2),
                'effective_date' => now()->addMonths(1),
                'reason' => 'Départ à la retraite',
            ]);

            Dismissal::create([
                'employee_id' => $employees[1]->id,
                'event_date' => now()->subMonths(3),
                'reason' => 'Faute grave',
                'effective_date' => now()->subMonths(3),
                'severance_pay' => 500000,
                'notice_days' => 0,
            ]);
        }
    }
}
