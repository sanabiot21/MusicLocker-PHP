# Music Locker

> Your Personalized Music and Albums Repository

A PHP-based web application that allows users to create and manage their personal music catalog without relying on external streaming platforms. Music Locker provides a private, organized space where music enthusiasts can log their favorite tracks, albums, and associated memories.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [API Integration](#api-integration)
- [Security](#security)
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
- **MySQL 8.0** - Database with ACID compliance
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
- MySQL 8.0 or higher
- Apache/Nginx web server
- Composer

### PHP Extensions
- curl
- json
- mbstring
- openssl
- pdo
- pdo_mysql

### Browser Requirements
- Chrome 90+
- Firefox 88+
- Safari 14+
- JavaScript enabled

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/music-locker.git
cd music-locker
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Database Setup

Create a MySQL database:

```sql
CREATE DATABASE music_locker;
```

Import the database schema:

```bash
mysql -u your_username -p music_locker < database/music_locker.sql
```

### 4. Environment Configuration

Copy the `.env` file and configure your settings:

```bash
cp .env .env.local
```

Edit `.env` with your configuration:

```env
# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_NAME=music_locker
DB_USER=your_username
DB_PASS=your_password

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

### 5. Start Development Server

```bash
composer serve
```

Or manually:

```bash
php -S 127.0.0.1:8888 -t public
```

Visit `http://localhost:8888` in your browser.

## Configuration

### Spotify API Setup

1. Go to [Spotify Developer Dashboard](https://developer.spotify.com/dashboard)
2. Create a new application
3. Copy the Client ID and Client Secret
4. Add `http://localhost:8888/spotify/callback` to Redirect URIs
5. Update your `.env` file with the credentials

### Database Configuration

The application uses PDO for database connections with ACID compliance. Configure your database credentials in the `.env` file.

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

```
MusicLocker-PHP/
├── public/                 # Web root
│   ├── assets/
│   │   ├── css/           # Stylesheets
│   │   ├── js/            # JavaScript files
│   │   └── img/           # Images
│   └── index.php          # Application entry point
├── src/
│   ├── Controllers/       # Request handlers
│   ├── Models/           # Data models
│   ├── Services/         # Business logic
│   ├── Utils/            # Helper functions
│   └── Views/            # Templates
│       ├── auth/         # Authentication views
│       ├── music/        # Music management views
│       ├── admin/        # Admin views
│       ├── playlists/    # Playlist views
│       └── layouts/      # Layout templates
├── database/             # Database files
├── docs/                 # Documentation
├── tests/                # Unit tests
├── vendor/               # Composer dependencies
├── .env                  # Environment configuration
├── .gitignore           # Git ignore rules
├── .gitattributes       # Git attributes
├── composer.json        # PHP dependencies
└── phpunit.xml          # Test configuration
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
