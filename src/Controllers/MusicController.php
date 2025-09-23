<?php

namespace MusicLocker\Controllers;

use MusicLocker\Models\MusicEntry;
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
    private SpotifyService $spotify;
    
    public function __construct()
    {
        parent::__construct();
        $this->musicModel = new MusicEntry();
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
        $favorites = isset($_GET['favorites']);
        $sortBy = $_GET['sort_by'] ?? 'created_at';
        
        $result = $this->musicModel->getUserCollection($userId, [
            'search' => $search,
            'genre' => $genre,  
            'rating' => $rating,
            'favorites' => $favorites,
            'sort_by' => $sortBy,
            'limit' => 20,
            'offset' => ($page - 1) * 20
        ]);
        
        $entries = $result['entries'] ?? [];
        
        $genreResults = $this->musicModel->getPopularGenres($userId, 50);
        $genres = array_column($genreResults, 'genre');
        $stats = $this->musicModel->getCollectionStats($userId);
        
        $this->setTitle('My Music Collection');
        $this->addData('current_page', 'music');
        $this->addData('entries', $entries);
        $this->addData('genres', $genres);
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
        
        $this->setTitle($entry['title']);
        $this->addData('current_page', 'music');
        $this->addData('entry', $entry);
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
        $searchQuery = $this->input('q', '');
        
        if ($searchQuery) {
            $spotifyResults = $this->spotify->search($searchQuery, ['track'], 20);
        }
        
        $this->setTitle('Add Music');
        $this->addData('current_page', 'music-add');
        $this->addData('spotifyResults', $spotifyResults);
        $this->addData('searchQuery', $searchQuery);
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
        
        $entryId = $this->musicModel->create([
            'user_id' => $userId,
            'title' => $this->input('title'),
            'artist' => $this->input('artist'),
            'album' => $this->input('album'),
            'genre' => $this->input('genre'),
            'release_year' => $this->input('release_year'),
            'duration' => $this->input('duration'),
            'spotify_id' => $this->input('spotify_id'),
            'spotify_url' => $this->input('spotify_url'),
            'album_art_url' => $this->input('album_art_url'),
            'personal_rating' => $this->input('personal_rating'),
            'is_favorite' => $this->input('is_favorite') !== null,
            'date_discovered' => date('Y-m-d')
        ]);
        
        if ($entryId) {
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
        
        $this->setTitle('Edit: ' . $entry['title']);
        $this->addData('current_page', 'music');
        $this->addData('entry', $entry);
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
            
            $this->json([
                'success' => true,
                'is_favorite' => $isFavorite
            ]);
        } else {
            $this->json(['success' => false, 'error' => 'Failed to update'], 500);
        }
    }
}