<?php

namespace App\Application\Services;

use App\Application\Ports\BookRepository;
use App\Domain\Book;
use App\Domain\Exceptions\IsbnTaken;
use App\Domain\ValueObjects\Isbn;
use App\Domain\ValueObjects\Quantity;
use Illuminate\Support\Str;

class CreateBookService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
    ) {
    }

    public function create(string $title, string $author, string $isbn, string $categoryId, string $categoryName, int $totalCopies): Book
    {
        $existing = $this->bookRepository->findByIsbn($isbn);
        if ($existing !== null) {
            throw new IsbnTaken();
        }

        $book = new Book(
            id: Str::uuid()->toString(),
            title: trim($title),
            author: trim($author),
            isbn: new Isbn($isbn),
            categoryId: $categoryId,
            categoryName: $categoryName,
            totalCopies: new Quantity($totalCopies),
        );

        $this->bookRepository->save($book);

        return $book;
    }
}
