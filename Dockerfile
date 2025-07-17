FROM php:8.2-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    zip \
    git \
    curl \
    libonig-dev \
    pkg-config \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mbstring zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy only what's needed for composer
COPY composer.json composer.lock artisan bootstrap/ ./

# Now install dependencies
RUN composer install --no-dev --optimize-autoloader

# Now copy the rest of the app (after vendor exists)
COPY . .

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Railway exposes a dynamic PORT
EXPOSE ${PORT}

# Start Laravel app
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
