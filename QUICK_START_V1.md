# 🚀 Music Locker v1 - Quick Render Deployment

Deploy your custom PHP app to Render in **5 minutes** - just like v2 (Laravel)!

---

## What You Get

✅ Single web service on Render (like v2)
✅ Nginx + PHP-FPM (Alpine Linux - lightweight)
✅ Auto-scaling support
✅ Auto-redeploy on GitHub push
✅ Health checks included
✅ Production optimized

---

## 1️⃣ Choose Your Database

### Option A: MySQL (Recommended for v1)

**Easiest Setup:** Use [Railway](https://railway.app)
- Create account at railway.app
- Create MySQL database
- Get connection info:
  ```
  DB_HOST=railway-host.com
  DB_PORT=3306
  DB_DATABASE=music_locker
  DB_USERNAME=root
  DB_PASSWORD=your-password
  ```

### Option B: PostgreSQL

**Use Supabase:** (Already working with v2)
- Create account at supabase.com
- Create project
- Get connection info:
  ```
  DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
  DB_PORT=6543
  DB_DATABASE=postgres
  DB_USERNAME=postgres.your-ref
  DB_PASSWORD=your-password
  ```

---

## 2️⃣ Get Spotify Credentials

1. Go to https://developer.spotify.com
2. Create or login to your app
3. Get:
   - `SPOTIFY_CLIENT_ID`
   - `SPOTIFY_CLIENT_SECRET`

---

## 3️⃣ Push Code to GitHub

```bash
cd /path/to/MusicLocker-PHP

git add .
git commit -m "Deploy v1 to Render: Updated Docker, Nginx, and supervisor config"
git push origin main
```

---

## 4️⃣ Create Render Web Service

1. Go to **https://dashboard.render.com**
2. Click **"New"** → **"Web Service"**
3. Select your GitHub repository
4. Fill in:
   - **Name:** `music-locker-v1`
   - **Runtime:** `Docker`
   - **Region:** `Singapore` (or your choice)
   - **Plan:** `Starter` ($7/month)

5. Click **"Create Web Service"**

---

## 5️⃣ Add Environment Variables

In Render Dashboard, click your service → **"Environment"**

Add these variables:

```env
# Application
APP_NAME=Music Locker v1
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Manila
APP_URL=https://your-service.onrender.com  # Will show after deploy

# Database (MySQL)
DB_CONNECTION=mysql
DB_HOST=your-railway-host.com
DB_PORT=3306
DB_DATABASE=music_locker
DB_USERNAME=root
DB_PASSWORD=your-password

# OR Database (PostgreSQL/Supabase)
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.YOUR_REF
DB_PASSWORD=your-password
DB_SSLMODE=require

# Spotify
SPOTIFY_CLIENT_ID=your-id-here
SPOTIFY_CLIENT_SECRET=your-secret-here

# Logging
LOG_LEVEL=error
```

**Important:** Copy your actual values, don't use placeholders!

---

## 6️⃣ Deploy!

1. Click **"Deploy"** button in Render Dashboard
2. Watch the logs scroll by
3. Wait for **"Live"** status (green) ✅
4. Takes about **5-10 minutes**

---

## 7️⃣ Verify It Works

### In Browser
```
https://your-service-name.onrender.com
```

Should show:
- ✅ Login page loads
- ✅ No errors
- ✅ Can login

### In Terminal
```bash
# Should return HTML (no errors)
curl https://your-service-name.onrender.com

# Should show green checkmark
curl -I https://your-service-name.onrender.com
```

---

## 📋 Complete Environment Variables Reference

### Required

| Variable | Example | Notes |
|----------|---------|-------|
| `DB_HOST` | `railway-host.com` | Your database host |
| `DB_PORT` | `3306` | MySQL: 3306, PostgreSQL: 5432 or 6543 (pooler) |
| `DB_DATABASE` | `music_locker` | Your database name |
| `DB_USERNAME` | `root` | Your database user |
| `DB_PASSWORD` | `your-pass` | Your database password |
| `SPOTIFY_CLIENT_ID` | `356702eb...` | From Spotify Dashboard |
| `SPOTIFY_CLIENT_SECRET` | `3a826c32...` | From Spotify Dashboard |

### Recommended

| Variable | Value | Notes |
|----------|-------|-------|
| `APP_ENV` | `production` | Always production |
| `APP_DEBUG` | `false` | Never true in production |
| `APP_TIMEZONE` | `Asia/Manila` | Or your timezone |
| `DB_CONNECTION` | `mysql` or `pgsql` | Your database type |
| `LOG_LEVEL` | `error` | Production logging level |

### Optional (PostgreSQL only)

| Variable | Value | Notes |
|----------|-------|-------|
| `DB_SSLMODE` | `require` | Required for Supabase |

---

## 🔍 Monitoring Your Service

### Check Status
```
Render Dashboard → Service → Status
```
Should show **"Live"** (green indicator)

### View Logs
```
Render Dashboard → Service → Logs
```
Look for:
- ✅ "Starting Music Locker v1"
- ✅ "Database is ready"
- ✅ "nginx and php-fpm via supervisor"

### Test Database Connection
```
Render Dashboard → Service → Shell
```

Run:
```bash
php -r "
try {
  \$pdo = new PDO('mysql:host=YOUR_HOST;dbname=YOUR_DB', 'YOUR_USER', 'YOUR_PASS');
  echo 'Database connected!';
} catch(Exception \$e) {
  echo 'Error: ' . \$e->getMessage();
}
"
```

Should output: `Database connected!`

---

## ❌ Troubleshooting

### "Build Failed"
1. Check Dockerfile syntax
2. Check `composer.json` is valid
3. Look at full error in Render logs

**Fix:**
```bash
# Test locally
docker build -t test .
composer validate
```

### "504 Gateway Error"
1. Service not started yet (wait 5-10 min)
2. PHP or Nginx crashed
3. Check logs for errors

**Fix:**
```
Render Dashboard → Logs → Look for errors
```

### "Database Connection Error"
1. Wrong credentials
2. Database not running
3. Network/firewall issue

**Fix:**
- Verify DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD
- For PostgreSQL, add DB_SSLMODE=require
- Restart service: Click "Restart" button

### "App loads but no features work"
1. Database not connected
2. Permissions issue
3. .env not loaded

**Fix:**
```bash
# Via Shell, check .env
cat /var/www/.env

# Check permissions
ls -la /var/www/storage/

# Restart service
# In Render Dashboard, click "Restart"
```

---

## 📊 After Deployment

### Next Steps

1. **Test the App**
   - [ ] Login works
   - [ ] Music page loads
   - [ ] Spotify search works
   - [ ] Can add music entries

2. **Setup Custom Domain (Optional)**
   - Render Dashboard → Settings → Custom Domains
   - Point your domain to Render
   - Update APP_URL to your domain

3. **Enable Autoscaling (Optional)**
   - Render Dashboard → Settings → Scaling
   - Max instances: 2 (default)
   - Min instances: 1 (default)

4. **Setup Backups (If Using Database)**
   - Railway: Enable automatic backups
   - Supabase: Already enabled

---

## 🎯 Architecture

```
Your Code (GitHub)
      ↓
Render (Docker Build)
      ↓
Docker Image:
  - Alpine Linux (lightweight)
  - PHP 8.2 (FPM)
  - Nginx (web server)
  - Supervisor (process manager)
      ↓
Web Service:
  - https://your-service.onrender.com
  - Auto-scaling: 1-2 instances
  - Auto-deploy: On GitHub push
      ↓
Database:
  - MySQL (Railway) or PostgreSQL (Supabase)
  - Your data persists
  - Can backup anytime
```

---

## 💡 Pro Tips

### 1. Auto-Deploy on Push
Your service auto-deploys when you push to GitHub:
```bash
git push origin main
# Render automatically redeploys! ✅
```

### 2. View Real-Time Logs
```
Render Dashboard → Service → Logs
# Tail logs in real-time
# Great for debugging
```

### 3. Use Render Shell
```
Render Dashboard → Service → Shell
# SSH into your running container
# Debug, inspect files, run PHP commands
```

### 4. Monitor Performance
```
Render Dashboard → Service → Metrics
# CPU usage
# Memory usage
# Response times
```

---

## 🆘 Need Help?

### For v1 (This App)
- `QUICK_START_V1.md` ← You are here
- `RENDER_DEPLOYMENT_V1.md` ← Full detailed guide

### For v2 (Laravel)
- `RENDER_DEPLOYMENT_COMPLETE.md` ← v2 guide

### For Both Together
- `DEPLOY_BOTH_VERSIONS.md` ← Side-by-side guide

### Render Docs
- https://render.com/docs

---

## ✅ Success Checklist

Before considering deployment complete:

- [ ] Repository pushed to GitHub
- [ ] Database created and credentials ready
- [ ] Spotify credentials obtained
- [ ] All environment variables added to Render
- [ ] "Deploy" clicked in Render Dashboard
- [ ] Service shows "Live" (green) ✅
- [ ] App loads in browser
- [ ] Login works
- [ ] Spotify search works
- [ ] No errors in Render logs

---

## 🎉 Done!

Your Music Locker v1 is now running on Render, just like v2!

**Your URL:**
```
https://your-service-name.onrender.com
```

Share this link with users! 🚀

---

## 📞 What's Different from v1 Local?

| Feature | Local | Render |
|---------|-------|--------|
| **Server** | PHP built-in | Nginx + PHP-FPM |
| **Process Manager** | None | Supervisor |
| **Database** | Local | Remote (Railway/Supabase) |
| **URL** | localhost:8888 | https://your-service.onrender.com |
| **SSL/HTTPS** | No | Yes ✅ |
| **Uptime** | When you run it | 24/7 ✅ |
| **Scale** | 1 instance | 1-2 instances (auto) |

---

*Last Updated: 2025-10-21*
*Production-ready, tested, and optimized!*
