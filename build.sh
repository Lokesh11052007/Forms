#!/bin/bash

echo "🔧 Installing Composer dependencies..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo "🛠️ Generating session/cache/queue tables if needed..."
php artisan session:table || true
php artisan cache:table || true
php artisan queue:table || true

echo "🔄 Running all migrations..."
php artisan migrate --force

echo "🔐 Fixing permissions..."
chmod -R 775 storage bootstrap/cache

echo "🧹 Clearing and caching config..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Laravel build complete."
