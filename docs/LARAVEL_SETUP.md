# Laravel Setup Guide

**Date Created**: September 23, 2025  
**Project**: Music Locker PHP Migration  
**Team**: NaturalStupidity

## Overview

This guide provides detailed instructions for setting up the Laravel migration project within the consolidated directory structure. The Laravel project is located in the `/laravel` subdirectory and will be deployed to Render with Supabase PostgreSQL.

## Prerequisites

### Local Development
- PHP 8.2+ with required extensions
- Composer
- Node.js 18+ (for Vite asset compilation)
- PostgreSQL client libraries (for Supabase connection)

### Required PHP Extensions
```bash
# Check if extensions are installed
php -m | grep -E "(pdo_pgsql|pgsql|curl|json|mbstring|openssl|pdo|tokenizer|xml|ctype|fileinfo|bcmath)"
```

If missing, install via XAMPP or enable in php.ini:
```ini
extension=pdo_pgsql
extension=pgsql
extension=curl
extension=json
extension=mbstring
extension=openssl
extension=pdo
extension=tokenizer
extension=xml
extension=ctype
extension=fileinfo
extension=bcmath
```

## Initial Setup

### 1. Copy Laravel Project

If you have the Laravel project in XAMPP, copy it to the laravel subdirectory:

**Windows PowerShell:**
```powershell
Copy-Item -Path "C:\xampp\htdocs\MusicLocker-PHP" -Destination "C:\Users\shawn\Desktop\MusicLocker-PHP\laravel" -Recurse
```

**Windows Command Prompt:**
```cmd
xcopy "C:\xampp\htdocs\MusicLocker-PHP" "C:\Users\shawn\Desktop\MusicLocker-PHP\laravel" /E /I
```

### 2. Install Dependencies

```bash
cd laravel
composer install
npm install
```

### 3. Environment Configuration

```bash
# Copy environment template
cp .env.example .env

# Generate application key
php artisan key:generate
```

## Database Configuration

### Supabase Setup

1. **Create Supabase Project**
   - Go to [Supabase Dashboard](https://supabase.com/dashboard)
   - Create new project
   - Note your project credentials

2. **Get Connection Details**
   - Project URL: `https://your-project-id.supabase.co`
   - Database Password: (set during project creation)
   - Database Name: `postgres` (default)
   - Username: `postgres` (default)
   - Host: `db.your-project-id.supabase.co`
   - Port: `5432`

### Local Environment (.env)

```env
# Application
APP_NAME="Music Locker"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database - Supabase PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=db.your-project-id.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-password
DB_SSLMODE=require

# Cache & Session (for Render deployment)
CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Spotify API
SPOTIFY_CLIENT_ID=356702eb81d0499381fcf5222ab757fb
SPOTIFY_CLIENT_SECRET=3a826c32f5dc41e9939b4ec3229a5647

# Mail (for password reset)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@musiclocker.local"
MAIL_FROM_NAME="${APP_NAME}"

# Logging
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug
```

### Production Environment (.env for Render)

```env
# Application
APP_NAME="Music Locker"
APP_ENV=production
APP_KEY=base64:your-production-key
APP_DEBUG=false
APP_URL=https://your-render-app.onrender.com

# Database - Supabase PostgreSQL (same as local)
DB_CONNECTION=pgsql
DB_HOST=db.your-project-id.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-password
DB_SSLMODE=require

# Cache & Session (required for Render)
CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Spotify API
SPOTIFY_CLIENT_ID=356702eb81d0499381fcf5222ab757fb
SPOTIFY_CLIENT_SECRET=3a826c32f5dc41e9939b4ec3229a5647

# Mail (configure for production)
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

## Running the Application

### Development Server

```bash
cd laravel
php artisan serve
```

Visit: `http://localhost:8000`

### With XAMPP Virtual Host (Optional)

Create a virtual host for easier development:

**1. Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:**
```apache
<VirtualHost *:80>
    DocumentRoot "C:/Users/shawn/Desktop/MusicLocker-PHP/laravel/public"
    ServerName musiclocker.local
    <Directory "C:/Users/shawn/Desktop/MusicLocker-PHP/laravel/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**2. Edit `C:\Windows\System32\drivers\etc\hosts`:**
```
127.0.0.1 musiclocker.local
```

**3. Restart Apache and visit:** `http://musiclocker.local`

## Asset Compilation

### Development (with hot reload)
```bash
cd laravel
npm run dev
```

### Production Build
```bash
cd laravel
npm run build
```

## Database Migrations

### Initial Setup
```bash
cd laravel

# Run migrations (will create tables from database/schema.sql conversion)
php artisan migrate

# Seed default data (if seeders are created)
php artisan db:seed
```

### Testing Database Connection
```bash
php artisan tinker
# In tinker:
DB::connection()->getPdo();
```

## Troubleshooting

### Common Issues

**1. PostgreSQL Connection Error**
```
SQLSTATE[08006] [7] could not connect to server
```
- Verify Supabase credentials
- Check if `pdo_pgsql` extension is enabled
- Ensure SSL mode is set to `require`

**2. Laravel Key Missing**
```
No application encryption key has been specified
```
```bash
php artisan key:generate
```

**3. Storage Permissions**
```bash
# Fix storage permissions
php artisan storage:link
```

**4. Composer Autoload Issues**
```bash
composer dump-autoload
```

### Debugging

**Enable Debug Mode:**
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

**View Logs:**
```bash
tail -f storage/logs/laravel.log
```

**Check Configuration:**
```bash
php artisan config:show
```

## Deployment Preparation

### Render.com Configuration

**1. Build Command:**
```bash
cd laravel && composer install --no-dev --optimize-autoloader && npm ci && npm run build
```

**2. Start Command:**
```bash
cd laravel && php artisan serve --host=0.0.0.0 --port=$PORT
```

**3. Environment Variables:**
Set all production `.env` variables in Render dashboard.

**4. Build Directory:**
Set to `laravel` in Render settings.

### Pre-Deployment Checklist

- [ ] Environment variables configured
- [ ] Database migrations tested
- [ ] Asset compilation working
- [ ] Spotify API credentials valid
- [ ] Mail configuration tested
- [ ] SSL/HTTPS redirects configured

## Security Considerations

### Production Security
- Set `APP_DEBUG=false`
- Use strong `APP_KEY`
- Enable HTTPS redirects
- Configure proper CORS settings
- Use environment variables for secrets
- Enable Laravel's security headers

### Spotify API Security
- Never commit API credentials
- Use environment variables
- Implement proper rate limiting
- Validate all API responses

## Next Steps

After completing this setup:

1. **Run Database Migrations**: Convert `database/schema.sql` to Laravel migrations
2. **Create Eloquent Models**: Build models with relationships
3. **Implement Controllers**: Migrate custom PHP controllers to Laravel
4. **Convert Views**: Transform PHP templates to Blade
5. **Test Functionality**: Ensure all features work correctly
6. **Deploy to Render**: Configure production deployment

For detailed migration steps, see [LARAVEL_MIGRATION_PLAN.md](LARAVEL_MIGRATION_PLAN.md).

---

**Note**: This setup guide assumes you're migrating from the existing custom PHP implementation. Keep both projects running in parallel during migration for testing and comparison.
