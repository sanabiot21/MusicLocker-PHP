<?php

namespace MusicLocker\Controllers;

use MusicLocker\Models\Playlist;
use MusicLocker\Models\MusicEntry;

/**
 * Playlist Controller
 * Music Locker - Team NaturalStupidity
 * 
 * Handles playlist CRUD operations
 */
class PlaylistController extends BaseController
{
    private Playlist $playlistModel;
    private MusicEntry $musicModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->playlistModel = new Playlist();
        $this->musicModel = new MusicEntry();
    }
    
    /**
     * Display user's playlists
     */
    public function index(): void
    {
        $this->requireAuth();
        
        $userId = current_user_id();
        $playlists = $this->playlistModel->getUserPlaylists($userId);
        
        $this->setTitle('Your Playlists');
        $this->addData('current_page', 'playlists');
        $this->addData('playlists', $playlists);
        $this->view('playlists.index');
    }
    
    /**
     * Show individual playlist
     */
    public function show(int $id): void
    {
        $this->requireAuth();
        
        $userId = current_user_id();
        $playlist = $this->playlistModel->findById($id, $userId);
        
        if (!$playlist) {
            http_response_code(404);
            echo "Playlist not found";
            return;
        }
        
        $entries = $this->playlistModel->getPlaylistEntries($id, $userId);
        
        // Load a lightweight list of the user's collection to add from (simple, no over-engineering)
        $userCollection = $this->musicModel->getUserCollection($userId, [
            'limit' => 200,
            'offset' => 0,
            'sort_by' => 'title',
            'sort_order' => 'ASC'
        ]);
        $userEntries = $userCollection['entries'] ?? [];
        
        $this->setTitle($playlist['name']);
        $this->addData('current_page', 'playlists');
        $this->addData('playlist', $playlist);
        $this->addData('entries', $entries);
        $this->addData('userEntries', $userEntries);
        $this->view('playlists.show');
    }
    
    /**
     * Show create playlist form
     */
    public function create(): void
    {
        $this->requireAuth();
        
        if ($this->isMethod('POST')) {
            $this->store();
            return;
        }
        
        $this->setTitle('Create Playlist');
        $this->addData('current_page', 'playlists');
        $this->view('playlists.create');
    }
    
    /**
     * Store new playlist
     */
    private function store(): void
    {
        if (!$this->validateCSRF()) {
            flash('error', 'Invalid security token');
            $this->redirect('/playlists/create');
            return;
        }
        
        $userId = current_user_id();
        
        $playlistId = $this->playlistModel->create([
            'user_id' => $userId,
            'name' => $this->input('name'),
            'description' => $this->input('description'),
            'is_public' => false
        ]);
        
        if ($playlistId) {
            // Log activity with context
            $name = (string)$this->input('name');
            log_activity('playlist_create', 'playlist', $playlistId, 'Created playlist: ' . $name);
            flash('success', 'Playlist created successfully!');
            $this->redirect('/playlists' . "/$playlistId");
        } else {
            flash('error', 'Failed to create playlist');
            $this->redirect('/playlists/create');
        }
    }
    
    /**
     * Show edit playlist form
     */
    public function edit(int $id): void
    {
        $this->requireAuth();
        
        if ($this->isMethod('POST')) {
            $this->update($id);
            return;
        }
        
        $userId = current_user_id();
        $playlist = $this->playlistModel->findById($id, $userId);
        
        if (!$playlist) {
            http_response_code(404);
            echo "Playlist not found";
            return;
        }
        
        $this->setTitle('Edit: ' . $playlist['name']);
        $this->addData('current_page', 'playlists');
        $this->addData('playlist', $playlist);
        $this->view('playlists.edit');
    }
    
    /**
     * Update playlist
     */
    private function update(int $id): void
    {
        if (!$this->validateCSRF()) {
            flash('error', 'Invalid security token');
            $this->redirect('/playlists' . "/$id/edit");
            return;
        }
        
        $userId = current_user_id();
        
        $success = $this->playlistModel->update($id, [
            'name' => $this->input('name'),
            'description' => $this->input('description'),
            'is_public' => false
        ], $userId);
        
        if ($success) {
            // Log activity with context
            $name = (string)$this->input('name');
            log_activity('playlist_update', 'playlist', $id, 'Updated playlist: ' . $name);
            flash('success', 'Playlist updated successfully!');
            $this->redirect('/playlists' . "/$id");
        } else {
            flash('error', 'Failed to update playlist');
            $this->redirect('/playlists' . "/$id/edit");
        }
    }
    
    /**
     * Delete playlist
     */
    public function delete(int $id): void
    {
        $this->requireAuth();
        
        if (!$this->isMethod('POST')) {
            $this->redirect('/playlists');
            return;
        }
        
        if (!$this->validateCSRF()) {
            flash('error', 'Invalid security token');
            $this->redirect('/playlists');
            return;
        }
        
        $userId = current_user_id();
        $success = $this->playlistModel->delete($id, $userId);
        
        if ($success) {
            // Log activity with context
            log_activity('playlist_delete', 'playlist', $id, 'Deleted playlist ID ' . $id);
            flash('success', 'Playlist deleted successfully!');
        } else {
            flash('error', 'Failed to delete playlist');
        }
        
        $this->redirect('/playlists');
    }
    
    /**
     * Add track to playlist (AJAX)
     */
    public function addTrack(): void
    {
        $this->requireAuth();
        
        if (!$this->isMethod('POST')) {
            $this->json(['success' => false, 'error' => 'Invalid method'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $playlistId = $input['playlist_id'] ?? null;
        $musicEntryId = $input['music_entry_id'] ?? null;
        $csrfToken = $input['csrf_token'] ?? '';
        
        if (!validate_csrf($csrfToken)) {
            $this->json(['success' => false, 'error' => 'Invalid token'], 403);
            return;
        }
        
        $userId = current_user_id();
        $success = $this->playlistModel->addTrack($playlistId, $musicEntryId, $userId);
        
        if ($success) {
            // Log activity with context
            $playlist = $this->playlistModel->findById((int)$playlistId, $userId);
            $track = $this->musicModel->findById((int)$musicEntryId, $userId);
            $desc = 'Added track ' . ((int)$musicEntryId) . ( $track ? (' - ' . ($track['title'] ?? '')) : '' ) . ' to ' . ( $playlist['name'] ?? ('playlist ' . (int)$playlistId) );
            log_activity('playlist_add_track', 'playlist', (int)$playlistId, $desc);
            $this->json(['success' => true, 'message' => 'Track added to playlist']);
        } else {
            $this->json(['success' => false, 'error' => 'Failed to add track'], 500);
        }
    }
    
    /**
     * Remove track from playlist (AJAX)
     */
    public function removeTrack(): void
    {
        $this->requireAuth();
        
        if (!$this->isMethod('POST')) {
            $this->json(['success' => false, 'error' => 'Invalid method'], 405);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $playlistId = $input['playlist_id'] ?? null;
        $entryId = $input['entry_id'] ?? null;
        $csrfToken = $input['csrf_token'] ?? '';
        
        if (!validate_csrf($csrfToken)) {
            $this->json(['success' => false, 'error' => 'Invalid token'], 403);
            return;
        }
        
        $userId = current_user_id();
        $success = $this->playlistModel->removeTrack($playlistId, $entryId, $userId);
        
        if ($success) {
            // Log activity with context
            $playlist = $this->playlistModel->findById((int)$playlistId, $userId);
            $track = $this->musicModel->findById((int)$entryId, $userId);
            $desc = 'Removed track ' . ((int)$entryId) . ( $track ? (' - ' . ($track['title'] ?? '')) : '' ) . ' from ' . ( $playlist['name'] ?? ('playlist ' . (int)$playlistId) );
            log_activity('playlist_remove_track', 'playlist', (int)$playlistId, $desc);
            $this->json(['success' => true, 'message' => 'Track removed from playlist']);
        } else {
            $this->json(['success' => false, 'error' => 'Failed to remove track'], 500);
        }
    }
}

