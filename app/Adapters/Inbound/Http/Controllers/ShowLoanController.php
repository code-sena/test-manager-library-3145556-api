<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Ports\Clock;
use App\Application\Ports\LoanRepository;
use Illuminate\Http\JsonResponse;

class ShowLoanController
{
    public function __construct(
        private readonly LoanRepository $loans,
        private readonly Clock $clock,
    ) {
    }

    public function __invoke(string $id): JsonResponse
    {
        $loan = $this->loans->findById($id);

        if ($loan === null) {
            return response()->json([
                'code' => 'NOT_FOUND',
                'message' => 'El préstamo solicitado no existe.',
                'details' => [],
            ], 404);
        }

        $now = $this->clock->now();

        return response()->json([
            'id' => $loan->id,
            'member_id' => $loan->memberId,
            'loaned_at' => $loan->loanedAt->format('Y-m-d\TH:i:s\Z'),
            'status' => $loan->status($now)->value,
            'items' => array_map(fn($i) => [
                'id' => $i->id,
                'book_id' => $i->bookId,
                'book_title' => $i->bookTitle,
                'isbn' => $i->isbn,
                'category_name' => $i->categoryName,
                'due_at' => $i->dueAt->format('Y-m-d\TH:i:s\Z'),
                'returned_at' => $i->returnedAt?->format('Y-m-d\TH:i:s\Z'),
            ], $loan->items),
        ]);
    }
}
