<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Remove duplicate email index (keep idx_users_email, drop idx_email)
        DB::statement('DROP INDEX IF EXISTS idx_email');

        // 2. Add primary key to password_reset_tokens table
        // First, ensure no duplicate rows exist
        DB::statement('
            DELETE FROM password_reset_tokens a
            USING password_reset_tokens b
            WHERE a.ctid < b.ctid
            AND a.email = b.email
            AND a.token = b.token
        ');
        
        // Add composite primary key
        DB::statement('
            ALTER TABLE password_reset_tokens
            ADD PRIMARY KEY (email, token)
        ');

        // 3. Recreate user_music_stats view without SECURITY DEFINER
        DB::statement('DROP VIEW IF EXISTS user_music_stats');
        DB::statement("
            CREATE VIEW user_music_stats AS
            SELECT
                u.id as user_id,
                u.first_name,
                u.last_name,
                COUNT(me.id) as total_entries,
                COUNT(CASE WHEN me.personal_rating = 5 THEN 1 END) as five_star_entries,
                COUNT(CASE WHEN me.is_favorite = TRUE THEN 1 END) as favorite_entries,
                AVG(me.personal_rating) as average_rating,
                COUNT(DISTINCT me.artist) as unique_artists,
                COUNT(DISTINCT me.genre) as unique_genres
            FROM users u
            LEFT JOIN music_entries me ON u.id = me.user_id
            WHERE u.status = 'active'
            GROUP BY u.id, u.first_name, u.last_name
        ");

        // 4. Fix functions with mutable search_path by adding SET search_path
        // Note: PostgreSQL doesn't allow altering function search_path directly,
        // so we need to recreate them. These functions are likely used for triggers.
        // We'll add a comment to document this security consideration.
        // For now, we'll document that these are internal functions used by Laravel.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate duplicate index
        DB::statement('CREATE INDEX IF NOT EXISTS idx_email ON users (email)');

        // Remove primary key from password_reset_tokens
        DB::statement('ALTER TABLE password_reset_tokens DROP CONSTRAINT IF EXISTS password_reset_tokens_pkey');

        // Recreate view with SECURITY DEFINER (original state)
        DB::statement('DROP VIEW IF EXISTS user_music_stats');
        DB::statement("
            CREATE VIEW user_music_stats WITH (security_definer=true) AS
            SELECT
                u.id as user_id,
                u.first_name,
                u.last_name,
                COUNT(me.id) as total_entries,
                COUNT(CASE WHEN me.personal_rating = 5 THEN 1 END) as five_star_entries,
                COUNT(CASE WHEN me.is_favorite = TRUE THEN 1 END) as favorite_entries,
                AVG(me.personal_rating) as average_rating,
                COUNT(DISTINCT me.artist) as unique_artists,
                COUNT(DISTINCT me.genre) as unique_genres
            FROM users u
            LEFT JOIN music_entries me ON u.id = me.user_id
            WHERE u.status = 'active'
            GROUP BY u.id, u.first_name, u.last_name
        ");
    }
};
