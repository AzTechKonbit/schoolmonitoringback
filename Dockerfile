FROM php:8.2-apache

RUN apt-get update && apt-get install -y libonig-dev libzip-dev unzip libsqlite3-dev && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring pdo_sqlite zip

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN a2enmod rewrite

COPY . /var/www/

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

ENV APACHE_DOCUMENT_ROOT=/var/www/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www

COPY docker/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
