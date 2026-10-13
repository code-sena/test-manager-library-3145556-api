<?php

use App\Domain\Book;
use App\Domain\Exceptions\BookNotAvailable;
use App\Domain\ValueObjects\Isbn;
use App\Domain\ValueObjects\Quantity;

function makeBook(int $total = 3, ?int $available = null): Book
{
    return new Book(
        id: 'book-1',
        title: 'Test Book',
        author: 'Test Author',
        isbn: new Isbn('9780307474728'),
        categoryId: 'cat-1',
        categoryName: 'Novela',
        totalCopies: new Quantity($total),
        availableCopies: $available,
    );
}

it('sets available copies equal to total when not specified', function () {
    $book = makeBook(total: 5);

    expect($book->availableCopies)->toBe(5);
});

it('withdraws a copy', function () {
    $book = makeBook(total: 3, available: 3);

    $book->withdraw();

    expect($book->availableCopies)->toBe(2);
});

it('throws when withdrawing from zero available', function () {
    $book = makeBook(total: 1, available: 0);

    $book->withdraw();
})->throws(BookNotAvailable::class);

it('restocks a copy', function () {
    $book = makeBook(total: 3, available: 1);

    $book->restock();

    expect($book->availableCopies)->toBe(2);
});

it('throws when restocking beyond total', function () {
    $book = makeBook(total: 3, available: 3);

    $book->restock();
})->throws(DomainException::class);

it('updates total and adjusts available', function () {
    $book = makeBook(total: 5, available: 3);

    $book->updateTotal(7);

    expect($book->totalCopies->value)->toBe(7);
    expect($book->availableCopies)->toBe(5);
});

it('throws when update would make available negative', function () {
    $book = makeBook(total: 5, available: 2);

    $book->updateTotal(1);
})->throws(DomainException::class);

it('marks as deleted', function () {
    $book = makeBook();

    $book->delete();

    expect($book->isDeleted())->toBeTrue();
    expect($book->deletedAt)->not->toBeNull();
});
