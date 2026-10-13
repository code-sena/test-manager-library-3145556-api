<?php

namespace App\Domain\ValueObjects;

use App\Domain\Exceptions\InvalidDocumentNumber;

final readonly class DocumentNumber
{
    public function __construct(
        public string $value,
    ) {
        $trimmed = trim($value);
        if ($trimmed === '') {
            throw new InvalidDocumentNumber($value);
        }
    }
}
