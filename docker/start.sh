#!/usr/bin/env bash
set -e

php artisan optimize:clear
php artisan config:cache
php artisan migrate --force

apache2-foreground
