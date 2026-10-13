<?php

namespace App\Domain\Exceptions;

class DuplicateBookInLoan extends \DomainException
{
    public function __construct()
    {
        parent::__construct('The same book cannot appear twice in one loan.');
    }
}
