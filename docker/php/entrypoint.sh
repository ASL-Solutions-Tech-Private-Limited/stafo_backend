#!/bin/sh
set -e

# Ensure storage and cache directories exist
mkdir -p /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/logs \
         /var/www/bootstrap/cache

chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# Create storage symlink if public/storage does not exist
if [ ! -L "/var/www/public/storage" ]; then
    php /var/www/artisan storage:link 2>/dev/null || true
fi

exec "$@"
