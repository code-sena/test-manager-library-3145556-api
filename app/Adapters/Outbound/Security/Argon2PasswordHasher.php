<?php

namespace App\Adapters\Outbound\Security;

use App\Application\Ports\PasswordHasher;

class Argon2PasswordHasher implements PasswordHasher
{
    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
