<?php

namespace App\Domain\Exceptions;

class BookHasOpenLoans extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Cannot delete a book with open loans.');
    }
}
