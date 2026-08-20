<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */

    /**
     * Contrôle volontairement séparé du système Ability/Role — sinon,
     * gérer qui a le droit de gérer les rôles créerait une dépendance
     * circulaire (il faudrait déjà une habileté pour en accorder une).
     * Ce middleware vérifie directement un rôle Keycloak fixe, jamais
     * la table role_ability : impossible de se verrouiller dehors, même
     * si cette table est vide ou mal configurée.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::hasRole('employe-api', 'super-admin')) {
            abort(403, "Accès réservé aux super-administrateurs.");
        }

        return $next($request);    }
}
