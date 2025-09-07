#!/bin/bash
# docker/scripts/entrypoint.sh

set -e

PROJECT_DIR="/var/www/html/api"

# Create necessary directories only if they don't exist
for dir in "${PROJECT_DIR}/storage/framework/sessions" \
           "${PROJECT_DIR}/storage/framework/views" \
           "${PROJECT_DIR}/storage/framework/cache" \
           "${PROJECT_DIR}/storage/logs" \
           "${PROJECT_DIR}/bootstrap/cache" \
           "${PROJECT_DIR}/vendor"; do
    if [ ! -d "$dir" ]; then
        mkdir -p "$dir"
        echo "Created directory: $dir"
    fi
done

# Set permissions only if needed
if [ ! -w "${PROJECT_DIR}/storage" ] || [ ! -w "${PROJECT_DIR}/bootstrap/cache" ]; then
    echo "Setting permissions for storage and cache directories..."
    sudo chown -R www-data:www-data "${PROJECT_DIR}/storage"
    sudo chown -R www-data:www-data "${PROJECT_DIR}/bootstrap/cache"
    sudo chmod -R 775 "${PROJECT_DIR}/storage"
    sudo chmod -R 775 "${PROJECT_DIR}/bootstrap/cache"
fi

# Git configuration
git config --global --add safe.directory '*'
git config --global core.fileMode false
git config --global core.longpaths true

# Fix composer pubkeys issue
echo "Updating composer keys..."
composer self-update --update-keys --quiet 2>/dev/null || true

# Change to the correct working directory 
cd "${PROJECT_DIR}"

# Install dependencies if needed
if [ ! -f 'vendor/autoload.php' ]; then
    echo 'Installing Composer dependencies...'
    composer clear-cache
    COMPOSER_MEMORY_LIMIT=-1 composer install --no-cache --prefer-dist --no-scripts
else
    echo 'Composer dependencies already installed.'
fi

# Start server
echo "Starting server on port 80..."
if command -v php-fpm &> /dev/null; then
    echo "Starting with PHP-FPM..."
    # Start PHP-FPM
    php-fpm -D
    
    # Start Nginx or another web server if configured
    # For now, fallback to artisan serve
    exec php artisan serve --host=0.0.0.0 --port=80
else
    echo "Starting with artisan serve..."
    exec php artisan serve --host=0.0.0.0 --port=80
fi