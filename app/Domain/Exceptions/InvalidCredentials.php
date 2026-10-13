<?php

namespace App\Domain\Exceptions;

class InvalidCredentials extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Invalid username or password.');
    }
}
