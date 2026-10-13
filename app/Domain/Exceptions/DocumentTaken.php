<?php

namespace App\Domain\Exceptions;

class DocumentTaken extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Document number is already registered.');
    }
}
