#!/bin/sh
set -e

cd /var/www/html

# Persist the SQLite database outside the image.
mkdir -p /data
if [ ! -f /data/database.sqlite ]; then
    touch /data/database.sqlite
fi

# Create a runtime .env from the tracked template (never baked into the image).
if [ ! -f .env ]; then
    cp .env.example .env
fi

# `artisan serve` does not forward container environment variables to the PHP
# server child process, so point SQLite at the persistent volume inside .env.
if ! grep -Eq '^DB_DATABASE=' .env; then
    echo 'DB_DATABASE=/data/database.sqlite' >> .env
else
    sed -i 's|^DB_DATABASE=.*|DB_DATABASE=/data/database.sqlite|' .env
fi

if ! grep -Eq '^APP_KEY=.+' .env; then
    php artisan key:generate --force
fi

if ! grep -Eq '^JWT_SECRET=.+' .env; then
    php artisan jwt:secret --force
fi

# Apply all migrations; this is safe to run on every start.
php artisan migrate --force

# Seed only once, on a fresh database.
if [ ! -f /data/.seeded ]; then
    USER_COUNT=$(php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo \App\Models\User::count();
' 2>/dev/null | tr -cd '0-9')
    if [ "${USER_COUNT:-0}" = "0" ]; then
        php artisan db:seed --force
    fi
    touch /data/.seeded
fi

exec php artisan serve --host=0.0.0.0 --port=8000
