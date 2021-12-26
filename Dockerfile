FROM alpine:latest

# Set the locale
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8
WORKDIR /application
RUN apk --update add \
        php7 \
        php7-bcmath \
        php7-dom \
        php7-ctype \
        php7-curl \
        php7-fpm \
        php7-gd \
        php7-iconv \
        php7-intl \
        php7-json \
        php7-mbstring \
        php7-mcrypt \
        php7-fileinfo \
        php7-mysqlnd \
        php7-opcache \
        php7-openssl \
        php7-pdo \
        php7-pdo_mysql \
        php7-pdo_pgsql \
        php7-pdo_sqlite \
        php7-phar \
        php7-posix \
        php7-session \
        php7-soap \
        php7-xml \
        php7-zip \
        php7-tokenizer \
        php7-xmlwriter \
        git \
        php7-simplexml \
        curl \
        npm \
        nginx \
        supervisor \
    && rm -rf /var/cache/apk/*

COPY . /application

COPY docker_files/php.ini /etc/php7/conf.d/50-setting.ini
COPY docker_files/php-fpm.conf /etc/php7/php-fpm.conf
COPY docker_files/nginx.conf /etc/nginx/nginx.conf
COPY docker_files/.env /application/.env
COPY docker_files/start_nginx.sh /application/start_nginx.sh
COPY docker_files/supervisord.conf /etc/supervisor/conf.d/supervisord.conf



RUN cd /application && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin -- --filename=composer && composer install && chown -R nobody:nobody /application && chmod +x /application/start_nginx.sh

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
