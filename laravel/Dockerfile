# Multi-stage Dockerfile for Laravel Music Locker
# Stage 1: Build dependencies and assets
FROM php:8.2-fpm-alpine AS builder

# Install system dependencies and build tools
RUN apk add --no-cache \
    git \
    curl \
    openssl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    postgresql-dev \
    postgresql-libs \
    oniguruma-dev \
    libzip-dev \
    autoconf \
    g++ \
    make \
    linux-headers

# Install PostgreSQL extensions first
RUN docker-php-ext-install -j$(nproc) pdo_pgsql pgsql

# Install other simple extensions
RUN docker-php-ext-install -j$(nproc) mbstring exif pcntl bcmath zip

# Configure and install GD extension separately (it's complex)
RUN docker-php-ext-configure gd \
    --with-freetype=/usr/include/ \
    --with-jpeg=/usr/include/ \
    --with-webp=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy all application files first
COPY . .

# Create comprehensive .env file for PostgreSQL/Supabase with generated key
RUN APP_KEY="base64:$(openssl rand -base64 32)" && \
    echo "APP_NAME=Laravel" > .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_KEY=$APP_KEY" >> .env && \
    echo "APP_DEBUG=false" >> .env && \
    echo "APP_URL=http://localhost" >> .env && \
    echo "LOG_CHANNEL=stack" >> .env && \
    echo "LOG_DEPRECATIONS_CHANNEL=null" >> .env && \
    echo "LOG_LEVEL=debug" >> .env && \
    echo "DB_CONNECTION=pgsql" >> .env && \
    echo "DB_HOST=localhost" >> .env && \
    echo "DB_PORT=5432" >> .env && \
    echo "DB_DATABASE=laravel" >> .env && \
    echo "DB_USERNAME=postgres" >> .env && \
    echo "DB_PASSWORD=" >> .env && \
    echo "BROADCAST_DRIVER=log" >> .env && \
    echo "CACHE_DRIVER=file" >> .env && \
    echo "FILESYSTEM_DISK=local" >> .env && \
    echo "QUEUE_CONNECTION=sync" >> .env && \
    echo "SESSION_DRIVER=file" >> .env && \
    echo "SESSION_LIFETIME=120" >> .env

# Install PHP dependencies (production optimized)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --ignore-platform-reqs --verbose --no-scripts || \
    (echo "Composer install failed, trying with scripts..." && composer install --no-dev --no-interaction --prefer-dist --ignore-platform-reqs --verbose)

# Install Node dependencies
RUN npm ci

# Build frontend assets
RUN npm run build

# Skip artisan key:generate since we already have the key in .env

# Optimize Laravel for production (with error handling)
RUN php artisan config:cache --ansi || echo "Config cache failed - continuing..." && \
    php artisan route:cache --ansi || echo "Route cache failed - continuing..." && \
    php artisan view:cache --ansi || echo "View cache failed - continuing..."

# Stage 2: Production runtime
FROM php:8.2-fpm-alpine AS production

# Install runtime dependencies and build tools
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    postgresql-libs \
    libpng \
    libpng-dev \
    libjpeg-turbo \
    libjpeg-turbo-dev \
    freetype \
    freetype-dev \
    libwebp \
    libwebp-dev \
    libxml2 \
    oniguruma \
    oniguruma-dev \
    libzip \
    libzip-dev \
    autoconf \
    g++ \
    make \
    linux-headers

# Install PostgreSQL extensions first
RUN docker-php-ext-install -j$(nproc) pdo_pgsql pgsql

# Install other simple extensions
RUN docker-php-ext-install -j$(nproc) mbstring exif pcntl bcmath zip

# Configure and install GD extension separately (it's complex)
RUN docker-php-ext-configure gd \
    --with-freetype=/usr/include/ \
    --with-jpeg=/usr/include/ \
    --with-webp=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# Ensure www-data user and group exist
RUN if ! getent group www-data >/dev/null 2>&1; then \
        addgroup -g 1000 -S www-data; \
    fi && \
    if ! getent passwd www-data >/dev/null 2>&1; then \
        adduser -u 1000 -D -S -G www-data www-data; \
    fi

# Set working directory
WORKDIR /var/www

# Copy built application from builder stage
COPY --from=builder --chown=www-data:www-data /var/www .

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create necessary directories and set permissions
RUN mkdir -p /var/log/nginx /var/log/supervisor /run/nginx && \
    mkdir -p /var/www/storage/app /var/www/storage/framework /var/www/storage/logs && \
    mkdir -p /var/www/bootstrap/cache && \
    ls -la /var/www/ && \
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache || echo "chown failed - continuing..." && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache || echo "chmod failed - continuing..."

# Copy startup script
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Start supervisor
CMD ["/usr/local/bin/start.sh"]
