<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\SearchBooksService;
use Illuminate\Http\JsonResponse;

class ShowBookController
{
    public function __construct(
        private readonly SearchBooksService $service,
    ) {
    }

    public function __invoke(string $id): JsonResponse
    {
        $book = $this->service->findById($id);

        if ($book === null) {
            return response()->json([
                'code' => 'NOT_FOUND',
                'message' => 'El libro solicitado no existe o fue dado de baja.',
                'details' => [],
            ], 404);
        }

        return response()->json($book);
    }
}
