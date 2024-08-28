# Use the official PHP-FPM image
FROM php:8.2-fpm

# Install necessary PHP extensions and tools
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set the working directory to the project root
WORKDIR /var/www/html

# Copy the composer files first and install dependencies
#COPY ./code/composer.json ./code/composer.lock ./
COPY ./code /var/www/html

# Copy the rest of the application code

RUN composer install
# Run the autoloader (after all files are copied)
RUN chown -R www-data:www-data /var/www/html/vendor
RUN composer dump-autoload --optimize

# Set file permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
