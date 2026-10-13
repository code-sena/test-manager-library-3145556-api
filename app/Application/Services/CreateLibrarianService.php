<?php

namespace App\Application\Services;

use App\Application\Ports\PasswordHasher;
use App\Application\Ports\UserRepository;
use App\Domain\User;

class CreateLibrarianService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordHasher $passwordHasher,
    ) {
    }

    public function create(string $username, string $password, string $role): User
    {
        if ($role === 'admin') {
            throw new \App\Domain\Exceptions\ValidationError('Cannot create admin users via API.');
        }

        $normalized = strtolower(trim($username));

        if ($this->userRepository->findByUsername($normalized) !== null) {
            throw new \App\Domain\Exceptions\UsernameTaken();
        }

        $user = new User(
            id: \Illuminate\Support\Str::uuid()->toString(),
            username: $normalized,
            passwordHash: $this->passwordHasher->hash($password),
            role: $role,
        );

        $this->userRepository->save($user);

        return $user;
    }
}
