# IBU Chess - Milestone 2 (Backend API)

This branch focuses exclusively on the Milestone 2 deliverables: a relational schema, data-access layer, and an aggressively modular PHP API that will power the SPA later on.

## Tech Stack
- PHP 8.2+ with the PDO MySQL extension enabled
- MySQL 8 (or compatible) for persistence
- Optional: Composer if you want to swap in a formal PSR-4 autoloader later

## Quick Start
1. Create `backend/.env` (or export the variables in your shell) using the keys outlined below.
2. Import the schema:
   ```sql
   SOURCE backend/sql/schema.sql;
   ```
3. Serve the API from the repository root:
   ```bash
   php -S localhost:8000 -t backend/public
   ```
4. Hit `http://localhost:8000/api` to verify the router advertises the available resources.

## Environment Variables
The backend reads connection info from the environment (or `backend/.env`). A minimal file would look like:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=ibu_chess
DB_USER=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
```

| Key | Default | Notes |
| --- | --- | --- |
| `DB_HOST` | `127.0.0.1` | Database host/IP |
| `DB_PORT` | `3306` | MySQL port |
| `DB_NAME` | `ibu_chess` | Schema name created by the SQL script |
| `DB_USER` | `root` | Account with CREATE/ALTER rights |
| `DB_PASSWORD` | `` | Plain password (use vault tooling in production) |
| `DB_CHARSET` | `utf8mb4` | Forces full Unicode support |

## Database Blueprint
`backend/sql/schema.sql` provisions six core tables:
- `users`
- `tournaments`
- `games`
- `moves`
- `tournament_participants`
- `reviews`

Foreign keys cascade updates/deletes where it makes sense (e.g. wiping a tournament clears its participants and games).

## API Surface
Base URL: `http://localhost:8000/api`

Every resource supports the full CRUD matrix (GET collection, GET item, POST, PUT/PATCH, DELETE) with JSON payloads:
- `/users`
- `/tournaments`
- `/games`
- `/moves`
- `/tournament-participants`
- `/reviews`

Extras:
- Query params `limit`, `offset`, `order_by`, `order_dir` apply to all listings.
- Column-specific filters work by simply appending `?column=value`.
- Responses ship with permissive CORS headers so the SPA can consume the API locally without proxying.

## Code Architecture
- `backend/bootstrap.php` wires a handcrafted PSR-4 autoloader and lightweight `.env` loader.
- Namespaced classes live under `backend/App/**`, split into `Config`, `Dao`, `Services`, and `Routes`.
- `App\Config\Database::connection()` exposes a memoized PDO handle that the repositories share.
- `App\Dao\BaseDao` provides a query-builder-esque helper with guarded payload filtering, optimistic fetch-after-write, and pagination helpers.
- `App\Services\*Service` thinly wrap their DAO counterparts to keep controller logic razor-thin.
- `App\Routes\ApiRouter` orchestrates routing, request parsing, error mapping, and response emission.

The static SPA from Milestone 1 is still parked under `frontend/` for reference, but it is not required when grading this milestone. Open `frontend/index.html` if you want to browse the mock flows.
