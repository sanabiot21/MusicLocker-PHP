<?php

namespace MusicLocker\Controllers;

use MusicLocker\Models\MusicEntry;
use MusicLocker\Models\MusicNote;
use MusicLocker\Services\SpotifyService;

/**
 * Simple Music Controller (Clean Architecture)
 * Music Locker - Team NaturalStupidity
 * 
 * Handles all music catalog CRUD operations without modals or complexity
 */
class MusicController extends BaseController
{
    private MusicEntry $musicModel;
    private MusicNote $noteModel;
    private SpotifyService $spotify;
    
    public function __construct()
    {
        parent::__construct();
        $this->musicModel = new MusicEntry();
        $this->noteModel = new MusicNote();
        $this->spotify = new SpotifyService();
    }
    
    /**
     * Display music collection
     */
    public function index(): void
    {
        $this->requireAuth();
        
        $userId = current_user_id();
        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $genre = $_GET['genre'] ?? '';
        $rating = $_GET['rating'] ?? '';
        $tag = $_GET['tag'] ?? '';
        $moodTag = $_GET['mood'] ?? '';
        $favorites = isset($_GET['favorites']);
        $sortBy = $_GET['sort_by'] ?? 'created_at';
        
        // Build filter options including optional mood tag
        $options = [
            'search' => $search,
            'genre' => $genre,
            'rating' => $rating,
            'favorites' => $favorites,
            'sort_by' => $sortBy,
            'limit' => 20,
            'offset' => ($page - 1) * 20
        ];
        
        $tagIdsFilter = [];
        if (!empty($tag)) { $tagIdsFilter[] = $tag; }
        if (!empty($moodTag)) { $tagIdsFilter[] = $moodTag; }
        if (count($tagIdsFilter) > 1) {
            $options['tag_ids'] = $tagIdsFilter;
        } elseif (count($tagIdsFilter) === 1) {
            $options['tag'] = $tagIdsFilter[0];
        }
        
        $result = $this->musicModel->getUserCollection($userId, $options);
        
        $entries = $result['entries'] ?? [];
        
        $genreResults = $this->musicModel->getPopularGenres($userId, 50);
        $genres = array_filter(array_column($genreResults, 'genre'), function($genre) {
            return !empty($genre) && trim($genre) !== '';
        });
        $stats = $this->musicModel->getCollectionStats($userId);
        
        // Get all available tags for filter dropdown
        $tagModel = new \MusicLocker\Models\Tag();
        $availableTags = $tagModel->getUserTags($userId);
        $moodTags = array_values(array_filter($availableTags, function($t) {
            return isset($t['name']) && stripos($t['name'], 'Mood:') === 0;
        }));
        
        $this->setTitle('My Music Collection');
        $this->addData('current_page', 'music');
        $this->addData('entries', $entries);
        $this->addData('genres', $genres);
        $this->addData('availableTags', $availableTags);
        $this->addData('moodTags', $moodTags);
        $this->addData('stats', $stats);
        $this->view('music.index');
    }
    
    /**
     * Show individual music entry
     */
    public function show(int $id): void
    {
        $this->requireAuth();
        
        $userId = current_user_id();
        $entry = $this->musicModel->findById($id, $userId);
        
        if (!$entry) {
            http_response_code(404);
            echo "Music entry not found";
            return;
        }
        
        // Get note if exists
        $note = $this->noteModel->getByMusicEntry($id, $userId);
        
        // Get tags for this entry
        $tags = $this->musicModel->getEntryTags($id);
        
        $this->setTitle($entry['title']);
        $this->addData('current_page', 'music');
        $this->addData('entry', $entry);
        $this->addData('note', $note);
        $this->addData('tags', $tags);
        $this->view('music.show');
    }
    
