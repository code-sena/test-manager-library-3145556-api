<?php

use Illuminate\Support\Facades\Route;

// api-contract.md §3.1 — público, sin token.
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
