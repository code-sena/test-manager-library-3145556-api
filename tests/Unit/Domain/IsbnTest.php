<?php

use App\Domain\Exceptions\InvalidIsbn;
use App\Domain\ValueObjects\Isbn;

it('accepts a valid 13-digit ISBN', function () {
    $isbn = new Isbn('9780307474728');

    expect($isbn->value)->toBe('9780307474728');
});

it('rejects an ISBN with fewer than 13 digits', function () {
    new Isbn('978030747472');
})->throws(InvalidIsbn::class);

it('rejects an ISBN with more than 13 digits', function () {
    new Isbn('97803074747280');
})->throws(InvalidIsbn::class);

it('rejects an ISBN with non-digit characters', function () {
    new Isbn('978030747472A');
})->throws(InvalidIsbn::class);
