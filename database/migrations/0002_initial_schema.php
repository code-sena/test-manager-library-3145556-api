<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');

        DB::statement('CREATE TABLE library.category (
            id uuid PRIMARY KEY,
            name varchar(120) NOT NULL UNIQUE
        )');

        DB::statement('CREATE TABLE library.book (
            id uuid PRIMARY KEY,
            title varchar(250) NOT NULL,
            author varchar(200) NOT NULL,
            isbn char(13) NOT NULL,
            category_id uuid NOT NULL REFERENCES library.category(id) ON DELETE RESTRICT ON UPDATE NO ACTION,
            total_copies integer NOT NULL,
            available_copies integer NOT NULL,
            cover_key varchar(512) NULL,
            deleted_at timestamptz NULL,
            CONSTRAINT ck_book_available_range CHECK (available_copies >= 0 AND available_copies <= total_copies)
        )');

        DB::statement('CREATE UNIQUE INDEX book_isbn_active_unique ON library.book (isbn) WHERE deleted_at IS NULL');
        DB::statement('CREATE INDEX book_category_title_idx ON library.book (category_id, title) WHERE deleted_at IS NULL');

        DB::statement('CREATE TABLE library.member (
            id uuid PRIMARY KEY,
            full_name varchar(200) NOT NULL,
            document_number varchar(30) NOT NULL UNIQUE
        )');

        DB::statement('CREATE TABLE library."user" (
            id uuid PRIMARY KEY,
            username varchar(120) NOT NULL UNIQUE,
            password_hash varchar(512) NOT NULL,
            role varchar(40) NOT NULL
        )');

        DB::statement('CREATE TABLE library.loan (
            id uuid PRIMARY KEY,
            member_id uuid NOT NULL REFERENCES library.member(id) ON DELETE RESTRICT ON UPDATE NO ACTION,
            loaned_by_user_id uuid NOT NULL REFERENCES library."user"(id) ON DELETE RESTRICT ON UPDATE NO ACTION,
            loaned_at timestamptz NOT NULL
        )');

        DB::statement('CREATE INDEX loan_loaned_at_idx ON library.loan (loaned_at)');

        DB::statement('CREATE TABLE library.loan_item (
            id uuid PRIMARY KEY,
            loan_id uuid NOT NULL REFERENCES library.loan(id) ON DELETE CASCADE ON UPDATE NO ACTION,
            book_id uuid NOT NULL REFERENCES library.book(id) ON DELETE RESTRICT ON UPDATE NO ACTION,
            book_title varchar(250) NOT NULL,
            isbn char(13) NOT NULL,
            category_name varchar(120) NOT NULL,
            due_at timestamptz NOT NULL,
            returned_at timestamptz NULL
        )');

        DB::statement('CREATE UNIQUE INDEX loan_item_loan_book_unique ON library.loan_item (loan_id, book_id)');
        DB::statement('CREATE INDEX loan_item_book_id_idx ON library.loan_item (book_id)');
        DB::statement('CREATE INDEX loan_item_due_at_unreturned_idx ON library.loan_item (due_at) WHERE returned_at IS NULL');

        DB::table('library.category')->insert([
            ['id' => '11111111-1111-4111-8111-111111111111', 'name' => 'Novela'],
            ['id' => '22222222-2222-4222-8222-222222222222', 'name' => 'Ciencia'],
            ['id' => '33333333-3333-4333-8333-333333333333', 'name' => 'Tecnología'],
            ['id' => '44444444-4444-4444-8444-444444444444', 'name' => 'Historia'],
            ['id' => '55555555-5555-4555-8555-555555555555', 'name' => 'Infantil'],
        ]);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS library.loan_item CASCADE');
        DB::statement('DROP TABLE IF EXISTS library.loan CASCADE');
        DB::statement('DROP TABLE IF EXISTS library."user" CASCADE');
        DB::statement('DROP TABLE IF EXISTS library.member CASCADE');
        DB::statement('DROP TABLE IF EXISTS library.book CASCADE');
        DB::statement('DROP TABLE IF EXISTS library.category CASCADE');
    }
};
