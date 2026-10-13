<?php

namespace App\Adapters\Inbound\Http\Controllers;

use App\Application\Services\UpdateBookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateBookController
{
    public function __construct(
        private readonly UpdateBookService $service,
    ) {
    }

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:250',
            'author' => 'required|string|max:200',
            'category_id' => 'required|uuid',
            'total_copies' => 'required|integer|min:1',
        ]);

        $category = \Illuminate\Support\Facades\DB::table('library.category')
            ->where('id', $data['category_id'])
            ->first();

        if (! $category) {
            throw new \App\Domain\Exceptions\ValidationError('Category not found.');
        }

        $book = $this->service->update(
            $id,
            $data['title'],
            $data['author'],
            $data['category_id'],
            $category->name,
            $data['total_copies'],
        );

        return response()->json([
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn->value,
            'category' => ['id' => $book->categoryId, 'name' => $book->categoryName],
            'total_copies' => $book->totalCopies->value,
            'available_copies' => $book->availableCopies,
            'cover_key' => $book->coverKey,
        ]);
    }
}
