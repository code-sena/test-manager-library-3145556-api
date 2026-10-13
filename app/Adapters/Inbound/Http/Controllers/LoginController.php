<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\LoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController
{
    public function __construct(
        private readonly LoginService $loginService,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->loginService->login($data['username'], $data['password']);

        return response()->json($result);
    }
}
