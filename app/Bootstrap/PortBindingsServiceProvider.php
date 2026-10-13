<?php

namespace App\Bootstrap;

use App\Adapters\Outbound\Security\Argon2PasswordHasher;
use App\Adapters\Outbound\Security\JwtTokenIssuer;
use App\Adapters\Outbound\Storage\DiskCoverStorage;
use App\Application\Ports\Clock;
use App\Application\Ports\PasswordHasher;
use App\Application\Ports\TokenIssuer;
use Illuminate\Support\ServiceProvider;

class PortBindingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Clock::class, \App\Adapters\Outbound\SystemClock::class);
        $this->app->bind(PasswordHasher::class, Argon2PasswordHasher::class);
        $this->app->bind(TokenIssuer::class, JwtTokenIssuer::class);
        $this->app->bind(\App\Application\Ports\CoverStorage::class, DiskCoverStorage::class);

        $this->app->bind(\App\Application\Ports\BookRepository::class, \App\Adapters\Outbound\Persistence\BookRepositoryImpl::class);
        $this->app->bind(\App\Application\Ports\LoanRepository::class, \App\Adapters\Outbound\Persistence\LoanRepositoryImpl::class);
        $this->app->bind(\App\Application\Ports\MemberRepository::class, \App\Adapters\Outbound\Persistence\MemberRepositoryImpl::class);
        $this->app->bind(\App\Application\Ports\UserRepository::class, \App\Adapters\Outbound\Persistence\UserRepositoryImpl::class);
        $this->app->bind(\App\Application\Ports\ReportReader::class, \App\Adapters\Outbound\Persistence\ReportReaderImpl::class);
    }
}
