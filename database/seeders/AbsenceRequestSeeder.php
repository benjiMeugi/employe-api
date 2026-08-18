<?php

namespace Database\Seeders;

use App\Models\AbsenceRequest;
use App\Models\AbsenceType;
use App\Models\Employe;
use Illuminate\Database\Seeder;

class AbsenceRequestSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employe::inRandomOrder()->take(5)->get();
        $congeAnnuel = AbsenceType::where('code', 'CA')->first();
        $congeMaladie = AbsenceType::where('code', 'CM')->first();
        $approver = Employe::inRandomOrder()->first();

        if ($employees->isEmpty() || ! $congeAnnuel || ! $congeMaladie) {
            $this->command->warn(
                'AbsenceRequestSeeder: employés ou types d\'absence introuvables — ' .
                'assure-toi que EmployeSeeder et AbsenceTypeSeeder tournent avant celui-ci.'
            );
            return;
        }

        // Une demande approuvée
        AbsenceRequest::create([
            'employee_id' => $employees[0]->id,
            'absence_type_id' => $congeAnnuel->id,
            'requested_start_date' => '2026-09-01',
            'requested_end_date' => '2026-09-10',
            'requested_days_count' => 10,
            'reason' => 'Congés familiaux',
            'status' => 'approved',
            'approver_id' => $approver->id,
            'decision_datetime' => now()->subDays(5),
            'decision_comment' => 'Accordé, bonne période',
        ]);

        // Une demande en attente
        AbsenceRequest::create([
            'employee_id' => $employees[1]->id,
            'absence_type_id' => $congeMaladie->id,
            'requested_start_date' => '2026-08-20',
            'requested_end_date' => '2026-08-22',
            'requested_days_count' => 3,
            'reason' => 'Grippe',
            'status' => 'pending',
            'approver_id' => null,
            'decision_datetime' => null,
            'decision_comment' => null,
        ]);

        // Une demande rejetée
        AbsenceRequest::create([
            'employee_id' => $employees[2]->id,
            'absence_type_id' => $congeAnnuel->id,
            'requested_start_date' => '2026-12-20',
            'requested_end_date' => '2026-12-31',
            'requested_days_count' => 12,
            'reason' => 'Congés de fin d\'année',
            'status' => 'rejected',
            'approver_id' => $approver->id,
            'decision_datetime' => now()->subDays(2),
            'decision_comment' => 'Solde insuffisant sur cette période',
        ]);
    }
}
