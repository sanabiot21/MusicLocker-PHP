<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaylistEntry extends Model
{
    use HasFactory;

    protected $table = 'playlist_entries';

    protected $fillable = [
        'playlist_id',
        'music_entry_id',
        'position',
        'added_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public $timestamps = false; // Only created_at, no updated_at

    // Override to use only created_at
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = now();
        });
    }

    // Relationships

    /**
     * Get the playlist this entry belongs to
     */
    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class);
    }

    /**
     * Get the music entry
     */
    public function musicEntry(): BelongsTo
    {
        return $this->belongsTo(MusicEntry::class, 'music_entry_id');
    }

    /**
     * Get the user who added this entry
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }
}
