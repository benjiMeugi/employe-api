<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Exception $e, Request $request) {
        if ($request->is('api/*')) {
            Log::error("Api error: " . $e->getMessage() . " " . $e->getFile() . ":" . $e->getLine());

            $status = match (true) {
                $e instanceof ModelNotFoundException,
                $e instanceof NotFoundHttpException => 404,
                $e instanceof MethodNotAllowedHttpException => 405,
                default => 500,
            };

            return response()->json([
                'success' => false,
                'code' => $status,
                'body' => [
                    'error_message' => app()->isProduction() && $status === 500
                        ? 'Une erreur est survenue'
                        : $e->getMessage(),
                    'errors' => null,
                    'response_data' => null,
                ],
            ], $status);
        }
    });
    })->create();
