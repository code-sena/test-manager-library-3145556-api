<?php

namespace App\Application\Ports;

use App\Domain\Member;

interface MemberRepository
{
    public function findById(string $id): ?Member;

    public function findByDocument(string $document): ?Member;

    public function search(?string $q, int $page, int $size): array;

    public function countSearch(?string $q): int;

    public function save(Member $member): void;
}
