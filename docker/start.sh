#!/bin/bash
set -e

echo "🚀 Starting Music Locker v1 Application..."

# Create storage directories if they don't exist
echo "📁 Creating storage directories..."
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/cache
mkdir -p /var/www/html/storage/uploads

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chmod -R 755 /var/www/html/storage

# Copy .env if it doesn't exist
if [ ! -f /var/www/html/.env ]; then
    echo "📝 Creating .env from template..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Wait for database to be available
echo "⏳ Checking database connectivity..."
if [ -n "$DB_HOST" ]; then
    counter=0
    while ! nc -z "$DB_HOST" "${DB_PORT:-3306}" && [ $counter -lt 30 ]; do
        echo "⏳ Waiting for database ($counter/30)..."
        sleep 1
        counter=$((counter+1))
    done

    if ! nc -z "$DB_HOST" "${DB_PORT:-3306}"; then
        echo "⚠️  Database not available, but continuing..."
    else
        echo "✅ Database is available"
    fi
fi

# Start Apache
echo "🌐 Starting Apache..."
exec apache2-foreground
