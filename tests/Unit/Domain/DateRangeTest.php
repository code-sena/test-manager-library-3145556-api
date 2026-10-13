<?php

use App\Domain\Exceptions\InvalidDateRange;
use App\Domain\ValueObjects\DateRange;

it('accepts a valid date range', function () {
    $start = new DateTimeImmutable('2026-10-01');
    $end = new DateTimeImmutable('2026-10-31');

    $range = new DateRange($start, $end);

    expect($range->start)->toBe($start);
    expect($range->end)->toBe($end);
});

it('accepts same start and end', function () {
    $date = new DateTimeImmutable('2026-10-01');

    $range = new DateRange($date, $date);

    expect($range->start)->toBe($range->end);
});

it('rejects end before start', function () {
    $start = new DateTimeImmutable('2026-10-31');
    $end = new DateTimeImmutable('2026-10-01');

    new DateRange($start, $end);
})->throws(InvalidDateRange::class);
