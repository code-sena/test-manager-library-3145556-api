FROM php:8.3-cli-alpine

RUN apk add --no-cache postgresql-dev libpng-dev libzip-dev zip unzip git

RUN docker-php-ext-install pdo pgsql pdo_pgsql gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p /var/www/storage/covers && chmod -R 775 /var/www/storage

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan library:bootstrap-admin && php -S 0.0.0.0:8000 -t /var/www/public"]
