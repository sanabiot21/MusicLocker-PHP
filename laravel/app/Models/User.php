<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',  // Changed from password_hash - use accessor/mutator
        'email_verified',
        'verification_token',
        'reset_token',
        'status',
        'ban_reason',
        'role',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'verification_token',
        'reset_token',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified' => 'boolean',
            'last_login' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Override the password column name for authentication
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Relationships

    /**
     * Get all music entries for this user
     */
    public function musicEntries(): HasMany
    {
        return $this->hasMany(MusicEntry::class);
    }

    /**
     * Get all tags for this user
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * Get all music notes for this user
     */
    public function musicNotes(): HasMany
    {
        return $this->hasMany(MusicNote::class);
    }

    /**
     * Get all playlists for this user
     */
    public function playlists(): HasMany
    {
        return $this->hasMany(Playlist::class);
    }

    /**
     * Get all sessions for this user
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Get all activity logs for this user
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get all admin notes for this user
     */
    public function adminNotes(): HasMany
    {
        return $this->hasMany(AdminNote::class, 'user_id');
    }

    // Accessors & Mutators

    /**
     * Password accessor - returns password_hash column
     */
    public function getPasswordAttribute()
    {
        return $this->attributes['password_hash'] ?? null;
    }

    /**
     * Password mutator - sets password_hash column
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = $value;
    }

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if user is an admin
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is active
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    // Scopes

    /**
     * Scope to get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Business Logic Methods

    /**
     * Create default system tags for a new user
     */
    public function createDefaultTags(): void
    {
        $defaultTags = [
            ['name' => 'Favorites', 'color' => '#ff6b6b', 'description' => 'Personal favorite tracks'],
            ['name' => 'Chill', 'color' => '#4ecdc4', 'description' => 'Relaxing and calm music'],
            ['name' => 'Workout', 'color' => '#45b7d1', 'description' => 'High energy tracks for exercise'],
            ['name' => 'Study', 'color' => '#96ceb4', 'description' => 'Focus music for studying'],
            ['name' => 'Party', 'color' => '#feca57', 'description' => 'Upbeat music for social gatherings'],
            ['name' => 'Nostalgic', 'color' => '#ff9ff3', 'description' => 'Music that brings back memories'],
            ['name' => 'Discover', 'color' => '#00d4ff', 'description' => 'Recently discovered tracks'],
            ['name' => 'Top Rated', 'color' => '#8a2be2', 'description' => '5-star personal ratings'],
        ];

        foreach ($defaultTags as $tagData) {
            $this->tags()->create(array_merge($tagData, ['is_system_tag' => true]));
        }
    }

    /**
     * Get user statistics
     */
    public function getUserStats(): array
    {
        $stats = $this->musicEntries()
            ->selectRaw('
                COUNT(*) as total_entries,
                COUNT(CASE WHEN personal_rating = 5 THEN 1 END) as five_star_entries,
                COUNT(CASE WHEN is_favorite = TRUE THEN 1 END) as favorite_entries,
                AVG(personal_rating) as average_rating,
                COUNT(DISTINCT artist) as unique_artists,
                COUNT(DISTINCT genre) as unique_genres
            ')
            ->first();

        return [
            'total_entries' => $stats->total_entries ?? 0,
            'five_star_entries' => $stats->five_star_entries ?? 0,
            'favorite_entries' => $stats->favorite_entries ?? 0,
            'average_rating' => round($stats->average_rating ?? 0, 2),
            'unique_artists' => $stats->unique_artists ?? 0,
            'unique_genres' => $stats->unique_genres ?? 0,
        ];
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): bool
    {
        $this->last_login = now();
        return $this->save();
    }

    /**
     * Toggle user status (for admin)
     */
    public function toggleStatus(): bool
    {
        $this->status = $this->status === 'active' ? 'inactive' : 'active';
        return $this->save();
    }

    /**
     * Check if this user can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Prevent deleting primary admin (ID 1)
        return $this->id !== 1;
    }

    // Admin Statistics Methods

    /**
     * Get total number of users
     */
    public static function getTotalUsers(): int
    {
        return static::count();
    }

    /**
     * Get number of active users
     */
    public static function getActiveUsers(): int
    {
        return static::where('status', 'active')->count();
    }

    /**
     * Get number of new users registered today
     */
    public static function getNewUsersToday(): int
    {
        return static::whereDate('created_at', today())->count();
    }

    /**
     * Get total number of music entries across all users
     */
    public static function getTotalMusicEntries(): int
    {
        return MusicEntry::count();
    }

    /**
     * Get weekly user growth
     */
    public static function getWeeklyUserGrowth(): int
    {
        return static::where('created_at', '>=', now()->subWeek())->count();
    }

    /**
     * Get weekly music entries count
     */
    public static function getWeeklyMusicCount(): int
    {
        return MusicEntry::where('created_at', '>=', now()->subWeek())->count();
    }

    /**
     * Get most active user (by music entry count)
     */
    public static function getMostActiveUser(): ?User
    {
        return static::withCount('musicEntries')
            ->orderBy('music_entries_count', 'desc')
            ->first();
    }

    /**
     * Get most popular tag name
     */
    public static function getPopularTag(): ?string
    {
        $tag = Tag::withCount('musicEntries')
            ->orderBy('music_entries_count', 'desc')
            ->first();

        return $tag?->name;
    }

    /**
     * Get all users with pagination
     */
    public static function getAllUsers(int $limit = 50, int $offset = 0)
    {
        return static::with(['musicEntries'])
            ->withCount('musicEntries')
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
    }

    /**
     * Request password reset (admin approval workflow)
     */
    public function requestPasswordReset(): bool
    {
        $this->reset_token = bin2hex(random_bytes(32));
        return $this->save();
    }

    /**
     * Clear password reset request
     */
    public function clearPasswordResetRequest(): bool
    {
        $this->reset_token = null;
        return $this->save();
    }

    /**
     * Get pending password reset requests
     */
    public static function getPendingResetRequests()
    {
        return static::whereNotNull('reset_token')->get();
    }
}
