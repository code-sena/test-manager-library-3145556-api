<?php

namespace App\Domain\Exceptions;

class ItemAlreadyReturned extends \DomainException
{
    public function __construct()
    {
        parent::__construct('This item has already been returned.');
    }
}
