<?php

namespace App\Domain;

class LoanItem
{
    public function __construct(
        public readonly string $id,
        public readonly string $loanId,
        public readonly string $bookId,
        public readonly string $bookTitle,
        public readonly string $isbn,
        public readonly string $categoryName,
        public readonly \DateTimeImmutable $dueAt,
        public ?\DateTimeImmutable $returnedAt = null,
    ) {
    }

    public function daysLate(\DateTimeImmutable $now): int
    {
        if ($this->returnedAt !== null) {
            return 0;
        }

        if ($this->dueAt >= $now) {
            return 0;
        }

        return (int) $now->diff($this->dueAt)->format('%r%a');
    }
}
