<?php

namespace App\Domain\Exceptions;

class ValidationError extends \DomainException
{
    public function __construct(string $message = 'Validation failed.')
    {
        parent::__construct($message);
    }
}
