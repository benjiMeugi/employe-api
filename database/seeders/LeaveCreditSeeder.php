<?php

namespace Database\Seeders;

use App\Models\AbsenceType;
use App\Models\Employe;
use App\Models\LeaveCredit;
use Illuminate\Database\Seeder;

class LeaveCreditSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employe::inRandomOrder()->take(5)->get();
        $congeAnnuel = AbsenceType::where('code', 'CA')->first();

        if ($employees->isEmpty() || ! $congeAnnuel) {
            $this->command->warn(
                'LeaveCreditSeeder: employés ou AbsenceType "CA" introuvables — ' .
                'assure-toi que EmployeSeeder et AbsenceTypeSeeder tournent avant celui-ci.'
            );
            return;
        }

        foreach ($employees as $employee) {
            // Lot de l'année en cours
            LeaveCredit::create([
                'employee_id' => $employee->id,
                'absence_type_id' => $congeAnnuel->id,
                'period' => '2026',
                'acquired_days' => 30,
                'acquisition_date' => '2026-01-01',
                'expiration_date' => '2027-03-31',
            ]);

            // Lot reporté de l'année précédente, sur quelques employés
            if (rand(0, 1)) {
                LeaveCredit::create([
                    'employee_id' => $employee->id,
                    'absence_type_id' => $congeAnnuel->id,
                    'period' => '2025 (reporté)',
                    'acquired_days' => rand(2, 8),
                    'acquisition_date' => '2025-01-01',
                    'expiration_date' => '2026-03-31',
                ]);
            }
        }
    }
}
