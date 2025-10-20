<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\MusicEntry;
use Illuminate\Http\Request;

/**
 * Playlist Controller
 * Handles CRUD operations for playlists and track management
 */
class PlaylistController extends Controller
{
    /**
     * Display a listing of playlists
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Playlist::where('user_id', $user->id)->withCount('musicEntries');

        // Filter by public/private
        if ($request->filled('public')) {
            $query->where('is_public', $request->boolean('public'));
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $playlists = $query->get();

        return view('playlists.index', [
            'playlists' => $playlists
        ]);
    }

    /**
     * Show the form for creating a new playlist
     */
    public function create()
    {
        return view('playlists.create');
    }

    /**
     * Store a newly created playlist (handles POST from create form)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        $playlist = Playlist::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_public' => false, // Always private
        ]);

        // Log activity
        \App\Models\ActivityLog::logActivity(
            auth()->id(),
            'playlist_create',
            'playlist',
            $playlist->id,
            'Created: ' . $playlist->name
        );

        return redirect()->route('playlists.show', $playlist->id)
            ->with('success', 'Playlist created successfully!');
    }

    /**
     * Display the specified playlist with tracks
     */
    public function show($id)
    {
        $playlist = Playlist::where('user_id', auth()->id())
            ->with(['musicEntries.tags'])
            ->findOrFail($id);

        // Get all user's music entries for "add track" modal
        $availableMusic = MusicEntry::where('user_id', auth()->id())
            ->whereNotIn('id', $playlist->musicEntries->pluck('id'))
            ->orderBy('title')
            ->get();

        return view('playlists.show', [
            'playlist' => $playlist,
            'availableMusic' => $availableMusic
        ]);
    }

    /**
     * Show the form for editing the specified playlist
     */
    public function edit($id)
    {
        $playlist = Playlist::where('user_id', auth()->id())
            ->with(['musicEntries'])
            ->findOrFail($id);

        return view('playlists.edit', [
            'playlist' => $playlist
        ]);
    }

    /**
     * Update the specified playlist (handles POST from edit form)
     */
    public function update(Request $request, $id)
    {
        $playlist = Playlist::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        $playlist->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_public' => false, // Always private
        ]);

        // Log activity
        \App\Models\ActivityLog::logActivity(
            auth()->id(),
            'playlist_update',
            'playlist',
            $playlist->id,
            'Updated: ' . $playlist->name
        );

        return redirect()->route('playlists.show', $playlist->id)
            ->with('success', 'Playlist updated successfully!');
    }

    /**
     * Remove the specified playlist (handles POST from delete form)
     */
    public function destroy($id)
    {
        $playlist = Playlist::where('user_id', auth()->id())->findOrFail($id);
        
        // Log activity before deletion
        \App\Models\ActivityLog::logActivity(
            auth()->id(),
            'playlist_delete',
            'playlist',
            $playlist->id,
            'Deleted: ' . $playlist->name
        );
        
        $playlist->delete();

        return redirect()->route('playlists.index')
            ->with('success', 'Playlist deleted successfully!');
    }

    /**
     * Add a track to the playlist (AJAX/JSON)
     */
    public function addTrack(Request $request)
    {
        try {
            // Accept both JSON request body and form data
            $input = $request->isJson() 
                ? $request->json()->all() 
                : $request->all();

            $playlistId = $input['playlist_id'] ?? null;
            $musicEntryId = $input['music_entry_id'] ?? null;

            if (!$playlistId || !$musicEntryId) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $playlist = Playlist::where('user_id', auth()->id())->findOrFail($playlistId);
            
            // Verify the music entry belongs to the user
            $musicEntry = MusicEntry::where('id', $musicEntryId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // Check if already in playlist
            if ($playlist->musicEntries()->where('music_entries.id', $musicEntry->id)->exists()) {
                return response()->json(['success' => false, 'error' => 'Track is already in this playlist!'], 400);
            }

            // Determine position - use max position + 1 to avoid conflicts
            $maxPosition = \DB::table('playlist_entries')
                ->where('playlist_id', $playlist->id)
                ->max('position');
            $position = ($maxPosition !== null) ? $maxPosition + 1 : 0;

            // Add to playlist
            $playlist->musicEntries()->attach($musicEntry->id, [
                'position' => $position,
                'added_by_user_id' => auth()->id()
            ]);

            // Log activity
            \App\Models\ActivityLog::logActivity(
                auth()->id(),
                'playlist_add_track',
                'playlist',
                $playlist->id,
                'Added track: ' . $musicEntry->title . ' to ' . $playlist->name
            );

            return response()->json(['success' => true, 'message' => 'Track added to playlist!']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'error' => 'Playlist or track not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Error adding track to playlist: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to add track to playlist'], 500);
        }
    }

    /**
     * Remove a track from the playlist (AJAX/JSON)
     */
    public function removeTrack(Request $request)
    {
        try {
            // Accept both JSON request body and form data
            $input = $request->isJson() 
                ? $request->json()->all() 
                : $request->all();

            $playlistId = $input['playlist_id'] ?? null;
            $entryId = $input['entry_id'] ?? null;

            if (!$playlistId || !$entryId) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $playlist = Playlist::where('user_id', auth()->id())->findOrFail($playlistId);

            // Verify the music entry exists in this playlist
            if (!$playlist->musicEntries()->where('music_entries.id', $entryId)->exists()) {
                return response()->json(['success' => false, 'error' => 'Track not found in this playlist!'], 404);
            }

            // Get the music entry for logging
            $musicEntry = MusicEntry::findOrFail($entryId);

            // Remove from playlist
            $playlist->musicEntries()->detach($entryId);

            // Log activity
            \App\Models\ActivityLog::logActivity(
                auth()->id(),
                'playlist_remove_track',
                'playlist',
                $playlist->id,
                'Removed track: ' . $musicEntry->title . ' from ' . $playlist->name
            );

            return response()->json(['success' => true, 'message' => 'Track removed from playlist!']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'error' => 'Playlist or track not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Error removing track from playlist: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to remove track from playlist'], 500);
        }
    }
}
