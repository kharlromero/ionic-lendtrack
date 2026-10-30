# LendTrack Ionic app

This is an Ionic React + Capacitor mobile shell for the existing LendTrack Laravel application. It intentionally leaves the Laravel app responsible for all business behavior, including sign-in, role checks, borrower/loan/payment CRUD, loan calculations, notifications, reports, and exports. The Ionic app loads the existing Laravel UI rather than duplicating those rules in a second frontend, which minimizes risk of changing the system's workflow.

## 1. Start the Laravel backend

From the repository root (the folder containing `artisan`):

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve --host 0.0.0.0 --port 8000
```

Configure the database in the root `.env` before migrating. Use your existing database backup if you need existing records; do not overwrite a production database.

## 2. Start Ionic in a browser

```bash
cd ionic-app
cp .env.example .env
npm install
npm run dev
```

Open the Vite URL shown in the terminal (normally `http://localhost:8100`). The backend should be reachable at `http://localhost:8000`.

## 3. Test on a physical phone

Set `VITE_LENDTRACK_URL` in `ionic-app/.env` to a reachable HTTPS backend URL. For LAN-only testing, use your development computer's LAN IP instead of `localhost` (for example `http://192.168.1.20:8000`), and ensure the phone and computer are on the same network. Never expose a development server to the public internet.

## 4. Build Android

```bash
cd ionic-app
npm install
npm run build
npx cap add android
npx cap sync android
npx cap open android
```

Build and run the Android project in Android Studio. For later frontend changes, run `npm run build` and `npx cap sync android` again. iOS builds require macOS and Xcode; install `@capacitor/ios`, then run `npx cap add ios` and `npx cap open ios`.

## Important deployment and session notes

- Deploy the Laravel backend over HTTPS for production and set `VITE_LENDTRACK_URL` to that deployed URL before building the mobile app.
- This app embeds the existing Laravel pages in a WebView. Depending on the platform and browser cookie policy, embedded cross-origin session cookies may be blocked. If login does not persist on a device, use the **Open in browser** button as a fallback or plan a later API/token-auth migration. Do not weaken cookie security on a public deployment just to bypass this limitation.
- The existing Laravel routes and controllers are kept intact. This is a compatibility-first Ionic shell, not a full rewrite of every Blade screen into native Ionic components.
- Do not commit `.env`, production credentials, or a live database file.
