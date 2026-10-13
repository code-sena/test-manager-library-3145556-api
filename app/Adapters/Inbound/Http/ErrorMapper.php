<?php

namespace App\Adapters\Inbound\Http;

use App\Domain\Exceptions\BookHasOpenLoans;
use App\Domain\Exceptions\BookNotAvailable;
use App\Domain\Exceptions\DocumentTaken;
use App\Domain\Exceptions\DuplicateBookInLoan;
use App\Domain\Exceptions\InvalidCredentials;
use App\Domain\Exceptions\InvalidDateRange;
use App\Domain\Exceptions\IsbnTaken;
use App\Domain\Exceptions\ItemAlreadyReturned;
use App\Domain\Exceptions\MemberHasOverdue;
use App\Domain\Exceptions\MemberLimitExceeded;
use App\Domain\Exceptions\UsernameTaken;
use App\Domain\Exceptions\ValidationError;

class ErrorMapper
{
    public static function toResponse(\Throwable $e): ?array
    {
        return match (true) {
            $e instanceof InvalidCredentials => ['code' => 'INVALID_CREDENTIALS', 'message' => 'Usuario o clave incorrectos.', 'details' => [], 'status' => 401],
            $e instanceof UsernameTaken => ['code' => 'USERNAME_TAKEN', 'message' => 'El nombre de usuario ya está en uso.', 'details' => [], 'status' => 409],
            $e instanceof IsbnTaken => ['code' => 'ISBN_TAKEN', 'message' => 'El ISBN ya está registrado en otro libro activo.', 'details' => [], 'status' => 409],
            $e instanceof DocumentTaken => ['code' => 'DOCUMENT_TAKEN', 'message' => 'El documento ya está registrado en otro socio.', 'details' => [], 'status' => 409],
            $e instanceof BookHasOpenLoans => ['code' => 'BOOK_HAS_OPEN_LOANS', 'message' => 'El libro tiene préstamos abiertos y no puede darse de baja.', 'details' => [], 'status' => 409],
            $e instanceof BookNotAvailable => ['code' => 'BOOK_NOT_AVAILABLE', 'message' => 'No quedan ejemplares disponibles.', 'details' => [], 'status' => 409],
            $e instanceof MemberHasOverdue => ['code' => 'MEMBER_HAS_OVERDUE', 'message' => 'El socio tiene préstamos vencidos.', 'details' => [], 'status' => 409],
            $e instanceof ItemAlreadyReturned => ['code' => 'ITEM_ALREADY_RETURNED', 'message' => 'La línea ya fue devuelta.', 'details' => [], 'status' => 409],
            $e instanceof MemberLimitExceeded => ['code' => 'MEMBER_LIMIT_EXCEEDED', 'message' => $e->getMessage(), 'details' => [['field' => 'member_id', 'issue' => $e->getMessage()]], 'status' => 422],
            $e instanceof DuplicateBookInLoan => ['code' => 'DUPLICATE_BOOK_IN_LOAN', 'message' => 'El mismo libro no puede repetirse en un préstamo.', 'details' => [], 'status' => 422],
            $e instanceof ValidationError => ['code' => 'VALIDATION_ERROR', 'message' => $e->getMessage(), 'details' => [], 'status' => 422],
            $e instanceof InvalidDateRange => ['code' => 'INVALID_DATE_RANGE', 'message' => 'La fecha final es anterior a la inicial.', 'details' => [], 'status' => 422],
            default => null,
        };
    }
}
