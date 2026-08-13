<?php

namespace Database\Seeders;

use App\Models\CareerEvent;
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
    /**
     * Crée une ligne career_events + sa ligne fille correspondante,
     * avec le même id partagé — reproduit exactement ce que fait
     * CareerEventChildRepository::store() côté API.
     */
    private function createEvent(string $event, array $commonAttributes, string $childClass, array $childAttributes)
    {
        $careerEvent = CareerEvent::create(array_merge($commonAttributes, [
            'event' => $event,
        ]));

        $child = new $childClass($childAttributes);
        $child->id = $careerEvent->id;
        $child->exists = false;
        $child->save();

        return $child;
    }

    public function run(): void
    {
        $employees = Employe::inRandomOrder()->take(5)->get();
        $positions = Position::inRandomOrder()->take(3)->get();
        $units = Unit::inRandomOrder()->take(3)->get();

        if ($employees->isEmpty() || $positions->isEmpty() || $units->isEmpty()) {
            $this->command->warn(
                'CareerEventSeeder: employees/positions/units introuvables — ' .
                'assure-toi que EmployeeSeeder, PositionSeeder et UnitSeeder tournent avant celui-ci.'
            );
            return;
        }

        foreach ($employees as $employee) {
            // Une affectation, quand on l'aura ajoutée (dépend d'Assignment déjà en place ou non)

            // Une promotion
            $this->createEvent(
                'promotion',
                [
                    'employe_id' => $employee->id,
                    'event_date' => now()->subMonths(rand(1, 24)),
                    'comment' => 'Promotion suite à évaluation annuelle',
                ],
                Promotion::class,
                [
                    'previous_position_id' => $positions->random()->id,
                    'new_position_id' => $positions->random()->id,
                    'previous_classification_id' => null,
                    'new_classification_id' => null,
                    'reason' => 'Évolution de carrière',
                ]
            );

            // Une sanction (pas systématique, seulement pour certains employés)
            if (rand(0, 1)) {
                $this->createEvent(
                    'sanction',
                    [
                        'employe_id' => $employee->id,
                        'event_date' => now()->subMonths(rand(1, 12)),
                        'comment' => null,
                    ],
                    Sanction::class,
                    [
                        'sanction_type' => collect(['Warning', 'Suspension', 'Demotion'])->random(),
                        'reason' => 'Retards répétés',
                        'duration_days' => rand(1, 5),
                    ]
                );
            }
        }

        // Une retraite et un licenciement, sur deux employés distincts
        if ($employees->count() >= 2) {
            $this->createEvent(
                'retirement',
                [
                    'employe_id' => $employees[0]->id,
                    'event_date' => now()->subMonths(2),
                    'comment' => null,
                ],
                Retirement::class,
                [
                    'effective_date' => now()->addMonths(1),
                    'reason' => 'Départ à la retraite',
                ]
            );

            $this->createEvent(
                'dismissal',
                [
                    'employe_id' => $employees[1]->id,
                    'event_date' => now()->subMonths(3),
                    'comment' => null,
                ],
                Dismissal::class,
                [
                    'reason' => 'Faute grave',
                    'effective_date' => now()->subMonths(3),
                    'severance_pay' => 500000,
                    'notice_days' => 0,
                ]
            );
        }
    }
}
