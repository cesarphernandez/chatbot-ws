FROM php:8.3-fpm
RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY ./code /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
