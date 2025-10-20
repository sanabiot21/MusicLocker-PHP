<?php

namespace App\Http\Controllers\Api;

use App\Services\SpotifyService;
use Illuminate\Http\Request;
use Exception;

class SpotifyController extends ApiController
{
    protected SpotifyService $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    /**
     * Search Spotify catalog
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('q', '');
            $type = $request->input('type', 'track');
            $limit = min((int)$request->input('limit', 20), 50);

            if (empty($query)) {
                return $this->errorResponse('Search query is required', 400);
            }

            $types = explode(',', $type);
            $results = $this->spotifyService->search($query, $types, $limit);

            return $this->successResponse($results, 'Search completed successfully');

        } catch (Exception $e) {
            return $this->errorResponse(
                'Spotify search failed: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Search Spotify with focus on preview URLs
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchWithPreview(Request $request)
    {
        try {
            $songName = $request->input('song', '');
            $artistName = $request->input('artist', '');
            $limit = min((int)$request->input('limit', 20), 50);

            if (empty($songName)) {
                return $this->errorResponse('Song name is required', 400);
            }

            $results = $this->spotifyService->searchWithPreview($songName, $artistName, $limit);

            return $this->successResponse($results, 'Preview search completed successfully');

        } catch (Exception $e) {
            return $this->errorResponse(
                'Spotify preview search failed: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get track details by Spotify ID
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTrack(string $id)
    {
        try {
            $track = $this->spotifyService->getTrack($id);
            $metadata = $this->spotifyService->extractTrackMetadata($track);

            return $this->successResponse($metadata, 'Track details retrieved successfully');

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to get track details: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get album details by Spotify ID
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAlbum(string $id)
    {
        try {
            $album = $this->spotifyService->getAlbum($id);

            return $this->successResponse($album, 'Album details retrieved successfully');

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to get album details: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get artist details by Spotify ID
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArtist(string $id)
    {
        try {
            $artist = $this->spotifyService->getArtist($id);

            return $this->successResponse($artist, 'Artist details retrieved successfully');

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to get artist details: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get album tracks by Spotify album ID
     *
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAlbumTracks(string $id, Request $request)
    {
        try {
            $limit = min((int)$request->input('limit', 50), 50);
            $tracks = $this->spotifyService->getAlbumTracks($id, $limit);

            return $this->successResponse($tracks, 'Album tracks retrieved successfully');

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to get album tracks: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Test Spotify API connection
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection()
    {
        try {
            $isConnected = $this->spotifyService->testConnection();

            if ($isConnected) {
                return $this->successResponse(
                    ['connected' => true],
                    'Spotify API connection successful'
                );
            } else {
                return $this->errorResponse('Spotify API connection failed', 500);
            }

        } catch (Exception $e) {
            return $this->errorResponse(
                'Spotify API test failed: ' . $e->getMessage(),
                500
            );
        }
    }
}
