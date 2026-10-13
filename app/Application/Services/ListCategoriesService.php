<?php

namespace App\Application\Services;

use Illuminate\Support\Facades\DB;

class ListCategoriesService
{
    public function list(): array
    {
        return DB::table('library.category')
            ->orderBy('name')
            ->get()
            ->map(fn($row) => [
                'id' => $row->id,
                'name' => $row->name,
            ])
            ->all();
    }
}
