<?php

namespace App\Domain;

use App\Domain\Exceptions\InvalidQuantity;
use App\Domain\ValueObjects\Isbn;
use App\Domain\ValueObjects\Quantity;

class Book
{
    public function __construct(
        public readonly string $id,
        public string $title,
        public string $author,
        public Isbn $isbn,
        public string $categoryId,
        public string $categoryName,
        public Quantity $totalCopies,
        public ?int $availableCopies = null,
        public ?string $coverKey = null,
        public ?\DateTimeImmutable $deletedAt = null,
    ) {
        if ($availableCopies === null) {
            $availableCopies = $totalCopies->value;
        }

        $this->assertInvariants($availableCopies);
    }

    public function withdraw(): void
    {
        if ($this->availableCopies <= 0) {
            throw new \App\Domain\Exceptions\BookNotAvailable($this->title);
        }

        $this->availableCopies--;
    }

    public function restock(): void
    {
        if ($this->availableCopies >= $this->totalCopies->value) {
            throw new \DomainException('Available copies cannot exceed total copies.');
        }

        $this->availableCopies++;
    }

    public function updateTotal(int $newTotal): void
    {
        $difference = $newTotal - $this->totalCopies->value;
        $newAvailable = $this->availableCopies + $difference;

        if ($newAvailable < 0) {
            throw new InvalidQuantity($newAvailable);
        }

        $this->totalCopies = new Quantity($newTotal);
        $this->availableCopies = $newAvailable;
    }

    public function delete(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    private function assertInvariants(int $available): void
    {
        if ($available < 0 || $available > $this->totalCopies->value) {
            throw new \DomainException("Available copies ({$available}) must be between 0 and total ({$this->totalCopies->value}).");
        }
    }
}
