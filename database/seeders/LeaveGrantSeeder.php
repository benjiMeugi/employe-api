<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class LeaveGrantSeeder extends Seeder
{
    /**
     * Plutôt que d'inventer des octrois, on rejoue la vraie commande
     * d'accumulation sur les 12 derniers mois. Deux avantages : les
     * données de test correspondent exactement à ce que produira le
     * job en conditions réelles, et ça teste la commande au passage.
     *
     * L'idempotence de leave_grants (unique employee/type/period)
     * garantit qu'un jour parcouru deux fois ne crédite jamais deux fois.
     */
    public function run(): void
    {
        $start = Carbon::today()->subYear();
        $today = Carbon::today();

        $this->command->info('Accumulation rejouée sur 12 mois...');

        $cursor = $start->copy();

        while ($cursor->lte($today)) {
            Artisan::call('leave:accrue', [
                '--date' => $cursor->toDateString(),
                '--sync' => true,
            ]);

            $cursor->addDay();
        }

        $count = \App\Models\LeaveGrant::count();
        $this->command->info("{$count} octroi(s) créé(s).");
    }
}
