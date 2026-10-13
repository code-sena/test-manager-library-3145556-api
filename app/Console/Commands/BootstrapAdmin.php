<?php

namespace App\Console\Commands;

use App\Application\Ports\PasswordHasher;
use App\Application\Ports\UserRepository;
use App\Domain\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BootstrapAdmin extends Command
{
    protected $signature = 'library:bootstrap-admin';

    protected $description = 'Create the initial admin user from environment variables';

    public function handle(UserRepository $userRepository, PasswordHasher $passwordHasher): int
    {
        $username = env('ADMIN_USERNAME', 'admin');
        $password = env('ADMIN_PASSWORD');

        if (! $password) {
            $this->error('ADMIN_PASSWORD environment variable is not set.');
            return 1;
        }

        $normalized = strtolower(trim($username));

        if ($userRepository->findByUsername($normalized) !== null) {
            $this->info("Admin user '{$normalized}' already exists.");
            return 0;
        }

        $user = new User(
            id: Str::uuid()->toString(),
            username: $normalized,
            passwordHash: $passwordHasher->hash($password),
            role: 'admin',
        );

        $userRepository->save($user);

        $this->info("Admin user '{$normalized}' created successfully.");

        return 0;
    }
}
