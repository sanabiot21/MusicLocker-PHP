# Music Locker

> Your Personalized Music and Albums Repository

A PHP-based web application that allows users to create and manage their personal music catalog without relying on external streaming platforms. Music Locker provides a private, organized space where music enthusiasts can log their favorite tracks, albums, and associated memories.

**This project showcases two complete implementations:**
- **V1**: Custom PHP MVC architecture (Production-ready)
- **V2**: Laravel framework migration (Modern, scalable)

## Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [API Integration](#api-integration)
- [Security](#security)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [Team](#team)
- [License](#license)

## Overview

Music Locker solves the problem of scattered music discoveries and forgotten favorite songs by providing a personal record of your music taste. Track songs you discover and organize your musical preferences in a simple, distraction-free environment without algorithm interference or subscription requirements.

### What Music Locker IS

- A personal music catalog manager
- A place to store music memories and notes
- An organized database of your favorite tracks and albums
- A mobile-responsive web application
- Integration with Spotify for metadata lookup

### What Music Locker IS NOT

- A music streaming service
- A file hosting platform
- A social music sharing network
- A music recommendation engine

## Architecture

This project demonstrates two complete, production-ready implementations of the Music Locker application:

### V1: Custom PHP MVC Implementation

**Location:** Root directory ([/src](src/), [/public](public/), [/config](config/))

**Philosophy:** Built from scratch to demonstrate deep understanding of web application fundamentals without framework dependencies.

**Key Characteristics:**
- **Pure MVC Pattern:** Custom-built Model-View-Controller architecture
- **Manual Routing:** Switch/case based routing in [/public/index.php](public/index.php)
- **PDO Database Layer:** Direct database access with prepared statements
- **Repository Pattern:** Interface-based data access ([/src/Repositories](src/Repositories/))
- **Security-First:** Custom CSRF protection, session management, and input validation
- **Database:** PostgreSQL 13+ (Supabase) with ACID compliance
- **Deployment:** Docker with Nginx + PHP-FPM on Render.com

**Architecture Layers:**
```
/src
├── /Controllers        # 6 controllers (Auth, Music, Playlist, Admin, Dashboard, Spotify)
├── /Models             # 6 models (User, MusicEntry, MusicNote, Tag, Playlist, SystemSetting)
├── /Services           # Business logic (Database PDO wrapper, SpotifyService)
├── /Repositories       # Data access layer with interfaces (User, Tag repositories)
├── /Security           # Auth & protection (CsrfManager, SessionManager, InputValidator)
├── /Utils              # Helpers (ConfigManager, UrlHelper, HelperFunctions)
└── /Views              # HTML templates (auth, music, admin, playlists, layouts)
```

**Request Flow:**
```
Browser → Nginx → PHP-FPM → /public/index.php (router)
  → Controller → Model/Service → Database
  → View → Response
```

### V2: Laravel Framework Migration

**Location:** [/laravel](laravel/) directory

**Philosophy:** Modern framework approach leveraging Laravel's ecosystem for rapid development, scalability, and maintainability.

**Key Characteristics:**
- **Laravel 12+:** Full Laravel framework with latest features
- **Eloquent ORM:** Model-based database abstraction
- **Artisan CLI:** Code generation and task automation
- **RESTful API:** Separated web and API routes with Laravel Sanctum
- **Blade Templates:** Template engine with component system
- **Migration System:** Version-controlled database schema
- **Database:** PostgreSQL 13+ (Supabase integration)
- **Deployment:** Docker-ready with Laravel best practices

**Architecture Layers:**
```
/laravel
├── /app
│   ├── /Http
│   │   ├── /Controllers     # Web & API controllers
│   │   ├── /Middleware      # Authentication & authorization
│   │   └── /Requests        # Form validation requests
│   ├── /Models              # Eloquent models with relationships
│   ├── /Services            # Business logic services
│   └── /Helpers             # Utility functions
├── /routes
│   ├── web.php              # Web routes with middleware
│   ├── api.php              # RESTful API routes
│   └── console.php          # Artisan commands
├── /database
│   ├── /migrations          # Schema version control
│   └── /seeders             # Database seeding
├── /resources
│   ├── /views               # Blade templates
│   └── /js                  # Frontend assets (Vite)
├── /config                  # Configuration files
├── /storage                 # Logs, cache, sessions
└── /public                  # Web root
```

**Request Flow:**
```
Browser → Nginx → PHP-FPM → /laravel/public/index.php (Laravel bootstrap)
  → Routes (middleware) → Controller → Eloquent Model → Database
  → Blade View/JSON Response → Browser
```

### V1 vs V2 Comparison

| Aspect | V1 (Custom PHP) | V2 (Laravel) |
|--------|-----------------|--------------|
| **Routing** | Manual switch/case | Laravel routes with middleware |
| **Database** | PDO with prepared statements | Eloquent ORM |
| **Database Type** | PostgreSQL 13+ (Supabase) | PostgreSQL 13+ (Supabase) |
| **Templates** | Raw PHP views | Blade template engine |
| **Authentication** | Custom session management | Laravel Auth + Sanctum |
| **API** | AJAX endpoints in controllers | RESTful API with resource controllers |
| **Validation** | Custom InputValidator class | Laravel Form Requests |
| **Migrations** | SQL schema files | Laravel migrations |
| **Dependency Injection** | Manual instantiation | Service container |
| **Testing** | PHPUnit (basic) | PHPUnit + Feature tests |
| **Caching** | Manual implementation | Laravel Cache facade |
| **Queue System** | None | Laravel Queue (ready) |
| **CLI Tools** | None | Artisan commands |
| **Development Time** | Longer (everything from scratch) | Faster (framework features) |
| **Learning Curve** | Lower (pure PHP) | Higher (Laravel conventions) |
| **Scalability** | Manual optimization needed | Built-in scaling features |
| **Community Support** | Self-maintained | Large Laravel ecosystem |

### Why Two Implementations?

1. **Educational Value:** V1 demonstrates understanding of core web concepts; V2 shows modern framework proficiency
2. **Production Flexibility:** Choose custom PHP for lightweight deployments or Laravel for enterprise features
3. **Migration Path:** V1 serves as a stable base while V2 provides a modern upgrade path
4. **Code Comparison:** Side-by-side comparison of approaches to the same problem

## Features

### Core Functionality

- **User Management**
  - Secure registration and authentication
  - Session management
  - Password recovery
  - User profiles

- **Music Catalog**
  - Add, edit, and delete songs and albums
  - Personal notes and memories for each entry
  - Custom mood/vibe tagging system
  - Search and filter within your collection
  - Detailed information display

- **Dashboard & Statistics**
  - Collection overview
  - Basic statistics
  - Recent additions

- **Data Management**
  - ACID-compliant database transactions
  - Data export for personal backup
  - Basic offline functionality through caching

- **Admin Features**
  - User account management
  - System health monitoring
  - Database maintenance tools
  - API configuration

## Technology Stack

### Backend
- **PHP 8.2+** - Core language
- **PostgreSQL 13+** - Database with ACID compliance
- **Composer** - Dependency management

### Frontend
- **HTML5, CSS3, JavaScript (ES6+)**
- **Bootstrap** - Responsive design framework
- **localStorage** - Offline caching

### Dependencies
- `vlucas/phpdotenv` - Environment configuration
- `monolog/monolog` - Logging
- `phpmailer/phpmailer` - Email functionality

### External APIs
- **Spotify Web API** - Song metadata lookup

### Security
- PHP sessions
- bcrypt password hashing
- CSRF protection
- Input validation and sanitization

## Requirements

### Server Requirements
- PHP 8.2 or higher
- PostgreSQL 13 or higher
- Apache/Nginx web server
- Composer

### PHP Extensions
- curl
- json
- mbstring
- openssl
- pdo
- pdo_pgsql

### Browser Requirements
- Chrome 90+
- Firefox 88+
- Safari 14+
- JavaScript enabled

## Installation

### Current Custom PHP Implementation

#### 1. Clone the Repository

```bash
git clone https://github.com/your-username/music-locker.git
cd music-locker
```

#### 2. Install Dependencies

```bash
composer install
```

#### 3. Database Setup

Create a PostgreSQL database (or use Supabase):

```bash
# Using Supabase (recommended)
# 1. Create a new project at supabase.com
# 2. Go to SQL Editor
# 3. Import and run the contents of database/schema.sql
```

If using a local PostgreSQL instance:

```bash
createdb music_locker
psql -d music_locker -f database/schema.sql
```

#### 4. Environment Configuration

Copy the `.env` file and configure your settings:

```bash
cp .env .env.local
```

Edit `.env` with your configuration:

```env
# Database Configuration (PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=6543
DB_NAME=postgres
DB_USER=postgres.your-project-ref
DB_PASS=your_password
DB_SSLMODE=require

# Application Settings
APP_NAME="Music Locker"
APP_URL=http://localhost:8888
APP_ENV=development

# Spotify API
SPOTIFY_CLIENT_ID=your_spotify_client_id
SPOTIFY_CLIENT_SECRET=your_spotify_client_secret

# Email Configuration (for password recovery)
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_password
MAIL_FROM=noreply@musiclocker.local
```

#### 5. Start Development Server

```bash
composer serve
```

Or manually:

```bash
php -S 127.0.0.1:8888 -t public
```

Visit `http://localhost:8888` in your browser.

### Laravel Migration Project

#### 1. Copy Laravel Project

If you have the Laravel project in XAMPP, copy it to the laravel subdirectory:

```powershell
Copy-Item -Path "C:\xampp\htdocs\MusicLocker-PHP" -Destination "C:\Users\shawn\Desktop\MusicLocker-PHP\laravel" -Recurse
```

#### 2. Install Laravel Dependencies

```bash
cd laravel
composer install
npm install  # If using Vite/frontend build tools
```

#### 3. Environment Setup

Copy and configure the environment file:

```bash
cp .env.example .env
php artisan key:generate
```

#### 4. Database Configuration

Configure for Supabase (PostgreSQL):

```env
DB_CONNECTION=pgsql
DB_HOST=<your-supabase-host>.supabase.co
DB_PORT=5432
DB_DATABASE=<your-database-name>
DB_USERNAME=<your-username>
DB_PASSWORD=<your-password>
DB_SSLMODE=require
```

#### 5. Start Laravel Development Server

```bash
cd laravel
php artisan serve
```

Visit `http://127.0.0.1:8000` in your browser.

For detailed Laravel setup instructions, see [docs/LARAVEL_SETUP.md](docs/LARAVEL_SETUP.md).

## Configuration

### Spotify API Setup

1. Go to [Spotify Developer Dashboard](https://developer.spotify.com/dashboard)
2. Create a new application
3. Copy the Client ID and Client Secret
4. Add `http://localhost:8888/spotify/callback` to Redirect URIs
5. Update your `.env` file with the credentials

### Database Configuration

The application uses PDO for database connections with ACID compliance. Configure your PostgreSQL credentials in the `.env` file as shown in the Installation section. The application is optimized for Supabase's connection pooler.

## Usage

### For Regular Users

1. **Register** - Create your account at `/register`
2. **Login** - Access your account at `/login`
3. **Add Music** - Search for songs using Spotify integration or add manually
4. **Organize** - Add notes, tags, and memories to your entries
5. **Browse** - Search and filter your personal collection
6. **Export** - Backup your data anytime

### For Administrators

1. Access admin dashboard at `/admin/dashboard`
2. Manage user accounts at `/admin/users`
3. Monitor system health at `/admin/system-health`
4. Configure settings at `/admin/settings`

## Project Structure

Complete directory structure showing both V1 (root) and V2 (Laravel) implementations:

```
MusicLocker-PHP/
│
├── V1: CUSTOM PHP IMPLEMENTATION (Root Level)
│   │
│   ├── /public/                    # Web root for V1
│   │   ├── /assets/
│   │   │   ├── /css/              # Stylesheets
│   │   │   ├── /js/               # JavaScript files
│   │   │   └── /img/              # Images
│   │   └── index.php              # Application entry point & router
│   │
│   ├── /src/                      # V1 MVC Implementation
│   │   ├── /Controllers/          # Request handlers
│   │   │   ├── AdminController.php
│   │   │   ├── AuthController.php
│   │   │   ├── BaseController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── HomeController.php
│   │   │   ├── MusicController.php
│   │   │   ├── PlaylistController.php
│   │   │   └── SpotifyController.php
│   │   │
│   │   ├── /Models/               # Data models
│   │   │   ├── MusicEntry.php
│   │   │   ├── MusicNote.php
│   │   │   ├── Playlist.php
│   │   │   ├── SystemSetting.php
│   │   │   ├── Tag.php
│   │   │   └── User.php
│   │   │
│   │   ├── /Services/             # Business logic layer
│   │   │   ├── Database.php       # PDO wrapper (singleton pattern)
│   │   │   └── SpotifyService.php # Spotify API integration
│   │   │
│   │   ├── /Repositories/         # Data access layer
│   │   │   ├── BaseRepository.php
│   │   │   ├── UserRepository.php
│   │   │   ├── TagRepository.php
│   │   │   └── /Interfaces/
│   │   │       ├── UserRepositoryInterface.php
│   │   │       └── TagRepositoryInterface.php
│   │   │
│   │   ├── /Security/             # Security components
│   │   │   ├── CsrfManager.php    # CSRF token protection
│   │   │   ├── SessionManager.php # Session handling
│   │   │   └── InputValidator.php # Input sanitization & validation
│   │   │
│   │   ├── /Utils/                # Helper utilities
│   │   │   ├── ConfigManager.php
│   │   │   ├── UrlHelper.php
│   │   │   ├── helpers.php
│   │   │   └── HelperFunctions.php
│   │   │
│   │   └── /Views/                # HTML templates (raw PHP)
│   │       ├── /auth/             # Authentication views
│   │       │   ├── login.php
│   │       │   ├── register.php
│   │       │   ├── forgot.php
│   │       │   ├── reset.php
│   │       │   └── profile.php
│   │       ├── /music/            # Music management views
│   │       │   ├── index.php
│   │       │   ├── add.php
│   │       │   ├── edit.php
│   │       │   └── show.php
│   │       ├── /playlists/        # Playlist views
│   │       │   ├── index.php
│   │       │   ├── create.php
│   │       │   ├── edit.php
│   │       │   └── show.php
│   │       ├── /admin/            # Admin panel views
│   │       │   ├── dashboard.php
│   │       │   ├── users.php
│   │       │   ├── user-detail.php
│   │       │   ├── user-music.php
│   │       │   ├── settings.php
│   │       │   └── system-health.php
│   │       ├── /layouts/          # Layout templates
│   │       │   └── app.php
│   │       ├── home.php           # Landing page
│   │       └── dashboard.php      # User dashboard
│   │
│   ├── /config/                   # V1 Configuration files
│   │   ├── app.php                # Application settings
│   │   ├── database.php           # Database configuration
│   │   └── spotify.php            # Spotify API settings
│   │
│   ├── /database/                 # Database schemas
│   │   ├── music_locker.sql       # MySQL/MariaDB schema
│   │   └── schema.sql             # PostgreSQL schema (Supabase)
│   │
│   ├── /tests/                    # V1 Unit tests
│   │
│   ├── /scripts/                  # Utility scripts
│   │
│   ├── /vendor/                   # V1 Composer dependencies
│   │
│   ├── composer.json              # V1 PHP dependencies
│   ├── composer.lock
│   ├── phpunit.xml                # Test configuration
│   ├── .env                       # V1 Environment configuration
│   └── .env.example               # V1 Environment template
│
├── V2: LARAVEL IMPLEMENTATION
│   │
│   └── /laravel/                  # Complete Laravel project
│       │
│       ├── /app/                  # Laravel application core
│       │   ├── /Http/
│       │   │   ├── /Controllers/  # Web & API controllers
│       │   │   ├── /Middleware/   # Authentication & authorization
│       │   │   └── /Requests/     # Form validation requests
│       │   ├── /Models/           # Eloquent ORM models
│       │   ├── /Services/         # Business logic services
│       │   ├── /Helpers/          # Helper functions
│       │   └── /Providers/        # Service providers
│       │       └── AppServiceProvider.php
│       │
│       ├── /bootstrap/            # Laravel bootstrap
│       │   └── providers.php
│       │
│       ├── /config/               # Laravel configuration
│       │   ├── app.php
│       │   ├── auth.php
│       │   ├── cache.php
│       │   ├── database.php
│       │   ├── filesystems.php
│       │   ├── logging.php
│       │   ├── mail.php
│       │   ├── queue.php
│       │   └── session.php
│       │
│       ├── /database/             # Database layer
│       │   ├── /migrations/       # Schema migrations
│       │   ├── /seeders/          # Database seeders
│       │   │   └── DatabaseSeeder.php
│       │   └── /factories/        # Model factories
│       │       └── UserFactory.php
│       │
│       ├── /public/               # V2 Web root
│       │   ├── index.php          # Laravel entry point
│       │   ├── favicon.ico
│       │   └── robots.txt
│       │
│       ├── /resources/            # Frontend resources
│       │   ├── /views/            # Blade templates
│       │   │   └── welcome.blade.php
│       │   └── /js/               # JavaScript assets
│       │       └── bootstrap.js
│       │
│       ├── /routes/               # Laravel routing
│       │   ├── web.php            # Web routes
│       │   ├── api.php            # API routes
│       │   └── console.php        # Artisan commands
│       │
│       ├── /storage/              # Storage layer
│       │   ├── /app/              # File storage
│       │   ├── /framework/        # Framework cache & sessions
│       │   └── /logs/             # Application logs
│       │
│       ├── /tests/                # Laravel tests
│       │   ├── /Feature/          # Feature tests
│       │   │   └── ExampleTest.php
│       │   ├── /Unit/             # Unit tests
│       │   │   └── ExampleTest.php
│       │   └── TestCase.php
│       │
│       ├── /vendor/               # V2 Composer dependencies (Laravel ecosystem)
│       │   └── /laravel/
│       │       ├── /framework/    # Laravel core
│       │       └── /sanctum/      # API authentication
│       │
│       ├── artisan                # Laravel CLI tool
│       ├── composer.json          # V2 Laravel dependencies
│       ├── package.json           # Node.js dependencies (Vite)
│       ├── vite.config.js         # Vite configuration
│       ├── phpunit.xml            # Laravel test configuration
│       ├── .env.example           # V2 Environment template
│       ├── README.md              # Laravel project README
│       └── CHANGELOG.md           # Version history
│
├── DEPLOYMENT & INFRASTRUCTURE
│   │
│   ├── /docker/                   # Docker configuration
│   │   ├── nginx.conf             # Nginx web server config
│   │   ├── apache.conf            # Alternative Apache config
│   │   ├── supervisord.conf       # Process supervisor
│   │   └── start.sh               # Container startup script
│   │
│   ├── render.yaml                # Render.com deployment config
│   └── Dockerfile                 # (V2: located in /laravel/)
│
├── DOCUMENTATION
│   │
│   ├── /docs/                     # Project documentation
│   │   └── LARAVEL_SETUP.md       # Laravel setup guide
│   │
│   ├── README.md                  # This file
│   └── LICENSE                    # MIT License
│
└── PROJECT FILES
    │
    ├── .gitignore                 # Git ignore rules
    ├── .gitattributes             # Git attributes
    ├── .env                       # Production environment (V1)
    ├── .env.example               # Environment template (V1)
    │
    └── DEVELOPMENT TOOLS
        ├── .claude/               # Claude Code configuration
        └── .cursor/               # Cursor IDE settings
```

### Key Directories Explained

**V1 (Custom PHP):**
- [/src](src/): Core application code with MVC architecture
- [/public](public/): Publicly accessible web root
- [/config](config/): Application, database, and API configuration
- [/database](database/): SQL schema files for MySQL and PostgreSQL
- [/vendor](vendor/): Composer dependencies (PHPDotEnv, Monolog, PHPMailer)

**V2 (Laravel):**
- [/laravel/app](laravel/app/): Laravel application logic
- [/laravel/routes](laravel/routes/): Web and API route definitions
- [/laravel/database](laravel/database/): Migrations and seeders for schema versioning
- [/laravel/resources](laravel/resources/): Blade templates and frontend assets
- [/laravel/vendor](laravel/vendor/): Laravel framework and ecosystem packages

**Shared:**
- [/docker](docker/): Production deployment configurations
- [/docs](docs/): Comprehensive documentation for both implementations
- [/storage](storage/): Logs and cached data (V1)

### Dependencies Overview

**V1 Custom PHP Dependencies** ([composer.json](composer.json)):
```json
{
  "require": {
    "php": "^8.2",
    "vlucas/phpdotenv": "^5.5",        // Environment management
    "monolog/monolog": "^3.4",         // Logging
    "phpmailer/phpmailer": "^6.8"      // Email functionality
  },
  "require-dev": {
    "phpunit/phpunit": "^10.3"         // Testing framework
  }
}
```

**V2 Laravel Dependencies** ([/laravel/composer.json](laravel/composer.json)):
```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^12.0",      // Laravel core
    "laravel/sanctum": "^4.0",         // API authentication
    "laravel/tinker": "^2.9"           // REPL for debugging
  },
  "require-dev": {
    "fakerphp/faker": "^1.23",         // Test data generation
    "phpunit/phpunit": "^11.0"         // Testing framework
  }
}
```

## API Integration

### Spotify Web API

The application integrates with Spotify to fetch song metadata:

- Search for songs, albums, and artists
- Retrieve album artwork
- Get track details (duration, release date, etc.)
- Fetch artist information

API rate limits apply based on your Spotify API tier.

## Security

Music Locker implements multiple security measures:

- **Password Security** - bcrypt hashing with appropriate cost factor
- **Session Management** - Secure PHP sessions with regeneration
- **CSRF Protection** - Token-based protection on forms
- **Input Validation** - Server-side validation and sanitization
- **SQL Injection Prevention** - PDO prepared statements
- **XSS Prevention** - Output escaping in templates

## Deployment

This project is configured for production deployment on Render.com with Docker containerization.

### Current Production Setup

**Platform:** Render.com
**Region:** Singapore (Asia/Manila timezone)
**Database:** Supabase PostgreSQL (pooled connections)
**Configuration:** [render.yaml](render.yaml)

### Deployment Architecture

```
Render.com
    ↓
Docker Container
    ├── Nginx (Port 80)
    │   ├── Static file serving
    │   ├── Reverse proxy to PHP-FPM
    │   └── Security headers
    ├── PHP-FPM 8.2 (Port 9000)
    │   └── FastCGI process manager
    └── Supervisord
        └── Process management
    ↓
Supabase (PostgreSQL)
    └── aws-1-ap-southeast-1.pooler.supabase.com
```

### Render.com Configuration

**Service Details:**
- Service Type: Web
- Runtime: Docker
- Auto Deploy: Enabled
- Health Check: `/` endpoint
- Instances: 1-2 (auto-scaling)
- Plan: Starter

**Environment Variables:**
```bash
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Manila
LOG_LEVEL=error

# Database Configuration (Supabase Session Pooler)
# Option 1: Use individual variables (recommended)
DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.[PROJECT_REF]
DB_PASSWORD=your_supabase_password
DB_SSLMODE=require

# Option 2: Use full connection URL (alternative if Option 1 fails)
# DB_URL=postgresql://postgres.[PROJECT_REF]:[PASSWORD]@aws-1-ap-southeast-1.pooler.supabase.com:5432/postgres?sslmode=require

# Optional: Connection timeout and retry settings
DB_TIMEOUT=10
DB_RETRY_AFTER=5
DB_PERSISTENT=false
```

**Important:** 
- `DB_PORT=5432` is required for Supabase's session pooler (IPv4 compatible, supports prepared statements)
- `DB_HOST` must point to the pooler endpoint (ends with `.pooler.supabase.com`)
- `DB_USERNAME` should include project reference: `postgres.[PROJECT_REF]`
- Session pooler is recommended for Laravel as it supports prepared statements (Transaction pooler on port 6543 does not)

**Troubleshooting Connection Refused Errors:**
1. **Verify Session Pooler is Enabled:** In Supabase Dashboard → Settings → Database → Connection Pooling, ensure "Session mode" is enabled
2. **Check Project Status:** Ensure your Supabase project is Active (not Paused)
3. **Network Settings:** Verify "Allow connections from any IP" is enabled in Supabase Dashboard → Settings → Database
4. **Try DB_URL Format:** If individual variables fail, use the `DB_URL` format from Supabase's connection string panel
5. **Verify Credentials:** Ensure password has no quotes in Render's environment variables

### Docker Configuration

**Nginx Setup ([/docker/nginx.conf](docker/nginx.conf)):**
- FastCGI proxy to PHP-FPM on 127.0.0.1:9000
- Gzip compression enabled
- Static asset caching (365 days)
- Security headers (X-Frame-Options, X-Content-Type-Options, X-XSS-Protection)
- Denies access to sensitive directories (.env, /config, /src, /storage, /vendor)
- Request timeout: 300 seconds

**Supervisord ([/docker/supervisord.conf](docker/supervisord.conf)):**
- Manages Nginx and PHP-FPM processes
- Auto-restart on failure
- Log rotation

**Startup Script ([/docker/start.sh](docker/start.sh)):**
- Container initialization
- Environment validation
- Service startup sequence

### Deployment Steps

#### V1 (Custom PHP) - Render.com

1. **Configure Render Service:**
   - Connect GitHub repository
   - Set Docker runtime
   - Configure environment variables from `.env.example`

2. **Database Setup:**
   - Create Supabase project
   - Import `/database/schema.sql` (PostgreSQL)
   - Configure connection pooling
   - Update `DB_HOST` in environment variables

3. **Deploy:**
   - Push to main branch (auto-deploy enabled)
   - Monitor build logs on Render dashboard
   - Verify health check status

#### V2 (Laravel) - Render.com

1. **Prepare Laravel:**
   ```bash
   cd laravel
   composer install --no-dev --optimize-autoloader
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Configure Dockerfile:**
   - Use `/laravel/Dockerfile` (multi-stage build)
   - PHP 8.2-FPM Alpine base image
   - Installs: pdo_pgsql, mbstring, GD, bcmath, zip, exif, pcntl

3. **Database Migration:**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

4. **Deploy:**
   - Set `dockerfilePath: laravel/Dockerfile` in render.yaml
   - Configure Laravel environment variables
   - Deploy and monitor

### Manual Docker Deployment

**Build Image:**
```bash
# V1
docker build -t music-locker-v1 .

# V2
docker build -t music-locker-v2 -f laravel/Dockerfile laravel/
```

**Run Container:**
```bash
docker run -d \
  -p 80:80 \
  --env-file .env \
  --name music-locker \
  music-locker-v1
```

**Verify:**
```bash
docker logs music-locker
docker exec -it music-locker php -v
```

### Health Monitoring

**Health Check Endpoint:** `/`
**Expected Response:** HTTP 200
**Monitoring Tools:**
- Render.com dashboard (uptime, response time, logs)
- Custom system health endpoint: `/admin/system-health` (admin only)

### Scaling Considerations

**Horizontal Scaling:**
- Configure `maxInstances` in render.yaml
- Session storage: Database-backed sessions (shared state)
- File uploads: Use external storage (S3, Cloudinary)

**Database Optimization:**
- Connection pooling enabled (Supabase Pooler)
- Query optimization with indexes
- Database view for statistics: `user_music_stats`

**Caching:**
- V1: Manual file-based caching
- V2: Laravel Cache (Redis recommended for production)

### Security Notes

- SSL/TLS: Automatic via Render.com
- Environment variables: Stored securely in Render dashboard
- Database: SSL required (`DB_SSLMODE=require`)
- Secrets: Never commit `.env` files to Git
- File permissions: Nginx runs as non-root user

### Supabase Network Configuration & Troubleshooting

#### Understanding IP Whitelisting

Supabase uses **IP whitelisting** (also called "IP allowlisting" or "network restrictions") to control which IP addresses can connect to your database. This is a security feature that prevents unauthorized access.

**What is IP Whitelisting?**
- A list of allowed IP addresses/IP ranges that can connect to your Supabase database
- By default, Supabase may restrict connections to specific IPs
- Render's infrastructure uses dynamic IP addresses that need to be whitelisted

#### Configuring Supabase Network Settings

1. **Access Supabase Dashboard:**
   - Go to your Supabase project dashboard
   - Navigate to **Settings** → **Database** → **Connection Pooling** or **Network**

2. **Enable Connection Pooler:**
   - Ensure transaction pooler is enabled (port 6543)
   - Copy the pooler connection string (should end with `.pooler.supabase.com`)

3. **Configure Network Restrictions:**
   - **Option A: Allow All IPs (Development/Testing)**
     - Set network restrictions to allow connections from anywhere (`0.0.0.0/0`)
     - ⚠️ **Warning:** Less secure, only for development/testing
   
   - **Option B: Whitelist Render IPs (Production)**
     - Render uses dynamic IPs, so you may need to allow all IPs or use Supabase's allowed IPs feature
     - Check Render's documentation for current IP ranges
     - Add those IP ranges to Supabase's network allowlist

4. **Verify Connection String:**
   - Use the **Transaction Pooler** connection string (port 6543)
   - Format: `aws-{region}-{instance}.pooler.supabase.com:6543`
   - Example: `aws-1-ap-southeast-1.pooler.supabase.com:6543`

#### Troubleshooting Connection Issues

**Error: "Connection refused" or "SQLSTATE[08006]"**

1. **Verify Environment Variables:**
   ```bash
   # Check these are set correctly in Render dashboard:
   DB_CONNECTION=pgsql
   DB_HOST=aws-1-ap-southeast-1.pooler.supabase.com  # Must end with .pooler.supabase.com
   DB_PORT=6543  # Required for transaction pooler
   DB_SSLMODE=require
   ```

2. **Test Connection Locally:**
   ```bash
   cd laravel
   php scripts/test-db-connection.php
   ```
   This script tests the connection independently of Laravel.

3. **Check Supabase Project Status:**
   - Ensure your Supabase project is active (not paused)
   - Verify database is running in Supabase dashboard
   - Check project logs for connection attempts

4. **Verify Network Settings:**
   - In Supabase dashboard, check if there are IP restrictions
   - If restrictions exist, temporarily allow all IPs to test (`0.0.0.0/0`)
   - Once connection works, you can tighten restrictions

5. **Check Connection Pooler:**
   - Ensure transaction pooler is enabled in Supabase
   - Verify you're using the pooler endpoint, not direct connection
   - Direct connection (port 5432) requires IPv4 support which Render doesn't provide

6. **Review Deployment Logs:**
   - Check Render deployment logs for detailed error messages
   - The startup script now includes retry logic with exponential backoff
   - Look for connection attempt messages and specific error codes

**Error: "timeout expired"**

- Increase `DB_TIMEOUT` environment variable (default: 10 seconds)
- Check Supabase project isn't paused or experiencing issues
- Verify network connectivity between Render and Supabase

**Common Mistakes:**

- ❌ Using direct connection (port 5432) instead of pooler (port 6543)
- ❌ Missing `DB_PORT=6543` environment variable
- ❌ Using wrong host (direct connection host instead of pooler host)
- ❌ Setting `DB_SSLMODE=disable` (Supabase requires SSL)
- ❌ Not whitelisting Render's IP addresses in Supabase

**Connection Test Script:**

Use the provided test script to diagnose issues:
```bash
cd laravel
php scripts/test-db-connection.php
```

This script will:
- Display current database configuration
- Attempt a direct PDO connection
- Test a simple query
- Provide specific error messages and troubleshooting steps

## Development

### Running Tests

```bash
composer test
```

### Code Style Check

```bash
composer cs-check
```

### Code Style Fix

```bash
composer cs-fix
```

### Development Servers

**V1 (Custom PHP):**
```bash
composer serve
# or
php -S 127.0.0.1:8888 -t public
```

**V2 (Laravel):**
```bash
cd laravel
php artisan serve
# Runs on http://127.0.0.1:8000
```

## Constraints & Assumptions

- Internet connection required for external API music searches and initial login
- Users must have a valid email address for registration and password recovery
- JavaScript must be enabled for dynamic features and offline functionality
- External API rate limits may restrict the number of music searches per hour
- Single-user sessions - no concurrent login support from multiple devices
- English language only
- No music file uploading, streaming, or playback functionality

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please ensure your code follows PSR-12 coding standards and includes appropriate tests.

## Implementation Status

### V1: Custom PHP (Production)

**Status:** ✅ Fully Functional & Deployed

- Live on Render.com
- PostgreSQL database via Supabase
- Docker containerization with Nginx + PHP-FPM
- All features implemented and tested
- Security measures in place
- Admin panel operational

**Technology:** Pure PHP 8.2, Custom MVC, PDO, Manual routing

### V2: Laravel (Modern Alternative)

**Status:** ✅ Complete Migration

- Full Laravel 12 implementation
- Eloquent ORM with PostgreSQL support
- RESTful API with Laravel Sanctum
- Blade templating system
- Database migrations and seeders
- Ready for deployment

**Technology:** Laravel 12, Eloquent, Artisan CLI, Blade templates

### Migration Path

Developers can choose either implementation based on their needs:

- **Use V1** for: Lightweight deployment, learning PHP fundamentals, custom control
- **Use V2** for: Rapid development, Laravel ecosystem, modern tooling, API-first design

Both implementations share the same database schema (with minor syntax differences for MySQL vs PostgreSQL) and can coexist in the same repository.

For Laravel setup details, see:
- [Laravel Setup Guide](docs/LARAVEL_SETUP.md)

## Team

**Team NaturalStupidity**

- Reynaldo D. Grande Jr. II
- Louis Jansen G. Letgio
- Euzyk Kendyl Villarino
- Shawn Patrick R. Dayanan

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- Spotify Web API for music metadata
- Bootstrap for responsive design
- All contributors and testers

---

**Note**: Music Locker does not host, stream, or distribute copyrighted music files. It is solely a cataloging tool for personal use.

For support or questions, please open an issue on GitHub.
