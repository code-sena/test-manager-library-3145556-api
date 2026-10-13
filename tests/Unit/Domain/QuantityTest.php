<?php

use App\Domain\Exceptions\InvalidQuantity;
use App\Domain\ValueObjects\Quantity;

it('accepts a positive quantity', function () {
    $quantity = new Quantity(5);

    expect($quantity->value)->toBe(5);
});

it('rejects zero', function () {
    new Quantity(0);
})->throws(InvalidQuantity::class);

it('rejects a negative quantity', function () {
    new Quantity(-1);
})->throws(InvalidQuantity::class);
