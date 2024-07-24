<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // When form validation fails return json response with validation errors
        $exceptions->render(function (
            \Illuminate\Validation\ValidationException $exception,
            \Illuminate\Http\Request $request
        ) {
            if ($request->is('api/*')) {
                return response()->json(
                    $exception->errors(),
                    \Illuminate\Http\JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        });

        // When model not found return json response with 404 status code
        $exceptions->render(function (
            \Illuminate\Database\Eloquent\ModelNotFoundException $exception,
            \Illuminate\Http\Request $request
        ) {
            if ($request->is('api/*')) {
                return response()->json(
                    ['message' => 'Not Found'],
                    \Illuminate\Http\JsonResponse::HTTP_NOT_FOUND
                );
            }
        });

        // When route not found return json response with 404 status code
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception,
            \Illuminate\Http\Request $request
        ) {
            return response()->json(
                ['message' => 'Not Found'],
                \Illuminate\Http\JsonResponse::HTTP_NOT_FOUND
            );
        });

        // When HttpJSONException is thrown return json response with message, help and status
        $exceptions->render(function (
            \App\Exceptions\HttpJSONException $exception,
            \Illuminate\Http\Request $request
        ) {
            return response()->json(
                [
                    'message' => $exception->getMessage(),
                    'help' => $exception->help,
                ],
                $exception->status
            );
        });

        // When method not allowed return json response with 405 status code
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $exception,
            \Illuminate\Http\Request $request
        ) {
            return response()->json(
                ['message' => 'Method Not Allowed'],
                \Illuminate\Http\JsonResponse::HTTP_METHOD_NOT_ALLOWED
            );
        });
    })
    ->withProviders([
        Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
    ])
    ->withEvents(
        discover: __DIR__ . '/../app/Jobs',
    )
    ->withEvents(
        discover: __DIR__ . '/../app/Listeners',
    )
    ->create();
