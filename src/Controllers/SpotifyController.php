<?php

namespace MusicLocker\Controllers;

use MusicLocker\Services\SpotifyService;
use Exception;

/**
 * Simple Spotify Integration Controller
 * Music Locker - Team NaturalStupidity
 * 
 * Handles Spotify search and metadata retrieval using Client Credentials Flow
 * No user OAuth required - uses app credentials only
 */
class SpotifyController extends BaseController
{
    private SpotifyService $spotifyService;
    
    public function __construct()
    {
        parent::__construct();
        $this->spotifyService = new SpotifyService();
    }
    
    /**
     * Search for music via Spotify API (Public endpoint - no user auth required)
     */
    public function search(): void
    {
        if (!$this->isMethod('GET')) {
            json_response(['error' => 'Method not allowed'], 405);
            return;
        }
        
        $query = trim($this->input('q', ''));
        $type = $this->input('type', 'track');
        $limit = min((int)$this->input('limit', 20), 50);
        
        if (empty($query)) {
            json_response(['error' => 'Search query is required'], 400);
            return;
        }
        
        try {
            $types = explode(',', $type);
            $results = $this->spotifyService->search($query, $types, $limit);
            
            // Format results for frontend consumption
            $formattedResults = $this->formatSearchResults($results);
            
            json_response([
                'success' => true,
                'query' => $query,
                'results' => $formattedResults,
                'total' => $this->getTotalFromResults($results)
            ]);
            
        } catch (Exception $e) {
            error_log("Spotify search error: " . $e->getMessage());
            json_response(['error' => 'Search failed: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Enhanced search for tracks with preview URLs (Public endpoint - no user auth required)
     * Uses multi-market search strategy to find tracks with available preview URLs
     */
    public function searchWithPreview(): void
    {
        if (!$this->isMethod('GET')) {
            json_response(['error' => 'Method not allowed'], 405);
            return;
        }
        
        $songName = trim($this->input('song', ''));
        $artistName = trim($this->input('artist', ''));
        $limit = min((int)$this->input('limit', 10), 20);
        
        if (empty($songName)) {
            json_response(['error' => 'Song name is required'], 400);
            return;
        }
        
        try {
            $results = $this->spotifyService->searchWithPreview($songName, $artistName, $limit);
            
            // Format results for frontend consumption
            $formattedResults = $this->formatSearchResults($results);
            
            json_response([
                'success' => true,
                'query' => $songName . (!empty($artistName) ? ' by ' . $artistName : ''),
                'results' => $formattedResults,
                'total' => count($formattedResults),
                'preview_focused' => true,
                'message' => count($formattedResults) > 0 
                    ? 'Found ' . count($formattedResults) . ' tracks with preview URLs'
                    : 'No tracks with preview URLs found for this search'
            ]);
            
        } catch (Exception $e) {
            error_log("Spotify preview search error: " . $e->getMessage());
            json_response(['error' => 'Preview search failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get track details by Spotify ID (Public endpoint - no user auth required)
     */
    public function track(): void
    {
        $trackId = $this->input('id');
        
        if (!$trackId) {
            json_response(['error' => 'Track ID is required'], 400);
            return;
        }
        
        try {
            $track = $this->spotifyService->getTrack($trackId);
            $metadata = $this->spotifyService->extractTrackMetadata($track);
            
            json_response([
                'success' => true,
                'track' => $track,
                'metadata' => $metadata
            ]);
            
        } catch (Exception $e) {
            error_log("Spotify track fetch error: " . $e->getMessage());
            json_response(['error' => 'Failed to fetch track details'], 500);
        }
    }
    
    /**
     * Get album tracks by Spotify album ID (Public endpoint - no user auth required)
     */
    public function albumTracks(): void
    {
        if (!$this->isMethod('GET')) {
            json_response(['error' => 'Method not allowed'], 405);
            return;
        }
        
        $albumId = $this->input('id');
        
        if (!$albumId) {
            json_response(['error' => 'Album ID is required'], 400);
            return;
        }
        
        try {
            // Get album details and tracks
            $album = $this->spotifyService->getAlbum($albumId);
            $tracksResponse = $this->spotifyService->getAlbumTracks($albumId);
            
            // Get genre from album artists
            $genre = 'Unknown';
            if (!empty($album['artists'][0]['id'])) {
                $artistInfo = $this->spotifyService->getArtist($album['artists'][0]['id']);
                $genre = !empty($artistInfo['genres']) ? ucfirst($artistInfo['genres'][0]) : 'Unknown';
            }
            
            // Format tracks with album context
            $tracks = array_map(function($track) use ($album, $genre) {
                $artists = array_map(fn($artist) => $artist['name'], $track['artists']);
                return [
                    'id' => $track['id'],
                    'name' => $track['name'],
                    'artists' => $artists,
                    'artist_names' => implode(', ', $artists),
                    'track_number' => $track['track_number'],
                    'duration_ms' => $track['duration_ms'],
                    'preview_url' => $track['preview_url'] ?? null,
                    'spotify_url' => $track['external_urls']['spotify'] ?? null,
                    // Include album context
                    'album' => [
                        'id' => $album['id'],
                        'name' => $album['name'],
                        'images' => $album['images'] ?? [],
                        'release_date' => $album['release_date'] ?? null,
                        'total_tracks' => $album['total_tracks']
                    ],
                    'genre' => $genre
                ];
            }, $tracksResponse['items'] ?? []);
            
            json_response([
                'success' => true,
                'album' => [
                    'id' => $album['id'],
                    'name' => $album['name'],
                    'artists' => array_map(fn($artist) => $artist['name'], $album['artists']),
                    'release_date' => $album['release_date'] ?? null,
                    'total_tracks' => $album['total_tracks'],
                    'images' => $album['images'] ?? [],
                    'genre' => $genre
                ],
                'tracks' => $tracks,
                'total' => count($tracks)
            ]);
            
        } catch (Exception $e) {
            error_log("Spotify album tracks fetch error: " . $e->getMessage());
            json_response(['error' => 'Failed to fetch album tracks: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Test Spotify API connection
     */
    public function test(): void
    {
        try {
            $isConnected = $this->spotifyService->testConnection();
            
            json_response([
                'success' => true,
                'connected' => $isConnected,
                'message' => $isConnected ? 'Spotify API connected successfully' : 'Failed to connect to Spotify API'
            ]);
            
        } catch (Exception $e) {
            json_response([
                'success' => false,
                'connected' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Format search results for frontend
     */
    private function formatSearchResults(array $results): array
    {
        $formatted = [];
        
        if (isset($results['tracks']['items'])) {
            $formatted['tracks'] = array_map(function($track) {
                $artists = array_map(fn($artist) => $artist['name'], $track['artists']);
                $albumImages = $track['album']['images'] ?? [];
                
                return [
                    'id' => $track['id'],
                    'name' => $track['name'],
                    'artists' => $artists,
                    'artist_names' => implode(', ', $artists),
                    'album' => $track['album']['name'],
                    'release_date' => $track['album']['release_date'] ?? null,
                    'duration_ms' => $track['duration_ms'],
                    'duration_formatted' => format_duration(intval($track['duration_ms'] / 1000)),
                    'preview_url' => $track['preview_url'],
                    'spotify_url' => $track['external_urls']['spotify'] ?? null,
                    'image_url' => !empty($albumImages) ? $albumImages[0]['url'] : null,
                    'popularity' => $track['popularity'] ?? 0
                ];
            }, $results['tracks']['items']);
        }
        
        if (isset($results['artists']['items'])) {
            $formatted['artists'] = array_map(function($artist) {
                $images = $artist['images'] ?? [];
                
                return [
                    'id' => $artist['id'],
                    'name' => $artist['name'],
                    'genres' => $artist['genres'] ?? [],
                    'popularity' => $artist['popularity'] ?? 0,
                    'followers' => $artist['followers']['total'] ?? 0,
                    'image_url' => !empty($images) ? $images[0]['url'] : null,
                    'spotify_url' => $artist['external_urls']['spotify'] ?? null
                ];
            }, $results['artists']['items']);
        }
        
        if (isset($results['albums']['items'])) {
            $formatted['albums'] = array_map(function($album) {
                $artists = array_map(fn($artist) => $artist['name'], $album['artists']);
                $images = $album['images'] ?? [];
                
                return [
                    'id' => $album['id'],
                    'name' => $album['name'],
                    'artists' => $artists,
                    'artist_names' => implode(', ', $artists),
                    'release_date' => $album['release_date'] ?? null,
                    'total_tracks' => $album['total_tracks'] ?? 0,
                    'image_url' => !empty($images) ? $images[0]['url'] : null,
                    'spotify_url' => $album['external_urls']['spotify'] ?? null,
                    'album_type' => $album['album_type'] ?? 'album'
                ];
            }, $results['albums']['items']);
        }
        
        return $formatted;
    }
    
    /**
     * Get total count from search results
     */
    private function getTotalFromResults(array $results): int
    {
        $total = 0;
        
        if (isset($results['tracks']['total'])) {
            $total += $results['tracks']['total'];
        }
        
        if (isset($results['artists']['total'])) {
            $total += $results['artists']['total'];
        }
        
        if (isset($results['albums']['total'])) {
            $total += $results['albums']['total'];
        }
        
        return $total;
    }
}