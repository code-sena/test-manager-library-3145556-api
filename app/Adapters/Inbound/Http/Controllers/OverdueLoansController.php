<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\OverdueService;
use Illuminate\Http\JsonResponse;

class OverdueLoansController
{
    public function __construct(
        private readonly OverdueService $service,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return response()->json(['items' => $this->service->listOverdue()]);
    }
}
