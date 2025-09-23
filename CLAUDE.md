# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Music Locker is a PHP-based personal music catalog management system by Team NaturalStupidity. The application allows users to create private music libraries without streaming functionality - focusing on organization, personal notes, and mood tagging of musical discoveries.

**Current Status: Phase 3 Implementation - In Development**

- Frontend: Bootstrap 5.3.2 with custom dark-techno theme
- Backend: MVC architecture with basic functionality
- Database: Schema implemented with basic tables
- Authentication: Basic session-based authentication
- Music Catalog: CRUD operations for music entries
- API Integration: Spotify Web API service (Client Credentials)
- Configuration: Environment-based system with Ngrok support

## Development Environment Setup

### XAMPP Configuration

The project uses XAMPP with a virtual host configuration to serve from the current directory:

```apache
# Add to httpd-vhosts.conf
<VirtualHost *:80>
    DocumentRoot "C:/Users/shawn/Desktop/MusicLocker-PHP/public"
    ServerName musiclocker.local
    ServerAlias www.musiclocker.local
    <Directory "C:/Users/shawn/Desktop/MusicLocker-PHP/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to Windows hosts file: `127.0.0.1    musiclocker.local`

### Essential Commands

**Start Development Environment:**

```bash
# Start XAMPP services
# Open XAMPP Control Panel -> Start Apache & MySQL

# Access application
http://musiclocker.local

# Database management
http://localhost/phpmyadmin/
```

**Database Setup:**

```sql
# Create database via phpMyAdmin or command line
CREATE DATABASE music_locker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Import schema when available
# Import: database/schema.sql via phpMyAdmin
```

**Environment Testing:**

```bash
# Comprehensive system test (NEW - replaces basic tests)
http://{{ngrok provided forward link}}/system-test.php

# Test XAMPP configuration (legacy)
http://{{ngrok provided forward link}}/test-db.php
```

**Ngrok Integration for HTTPS Support:**

```bash
# Start Ngrok tunnel
ngrok http 80

# Access application via HTTPS
https://abc123.ngrok-free.app
```

## Architecture & Technology Stack

### Current Implementation

- **Frontend Framework:** Bootstrap 5.3.2
- **Theme:** Custom dark-techno aesthetic with neon accents (#00d4ff blue, #8a2be2 purple)
- **Typography:** Kode Mono (headings), Titillium Web (body)
- **Icons:** Bootstrap Icons + custom SVG assets
- **JavaScript:** Vanilla JS (minimal, form validation)

### Required Backend Stack

- **PHP:** 8.2+ with extensions (curl, pdo_mysql, mbstring, openssl, fileinfo)
- **Database:** MySQL 8.0 with ACID compliance
- **External API:** Spotify Web API (Client ID: 356702eb81d0499381fcf5222ab757fb)
- **Security:** PHP sessions, bcrypt password hashing, CSRF protection
- **Architecture:** MVC pattern with PSR-4 autoloading

### Project Structure

```
MusicLocker-PHP/
├── music-locker-bootstrap/    # Complete frontend (DO NOT MODIFY)
│   ├── assets/css/dark-techno-theme.css
│   ├── index.html, login.html, register.html, forgot.html
│   └── assets/img/ (SVG icons)
├── public/                    # Web root
│   ├── index.php             # Application entry point
│   └── assets/               # Compiled/processed assets
├── src/                      # PHP application code
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   └── Views/
├── config/                   # Configuration files
├── database/                 # Schema and migrations
└── docs/                     # Project documentation
    ├── api-docs.md           # Spotify API integration guide
    ├── FRD_MusicLocker_PHP.md # Functional requirements
    └── PRP_MusicLocker_PHP.md # Project progress tracking
