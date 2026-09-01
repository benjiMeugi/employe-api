<?php

namespace Database\Seeders;

use App\Models\AbsenceRequest;
use App\Models\AbsenceType;
use App\Models\Employe;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AbsenceRequestSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employe::query()
            ->orderBy('id')
            ->get();

        if ($employees->isEmpty()) {
            $this->command->warn(
                'AbsenceRequestSeeder: aucun employé trouvé.'
            );

            return;
        }

        $types = AbsenceType::query()
            ->get()
            ->keyBy('code');

        $approver = Employe::query()
            ->orderBy('id')
            ->first();

        if (! $approver) {
            return;
        }

        /*
         * ============================================================
         * CHAQUE EMPLOYÉ
         * ============================================================
         */

        foreach ($employees as $index => $employee) {

            /*
             * --------------------------------------------------------
             * 1. CONGÉ ANNUEL HISTORIQUE — APPROUVÉ
             * --------------------------------------------------------
             */

            $this->createHistoricalRequest(
                employee: $employee,
                type: $types['CA'],
                startDate: Carbon::now()->subMonths(10)->startOfDay(),
                days: 5,
                reason: 'Congés annuels',
                status: 'approved',
                approver: $approver
            );

            /*
             * --------------------------------------------------------
             * 2. CONGÉ ANNUEL HISTORIQUE — APPROUVÉ
             * --------------------------------------------------------
             */

            $this->createHistoricalRequest(
                employee: $employee,
                type: $types['CA'],
                startDate: Carbon::now()->subMonths(6)->startOfDay(),
                days: 7,
                reason: 'Congés familiaux',
                status: 'approved',
                approver: $approver
            );

            /*
             * --------------------------------------------------------
             * 3. MALADIE HISTORIQUE
             * --------------------------------------------------------
             */

            $this->createHistoricalRequest(
                employee: $employee,
                type: $types['CM'],
                startDate: Carbon::now()->subMonths(4)->startOfDay(),
                days: rand(2, 4),
                reason: 'Arrêt maladie',
                status: 'approved',
                approver: $approver
            );

            /*
             * --------------------------------------------------------
             * 4. DEMANDE REJETÉE
             * --------------------------------------------------------
             */

            $this->createHistoricalRequest(
                employee: $employee,
                type: $types['CA'],
                startDate: Carbon::now()->subMonths(3)->startOfDay(),
                days: 10,
                reason: 'Congés personnels',
                status: 'rejected',
                approver: $approver
            );

            /*
             * --------------------------------------------------------
             * 5. DEMANDE RÉCENTE EN ATTENTE
             * --------------------------------------------------------
             *
             * Seulement certains employés afin de garder une vraie
             * diversité dans les statuts.
             */

            if ($index % 2 === 0) {
                $this->createHistoricalRequest(
                    employee: $employee,
                    type: $types['CA'],
                    startDate: Carbon::now()->addDays(15)->startOfDay(),
                    days: 5,
                    reason: 'Congés à venir',
                    status: 'pending',
                    approver: null
                );
            }

            /*
             * --------------------------------------------------------
             * 6. PERMISSION EXCEPTIONNELLE
             * --------------------------------------------------------
             */

            if ($index % 3 === 0) {
                $this->createHistoricalRequest(
                    employee: $employee,
                    type: $types['PEX'],
                    startDate: Carbon::now()->subMonths(2)->startOfDay(),
                    days: 2,
                    reason: 'Événement familial exceptionnel',
                    status: 'approved',
                    approver: $approver
                );
            }

            /*
             * --------------------------------------------------------
             * 7. PERMISSION HORAIRE
             * --------------------------------------------------------
             */

            if ($index % 4 === 0) {
                $this->createHourlyRequest(
                    employee: $employee,
                    type: $types['PH'],
                    startDate: Carbon::now()->subDays(20)->startOfDay(),
                    hours: 3,
                    reason: 'Rendez-vous administratif',
                    status: 'approved',
                    approver: $approver
                );
            }

            /*
             * --------------------------------------------------------
             * 8. MARIAGE
             * --------------------------------------------------------
             */

            if (
                $index % 7 === 0
                && isset($types['MAR'])
            ) {
                $this->createHistoricalRequest(
                    employee: $employee,
                    type: $types['MAR'],
                    startDate: Carbon::now()->subYear()->startOfDay(),
                    days: 3,
                    reason: 'Mariage',
                    status: 'approved',
                    approver: $approver
                );
            }

            /*
             * --------------------------------------------------------
             * 9. DÉCÈS
             * --------------------------------------------------------
             */

            if (
                $index % 6 === 0
                && isset($types['DEC'])
            ) {
                $this->createHistoricalRequest(
                    employee: $employee,
                    type: $types['DEC'],
                    startDate: Carbon::now()->subMonths(8)->startOfDay(),
                    days: 3,
                    reason: 'Décès d’un proche',
                    status: 'approved',
                    approver: $approver
                );
            }

            /*
             * --------------------------------------------------------
             * 10. MATERNITÉ
             * --------------------------------------------------------
             */

            if (
                $employee->gender === 'F'
                && isset($types['MAT'])
            ) {
                $this->createHistoricalRequest(
                    employee: $employee,
                    type: $types['MAT'],
                    startDate: Carbon::now()->subMonths(14)->startOfDay(),
                    days: 98,
                    reason: 'Congé maternité',
                    status: 'approved',
                    approver: $approver
                );
            }

            /*
             * --------------------------------------------------------
             * 11. PATERNITÉ
             * --------------------------------------------------------
             */

            if (
                $employee->gender === 'M'
                && isset($types['PAT'])
                && $index % 4 === 0
            ) {
                $this->createHistoricalRequest(
                    employee: $employee,
                    type: $types['PAT'],
                    startDate: Carbon::now()->subMonths(9)->startOfDay(),
                    days: 3,
                    reason: 'Naissance d’un enfant',
                    status: 'approved',
                    approver: $approver
                );
            }
        }

        $this->command->info(
            'Demandes d’absence historiques créées avec succès.'
        );
    }

    /**
     * Crée une demande en respectant le comportement du modèle :
     * elle est d'abord créée en pending, puis éventuellement
     * approuvée/rejetée.
     */
    private function createHistoricalRequest(
        Employe $employee,
        AbsenceType $type,
        Carbon $startDate,
        int $days,
        string $reason,
        string $status,
        ?Employe $approver
    ): AbsenceRequest {
        $request = AbsenceRequest::create([
            'employee_id' => $employee->id,
            'absence_type_id' => $type->id,
            'requested_start_date' => $startDate->toDateString(),
            'requested_days' => $days,
            'requested_hours' => null,
            'reason' => $reason,
        ]);

        if ($status !== 'pending') {
            $request->update([
                'status' => $status,
                'approver_id' => $approver?->id,
                'decision_datetime' => $startDate->copy()->subDays(10),
                'decision_comment' => $status === 'approved'
                    ? 'Demande approuvée.'
                    : 'Demande rejetée.',
            ]);
        }

        return $request;
    }

    /**
     * Même logique pour les permissions horaires.
     */
    private function createHourlyRequest(
        Employe $employee,
        AbsenceType $type,
        Carbon $startDate,
        int $hours,
        string $reason,
        string $status,
        ?Employe $approver
    ): AbsenceRequest {
        $request = AbsenceRequest::create([
            'employee_id' => $employee->id,
            'absence_type_id' => $type->id,
            'requested_start_date' => $startDate->toDateString(),
            'requested_days' => null,
            'requested_hours' => $hours,
            'reason' => $reason,
        ]);

        if ($status !== 'pending') {
            $request->update([
                'status' => $status,
                'approver_id' => $approver?->id,
                'decision_datetime' => $startDate->copy()->subDays(5),
                'decision_comment' => $status === 'approved'
                    ? 'Permission accordée.'
                    : 'Permission refusée.',
            ]);
        }

        return $request;
    }
}
