<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MusicEntry extends Model
{
    use HasFactory;

    protected $table = 'music_entries';

    protected $fillable = [
        'user_id',
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
    ];

    protected function casts(): array
    {
        return [
            'is_favorite' => 'boolean',
            'personal_rating' => 'integer',
            'duration' => 'integer',
            'release_year' => 'integer',
            'date_discovered' => 'date',
            'date_added' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relationships

    /**
     * Get the user that owns this music entry
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all notes for this music entry
     */
    public function notes(): HasMany
    {
        return $this->hasMany(MusicNote::class, 'music_entry_id');
    }

    /**
     * Get all tags for this music entry
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'music_entry_tags', 'music_entry_id', 'tag_id')
            ->withPivot('created_at');
    }

    /**
     * Get all playlists containing this music entry
     */
    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, 'playlist_entries', 'music_entry_id', 'playlist_id')
            ->withPivot(['position', 'added_by_user_id', 'created_at'])
            ->orderBy('position');
    }

    // Scopes

    /**
     * Scope to get only favorites
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope to filter by rating
     */
    public function scopeByRating($query, int $rating)
    {
        return $query->where('personal_rating', $rating);
    }

    /**
     * Scope to filter by genre
     */
    public function scopeByGenre($query, string $genre)
    {
        return $query->where('genre', 'LIKE', "%{$genre}%");
    }

    /**
     * Scope to search music entries
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('artist', 'LIKE', "%{$search}%")
              ->orWhere('album', 'LIKE', "%{$search}%")
              ->orWhere('genre', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope to get recently added entries
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors & Business Logic

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration) {
            return 'Unknown';
        }

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(): bool
    {
        $this->is_favorite = !$this->is_favorite;
        return $this->save();
    }

    /**
     * Update rating
     */
    public function updateRating(int $rating): bool
    {
        if ($rating < 0 || $rating > 5) {
            return false;
        }

        $this->personal_rating = $rating;
        return $this->save();
    }

    /**
     * Get collection statistics for a user
     */
    public static function getCollectionStats(int $userId): array
    {
        $stats = self::where('user_id', $userId)
            ->selectRaw('
                COUNT(*) as total,
                COUNT(CASE WHEN personal_rating = 5 THEN 1 END) as five_star,
                COUNT(CASE WHEN is_favorite = TRUE THEN 1 END) as favorites,
                AVG(personal_rating) as avg_rating,
                COUNT(DISTINCT artist) as unique_artists,
                COUNT(DISTINCT genre) as unique_genres,
                SUM(duration) as total_duration
            ')
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'five_star' => $stats->five_star ?? 0,
            'favorites' => $stats->favorites ?? 0,
            'avg_rating' => round($stats->avg_rating ?? 0, 2),
            'unique_artists' => $stats->unique_artists ?? 0,
            'unique_genres' => $stats->unique_genres ?? 0,
            'total_duration' => $stats->total_duration ?? 0,
        ];
    }
}
