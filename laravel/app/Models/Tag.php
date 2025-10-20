<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';

    protected $fillable = [
        'user_id',
        'name',
        'color',
        'description',
        'is_system_tag',
    ];

    protected function casts(): array
    {
        return [
            'is_system_tag' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relationships

    /**
     * Get the user that owns this tag
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all music entries with this tag
     */
    public function musicEntries(): BelongsToMany
    {
        return $this->belongsToMany(MusicEntry::class, 'music_entry_tags', 'tag_id', 'music_entry_id')
            ->withPivot('created_at');
    }

    // Scopes

    /**
     * Scope to get only system tags
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system_tag', true);
    }

    /**
     * Scope to get only user-created tags
     */
    public function scopeUserTags($query)
    {
        return $query->where('is_system_tag', false);
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
     * Get tag usage count
     */
    public function getUsageCountAttribute(): int
    {
        return $this->musicEntries()->count();
    }
}
