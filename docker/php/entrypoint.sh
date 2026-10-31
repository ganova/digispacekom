#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

# vendor lives in a named volume (fast on Windows/macOS)
if [ "$APP_ENV" = "production" ]; then
    composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader
elif [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

# stale caches (an old DB password, a package list from another install mode) would break boot
php artisan package:discover >/dev/null
php artisan config:clear >/dev/null
php artisan route:clear >/dev/null
php artisan event:clear >/dev/null

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

# production: cache config/route/view for speed
if [ "$APP_ENV" = "production" ]; then
    php artisan optimize
else
    php artisan view:clear >/dev/null
fi

# logo klien yang diunggah dari CMS disimpan di public/uploads (ikut volume ./src)
mkdir -p public/uploads/clients
chown -R www-data:www-data storage bootstrap/cache public/uploads

exec "$@"
