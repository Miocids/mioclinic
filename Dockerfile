FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by Laravel/Filament
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip

# Install redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /var/www/html

# Define user using existing www-data
RUN usermod -u 1000 www-data \
    && chown -R www-data:www-data /var/www/html

# Copy entrypoint scripts
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/php/worker-entrypoint.sh /usr/local/bin/worker-entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/worker-entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
