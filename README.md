# Volt AI Blank

A blank **Laravolt** starter template used as a sandbox for testing AI coding agents. Agents build features such as CRUD, REST APIs and Excel import/export on top of it. After evaluation the project is reset to a clean state; commits named `blank` mark those resets.

## Tech Stack

- **Laravel 12** + **Laravolt 6.x** (Fomantic UI, Livewire 3)
- **Auth:** Laravolt built-in auth (web) and Laravel Sanctum (API)
- **Database:** SQLite
- **Testing:** Pest 4
- **Code style:** Laravel Pint
- **Import/Export:** Maatwebsite Excel 3.x

## Getting Started

> **Requires PHP 8.2+**, Composer, and Node.js.

```bash
# Install dependencies, create .env, generate key, migrate, build assets
composer setup

# Optional: seed a test user (test@example.com / password)
php artisan db:seed

# Start server, queue worker, log viewer (Pail), and Vite concurrently
composer dev
```

Open `/showcase` after logging in to browse the available Laravolt UI components (buttons, forms, cards, panels, tabs, tables, charts, statistics).

### Verify Installation

```bash
composer test
```

## Available Tooling

| Command | Description |
|---------|-------------|
| `composer setup` | Full first-time setup (install, `.env`, key, migrate, `npm install`, build) |
| `composer dev` | Runs `artisan serve`, queue listener, Pail, and Vite dev server |
| `composer test` | Clears config cache and runs the full Pest suite |
| `php artisan test --compact --filter=Name` | Runs a specific test |
| `vendor/bin/pint --dirty` | Formats changed PHP files |
| `npm run build` | Builds frontend assets |

## Project Structure

```
app/
├── Enums/Permission.php        # Permission constants (ACL)
├── Http/
│   ├── Controllers/            # Web controllers
│   ├── Controllers/Api/        # API controllers (use ApiResponse helper)
│   ├── Controllers/My/         # Profile & password
│   ├── Livewire/               # Tables, charts, statistics
│   └── Responses/ApiResponse.php
├── Exports/  Imports/  Services/  Policies/   # Empty, filled per feature
└── Models/User.php             # Extends Laravolt User, uses ULID
config/laravolt/menu/menu.php   # Sidebar menu
routes/  web.php · api.php · my.php · auth.php
docs/    crud/ · api/ · exports/ · imports/    # Feature specs (input for agents)
.ai/workflow/                   # TDD workflow guides for agents
```

## Development Workflow (TDD)

Every feature follows the same flow:

1. **User** writes the spec in `docs/{type}/{feature}.md`.
2. **Agent** reads the matching guide in `.ai/workflow/`.
3. **Agent** writes failing tests (Red), implements until they pass (Green), then refactors.

| Feature type | Workflow | Spec location |
|--------------|----------|---------------|
| CRUD (web) | `STANDARD-WORKFLOW.md` | `docs/crud/{feature}.md` |
| REST API | `API-WORKFLOW.md` | `docs/api/{resource}.md` |
| Export | `EXPORT-WORKFLOW.md` | `docs/exports/{feature}.md` |
| Import | `IMPORT-WORKFLOW.md` | `docs/imports/{resource}.md` |

## Conventions

- Use Laravolt Form Builder (`form()->...`) instead of raw HTML forms.
- Use Laravolt Suitable/TableView instead of raw HTML tables.
- Use `<x-volt-*>` Blade components for UI.
- Define permissions in `app/Enums/Permission.php` and authorize by permission, never by role.
- Every change ships with a test.

See `CLAUDE.md` for the full agent guidelines.
