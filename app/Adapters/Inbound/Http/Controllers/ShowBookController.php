<?php

namespace App\Adapters\Inbound\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ShowBookController
{
    public function __invoke(): JsonResponse
    {
        return response()->json(['message' => 'Not yet implemented'], 501);
    }
}
