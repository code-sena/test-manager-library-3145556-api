<?php

namespace App\Application\Services;

use App\Application\Ports\BookRepository;
use App\Application\Ports\CoverStorage;
use Illuminate\Support\Str;

class UploadCoverService
{
    private const MAX_SIZE = 2 * 1024 * 1024;
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png'];

    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly CoverStorage $coverStorage,
    ) {
    }

    public function upload(string $bookId, string $contents, string $contentType): string
    {
        if (! in_array($contentType, self::ALLOWED_TYPES, true)) {
            throw new \App\Domain\Exceptions\ValidationError('Only JPEG and PNG images are allowed.');
        }

        if (strlen($contents) > self::MAX_SIZE) {
            throw new \App\Domain\Exceptions\ValidationError('File exceeds 2 MB limit.');
        }

        $book = $this->bookRepository->findById($bookId);
        if ($book === null) {
            throw new \App\Domain\Exceptions\ValidationError("Book not found: {$bookId}");
        }

        $key = Str::uuid()->toString();
        $this->coverStorage->store($key, $contents, $contentType);

        $book->coverKey = $key;
        $this->bookRepository->save($book);

        return $key;
    }

    public function delete(string $bookId): void
    {
        $book = $this->bookRepository->findById($bookId);
        if ($book === null) {
            throw new \App\Domain\Exceptions\ValidationError("Book not found: {$bookId}");
        }

        if ($book->coverKey !== null) {
            $this->coverStorage->delete($book->coverKey);
            $book->coverKey = null;
            $this->bookRepository->save($book);
        }
    }
}
