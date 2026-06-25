FROM composer:2 AS vendor
WORKDIR /app
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json .npmrc ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build

FROM php:8.4-fpm-alpine AS runtime
RUN apk add --no-cache icu-libs libzip postgresql-libs oniguruma libxml2 \
    && apk add --no-cache --virtual .build-deps icu-dev libzip-dev postgresql-dev oniguruma-dev libxml2-dev $PHPIZE_DEPS \
    && docker-php-ext-install -j$(nproc) bcmath dom intl mbstring opcache pcntl pdo_pgsql xml zip \
    && apk del .build-deps
WORKDIR /var/www/html
COPY --from=vendor /app /var/www/html
COPY --from=frontend /app/public/build /var/www/html/public/build
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-sim-rumah-maggot.ini
COPY docker/php/entrypoint.sh /usr/local/bin/app-entrypoint
RUN chmod +x /usr/local/bin/app-entrypoint \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs storage/app/public bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache
ENTRYPOINT ["app-entrypoint"]
CMD ["php-fpm", "-F"]
