# Start from official PHP image
FROM php:8.2-fpm

# Install required PHP extensions and system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip git curl libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql mbstring

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy only files needed for composer
COPY composer.json composer.lock ./

# Then install deps early (cache layer if deps don’t change)
RUN composer install --no-dev --optimize-autoloader

# Copy the rest of the app
COPY . .


# ✅ Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Expose port
EXPOSE ${PORT}

# Run Laravel server
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