```

## Core Application Concepts

### User Authentication Flow

- Registration with email verification
- Session-based authentication (no JWT)
- Password reset via email tokens
- Single-session per user (no concurrent logins)

### Music Catalog System

- **Personal Focus:** No music file hosting/streaming, metadata only
- **CRUD Operations:** Add, edit, delete music entries with personal ratings
- **External Lookup:** Spotify API integration for metadata enrichment
- **Organization:** Custom mood/vibe tagging system, personal notes
- **Privacy:** Single-user personal collections, no social features

### Database Schema (To Implement)

- **users:** Authentication, profile, session management
- **music_entries:** Track/album metadata, personal ratings, discovery dates
- **music_notes:** Personal memories, mood context, listening notes
- **tags:** User-defined categorization system with color coding
- **music_entry_tags:** Many-to-many tagging relationships

### Spotify API Integration

- **Client ID:** 356702eb81d0499381fcf5222ab757fb
- **Client Secret:** 3a826c32f5dc41e9939b4ec3229a5647 (stored securely)
- **Authentication:** Client Credentials Flow (simple, no user OAuth required)
- **API Access:** Public catalog data - tracks, artists, albums, metadata
- **No User Data:** No access to user playlists, favorites, or personal data
- **Rate Limiting:** 100 requests/minute with automatic token management
- **Integration:** Direct search and metadata import in music forms
- **Documentation:** Simplified implementation using app credentials only

## Development Priorities

### Phase 1: Foundation (Critical)

1. Database schema implementation from docs/FRD requirements
2. PHP MVC structure with autoloading (Composer)
3. Configuration management (database, API credentials)
4. Basic routing system for clean URLs

### Phase 2: Authentication

1. User registration processing with validation
2. Login/logout functionality with secure sessions
3. Password reset flow with email tokens
4. CSRF protection implementation

### Phase 3: Core Features

1. Music catalog CRUD operations
2. Personal notes and rating system
3. Tag creation and management
4. Search and filtering within personal collection

### Phase 4: External Integration (✅ COMPLETED)

1. ✅ Spotify Web API client implementation (Client Credentials)
2. ✅ Simple search integration (no user auth required)
3. ✅ Music search and metadata import in add/edit forms
4. ✅ Error handling for API rate limits and failures

## Frontend Integration Notes

### Dark-Techno Theme Consistency

- Primary colors: #0a0a0a (background), #00d4ff (accent blue), #8a2be2 (accent purple)
- Maintain neon glow effects: `box-shadow: 0 0 20px var(--accent-blue)`
- Button styles: `.btn-glow` class with gradient backgrounds
- Typography: Preserve font hierarchy (Kode Mono for headings)

### Bootstrap Component Usage

- Form validation classes: `.is-valid`, `.is-invalid`
- Loading states: Use Bootstrap spinners with theme colors
- Modal dialogs: Maintain dark theme overrides
- Responsive breakpoints: Already optimized for mobile-first

### JavaScript Integration

- Form validation: Build on existing patterns in bootstrap files
- AJAX requests: Use fetch API for backend communication
- Progressive enhancement: Ensure functionality without JavaScript
- Toast notifications: Bootstrap toast component with theme styling

## Security Considerations

### Input Validation

- Sanitize all user inputs (HTMLPurifier for rich content)
- Validate email formats, password complexity
- Escape output in all view templates
- Use prepared statements for all database queries

### Session Management

- Regenerate session IDs on login/privilege escalation
- Implement session timeout and cleanup
- Store minimal data in sessions (user ID, authentication status)
- Secure cookie settings (httpOnly, secure for HTTPS)

### API Security

- Store Spotify credentials in environment variables
- Implement token refresh logic with proper error handling
- Rate limiting for user requests to external APIs
- Validate all API responses before processing

## Testing Strategy

### Manual Testing Endpoints

- `/test-db.php` - Database connectivity verification
- `/check-environment.php` - PHP extension and configuration check
- Development tools already created for environment validation

### Integration Points to Test

1. User registration/login flow end-to-end
2. Music search via Spotify API with error handling
3. CRUD operations for music entries with validation
4. Session security (timeout, concurrent login prevention)
5. Cross-browser compatibility (Chrome, Firefox, Safari, Edge)

## Common Development Patterns

### Error Handling

- Use try-catch blocks for database operations
- Log errors to files, display user-friendly messages
- Return JSON responses for AJAX requests
- Maintain error state in forms with Bootstrap validation classes

### Database Operations

- Use PDO with prepared statements exclusively
- Implement transaction handling for complex operations
- Create database abstraction layer for common queries
- Handle connection failures gracefully

### View Rendering

- Create template system compatible with existing Bootstrap markup
- Pass data to views via associative arrays
- Implement CSRF token helpers for forms
- Maintain SEO-friendly URLs with proper meta tags

## Important Implementation Notes

### File Security & Version Control

- **Never commit sensitive data**: Use `.gitignore` to protect config files with API keys
- **Environment Variables**: Store Spotify credentials securely in production
- **XAMPP Development**: Use local virtual host configuration for development

### Latest Spotify API Implementation (2025)

- **Authorization Code Flow**: Recommended over deprecated Implicit Grant
- **PKCE Support**: Enhanced security for public clients (optional for server-side)
- **Token Lifecycle**: 1-hour access tokens, persistent refresh tokens
- **Error Handling**: Comprehensive rate limit and API failure handling

### Development Workflow

1. **Start with Foundation**: Database schema → Configuration → MVC structure
2. **Authentication First**: Secure user management before external integrations
3. **Test Early**: Use provided diagnostic tools throughout development
4. **Maintain Theme**: Preserve dark-techno aesthetic in all backend templates

This Music Locker project transforms personal music discovery into an organized, private digital experience while maintaining the completed dark-techno aesthetic and responsive design.

## Known Issues & Improvements Needed

### Current Problems
- Navigation bar spacing issues across different views
- Recent Activity shows placeholder instead of real data  
- Music card components missing artist icons
- Modal dialogs (Add/Edit/View/Delete) experiencing loading issues
- Audio preview functionality not working properly
- List View toggle not functioning correctly

### Code Quality Issues
- Large controller files that need refactoring
- Mixed concerns in views and controllers
- Need for service layer implementation
- Lack of repository pattern
- Missing comprehensive input validation
- No unit testing framework

## Development Guidelines

### Security Requirements
- Use prepared statements for all database queries
- Validate and sanitize all user inputs
- Implement CSRF protection for forms
- Secure session management with proper regeneration
- Rate limiting for API endpoints

### Code Quality Standards
- Controllers: Maximum 200 lines
- Views: Maximum 300 lines  
- Models: Maximum 150 lines
- Services: Maximum 250 lines
- Follow SOLID principles
- Use dependency injection
- Implement proper error handling

### Testing Strategy
- Unit tests for all business logic
- Integration tests for API endpoints  
- Manual testing for UI workflows
- Performance testing for database queries
