<?php

namespace App\Domain\Exceptions;

class InvalidDateRange extends \InvalidArgumentException
{
    public function __construct(\DateTimeImmutable $start, \DateTimeImmutable $end)
    {
        parent::__construct('End date must be greater than or equal to start date.');
    }
}
