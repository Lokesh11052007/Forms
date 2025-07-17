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

# Copy only necessary files early to avoid errors in composer
COPY composer.json composer.lock artisan bootstrap/ ./

# Run composer install (now artisan + bootstrap/app.php exist!)
RUN composer install --no-dev --optimize-autoloader

# Now copy the rest of the Laravel project
COPY . .

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Railway uses dynamic port variable
EXPOSE ${PORT}

# Start the Laravel app
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
