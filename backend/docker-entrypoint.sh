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

# Forward integration credentials from the container environment into .env,
# because `artisan serve` does not pass process env vars to its child process.
for key in HOSPITAL_EMR_BASE_URL HOSPITAL_EMR_OUTBOUND_API_KEY \
           HOSPITAL_EMR_INBOUND_API_KEY HOSPITAL_EMR_WEBHOOK_SIGNING_SECRET \
           HOSPITAL_EMR_WEBHOOK_VERIFY_SECRET HOSPITAL_EMR_WEBHOOK_URL; do
    value=$(printenv "$key" || true)
    [ -z "$value" ] && continue
    escaped=$(printf '%s' "$value" | sed -e 's/[\\&|]/\\&/g')
    if grep -Eq "^${key}=" .env; then
        sed -i "s|^${key}=.*|${key}=${escaped}|" .env
    else
        echo "${key}=${value}" >> .env
    fi
done

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
