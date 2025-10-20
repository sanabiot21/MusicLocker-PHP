<?php

namespace App\Http\Controllers;

use App\Models\MusicEntry;
use App\Models\Tag;
use App\Models\MusicNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Music Controller
 * Handles CRUD operations for music entries
 */
class MusicController extends Controller
{
    /**
     * Display a listing of music entries with filters
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = MusicEntry::where('user_id', $user->id)->with(['tags']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ILIKE', "%{$search}%")
                  ->orWhere('artist', 'ILIKE', "%{$search}%")
                  ->orWhere('album', 'ILIKE', "%{$search}%");
            });
        }

        // Genre filter
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }

        // Rating filter
        if ($request->filled('rating')) {
            $query->where('personal_rating', '>=', $request->rating);
        }

        // Favorite filter
        if ($request->filled('favorite')) {
            $query->where('is_favorite', true);
        }

        // Tag filter
        if ($request->filled('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag_id);
            });
        }

        // Mood filter
        if ($request->filled('mood')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->mood);
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 12);
        $musicEntries = $query->paginate($perPage)->withQueryString();

        // Get all tags for filter dropdown
        $tags = Tag::where('user_id', $user->id)->orderBy('name')->get();

        // Get mood tags (non-system tags that contain "mood" in name or are mood-related)
        $moodTags = Tag::where('user_id', $user->id)
            ->where(function($q) {
                $q->where('name', 'ILIKE', '%mood%')
                  ->orWhere('name', 'ILIKE', '%chill%')
                  ->orWhere('name', 'ILIKE', '%workout%')
                  ->orWhere('name', 'ILIKE', '%party%')
                  ->orWhere('name', 'ILIKE', '%study%')
                  ->orWhere('name', 'ILIKE', '%nostalgic%');
            })
            ->orderBy('name')
            ->get();

        // Get unique genres for filter
        $genres = MusicEntry::where('user_id', $user->id)
            ->whereNotNull('genre')
            ->distinct()
            ->pluck('genre')
            ->sort()
            ->values();

        // Get sort options
        $sortOptions = [
            'created_at' => 'Date Added',
            'title' => 'Title',
            'artist' => 'Artist',
            'album' => 'Album',
            'personal_rating' => 'Rating',
            'is_favorite' => 'Favorites First'
        ];

        // Get stats for display
        $stats = [
            'total_entries' => MusicEntry::where('user_id', $user->id)->count(),
            'favorite_entries' => MusicEntry::where('user_id', $user->id)->where('is_favorite', true)->count(),
            'five_star_entries' => MusicEntry::where('user_id', $user->id)->where('personal_rating', 5)->count(),
        ];

        return view('music.index', [
            'entries' => $musicEntries,
            'tags' => $tags,
            'moodTags' => $moodTags,
            'genres' => $genres,
            'sortOptions' => $sortOptions,
            'stats' => $stats,
            'filters' => $request->only(['search', 'genre', 'rating', 'favorite', 'tag_id', 'mood', 'sort_by', 'sort_order'])
        ]);
    }

    /**
     * Show the form for creating a new music entry
     */
    public function create()
    {
        $tags = Tag::where('user_id', auth()->id())->orderBy('name')->get();

        return view('music.create', [
            'tags' => $tags,
            'availableTags' => $tags
        ]);
    }

    /**
     * Store a newly created music entry
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'album' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:100',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'duration' => 'nullable|integer|min:0',
            'personal_rating' => 'nullable|integer|min:1|max:5',
            'spotify_id' => 'nullable|string|max:255',
            'spotify_url' => 'nullable|url|max:500',
            'preview_url' => 'nullable|url|max:500',
            'album_art_url' => 'nullable|url|max:500',
            'note_text' => 'nullable|string|max:2000',
            'mood' => 'nullable|string|max:255',
            'memory_context' => 'nullable|string|max:1000',
            'listening_context' => 'nullable|string|max:1000',
            'is_favorite' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        // Check if track with same spotify_id already exists for this user
        if (!empty($validated['spotify_id'])) {
            $existing = MusicEntry::where('user_id', auth()->id())
                ->where('spotify_id', $validated['spotify_id'])
                ->first();
            
            if ($existing) {
                return redirect()->route('music.show', $existing->id)
                    ->with('info', 'This track is already in your collection! Redirecting to the existing entry.');
            }
        }

        // Create the music entry
        $musicEntry = MusicEntry::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'artist' => $validated['artist'],
            'album' => $validated['album'] ?? null,
            'genre' => $validated['genre'] ?? 'Unknown',
            'release_year' => $validated['release_year'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'personal_rating' => $validated['personal_rating'] ?? 3,
            'spotify_id' => $validated['spotify_id'] ?? null,
            'spotify_url' => $validated['spotify_url'] ?? null,
            'preview_url' => $validated['preview_url'] ?? null,
            'album_art_url' => $validated['album_art_url'] ?? null,
            'is_favorite' => filter_var($validated['is_favorite'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ]);

        // Log activity
        \App\Models\ActivityLog::logActivity(
            auth()->id(),
            'music_create',
            'music_entry',
            $musicEntry->id,
            'Created: ' . $musicEntry->title . ' by ' . $musicEntry->artist
        );

        // Create personal note if any note fields are filled
        if (!empty($validated['note_text']) || !empty($validated['mood']) || !empty($validated['memory_context']) || !empty($validated['listening_context'])) {
            MusicNote::create([
                'music_entry_id' => $musicEntry->id,
                'user_id' => auth()->id(),
                'note_text' => $validated['note_text'] ?? null,
                'mood' => $validated['mood'] ?? null,
                'memory_context' => $validated['memory_context'] ?? null,
                'listening_context' => $validated['listening_context'] ?? null,
            ]);
        }

        // Attach tags if provided
        if (!empty($validated['tags'])) {
            $musicEntry->tags()->attach($validated['tags']);
        }

        return redirect()->route('music.show', $musicEntry->id)
            ->with('success', 'Music entry added successfully!');
    }

    /**
     * Display the specified music entry
     */
    public function show($id)
    {
        $entry = MusicEntry::where('user_id', auth()->id())
            ->with(['tags', 'notes'])
            ->findOrFail($id);

        return view('music.show', [
            'entry' => $entry
        ]);
    }

