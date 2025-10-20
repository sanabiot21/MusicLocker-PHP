# Music Locker v1 (Custom PHP) - Render Deployment
# Stage 1: Builder
FROM php:8.2-cli-alpine AS builder

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    unzip \
    postgresql-dev \
    postgresql-libs \
    openssl \
    libzip-dev \
    autoconf \
    g++ \
    make \
    linux-headers \
    oniguruma-dev

# Install PHP extensions - simple approach
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files
COPY composer.json composer.lock* ./

# Install PHP dependencies (production optimized)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Copy all application code
COPY . .

# Stage 2: Production Runtime
FROM php:8.2-cli-alpine AS production

# Install runtime dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    postgresql-libs \
    libzip-dev \
    zip \
    curl \
    linux-headers \
    oniguruma-dev

# Install PHP extensions - simple approach
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip

# Skip user creation - use existing user
RUN echo "Using existing user setup"

# Set working directory
WORKDIR /var/www

# Copy built application from builder stage
COPY --from=builder /var/www .

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create necessary directories
RUN mkdir -p /var/log/nginx /var/log/supervisor /run/nginx && \
    mkdir -p /var/www/storage/logs && \
    chmod -R 775 /var/www/storage

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
