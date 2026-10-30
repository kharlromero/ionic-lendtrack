# Ionic migration notes

The `ionic-app/` directory contains an Ionic React + Capacitor shell. The existing Laravel application is preserved as the backend and continues to own all current routes, validation, role permissions, loan/payment calculations, and reports. The Ionic app opens those pages in a WebView to avoid changing business processes during the first migration stage.

This is deliberately a compatibility-first wrapper, not a claim that every Blade template has already been rewritten as a native Ionic screen. A true API-first Ionic rewrite would require adding and testing authenticated JSON endpoints for every current operation and carefully porting the Blade form validation, calculations, admin restrictions, export flows, and notification handling.

See `ionic-app/README.md` for run/build steps.
