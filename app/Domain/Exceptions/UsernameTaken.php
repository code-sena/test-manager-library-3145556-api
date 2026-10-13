<?php

namespace App\Domain\Exceptions;

class UsernameTaken extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Username is already taken.');
    }
}
