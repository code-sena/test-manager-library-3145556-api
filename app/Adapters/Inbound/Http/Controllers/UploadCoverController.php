<?php

namespace App\Adapters\Inbound\Http\Controllers;

use Illuminate\Http\JsonResponse;

class UploadCoverController
{
    public function __invoke(): JsonResponse
    {
        return response()->json(['message' => 'Not yet implemented'], 501);
    }
}
