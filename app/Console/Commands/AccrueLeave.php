<?php

namespace App\Console\Commands;

use App\Jobs\AccrueLeaveForContract;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Tourne quotidiennement. Chaque employé est crédité à l'anniversaire
 * mensuel de son contrat, jamais à une date fixe commune — d'où une
 * exécution tous les jours plutôt qu'une fois par mois.
 *
 * Lançable à la main pour rattraper un jour manqué :
 *   php artisan leave:accrue --date=2026-09-20
 */
class AccrueLeave extends Command
{
    protected $signature = 'leave:accrue
                            {--date= : Date de référence (par défaut aujourd\'hui)}
                            {--sync : Exécuter immédiatement, sans passer par la file}';

    protected $description = 'Crédite les jours de congé des contrats arrivés à leur anniversaire mensuel';

    public function handle(): int
    {
        $reference = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : Carbon::today();

        $this->info("Accumulation au {$reference->toDateString()}");

        $dispatched = 0;

        Contract::where('status', 'Active')
            ->whereDate('start_date', '<=', $reference)
            ->chunkById(200, function ($contracts) use ($reference, &$dispatched) {
                foreach ($contracts as $contract) {
                    if (! $this->isAccrualDay($contract, $reference)) {
                        continue;
                    }

                    $job = new AccrueLeaveForContract($contract->id, $reference->toDateString());

                    $this->option('sync')
                        ? dispatch_sync($job)
                        : dispatch($job);

                    $dispatched++;
                }
            });

        $this->info("{$dispatched} contrat(s) traité(s).");

        return self::SUCCESS;
    }

    /**
     * Le contrat atteint-il un anniversaire mensuel ce jour-là ?
     *
     * addMonthsNoOverflow gère le cas des fins de mois : un contrat
     * démarré un 31 janvier tombe au 28 (ou 29) février, puis au
     * 31 mars — jamais au 3 mars.
     *
     * Le premier crédit arrive un mois complet après la prise d'effet :
     * embauche le 20 août, premier crédit le 20 septembre.
     */
    private function isAccrualDay(Contract $contract, Carbon $reference): bool
    {
        $start = Carbon::parse($contract->start_date);

        if ($start->gte($reference)) {
            return false;
        }

        $monthsElapsed = $start->diffInMonths($reference);

        if ($monthsElapsed < 1) {
            return false;
        }

        return $start->copy()
            ->addMonthsNoOverflow($monthsElapsed)
            ->isSameDay($reference);
    }
}
