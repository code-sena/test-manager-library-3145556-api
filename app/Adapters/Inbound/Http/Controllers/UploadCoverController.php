<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\UploadCoverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadCoverController
{
    public function __construct(
        private readonly UploadCoverService $service,
    ) {
    }

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $file = $request->file('file');
        $contents = file_get_contents($file->getRealPath());
        $contentType = $file->getMimeType();

        $key = $this->service->upload($id, $contents, $contentType);

        return response()->json(['cover_key' => $key]);
    }
}
