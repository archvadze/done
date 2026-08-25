#!/bin/sh
set -e

cd /var/www/html

# Generate an app key on the fly if one wasn't provided as an env var
if [ -z "$APP_KEY" ]; then
  export APP_KEY=$(php artisan key:generate --show --force)
fi

mkdir -p database
touch database/database.sqlite

php artisan config:clear
php artisan migrate --force
php artisan db:seed --force || true
php artisan config:cache

# Render provides the port to bind to via $PORT
php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
