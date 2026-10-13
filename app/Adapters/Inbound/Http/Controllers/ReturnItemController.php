<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\ReturnItemService;
use Illuminate\Http\JsonResponse;

class ReturnItemController
{
    public function __construct(
        private readonly ReturnItemService $service,
    ) {
    }

    public function __invoke(string $loanId, string $itemId): JsonResponse
    {
        $item = $this->service->return($loanId, $itemId);

        return response()->json([
            'id' => $item->id,
            'loan_id' => $item->loanId,
            'book_id' => $item->bookId,
            'book_title' => $item->bookTitle,
            'returned_at' => $item->returnedAt->format('Y-m-d\TH:i:s\Z'),
        ]);
    }
}
