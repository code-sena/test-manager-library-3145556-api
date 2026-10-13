<?php

namespace App\Adapters\Outbound\Persistence;

use App\Application\Ports\BookRepository;
use App\Domain\Book as DomainBook;
use Illuminate\Support\Facades\DB;

class BookRepositoryImpl implements BookRepository
{
    public function findById(string $id): ?DomainBook
    {
        $row = DB::table('library.book')->where('id', $id)->whereNull('deleted_at')->first();

        if (! $row) {
            return null;
        }

        return $this->toDomain($row);
    }

    public function findByIsbn(string $isbn): ?DomainBook
    {
        $row = DB::table('library.book')->where('isbn', $isbn)->whereNull('deleted_at')->first();

        if (! $row) {
            return null;
        }

        return $this->toDomain($row);
    }

    public function search(?string $q, ?string $categoryId, int $page, int $size): array
    {
        $query = DB::table('library.book')->whereNull('deleted_at');

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'ILIKE', "%{$q}%")
                  ->orWhere('author', 'ILIKE', "%{$q}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $query->orderBy('title');

        $rows = $query->forPage($page, $size)->get();

        return array_map(fn($r) => $this->toDomain($r), $rows->all());
    }

    public function countSearch(?string $q, ?string $categoryId): int
    {
        $query = DB::table('library.book')->whereNull('deleted_at');

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'ILIKE', "%{$q}%")
                  ->orWhere('author', 'ILIKE', "%{$q}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->count();
    }

    public function save(DomainBook $book): void
    {
        DB::table('library.book')->updateOrInsert(
            ['id' => $book->id],
            [
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn->value,
                'category_id' => $book->categoryId,
                'total_copies' => $book->totalCopies->value,
                'available_copies' => $book->availableCopies,
                'cover_key' => $book->coverKey,
                'deleted_at' => $book->deletedAt,
            ]
        );
    }

    public function delete(string $id): void
    {
        DB::table('library.book')->where('id', $id)->update(['deleted_at' => now()]);
    }

    public function hasOpenLoans(string $bookId): bool
    {
        return DB::table('library.loan_item')
            ->where('book_id', $bookId)
            ->whereNull('returned_at')
            ->exists();
    }

    private function toDomain(object $row): DomainBook
    {
        return new DomainBook(
            id: $row->id,
            title: $row->title,
            author: $row->author,
            isbn: new \App\Domain\ValueObjects\Isbn($row->isbn),
            categoryId: $row->category_id,
            categoryName: $row->category_name ?? '',
            totalCopies: new \App\Domain\ValueObjects\Quantity($row->total_copies),
            availableCopies: $row->available_copies,
            coverKey: $row->cover_key,
            deletedAt: $row->deleted_at ? new \DateTimeImmutable($row->deleted_at) : null,
        );
    }
}
