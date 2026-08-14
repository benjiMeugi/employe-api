<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CareerEventController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\DismissalController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PromotionController;
use \App\Http\Controllers\RetirementController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\UnitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

function resolveAbility(string $start_ability, string $end_ability)
{
    // TODO: Juste pour le dev, à supprimer plus tard qd l'auth sera en place
    return null;
    //return $start_ability. '-'. $end_ability;
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
