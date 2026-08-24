<?php

namespace App\Http\Controllers;

use App\Models\Ability;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    /**
     * Renvoie les droits d'accès du user connecté : ses rôles Keycloak
     * (client employe-api) et ses habiletés effectives — dérivées de
     * la table Ability/Role, exactement comme Gate::before() les
     * calcule pour les vraies décisions d'autorisation. Le frontend
     * peut s'appuyer là-dessus pour savoir quoi afficher/masquer.
     *
     * Volontairement pas d'informations personnelles ici (nom complet,
     * coordonnées...) — uniquement ce qui concerne les droits d'accès.
     * L'identifiant technique (sub) reste présent, comme référence
     * strictement nécessaire pour corréler avec d'autres appels.
     */
    public function show()
    {
        $token = Auth::token();

        $roles = $token->resource_access->{'employe-api'}?->roles ?? [];

        $abilities = Ability::whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('label', $roles);
        })->pluck('label')->unique()->values();

        return $this->respondOk([
            'sub' => $token->sub ?? null,
            'roles' => $roles,
            'abilities' => $abilities,
        ]);
    }
}
