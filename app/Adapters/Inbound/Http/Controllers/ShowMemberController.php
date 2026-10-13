<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Ports\LoanRepository;
use App\Application\Ports\MemberRepository;
use Illuminate\Http\JsonResponse;

class ShowMemberController
{
    public function __construct(
        private readonly MemberRepository $members,
        private readonly LoanRepository $loans,
    ) {
    }

    public function __invoke(string $id): JsonResponse
    {
        $member = $this->members->findById($id);

        if ($member === null) {
            return response()->json([
                'code' => 'NOT_FOUND',
                'message' => 'El socio solicitado no existe.',
                'details' => [],
            ], 404);
        }

        return response()->json([
            'id' => $member->id,
            'full_name' => $member->fullName,
            'document_number' => $member->documentNumber->value,
            'active_loans' => $this->loans->countActiveLoansForMember($member->id),
        ]);
    }
}
