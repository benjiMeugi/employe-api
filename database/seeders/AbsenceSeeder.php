<?php

namespace Database\Seeders;

use App\Models\Absence;
use App\Models\AbsenceRequest;
use App\Models\AbsenceType;
use App\Models\Employe;
use App\Models\LeaveGrant;
use Illuminate\Database\Seeder;

class AbsenceSeeder extends Seeder
{
    public function run(): void
    {
        $congeAnnuel = AbsenceType::where('code', 'CA')->first();
        $congeMaladie = AbsenceType::where('code', 'CM')->first();
        $approvedRequest = AbsenceRequest::where('status', 'approved')->first();
        $leaveCredit = LeaveGrant::first();

        if (! $congeAnnuel || ! $congeMaladie) {
            $this->command->warn(
                'AbsenceSeeder: types d\'absence introuvables — ' .
                'assure-toi que AbsenceTypeSeeder tourne avant celui-ci.'
            );
            return;
        }

        // Absence issue d'une demande approuvée (le cas normal)
        if ($approvedRequest) {
            Absence::create([
                'employee_id' => $approvedRequest->employee_id,
                'absence_type_id' => $congeAnnuel->id,
                'start_date' => $approvedRequest->requested_start_date,
                'end_date' => $approvedRequest->requested_end_date,
                'duration_days' => $approvedRequest->requested_days_count,
                'absence_request_id' => $approvedRequest->id,
            ]);
        }

        // Absence saisie rétroactivement, sans passer par une demande
        $employee = Employe::inRandomOrder()->first();
        if ($employee) {
            Absence::create([
                'employee_id' => $employee->id,
                'absence_type_id' => $congeMaladie->id,
                'start_date' => now()->subDays(10)->toDateString(),
                'end_date' => now()->subDays(8)->toDateString(),
                'duration_days' => 2,
                'absence_request_id' => null,
            ]);
        }
    }
}