    /**
     * Show add music page with Spotify search
     */
    public function add(): void
    {
        $this->requireAuth();
        
        if ($this->isMethod('POST')) {
            $this->store();
            return;
        }
        
        $spotifyResults = [];
        $albumTracks = [];
        $albumInfo = null;
        $searchQuery = $this->input('q', '');
        
        if ($searchQuery) {
            // Search for both tracks and albums
            $spotifyResults = $this->spotify->search($searchQuery, ['track', 'album'], 20);
            
            // Check if we have a matching album - prioritize exact or close matches
            $matchingAlbum = null;
            if (!empty($spotifyResults['albums']['items'])) {
                $queryLower = strtolower($searchQuery);
                
                // Try to find best album match
                foreach ($spotifyResults['albums']['items'] as $album) {
                    $albumNameLower = strtolower($album['name']);
                    
                    // Exact match
                    if ($albumNameLower === $queryLower) {
                        $matchingAlbum = $album;
                        break;
                    }
                    
                    // Contains the search query
                    if (!$matchingAlbum && str_contains($albumNameLower, $queryLower)) {
                        $matchingAlbum = $album;
                    }
                }
                
                // If no specific match, use first album
                if (!$matchingAlbum && !empty($spotifyResults['albums']['items'][0])) {
                    $matchingAlbum = $spotifyResults['albums']['items'][0];
                }
            }
            
            // If we found a matching album, fetch its tracks
            if ($matchingAlbum) {
                try {
                    $tracksResponse = $this->spotify->getAlbumTracks($matchingAlbum['id']);
                    
                    if (!empty($tracksResponse['items'])) {
                        // Get genre from album artist
                        $genre = 'Unknown';
                        if (!empty($matchingAlbum['artists'][0]['id'])) {
                            $artistInfo = $this->spotify->getArtist($matchingAlbum['artists'][0]['id']);
                            $genre = !empty($artistInfo['genres']) ? ucfirst($artistInfo['genres'][0]) : 'Unknown';
                        }
                        
                        // Format album tracks
                        foreach ($tracksResponse['items'] as $track) {
                            $albumTracks[] = [
                                'id' => $track['id'],
                                'name' => $track['name'],
                                'artists' => $track['artists'],
                                'artist_names' => implode(', ', array_column($track['artists'], 'name')),
                                'track_number' => $track['track_number'],
                                'duration_ms' => $track['duration_ms'],
                                'spotify_url' => $track['external_urls']['spotify'] ?? null,
                                'album' => [
                                    'name' => $matchingAlbum['name'],
                                    'images' => $matchingAlbum['images'] ?? []
                                ],
                                'genre' => $genre
                            ];
                        }
                        
                        $albumInfo = [
                            'id' => $matchingAlbum['id'],
                            'name' => $matchingAlbum['name'],
                            'artists' => array_column($matchingAlbum['artists'], 'name'),
                            'images' => $matchingAlbum['images'] ?? [],
                            'release_date' => $matchingAlbum['release_date'] ?? null,
                            'total_tracks' => $matchingAlbum['total_tracks'],
                            'genre' => $genre
                        ];
                    }
                } catch (\Exception $e) {
                    error_log("Failed to fetch album tracks: " . $e->getMessage());
                }
            }
            
            // Enhance regular track results with genre information
            if (!empty($spotifyResults['tracks']['items'])) {
                foreach ($spotifyResults['tracks']['items'] as &$track) {
                    $genre = $this->spotify->getGenreFromTrack($track);
                    $track['genre'] = $genre ?? 'Unknown';
                }
            }
        }
        
        // Get all available tags for user
        $userId = current_user_id();
        $tagModel = new \MusicLocker\Models\Tag();
        $availableTags = $tagModel->getUserTags($userId);
        
        $this->setTitle('Add Music');
        $this->addData('current_page', 'music-add');
        $this->addData('spotifyResults', $spotifyResults);
        $this->addData('albumTracks', $albumTracks);
        $this->addData('albumInfo', $albumInfo);
        $this->addData('searchQuery', $searchQuery);
        $this->addData('availableTags', $availableTags);
        $this->view('music.add');
    }
    
