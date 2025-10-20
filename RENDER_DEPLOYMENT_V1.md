# Render Deployment Guide - Music Locker v1 (Custom PHP)

**Date:** 2025-10-21
**Project:** Music Locker PHP v1 → Render
**Team:** NaturalStupidity
**Platform:** Render.com with MySQL/PostgreSQL

---

## Table of Contents

1. [Quick Start](#quick-start)
2. [Prerequisites](#prerequisites)
3. [Step-by-Step Deployment](#step-by-step-deployment)
4. [Environment Configuration](#environment-configuration)
5. [Database Setup](#database-setup)
6. [Post-Deployment](#post-deployment)
7. [Monitoring & Troubleshooting](#monitoring--troubleshooting)
8. [Comparison: v1 vs v2 (Laravel)](#comparison-v1-vs-v2-laravel)

---

## Quick Start

**TL;DR:** Deploy in 5 minutes:

```bash
# 1. Ensure all files are pushed to GitHub
git add .
git commit -m "Prepare v1 for Render deployment"
git push origin main

# 2. Go to Render Dashboard
# 3. Click "New" → "Blueprint"
# 4. Connect your GitHub repository
# 5. Configure environment variables (see Environment Configuration)
# 6. Click "Deploy"
```

---

## Prerequisites

### Required Accounts
- ✅ [Render.com](https://render.com) account (free tier available)
- ✅ [GitHub](https://github.com) repository with code pushed
- ✅ Database (choose one):
  - MySQL/MariaDB (via Railway, Planetscale, etc.)
  - PostgreSQL (Supabase)

### Local Preparation
- ✅ Git repository initialized and pushed to GitHub
- ✅ `composer.json` and `composer.lock` present
- ✅ `Dockerfile` present (created by this guide)
- ✅ `docker/` folder with config files
- ✅ `.env.example` template present

### Files Included

```
MusicLocker-PHP/
├── Dockerfile                 # Docker build config
├── docker/
│   ├── apache.conf           # Apache webserver config
│   └── start.sh              # Startup script
├── .env.example              # Environment template
├── public/
│   └── index.php             # Entry point
├── src/
│   ├── Controllers/          # Application logic
│   ├── Models/               # Database models
│   ├── Services/             # Business logic
│   └── Utils/                # Helpers & utilities
├── config/
│   ├── app.php               # App config
│   ├── database.php          # Database config
│   └── spotify.php           # Spotify config
├── composer.json             # PHP dependencies
└── render.yaml               # Render blueprint
```

---

## Step-by-Step Deployment

### Step 1: Verify Repository Structure

```bash
# Ensure directory structure is correct
ls -la Dockerfile
ls -la docker/apache.conf
ls -la docker/start.sh
ls -la .env.example
ls -la public/index.php
ls -la composer.json
```

### Step 2: Choose Your Database

**Option A: MySQL (Recommended for v1)**

Use one of these services:
- [Railway MySQL](https://railway.app) - Easy, $5/month
- [PlanetScale](https://planetscale.com) - MySQL compatible, free tier
- [AWS RDS](https://aws.amazon.com/rds/) - Managed, paid

Get connection details:
- `DB_HOST` - Database hostname
- `DB_PORT` - Usually 3306
- `DB_DATABASE` - Database name
- `DB_USERNAME` - MySQL username
- `DB_PASSWORD` - MySQL password

**Option B: PostgreSQL (Supabase)**

1. Create Supabase project: https://supabase.com
2. Go to **Project Settings** → **Database**
3. Copy connection string or note:
   - `DB_HOST` - Connection string host
   - `DB_PORT` - Connection pooler port (6543)
   - `DB_DATABASE` - Usually `postgres`
   - `DB_USERNAME` - `postgres.YOUR_PROJECT_REF`
   - `DB_PASSWORD` - Your database password

### Step 3: Push to GitHub

```bash
cd /path/to/MusicLocker-PHP
git add .
git commit -m "Add Render deployment files for v1"
git push origin main
```

### Step 4: Create Render Service Using Blueprint

1. **Open Render Dashboard**: https://dashboard.render.com
2. **Click "New"** → **"Blueprint"**
3. **Select GitHub repository**: `your-github/MusicLocker-PHP`
4. **Name**: `music-locker-v1`
5. **Branch**: `main`
6. **Click "Create Blueprint"**

### Step 5: Configure Environment Variables

In Render Dashboard, set these variables:

#### Application Settings
```env
APP_NAME=Music Locker
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-service-name.onrender.com
APP_TIMEZONE=Asia/Manila
LOG_LEVEL=error
```

#### Database Configuration (MySQL)
```env
DB_CONNECTION=mysql
DB_HOST=your-mysql-host.com
DB_PORT=3306
DB_DATABASE=music_locker
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

**OR** Database Configuration (PostgreSQL/Supabase)
```env
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.YOUR_PROJECT_REF
DB_PASSWORD=YOUR_PASSWORD
DB_SSLMODE=require
```

#### Spotify Integration (Optional)
```env
SPOTIFY_CLIENT_ID=your-client-id
SPOTIFY_CLIENT_SECRET=your-client-secret
```

### Step 6: Deploy

1. **Review** the environment variables
2. **Click "Deploy"** button
3. **Monitor** build logs (takes 5-10 minutes)
4. **Check status** - Should show "Live" when complete

---

## Environment Configuration

### Required Variables

| Variable | Value | Notes |
|----------|-------|-------|
| `APP_NAME` | Music Locker | Application name |
| `APP_ENV` | production | Must be "production" |
| `APP_DEBUG` | false | Never true in production |
| `APP_URL` | https://your-app.onrender.com | Your Render URL |
| `APP_TIMEZONE` | Asia/Manila | Timezone for timestamps |
| `DB_CONNECTION` | mysql or pgsql | Database driver |
| `DB_HOST` | Database hostname | Your database host |
| `DB_PORT` | 3306 or 6543 | MySQL=3306, PG Pooler=6543 |
| `DB_DATABASE` | Database name | Your database name |
| `DB_USERNAME` | Database user | Your database username |
| `DB_PASSWORD` | Database password | Your database password |

### Optional Variables

```env
# Spotify (if using Spotify features)
SPOTIFY_CLIENT_ID=YOUR_ID
SPOTIFY_CLIENT_SECRET=YOUR_SECRET

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_USERNAME=apikey
MAIL_PASSWORD=YOUR_KEY

# Logging
LOG_LEVEL=error
LOG_PATH=storage/logs

# Session
SESSION_LIFETIME=120
COOKIE_SECURE=true
COOKIE_HTTPONLY=true
```

### PostgreSQL SSL Mode (Supabase)

Add this if using Supabase:
```env
DB_SSLMODE=require
```

---

## Database Setup

### Create Initial Database

**For MySQL:**
```sql
CREATE DATABASE music_locker;
USE music_locker;
-- Tables will be created on first app startup
```

**For PostgreSQL (Supabase):**
- Database already created by Supabase
- Use connection pooler for best performance

### Verify Connection

After deployment, in Render Shell:

```bash
# Test database connection
php -r "
\$host = getenv('DB_HOST');
\$user = getenv('DB_USERNAME');
\$pass = getenv('DB_PASSWORD');
\$db = getenv('DB_DATABASE');
\$conn = new PDO('mysql:host=\$host;\$db', \$user, \$pass);
echo 'Connected!';
"
```

---

## Post-Deployment

### 1. Verify Deployment

```bash
# Check the app is running
curl https://your-service-name.onrender.com

# Check health endpoint
curl https://your-service-name.onrender.com/health
```

### 2. Access Application

1. **Get your URL** from Render Dashboard
2. **Format**: `https://your-service-name.onrender.com`
3. **Login** with your credentials
4. **Test features**: Music, playlists, admin dashboard

### 3. Create Admin Account (If Needed)

Via browser at login page or if migrations ran, create user in database:

```sql
INSERT INTO users (
    first_name, last_name, email, password, role, status, created_at, updated_at
) VALUES (
    'Admin', 'User', 'admin@example.com',
    MD5('password'),  -- Use proper password hashing in production!
    'admin', 'active', NOW(), NOW()
);
```

**Better:** Use application registration form on `/register`

### 4. Setup Custom Domain (Optional)

1. **In Render Dashboard**:
   - Go to your service
   - Settings → Custom Domains
   - Add your domain

2. **Update DNS**:
   - Follow Render's DNS setup instructions
   - Add CNAME record

3. **Update APP_URL**:
   - Set `APP_URL=https://yourdomain.com`

---

## Monitoring & Troubleshooting

### View Logs

**Render Dashboard:**
1. Select your service
2. Click "Logs" tab
3. View real-time logs

### Common Issues

#### Issue: Build Fails

**Symptoms:** Red X, build logs show error

**Solutions:**
1. Check Docker syntax: `docker build -t test .`
2. Verify Composer: `composer validate`
3. Check file permissions
4. View full logs in Render Dashboard

#### Issue: Database Connection Error

**Symptoms:** "Unable to connect to database" or "SQLSTATE[HY000]"

**Solutions:**
1. Verify database is running
2. Check credentials in environment variables
3. For Supabase, ensure `DB_SSLMODE=require`
4. Test from Render Shell:
   ```bash
   php -r "
   \$host = getenv('DB_HOST');
   \$user = getenv('DB_USERNAME');
   \$pass = getenv('DB_PASSWORD');
   try {
       \$pdo = new PDO('mysql:host=\$host', \$user, \$pass);
       echo 'Connected!';
   } catch(Exception \$e) {
       echo 'Error: ' . \$e->getMessage();
   }
   "
   ```

#### Issue: 404 or Routing Errors

**Symptoms:** Routes not working, 404 errors

**Solutions:**
1. Verify Apache `mod_rewrite` is enabled
2. Check `.htaccess` in public folder exists
3. Check `DocumentRoot` in apache.conf points to `/var/www/html/public`
4. Restart service: Render Dashboard → Restart

#### Issue: Permission Denied Errors

**Symptoms:** "Permission denied" in logs when writing files

**Solutions:**
1. Check `docker/start.sh` sets correct permissions
2. Ensure `storage/` and `logs/` directories are writable
3. Verify ownership: `www-data:www-data`

#### Issue: Timezone Showing Wrong Time

**Symptoms:** Times are off by several hours

**Solutions:**
1. Verify `APP_TIMEZONE=Asia/Manila` is set
2. Check `public/index.php` line 21 sets timezone
3. Restart service after changing timezone
4. Database should store UTC (correct)

### Debug Commands

Access Render Shell:

```bash
# In Render Dashboard → Your Service → Shell

# Check environment variables
env | grep APP_
env | grep DB_

# Test PHP
php -v
php -i | grep timezone

# Check Apache
apache2ctl -M | grep rewrite
apache2ctl -S

# View logs
tail -f /var/log/apache2/error.log
tail -f /var/log/apache2/access.log

# Test file permissions
ls -la /var/www/html/storage/
ls -la /var/www/html/

# Check disk space
df -h
```

---

## Comparison: v1 vs v2 (Laravel)

| Feature | v1 (Custom) | v2 (Laravel) |
|---------|-------------|-------------|
| **Framework** | Custom PHP | Laravel 11 |
| **Entry Point** | `public/index.php` | `public/index.php` (Laravel) |
| **Docker** | Apache + PHP 8.2 | Apache + PHP 8.2 |
| **Database** | MySQL/PostgreSQL | PostgreSQL (Supabase) |
| **Performance** | Good | Good (with caching) |
| **Maintainability** | Manual | Framework helpers |
| **Testing** | Manual setup | PHPUnit built-in |
| **Routing** | Manual switch/case | Routes file |
| **Migrations** | Manual | Artisan commands |
| **Deployment** | This guide | RENDER_DEPLOYMENT_COMPLETE.md |

**Recommendation:**
- **v1** - Good for legacy/custom logic, manual control
- **v2** - Better for new features, team collaboration, scaling

---

## Performance & Scaling

### Optimization

**Apache:**
- Compression enabled (DEFLATE)
- Caching headers set
- Long-term cache for assets

**PHP:**
- Composer autoloader optimized
- Error reporting minimal in production
- Sessions cached

### Monitoring

**In Render Dashboard:**
- CPU usage
- Memory usage
- Response times
- Build history

**Recommendations:**
- Start with Starter plan ($7/month)
- Monitor for 1-2 weeks
- Upgrade to Starter Plus ($25/month) if needed
- Consider Redis cache if traffic grows

### Scaling

**Horizontal Scaling:**
```yaml
# In render.yaml
maxInstances: 3
minInstances: 1
```

**Vertical Scaling:**
- Upgrade Render plan: Starter → Starter Plus → Standard
- Allocate more CPU/RAM per instance

---

## Security Checklist

- ✅ `APP_DEBUG=false` (production)
- ✅ Use environment variables (not hardcoded)
- ✅ `DB_SSLMODE=require` (for Supabase)
- ✅ Enable HTTPS (Render auto-enforces)
- ✅ Set strong database password
- ✅ Use secure session cookies
- ✅ Disable directory listing
- ✅ Set correct file permissions
- ✅ Regular backups enabled
- ✅ Rotate API keys regularly

---

## Maintenance & Updates

### Updating Code

```bash
# Make changes locally
git add .
git commit -m "Update feature"
git push origin main

# Render automatically redeploys (if autoDeploy: true)
```

### Backup Database

**For MySQL:**
- Use hosting provider's backup feature
- Or schedule manual backups

**For Supabase:**
- Supabase Dashboard → Backups
- Enable automatic backups
- Manual export available

---

## Support & Resources

### Documentation
- [Render Documentation](https://render.com/docs)
- [PHP Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [Apache Configuration](https://httpd.apache.org/docs/2.4/)
- [Composer Documentation](https://getcomposer.org/doc/)

### Team Resources
- **Repository**: GitHub
- **Team**: NaturalStupidity
- **Project**: Music Locker PHP v1 Migration
- **Status**: Ready for Render Deployment ✅

---

## Quick Reference

### Environment Variables (Copy-Paste)

```env
# Application
APP_NAME=Music Locker
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-service.onrender.com
APP_TIMEZONE=Asia/Manila

# Database (MySQL)
DB_CONNECTION=mysql
DB_HOST=your-host.com
DB_PORT=3306
DB_DATABASE=music_locker
DB_USERNAME=your-user
DB_PASSWORD=your-password

# Spotify
SPOTIFY_CLIENT_ID=your-id
SPOTIFY_CLIENT_SECRET=your-secret

# Logging
LOG_LEVEL=error
```

### Common Commands

```bash
# Check Docker build
docker build -t music-locker-v1 .

# Test locally
docker run -p 8000:80 music-locker-v1

# Validate composer
composer validate

# Check syntax
php -l public/index.php
```

---

## Summary

✅ **Deployment Ready**
- Docker configuration complete
- Environment template provided
- Startup script configured
- Apache properly configured

✅ **Production Ready**
- Security headers enabled
- Compression configured
- Error handling in place
- Logging configured

✅ **Both v1 and v2 Ready**
- v1: Custom PHP (this guide)
- v2: Laravel (RENDER_DEPLOYMENT_COMPLETE.md)
- Can deploy both simultaneously to Render

**Your Music Locker v1 application is ready for production deployment on Render!** 🚀

---

*Last Updated: 2025-10-21*
*Compatible with PHP 8.2 and Docker*