    /**
     * Show the form for editing the specified music entry
     */
    public function edit($id)
    {
        $entry = MusicEntry::where('user_id', auth()->id())
            ->with(['tags', 'notes'])
            ->findOrFail($id);

        $tags = Tag::where('user_id', auth()->id())->orderBy('name')->get();

        return view('music.edit', [
            'entry' => $entry,
            'tags' => $tags,
            'availableTags' => $tags,
            'note' => $entry->notes()->first()
        ]);
    }

    /**
     * Update the specified music entry
     */
    public function update(Request $request, $id)
    {
        $entry = MusicEntry::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'album' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:100',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'duration' => 'nullable|integer|min:0',
            'personal_rating' => 'nullable|integer|min:1|max:5',
            'album_art_url' => 'nullable|url|max:500',
            'spotify_url' => 'nullable|url|max:500',
            'note_text' => 'nullable|string|max:2000',
            'mood' => 'nullable|string|max:255',
            'memory_context' => 'nullable|string|max:1000',
            'listening_context' => 'nullable|string|max:1000',
            'is_favorite' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        // Update the entry - preserve Spotify fields if not being edited
        $entry->update([
            'title' => $validated['title'],
            'artist' => $validated['artist'],
            'album' => $validated['album'] ?? null,
            'genre' => $validated['genre'] ?? 'Unknown',
            'release_year' => $validated['release_year'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'personal_rating' => $validated['personal_rating'] ?? 3,
            'album_art_url' => $validated['album_art_url'] ?? $entry->album_art_url,
            'spotify_url' => $validated['spotify_url'] ?? $entry->spotify_url,
            'is_favorite' => filter_var($validated['is_favorite'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ]);

        // Log activity
        \App\Models\ActivityLog::logActivity(
            auth()->id(),
            'music_update',
            'music_entry',
            $entry->id,
            'Updated: ' . $entry->title . ' by ' . $entry->artist
        );

        // Update or create personal note
        $note = $entry->notes()->first();
        if (!empty($validated['note_text']) || !empty($validated['mood']) || !empty($validated['memory_context']) || !empty($validated['listening_context'])) {
            if ($note) {
                $note->update([
                    'note_text' => $validated['note_text'] ?? null,
                    'mood' => $validated['mood'] ?? null,
                    'memory_context' => $validated['memory_context'] ?? null,
                    'listening_context' => $validated['listening_context'] ?? null,
                ]);
            } else {
                MusicNote::create([
                    'music_entry_id' => $entry->id,
                    'user_id' => auth()->id(),
                    'note_text' => $validated['note_text'] ?? null,
                    'mood' => $validated['mood'] ?? null,
                    'memory_context' => $validated['memory_context'] ?? null,
                    'listening_context' => $validated['listening_context'] ?? null,
                ]);
            }
        } elseif ($note) {
            // Delete note if all fields are empty
            $note->delete();
        }

        // Sync tags
        if (isset($validated['tags'])) {
            $entry->tags()->sync($validated['tags']);
        } else {
            $entry->tags()->detach();
        }

        return redirect()->route('music.show', $entry->id)
            ->with('success', 'Music entry updated successfully!');
    }

    /**
     * Remove the specified music entry
     */
    public function destroy($id)
    {
        $entry = MusicEntry::where('user_id', auth()->id())->findOrFail($id);
        $entry->delete();

        // Log activity
        \App\Models\ActivityLog::logActivity(
            auth()->id(),
            'music_delete',
            'music_entry',
            $entry->id,
            'Deleted: ' . $entry->title . ' by ' . $entry->artist
        );

        return redirect()->route('music.index')
            ->with('success', 'Music entry deleted successfully!');
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite($id)
    {
        $entry = MusicEntry::where('user_id', auth()->id())->findOrFail($id);
        $entry->is_favorite = !$entry->is_favorite;
        $entry->save();

        // Log activity
        $action = $entry->is_favorite ? 'Added to favorites' : 'Removed from favorites';
        \App\Models\ActivityLog::logActivity(
            auth()->id(),
            'music_toggle_favorite',
            'music_entry',
            $entry->id,
            $action . ': ' . $entry->title . ' by ' . $entry->artist
        );

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_favorite' => $entry->is_favorite
            ]);
        }

        return redirect()->back()
            ->with('success', $entry->is_favorite ? 'Added to favorites!' : 'Removed from favorites!');
    }
}

