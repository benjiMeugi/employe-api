<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\AbsenceRequestController;
use App\Http\Controllers\AbsenceTypeController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CareerEventController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractTypeController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\DismissalController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveBalanceController;
use App\Http\Controllers\LeaveGrantController;
use App\Http\Controllers\OngoingAbsenceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PayrollLineTypeController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\PayslipLineController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\RetirementController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\UnitAbsenceScheduleController;
use App\Http\Controllers\UnitController;
use App\Http\Middleware\VerifyAuthApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Génère le nom d'habileté attendu par CheckAbility (ex: "employe-list"),
 * préfixé par "can:" pour que le middleware CheckAbility vérifie bien
 * la permission correspondante.
 */
function resolveAbility(string $abilityName, string $action)
{
    return 'ability:' . $abilityName . '-' . $action;
}

// Helper pour enregistrer rapidement les 5 routes standard d'un CRUD API
function registerApiCrud(string $prefix, string $controller)
{
    Route::prefix($prefix)->group(function () use ($controller, $prefix) {
        $abilityName = $prefix;

        Route::get('/', [$controller, 'index'])->middleware(resolveAbility($abilityName, 'list'));
        Route::get('/{id}', [$controller, 'show'])->middleware(resolveAbility($abilityName, 'show'));
        Route::post('/', [$controller, 'store'])->middleware(resolveAbility($abilityName, 'create'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($abilityName, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($abilityName, 'delete'));
    });
}

Route::middleware([VerifyAuthApiToken::class])->group(function () {

    // CRUDs standards factorisés via registerApiCrud
    registerApiCrud('title', TitleController::class);
    registerApiCrud('position', PositionController::class);
    registerApiCrud('classification', ClassificationController::class);
    registerApiCrud('employe', EmployeController::class);
    registerApiCrud('retirement', RetirementController::class);
    registerApiCrud('dismissal', DismissalController::class);
    registerApiCrud('sanction', SanctionController::class);
    registerApiCrud('assignment', AssignmentController::class);
    registerApiCrud('unit', UnitController::class);
    registerApiCrud('promotion', PromotionController::class);
    registerApiCrud('absence_type', AbsenceTypeController::class);
    registerApiCrud('leave_grant', LeaveGrantController::class);
    registerApiCrud('absence_request', AbsenceRequestController::class);
    registerApiCrud('absence', AbsenceController::class);
    registerApiCrud('holiday', HolidayController::class);
    registerApiCrud('contracttype', ContractTypeController::class);
    registerApiCrud('contract', ContractController::class);
    registerApiCrud('payslip', PayslipController::class);
    registerApiCrud('payslipLine', PayslipLineController::class);
    registerApiCrud('payrollLineType', PayrollLineTypeController::class);

    // Cas spécifique : career_event (pas de store/update)
    Route::prefix('career_event')->group(function () {
        $controller = CareerEventController::class;
        $startAbility = 'career_event';
        Route::get('/', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::get('/{id}', [$controller, 'show'])->middleware(resolveAbility($startAbility, 'show'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
    });

    // Pièces jointes
    Route::prefix('attachment')->group(function () {
        $controller = AttachmentController::class;
        $startAbility = 'attachment';
        Route::get('/', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::get('/{id}', [$controller, 'show'])->middleware(resolveAbility($startAbility, 'show'));
        Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
        Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
        Route::get('/{id}/download', [$controller, 'download'])->middleware(resolveAbility($startAbility, 'download'));
        Route::post('/absence/{id}', [AttachmentController::class, 'storeForAbsence'])->middleware(resolveAbility($startAbility, 'create'));
        Route::post('/absence_request/{id}', [AttachmentController::class, 'storeForAbsenceRequest'])->middleware(resolveAbility($startAbility, 'create'));
    });

    // Gestion des rôles/habiletés gérée totalement par l'API d'authentification

    // Solde de congés
    Route::prefix('leave_balance')->group(function () {
        $controller = LeaveBalanceController::class;
        $startAbility = 'leave_balance';

        Route::get('/current', [$controller, 'current'])->middleware(resolveAbility($startAbility, 'current'));
        Route::get('/', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::get('/employee/{employeeId}', [$controller, 'forEmployee'])->middleware(resolveAbility($startAbility, 'list'));
    });

    // Absence en cours
    Route::prefix('ongoing_absence')->group(function () {
        $controller = OngoingAbsenceController::class;
        $startAbility = 'ongoing_absence';

        Route::get('/current', [$controller, 'current'])->middleware(resolveAbility($startAbility, 'current'));
        Route::get('/', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::get('/employee/{employeeId}', [$controller, 'forEmployee'])->middleware(resolveAbility($startAbility, 'list'));
    });

    // Planning d'absence par unité
    Route::prefix('unit_absence_schedule')->group(function () {
        $controller = UnitAbsenceScheduleController::class;
        $startAbility = 'unit_absence_schedule';

        Route::get('/', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
        Route::get('/staffing', [$controller, 'staffing'])->middleware(resolveAbility($startAbility, 'list'));
        Route::get('/unit/{unitId}', [$controller, 'forUnit'])->middleware(resolveAbility($startAbility, 'list'));
        Route::get('/unit/{unitId}/coverage', [$controller, 'coverage'])->middleware(resolveAbility($startAbility, 'coverage'));
    });

    // Permissions de l'utilisateur connecté
    Route::get('/permissions', [PermissionController::class, 'show']);
});