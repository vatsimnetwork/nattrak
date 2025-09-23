FROM node:18-bullseye AS nodejs

WORKDIR /var/www/app

ARG VITE_PUSHER_APP_KEY
ARG VITE_PUSHER_HOST
ARG VITE_PUSHER_PORT

COPY package.json package-lock.json vite.config.js /var/www/app/
COPY resources /var/www/app/resources

RUN set -ex \
    && npm ci \
    && VITE_PUSHER_APP_KEY=${VITE_PUSHER_APP_KEY} VITE_PUSHER_HOST=${VITE_PUSHER_HOST} VITE_PUSHER_PORT=${VITE_PUSHER_PORT} npm run build

FROM unit:1.31.1-php8.2

# Install PHP extensions
RUN set -ex \
    && apt-get update \
    && apt-get install --no-install-recommends -y git unzip \
    && docker-php-ext-install bcmath opcache pcntl pdo_mysql \
    && pecl install excimer-1.2.5 redis-5.3.7 \
    && apt-get purge -y --auto-remove git \
    && docker-php-ext-enable excimer redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure PHP & Unit
COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/bootstrap-laravel.sh /docker-entrypoint.d/
COPY docker/unit.json /docker-entrypoint.d/

# Copy application files
WORKDIR /var/www/app
COPY --chown=unit:unit . /var/www/app

# Install Composer dependencies
RUN set -ex \
    && composer install --no-dev --optimize-autoloader \
    && php artisan vendor:publish --tag=livewire:assets \
    && rm -rf /root/.composer \
    && chown -R unit:unit bootstrap/cache public/vendor/livewire vendor

# Copy built frontend files
COPY --from=nodejs --chown=unit:unit /var/www/app/public/build /var/www/app/public/build

# CMD and ENTRYPOINT are inherited from the Unit image
