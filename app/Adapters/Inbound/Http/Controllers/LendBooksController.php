<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Ports\Clock;
use App\Application\Services\LendBooksService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LendBooksController
{
    public function __construct(
        private readonly LendBooksService $service,
        private readonly Clock $clock,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'member_id' => 'required|uuid',
            'items' => 'required|array|min:1|max:3',
            'items.*.book_id' => 'required|uuid',
        ]);

        $user = $request->user();
        $loan = $this->service->lend(
            $data['member_id'],
            array_map(fn($i) => $i['book_id'], $data['items']),
            $user->sub,
            $this->clock->now(),
        );

        return response()->json([
            'id' => $loan->id,
            'member_id' => $loan->memberId,
            'loaned_at' => $loan->loanedAt->format('Y-m-d\TH:i:s\Z'),
            'status' => $loan->status($this->clock->now())->value,
            'items' => array_map(fn($i) => [
                'id' => $i->id,
                'book_id' => $i->bookId,
                'book_title' => $i->bookTitle,
                'isbn' => $i->isbn,
                'category_name' => $i->categoryName,
                'due_at' => $i->dueAt->format('Y-m-d\TH:i:s\Z'),
                'returned_at' => null,
            ], $loan->items),
        ], 201);
    }
}
