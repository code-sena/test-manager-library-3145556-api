<?php

namespace App\Application\Services;

use App\Application\Ports\ReportReader;
use App\Domain\Exceptions\InvalidDateRange;

class MostBorrowedReportService
{
    public function __construct(
        private readonly ReportReader $reportReader,
    ) {
    }

    public function generate(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        if ($to < $from) {
            throw new InvalidDateRange($from, $to);
        }

        return $this->reportReader->mostBorrowed($from, $to);
    }
}
