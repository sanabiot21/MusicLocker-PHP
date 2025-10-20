<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;
use Exception;

class TagController extends ApiController
{
    /**
     * Display a listing of the user's tags
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            $query = Tag::where('user_id', $user->id);

            // Filter by system tags
            if ($request->has('system')) {
                $isSystem = $request->boolean('system');
                $query->where('is_system_tag', $isSystem);
            }

            // Include usage count
            if ($request->boolean('with_usage', false)) {
                $query->withCount('musicEntries');
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'name');
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $tags = $query->get();

            return $this->successResponse(
                TagResource::collection($tags),
                'Tags retrieved successfully'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve tags: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Store a newly created tag
     *
     * @param StoreTagRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTagRequest $request)
    {
        try {
            $user = auth()->user();

            $tag = Tag::create([
                'user_id' => $user->id,
                'name' => $request->input('name'),
                'color' => $request->input('color', '#000000'),
                'description' => $request->input('description'),
                'is_system_tag' => false, // User-created tags are never system tags
            ]);

            return $this->successResponse(
                new TagResource($tag),
                'Tag created successfully',
                201
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to create tag: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified tag
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $user = auth()->user();

            $tag = Tag::where('user_id', $user->id)
                ->with('musicEntries')
                ->findOrFail($id);

            return $this->successResponse(
                new TagResource($tag),
                'Tag retrieved successfully'
            );

        } catch (Exception $e) {
            return $this->notFoundResponse('Tag not found');
        }
    }

    /**
     * Update the specified tag
     *
     * @param UpdateTagRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTagRequest $request, int $id)
    {
        try {
            $user = auth()->user();

            $tag = Tag::where('user_id', $user->id)->findOrFail($id);

            // Prevent editing system tags
            if ($tag->is_system_tag) {
                return $this->forbiddenResponse('Cannot edit system tags');
            }

            $tag->update($request->only([
                'name',
                'color',
                'description',
            ]));

            return $this->successResponse(
                new TagResource($tag),
                'Tag updated successfully'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to update tag: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified tag
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $user = auth()->user();

            $tag = Tag::where('user_id', $user->id)->findOrFail($id);

            // Prevent deleting system tags
            if ($tag->is_system_tag) {
                return $this->forbiddenResponse('Cannot delete system tags');
            }

            $tag->delete();

            return $this->successResponse(
                null,
                'Tag deleted successfully'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to delete tag: ' . $e->getMessage(),
                500
            );
        }
    }
}
