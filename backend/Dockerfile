FROM php:8.2-cli-alpine

# PHP extensions required by Laravel, JWT auth, dompdf and the SQLite database.
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
        libzip-dev icu-dev oniguruma-dev libxml2-dev sqlite-dev \
    && apk add --no-cache libzip icu oniguruma libxml2 sqlite-libs git \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_sqlite mbstring dom xml xmlwriter zip bcmath intl opcache \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install dependencies first so Docker can cache this layer.
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .

RUN composer dump-autoload --optimize \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chmod +x docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["./docker-entrypoint.sh"]
