#!/usr/bin/env bash
set -e

# Render fallback: generate a runtime APP_KEY if env var is missing.
if [ -z "${APP_KEY}" ]; then
  export APP_KEY="$(php artisan key:generate --show --no-interaction)"
fi

# Force safe runtime drivers on boot.
export SESSION_DRIVER=file
export CACHE_STORE=file
export QUEUE_CONNECTION=sync

# Build config cache, but do not crash boot if this step fails transiently.
php artisan config:cache || true

# Retry migrations while managed DB wakes up.
for i in $(seq 1 15); do
  if php artisan migrate --force; then
    break
  fi
  echo "Migration attempt $i failed, retrying in 4s..."
  sleep 4
done

# Serve Laravel directly on Render's required port.
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"