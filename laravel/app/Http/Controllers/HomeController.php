<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MusicEntry;
use App\Models\MusicNote;
use App\Models\Tag;

/**
 * Home Controller
 * Handles the public landing page
 */
class HomeController extends Controller
{
    /**
     * Display the landing page
     */
    public function index()
    {
        $stats = [
            'active_users' => User::where('status', 'active')->count(),
            'songs_cataloged' => MusicEntry::count(),
            'personal_notes' => MusicNote::count(),
            'mood_tags_created' => Tag::where('is_system_tag', false)->count(),
        ];

        return view('home', compact('stats'));
    }
}
