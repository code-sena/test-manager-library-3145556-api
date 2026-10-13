<?php

namespace App\Application\Ports;

use App\Domain\Loan;
use App\Domain\LoanItem;

interface LoanRepository
{
    public function findById(string $id): ?Loan;

    public function save(Loan $loan): void;

    public function list(?\DateTimeImmutable $from, ?\DateTimeImmutable $to, ?string $status, ?string $memberId, int $page, int $size): array;

    public function countList(?\DateTimeImmutable $from, ?\DateTimeImmutable $to, ?string $status, ?string $memberId): int;

    public function findOverdueItems(): array;

    public function countActiveLoansForMember(string $memberId): int;

    public function hasOverdueLoansForMember(string $memberId): bool;

    public function returnItem(string $loanId, string $itemId): LoanItem;
}
