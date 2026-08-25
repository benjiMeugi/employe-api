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
    public function register(): void
    {
        //
    }

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

            if (empty($allowedRoles)) {
                return null;
            }

            // Auth::token() renvoie une chaîne JSON brute, pas un objet
            // déjà décodé — json_decode() est indispensable avant de
            // pouvoir accéder à resource_access. Sans ça, ->resource_access
            // sur une chaîne renvoie silencieusement null, jamais d'erreur.
            $decodedToken = json_decode(Auth::token());
            $userRoles = $decodedToken->resource_access->{'employe-api'}->roles ?? [];

            return count(array_intersect($userRoles, $allowedRoles)) > 0;
        });
    }
}
