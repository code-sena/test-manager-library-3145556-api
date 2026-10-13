<?php

use App\Adapters\Inbound\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class)->name('health');

Route::prefix('v1')->group(function () {
    Route::get('/health', HealthController::class)->name('v1.health');

    Route::post('/auth/login', \App\Adapters\Inbound\Http\Controllers\LoginController::class);
    Route::get('/categories', \App\Adapters\Inbound\Http\Controllers\ListCategoriesController::class)->middleware('auth.jwt');

    Route::get('/books', \App\Adapters\Inbound\Http\Controllers\SearchBooksController::class)->middleware('auth.jwt');
    Route::post('/books', \App\Adapters\Inbound\Http\Controllers\CreateBookController::class)->middleware('auth.jwt', 'role:admin');
    Route::get('/books/{id}', \App\Adapters\Inbound\Http\Controllers\ShowBookController::class)->middleware('auth.jwt')->where('id', '[0-9a-f-]{36}');
    Route::put('/books/{id}', \App\Adapters\Inbound\Http\Controllers\UpdateBookController::class)->middleware('auth.jwt', 'role:admin')->where('id', '[0-9a-f-]{36}');
    Route::delete('/books/{id}', \App\Adapters\Inbound\Http\Controllers\DeleteBookController::class)->middleware('auth.jwt', 'role:admin')->where('id', '[0-9a-f-]{36}');
    Route::get('/books/{id}/cover', \App\Adapters\Inbound\Http\Controllers\ShowCoverController::class)->middleware('auth.jwt')->where('id', '[0-9a-f-]{36}');
    Route::put('/books/{id}/cover', \App\Adapters\Inbound\Http\Controllers\UploadCoverController::class)->middleware('auth.jwt', 'role:admin')->where('id', '[0-9a-f-]{36}');
    Route::delete('/books/{id}/cover', \App\Adapters\Inbound\Http\Controllers\DeleteCoverController::class)->middleware('auth.jwt', 'role:admin')->where('id', '[0-9a-f-]{36}');

    Route::get('/members', \App\Adapters\Inbound\Http\Controllers\ListMembersController::class)->middleware('auth.jwt', 'role:admin,librarian');
    Route::post('/members', \App\Adapters\Inbound\Http\Controllers\CreateMemberController::class)->middleware('auth.jwt', 'role:admin,librarian');
    Route::get('/members/{id}', \App\Adapters\Inbound\Http\Controllers\ShowMemberController::class)->middleware('auth.jwt', 'role:admin,librarian')->where('id', '[0-9a-f-]{36}');

    Route::post('/users', \App\Adapters\Inbound\Http\Controllers\CreateUserController::class)->middleware('auth.jwt', 'role:admin');

    Route::get('/loans/overdue', \App\Adapters\Inbound\Http\Controllers\OverdueLoansController::class)->middleware('auth.jwt', 'role:admin,librarian');
    Route::get('/loans/{id}', \App\Adapters\Inbound\Http\Controllers\ShowLoanController::class)->middleware('auth.jwt', 'role:admin,librarian')->where('id', '[0-9a-f-]{36}');
    Route::get('/loans', \App\Adapters\Inbound\Http\Controllers\ListLoansController::class)->middleware('auth.jwt', 'role:admin,librarian');
    Route::post('/loans', \App\Adapters\Inbound\Http\Controllers\LendBooksController::class)->middleware('auth.jwt', 'role:admin,librarian');
    Route::post('/loans/{loanId}/items/{itemId}/return', \App\Adapters\Inbound\Http\Controllers\ReturnItemController::class)->middleware('auth.jwt', 'role:admin,librarian')->where(['loanId' => '[0-9a-f-]{36}', 'itemId' => '[0-9a-f-]{36}']);

    Route::get('/reports/most-borrowed', \App\Adapters\Inbound\Http\Controllers\MostBorrowedReportController::class)->middleware('auth.jwt', 'role:admin');
});
