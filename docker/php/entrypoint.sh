#!/bin/bash
set -e

echo "Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "Installing NPM dependencies and building assets..."
npm install
npm run build

echo "Setting correct permissions for storage and bootstrap/cache..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "Running Database Migrations..."
php artisan migrate --force

#echo "Running Database Seeders..."
#php artisan db:seed --force

echo "Storage Link"
php artisan storage:link

echo "Starting PHP-FPM..."
exec docker-php-entrypoint php-fpm
