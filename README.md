# Court Management System

A full court-case management platform built on Laravel: register and track court cases, manage participants, schedule hearings and courtrooms, store case documents, record fees and payments, manage bail bonds and financial adjustments, generate reports, and share work templates.

An admin dashboard handles user account registration and deletion, with verification steps so accounts are never removed silently.

## Features

- **Case Management** — register cases, track filing status, close dates and case details.
- **Participants** — judges, magistrates, advocates, clerks, parties, and witnesses linked to cases with roles.
- **Hearings & Courtrooms** — schedule hearings into specific courtrooms with conflict detection; reschedule history is preserved.
- **Court Events** — deadlines, reminders, meetings and custom events; expired pending events are flagged automatically.
- **Case Documents** — securely uploaded documents with confidentiality levels and an access log of every view and download.
- **Fees & Payments** — record court fees, track payments and balances, keep a complete audit trail (payments are audit-locked and never deleted).
- **Bail Bonds** — record bail orders, deposits, releases and forfeitures.
- **Financial Adjustments** — audit-safe corrections to payments and bail bonds requiring approval before completion.
- **Reports** — generate printable case and financial reports, share with colleagues, download as files.
- **Work Templates** — type standard documents, save as Word files, share with colleagues.
- **User Management** — admins create accounts, assign roles, and delete accounts with verification that deletion actually removes the account and its records.

## Tech Stack

- Laravel (PHP 8.3+)
- MySQL (production) / SQLite (local quick start)
- Blade templates, no front-end framework required
- Vite for asset building

## Local Setup

```bash
# 1. Install dependencies
composer install
npm install && npm run build

# 2. Configure environment
copy .env.example .env      # Windows  (use `cp` on Linux/macOS)
php artisan key:generate

# 3. Quick start with SQLite (no server needed)
#    .env.example already defaults to sqlite; just run the migrations:
php artisan migrate

#    For MySQL instead, set these in .env:
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=court_management
#    DB_USERNAME=root
#    DB_PASSWORD=your_password

# 4. Seed roles, permissions and the admin account
php artisan db:seed

# 5. Start the server
php artisan serve
```

Visit `http://127.0.0.1:8000`.

### Default Admin Account

After seeding, log in with the credentials created by the `AdminUserSeeder` (see `database/seeders`). Change this immediately on first login.

## Deployment Notes

- Set `APP_ENV=production`, `APP_DEBUG=false` and a real `APP_URL` in production `.env`.
- Point the web server's document root to `public/` (never serve the project root directly — that exposes `.env`).
- Run `php artisan config:cache && php artisan route:cache` after each deploy.
- Set `FILESYSTEM_DISK=local` and ensure `storage/` and `bootstrap/cache/` are writable by the web user.
- Case documents are stored outside the web root (`storage/app/case-documents`) so they are never publicly accessible.

## User Account Deletion (Admin Page)

The admin user-management page deletes accounts permanently, not just in theory:

- Route model binding is explicit (`->parameter('user-management', 'user')`) so the controller always receives a real user, never an empty model.
- Deletion runs inside a DB transaction that also removes role links, shares, access logs, and every record the account created (cases, documents, hearings, fees, payments, bonds, events, reports and templates).
- Any FK constraint failure is caught and reported back to the admin — the page never shows "deleted successfully" unless the account is actually gone.
- The index page sends `Cache-Control: no-store` so a stale cached list never makes a deleted account appear to still exist.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
