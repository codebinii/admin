# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Laravel 12 (PHP ^8.2) — 100% REST API. Multi-purpose modular admin API (Codebini). No frontend/Blade/Vite.

## Commands

```bash
composer run setup   # install deps, .env, key:generate, migrate
composer run dev     # php artisan serve + queue:listen (concurrent)
composer run test    # clear config + php artisan test

php artisan test --filter=TestName   # run single test
./vendor/bin/pint                    # fix code style (Laravel Pint)
```

## Architecture

- All routes in `routes/api.php` — automatically prefixed with `/api` and use the `api` middleware group.
- `routes/console.php` for Artisan commands.
- Tests use in-memory SQLite — no DB setup needed.

## Development Rules

See [`docs/DEVELOPMENT_RULES.md`](docs/DEVELOPMENT_RULES.md) for mandatory project conventions:

- SOLID principles, modular directory structure, naming conventions
- No fake data, no auto-testing, no unsolicited features
- Controllers → Services → Repositories separation
- Boolean fields normalized with `NormalizesBooleans` trait
- Commit workflow and session closure procedure
