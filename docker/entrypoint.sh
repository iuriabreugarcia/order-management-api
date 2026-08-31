#!/bin/sh
set -eu

cd /var/www/html

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/api-docs

ENV_FILE="/var/www/html/.env"
KEY_FILE="/var/www/html/storage/docker-app-key"

if [ ! -f "$ENV_FILE" ]; then
    cp /var/www/html/.env.example "$ENV_FILE"
fi

if [ ! -f "$KEY_FILE" ]; then
    php -r 'echo "base64:".base64_encode(random_bytes(32));' > "$KEY_FILE"
fi

APP_KEY_VALUE="$(cat "$KEY_FILE")"

if grep -q '^APP_KEY=' "$ENV_FILE"; then
    sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY_VALUE}|" "$ENV_FILE"
else
    printf '\nAPP_KEY=%s\n' "$APP_KEY_VALUE" >> "$ENV_FILE"
fi

export APP_KEY="$APP_KEY_VALUE"

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    export DB_DATABASE="${DB_DATABASE:-/var/www/html/storage/database.sqlite}"
    mkdir -p "$(dirname "$DB_DATABASE")"
    touch "$DB_DATABASE"
fi

php artisan config:clear --no-ansi
php artisan migrate --force --no-interaction
php artisan l5-swagger:generate

exec "$@"
