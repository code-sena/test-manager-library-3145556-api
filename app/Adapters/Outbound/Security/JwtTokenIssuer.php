<?php

namespace App\Adapters\Outbound\Security;

use App\Application\Ports\TokenIssuer;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtTokenIssuer implements TokenIssuer
{
    public function issue(string $userId, string $role): string
    {
        $secret = env('JWT_SECRET');
        $issuedAt = time();
        $expiresAt = $issuedAt + 28800;

        $payload = [
            'sub' => $userId,
            'role' => $role,
            'iat' => $issuedAt,
            'exp' => $expiresAt,
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    public function decode(string $token): object
    {
        $secret = env('JWT_SECRET');

        return JWT::decode($token, new Key($secret, 'HS256'));
    }
}
