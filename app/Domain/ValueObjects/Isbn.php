<?php

namespace App\Domain\ValueObjects;

use App\Domain\Exceptions\InvalidIsbn;

final readonly class Isbn
{
    public function __construct(
        public string $value,
    ) {
        if (! preg_match('/^\d{13}$/', $value)) {
            throw new InvalidIsbn($value);
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
