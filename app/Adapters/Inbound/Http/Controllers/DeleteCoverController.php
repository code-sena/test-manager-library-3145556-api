<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\UploadCoverService;
use Illuminate\Http\JsonResponse;

class DeleteCoverController
{
    public function __construct(
        private readonly UploadCoverService $service,
    ) {
    }

    public function __invoke(string $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
