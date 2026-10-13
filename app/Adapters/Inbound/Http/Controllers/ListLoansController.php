<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Ports\Clock;
use App\Application\Ports\LoanRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListLoansController
{
    public function __construct(
        private readonly LoanRepository $loans,
        private readonly Clock $clock,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $size = min((int) $request->query('size', 20), 50);
        $page = max((int) $request->query('page', 1), 1);

        $from = $request->query('from') ? new \DateTimeImmutable($request->query('from')) : null;
        $to = $request->query('to') ? new \DateTimeImmutable($request->query('to')) : null;

        $items = $this->loans->list($from, $to, $request->query('status'), $request->query('member_id'), $page, $size);
        $total = $this->loans->countList($from, $to, $request->query('status'), $request->query('member_id'));
        $now = $this->clock->now();

        return response()->json([
            'items' => array_map(fn($loan) => [
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
            ], $items),
            'page' => $page,
            'size' => $size,
            'total' => $total,
        ]);
    }
}
