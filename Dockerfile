# Dockerfile
FROM php:8.3.16-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libicu-dev libpq-dev libzip-dev libonig-dev libxml2-dev mariadb-client \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy php.ini config
COPY .docker/php.ini /usr/local/etc/php/php.ini

# Permissions (adjust for your user if needed)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
