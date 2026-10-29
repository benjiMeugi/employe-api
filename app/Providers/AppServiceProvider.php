<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * 
     * Les autorisations sont désormais centralisées dans authentication-api.
     * L'autorisation est gérée directement par le middleware CheckAbility
     * qui lit les permissions depuis auth_user injecté par VerifyAuthApiToken.
     */
    public function boot(): void
    {
        // Mapping morphologique pour les relations polymorphiques
        Relation::morphMap([
            'absence' => \App\Models\Absence::class,
            'absence_request' => \App\Models\AbsenceRequest::class,
        ]);
    }
}
