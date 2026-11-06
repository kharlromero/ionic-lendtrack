# LendTrack

LendTrack is a Laravel-based loan management system for tracking borrowers, loans, and payments. It supports user authentication, borrower onboarding, loan creation, payment recording, and collection reporting.

## Features

- Borrower management
- Loan creation and tracking
- Payment recording and reconciliation
- Dashboard metrics for portfolio overview
- Role-based delete permissions (`admin` only)
- Authenticated user profile management
- JSON API endpoints for loan and collection data

## Tech Stack

- PHP `^8.2`
- Laravel `^12.0`
- Eloquent ORM
- Blade templates
- Vite + Tailwind CSS + DaisyUI
- Alpine.js
- Axios
- PHPUnit

## Architecture

### Models

- `Borrower`
  - first name, last name, phone, email, address, status, notes
  - relationship: has many `Loan`
- `Loan`
  - borrower reference, principal, interest, term, start date, due date, payable amount, paid amount, status, remarks
  - relationship: belongs to `Borrower`, belongs to `User`, has many `Payment`
- `Payment`
  - loan reference, amount, paid date, method, reference number, remarks
  - relationship: belongs to `Loan`, belongs to `User`
- `User`
  - name, email, password, role
  - relationship: has many `Loan`, has many `Payment`

### Controllers

- `BorrowerController` — CRUD borrower operations, search, filter, and admin delete protection
- `LoanController` — CRUD loans, loan calculation, status handling, reference generation
- `PaymentController` — CRUD payments, loan total recalculation, status updates
- `DashboardController` — summary metrics and recent payments
- `ProfileController` — profile edit/update/delete
- API controllers: `LoanApiController`, `ReportApiController`

### Routes

- `routes/web.php`
  - `/dashboard`
  - `/borrowers`
  - `/loans`
  - `/payments`
  - `/profile`
- `routes/api.php`
  - `/api/loans`
  - `/api/loans/{loan}`
  - `/api/loans/{loan}/payments`
  - `/api/reports/collections`

## Installation

1. Clone the repository
2. Enable the PHP `gd` and `zip` extensions in the PHP configuration used by Composer.
3. Install PHP dependencies:
   ```bash
   composer install
   ```
4. Install frontend dependencies:
   ```bash
   npm install
   ```
5. Copy environment file and generate app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
6. Run migrations:
   ```bash
   php artisan migrate
   ```
7. Start the app:
   ```bash
   php artisan serve
   npm run dev
   ```

## Deploying with Docker on Render

The repository root contains a `render.yaml` Blueprint for a free demo backend and PostgreSQL database. Push the repository to a Git provider, connect it to Render, and create a Blueprint from the repository. The container builds the Laravel assets, listens on port `10000`, and runs database migrations on startup. The Blueprint disables public registration and generates the application key.

Set `BOOTSTRAP_ADMIN_EMAIL` and `BOOTSTRAP_ADMIN_PASSWORD` as secret environment variables when Render prompts for them. Use a unique password with at least 12 characters. On first startup, the container creates the initial administrator only if the database has no users. Remove both bootstrap secrets from the service environment after that first successful deploy; do not put them in source control or send them in chat.

The free configuration is only for a temporary demo with dummy data. Render free web services can sleep and have ephemeral local files; free PostgreSQL databases expire after 30 days. Do not use this configuration for actual borrower or loan records, or rely on it to retain uploaded borrower photos. Use paid persistent storage and database backups before handling real financial or personal information.

After the backend is deployed, set `VITE_LENDTRACK_URL` in `ionic-app/.env` to the deployed HTTPS URL, then rebuild and sync the Android app. The APK cannot be finalized until Render assigns the backend URL.

## Development Scripts

- `npm run dev` — start Vite development server
- `npm run build` — build production assets
- `php artisan test` — run Laravel tests

## Database

Key tables:

- `borrowers`
- `loans`
- `payments`
- `users` (with `role` column added)

Important behaviors:

- borrower deletion cascades to loans
- loan deletion cascades to payments
- payment updates recalculate loan totals and status

## Important Notes

- Loan status uses: `pending`, `active`, `paid`, `overdue`, `cancelled`
- Borrower status uses: `active`, `inactive`
- Payment status is derived from loan totals and due dates
- Delete actions are restricted to users with `admin` role

## View structure

- `resources/views/dashboard.blade.php`
- `resources/views/borrowers/*`
- `resources/views/loans/*`
- `resources/views/payments/*`
- `resources/views/profile/*`
- `resources/views/auth/*`
- `resources/views/layouts/*`

## License

This project is released under the MIT License.

## Ionic mobile app

A compatibility-first Ionic React + Capacitor app is available in [`ionic-app/`](ionic-app/README.md). It loads the existing Laravel application so current authentication, validation, role permissions, loan/payment rules, and reporting remain in the original backend. See `ionic-app/README.md` for browser and Android setup instructions. For production, deploy Laravel over HTTPS and set `VITE_LENDTRACK_URL` before building the mobile app.
