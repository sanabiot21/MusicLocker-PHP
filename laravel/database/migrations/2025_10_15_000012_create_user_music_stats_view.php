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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS user_music_stats');
    }
};
