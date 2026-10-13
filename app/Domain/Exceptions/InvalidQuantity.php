<?php

namespace App\Domain\Exceptions;

class InvalidQuantity extends \InvalidArgumentException
{
    public function __construct(int $value)
    {
        parent::__construct("Quantity must be at least 1, got: {$value}");
    }
}
