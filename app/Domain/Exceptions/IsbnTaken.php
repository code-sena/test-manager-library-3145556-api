<?php

namespace App\Domain\Exceptions;

class IsbnTaken extends \DomainException
{
    public function __construct()
    {
        parent::__construct('ISBN is already in use by another active book.');
    }
}
