<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAbilitySeeder extends Seeder
{
    /**
     * Propositions de départ, à valider/ajuster selon les vraies règles
     * de l'organisation — pas des décisions déjà figées.
     *
     * Modules "catalogue" (title, position, classification, unit,
     * absence_type) : lecture ouverte à tous les authentifiés,
     * écriture réservée à hr-manager.
     *
     * Modules "carrière" (employe, career_event, retirement, dismissal,
     * sanction, promotion, leave_credit) : entièrement réservés à
     * hr-manager, y compris la lecture.
     *
     * absence_request : cas particulier — un employee doit pouvoir
     * consulter et déposer ses propres demandes, mais seule hr-manager
     * peut trancher (update = changer le statut) ou supprimer.
     *
     * attachment : réservé à hr-manager (l'upload lui-même passe par
     * des routes dédiées, storeForAbsence/storeForAbsenceRequest,
     * volontairement laissées hors de ce système pour l'instant).
     */
    private array $catalogModules = ['title', 'position', 'classification', 'unit', 'absence_type'];

    private array $hrOnlyModules = [
        'employe', 'career_event', 'retirement', 'dismissal',
        'sanction', 'promotion', 'leave_credit', 'attachment',
    ];

    public function run(): void
    {
        $hrManager = Role::firstOrCreate(['label' => 'hr-manager'], ['description' => 'Gestion RH complète']);
        $employee = Role::firstOrCreate(['label' => 'employee'], ['description' => 'Employé standard']);

        // Existe en base pour la cohérence (listage, description...),
        // mais l'accès à /role et /ability lui-même reste protégé par
        // EnsureSuperAdmin (vérification directe du token Keycloak) —
        // jamais par cette table, pour éviter le problème "œuf et poule".
        $superAdmin = Role::firstOrCreate(
            ['label' => 'super-admin'],
            ['description' => 'Accès total, y compris la gestion des rôles/habiletés']
        );

        foreach ($this->catalogModules as $module) {
            $this->grant($hrManager, "{$module}-list");
            $this->grant($hrManager, "{$module}-create");
            $this->grant($hrManager, "{$module}-update");
            $this->grant($hrManager, "{$module}-delete");

            $this->grant($employee, "{$module}-list");
        }

        foreach ($this->hrOnlyModules as $module) {
            $this->grant($hrManager, "{$module}-list");
            $this->grant($hrManager, "{$module}-create");
            $this->grant($hrManager, "{$module}-update");
            $this->grant($hrManager, "{$module}-delete");
        }

        // career_event n'a pas de create/update (voir routes/api.php)
        $this->revoke($hrManager, 'career_event-create');
        $this->revoke($hrManager, 'career_event-update');

        // absence_request : cas particulier, les deux rôles participent différemment
        $this->grant($hrManager, 'absence_request-list');
        $this->grant($hrManager, 'absence_request-create');
        $this->grant($hrManager, 'absence_request-update');
        $this->grant($hrManager, 'absence_request-delete');

        $this->grant($employee, 'absence_request-list');
        $this->grant($employee, 'absence_request-create');

        // super-admin hérite de toutes les habiletés définies jusqu'ici —
        // recalculé à la fin, une fois que tout le reste existe.
        $allAbilityIds = Ability::pluck('id')->toArray();
        $superAdmin->abilities()->syncWithoutDetaching($allAbilityIds);
    }

    private function grant(Role $role, string $abilityLabel): void
    {
        $ability = Ability::firstOrCreate(['label' => $abilityLabel]);
        $role->abilities()->syncWithoutDetaching([$ability->id]);
    }

    private function revoke(Role $role, string $abilityLabel): void
    {
        $ability = Ability::where('label', $abilityLabel)->first();
        if ($ability) {
            $role->abilities()->detach($ability->id);
        }
    }
}
