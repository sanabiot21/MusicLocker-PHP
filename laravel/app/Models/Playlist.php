<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Playlist extends Model
{
    use HasFactory;

    protected $table = 'playlists';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_public',
        'cover_image_url',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relationships

    /**
     * Get the user that owns this playlist
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all music entries in this playlist
     */
    public function musicEntries(): BelongsToMany
    {
        return $this->belongsToMany(MusicEntry::class, 'playlist_entries', 'playlist_id', 'music_entry_id')
            ->withPivot(['position', 'added_by_user_id', 'created_at'])
            ->orderBy('position');
    }

    /**
     * Get all playlist entries
     */
    public function entries(): HasMany
    {
        return $this->hasMany(PlaylistEntry::class)->orderBy('position');
    }

    // Scopes

    /**
     * Scope to get only public playlists
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get only private playlists
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Business Logic

    /**
     * Get total duration of playlist
     */
    public function getTotalDurationAttribute(): int
    {
        return $this->musicEntries()->sum('duration');
    }

    /**
     * Get track count
     */
    public function getTrackCountAttribute(): int
    {
        return $this->musicEntries()->count();
    }

    /**
     * Add music entry to playlist
     */
    public function addMusicEntry(int $musicEntryId, int $addedByUserId): bool
    {
        $nextPosition = $this->entries()->max('position') + 1;

        return (bool) $this->entries()->create([
            'music_entry_id' => $musicEntryId,
            'position' => $nextPosition,
            'added_by_user_id' => $addedByUserId,
        ]);
    }
}
