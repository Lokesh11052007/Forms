FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip git curl \
    libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libonig5 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql mbstring

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy only composer files first (for caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy the rest of the app
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Generate Laravel key if missing
RUN if [ ! -f ".env" ]; then cp .env.example .env && php artisan key:generate; fi

# Clear and cache config
RUN php artisan config:cache

# Expose the correct port (Railway uses dynamic ports)
EXPOSE ${PORT}

# Start PHP-FPM (better for production) OR artisan serve (for testing)
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}