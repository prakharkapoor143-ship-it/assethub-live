#!/bin/sh
set -e

if [ -z "${APP_KEY}" ]; then
  export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
fi

export SESSION_DRIVER=file
export CACHE_STORE=file
export QUEUE_CONNECTION=sync

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

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"