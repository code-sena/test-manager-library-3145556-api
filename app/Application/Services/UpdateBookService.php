<?php

namespace App\Application\Services;

use App\Application\Ports\BookRepository;
use App\Domain\Book;

class UpdateBookService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
    ) {
    }

    public function update(string $id, string $title, string $author, string $categoryId, string $categoryName, int $totalCopies): Book
    {
        $book = $this->bookRepository->findById($id);
        if ($book === null) {
            throw new \App\Domain\Exceptions\ValidationError("Book not found: {$id}");
        }

        $book->title = trim($title);
        $book->author = trim($author);
        $book->categoryId = $categoryId;
        $book->categoryName = $categoryName;
        $book->updateTotal($totalCopies);

        $this->bookRepository->save($book);

        return $book;
    }
}
