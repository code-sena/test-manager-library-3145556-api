<?php

use App\Domain\Exceptions\DuplicateBookInLoan;
use App\Domain\Exceptions\ItemAlreadyReturned;
use App\Domain\Loan;
use App\Domain\LoanItem;
use App\Domain\LoanStatus;

function makeLoan(): Loan
{
    return new Loan(
        id: 'loan-1',
        memberId: 'member-1',
        loanedByUserId: 'user-1',
        loanedAt: new DateTimeImmutable('2026-10-01'),
    );
}

function makeItem(string $id, string $bookId, string $dueAt = '2026-10-15'): LoanItem
{
    return new LoanItem(
        id: $id,
        loanId: 'loan-1',
        bookId: $bookId,
        bookTitle: 'Test Book',
        isbn: '9780307474728',
        categoryName: 'Novela',
        dueAt: new DateTimeImmutable($dueAt),
    );
}

it('adds items to a loan', function () {
    $loan = makeLoan();
    $item = makeItem('item-1', 'book-1');

    $loan->addItem($item);

    expect($loan->items)->toHaveCount(1);
});

it('rejects duplicate book in same loan', function () {
    $loan = makeLoan();
    $loan->addItem(makeItem('item-1', 'book-1'));

    $loan->addItem(makeItem('item-2', 'book-1'));
})->throws(DuplicateBookInLoan::class);

it('rejects more than 3 items', function () {
    $loan = makeLoan();
    $loan->addItem(makeItem('item-1', 'book-1'));
    $loan->addItem(makeItem('item-2', 'book-2'));
    $loan->addItem(makeItem('item-3', 'book-3'));

    $loan->addItem(makeItem('item-4', 'book-4'));
})->throws(DomainException::class);

it('returns an item', function () {
    $loan = makeLoan();
    $loan->addItem(makeItem('item-1', 'book-1'));

    $returned = $loan->returnItem('item-1');

    expect($returned->returnedAt)->not->toBeNull();
});

it('throws when returning already returned item', function () {
    $loan = makeLoan();
    $loan->addItem(makeItem('item-1', 'book-1'));
    $loan->returnItem('item-1');

    $loan->returnItem('item-1');
})->throws(ItemAlreadyReturned::class);

it('calculates status as open when items are unreturned and not overdue', function () {
    $loan = makeLoan();
    $loan->addItem(makeItem('item-1', 'book-1', '2026-12-31'));

    $status = $loan->status(new DateTimeImmutable('2026-10-15'));

    expect($status)->toBe(LoanStatus::Open);
});

it('calculates status as overdue when unreturned item is past due', function () {
    $loan = makeLoan();
    $loan->addItem(makeItem('item-1', 'book-1', '2026-10-01'));

    $status = $loan->status(new DateTimeImmutable('2026-10-15'));

    expect($status)->toBe(LoanStatus::Overdue);
});

it('calculates status as closed when all items returned', function () {
    $loan = makeLoan();
    $loan->addItem(makeItem('item-1', 'book-1'));
    $loan->returnItem('item-1');

    $status = $loan->status(new DateTimeImmutable('2026-10-15'));

    expect($status)->toBe(LoanStatus::Closed);
});
