<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\SearchBooksService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchBooksController
{
    public function __construct(
        private readonly SearchBooksService $service,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $size = min((int) $request->query('size', 20), 50);
        $page = max((int) $request->query('page', 1), 1);

        $result = $this->service->search(
            $request->query('q'),
            $request->query('category_id'),
            $page,
            $size,
        );

        return response()->json($result);
    }
}
