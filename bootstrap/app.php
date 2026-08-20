<?php

use App\Http\Middleware\EnsureSuperAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'super-admin' => EnsureSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Exception $e, Request $request) {
        if ($request->is('api/*')) {
            Log::error("Api error: " . $e->getMessage() . " " . $e->getFile() . ":" . $e->getLine());

            return response()->json([
                'success' => false,
                'code' => 500,
                'body' => [
                    'error_message' => app()->isProduction() ? 'Une erreur est survenue' : $e->getMessage(),
                    'errors' => null,
                    'response_data' => null,
                ],
            ], 500);
        }
    });
    })->create();
