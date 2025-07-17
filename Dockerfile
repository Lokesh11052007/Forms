FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip git curl nginx \
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

# Cache config for production
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Copy Nginx config
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Expose the correct port (Railway uses dynamic ports)
EXPOSE ${PORT}

# Start Nginx and PHP-FPM
CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"