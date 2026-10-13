<?php

namespace App\Adapters\Inbound\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null || ! isset($user->role) || ! in_array($user->role, $roles, true)) {
            return response()->json([
                'code' => 'FORBIDDEN_ROLE',
                'message' => 'No tiene permiso para realizar esta acción.',
                'details' => [],
            ], 403);
        }

        return $next($request);
    }
}
