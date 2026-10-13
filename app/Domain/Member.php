<?php

namespace App\Domain;

use App\Domain\ValueObjects\DocumentNumber;

class Member
{
    public function __construct(
        public readonly string $id,
        public string $fullName,
        public DocumentNumber $documentNumber,
    ) {
    }
}
