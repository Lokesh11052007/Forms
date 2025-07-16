# Use the official PHP image with Apache
FROM php:8.2-apache

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libonig-dev libzip-dev unzip curl \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy app files into container
COPY . /var/www/html

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Fix Apache to use Laravel's public folder
RUN rm -rf /var/www/html/index.html \
    && cp -r public/* /var/www/html/ \
    && rm -rf public

# Ensure Apache serves from Laravel's public path
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html|' /etc/apache2/sites-available/000-default.conf
