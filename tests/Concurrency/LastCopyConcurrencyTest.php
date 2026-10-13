<?php

use App\Application\Ports\BookRepository;
use App\Application\Ports\LoanRepository;
use App\Domain\Book;
use App\Domain\Loan;
use App\Domain\LoanItem;
use App\Domain\ValueObjects\Isbn;
use App\Domain\ValueObjects\Quantity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

beforeEach(function () {
    DB::beginTransaction();
});

afterEach(function () {
    DB::rollBack();
});

it('allows only one of two concurrent loans of the last copy', function () {
    $bookId = Str::uuid()->toString();
    $memberId = Str::uuid()->toString();
    $userId = Str::uuid()->toString();

    DB::table('library.book')->insert([
        'id' => $bookId,
        'title' => 'Concurrent Book',
        'author' => 'Test Author',
        'isbn' => '9780000000001',
        'category_id' => '11111111-1111-4111-8111-111111111111',
        'total_copies' => 1,
        'available_copies' => 1,
    ]);

    DB::table('library.member')->insert([
        'id' => $memberId,
        'full_name' => 'Test Member',
        'document_number' => 'CONC001',
    ]);

    DB::table('library."user"')->insert([
        'id' => $userId,
        'username' => 'concurrent_user',
        'password_hash' => 'hash',
        'role' => 'librarian',
    ]);

    $loanId1 = Str::uuid()->toString();
    $loanId2 = Str::uuid()->toString();
    $itemId1 = Str::uuid()->toString();
    $itemId2 = Str::uuid()->toString();

    $now = now();

    DB::table('library.loan')->insert([
        ['id' => $loanId1, 'member_id' => $memberId, 'loaned_by_user_id' => $userId, 'loaned_at' => $now],
        ['id' => $loanId2, 'member_id' => $memberId, 'loaned_by_user_id' => $userId, 'loaned_at' => $now],
    ]);

    DB::table('library.loan_item')->insert([
        ['id' => $itemId1, 'loan_id' => $loanId1, 'book_id' => $bookId, 'book_title' => 'Concurrent Book', 'isbn' => '9780000000001', 'category_name' => 'Novela', 'due_at' => $now->modify('+14 days')],
        ['id' => $itemId2, 'loan_id' => $loanId2, 'book_id' => $bookId, 'book_title' => 'Concurrent Book', 'isbn' => '9780000000001', 'category_name' => 'Novela', 'due_at' => $now->modify('+14 days')],
    ]);

    $results = [];

    $promise1 = function () use ($bookId, &$results) {
        try {
            $affected = DB::table('library.book')
                ->where('id', $bookId)
                ->where('available_copies', 1)
                ->update(['available_copies' => DB::raw('available_copies - 1')]);

            $results[] = $affected > 0 ? 'success' : 'conflict';
        } catch (\Exception $e) {
            $results[] = 'error';
        }
    };

    $promise2 = function () use ($bookId, &$results) {
        try {
            $affected = DB::table('library.book')
                ->where('id', $bookId)
                ->where('available_copies', 1)
                ->update(['available_copies' => DB::raw('available_copies - 1')]);

            $results[] = $affected > 0 ? 'success' : 'conflict';
        } catch (\Exception $e) {
            $results[] = 'error';
        }
    };

    $promise1();
    $promise2();

    $successCount = count(array_filter($results, fn($r) => $r === 'success'));
    $conflictCount = count(array_filter($results, fn($r) => $r === 'conflict'));

    expect($successCount)->toBe(1);
    expect($conflictCount)->toBe(1);

    $book = DB::table('library.book')->where('id', $bookId)->first();
    expect($book->available_copies)->toBe(0);
});

it('prevents available_copies from going below zero', function () {
    $bookId = Str::uuid()->toString();

    DB::table('library.book')->insert([
        'id' => $bookId,
        'title' => 'Zero Book',
        'author' => 'Test Author',
        'isbn' => '9780000000002',
        'category_id' => '11111111-1111-4111-8111-111111111111',
        'total_copies' => 1,
        'available_copies' => 0,
    ]);

    $affected = DB::table('library.book')
        ->where('id', $bookId)
        ->where('available_copies', '>', 0)
        ->update(['available_copies' => DB::raw('available_copies - 1')]);

    expect($affected)->toBe(0);

    $book = DB::table('library.book')->where('id', $bookId)->first();
    expect($book->available_copies)->toBe(0);
});
