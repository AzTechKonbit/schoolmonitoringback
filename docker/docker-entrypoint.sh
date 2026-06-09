#!/bin/bash
set -e

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=" .env || [ -z "$(grep '^APP_KEY=' .env | cut -d= -f2)" ]; then
    php artisan key:generate --force
fi

php artisan migrate --force
php artisan db:seed --force
php artisan scribe:generate --force 2>/dev/null || true



# Cache config (optionnel mais recommandé en prod)
php artisan config:cache
php artisan route:cache
php artisan view:cache


chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
chmod 777 database/

exec apache2-foreground
