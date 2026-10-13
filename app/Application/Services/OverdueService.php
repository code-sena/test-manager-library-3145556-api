<?php

namespace App\Application\Services;

use App\Application\Ports\LoanRepository;

class OverdueService
{
    public function __construct(
        private readonly LoanRepository $loanRepository,
    ) {
    }

    public function listOverdue(): array
    {
        return $this->loanRepository->findOverdueItems();
    }
}
