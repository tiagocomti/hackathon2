FROM --platform=linux/arm64/v8 php:8.3-fpm

RUN apt-get update -y && apt-get install -y libpng-dev libicu-dev libzip-dev

RUN yes '' | pecl install swoole-5.0.3
RUN docker-php-ext-enable swoole

RUN pecl install timezonedb
RUN docker-php-ext-enable timezonedb

RUN pecl install redis
RUN docker-php-ext-enable redis.so

RUN docker-php-ext-install gd
RUN docker-php-ext-install gettext
RUN docker-php-ext-install intl
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install opcache
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install sockets
RUN docker-php-ext-install zip

RUN useradd www
RUN mkdir -p /var/www/html/ && chown www:www /var/www/html
COPY . /var/www/html
COPY docker-entrypoint.sh /
RUN chmod 777 /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

RUN cd /var/www/html/
WORKDIR /var/www/html
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux
RUN /usr/local/bin/composer update --quiet || true
RUN /usr/local/bin/composer install -n
RUN chmod -R 777 /var/www/html/runtime
RUN chmod -R 777 /var/www/html/web/assets

EXPOSE 9500

ENTRYPOINT ["/docker-entrypoint.sh"]