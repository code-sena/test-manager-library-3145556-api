<?php

namespace App\Domain\Exceptions;

class MemberLimitExceeded extends \DomainException
{
    public function __construct(int $activeLoans)
    {
        parent::__construct("Member already has {$activeLoans} active loans (max 3).");
    }
}
