<?php

namespace App\Http\Controllers\Api;

use App\Models\Playlist;
use App\Models\PlaylistEntry;
use App\Http\Requests\StorePlaylistRequest;
use App\Http\Requests\UpdatePlaylistRequest;
use App\Http\Requests\AddTrackToPlaylistRequest;
use App\Http\Resources\PlaylistResource;
use Illuminate\Http\Request;
use Exception;

class PlaylistController extends ApiController
{
    /**
     * Display a listing of the user's playlists
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            $query = Playlist::where('user_id', $user->id);

            // Filter by visibility
            if ($request->has('public')) {
                $isPublic = $request->boolean('public');
                $query->where('is_public', $isPublic);
            }

            // Include entries count
            if ($request->boolean('with_count', true)) {
                $query->withCount('musicEntries');
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $playlists = $query->get();

            return $this->successResponse(
                PlaylistResource::collection($playlists),
                'Playlists retrieved successfully'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve playlists: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Store a newly created playlist
     *
     * @param StorePlaylistRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePlaylistRequest $request)
    {
        try {
            $user = auth()->user();

            $playlist = Playlist::create([
                'user_id' => $user->id,
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'is_public' => $request->boolean('is_public', false),
                'cover_image_url' => $request->input('cover_image_url'),
            ]);

            return $this->successResponse(
                new PlaylistResource($playlist),
                'Playlist created successfully',
                201
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to create playlist: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified playlist
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $user = auth()->user();

            $playlist = Playlist::where('user_id', $user->id)
                ->with(['musicEntries.tags', 'musicEntries.notes'])
                ->findOrFail($id);

            return $this->successResponse(
                new PlaylistResource($playlist),
                'Playlist retrieved successfully'
            );

        } catch (Exception $e) {
            return $this->notFoundResponse('Playlist not found');
        }
    }

    /**
     * Update the specified playlist
     *
     * @param UpdatePlaylistRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePlaylistRequest $request, int $id)
    {
        try {
            $user = auth()->user();

            $playlist = Playlist::where('user_id', $user->id)->findOrFail($id);

            $playlist->update($request->only([
                'name',
                'description',
                'is_public',
                'cover_image_url',
            ]));

            return $this->successResponse(
                new PlaylistResource($playlist),
                'Playlist updated successfully'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to update playlist: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified playlist
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $user = auth()->user();

            $playlist = Playlist::where('user_id', $user->id)->findOrFail($id);
            $playlist->delete();

            return $this->successResponse(
                null,
                'Playlist deleted successfully'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to delete playlist: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Add a track to the playlist
     *
     * @param AddTrackToPlaylistRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTrack(AddTrackToPlaylistRequest $request, int $id)
    {
        try {
            $user = auth()->user();

            $playlist = Playlist::where('user_id', $user->id)->findOrFail($id);
            $musicEntryId = $request->input('music_entry_id');

            // Check if track already exists in playlist
            $exists = PlaylistEntry::where('playlist_id', $playlist->id)
                ->where('music_entry_id', $musicEntryId)
                ->exists();

            if ($exists) {
                return $this->errorResponse('Track already exists in this playlist', 400);
            }

            // Get next position
            $position = $request->input('position');
            if (!$position) {
                $maxPosition = PlaylistEntry::where('playlist_id', $playlist->id)
                    ->max('position');
                $position = $maxPosition ? $maxPosition + 1 : 1;
            }

            // Create playlist entry
            PlaylistEntry::create([
                'playlist_id' => $playlist->id,
                'music_entry_id' => $musicEntryId,
                'position' => $position,
                'added_by_user_id' => $user->id,
            ]);

            // Load playlist with entries
            $playlist->load(['musicEntries']);

            return $this->successResponse(
                new PlaylistResource($playlist),
                'Track added to playlist successfully'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to add track to playlist: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove a track from the playlist
     *
     * @param int $id
     * @param int $entryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeTrack(int $id, int $entryId)
    {
        try {
            $user = auth()->user();

            $playlist = Playlist::where('user_id', $user->id)->findOrFail($id);

            $deleted = PlaylistEntry::where('playlist_id', $playlist->id)
                ->where('music_entry_id', $entryId)
                ->delete();

            if (!$deleted) {
                return $this->notFoundResponse('Track not found in playlist');
            }

            // Reorder remaining entries
            $entries = PlaylistEntry::where('playlist_id', $playlist->id)
                ->orderBy('position')
                ->get();

            $position = 1;
            foreach ($entries as $entry) {
                $entry->update(['position' => $position++]);
            }

            return $this->successResponse(
                null,
                'Track removed from playlist successfully'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to remove track from playlist: ' . $e->getMessage(),
                500
            );
        }
    }
}
