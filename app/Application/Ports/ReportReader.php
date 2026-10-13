<?php

namespace App\Application\Ports;

interface ReportReader
{
    public function mostBorrowed(\DateTimeImmutable $from, \DateTimeImmutable $to): array;
}
