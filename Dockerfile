FROM php:8.2-apache-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       ca-certificates curl libcurl4-openssl-dev libfreetype6-dev libicu-dev \
       libjpeg62-turbo-dev libonig-dev libpng-dev libxml2-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
       bcmath curl exif gd intl mbstring mysqli opcache pdo_mysql soap zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/school-demo.ini
COPY docker/entrypoint.sh /usr/local/bin/school-demo-entrypoint
COPY --chown=root:root app/ /var/www/html/

RUN chmod 0755 /usr/local/bin/school-demo-entrypoint \
    && mkdir -p /var/www/html/application/tmp /var/www/html/application/logs \
       /var/www/html/application/cache /var/www/html/uploads /var/www/html/temp \
       /var/lib/school-demo/sessions /var/www/html/backend/captcha_images \
    && chown -R www-data:www-data /var/www/html/uploads \
       /var/www/html/application/tmp /var/www/html/application/logs \
       /var/www/html/application/cache /var/www/html/temp \
       /var/lib/school-demo/sessions /var/www/html/backend/captcha_images

WORKDIR /var/www/html
EXPOSE 80
ENTRYPOINT ["school-demo-entrypoint"]
CMD ["apache2-foreground"]
