<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'spotify' => [
        // Spotify Application Credentials
        'client_id' => env('SPOTIFY_CLIENT_ID'),
        'client_secret' => env('SPOTIFY_CLIENT_SECRET'),

        // Required Scopes for Music Locker
        'scopes' => [
            'user-read-private',    // Access user's profile information
            'user-read-email',      // Access user's email address
            'playlist-read-private', // Access user's private playlists (future feature)
            'playlist-read-collaborative', // Access collaborative playlists (future feature)
        ],

        // API Configuration
        'api' => [
            'base_url' => 'https://api.spotify.com/v1',
            'version' => 'v1',
            'timeout' => 30, // seconds
            'user_agent' => 'Music Locker/1.0.0',
        ],

        // Rate Limiting Configuration
        'rate_limit' => [
            'requests_per_minute' => 100,
            'retry_attempts' => 3,
            'backoff_multiplier' => 2, // exponential backoff
            'max_backoff' => 60, // max seconds to wait
        ],

        // Token Management
        'tokens' => [
            'access_token_lifetime' => 3600, // 1 hour in seconds
            'refresh_before_expiry' => 300, // refresh 5 minutes before expiry
            'encryption' => true, // encrypt tokens in database
        ],

        // Search Configuration
        'search' => [
            'default_limit' => 20,
            'max_limit' => 50,
            'default_types' => ['track', 'artist', 'album'],
            'market' => env('SPOTIFY_MARKET', 'US'), // ISO 3166-1 alpha-2 country code
        ],

        // Cache Configuration for API Responses
        'cache' => [
            'enabled' => env('SPOTIFY_CACHE_ENABLED', true),
            'ttl' => [
                'track_details' => 3600, // 1 hour
                'artist_details' => 7200, // 2 hours
                'album_details' => 7200, // 2 hours
                'search_results' => 1800, // 30 minutes
            ],
        ],

        // Error Handling Configuration
        'error_handling' => [
            'log_api_errors' => true,
            'max_retries' => 3,
            'retry_status_codes' => [429, 500, 502, 503, 504],
            'fallback_enabled' => false, // fallback to cached data on API failure
        ],

        // Development/Debug Settings
        'debug' => [
            'log_requests' => env('SPOTIFY_DEBUG_LOG', false),
            'log_responses' => env('SPOTIFY_DEBUG_LOG', false),
            'mock_api' => env('SPOTIFY_MOCK_API', false), // for testing
        ],

        // URLs for OAuth Flow
        'urls' => [
            'authorization' => 'https://accounts.spotify.com/authorize',
            'token' => 'https://accounts.spotify.com/api/token',
            'me' => 'https://api.spotify.com/v1/me',
        ],

        // Default Headers for API Requests
        'headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'Music Locker/1.0.0 (https://musiclocker.local)',
        ],
    ],

];
