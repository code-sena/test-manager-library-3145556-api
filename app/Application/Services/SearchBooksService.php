<?php

namespace App\Application\Services;

use App\Application\Ports\BookRepository;
use App\Domain\Book;

class SearchBooksService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
    ) {
    }

    public function search(?string $q, ?string $categoryId, int $page, int $size): array
    {
        $items = $this->bookRepository->search($q, $categoryId, $page, $size);
        $total = $this->bookRepository->countSearch($q, $categoryId);

        return [
            'items' => array_map(fn(Book $book) => $this->toArray($book), $items),
            'page' => $page,
            'size' => $size,
            'total' => $total,
        ];
    }

    public function findById(string $id): ?array
    {
        $book = $this->bookRepository->findById($id);
        if ($book === null) {
            return null;
        }

        return $this->toArray($book);
    }

    private function toArray(Book $book): array
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn->value,
            'category' => ['id' => $book->categoryId, 'name' => $book->categoryName],
            'total_copies' => $book->totalCopies->value,
            'available_copies' => $book->availableCopies,
            'cover_key' => $book->coverKey,
        ];
    }
}
