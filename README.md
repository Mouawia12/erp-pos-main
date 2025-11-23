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

## Recent Batch Updates (Sales/POS)
- Sales and POS invoice lines now display available quantity per warehouse, current product cost, and last selling price to reduce stock/pricing mistakes.
- Adding the same item again now shows a warning but keeps a separate line so different units/prices can be entered later (quantities no longer auto-merge).
- Sales invoice date/time is locked to the current timestamp on save to prevent backdating edits (UI input is read-only; server enforces now()).
- Purchase invoices now also warn on duplicate items and show available stock while editing.
- POS thermal print redesigned to include branch contact, customer details, notes/terms, and bilingual “Simplified Tax Invoice” heading.
- Multi-unit selection added to sales/POS/purchases lines (per-product units with conversion factors, defaulting to 1). Chosen unit_factor is now stored on line items and applied to stock movement and profit for sales, purchases, and returns; lines stay separate for mixed units.
- Payments: sales/POS now accept mixed payments (cash + multiple card terminals) per invoice; entries are validated against invoice total.
- Purchase payments: modal supports cash + multiple card entries, validation against invoice total, and posting each card as a separate payment entry.
- Access policy: optional single-device login toggle added to system settings; when enabled, concurrent sessions for the same user are blocked.
- New DB backup command and admin route: `php artisan db:backup` creates `storage/backups/db-backup-*.sql` (also accessible via `/admin/backup/database` for admin users).
- Default invoice type can now be set at system level and overridden per branch or per user; forms and prints honor the resolved default (tax, simplified, non-tax).
- Invoice terms templates: create/edit/delete reusable terms from `admin/invoice-terms` and inject them into system settings for printing on invoices.
- A5 print option for sales/returns via `print-sales/{id}?format=a5` alongside existing A4 and POS thermal layouts.
- Cost centers / representatives: sales, returns, POS, and purchases now allow selecting a representative per invoice and optionally using it as the cost center value.
- Sales invoices are locked after posting: only payment fields can change; deletion/editing of other fields is blocked.
- Tax mode (inclusive/exclusive) is persisted on sales and purchases; totals are recomputed server-side to respect the selected mode and printed on A4/POS outputs.
- Supplier invoice reference + attachment stored with purchase invoices.
- Branch details (CR/tax/manager/email) added to branch screens and to invoice prints.
- Expenses: added tax field; tax is posted to configured tax account during auto-accounting.
