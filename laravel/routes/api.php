<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SpotifyController;
use App\Http\Controllers\Api\MusicController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\PlaylistController;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {

    // ============================================
    // Public Routes (No Authentication Required)
    // ============================================

    // Spotify Search Endpoints
    Route::get('/spotify/search', [SpotifyController::class, 'search']);
    Route::get('/spotify/search-preview', [SpotifyController::class, 'searchWithPreview']);
    Route::get('/spotify/track/{id}', [SpotifyController::class, 'getTrack']);
    Route::get('/spotify/album/{id}', [SpotifyController::class, 'getAlbum']);
    Route::get('/spotify/artist/{id}', [SpotifyController::class, 'getArtist']);
    Route::get('/spotify/album/{id}/tracks', [SpotifyController::class, 'getAlbumTracks']);
    Route::get('/spotify/test', [SpotifyController::class, 'testConnection']);

    // ============================================
    // Protected Routes (Require Authentication)
    // ============================================

    Route::middleware('auth:sanctum')->group(function () {

        // User Profile
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);

        // Music Entries (Full CRUD)
        Route::apiResource('music', MusicController::class)->names([
            'index' => 'api.music.index',
            'store' => 'api.music.store',
            'show' => 'api.music.show',
            'update' => 'api.music.update',
            'destroy' => 'api.music.destroy'
        ]);
        Route::post('/music/{id}/toggle-favorite', [MusicController::class, 'toggleFavorite'])->name('api.music.toggle-favorite');
        Route::get('/music-stats', [MusicController::class, 'stats'])->name('api.music.stats');

        // Tags (Full CRUD)
        Route::apiResource('tags', TagController::class)->names([
            'index' => 'api.tags.index',
            'store' => 'api.tags.store',
            'show' => 'api.tags.show',
            'update' => 'api.tags.update',
            'destroy' => 'api.tags.destroy'
        ]);

        // Playlists (Full CRUD + Track Management)
        Route::apiResource('playlists', PlaylistController::class)->names([
            'index' => 'api.playlists.index',
            'store' => 'api.playlists.store',
            'show' => 'api.playlists.show',
            'update' => 'api.playlists.update',
            'destroy' => 'api.playlists.destroy'
        ]);
        Route::post('/playlists/{id}/add-track', [PlaylistController::class, 'addTrack'])->name('api.playlists.add-track');
        Route::delete('/playlists/{id}/remove-track/{entryId}', [PlaylistController::class, 'removeTrack'])->name('api.playlists.remove-track');
    });
});

/*
|--------------------------------------------------------------------------
| API Route Documentation
|--------------------------------------------------------------------------
|
| PUBLIC ENDPOINTS:
| -----------------
| GET  /api/v1/spotify/search              - Search Spotify catalog
|                                            Params: q, type, limit
|
| GET  /api/v1/spotify/search-preview      - Search with preview URLs focus
|                                            Params: song, artist, limit
|
| GET  /api/v1/spotify/track/{id}          - Get track details by ID
| GET  /api/v1/spotify/album/{id}          - Get album details by ID
| GET  /api/v1/spotify/artist/{id}         - Get artist details by ID
| GET  /api/v1/spotify/album/{id}/tracks   - Get album tracks
| GET  /api/v1/spotify/test                - Test Spotify API connection
|
| PROTECTED ENDPOINTS (Require: Authorization: Bearer {token}):
| --------------------------------------------------------------
| User Profile:
| GET  /api/v1/profile                     - Get user profile with stats
| PUT  /api/v1/profile                     - Update user profile
|
| Music Entries:
| GET    /api/v1/music                     - List user's music (paginated)
|                                            Params: search, genre, rating, favorite, tag_id, sort_by, sort_order, per_page
| POST   /api/v1/music                     - Create music entry
| GET    /api/v1/music/{id}                - Get single music entry
| PUT    /api/v1/music/{id}                - Update music entry
| DELETE /api/v1/music/{id}                - Delete music entry
| POST   /api/v1/music/{id}/toggle-favorite - Toggle favorite status
| GET    /api/v1/music-stats               - Get collection statistics
|
| Tags:
| GET    /api/v1/tags                      - List user's tags
|                                            Params: system, with_usage, sort_by, sort_order
| POST   /api/v1/tags                      - Create tag
| GET    /api/v1/tags/{id}                 - Get tag details
| PUT    /api/v1/tags/{id}                 - Update tag
| DELETE /api/v1/tags/{id}                 - Delete tag (non-system only)
|
| Playlists:
| GET    /api/v1/playlists                 - List user's playlists
|                                            Params: public, with_count, sort_by, sort_order
| POST   /api/v1/playlists                 - Create playlist
| GET    /api/v1/playlists/{id}            - Get playlist with entries
| PUT    /api/v1/playlists/{id}            - Update playlist
| DELETE /api/v1/playlists/{id}            - Delete playlist
| POST   /api/v1/playlists/{id}/add-track  - Add track to playlist
|                                            Body: music_entry_id, position (optional)
| DELETE /api/v1/playlists/{id}/remove-track/{entryId} - Remove track from playlist
|
*/
