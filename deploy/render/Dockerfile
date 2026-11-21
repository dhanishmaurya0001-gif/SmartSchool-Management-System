FROM php:8.2-apache-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       ca-certificates curl mariadb-server mariadb-client \
       libcurl4-openssl-dev libfreetype6-dev libicu-dev libjpeg62-turbo-dev \
       libonig-dev libpng-dev libxml2-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j2 bcmath curl exif gd intl mbstring mysqli opcache pdo_mysql soap zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/* /var/lib/mysql/*

COPY deploy/render/apache.conf /etc/apache2/sites-available/000-default.conf
COPY deploy/render/mpm-prefork.conf /etc/apache2/mods-available/mpm_prefork.conf
COPY deploy/render/php.ini /usr/local/etc/php/conf.d/render-demo.ini
COPY deploy/render/mariadb.cnf /etc/school-demo-mariadb.cnf
COPY deploy/render/https-bootstrap.php /opt/demo/https-bootstrap.php
COPY deploy/render/entrypoint.sh /usr/local/bin/render-school-demo
COPY database.sql /opt/demo/database.sql
COPY --chown=root:root app/ /var/www/html/

RUN chmod 0755 /usr/local/bin/render-school-demo \
    && mkdir -p /var/lib/demo-mysql /run/mysqld /var/lib/school-demo/sessions \
       /var/www/html/application/tmp /var/www/html/application/cache \
       /var/www/html/application/logs /var/www/html/temp \
       /var/www/html/backend/captcha_images \
    && chown mysql:mysql /var/lib/demo-mysql /run/mysqld \
    && chown -R www-data:www-data /var/www/html/uploads \
       /var/www/html/application/tmp /var/www/html/application/cache \
       /var/www/html/application/logs /var/www/html/temp \
       /var/www/html/backend/captcha_images /var/lib/school-demo/sessions

WORKDIR /var/www/html
EXPOSE 10000
ENTRYPOINT ["render-school-demo"]
