FROM node:18-bullseye AS nodejs

WORKDIR /var/www/app
COPY package.json package-lock.json vite.config.js /var/www/app/
COPY resources /var/www/app/resources

RUN set -ex \
    && npm ci \
    && npm run build

FROM composer:2.4.2 as vendor

WORKDIR /app

COPY database/ database/
COPY app/ app/
COPY composer.json composer.lock ./

RUN composer install \
    --prefer-dist \
    --no-dev \
    --no-scripts \
    --no-plugins \
    --no-interaction \
    --ignore-platform-reqs

FROM php:8.1-fpm-alpine

RUN apk update && apk add --no-cache \
    supervisor \
    curl \
    openssl \
    nginx \
    libxml2-dev \
    oniguruma-dev \
    libzip-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    freetype-dev \
    pcre-dev $PHPIZE_DEPS \
    && rm -rf /var/cache/apk/*

RUN docker-php-ext-configure gd --with-jpeg --with-freetype
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl dom bcmath gd
RUN pecl install redis && docker-php-ext-enable redis.so

WORKDIR /var/www/app
COPY --chown=www-data:www-data . /var/www/app
COPY --chown=www-data:www-data --from=vendor /app/vendor /var/www/app/vendor
COPY --chown=www-data:www-data --from=nodejs /var/www/app/public/build /var/www/app/public/build
RUN set -ex \
    && php artisan vendor:publish --tag=livewire:assets --force \
    && chown -R www-data:www-data public/vendor/livewire

RUN rm /usr/local/etc/php-fpm.d/zz-docker.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /etc/php8/conf.d/50-setting.ini
COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
