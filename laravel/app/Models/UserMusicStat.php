<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMusicStat extends Model
{
    /**
     * This is a database VIEW, not a table
     * Therefore it is read-only
     */
    protected $table = 'user_music_stats';
    protected $primaryKey = 'user_id';

    // No timestamps on views
    public $timestamps = false;

    // Prevent any writes to the view
    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'total_entries' => 'integer',
            'five_star_entries' => 'integer',
            'favorite_entries' => 'integer',
            'average_rating' => 'float',
            'unique_artists' => 'integer',
            'unique_genres' => 'integer',
        ];
    }

    /**
     * Get stats for a specific user
     */
    public static function forUser(int $userId): ?self
    {
        return self::where('user_id', $userId)->first();
    }

    /**
     * Get top users by music count
     */
    public static function topUsers(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return self::orderBy('total_entries', 'desc')->limit($limit)->get();
    }
}
