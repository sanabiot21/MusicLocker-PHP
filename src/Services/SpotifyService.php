<?php

namespace MusicLocker\Services;

use Exception;

/**
 * Simple Spotify Web API Service
 * Music Locker - Team NaturalStupidity
 * 
 * Uses Client Credentials Flow - no user OAuth required
 * Makes API calls using app credentials only for public data access
 */
class SpotifyService
{
    private string $clientId;
    private string $clientSecret;
    private string $apiBaseUrl;
    private ?string $accessToken = null;
    private int $tokenExpiry = 0;
    
    public function __construct()
    {
        $this->clientId = config('spotify.client_id');
        $this->clientSecret = config('spotify.client_secret');
        $this->apiBaseUrl = config('spotify.api.base_url');
        
        if (!$this->clientId || !$this->clientSecret) {
            throw new Exception('Spotify API credentials not configured');
        }
    }
    
    /**
     * Get access token using Client Credentials Flow
     */
    private function getAccessToken(): string
    {
        // Check if current token is still valid
        if ($this->accessToken && time() < $this->tokenExpiry) {
            return $this->accessToken;
        }
        
        $tokenUrl = 'https://accounts.spotify.com/api/token';
        
        $postData = http_build_query([
            'grant_type' => 'client_credentials'
        ]);
        
        $headers = [
            'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($postData)
        ];
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $postData,
                'timeout' => 30,
                'ignore_errors' => true
            ]
        ]);
        
        $response = file_get_contents($tokenUrl, false, $context);
        
        if ($response === false) {
            throw new Exception('Failed to get Spotify access token');
        }
        
        $tokenData = json_decode($response, true);
        
        if (isset($tokenData['error'])) {
            throw new Exception('Spotify API error: ' . $tokenData['error_description']);
        }
        
        $this->accessToken = $tokenData['access_token'];
        $this->tokenExpiry = time() + $tokenData['expires_in'] - 60; // Refresh 1 minute early
        
        return $this->accessToken;
    }
    
    /**
     * Make API request to Spotify with cURL fallback
     */
    private function makeRequest(string $endpoint): array
    {
        $token = $this->getAccessToken();
        $url = $this->apiBaseUrl . '/' . ltrim($endpoint, '/');
        
        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ];
        
        // Try cURL first (more reliable)
        if (function_exists('curl_init')) {
            $response = $this->makeRequestWithCurl($url, $headers);
        } else {
            // Fallback to file_get_contents
            $response = $this->makeRequestWithFileGetContents($url, $headers);
        }
        
        if ($response === false) {
            throw new Exception('Failed to make Spotify API request');
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['error'])) {
            throw new Exception('Spotify API error: ' . $data['error']['message']);
        }
        
        return $data;
    }
    
    /**
     * Make request using cURL (preferred method)
     */
    private function makeRequestWithCurl(string $url, array $headers): string
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false, // For development with ngrok
            CURLOPT_USERAGENT => 'MusicLocker/1.0'
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        
        curl_close($curl);
        
        if ($response === false) {
            throw new Exception('cURL error: ' . $error);
        }
        
        if ($httpCode >= 400) {
            throw new Exception('HTTP error: ' . $httpCode);
        }
        
        return $response;
    }
    
    /**
     * Make request using file_get_contents (fallback method)
     */
    private function makeRequestWithFileGetContents(string $url, array $headers): string
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", $headers),
                'timeout' => 30,
                'ignore_errors' => true
            ]
        ]);
        
        $response = file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception('file_get_contents failed');
        }
        
        return $response;
    }
    
    /**
     * Search for tracks, artists, albums
     */
    public function search(string $query, array $types = ['track'], int $limit = 20): array
    {
        $typeString = implode(',', $types);
        $endpoint = 'search?' . http_build_query([
            'q' => $query,
            'type' => $typeString,
            'limit' => $limit
        ]);
        
        return $this->makeRequest($endpoint);
    }
    
    /**
     * Enhanced search specifically for tracks with preview URLs
     * Implements preview-finding strategy by searching multiple markets and variations
     */
    public function searchWithPreview(string $songName, string $artistName = '', int $limit = 20): array
    {
        // Build search query with artist if provided
        $query = $songName;
        if (!empty($artistName)) {
            $query .= " artist:" . $artistName;
        }
        
        // Try default search first (no market specified)
        $allResults = [];
        
        try {
            $endpoint = 'search?' . http_build_query([
                'q' => $query,
                'type' => 'track',
                'limit' => min($limit * 2, 50) // Get more results to filter for previews
            ]);
            
            $response = $this->makeRequest($endpoint);
            
            if (isset($response['tracks']['items'])) {
                foreach ($response['tracks']['items'] as $track) {
                    // Only include tracks that have preview URLs
                    if (!empty($track['preview_url'])) {
                        $trackId = $track['id'];
                        // Avoid duplicates by using track ID as key
                        if (!isset($allResults[$trackId])) {
                            $allResults[$trackId] = $track;
                        }
                    }
                    
                    // Stop early if we have enough
                    if (count($allResults) >= $limit) {
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Default preview search failed: " . $e->getMessage());
        }
        
        // If we still don't have enough results, try specific markets (limited to 2 markets)
        if (count($allResults) < $limit) {
            $priorityMarkets = ['US', 'GB']; // Limit to 2 markets for speed
            
            foreach ($priorityMarkets as $market) {
                if (count($allResults) >= $limit) break;
                
                try {
                    $endpoint = 'search?' . http_build_query([
                        'q' => $query,
                        'type' => 'track',
                        'limit' => 20,
                        'market' => $market
                    ]);
                    
                    $response = $this->makeRequest($endpoint);
                    
                    if (isset($response['tracks']['items'])) {
                        foreach ($response['tracks']['items'] as $track) {
                            if (!empty($track['preview_url'])) {
                                $trackId = $track['id'];
                                if (!isset($allResults[$trackId])) {
                                    $allResults[$trackId] = $track;
                                }
                            }
                            
                            if (count($allResults) >= $limit) break;
                        }
                    }
                    
                } catch (Exception $e) {
                    error_log("Preview search failed for market {$market}: " . $e->getMessage());
                    continue;
                }
            }
        }
        
        // If still no results with previews, try alternative search terms
        if (empty($allResults) && !empty($artistName)) {
            try {
                // Try searching without artist constraint
                $fallbackQuery = $songName;
                $endpoint = 'search?' . http_build_query([
                    'q' => $fallbackQuery,
                    'type' => 'track',
                    'limit' => $limit * 2 // Search more to find ones with previews
                ]);
                
                $response = $this->makeRequest($endpoint);
                
                if (isset($response['tracks']['items'])) {
                    foreach ($response['tracks']['items'] as $track) {
                        if (!empty($track['preview_url'])) {
                            $trackId = $track['id'];
                            if (!isset($allResults[$trackId])) {
                                $allResults[$trackId] = $track;
                            }
                        }
                        
                        // Stop after finding enough
                        if (count($allResults) >= $limit) {
                            break;
                        }
                    }
                }
            } catch (Exception $e) {
                error_log("Fallback preview search failed: " . $e->getMessage());
            }
        }
        
        // Convert associative array back to indexed array and limit results
        $finalResults = array_values($allResults);
        $finalResults = array_slice($finalResults, 0, $limit);
        
        // Return in the same format as regular search
        return [
            'tracks' => [
                'items' => $finalResults,
                'total' => count($finalResults),
                'limit' => $limit,
                'offset' => 0
            ]
        ];
    }

    /**
     * Get track details by ID
     */
    public function getTrack(string $trackId): array
    {
        return $this->makeRequest("tracks/{$trackId}");
    }
    
    /**
     * Get artist details by ID
     */
    public function getArtist(string $artistId): array
    {
        return $this->makeRequest("artists/{$artistId}");
    }
    
    /**
     * Get album details by ID
     */
    public function getAlbum(string $albumId): array
    {
        return $this->makeRequest("albums/{$albumId}");
    }
    
    /**
     * Get album tracks by ID
     */
    public function getAlbumTracks(string $albumId, int $limit = 50): array
    {
        $endpoint = "albums/{$albumId}/tracks?" . http_build_query([
            'limit' => $limit
        ]);
        
        return $this->makeRequest($endpoint);
    }
    
    /**
     * Get genre from artist information
     * Fetches the first artist's genre since tracks don't have genre info directly
     */
    public function getGenreFromTrack(array $track): ?string
    {
        try {
            // Get first artist ID
            if (empty($track['artists']) || empty($track['artists'][0]['id'])) {
                return null;
            }
            
            $artistId = $track['artists'][0]['id'];
            $artistData = $this->getArtist($artistId);
            
            // Return first genre if available
            if (!empty($artistData['genres'])) {
                // Capitalize first letter of each word for better display
                return ucwords(str_replace('-', ' ', $artistData['genres'][0]));
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("Genre fetch error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Extract useful metadata from track data
     */
    public function extractTrackMetadata(array $track): array
    {
        $artists = array_map(fn($artist) => $artist['name'], $track['artists'] ?? []);
        $albumImages = $track['album']['images'] ?? [];
        
        return [
            'spotify_id' => $track['id'],
            'title' => $track['name'],
            'artist' => implode(', ', $artists),
            'album' => $track['album']['name'] ?? null,
            'release_year' => isset($track['album']['release_date']) 
                ? (int)substr($track['album']['release_date'], 0, 4) 
                : null,
            'duration' => intval(($track['duration_ms'] ?? 0) / 1000),
            'duration_formatted' => $this->formatDuration(intval(($track['duration_ms'] ?? 0) / 1000)),
            'preview_url' => $track['preview_url'] ?? null,
            'spotify_url' => $track['external_urls']['spotify'] ?? null,
            'album_art_url' => !empty($albumImages) ? $albumImages[0]['url'] : null,
            'popularity' => $track['popularity'] ?? 0,
            'genres' => $track['album']['genres'] ?? []
        ];
    }
    
    /**
     * Format duration in seconds to mm:ss
     */
    private function formatDuration(int $seconds): string
    {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }
    
    /**
     * Test API connection
     */
    public function testConnection(): bool
    {
        try {
            $this->search('test', ['track'], 1);
            return true;
        } catch (Exception $e) {
            error_log("Spotify test connection failed: " . $e->getMessage());
            return false;
        }
    }
}