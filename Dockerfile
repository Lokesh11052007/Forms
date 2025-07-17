FROM php:8.2-fpm

# Install system dependencies
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

# Copy files required before running `composer install`
COPY composer.json composer.lock ./
COPY artisan ./
COPY bootstrap ./bootstrap

# Run composer install (artisan and bootstrap/app.php now exist)
RUN composer install --no-dev --optimize-autoloader

# Now copy the rest of your Laravel project
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Expose port for Railway
EXPOSE ${PORT}

# Run Laravel
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
