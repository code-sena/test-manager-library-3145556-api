# test-manager-library-3145556-api

Manager Library API — Laravel 11 with hexagonal architecture on PostgreSQL 16.

## Run

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan library:bootstrap-admin
php artisan serve
```

Or with Docker (from `infra/`):

```bash
docker compose up -d
```

## Test

```bash
composer install
php artisan test
```

## Endpoints

All endpoints are under `/api/v1`. See `routes/api.php` and the API contract in `docs/spec-laravel/`.

| Method | Route | Role |
|---|---|---|
| GET | `/health` | public |
| GET | `/api/v1/health` | public |
| POST | `/api/v1/auth/login` | public |
| GET | `/api/v1/categories` | authenticated |
| GET | `/api/v1/books` | authenticated |
| POST | `/api/v1/books` | admin |
| GET | `/api/v1/books/{id}` | authenticated |
| PUT | `/api/v1/books/{id}` | admin |
| DELETE | `/api/v1/books/{id}` | admin |
| GET | `/api/v1/books/{id}/cover` | authenticated |
| PUT | `/api/v1/books/{id}/cover` | admin |
| DELETE | `/api/v1/books/{id}/cover` | admin |
| GET | `/api/v1/members` | admin, librarian |
| POST | `/api/v1/members` | admin, librarian |
| GET | `/api/v1/members/{id}` | admin, librarian |
| POST | `/api/v1/users` | admin |
| GET | `/api/v1/loans/overdue` | admin, librarian |
| GET | `/api/v1/loans/{id}` | admin, librarian |
| GET | `/api/v1/loans` | admin, librarian |
| POST | `/api/v1/loans` | admin, librarian |
| POST | `/api/v1/loans/{loanId}/items/{itemId}/return` | admin, librarian |
| GET | `/api/v1/reports/most-borrowed` | admin |

## Architecture

Hexagonal (ports and adapters):

- `app/Domain/` — pure PHP, no framework dependencies
- `app/Application/` — use cases + ports (interfaces)
- `app/Adapters/` — HTTP controllers, middleware, Eloquent repositories, JWT, Argon2
- `app/Bootstrap/` — sole composition point

## Environment variables

See `.env.example`.
