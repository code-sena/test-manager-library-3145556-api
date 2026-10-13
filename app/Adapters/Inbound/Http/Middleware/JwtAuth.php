<?php

namespace App\Adapters\Inbound\Http\Middleware;

use App\Adapters\Outbound\Security\JwtTokenIssuer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuth
{
    public function __construct(
        private readonly JwtTokenIssuer $tokenIssuer,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');

        if (! preg_match('/^Bearer\s+(.+)$/', $header, $matches)) {
            return response()->json([
                'code' => 'INVALID_CREDENTIALS',
                'message' => 'Se requiere un token de autenticación.',
                'details' => [],
            ], 401);
        }

        $token = $matches[1];

        try {
            $decoded = $this->tokenIssuer->decode($token);
        } catch (\Exception) {
            return response()->json([
                'code' => 'INVALID_CREDENTIALS',
                'message' => 'El token es inválido o ha expirado.',
                'details' => [],
            ], 401);
        }

        $request->setUserResolver(fn() => $decoded);

        return $next($request);
    }
}
