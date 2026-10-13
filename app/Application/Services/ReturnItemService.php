<?php

namespace App\Application\Services;

use App\Application\Ports\LoanRepository;
use App\Domain\LoanItem;
use Illuminate\Support\Facades\DB;

class ReturnItemService
{
    public function __construct(
        private readonly LoanRepository $loanRepository,
    ) {
    }

    public function return(string $loanId, string $itemId): LoanItem
    {
        $item = $this->loanRepository->returnItem($loanId, $itemId);

        DB::table('library.book')
            ->where('id', $item->bookId)
            ->whereRaw('available_copies < total_copies')
            ->update(['available_copies' => DB::raw('available_copies + 1')]);

        return $item;
    }
}
