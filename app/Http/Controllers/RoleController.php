<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Repository\Repository;
use App\Models\Ability;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RoleController extends Controller
{
    /**
     * @var Role
     */
    private Role $model;

    /**
     * @var Repository
     */
    private Repository $repository;

    public function __construct()
    {
        $this->model = new Role();
        $this->repository = new Repository($this->model);
    }

    /**
     * List resource
     *
     * @param Request $request
     */
    public function index(Request $request, $id = null)
    {
        if ($id !== null) {
            return $this->show($request, $id);
        }

        $query = $this->repository->parse_filters($request);

        if ($request->has('page') && $request->has('per_page')) {
            return $this->respondOk($query->paginate($request->input('per_page')));
        }
        return $this->respondOk($query->get());
    }

    /**
     * List single resource
     *
     * @param Request $request
     * @param int $id
     */
    public function show(Request $request, int $id)
    {
        return $this->repository->show($request, $id);
    }

    /**
     * Store resource
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $validator = $this->repository->check($request, $this->repository->rules());
        if (true !== $validator) {
            return $validator;
        }

        return $this->respondOk($this->repository->store($request));
    }

    /**
     * Update resource
     *
     * @param Request $request
     * @param int $id
     */
    public function update(Request $request, int $id)
    {
        $validator = $this->repository->check($request, $this->repository->update_rules(), $id);
        if (true !== $validator) {
            return $validator;
        }

        return $this->respondOk($this->repository->update($request, $id));
    }

    /**
     * Delete resource
     *
     * @param Request $request
     * @param int $id
     */
    public function delete(Request $request, int $id)
    {
        return $this->respondOk($this->repository->delete($request, $id));
    }

    /**
     * Remplace entièrement la liste des habiletés accordées à ce rôle.
     *
     * Corps attendu : {"ability_ids": [1, 2, 3]}
     *
     * @param Request $request
     * @param int $id
     */
    public function syncAbilities(Request $request, int $id)
    {
        $role = Role::findOrFail($id);

        // Capturé AVANT sync() — sinon impossible de savoir quelles
        // habiletés viennent d'être retirées.
        $oldAbilityIds = $role->abilities()->pluck('abilities.id')->toArray();
        $newAbilityIds = $request->input('ability_ids', []);

        $role->abilities()->sync($newAbilityIds);

        // Vide le cache pour toute habileté touchée par ce changement —
        // celles retirées comme celles ajoutées, pour ne rien laisser
        // d'obsolète en mémoire.
        $affectedIds = array_unique(array_merge($oldAbilityIds, $newAbilityIds));
        Ability::whereIn('id', $affectedIds)->get()
            ->each(fn (Ability $ability) => $ability->forgetCache());

        return $this->respondOk($role->load('abilities'));
    }

    /**
     * Ajoute une habileté à ce rôle, sans toucher aux autres déjà
     * présentes. Idempotent : si elle est déjà accordée, ne fait rien
     * et ne renvoie aucune erreur.
     *
     * Corps attendu : {"ability_id": 5}
     *
     * @param Request $request
     * @param int $id
     */
    public function addAbility(Request $request, int $id)
    {
        $role = Role::findOrFail($id);
        $abilityId = $request->input('ability_id');

        $role->abilities()->syncWithoutDetaching([$abilityId]);

        Ability::find($abilityId)?->forgetCache();

        return $this->respondOk($role->load('abilities'));
    }

    /**
     * Retire une habileté précise de ce rôle, sans toucher aux autres.
     *
     * Corps attendu : {"ability_id": 5}
     *
     * @param Request $request
     * @param int $id
     */
    public function removeAbility(Request $request, int $id)
    {
        $role = Role::findOrFail($id);
        $abilityId = $request->input('ability_id');

        $role->abilities()->detach($abilityId);

        Ability::find($abilityId)?->forgetCache();

        return $this->respondOk($role->load('abilities'));
    }
}
