<?php
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractTypeController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\TitleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

function resolveAbility(string $start_ability, string $end_ability)
{
    // TODO: Juste pour le dev, à supprimer plus tard qd l'auth sera en place
    return null;
    return $start_ability. '-'. $end_ability;
}

// TODO: Appliquer l'auth middleware sur toutes les routes
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


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

Route::prefix('contract-types')->group(function () {
    $controller = ContractTypeController::class;
    $startAbility = 'contract-types';
    Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
    Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
    Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
    Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
});

Route::prefix('contract')->group(function () {
    $controller = ContractController::class;
    $startAbility = 'contract';
    Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
    Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
    Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
    Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
});

Route::prefix('payslip')->group(function () {
    $controller = PayslipController::class;
    $startAbility = 'contract';
    Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
    Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
    Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
    Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
});

Route::prefix('payslipLine')->group(function () {
    $controller = payslipLineController::class;
    $startAbility = 'contract';
    Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
    Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
    Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
    Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
});

Route::prefix('payrollLineType')->group(function () {
    $controller = payrollLineTypeController::class;
    $startAbility = 'contract';
    Route::get('/{id?}', [$controller, 'index'])->middleware(resolveAbility($startAbility, 'list'));
    Route::post('/', [$controller, 'store'])->middleware(resolveAbility($startAbility, 'create'));
    Route::put('/{id}', [$controller, 'update'])->middleware(resolveAbility($startAbility, 'update'));
    Route::delete('/{id}', [$controller, 'delete'])->middleware(resolveAbility($startAbility, 'delete'));
});




