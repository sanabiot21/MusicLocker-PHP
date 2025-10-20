# Local Deployment Guide - Music Locker Laravel

**Status**: Ready for Local Deployment ✅
**Date**: October 17, 2025
**Framework**: Laravel 11
**Database**: PostgreSQL (Supabase)
**Environment**: Local Development

---

## Phase Integration Review ✅

### All Phases Complement Each Other Perfectly

#### Phase 1: Database & Models ✅
- 14 PostgreSQL migrations created and tested
- Eloquent models with proper relationships
- Supabase database configured
- **Complements**: Used by all subsequent phases

#### Phase 2: API Development ✅
- 5 API controllers with 27 endpoints
- Laravel Sanctum for API authentication
- API Resources for data transformation
- Form Requests for validation
- SpotifyService integration
- **Complements**: Phase 3 and 4 use these endpoints; Admin panel uses API for AJAX

#### Phase 3: Frontend Views & Authentication ✅
- 18 Blade views with dark-techno theme
- 9 web controllers (web + auth)
- Session-based authentication
- Profile management
- Music/Playlist management (web interface)
- **Complements**: Admin panel (Phase 4) uses same layout and styling

#### Phase 4: Admin Panel ✅
- 6 admin views with consistent design
- 1 admin controller with 11 methods
- Admin middleware for access control
- User management, system monitoring, settings
- Password reset approval workflow
- **Fixes Implemented**:
  - ✅ Role promotion restricted to primary admin (ID 1)
  - ✅ resetUserPassword() supports both JSON and redirect responses
  - ✅ userList() pagination preserves search filters with `withQueryString()`
  - ✅ updateSettings() has proper validation
  - ✅ full_name accessor used correctly throughout

---

## Pre-Deployment Checklist

### System Requirements
- [ ] PHP 8.2+ installed
- [ ] Composer installed
- [ ] PostgreSQL client tools (psql) installed
- [ ] Node.js & npm installed
- [ ] Git installed

### Environment Setup
- [ ] Supabase account created
- [ ] PostgreSQL database created on Supabase
- [ ] Connection credentials available
- [ ] .env file created (see setup steps below)

---

## Step-by-Step Local Deployment

### Step 1: Clone/Navigate to Project

```bash
cd C:\Users\shawn\Desktop\MusicLocker-PHP\laravel
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

**Expected Output**: Should complete without errors, ~200 packages installed

### Step 3: Install Node Dependencies

```bash
npm install
```

**Expected Output**: Node modules installed, package-lock.json created

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

**Expected Output**: "Application key set successfully"

### Step 5: Create .env File from Example

```bash
cp .env.example .env
```

Then update the following settings in `.env`:

```env
# Application
APP_NAME=MusicLocker
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration (Supabase PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=your-project.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your_password
DB_SSLMODE=require

# Spotify Configuration
SPOTIFY_CLIENT_ID=your_client_id
SPOTIFY_CLIENT_SECRET=your_client_secret
SPOTIFY_REDIRECT_URI=http://localhost:8000/auth/spotify/callback

# Session & Cache
SESSION_DRIVER=file
CACHE_DRIVER=file

# Mail (optional, skip for local)
MAIL_DRIVER=log
```

### Step 6: Verify Supabase Connection

Create a test file: `routes/test-db.php`

```php
Route::get('/test-db', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        return 'Database connection successful!';
    } catch (\Exception $e) {
        return 'Database connection failed: ' . $e->getMessage();
    }
});
```

Test with:
```bash
curl http://localhost:8000/test-db
```

### Step 7: Run Migrations

```bash
php artisan migrate
```

**Expected Output**:
```
Migrating: 2025_10_15_000001_create_users_table
Migrated:  2025_10_15_000001_create_users_table (xxx ms)
[... 13 more migrations ...]
Migrated:  2025_10_15_000014_seed_default_data
```

**Verify**: Check Supabase dashboard → SQL Editor → Query all tables should show data

### Step 8: Build Frontend Assets

```bash
npm run dev
```

For production-like testing:
```bash
npm run build
```

### Step 9: Create First Admin User (if not seeded)

```bash
php artisan tinker
```

Then in Tinker:
```php
$user = \App\Models\User::create([
    'first_name' => 'Admin',
    'last_name' => 'User',
    'email' => 'admin@musiclocker.local',
    'password' => \Illuminate\Support\Facades\Hash::make('Admin123!'),
    'role' => 'admin',
    'status' => 'active'
]);
exit;
```

### Step 10: Start Development Server

**Terminal 1** - Laravel Server:
```bash
php artisan serve
```

**Terminal 2** - Asset Watcher (optional but recommended):
```bash
npm run dev
```

---

## Testing the Deployment

### 1. Test Web Access

**Landing Page**:
```
http://localhost:8000
```

Expected: Beautiful dark-themed landing page with "Get Started" and "Learn More" buttons

### 2. Test Registration

- Click "Create Account" or go to `http://localhost:8000/register`
- Fill form: First Name, Last Name, Email, Password
- Create account
- Should redirect to dashboard

### 3. Test Login

- Logout (if still logged in)
- Go to `http://localhost:8000/login`
- Enter admin credentials created in Step 9
- Should redirect to dashboard

### 4. Test Admin Panel

- While logged in as admin, navigate to top navbar
- Should see "Admin Panel" link
- Click it to access `/admin/dashboard`

**Test admin features**:
- [ ] View user list
- [ ] Search/filter users
- [ ] View user details
- [ ] Edit user information
- [ ] View system health
- [ ] Update settings

### 5. Test API Endpoints

**Public Spotify Search**:
```bash
curl "http://localhost:8000/api/v1/spotify/search?q=Taylor%20Swift&type=track"
```

