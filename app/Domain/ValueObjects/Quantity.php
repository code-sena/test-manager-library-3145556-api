<?php

namespace App\Domain\ValueObjects;

use App\Domain\Exceptions\InvalidQuantity;

final readonly class Quantity
{
    public function __construct(
        public int $value,
    ) {
        if ($value < 1) {
            throw new InvalidQuantity($value);
        }
    }
}
