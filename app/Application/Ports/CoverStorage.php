<?php

namespace App\Application\Ports;

interface CoverStorage
{
    public function store(string $key, string $contents, string $contentType): void;

    public function read(string $key): ?string;

    public function delete(string $key): void;
}
