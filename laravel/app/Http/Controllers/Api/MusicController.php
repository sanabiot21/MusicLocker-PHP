<?php

namespace App\Http\Controllers\Api;

use App\Models\MusicEntry;
use App\Models\MusicNote;
use App\Http\Requests\StoreMusicEntryRequest;
use App\Http\Requests\UpdateMusicEntryRequest;
use App\Http\Resources\MusicEntryResource;
use App\Http\Resources\MusicEntryCollection;
use Illuminate\Http\Request;
use Exception;

class MusicController extends ApiController
{
    /**
     * Display a listing of the user's music entries
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $perPage = min((int)$request->input('per_page', 20), 100);

            $query = MusicEntry::where('user_id', $user->id)
                ->with(['tags', 'notes']);

            // Apply filters
            if ($request->has('search')) {
                $query->search($request->input('search'));
            }

            if ($request->has('genre')) {
                $query->byGenre($request->input('genre'));
            }

            if ($request->has('rating')) {
                $query->byRating((int)$request->input('rating'));
            }

            if ($request->filled('favorite') && $request->boolean('favorite')) {
                $query->favorites();
            }

            if ($request->has('tag_id')) {
                $query->whereHas('tags', function ($q) use ($request) {
                    $q->where('tags.id', $request->input('tag_id'));
                });
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $entries = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => MusicEntryResource::collection($entries->items()),
                'meta' => [
                    'total' => $entries->total(),
                    'per_page' => $entries->perPage(),
                    'current_page' => $entries->currentPage(),
                    'last_page' => $entries->lastPage(),
                    'from' => $entries->firstItem(),
                    'to' => $entries->lastItem(),
                ],
                'links' => [
                    'first' => $entries->url(1),
                    'last' => $entries->url($entries->lastPage()),
                    'prev' => $entries->previousPageUrl(),
                    'next' => $entries->nextPageUrl(),
                ],
            ]);

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve music entries: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Store a newly created music entry
     *
     * @param StoreMusicEntryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreMusicEntryRequest $request)
    {
        try {
            $user = auth()->user();

            // Create music entry
            $entry = MusicEntry::create([
                'user_id' => $user->id,
                'title' => $request->input('title'),
                'artist' => $request->input('artist'),
                'album' => $request->input('album'),
                'genre' => $request->input('genre'),
                'release_year' => $request->input('release_year'),
                'duration' => $request->input('duration'),
                'spotify_id' => $request->input('spotify_id'),
                'spotify_url' => $request->input('spotify_url'),
                'album_art_url' => $request->input('album_art_url'),
                'personal_rating' => $request->input('personal_rating'),
                'date_discovered' => $request->input('date_discovered', now()),
                'is_favorite' => $request->boolean('is_favorite', false),
            ]);

            // Attach tags if provided
            if ($request->has('tags')) {
                $entry->tags()->sync($request->input('tags'));
            }

            // Create note if provided
            if ($request->filled('note_text')) {
                MusicNote::create([
                    'music_entry_id' => $entry->id,
                    'user_id' => $user->id,
                    'note_text' => $request->input('note_text'),
                    'mood' => $request->input('mood'),
                    'memory_context' => $request->input('memory_context'),
                    'listening_context' => $request->input('listening_context'),
                ]);
            }

            // Load relationships for response
            $entry->load(['tags', 'notes']);

            return $this->successResponse(
                new MusicEntryResource($entry),
                'Music entry created successfully',
                201
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to create music entry: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified music entry
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $user = auth()->user();

            $entry = MusicEntry::where('user_id', $user->id)
                ->with(['tags', 'notes'])
                ->findOrFail($id);

            return $this->successResponse(
                new MusicEntryResource($entry),
                'Music entry retrieved successfully'
            );

        } catch (Exception $e) {
            return $this->notFoundResponse('Music entry not found');
        }
    }

    /**
     * Update the specified music entry
     *
     * @param UpdateMusicEntryRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateMusicEntryRequest $request, int $id)
    {
        try {
            $user = auth()->user();

            $entry = MusicEntry::where('user_id', $user->id)->findOrFail($id);

            // Update music entry fields
            $entry->update($request->only([
                'title',
                'artist',
                'album',
                'genre',
                'release_year',
                'duration',
                'spotify_id',
                'spotify_url',
                'album_art_url',
                'personal_rating',
                'date_discovered',
                'is_favorite',
            ]));

            // Update tags if provided
            if ($request->has('tags')) {
                $entry->tags()->sync($request->input('tags'));
            }

            // Update or create note if provided
            if ($request->filled('note_text')) {
                $note = $entry->notes()->first();

                if ($note) {
                    $note->update([
                        'note_text' => $request->input('note_text'),
                        'mood' => $request->input('mood'),
                        'memory_context' => $request->input('memory_context'),
                        'listening_context' => $request->input('listening_context'),
                    ]);
                } else {
                    MusicNote::create([
                        'music_entry_id' => $entry->id,
                        'user_id' => $user->id,
                        'note_text' => $request->input('note_text'),
                        'mood' => $request->input('mood'),
                        'memory_context' => $request->input('memory_context'),
                        'listening_context' => $request->input('listening_context'),
                    ]);
                }
            }

            // Load relationships for response
            $entry->load(['tags', 'notes']);

            return $this->successResponse(
                new MusicEntryResource($entry),
                'Music entry updated successfully'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to update music entry: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified music entry
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $user = auth()->user();

            $entry = MusicEntry::where('user_id', $user->id)->findOrFail($id);
            $entry->delete();

            return $this->successResponse(
                null,
                'Music entry deleted successfully'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to delete music entry: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Toggle favorite status of a music entry
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleFavorite(int $id)
    {
        try {
            $user = auth()->user();

            $entry = MusicEntry::where('user_id', $user->id)->findOrFail($id);
            $entry->toggleFavorite();

            return $this->successResponse(
                [
                    'id' => $entry->id,
                    'is_favorite' => $entry->is_favorite,
                ],
                'Favorite status updated successfully'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to toggle favorite: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get user's music collection statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        try {
            $user = auth()->user();
            $stats = $user->getUserStats();

            return $this->successResponse(
                $stats,
                'Statistics retrieved successfully'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve statistics: ' . $e->getMessage(),
                500
            );
        }
    }
}
