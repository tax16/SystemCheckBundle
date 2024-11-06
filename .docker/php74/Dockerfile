FROM php:7.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install zip pdo pdo_mysql sockets

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHPUnit for testing
RUN composer global require phpunit/phpunit --prefer-dist --no-progress --no-suggest
RUN echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/memory-limit.ini

# Set working directory
WORKDIR /var/www
