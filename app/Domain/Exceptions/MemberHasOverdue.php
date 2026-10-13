<?php

namespace App\Domain\Exceptions;

class MemberHasOverdue extends \DomainException
{
    public function __construct(string $memberName)
    {
        parent::__construct("Member has overdue loans: {$memberName}");
    }
}
