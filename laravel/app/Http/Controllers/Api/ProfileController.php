<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Exception;

class ProfileController extends ApiController
{
    /**
     * Display the authenticated user's profile
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        try {
            $user = auth()->user();

            // Load user stats
            $stats = $user->getUserStats();

            return $this->successResponse([
                'user' => new UserResource($user),
                'stats' => $stats,
            ], 'Profile retrieved successfully');

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve profile: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Update the authenticated user's profile
     *
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = auth()->user();

            // Update basic information
            if ($request->has('first_name')) {
                $user->first_name = $request->input('first_name');
            }

            if ($request->has('last_name')) {
                $user->last_name = $request->input('last_name');
            }

            if ($request->has('email')) {
                $user->email = $request->input('email');
            }

            // Handle password change
            if ($request->filled('new_password')) {
                // Verify current password
                if (!Hash::check($request->input('current_password'), $user->password_hash)) {
                    return $this->errorResponse('Current password is incorrect', 400);
                }

                // Update password
                $user->password_hash = Hash::make($request->input('new_password'));
            }

            $user->save();

            return $this->successResponse(
                new UserResource($user),
                'Profile updated successfully'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to update profile: ' . $e->getMessage(),
                500
            );
        }
    }
}
