#!/bin/sh

# Music Locker v1 (Custom PHP) - Render Startup Script

set -e

echo "🚀 Starting Music Locker v1 (Custom PHP) application..."

# Create storage directory if it doesn't exist
echo "📁 Creating storage directories..."
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/uploads
mkdir -p /var/www/storage/cache

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/storage
chmod -R 775 /var/www/storage

# Copy .env if it doesn't exist
if [ ! -f /var/www/.env ]; then
    echo "📝 Creating .env from .env.example..."
    if [ -f /var/www/.env.example ]; then
        cp /var/www/.env.example /var/www/.env
    else
        echo "⚠️  .env.example not found, creating minimal .env..."
        cat > /var/www/.env << 'EOF'
APP_NAME=Music Locker v1
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Manila
DB_CONNECTION=mysql
EOF
    fi
fi

# Wait for database to be available
if [ -n "$DB_HOST" ]; then
    echo "⏳ Waiting for database connection..."
    counter=0
    max_attempts=30

    while [ $counter -lt $max_attempts ]; do
        if php -r "
            \$host = getenv('DB_HOST');
            \$port = getenv('DB_PORT') ?: 3306;
            \$user = getenv('DB_USERNAME');
            \$pass = getenv('DB_PASSWORD');
            \$db = getenv('DB_DATABASE');
            \$conn_type = getenv('DB_CONNECTION') ?: 'mysql';

            try {
                if (\$conn_type === 'pgsql') {
                    \$dsn = 'pgsql:host=' . \$host . ';port=' . \$port . ';dbname=' . \$db;
                } else {
                    \$dsn = 'mysql:host=' . \$host . ';port=' . \$port . ';dbname=' . \$db;
                }
                \$pdo = new PDO(\$dsn, \$user, \$pass);
                echo 'Connected';
            } catch(Exception \$e) {
                exit(1);
            }
        " 2>/dev/null; then
            echo "✅ Database is ready!"
            break
        fi

        counter=$((counter + 1))
        echo "⏳ Attempt $counter/$max_attempts - Waiting for database..."
        sleep 1
    done

    if [ $counter -eq $max_attempts ]; then
        echo "⚠️  Database connection failed after $max_attempts attempts, but continuing..."
    fi
else
    echo "⚠️  DB_HOST not set, skipping database check"
fi

# Set correct permissions on all directories
echo "🔐 Setting final permissions..."
chown -R www-data:www-data /var/www
chmod -R 755 /var/www
chmod -R 777 /var/www/storage 2>/dev/null || true
chmod -R 777 /var/www/public 2>/dev/null || true

echo "🌐 Starting nginx and php-fpm via supervisor..."

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
