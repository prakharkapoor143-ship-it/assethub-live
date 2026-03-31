#!/usr/bin/env bash
set -e

# Render fallback: generate a runtime APP_KEY if env var is missing.
# This keeps first deploy simple when dashboard env var UI is unclear.
if [ -z "${APP_KEY}" ]; then
  export APP_KEY="$(php artisan key:generate --show --no-interaction)"
fi

php artisan optimize:clear
php artisan config:cache
php artisan migrate --force

apache2-foreground