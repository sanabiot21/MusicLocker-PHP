<?php

namespace App\Http\Controllers;

use App\Models\MusicEntry;
use App\Models\Playlist;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * Dashboard Controller
 * Handles the authenticated user's dashboard
 */
class DashboardController extends Controller
{
    /**
     * Display the dashboard with user statistics and recent music
     */
    public function index()
    {
        $user = auth()->user();

        // Get user statistics matching custom PHP
        $userStats = $user->getUserStats();
        
        // Add playlist count to match custom PHP stats
        $userStats['playlist_count'] = Playlist::where('user_id', $user->id)->count();

        // Get recent activity for timeline (last 10 activities)
        $recentActivity = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'type' => $log->action,
                    'action' => ucfirst(str_replace('_', ' ', $log->action)),
                    'description' => $log->description ?? 'No description available',
                    'timestamp' => $log->created_at
                ];
            });

        // Get recent music entries (last 6) for quick preview
        $recentMusic = MusicEntry::where('user_id', $user->id)
            ->with(['tags'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return view('dashboard', [
            'user' => $user,
            'userStats' => $userStats,
            'recentActivity' => $recentActivity,
            'recentMusic' => $recentMusic
        ]);
    }
}
