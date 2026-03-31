#!/usr/bin/env bash
set -e

# Render fallback: generate a runtime APP_KEY if env var is missing.
if [ -z "${APP_KEY}" ]; then
  export APP_KEY="$(php artisan key:generate --show --no-interaction)"
fi

# Force file-based runtime drivers on boot.
export SESSION_DRIVER=file
export CACHE_STORE=file
export QUEUE_CONNECTION=sync

# Render requires the web process to bind to PORT (typically 10000).
PORT_TO_USE="${PORT:-10000}"
sed -ri "s/^Listen 80$/Listen ${PORT_TO_USE}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT_TO_USE}>/" /etc/apache2/sites-available/000-default.conf

# Build config cache, but do not crash boot if this step fails transiently.
php artisan config:cache || true

# Retry migrations a few times while the managed DB wakes up.
MIGRATED=0
for i in $(seq 1 15); do
  if php artisan migrate --force; then
    MIGRATED=1
    break
  fi
  echo "Migration attempt $i failed, retrying in 4s..."
  sleep 4
done

if [ "$MIGRATED" -ne 1 ]; then
  echo "Migrations did not complete during startup. Continuing to boot web service."
fi

apache2-foreground