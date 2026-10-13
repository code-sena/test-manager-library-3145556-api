<?php

namespace App\Adapters\Outbound\Storage;

use App\Application\Ports\CoverStorage;
use Illuminate\Support\Facades\Storage;

class DiskCoverStorage implements CoverStorage
{
    public function store(string $key, string $contents, string $contentType): void
    {
        Storage::disk('covers')->put($key, $contents);
    }

    public function read(string $key): ?string
    {
        if (! Storage::disk('covers')->exists($key)) {
            return null;
        }

        return Storage::disk('covers')->get($key);
    }

    public function delete(string $key): void
    {
        Storage::disk('covers')->delete($key);
    }
}
