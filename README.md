# ERP-POS (Cloud Accounting + Inventory + POS)

Laravel 10 / PHP 8.1 project for multi-branch accounting, inventory, POS, and ZATCA-compliant invoicing. This repo is the working copy to align the production system with the client’s Arabic requirements document.

## Tech Stack
- PHP 8.1+, Laravel 10, MySQL
- Frontend: Laravel UI + Vite/NPM assets
- Auth/permissions: spatie/laravel-permission
- Localization: mcamara/laravel-localization
- ZATCA: salla/zatca

## Quick Start (local)
1) Copy env: `cp .env.example .env`
2) Configure DB in `.env` (point to provided production dump, e.g. `pos-ms.sql`).
3) Install PHP deps: `composer install`
4) Install JS deps: `npm install`
5) App key: `php artisan key:generate`
6) Import DB (keeps client data): `mysql -u <user> -p <db_name> < pos-ms.sql`
7) Run migrations (only if you need schema updates after import): `php artisan migrate`
8) Seed default admin (optional if DB already has users): `php artisan db:seed`
   - Creates `admin@example.com` / `password` with role `مدير النظام`.
9) Dev server: `php artisan serve` and `npm run dev` (or `npm run build` for production assets).

## Requirements Checklist
The full Arabic requirements from "طلبات-تطوير-برنامج-المحاسبة-والمخزون-كلاود.pdf" are captured in `docs/requirements-checklist.md`. Use it to mark items as ✅/⚠️/❌ after verifying against the running system.

## Working Notes
- Preserve existing production data; add schema changes via migrations only.
- Keep RTL layouts for Arabic and add English UI strings where required.
- Offline/PWA behavior, subscribers dashboard, reporting filters، POS enhancements، والتسعير متعدد الوحدات هي مناطق حرجة للمراجعة.
- When comparing with production, avoid deleting أو تعطيل المسارات العاملة إلا إذا طلبها المستند.
