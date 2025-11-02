#!/bin/sh

# Music Locker Laravel - Render Startup Script

# Don't exit on error immediately - we'll handle errors explicitly
set +e

echo "Starting Music Locker Laravel application..."

# Function to test database connection with retry logic
test_db_connection() {
    local max_attempts=5
    local attempt=1
    local delay=2
    
    echo "Checking database connection..."
    echo "DB_HOST: ${DB_HOST:-not set}"
    echo "DB_PORT: ${DB_PORT:-not set}"
    echo "DB_DATABASE: ${DB_DATABASE:-not set}"
    
    while [ $attempt -le $max_attempts ]; do
        echo "Database connection attempt $attempt of $max_attempts..."
        
        # Test connection using artisan tinker
        php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database connected successfully'; exit(0); } catch (Exception \$e) { echo 'Connection failed: ' . \$e->getMessage(); exit(1); }" 2>&1
        
        if [ $? -eq 0 ]; then
            echo "✓ Database connection successful!"
            return 0
        fi
        
        if [ $attempt -lt $max_attempts ]; then
            echo "✗ Database connection failed. Retrying in ${delay} seconds..."
            sleep $delay
            # Exponential backoff: 2s, 4s, 8s, 16s
            delay=$((delay * 2))
        fi
        
        attempt=$((attempt + 1))
    done
    
    echo "✗ Failed to connect to database after $max_attempts attempts"
    echo "Please check:"
    echo "  1. Supabase project is active and running"
    echo "  2. DB_HOST points to transaction pooler (ends with .pooler.supabase.com)"
    echo "  3. DB_PORT is set to 6543 for transaction pooler"
    echo "  4. DB_SSLMODE is set to 'require'"
    echo "  5. Supabase network settings allow connections from Render (IP whitelisting)"
    return 1
}

# Test database connection with retry logic
if ! test_db_connection; then
    echo "ERROR: Cannot proceed without database connection"
    exit 1
fi

# Run database migrations with retry logic
echo "Running database migrations..."
max_migration_attempts=3
migration_attempt=1
migration_delay=3

while [ $migration_attempt -le $max_migration_attempts ]; do
    echo "Migration attempt $migration_attempt of $max_migration_attempts..."
    
    php artisan migrate --force 2>&1
    
    if [ $? -eq 0 ]; then
        echo "✓ Migrations completed successfully!"
        break
    fi
    
    if [ $migration_attempt -lt $max_migration_attempts ]; then
        echo "✗ Migrations failed. Retrying in ${migration_delay} seconds..."
        sleep $migration_delay
        migration_delay=$((migration_delay * 2))
    else
        echo "✗ Migrations failed after $max_migration_attempts attempts"
        echo "ERROR: Cannot proceed without successful migrations"
        exit 1
    fi
    
    migration_attempt=$((migration_attempt + 1))
done

# Re-enable exit on error for the rest of the script
set -e

# Build assets if not already built
if [ ! -d "/var/www/public/build" ]; then
    echo "Building frontend assets..."
    npm run build
fi

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