**Protected Endpoint** (need token):
```bash
# First, generate token via Tinker
php artisan tinker
$user = \App\Models\User::first();
$token = $user->createToken('test')->plainTextToken;
exit;

# Then use token
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8000/api/v1/music
```

### 6. Test Music Management

- Go to `/dashboard`
- Click "Add Music"
- Try searching Spotify
- Add a track to your collection
- View in "My Music" section

### 7. Test Playlists

- Go to `/playlists`
- Create a playlist
- Add music to playlist
- Edit/delete playlist

---

## Troubleshooting

### Issue: "SQLSTATE[HY000]: General error: 7 ERROR"

**Cause**: Supabase SSL requirement

**Fix**: Ensure in `.env`:
```env
DB_SSLMODE=require
```

### Issue: "Migrate command not found"

**Cause**: PHP not in PATH or Laravel not installed

**Fix**:
```bash
composer install
php artisan migrate
```

### Issue: "Token mismatch" on forms

**Cause**: CSRF token not included

**Fix**: Verify all forms have `@csrf` Blade directive

### Issue: "Connection refused" to Supabase

**Cause**: Database credentials incorrect or firewall blocking

**Fix**:
1. Verify credentials in `.env`
2. Test connection: `php artisan tinker` → `DB::connection()->getPdo()`
3. Check Supabase project network settings

### Issue: Assets not loading (CSS/JS broken)

**Cause**: Assets not compiled

**Fix**:
```bash
npm install
npm run dev    # for development
# or
npm run build  # for production-like
```

### Issue: "Class not found" errors

**Cause**: Composer dependencies not installed

**Fix**:
```bash
composer install
composer dump-autoload
```

---

## Verify Supabase Integration

### Method 1: Via Tinker

```bash
php artisan tinker

# Test database connection
DB::connection()->getPdo()
# Should return PDOStatement object without errors

# Count users
\App\Models\User::count()
# Should return integer

# Get first user
\App\Models\User::first()
# Should return User model instance
```

### Method 2: Via Dashboard

```bash
php artisan serve
```

Then:
1. Navigate to `/admin/dashboard`
2. Check "System Health" card
3. Should show:
   - Database connection: "Connected"
   - Users count: >= 1
   - Music entries count: >= 0
   - Environment: "local"

### Method 3: Via SQL Editor (Supabase)

1. Go to Supabase Dashboard
2. Click "SQL Editor"
3. Run:
```sql
SELECT COUNT(*) as users FROM users;
SELECT COUNT(*) as music FROM music_entries;
```

Both should return counts without errors.

---

## Development Workflow

### After Any Git Pull

```bash
composer install      # Update PHP dependencies
npm install          # Update JS dependencies
php artisan migrate  # Run new migrations
npm run dev          # Rebuild assets
```

### When Modifying Views

- Changes to `.blade.php` files reflect immediately
- No rebuild needed

### When Modifying Assets

- CSS/JS changes compile on save (if `npm run dev` running)
- Refresh browser to see changes

### When Adding Models/Controllers

```bash
composer dump-autoload  # Refresh class map
```

### When Adding Migrations

```bash
php artisan migrate  # Run new migrations
```

---

## Performance Tips for Local Development

1. **Use SQLite for Faster Tests** (Optional):
```bash
touch database/database.sqlite
# Update .env: DB_CONNECTION=sqlite
php artisan migrate
```

2. **Enable Query Logging** to debug N+1 queries:
```php
// In routes/web.php
DB::listen(function ($query) {
    \Log::info($query->sql);
});
```

3. **Cache Configuration** (production-like):
```bash
php artisan config:cache
```

4. **Clear Caches When Needed**:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

## Security Notes for Local Development

⚠️ **Never commit sensitive data**:
- `.env` file (use `.env.example`)
- `.env.local` if using one
- `public_html/storage/logs/*`

✅ **Always use**:
- CSRF tokens on all forms (`@csrf`)
- Authorization checks (`auth()->check()`, `auth()->user()->role === 'admin'`)
- Input validation (Form Requests)
- Password hashing (Hash facade)

---

## Next Steps After Local Deployment

### If Everything Works:
1. ✅ Commit all changes to Git
2. ✅ Push to repository
3. ✅ Proceed to Phase 5 (optional enhancements)
4. ✅ Prepare for production deployment to Render

### If Issues Occur:
1. Check troubleshooting section
2. Review Laravel logs: `storage/logs/laravel.log`
3. Check Supabase console for database errors
4. Run: `php artisan optimize:clear`

---

## Test Data Loading

If migrations don't seed test data:

```bash
php artisan db:seed
```

Or manually via Tinker:
```php
\App\Models\User::factory()->count(5)->create();
```

---

## Success Indicators ✅

You'll know everything is working when:

- [ ] `http://localhost:8000` loads landing page
- [ ] Can register new user
- [ ] Can login as user
- [ ] Can access admin panel as admin
- [ ] Music/playlist management works
- [ ] API endpoints return JSON (with proper auth)
- [ ] Supabase dashboard shows data
- [ ] No errors in `storage/logs/laravel.log`
- [ ] Admin system health shows "Connected"

---

## Commands Quick Reference

```bash
# Setup
composer install
npm install
php artisan key:generate
cp .env.example .env

# Database
php artisan migrate
php artisan migrate:reset
php artisan db:seed

# Development
php artisan serve           # Start server
npm run dev               # Watch assets
php artisan tinker        # Interactive shell

# Debugging
php artisan logs          # View recent logs
php artisan config:show   # Show all config
php artisan route:list    # Show all routes

# Cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

**Status**: ✅ Ready for Local Deployment

**Application**: Fully Feature-Complete (Phases 1-4)

**Next Phase**: Phase 5 (Optional) or Production Deployment (Render.com)







