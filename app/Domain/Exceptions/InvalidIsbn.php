<?php

namespace App\Domain\Exceptions;

class InvalidIsbn extends \InvalidArgumentException
{
    public function __construct(string $isbn)
    {
        parent::__construct("ISBN must be exactly 13 digits, got: {$isbn}");
    }
}
