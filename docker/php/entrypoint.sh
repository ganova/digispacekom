#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

# vendor lives in a named volume (fast on Windows/macOS); install on first boot
if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if ! grep -q '^APP_KEY=base64' .env; then
    php artisan key:generate --force
fi

# wait for the database, then migrate (seed only on an empty DB)
until php artisan db:show >/dev/null 2>&1; do
    echo "waiting for database..."
    sleep 2
done

if php artisan migrate:status 2>&1 | grep -q 'Migration table not found'; then
    php artisan migrate --force --seed
else
    php artisan migrate --force
fi

chown -R www-data:www-data storage bootstrap/cache

exec "$@"
