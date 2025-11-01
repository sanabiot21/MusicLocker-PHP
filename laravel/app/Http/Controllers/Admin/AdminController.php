<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MusicEntry;
use App\Models\Playlist;
use App\Models\Tag;
use App\Models\ActivityLog;
use App\Models\AdminNote;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

/**
 * Admin Controller
 * Handles all admin panel functionality
 *
 * Password Reset Workflow (Admin Approval):
 * =========================================
 * 1. User requests password reset via forgot-password form
 *    → Sets reset_token to temporary token in database
 *
 * 2. Admin sees pending requests on dashboard
 *    → View shows users where reset_token IS NOT NULL
 *
 * 3. Admin manually resets password via /admin/users/{id}/reset-password
 *    → Admin sets new password directly
 *    → Clears reset_token
 *    → Logs activity
 *
 * Note: This is manual admin approval, not email-based reset link.
 *       The approveResetRequest() method exists for future email workflow.
 */
class AdminController extends Controller
{
    /**
     * Admin Dashboard
     * Display statistics and pending reset requests
     */
    public function dashboard()
    {
        // User Statistics
        $userStats = [
            'total_users' => User::getTotalUsers(),
            'active_users' => User::getActiveUsers(),
            'new_users_today' => User::getNewUsersToday(),
            'total_music_entries' => User::getTotalMusicEntries(),
        ];

        // Weekly Analytics
        $weeklyStats = [
            'new_users' => User::getWeeklyUserGrowth(),
            'new_music' => User::getWeeklyMusicCount(),
            'most_active' => User::getMostActiveUser(),
            'popular_tag' => User::getPopularTag(),
        ];

        // Get pending password reset requests
        $pendingResets = User::getPendingResetRequests();

        // Get recent activity (last 10) with formatted data
        $recentActivity = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                $activity->icon_class = $this->getActivityIcon($activity->action);
                $activity->icon_color = $this->getActivityColor($activity->action);
                return $activity;
            });

        return view('admin.dashboard', [
            'userStats' => $userStats,
            'weeklyStats' => $weeklyStats,
            'pendingResets' => $pendingResets,
            'recentActivity' => $recentActivity
        ]);
    }

    /**
     * Get Bootstrap Icon class for activity action
     */
    private function getActivityIcon($action)
    {
        $icons = [
            'create' => 'bi-plus-circle',
            'update' => 'bi-pencil-square',
            'delete' => 'bi-trash',
            'approve' => 'bi-check-circle',
            'reset_password' => 'bi-key',
            'default' => 'bi-info-circle'
        ];
        return $icons[$action] ?? $icons['default'];
    }

    /**
     * Get color class for activity action
     */
    private function getActivityColor($action)
    {
        $colors = [
            'create' => 'success',
            'update' => 'info',
            'delete' => 'danger',
            'approve' => 'success',
            'reset_password' => 'warning',
            'default' => 'secondary'
        ];
        return $colors[$action] ?? $colors['default'];
    }

    /**
     * User List
     * Display all users with pagination
     */
    public function userList(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        $query = User::with(['musicEntries'])
            ->withCount('musicEntries');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ILIKE', "%{$search}%")
                  ->orWhere('last_name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', [
            'users' => $users,
            'search' => $search,
            'status' => $status
        ]);
    }

    /**
     * User Detail
     * Display detailed information about a specific user
     */
    public function userDetail($id)
    {
        $user = User::with(['musicEntries', 'playlists', 'tags', 'adminNotes.admin'])
            ->withCount(['musicEntries', 'playlists'])
            ->findOrFail($id);

        $stats = $user->getUserStats();

        return view('admin.user-detail', [
            'user' => $user,
            'stats' => $stats
        ]);
    }

    /**
     * User Music
     * Display a specific user's music collection
     */
    public function userMusic($id)
    {
        $user = User::findOrFail($id);

        $musicEntries = MusicEntry::where('user_id', $id)
            ->with(['tags'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.user-music', [
            'user' => $user,
            'musicEntries' => $musicEntries
        ]);
    }

    /**
     * Update User
     * Update user information (AJAX)
     */
    public function updateUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'status' => 'required|in:active,inactive',
            'ban_reason' => 'nullable|string|max:1000',
            'role' => 'required|in:user,admin'
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Prevent modifying primary admin
        if ($user->id === 1 && $validated['role'] !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot demote primary admin'
            ], 403);
        }

        // Only primary admin can promote users to admin
        if (auth()->id() !== 1 && $user->role !== 'admin' && $validated['role'] === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only primary admin can promote users to admin role'
            ], 403);
        }

        // Clear ban_reason if status is being set to active
        $banReason = $validated['status'] === 'active' ? null : ($validated['ban_reason'] ?? null);

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
            'ban_reason' => $banReason,
            'role' => $validated['role']
        ]);

        log_activity('update', 'user', $user->id, "Updated user: {$user->full_name}");

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Delete User
     * Delete a user and all their data (AJAX)
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if (!$user->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete primary admin'
            ], 403);
        }

        $userName = $user->full_name;
        $user->delete();

        log_activity('delete', 'user', $id, "Deleted user: {$userName}");

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Approve Reset Request
     * Approve a user's password reset request (AJAX)
     */
    public function approveResetRequest(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($validated['user_id']);

        if (!$user->reset_token) {
            return response()->json([
                'success' => false,
                'message' => 'No pending reset request for this user'
            ], 400);
        }

        $user->clearPasswordResetRequest();

        log_activity('approve', 'password_reset', $user->id, "Approved password reset for: {$user->full_name}");

        return response()->json([
            'success' => true,
            'message' => 'Password reset request approved'
        ]);
    }

    /**
     * Reset User Password
     * Admin manually resets a user's password
     */
    public function resetUserPassword(Request $request, $id)
    {
        $validated = $request->validate([
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        $user->clearPasswordResetRequest();

        log_activity('reset_password', 'user', $user->id, "Reset password for: {$user->full_name}");

        // Support both AJAX and form submission
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);
        }

        return redirect()->route('admin.users.detail', $id)
            ->with('success', 'Password reset successfully');
    }

    /**
     * System Health
     * Display system health and database statistics
     */
    public function systemHealth()
    {
        $dbStats = [
            'users' => User::count(),
            'music_entries' => MusicEntry::count(),
            'playlists' => Playlist::count(),
            'tags' => Tag::count(),
            'activity_logs' => ActivityLog::count(),
        ];

        // Test database connection
        try {
            DB::connection()->getPdo();
            $dbConnection = 'Connected';
        } catch (\Exception $e) {
            $dbConnection = 'Failed: ' . $e->getMessage();
        }

        // Server info
        $serverInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database' => config('database.default'),
            'db_connection' => $dbConnection,
            'environment' => app()->environment(),
        ];

        return view('admin.system-health', [
            'dbStats' => $dbStats,
            'serverInfo' => $serverInfo
        ]);
    }

    /**
     * Save Admin Note
     * Create or update an admin note for a user (AJAX)
     */
    public function saveNote(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'note' => 'required|string|max:500'
        ]);

        $adminNote = AdminNote::create([
            'user_id' => $user->id,
            'admin_id' => auth()->id(),
            'note' => $validated['note']
        ]);

        log_activity('create', 'admin_note', $user->id, "Added note for user: {$user->full_name}");

        return response()->json([
            'success' => true,
            'message' => 'Note saved successfully',
            'note' => [
                'id' => $adminNote->id,
                'note' => $adminNote->note,
                'admin_name' => auth()->user()->full_name,
                'created_at' => $adminNote->created_at->format('M d, Y H:i')
            ]
        ]);
    }

    /**
     * Delete Admin Note
     * Delete an admin note for a user (AJAX)
     */
    public function deleteNote($userId, $noteId)
    {
        $note = AdminNote::where('id', $noteId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $user = User::findOrFail($userId);
        $note->delete();

        log_activity('delete', 'admin_note', $userId, "Deleted note for user: {$user->full_name}");

        return response()->json([
            'success' => true,
            'message' => 'Note deleted successfully'
        ]);
    }

    /**
     * Settings
     * Display system settings
     */
    public function settings()
    {
        $settings = SystemSetting::pluck('value', 'key')->toArray();

        return view('admin.settings', [
            'settings' => $settings
        ]);
    }

    /**
     * Update Settings
     * Update system settings (AJAX)
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string|max:1000',
            'registration_enabled' => 'required|boolean',
            'maintenance_mode' => 'required|boolean'
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        log_activity('update', 'settings', 0, 'Updated system settings');

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }
}
