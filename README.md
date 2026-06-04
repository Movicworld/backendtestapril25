# Expense Management API

Multi-Tenant SaaS Expense Management System — Laravel 11, Sanctum, RBAC, Queues, Audit Logging.

## Tech Stack

- Laravel 11
- MySQL / PostgreSQL
- Laravel Sanctum
- Redis (caching + queues)
- Laravel Queues & Scheduler

## Architecture

```
Controller → Service → Repository → Model
```

- **Controllers** handle validation (via FormRequests) and responses (via `ApiResponse` trait)
- **Services** contain business logic
- **Repositories** abstract database queries; interfaces allow easy swapping/mocking
- **Enums** for `Role` and `ApiStatus` — enforced via casts and middleware

## Setup

```bash
git clone <repo>
cd expense-management-api

composer install
cp .env.example .env
php artisan key:generate

# Configure DB and Redis in .env

php artisan migrate --seed
php artisan serve --port=8006
php artisan queue:work --queue=default
```

## .env (key settings)

```env
DB_CONNECTION=mysql
QUEUE_CONNECTION=redis
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
```

## RBAC Summary

| Action                  | Employee | Manager | Admin |
|-------------------------|----------|---------|-------|
| View expenses           | ✅       | ✅      | ✅    |
| Create expense          | ✅       | ✅      | ✅    |
| Update expense          | ❌       | ✅      | ✅    |
| Delete expense          | ❌       | ❌      | ✅    |
| Manage users            | ❌       | ❌      | ✅    |

## Multi-Tenancy

All queries are scoped to `company_id` via:
- `HasCompanyScope` trait → `scopeForCompany()`
- Repository methods always receive and apply `company_id`
- Cross-company access returns 404 (not leaking existence)

## Audit Logging

Every `update` and `delete` on expenses creates an `audit_logs` record with `before`/`after` values in the `changes` JSON column.

## Weekly Report Job

Runs every Monday at 08:00 via scheduler. Sends an expense summary email to all Admins per company.

```bash
# Run scheduler locally
php artisan schedule:work
```

## Running Tests

```bash
php artisan test
# or
php artisan test --filter ExpenseTest
```

## Postman

Import `expense-api.postman_collection.json` into Postman.  
The Login/Register requests auto-save the token to `{{token}}` via test scripts.

## Assumptions

- Registration creates a new Company + Admin in one request (company onboarding flow)
- `company_id` isolation is enforced at the repository layer, not via global scopes (intentional — avoids hidden magic in complex queries)
- Redis is recommended but the app falls back to `database` driver for queues and `file` for cache if Redis is unavailable
