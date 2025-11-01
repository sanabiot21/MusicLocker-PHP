#!/bin/bash

# Render deployment script for Music Locker Laravel
# This script handles the "View path not found" error

echo "🚀 Starting Music Locker Laravel deployment..."

# Ensure storage directories exist
echo "📁 Creating storage directories..."
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Install dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Generate application key if not exists
echo "🔑 Generating application key..."
php artisan key:generate --force

# Run migrations FIRST (before cache operations that might need the cache table)
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Clear all caches (skip if using database cache and table doesn't exist)
echo "🧹 Clearing caches..."
php artisan config:clear || echo "Config clear failed - continuing..."
php artisan cache:clear || echo "Cache clear failed - continuing..."
php artisan view:clear || echo "View clear failed - continuing..."
php artisan route:clear || echo "Route clear failed - continuing..."

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment completed successfully!"
