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
        // Insert default admin user (password: admin123)
        DB::table('users')->insert([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@musiclocker.local',
            'password_hash' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'email_verified' => true,
            'status' => 'active',
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get admin user ID
        $adminId = DB::table('users')->where('email', 'admin@musiclocker.local')->value('id');

        // Create default system tags for the admin user
        $systemTags = [
            ['user_id' => $adminId, 'name' => 'Favorites', 'color' => '#ff6b6b', 'description' => 'Personal favorite tracks', 'is_system_tag' => true],
            ['user_id' => $adminId, 'name' => 'Chill', 'color' => '#4ecdc4', 'description' => 'Relaxing and calm music', 'is_system_tag' => true],
            ['user_id' => $adminId, 'name' => 'Workout', 'color' => '#45b7d1', 'description' => 'High energy tracks for exercise', 'is_system_tag' => true],
            ['user_id' => $adminId, 'name' => 'Study', 'color' => '#96ceb4', 'description' => 'Focus music for studying', 'is_system_tag' => true],
            ['user_id' => $adminId, 'name' => 'Party', 'color' => '#feca57', 'description' => 'Upbeat music for social gatherings', 'is_system_tag' => true],
            ['user_id' => $adminId, 'name' => 'Nostalgic', 'color' => '#ff9ff3', 'description' => 'Music that brings back memories', 'is_system_tag' => true],
            ['user_id' => $adminId, 'name' => 'Discover', 'color' => '#00d4ff', 'description' => 'Recently discovered tracks', 'is_system_tag' => true],
            ['user_id' => $adminId, 'name' => 'Top Rated', 'color' => '#8a2be2', 'description' => '5-star personal ratings', 'is_system_tag' => true],
        ];

        foreach ($systemTags as $tag) {
            DB::table('tags')->insert(array_merge($tag, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Insert default system settings
        $systemSettings = [
            ['setting_key' => 'app_name', 'setting_value' => 'Music Locker', 'setting_type' => 'string', 'description' => 'Application name', 'is_public' => true],
            ['setting_key' => 'app_version', 'setting_value' => '1.0.0', 'setting_type' => 'string', 'description' => 'Current application version', 'is_public' => true],
            ['setting_key' => 'max_music_entries_per_user', 'setting_value' => '10000', 'setting_type' => 'integer', 'description' => 'Maximum music entries per user', 'is_public' => false],
            ['setting_key' => 'session_timeout', 'setting_value' => '3600', 'setting_type' => 'integer', 'description' => 'Session timeout in seconds', 'is_public' => false],
            ['setting_key' => 'enable_spotify_integration', 'setting_value' => '1', 'setting_type' => 'boolean', 'description' => 'Enable Spotify API integration', 'is_public' => false],
            ['setting_key' => 'default_items_per_page', 'setting_value' => '20', 'setting_type' => 'integer', 'description' => 'Default pagination limit', 'is_public' => true],
        ];

        foreach ($systemSettings as $setting) {
            DB::table('system_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove seeded data
        DB::table('system_settings')->whereIn('setting_key', [
            'app_name',
            'app_version',
            'max_music_entries_per_user',
            'session_timeout',
            'enable_spotify_integration',
            'default_items_per_page',
        ])->delete();

        DB::table('tags')->where('is_system_tag', true)->delete();
        DB::table('users')->where('email', 'admin@musiclocker.local')->delete();
    }
};
