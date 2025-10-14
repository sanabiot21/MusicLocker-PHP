# Multi-stage Dockerfile for Laravel Music Locker
# Stage 1: Build dependencies and assets
FROM php:8.2-fpm-alpine AS builder

# Install system dependencies and build tools
RUN apk add --no-cache \
    git \
    curl \
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

# Create basic .env file for composer install
RUN echo "APP_NAME=Laravel" > .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_KEY=" >> .env && \
    echo "APP_DEBUG=false" >> .env && \
    echo "APP_URL=http://localhost" >> .env

# Install PHP dependencies (production optimized)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --ignore-platform-reqs --verbose --no-scripts || \
    (echo "Composer install failed, trying with scripts..." && composer install --no-dev --no-interaction --prefer-dist --ignore-platform-reqs --verbose)

# Install Node dependencies
RUN npm ci

# Build frontend assets
RUN npm run build

# Optimize Laravel for production
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

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
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

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
