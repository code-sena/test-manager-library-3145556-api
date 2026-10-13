<?php

namespace App\Application\Services;

use App\Application\Ports\PasswordHasher;
use App\Application\Ports\TokenIssuer;
use App\Application\Ports\UserRepository;

class LoginService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordHasher $passwordHasher,
        private readonly TokenIssuer $tokenIssuer,
    ) {
    }

    public function login(string $username, string $password): array
    {
        $normalized = strtolower(trim($username));
        $user = $this->userRepository->findByUsername($normalized);

        if ($user === null || ! $this->passwordHasher->verify($password, $user->passwordHash)) {
            throw new \App\Domain\Exceptions\InvalidCredentials();
        }

        $token = $this->tokenIssuer->issue($user->id, $user->role);

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 28800,
            'role' => $user->role,
        ];
    }
}
