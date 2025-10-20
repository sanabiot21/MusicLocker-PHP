<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusicNote extends Model
{
    use HasFactory;

    protected $table = 'music_notes';

    protected $fillable = [
        'music_entry_id',
        'user_id',
        'note_text',
        'mood',
        'memory_context',
        'listening_context',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relationships

    /**
     * Get the music entry this note belongs to
     */
    public function musicEntry(): BelongsTo
    {
        return $this->belongsTo(MusicEntry::class, 'music_entry_id');
    }

    /**
     * Get the user who created this note
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes

    /**
     * Scope to filter by mood
     */
    public function scopeByMood($query, string $mood)
    {
        return $query->where('mood', $mood);
    }

    /**
     * Scope to search notes
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('note_text', 'LIKE', "%{$search}%");
    }
}
