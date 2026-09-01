<?php

namespace Database\Seeders;

use App\Models\Absence;
use App\Models\AbsenceRequest;
use Illuminate\Database\Seeder;

class AbsenceSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Toutes les demandes approuvées doivent normalement
         * donner lieu à une absence réelle.
         */
        $approvedRequests = AbsenceRequest::query()
            ->where('status', 'approved')
            ->with('absenceType')
            ->get();

        if ($approvedRequests->isEmpty()) {
            $this->command->warn(
                'AbsenceSeeder: aucune demande approuvée trouvée.'
            );

            return;
        }

        foreach ($approvedRequests as $request) {

            /*
             * Une demande horaire utilise requested_hours.
             */
            if ($request->absenceType->isHourly()) {

                Absence::firstOrCreate(
                    [
                        'absence_request_id' => $request->id,
                    ],
                    [
                        'employee_id' => $request->employee_id,
                        'absence_type_id' => $request->absence_type_id,
                        'start_date' => $request->requested_start_date,
                        'duration_days' => null,
                        'duration_hours' => $request->requested_hours,
                        'excess_is_deductible' => false,
                    ]
                );

                continue;
            }

            /*
             * Absence exprimée en jours.
             *
             * IMPORTANT :
             * on ne renseigne PAS end_date.
             *
             * Le modèle Absence le calcule automatiquement.
             */
            Absence::firstOrCreate(
                [
                    'absence_request_id' => $request->id,
                ],
                [
                    'employee_id' => $request->employee_id,
                    'absence_type_id' => $request->absence_type_id,
                    'start_date' => $request->requested_start_date,
                    'duration_days' => $request->requested_days,
                    'duration_hours' => null,
                    'excess_is_deductible' => false,
                ]
            );
        }

        $this->command->info(
            $approvedRequests->count() .
            ' absence(s) générée(s) depuis les demandes approuvées.'
        );
    }
}
