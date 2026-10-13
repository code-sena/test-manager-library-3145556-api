<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\ListCategoriesService;
use Illuminate\Http\JsonResponse;

class ListCategoriesController
{
    public function __construct(
        private readonly ListCategoriesService $service,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return response()->json($this->service->list());
    }
}
