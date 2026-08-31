<?php

use App\Http\Controllers\AbilityController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\AbsenceRequestController;
use App\Http\Controllers\AbsenceTypeController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CareerEventController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\DismissalController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveBalanceController;
use App\Http\Controllers\LeaveGrantController;
use App\Http\Controllers\OngoingAbsenceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PromotionController;
use \App\Http\Controllers\RetirementController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\UnitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Génère le nom d'habileté attendu par Gate::before() (ex: "employe-list"),
 * préfixé par "can:" pour que Laravel sache qu'il s'agit d'une
 * vérification d'autorisation, pas un simple alias de middleware.
 */
function resolveAbility(string $start_ability, string $end_ability)
{
    return 'can:' .$start_ability . '-' . $end_ability;
}

Route::middleware('auth:api')->group(function () {

    Route::prefix('title')->group(function () {
        $controller = TitleController::class;
        $startAbility = 'title';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('position')->group(function () {
        $controller = PositionController::class;
        $startAbility = 'position';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('classification')->group(function () {
        $controller = ClassificationController::class;
        $startAbility = 'classification';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('employe')->group(function () {
        $controller = EmployeController::class;
        $startAbility = 'employe';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('career_event')->group(function () {
        $controller = CareerEventController::class;
        $startAbility = 'career_event';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('retirement')->group(function () {
        $controller = RetirementController::class;
        $startAbility = 'retirement';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('dismissal')->group(function () {
        $controller = DismissalController::class;
        $startAbility = 'dismissal';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('sanction')->group(function () {
        $controller = SanctionController::class;
        $startAbility = 'sanction';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('assignment')->group(function () {
        $controller = AssignmentController::class;
        $startAbility = 'assignment';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('unit')->group(function () {
        $controller = UnitController::class;
        $startAbility = 'unit';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('promotion')->group(function () {
        $controller = PromotionController::class;
        $startAbility = 'promotion';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('absence_type')->group(function () {
        $controller = AbsenceTypeController::class;
        $startAbility = 'absence_type';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('leave_grant')->group(function () {
        $controller = LeaveGrantController::class;
        $startAbility = 'leave_grant';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('absence_request')->group(function () {
        $controller = AbsenceRequestController::class;
        $startAbility = 'absence_request';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('absence')->group(function () {
        $controller = AbsenceController::class;
        $startAbility = 'absence';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('attachment')->group(function () {
        $controller = AttachmentController::class;
        $startAbility = 'attachment';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::get('/{id}/download', [$controller, 'download']);
        Route::post('/absence/{id}', [AttachmentController::class, 'storeForAbsence']);
        Route::post('/absence_request/{id}', [AttachmentController::class, 'storeForAbsenceRequest']);
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

// ---------- Gestion des rôles/habiletés elle-même ----------
// Volontairement en simple auth:api + super-admin (pas de resolveAbility) :
// on évite de conditionner "qui peut gérer les permissions" par les
// permissions elles-mêmes, pour ne pas risquer de s'auto-verrouiller.

    Route::prefix('role')->group(function () {
        $controller = RoleController::class;
        Route::get('/{id?}', [$controller, 'index'])->middleware('super-admin');
        Route::post('/', [$controller, 'store'])->middleware('super-admin');
        Route::put('/{id}', [$controller, 'update'])->middleware('super-admin');
        Route::delete('/{id}', [$controller, 'delete'])->middleware('super-admin');

        Route::put('/{id}/abilities', [$controller, 'syncAbilities'])->middleware('super-admin');
        Route::post('/{id}/abilities', [$controller, 'addAbility'])->middleware('super-admin');
        Route::delete('/{id}/abilities', [$controller, 'removeAbility'])->middleware('super-admin');
    });

    Route::prefix('holiday')->group(function () {
        $controller = HolidayController::class;
        $startAbility = 'holiday';
        Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    Route::prefix('ability')->group(function () {
        $controller = AbilityController::class;
        Route::get('/{id?}', [$controller, 'index'])->middleware('super-admin');
        Route::post('/', [$controller, 'store'])->middleware('super-admin');
        Route::put('/{id}', [$controller, 'update'])->middleware('super-admin');
        Route::delete('/{id}', [$controller, 'delete'])->middleware('super-admin');
    });

    Route::prefix('leave_balance')->group(function () {
        $controller = LeaveBalanceController::class;
        $startAbility = 'leave_balance';

        // Consulter SON PROPRE solde : aucune permission particulière,
        // n'importe quel employé connecté y a droit.
        Route::get('/current', [$controller, 'current'])->middleware(resolveAbility($startAbility, 'current'));

        // Consulter le solde des autres : réservé, via les habiletés.
        Route::get('/', [$controller, 'index'])
            ->middleware(resolveAbility($startAbility, 'list'));
        Route::get('/employee/{employeeId}', [$controller, 'forEmployee'])
            ->middleware(resolveAbility($startAbility, 'list'));
    });

    Route::prefix('ongoing_absence')->group(function () {
        $controller = OngoingAbsenceController::class;
        $startAbility = 'ongoing_absence';

        // Sa propre absence en cours : ouvert à tout employé connecté.
        Route::get('/current', [$controller, 'current'])->middleware('current');

        Route::get('/', [$controller, 'index'])
            ->middleware(resolveAbility($startAbility, 'list'));
        Route::get('/employee/{employeeId}', [$controller, 'forEmployee'])
            ->middleware(resolveAbility($startAbility, 'list'));
    });

// Volontairement en simple auth:api, sans resolveAbility — n'importe
// quel utilisateur connecté doit pouvoir consulter SES PROPRES droits,
// ça n'a pas besoin d'une permission séparée.
    Route::get('/permissions', [PermissionController::class, 'show']);

});
