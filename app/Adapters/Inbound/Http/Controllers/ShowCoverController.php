<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Ports\BookRepository;
use App\Application\Ports\CoverStorage;
use Illuminate\Http\Response;

class ShowCoverController
{
    public function __construct(
        private readonly BookRepository $books,
        private readonly CoverStorage $covers,
    ) {
    }

    public function __invoke(string $id): Response
    {
        $book = $this->books->findById($id);

        if ($book === null || $book->coverKey === null) {
            return response()->json([
                'code' => 'NOT_FOUND',
                'message' => 'La portada solicitada no existe.',
                'details' => [],
            ], 404);
        }

        $contents = $this->covers->read($book->coverKey);

        if ($contents === null) {
            return response()->json([
                'code' => 'NOT_FOUND',
                'message' => 'La portada solicitada no existe.',
                'details' => [],
            ], 404);
        }

        return response($contents, 200, ['Content-Type' => 'application/octet-stream']);
    }
}
