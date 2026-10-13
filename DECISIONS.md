# Decisiones técnicas — traducción a React + Laravel

Este documento traduce `plan.md` §1 (escrito para Python) al stack real del reto: **React + Laravel**,
según piden los seis README. El negocio, las reglas, el modelo de datos y el contrato de la API
**no cambian** — están fijos en `test-manager-library-3145556-docs/spec-python/` (o `spec-.net/`,
mismo contenido de negocio). Lo que cambia aquí es solo la columna "Elección".

| # | Decisión | Elección (Python, spec) | Elección (este repo) | Por qué |
|---|---|---|---|---|
| D-01 | Lenguaje y runtime | Python 3.12 | PHP 8.3 + Laravel 11 | Stack del reto (README) |
| D-02 | Framework HTTP | FastAPI | Laravel (rutas `routes/api.php`, prefijo `/api/v1`) | Contrato en `api-contract.md`, idéntico para ambos |
| D-03 | Persistencia | SQLAlchemy 2 + Alembic | Eloquent + migraciones de Laravel, sobre PostgreSQL 16 | El esquema sigue siendo propiedad de las migraciones (ADR-001); `infra` levanta el motor vacío |
| D-04 | Concurrencia | Testigo de versión + `CHECK` de rango | `CHECK` de rango en la migración (`ck_book_available_range`) + transacción con bloqueo optimista a nivel de fila (`lockForUpdate` o verificación de versión) | Mismo requisito, RNF-2: el motor es la última barrera |
| D-05 | Fechas | `timestamptz`; reloj como puerto (`Clock`) | `timestamptz`; un `Clock` propio inyectable (no `now()` esparcido) para poder probar vencimientos sin esperar 14 días | Mismo motivo |
| D-06 | Reporte | Consulta agregada por un puerto de lectura | Query Builder/Eloquent con una consulta agregada en un "read model" (clase de solo lectura) | Estable y barato, igual que el original |
| D-07 | Objetos de valor | `Isbn`, `Quantity`, `DocumentNumber`, `DateRange` | Mismos conceptos como clases PHP inmutables en `app/Domain/ValueObjects/`, sin Eloquent | No tienen identidad; se validan en el dominio, no en el framework |
| D-08 | Portadas | Disco local / S3 detrás de un puerto | `Storage` de Laravel (driver `local`, cambiable a `s3` por configuración) | Clave opaca (`cover_key`), nunca ruta ni bytes |
| D-09 | Contraseñas | Argon2id detrás de `PasswordHasher` | `Hash::make()` de Laravel (Bcrypt/Argon2id vía `config/hashing.php`) | El dominio nunca ve la clave en claro (Artículo IX) |
| D-10 | Arranque | La app crea el admin inicial desde `ADMIN_USERNAME`/`ADMIN_PASSWORD` | Igual: un `Seeder`/comando `artisan` idempotente que lee esas variables en el arranque, no en una migración | La base no puede producir el hash |
| D-11 | Autenticación | JWT HS256, 8h, `sub` + `role` | JWT propio (HS256, 8h) emitido a mano con `firebase/php-jwt`, middleware de verificación + autorización por rol | Mismo contrato (`Authorization: Bearer <token>`); se evita el peso de un paquete de sesiones que no necesitamos (API sin estado) |
| D-12 | Pruebas | pytest con fakes + base real en integración | Pest/PHPUnit: pruebas unitarias de dominio (sin framework), pruebas de *feature* contra la API real, migraciones aplicadas sobre Postgres real | Misma pirámide de pruebas |

## Qué no se traduce (es negocio, no tecnología)

Historias de usuario (HU-01 a HU-10), reglas de negocio (RN-1 a RN-6), requisitos no funcionales
(RNF-1 a RNF-5), el modelo de datos completo (`data-model.md`) y el contrato HTTP (`api-contract.md`)
se implementan **tal cual** están escritos. Ninguna decisión de esta tabla los modifica.

## Estructura de carpetas (equivalente Laravel de `architecture.md` §2)

```text
app/
├── Domain/              # Book, Loan, LoanItem, Member, User; Isbn, Quantity, DateRange (PHP puro, sin Eloquent)
├── Services/            # Casos de uso: LendBooksService, ReturnItemService, LoginService, ...
├── Models/              # Eloquent: Category, Book, Member, Loan, LoanItem, User (adaptador de persistencia)
├── Http/
│   ├── Controllers/Api/ # Controladores finos: validan, llaman a Services/, formatean respuesta
│   ├── Requests/        # Form Requests = validación de entrada
│   └── Middleware/      # JwtAuthenticate, EnsureRole
└── Support/             # Clock, TokenIssuer, CoverStorage (los "puertos" del spec, como interfaces PHP)
```

Los controladores no contienen reglas de negocio; las reglas viven en `Domain/` y `Services/`. Esto
no es la arquitectura hexagonal con `import-linter` del spec Python (no es idiomático en Laravel y no
alcanza el tiempo de la prueba para montar esa verificación automática), pero conserva el principio:
el negocio no depende de los detalles de framework, y hay un único lugar (`Services/`) donde se
orquesta cada caso de uso.