    /**
     * Store new music entry
     */
    private function store(): void
    {
        if (!$this->validateCSRF()) {
            flash('error', 'Invalid security token');
            $this->redirect(route_url('music.add'));
            return;
        }
        
        $userId = current_user_id();
        $spotifyId = $this->input('spotify_id');
        
        // Check for duplicate if Spotify ID is provided
        if (!empty($spotifyId)) {
            $existingEntry = $this->musicModel->findBySpotifyId($spotifyId, $userId);
            if ($existingEntry) {
                flash('warning', 'This track is already in your collection!');
                $this->redirect(route_url('music') . '/' . $existingEntry['id']);
                return;
            }
        }
        
        try {
            $entryId = $this->musicModel->create([
                'user_id' => $userId,
                'title' => $this->input('title'),
                'artist' => $this->input('artist'),
                'album' => $this->input('album'),
                'genre' => $this->input('genre'),
                'release_year' => $this->input('release_year'),
                'duration' => $this->input('duration'),
                'spotify_id' => $spotifyId,
                'spotify_url' => $this->input('spotify_url'),
                'album_art_url' => $this->input('album_art_url'),
                'personal_rating' => $this->input('personal_rating'),
                'is_favorite' => $this->input('is_favorite') !== null,
                'date_discovered' => date('Y-m-d')
            ]);
        } catch (\Exception $e) {
            error_log("Music entry creation failed: " . $e->getMessage());
            
            // Check if it's a duplicate key error
            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), '1062')) {
                flash('warning', 'This track is already in your collection.');
            } else {
                flash('error', 'Failed to add music entry. Please try again.');
            }
            
            $this->redirect(route_url('music.add'));
            return;
        }
        
        if ($entryId) {
            // Log activity with context
            $title = (string)$this->input('title');
            $artist = (string)$this->input('artist');
            log_activity('music_create', 'music_entry', $entryId, 'Created: ' . $title . ' by ' . $artist);
            // Save note if provided
            $noteText = $this->input('note_text');
            if (!empty($noteText)) {
                $this->noteModel->create([
                    'music_entry_id' => $entryId,
                    'user_id' => $userId,
                    'note_text' => $noteText,
                    'mood' => $this->input('mood'),
                    'memory_context' => $this->input('memory_context'),
                    'listening_context' => $this->input('listening_context')
                ]);
            }
            
            // Handle tags + sync mood as tag
            $tagIds = $this->input('tags') ?? [];
            $moodText = trim((string)$this->input('mood'));
            if ($moodText !== '') {
                $tagModel = new \MusicLocker\Models\Tag();
                $moodTag = $tagModel->getOrCreateTag($userId, 'Mood: ' . $moodText);
                if (!empty($moodTag['id'])) {
                    $tagIds[] = (int)$moodTag['id'];
                }
            }
            if (!empty($tagIds) && is_array($tagIds)) {
                // De-duplicate tag IDs
                $tagIds = array_values(array_unique(array_map('intval', array_filter($tagIds, fn($v) => is_numeric($v)))));
                if (!empty($tagIds)) {
                    $this->musicModel->addTagsToEntry($entryId, $tagIds);
                }
            }
            
            flash('success', 'Music entry added successfully!');
            $this->redirect(route_url('music'));
        } else {
            flash('error', 'Failed to add music entry');
            $this->redirect(route_url('music.add'));
        }
    }
    
    /**
     * Show edit page
     */
    public function edit(int $id): void
    {
        $this->requireAuth();
        
        if ($this->isMethod('POST')) {
            $this->update($id);
            return;
        }
        
        $userId = current_user_id();
        $entry = $this->musicModel->findById($id, $userId);
        
        if (!$entry) {
            http_response_code(404);
            echo "Music entry not found";
            return;
        }
        
        // Get note if exists
        $note = $this->noteModel->getByMusicEntry($id, $userId);
        
        // Get tags for this entry
        $tags = $this->musicModel->getEntryTags($id);
        
        // Get all available tags for user
        $tagModel = new \MusicLocker\Models\Tag();
        $availableTags = $tagModel->getUserTags($userId);
        
        $this->setTitle('Edit: ' . $entry['title']);
        $this->addData('current_page', 'music');
        $this->addData('entry', $entry);
        $this->addData('note', $note);
        $this->addData('tags', $tags);
        $this->addData('availableTags', $availableTags);
        $this->view('music.edit');
    }
    
    /**
     * Update music entry
     */
    private function update(int $id): void
    {
        if (!$this->validateCSRF()) {
            flash('error', 'Invalid security token');
            $this->redirect(route_url('music') . "/$id/edit");
            return;
        }
        
        $userId = current_user_id();
        
        $success = $this->musicModel->update($id, [
            'title' => $this->input('title'),
            'artist' => $this->input('artist'),
            'album' => $this->input('album'),
            'genre' => $this->input('genre'),
            'release_year' => $this->input('release_year'),
            'personal_rating' => $this->input('personal_rating'),
            'is_favorite' => $this->input('is_favorite') !== null
        ], $userId);
        
        if ($success) {
            // Log activity with context
            $title = (string)$this->input('title');
            $artist = (string)$this->input('artist');
            log_activity('music_update', 'music_entry', $id, 'Updated: ' . $title . ' by ' . $artist);
            // Update or create note
            $noteText = $this->input('note_text');
            if (!empty($noteText)) {
                $moodText = trim((string)$this->input('mood'));
                $this->noteModel->createOrUpdate($id, $userId, [
                    'note_text' => $noteText,
                    'mood' => $moodText,
                    'memory_context' => $this->input('memory_context'),
                    'listening_context' => $this->input('listening_context')
                ]);
            } else {
                // If note text is empty, delete the note
                $this->noteModel->deleteByMusicEntry($id, $userId);
            }
            
            // Update tags (sync mood as tag if provided)
            $tagIds = $this->input('tags') ?? [];
            $moodText = trim((string)$this->input('mood'));
            
            // Get current tags to check for existing mood tags
            $currentTags = $this->musicModel->getEntryTags($id);
            $currentTagIds = array_map(fn($t) => (int)$t['id'], $currentTags);
            
            // Remove any existing mood tags first
            $tagModel = new \MusicLocker\Models\Tag();
            foreach ($currentTags as $tag) {
                if (stripos($tag['name'], 'Mood:') === 0) {
                    $tagIndex = array_search((int)$tag['id'], $currentTagIds);
                    if ($tagIndex !== false) {
                        unset($currentTagIds[$tagIndex]);
                    }
                }
            }
            
            // Add new mood tag if mood text is provided
            if ($moodText !== '') {
                $moodTag = $tagModel->getOrCreateTag($userId, 'Mood: ' . $moodText);
                if (!empty($moodTag['id'])) {
                    $currentTagIds[] = (int)$moodTag['id'];
                }
            }
            
            // Merge with manually selected tags and de-duplicate
            $allTagIds = array_merge($currentTagIds, array_map('intval', array_filter($tagIds, fn($v) => is_numeric($v))));
            $finalTagIds = array_values(array_unique($allTagIds));
            
            $this->musicModel->updateEntryTags($id, $finalTagIds);
            
            flash('success', 'Music entry updated successfully!');
            $this->redirect(route_url('music') . "/$id");
        } else {
            flash('error', 'Failed to update music entry');
            $this->redirect(route_url('music') . "/$id/edit");
        }
    }
    
    /**
     * Delete music entry
     */
    public function delete(int $id): void
    {
        $this->requireAuth();
        
        if (!$this->isMethod('POST')) {
            $this->redirect(route_url('music'));
            return;
        }
        
        if (!$this->validateCSRF()) {
            flash('error', 'Invalid security token');
            $this->redirect(route_url('music'));
            return;
        }
        
        $userId = current_user_id();
        
        $success = $this->musicModel->delete($id, $userId);
        
        if ($success) {
            // Log activity with context
            $entry = $this->musicModel->findById($id, $userId);
            $desc = $entry ? ('Deleted: ' . ($entry['title'] ?? 'Unknown') . ' by ' . ($entry['artist'] ?? 'Unknown')) : 'Deleted music entry';
            log_activity('music_delete', 'music_entry', $id, $desc);
            flash('success', 'Music entry deleted successfully!');
        } else {
            flash('error', 'Failed to delete music entry');
        }
        
        $this->redirect(route_url('music'));
    }
    
    /**
     * Toggle favorite status (AJAX)
     */
    public function toggleFavorite(): void
    {
        $this->requireAuth();
        
        if (!$this->isMethod('POST')) {
            $this->json(['success' => false, 'error' => 'Invalid method'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $entryId = $input['entry_id'] ?? null;
        $csrfToken = $input['csrf_token'] ?? '';
        
        if (!validate_csrf($csrfToken)) {
            $this->json(['success' => false, 'error' => 'Invalid token'], 403);
            return;
        }
        
        $userId = current_user_id();
        $success = $this->musicModel->toggleFavorite($entryId, $userId);
        
        if ($success) {
            // Get the updated entry to return current favorite status
            $entry = $this->musicModel->findById($entryId, $userId);
            $isFavorite = $entry ? (bool)$entry['is_favorite'] : false;
            
            // Log activity (favorite toggled) with context
            $desc = $entry ? ('Toggled favorite: ' . ($entry['title'] ?? 'Unknown') . ' by ' . ($entry['artist'] ?? 'Unknown')) : 'Toggled favorite';
            log_activity('music_toggle_favorite', 'music_entry', $entryId, $desc);
            
            $this->json([
                'success' => true,
                'is_favorite' => $isFavorite
            ]);
        } else {
            $this->json(['success' => false, 'error' => 'Failed to update'], 500);
        }
    }
}