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
use App\Http\Controllers\LeaveCreditController;
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

Route::prefix('title')->group(function () {
    $controller = TitleController::class;
    $startAbility = 'title';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('position')->group(function () {
    $controller = PositionController::class;
    $startAbility = 'position';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('classification')->group(function () {
    $controller = ClassificationController::class;
    $startAbility = 'classification';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('employe')->group(function () {
    $controller = EmployeController::class;
    $startAbility = 'employe';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('career_event')->group(function () {
    $controller = CareerEventController::class;
    $startAbility = 'career_event';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('retirement')->group(function () {
    $controller = RetirementController::class;
    $startAbility = 'retirement';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('dismissal')->group(function () {
    $controller = DismissalController::class;
    $startAbility = 'dismissal';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('sanction')->group(function () {
    $controller = SanctionController::class;
    $startAbility = 'sanction';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('assignment')->group(function () {
    $controller = AssignmentController::class;
    $startAbility = 'assignment';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('unit')->group(function () {
    $controller = UnitController::class;
    $startAbility = 'unit';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('promotion')->group(function () {
    $controller = PromotionController::class;
    $startAbility = 'promotion';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('absence_type')->group(function () {
    $controller = AbsenceTypeController::class;
    $startAbility = 'absence_type';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('leave_credit')->group(function () {
    $controller = LeaveCreditController::class;
    $startAbility = 'leave_credit';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('absence_request')->group(function () {
    $controller = AbsenceRequestController::class;
    $startAbility = 'absence_request';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('absence')->group(function () {
    $controller = AbsenceController::class;
    $startAbility = 'absence';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', resolveAbility($startAbility, 'create')]);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

Route::prefix('attachment')->group(function () {
    $controller = AttachmentController::class;
    $startAbility = 'attachment';
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', resolveAbility($startAbility, 'list')]);
    Route::get('/{id}/download', [$controller, 'download'])->middleware('auth:api');
    Route::post('/absence/{id}', [AttachmentController::class, 'storeForAbsence'])->middleware('auth:api');
    Route::post('/absence_request/{id}', [AttachmentController::class, 'storeForAbsenceRequest'])->middleware('auth:api');
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', resolveAbility($startAbility, 'update')]);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', resolveAbility($startAbility, 'delete')]);
});

// ---------- Gestion des rôles/habiletés elle-même ----------
// Volontairement en simple auth:api + super-admin (pas de resolveAbility) :
// on évite de conditionner "qui peut gérer les permissions" par les
// permissions elles-mêmes, pour ne pas risquer de s'auto-verrouiller.

Route::prefix('role')->group(function () {
    $controller = RoleController::class;
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', 'super-admin']);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', 'super-admin']);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', 'super-admin']);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', 'super-admin']);

    Route::put('/{id}/abilities', [$controller, 'syncAbilities'])->middleware(['auth:api', 'super-admin']);
    Route::post('/{id}/abilities', [$controller, 'addAbility'])->middleware(['auth:api', 'super-admin']);
    Route::delete('/{id}/abilities', [$controller, 'removeAbility'])->middleware(['auth:api', 'super-admin']);
});

Route::prefix('ability')->group(function () {
    $controller = AbilityController::class;
    Route::get('/{id?}', [$controller, 'index'])->middleware(['auth:api', 'super-admin']);
    Route::post('/', [$controller, 'store'])->middleware(['auth:api', 'super-admin']);
    Route::put('/{id}', [$controller, 'update'])->middleware(['auth:api', 'super-admin']);
    Route::delete('/{id}', [$controller, 'delete'])->middleware(['auth:api', 'super-admin']);
});

// Volontairement en simple auth:api, sans resolveAbility — n'importe
// quel utilisateur connecté doit pouvoir consulter SES PROPRES droits,
// ça n'a pas besoin d'une permission séparée.
Route::get('/permissions', [PermissionController::class, 'show'])->middleware('auth:api');
