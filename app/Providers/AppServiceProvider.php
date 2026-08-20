<?php

namespace App\Providers;

use App\Models\Ability;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'absence' => \App\Models\Absence::class,
            'absence_request' => \App\Models\AbsenceRequest::class,
        ]);

        Gate::before(function ($user, string $ability) {
            $allowedRoles = Cache::remember("ability:{$ability}", 60, function () use ($ability) {
                return Ability::where('label', $ability)
                    ->with('roles')
                    ->first()
                    ?->roles
                    ->pluck('label')
                    ->toArray() ?? [];
            });

            // Habileté jamais déclarée (ou déclarée mais sans aucun rôle
            // associé) : ni explicitement autorisée ni refusée ici —
            // Laravel continue sa logique normale.
            if (empty($allowedRoles)) {
                return null;
            }

            $userRoles = Auth::token()->resource_access->{'employe-api'}?->roles ?? [];

            return count(array_intersect($userRoles, $allowedRoles)) > 0;
        });
    }
}
