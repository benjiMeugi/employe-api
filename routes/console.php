<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tous les jours à 1h : chaque employé est crédité à l'anniversaire
// mensuel de son contrat, pas à une date commune — d'où une
// exécution quotidienne plutôt que mensuelle.
//
// --sync exécute les jobs immédiatement, sans passer par la file :
// pas besoin d'un worker séparé. À revoir si le volume d'employés
// rend la commande trop longue.
Schedule::command('leave:accrue --sync')->dailyAt('01:00');
