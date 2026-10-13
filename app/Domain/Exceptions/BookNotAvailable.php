<?php

namespace App\Domain\Exceptions;

class BookNotAvailable extends \DomainException
{
    public function __construct(string $title)
    {
        parent::__construct("No copies available for book: {$title}");
    }
}
