#!/bin/sh

# Music Locker Laravel - Render Startup Script

set -e

echo "Starting Music Locker Laravel application..."

# Wait for database to be ready
echo "Checking database connection..."
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully';" || {
    echo "Database connection failed. Retrying in 5 seconds..."
    sleep 5
    php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully';"
}

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Clear and cache configuration for production
echo "Optimizing Laravel for production..."

# Ensure storage directories exist
echo "Creating storage directories..."
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/framework/cache
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/logs
mkdir -p /var/www/bootstrap/cache

# Clear caches first
php artisan config:clear || echo "Config clear failed - continuing..."
php artisan cache:clear || echo "Cache clear failed - continuing..."
php artisan view:clear || echo "View clear failed - continuing..."
php artisan route:clear || echo "Route clear failed - continuing..."

# Cache for production
php artisan config:cache || echo "Config cache failed - continuing..."
php artisan route:cache || echo "Route cache failed - continuing..."
php artisan view:cache || echo "View cache failed - continuing..."

# Create storage link if it doesn't exist
if [ ! -L /var/www/public/storage ]; then
    echo "Creating storage link..."
    php artisan storage:link
fi

# Set proper permissions
echo "Setting file permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "Starting services..."

# Start supervisor (which manages nginx and php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
