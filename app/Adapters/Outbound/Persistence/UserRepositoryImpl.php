<?php

namespace App\Adapters\Outbound\Persistence;

use App\Application\Ports\UserRepository;
use App\Domain\User as DomainUser;
use Illuminate\Support\Facades\DB;

class UserRepositoryImpl implements UserRepository
{
    public function findByUsername(string $username): ?DomainUser
    {
        $row = DB::table('library."user"')->where('username', $username)->first();

        if (! $row) {
            return null;
        }

        return $this->toDomain($row);
    }

    public function save(DomainUser $user): void
    {
        DB::table('library."user"')->updateOrInsert(
            ['id' => $user->id],
            [
                'username' => $user->username,
                'password_hash' => $user->passwordHash,
                'role' => $user->role,
            ]
        );
    }

    private function toDomain(object $row): DomainUser
    {
        return new DomainUser(
            id: $row->id,
            username: $row->username,
            passwordHash: $row->password_hash,
            role: $row->role,
        );
    }
}
