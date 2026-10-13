<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\CreateLibrarianService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateUserController
{
    public function __construct(
        private readonly CreateLibrarianService $service,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string|max:120',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:librarian,admin',
        ]);

        $user = $this->service->create($data['username'], $data['password'], $data['role']);

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
        ], 201);
    }
}
