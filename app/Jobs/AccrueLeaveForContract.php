<?php

namespace App\Jobs;

use App\Models\AbsenceType;
use App\Models\Contract;
use App\Models\LeaveGrant;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Crédite les jours acquis d'un employé pour un mois donné.
 *
 * Rejouable sans risque : la contrainte d'unicité
 * (employee_id, absence_type_id, period) empêche tout double crédit,
 * même si le job tourne deux fois pour la même période.
 */
class AccrueLeaveForContract implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $contractId,
        public string $referenceDate
    ) {}

    public function handle(): void
    {
        $contract = Contract::find($this->contractId);

        if (! $contract || $contract->status !== 'Active') {
            return;
        }

        $reference = Carbon::parse($this->referenceDate);
        $period = $reference->format('Y-m');

        // Tous les types qui s'accumulent — en pratique le congé
        // annuel seul, mais la règle vient du catalogue, pas du code.
        $types = AbsenceType::where('accrual_rate_per_month', '>', 0)->get();

        foreach ($types as $type) {
            LeaveGrant::firstOrCreate(
                [
                    'employee_id' => $contract->employee_id,
                    'absence_type_id' => $type->id,
                    'period' => $period,
                ],
                [
                    'granted_days' => $type->accrual_rate_per_month,
                    'grant_date' => $reference->toDateString(),
                ]
            );
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Accumulation échouée pour le contrat {$this->contractId}", [
            'period' => Carbon::parse($this->referenceDate)->format('Y-m'),
            'message' => $exception->getMessage(),
        ]);
    }
}
