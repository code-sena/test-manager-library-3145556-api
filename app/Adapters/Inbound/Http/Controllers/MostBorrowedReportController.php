<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\MostBorrowedReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MostBorrowedReportController
{
    public function __construct(
        private readonly MostBorrowedReportService $service,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $from = new \DateTimeImmutable($data['from']);
        $to = new \DateTimeImmutable($data['to']);

        $rows = $this->service->generate($from, $to);

        return response()->json([
            'from' => $data['from'],
            'to' => $data['to'],
            'rows' => $rows,
        ]);
    }
}
