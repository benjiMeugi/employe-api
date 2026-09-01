<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Employe;
use App\Models\Position;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employe::query()
            ->orderBy('id')
            ->get();

        $positions = Position::all();
        $units = Unit::all();

        if ($employees->isEmpty() || $positions->isEmpty() || $units->isEmpty()) {
            $this->command->warn(
                'AssignmentSeeder: employés, postes ou unités introuvables.'
            );

            return;
        }

        foreach ($employees as $index => $employee) {

            /*
             * ---------------------------------------------------------
             * Affectation historique
             * ---------------------------------------------------------
             */

            $position = $positions->where(
                'id',
                $employee->position_id
            )->first();

            if (! $position) {
                continue;
            }

            /*
             * On choisit l'unité en fonction du type de poste.
             *
             * Les postes RH vont plutôt dans RH/Recrutement,
             * les postes techniques dans Direction Technique/Maintenance.
             */

            if (str_contains(strtolower($position->label), 'rh')) {
                $unit = Unit::where('code', 'RECRUT')->first()
                    ?? Unit::where('code', 'RH')->first();
            } elseif (
                str_contains(strtolower($position->label), 'informatique')
                || str_contains(strtolower($position->label), 'logiciel')
                || str_contains(strtolower($position->label), 'technicien')
            ) {
                $unit = Unit::where('code', 'MAINT')->first()
                    ?? Unit::where('code', 'DTQ')->first();
            } else {
                $unit = $units->random();
            }

            if (! $unit) {
                continue;
            }

            /*
             * Date de début basée sur l'embauche.
             */
            $startDate = $employee->hire_date
                ? \Carbon\Carbon::parse($employee->hire_date)
                : now()->subYears(2);

            /*
             * Certains employés ont une ancienne affectation
             * et une nouvelle affectation.
             */
            if ($index % 3 === 0) {

                $oldUnit = $units
                    ->where('id', '!=', $unit->id)
                    ->random();

                Assignment::create(
                    [
                        'employee_id' => $employee->id,
                        'start_date' => $startDate->copy()->toDateString(),
                        'position_id' => $position->id,
                        'unit_id' => $oldUnit->id,
                        'event_date' => $startDate->copy()->toDateString(),
                        'reason' => 'Première affectation',
                        'end_date' => now()
                            ->subYears(2)
                            ->toDateString(),
                    ]
                );

                /*
                 * Nouvelle affectation.
                 */
                $newStartDate = now()
                    ->subYears(2)
                    ->addMonths(rand(3, 12));

                Assignment::create(
                    [
                        'employee_id' => $employee->id,
                        'start_date' => $newStartDate->toDateString(),
                        'position_id' => $position->id,
                        'unit_id' => $unit->id,
                        'event_date' => $newStartDate->toDateString(),
                        'reason' => 'Changement d’affectation',
                        'end_date' => null,
                    ]
                );

            } else {

                /*
                 * Affectation actuelle unique.
                 */
                Assignment::create(
                    [
                        'employee_id' => $employee->id,
                        'start_date' => $startDate->toDateString(),
                        'position_id' => $position->id,
                        'unit_id' => $unit->id,
                        'event_date' => $startDate->toDateString(),
                        'reason' => 'Première affectation',
                        'end_date' => null,
                    ]
                );
            }
        }

        $this->command->info(
            $employees->count() . ' employé(s) ont été affectés.'
        );
    }
}
