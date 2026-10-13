<?php

use App\Domain\Member;
use App\Domain\User;
use App\Domain\ValueObjects\DocumentNumber;

it('creates a member with document number', function () {
    $member = new Member(
        id: 'member-1',
        fullName: 'John Doe',
        documentNumber: new DocumentNumber('DOC123'),
    );

    expect($member->fullName)->toBe('John Doe');
    expect($member->documentNumber->value)->toBe('DOC123');
});

it('creates a user with valid role', function () {
    $user = new User(
        id: 'user-1',
        username: 'admin',
        passwordHash: 'hash',
        role: 'admin',
    );

    expect($user->role)->toBe('admin');
});

it('rejects invalid role', function () {
    new User(
        id: 'user-1',
        username: 'test',
        passwordHash: 'hash',
        role: 'superadmin',
    );
})->throws(DomainException::class);
