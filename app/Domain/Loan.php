<?php

namespace App\Domain;

use App\Domain\Exceptions\DuplicateBookInLoan;
use App\Domain\Exceptions\ItemAlreadyReturned;

class Loan
{
    /** @var LoanItem[] */
    public array $items = [];

    public function __construct(
        public readonly string $id,
        public readonly string $memberId,
        public readonly string $loanedByUserId,
        public readonly \DateTimeImmutable $loanedAt,
    ) {
    }

    public function addItem(LoanItem $item): void
    {
        foreach ($this->items as $existing) {
            if ($existing->bookId === $item->bookId) {
                throw new DuplicateBookInLoan();
            }
        }

        if (count($this->items) >= 3) {
            throw new \DomainException('A loan cannot have more than 3 items.');
        }

        $this->items[] = $item;
    }

    public function returnItem(string $itemId): LoanItem
    {
        foreach ($this->items as $item) {
            if ($item->id === $itemId) {
                if ($item->returnedAt !== null) {
                    throw new ItemAlreadyReturned();
                }

                $item->returnedAt = new \DateTimeImmutable();
                return $item;
            }
        }

        throw new \DomainException("Item {$itemId} not found in loan {$this->id}.");
    }

    public function status(\DateTimeImmutable $now): LoanStatus
    {
        $hasUnreturned = false;

        foreach ($this->items as $item) {
            if ($item->returnedAt === null) {
                $hasUnreturned = true;
                if ($item->dueAt < $now) {
                    return LoanStatus::Overdue;
                }
            }
        }

        if ($hasUnreturned) {
            return LoanStatus::Open;
        }

        return LoanStatus::Closed;
    }

    public function hasUnreturned(): bool
    {
        foreach ($this->items as $item) {
            if ($item->returnedAt === null) {
                return true;
            }
        }

        return false;
    }

    public function activeLoanCount(): int
    {
        $count = 0;
        foreach ($this->items as $item) {
            if ($item->returnedAt === null) {
                $count++;
            }
        }
        return $count;
    }
}
