#!/bin/bash
set -e

cat > /var/www/.env << ENVFILE
APP_NAME=${APP_NAME:-FoodR}
APP_ENV=${APP_ENV:-local}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-true}
APP_URL=${APP_URL:-http://localhost:8000}
APP_LOCALE=${APP_LOCALE:-en}
APP_FALLBACK_LOCALE=${APP_FALLBACK_LOCALE:-en}
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug
DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-mysql}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-foodr}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-secret}
SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
BROADCAST_CONNECTION=${BROADCAST_CONNECTION:-log}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-public}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
CACHE_STORE=${CACHE_STORE:-database}
MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST:-mail.foodr.hu}
MAIL_PORT=${MAIL_PORT:-587}
MAIL_USERNAME=${MAIL_USERNAME:-noreply@foodr.hu}
MAIL_PASSWORD=${MAIL_PASSWORD:-noreplyfoodr123!}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-tls}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-noreply@foodr.hu}
MAIL_FROM_NAME=${MAIL_FROM_NAME:-FoodR}
VITE_APP_NAME=FoodR
ENVFILE

echo "Waiting for MySQL..."
until php -r "new PDO('mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306}', '${DB_USERNAME:-root}', '${DB_PASSWORD:-secret}');" 2>/dev/null; do
    sleep 2
    echo "MySQL not ready, retrying..."
done
echo "MySQL is ready!"

if grep -q "^APP_KEY=$" /var/www/.env; then
    echo "Generating application key..."
    php artisan key:generate --force --no-interaction
fi

echo "Running migrations..."
php artisan migrate --force

php artisan storage:link --force 2>/dev/null || true

php artisan wayfinder:generate --with-form 2>/dev/null || echo "Wayfinder skipped."

php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "http://localhost:5173" > /var/www/public/hot

echo "Starting FoodR on port 8000..."
exec php artisan serve --host=0.0.0.0 --port=8000