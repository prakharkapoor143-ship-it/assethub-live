#!/usr/bin/env bash
set -e

# Generate a runtime APP_KEY only if one is not provided by platform env vars.
if [ -z "${APP_KEY}" ]; then
  export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
fi

# Safe defaults so boot does not depend on DB-backed cache/session tables.
export SESSION_DRIVER=file
export CACHE_STORE=file
export QUEUE_CONNECTION=sync

# Run migrations in the background so health checks can pass quickly.
(
  for i in $(seq 1 20); do
    if php artisan migrate --force; then
      echo "Migrations completed."
      exit 0
    fi
    echo "Migration attempt $i failed, retrying in 5s..."
    sleep 5
  done
  echo "Migrations could not complete during startup retries."
) &

# Start web server immediately on Render's required port.
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"