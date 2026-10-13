<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\CreateBookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateBookController
{
    public function __construct(
        private readonly CreateBookService $service,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:250',
            'author' => 'required|string|max:200',
            'isbn' => 'required|string|size:13',
            'category_id' => 'required|uuid',
            'total_copies' => 'required|integer|min:1',
        ]);

        $category = $this->getCategory($data['category_id']);

        $book = $this->service->create(
            $data['title'],
            $data['author'],
            $data['isbn'],
            $data['category_id'],
            $category['name'],
            $data['total_copies'],
        );

        return response()->json([
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn->value,
            'category' => $category,
            'total_copies' => $book->totalCopies->value,
            'available_copies' => $book->availableCopies,
            'cover_key' => $book->coverKey,
        ], 201);
    }

    private function getCategory(string $categoryId): array
    {
        $category = \Illuminate\Support\Facades\DB::table('library.category')
            ->where('id', $categoryId)
            ->first();

        if (! $category) {
            throw new \App\Domain\Exceptions\ValidationError('Category not found.');
        }

        return ['id' => $category->id, 'name' => $category->name];
    }
}
