<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (NotFoundHttpException $notFoundHttpException, Request $request) {
            if ($request->is('api/*') || $request->is('api')) {
                return response()->json([
                    'message' => 'result not found.',
                ], 404);
            }
        });

        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            if ($request->is('api/*') || $request->is('api')) {
                return true;
            }

            return $request->expectsJson();
        });

        if (App::environment('production')) {
            Integration::handles($exceptions);
        }

    })->create();
