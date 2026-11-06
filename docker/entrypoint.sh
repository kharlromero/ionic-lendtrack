#!/bin/sh
set -eu

: "${APP_KEY:?Set APP_KEY in the service environment}"
: "${APP_URL:?Set APP_URL to the deployed HTTPS URL}"
: "${DB_CONNECTION:?Set DB_CONNECTION to the configured database driver}"

if [ "${APP_DEBUG:-false}" != "false" ]; then
    echo "APP_DEBUG must be false for a public deployment." >&2
    exit 1
fi

php artisan migrate --force

if [ -n "${BOOTSTRAP_ADMIN_EMAIL:-}" ] || [ -n "${BOOTSTRAP_ADMIN_PASSWORD:-}" ]; then
    php artisan lendtrack:bootstrap-admin
fi

exec "$@"
