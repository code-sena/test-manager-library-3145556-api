<?php

use App\Adapters\Inbound\Http\Middleware\JwtAuth;
use App\Adapters\Inbound\Http\Middleware\RequireRole;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.jwt' => JwtAuth::class,
            'role' => RequireRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (DomainException $e, Request $request) {
            $mapped = \App\Adapters\Inbound\Http\ErrorMapper::toResponse($e);

            if ($mapped !== null && $request->is('api/*')) {
                return response()->json([
                    'code' => $mapped['code'],
                    'message' => $mapped['message'],
                    'details' => $mapped['details'],
                ], $mapped['status']);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'code' => 'VALIDATION_ERROR',
                'message' => 'Los datos enviados no son válidos.',
                'details' => collect($e->errors())->map(fn($messages, $field) => [
                    'field' => $field,
                    'issue' => $messages[0],
                ])->values()->all(),
            ], 422);
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json([
                'code' => 'INVALID_CREDENTIALS',
                'message' => 'Credenciales inválidas.',
                'details' => [],
            ], 401);
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            return response()->json([
                'code' => 'NOT_FOUND',
                'message' => 'El recurso solicitado no existe.',
                'details' => [],
            ], 404);
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->json([
                'code' => 'NOT_FOUND',
                'message' => 'El recurso solicitado no existe.',
                'details' => [],
            ], 404);
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'code' => 'INTERNAL_ERROR',
                    'message' => 'Ocurrió un error inesperado.',
                    'details' => [],
                ], 500);
            }
        });
    })
    ->create();
