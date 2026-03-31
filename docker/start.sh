#!/usr/bin/env bash
set -e

# Render fallback: generate a runtime APP_KEY if env var is missing.
if [ -z "${APP_KEY}" ]; then
  export APP_KEY="$(php artisan key:generate --show --no-interaction)"
fi

# Use file-based runtime drivers on first boot to avoid DB cache/session table dependency.
export SESSION_DRIVER=file
export CACHE_STORE=file
export QUEUE_CONNECTION=sync

php artisan config:cache
php artisan migrate --force

apache2-foreground