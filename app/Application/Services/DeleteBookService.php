<?php

namespace App\Application\Services;

use App\Application\Ports\BookRepository;
use App\Domain\Exceptions\BookHasOpenLoans;

class DeleteBookService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
    ) {
    }

    public function delete(string $id): void
    {
        $book = $this->bookRepository->findById($id);
        if ($book === null) {
            throw new \App\Domain\Exceptions\ValidationError("Book not found: {$id}");
        }

        if ($this->bookRepository->hasOpenLoans($id)) {
            throw new BookHasOpenLoans();
        }

        $book->delete();
        $this->bookRepository->save($book);
    }
}
