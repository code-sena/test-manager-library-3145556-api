<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\DeleteBookService;
use Illuminate\Http\JsonResponse;

class DeleteBookController
{
    public function __construct(
        private readonly DeleteBookService $service,
    ) {
    }

    public function __invoke(string $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
