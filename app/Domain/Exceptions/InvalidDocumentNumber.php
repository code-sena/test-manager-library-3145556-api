<?php

namespace App\Domain\Exceptions;

class InvalidDocumentNumber extends \InvalidArgumentException
{
    public function __construct(string $value)
    {
        parent::__construct('Document number must not be empty.');
    }
}
