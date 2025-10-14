# Render Deployment Guide for Music Locker Laravel

**Date Created**: October 14, 2025  
**Project**: Music Locker PHP → Laravel Migration  
**Team**: NaturalStupidity  
**Deployment Platform**: Render.com

## Overview

This guide provides step-by-step instructions for deploying the Music Locker Laravel application to Render.com using Docker with Nginx + PHP-FPM, connected to Supabase PostgreSQL.

## Prerequisites

### Required Accounts
- [Render.com](https://render.com) account
- [Supabase](https://supabase.com) project with PostgreSQL database
- GitHub repository with the Music Locker code

### Local Requirements
- Git repository pushed to GitHub
- Laravel application in `/laravel` subdirectory
- Docker configuration files (included)

## Deployment Files

### Docker Configuration
- `laravel/Dockerfile` - Multi-stage Docker build
- `laravel/docker/nginx.conf` - Nginx web server configuration
- `laravel/docker/supervisord.conf` - Process manager configuration
- `laravel/docker/start.sh` - Startup script with migrations

### Render Configuration
- `render.yaml` - Render Blueprint (Infrastructure as Code)

## Step-by-Step Deployment

### 1. Prepare Repository

Ensure your repository structure looks like this:
```
MusicLocker-PHP/
├── laravel/                    # Laravel application
│   ├── Dockerfile             # Docker configuration
│   ├── docker/
│   │   ├── nginx.conf         # Nginx config
│   │   ├── supervisord.conf   # Supervisor config
│   │   └── start.sh           # Startup script
│   ├── app/                   # Laravel app
│   ├── public/                # Web root
│   └── ...
├── render.yaml                # Render blueprint
└── docs/
    └── RENDER_DEPLOYMENT.md   # This file
```

### 2. Push to GitHub

```bash
git add .
git commit -m "Add Render deployment configuration"
git push origin main
```

### 3. Create Render Service

#### Option A: Using Render Blueprint (Recommended)
1. Go to [Render Dashboard](https://dashboard.render.com)
2. Click "New" → "Blueprint"
3. Connect your GitHub repository
4. Render will automatically detect `render.yaml`
5. Review the configuration and deploy

#### Option B: Manual Service Creation
1. Go to [Render Dashboard](https://dashboard.render.com)
2. Click "New" → "Web Service"
3. Connect your GitHub repository
4. Configure the service:
   - **Name**: `music-locker-laravel`
   - **Runtime**: `Docker`
   - **Root Directory**: `laravel`
   - **Dockerfile Path**: `./Dockerfile`
   - **Auto-Deploy**: `Yes`

### 4. Configure Environment Variables

Set these environment variables in the Render Dashboard:

#### Application Settings
```env
APP_NAME="Music Locker"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com
APP_KEY=base64:your-generated-key-here
```

**Generate APP_KEY:**
```bash
cd laravel
php artisan key:generate --show
```

#### Database Configuration (Supabase)
```env
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.xkitrpslmahzsniupqpz
DB_PASSWORD=ry.D@cayana890.
DB_SSLMODE=require
```

#### Session & Cache
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
```

#### Spotify API
```env
SPOTIFY_CLIENT_ID=356702eb81d0499381fcf5222ab757fb
SPOTIFY_CLIENT_SECRET=3a826c32f5dc41e9939b4ec3229a5647
```

#### Mail Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Music Locker"
```

#### Music Locker Specific
```env
MUSIC_LOCKER_MAX_ENTRIES_PER_USER=10000
MUSIC_LOCKER_SESSION_TIMEOUT=3600
```

#### Logging
```env
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 5. Deploy

1. Click "Deploy" in the Render Dashboard
2. Monitor the build logs
3. Wait for deployment to complete (usually 5-10 minutes)

## Build Process

The Docker build process includes:

1. **Stage 1: Builder**
   - Install PHP 8.2 + extensions (pdo_pgsql, mbstring, etc.)
   - Install Node.js for asset compilation
   - Run `composer install --no-dev --optimize-autoloader`
   - Run `npm ci && npm run build`
   - Cache Laravel configuration, routes, and views

2. **Stage 2: Production**
   - Copy built application from builder stage
   - Configure Nginx + PHP-FPM with Supervisor
   - Set proper file permissions
   - Health check endpoint at `/health`

## Post-Deployment

### 1. Verify Deployment
- Visit your Render app URL
- Check health endpoint: `https://your-app.onrender.com/health`
- Verify database connection works

### 2. Run Initial Setup (if needed)
The startup script automatically:
- Runs database migrations (`php artisan migrate --force`)
- Clears and caches configuration
- Creates storage symlink
- Sets file permissions

### 3. Configure Custom Domain (Optional)
1. In Render Dashboard, go to your service
2. Click "Settings" → "Custom Domains"
3. Add your domain (e.g., `musiclocker.app`)
4. Update DNS records as instructed
5. Update `APP_URL` environment variable

## Monitoring & Maintenance

### Logs
- **Application Logs**: Available in Render Dashboard → Logs
- **Error Tracking**: Check Laravel logs for errors
- **Performance**: Monitor response times and resource usage

### Scaling
- **Horizontal**: Increase number of instances in Render Dashboard
- **Vertical**: Upgrade to higher plan (Starter Plus, Standard, Pro)

### Updates
- Push code changes to GitHub
- Render automatically rebuilds and deploys
- Zero-downtime deployments with health checks

## Troubleshooting

### Common Issues

#### 1. Build Failures
```bash
# Check Dockerfile syntax
docker build -t music-locker .

# Verify composer.json and package.json
composer validate
npm audit
```

#### 2. Database Connection Issues
- Verify Supabase credentials in environment variables
- Check if Supabase project is active (not paused)
- Ensure `DB_SSLMODE=require` is set

#### 3. Asset Loading Issues
- Verify `APP_URL` matches your Render domain
- Check if `npm run build` completed successfully
- Ensure Vite configuration is correct

#### 4. Permission Errors
- Check startup script logs
- Verify file permissions in Docker container
- Ensure storage directories are writable

### Debug Commands

Access your Render service shell:
```bash
# In Render Dashboard → Shell
php artisan tinker
DB::connection()->getPdo();  # Test DB connection
php artisan config:show     # Show configuration
php artisan route:list      # List routes
```

## Security Considerations

### Production Security
- `APP_DEBUG=false` (never true in production)
- Strong `APP_KEY` (generate new for production)
- HTTPS enforced (automatic with Render)
- Database SSL required (`DB_SSLMODE=require`)
- Secure headers configured in Nginx

### Environment Variables
- Never commit sensitive values to Git
- Use Render's encrypted environment variables
- Rotate API keys and passwords regularly

## Performance Optimization

### Caching
- Configuration cached (`php artisan config:cache`)
- Routes cached (`php artisan route:cache`)
- Views cached (`php artisan view:cache`)
- Database sessions for horizontal scaling

### Assets
- Vite builds optimized production assets
- Nginx serves static files with long-term caching
- Gzip compression enabled

## Cost Optimization

### Render Plans
- **Starter ($7/month)**: Good for development/testing
- **Starter Plus ($25/month)**: Recommended for production
- **Standard ($85/month)**: High traffic applications

### Resource Usage
- Monitor CPU and memory usage
- Optimize database queries
- Use caching effectively
- Consider CDN for static assets

## Support & Resources

### Documentation
- [Render Documentation](https://render.com/docs)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Supabase Documentation](https://supabase.com/docs)

### Team Contact
- **Team**: NaturalStupidity
- **Project**: Music Locker PHP Migration
- **Repository**: GitHub repository URL

---

**Note**: This deployment configuration is optimized for the Music Locker application with Supabase PostgreSQL. Adjust environment variables and scaling based on your specific requirements.
