#!/bin/bash

echo "🔧 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "🔧 Running migrations (including session, cache, queue)..."
php artisan migrate --force

echo "🛠️ Generating session table if not already done..."
php artisan session:table || true

echo "🛠️ Generating cache table if not already done..."
php artisan cache:table || true

echo "🛠️ Generating queue table if not already done..."
php artisan queue:table || true

echo "🧹 Clearing old caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "🚀 Caching config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Laravel build completed successfully."
