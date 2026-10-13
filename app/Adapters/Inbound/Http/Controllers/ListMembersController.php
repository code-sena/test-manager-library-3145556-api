<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Ports\MemberRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListMembersController
{
    public function __construct(
        private readonly MemberRepository $members,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $size = min((int) $request->query('size', 20), 50);
        $page = max((int) $request->query('page', 1), 1);
        $q = $request->query('q');

        $items = $this->members->search($q, $page, $size);
        $total = $this->members->countSearch($q);

        return response()->json([
            'items' => array_map(fn($m) => [
                'id' => $m->id,
                'full_name' => $m->fullName,
                'document_number' => $m->documentNumber->value,
            ], $items),
            'page' => $page,
            'size' => $size,
            'total' => $total,
        ]);
    }
}
