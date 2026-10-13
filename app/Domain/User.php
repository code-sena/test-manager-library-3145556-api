<?php

namespace App\Domain;

class User
{
    public function __construct(
        public readonly string $id,
        public string $username,
        public string $passwordHash,
        public string $role,
    ) {
        if (! in_array($role, ['admin', 'librarian'], true)) {
            throw new \DomainException("Invalid role: {$role}");
        }
    }
}
