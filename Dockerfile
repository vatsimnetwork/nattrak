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

ARG NEW_RELIC_AGENT_VERSION=10.13.0.2

# Install PHP extensions
RUN set -ex \
    && apt-get update \
    && apt-get install --no-install-recommends -y git unzip \
    && docker-php-ext-install bcmath opcache pcntl pdo_mysql \
    && pecl install redis-5.3.7 \
    && apt-get purge -y --auto-remove git \
    && curl -L https://download.newrelic.com/php_agent/archive/${NEW_RELIC_AGENT_VERSION}/newrelic-php5-${NEW_RELIC_AGENT_VERSION}-linux.tar.gz | tar -C /tmp -zx \
    && cp /tmp/newrelic-php5-${NEW_RELIC_AGENT_VERSION}-linux/agent/x64/newrelic-20220829.so /usr/local/lib/php/extensions/no-debug-non-zts-20220829/newrelic.so \
    && rm -rf /tmp/newrelic-php5-* /tmp/pear /var/lib/apt/lists/* \
    && docker-php-ext-enable newrelic redis

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
