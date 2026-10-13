<?php

namespace App\Domain\ValueObjects;

use App\Domain\Exceptions\InvalidDateRange;

final readonly class DateRange
{
    public function __construct(
        public \DateTimeImmutable $start,
        public \DateTimeImmutable $end,
    ) {
        if ($end < $start) {
            throw new InvalidDateRange($start, $end);
        }
    }
}
