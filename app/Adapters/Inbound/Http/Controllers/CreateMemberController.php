<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\CreateMemberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateMemberController
{
    public function __construct(
        private readonly CreateMemberService $service,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:200',
            'document_number' => 'required|string|max:30',
        ]);

        $member = $this->service->create(
            $data['full_name'],
            $data['document_number'],
        );

        return response()->json([
            'id' => $member->id,
            'full_name' => $member->fullName,
            'document_number' => $member->documentNumber->value,
        ], 201);
    }
}
