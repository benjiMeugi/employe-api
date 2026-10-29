<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAbility
{
    /**
     * Middleware de vérification des habilités.
     * 
     * Les autorisations sont centralisées dans authentication-api.
     * Ce middleware vérifie directement dans les permissions renvoyées
     * par authentication-api via VerifyAuthApiToken, sans passer par
     * le système Gate/Ability local.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $ability  Nom de l'habilité à vérifier (ex: employe-list)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        // Récupère l'utilisateur authentifié injecté par VerifyAuthApiToken
        $authUser = $request->input('auth_user');

        if (is_object($authUser)) {
            $authUser = (array) $authUser;
        }

        // Les permissions proviennent directement de authentication-api
        $permissions = $authUser['permissions'] ?? [];

        // On part du principe que les permissions sont toujours un tableau
        if (in_array($ability, $permissions)) {
            return $next($request);
        }

        // Accès refusé si l'habilité n'est pas présente
        abort(403, "Action non autorisée.");
    }
}
