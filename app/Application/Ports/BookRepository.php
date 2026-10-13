<?php

namespace App\Application\Ports;

use App\Domain\Book;

interface BookRepository
{
    public function findById(string $id): ?Book;

    public function findByIsbn(string $isbn): ?Book;

    public function search(?string $q, ?string $categoryId, int $page, int $size): array;

    public function countSearch(?string $q, ?string $categoryId): int;

    public function save(Book $book): void;

    public function delete(string $id): void;

    public function hasOpenLoans(string $bookId): bool;
}
