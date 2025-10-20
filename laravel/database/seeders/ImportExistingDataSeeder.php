<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportExistingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Imports existing data from the legacy Music Locker database
     * Source: database/insert_data.sql
     */
    public function run(): void
    {
        $this->command->info('Starting data import from legacy database...');

        // Disable foreign key checks temporarily
        DB::statement('SET session_replication_role = replica;');

        try {
            // Clear existing data (except default admin and system tags)
            $this->clearExistingData();

            // Import in dependency order
            $this->importUsers();
            $this->importTags();
            $this->importSystemSettings();
            $this->importMusicEntries();
            $this->importMusicNotes();
            $this->importMusicEntryTags();
            $this->importPlaylists();
            $this->importPlaylistEntries();
            $this->importUserSessions();
            $this->importActivityLog();

            // Reset sequences
            $this->resetSequences();

            $this->command->info('Data import completed successfully!');
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET session_replication_role = DEFAULT;');
        }
    }

    private function clearExistingData(): void
    {
        $this->command->info('Clearing existing seeded data...');

        DB::table('activity_log')->truncate();
        DB::table('user_sessions')->truncate();
        DB::table('playlist_entries')->truncate();
        DB::table('playlists')->truncate();
        DB::table('music_entry_tags')->truncate();
        DB::table('music_notes')->truncate();
        DB::table('music_entries')->truncate();
        DB::table('tags')->truncate();
        DB::table('system_settings')->truncate();
        DB::table('users')->truncate();
    }

    private function importUsers(): void
    {
        $this->command->info('Importing users...');

        $users = [
            ['id' => 1, 'first_name' => 'Roche', 'last_name' => 'Plando', 'email' => 'admin@musiclocker.local', 'password_hash' => '$2y$10$Q/LetPPPaWnd60XA.VpHo.36e0sKAuuDM2kcASfvU0zx6lirJGbPe', 'email_verified' => true, 'verification_token' => null, 'reset_token' => null, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-10-13 13:45:04', 'last_login' => '2025-10-13 13:45:04', 'status' => 'active', 'role' => 'admin'],
            ['id' => 3, 'first_name' => 'Reynaldo', 'last_name' => 'Jr.', 'email' => 'reynaldogrande9@gmail.com', 'password_hash' => '$2y$10$11WveYDY/nt7KVSnNWUZH.tMi4VCdXNXu4We45NJOgqiPUsKghhYC', 'email_verified' => false, 'verification_token' => '5c567482054f88096ba1b975636467b6c88877a8def2a0e0dd36d0159cd7190a', 'reset_token' => 'f1cff0cb95d567b763201d35fa2c1a8e118141e3e4d24b2965b5bbcb4d631b13', 'created_at' => '2025-09-04 18:25:13', 'updated_at' => '2025-10-12 12:25:42', 'last_login' => '2025-10-12 12:25:42', 'status' => 'active', 'role' => 'user'],
            ['id' => 6, 'first_name' => 'Shawn Patrick ', 'last_name' => 'Dayanan', 'email' => 'alexismae69420@gmail.com', 'password_hash' => '$2y$10$BAvH7PlZIsn/Q8m2tVRYzO8nGedQsy4Ibs6PswsCfpC4W4LX5MLcC', 'email_verified' => false, 'verification_token' => '0d69fe6eb874cfdcbcc0723aa693162c553a10683d3b87741b6ee50f1d855ba0', 'reset_token' => null, 'created_at' => '2025-09-05 07:54:34', 'updated_at' => '2025-10-11 23:22:29', 'last_login' => '2025-09-05 07:54:50', 'status' => 'active', 'role' => 'user'],
            ['id' => 9, 'first_name' => 'shawn ', 'last_name' => 'Everest', 'email' => 'dayananshawn@gmail.com', 'password_hash' => '$2y$10$wVHG2/C9t0DhFNLjR3YPXeaR/EEfA2yUg7bF/zhML9lm09SVQ0c2u', 'email_verified' => false, 'verification_token' => '8a908f3e63bead14a6e165d1f1c258bcbd84daae77edd574151730fc4bb6260e', 'reset_token' => null, 'created_at' => '2025-10-12 11:07:04', 'updated_at' => '2025-10-12 19:41:00', 'last_login' => '2025-10-12 19:41:00', 'status' => 'inactive', 'role' => 'user'],
            ['id' => 11, 'first_name' => 'Reynaldo', 'last_name' => 'Grande Jr. II', 'email' => 'testing@gmail.com', 'password_hash' => '$2y$10$Yle0MGXEp0zLzpsW6P2wMev6hAoJ5slYOQi3kNNC5pIUhwREliI7K', 'email_verified' => false, 'verification_token' => '0c5b534bdb83640b565c28288dd8fd69922bd734aa565e5c6bc19dd5b6351cfe', 'reset_token' => null, 'created_at' => '2025-10-12 12:21:14', 'updated_at' => '2025-10-13 13:44:55', 'last_login' => '2025-10-13 13:44:06', 'status' => 'active', 'role' => 'user'],
            ['id' => 12, 'first_name' => 'Louis', 'last_name' => 'Grande Jr. II', 'email' => 'testis@gmail.com', 'password_hash' => '$2y$10$/H97caXG318HMTOgK0.Yc.b3VyGDc1qz3B7pj2juZFVYDu5s8B9C2', 'email_verified' => false, 'verification_token' => 'b9cc9b3f37cbacbfcd21237c5155ef73795f0f46642fd001664eba1ad2b951bb', 'reset_token' => null, 'created_at' => '2025-10-12 19:31:37', 'updated_at' => '2025-10-12 19:38:09', 'last_login' => '2025-10-12 19:38:09', 'status' => 'inactive', 'role' => 'user'],
        ];

        DB::table('users')->insert($users);
        $this->command->info('Imported ' . count($users) . ' users');
    }

    private function importTags(): void
    {
        $this->command->info('Importing tags...');

        $tags = [
            // User 1 tags
            ['id' => 1, 'user_id' => 1, 'name' => 'Favorites', 'color' => '#ff6b6b', 'description' => 'Personal favorite tracks', 'is_system_tag' => true, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-09-04 17:18:47'],
            ['id' => 2, 'user_id' => 1, 'name' => 'Chill', 'color' => '#4ecdc4', 'description' => 'Relaxing and calm music', 'is_system_tag' => true, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-09-04 17:18:47'],
            ['id' => 3, 'user_id' => 1, 'name' => 'Workout', 'color' => '#45b7d1', 'description' => 'High energy tracks for exercise', 'is_system_tag' => true, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-09-04 17:18:47'],
            ['id' => 4, 'user_id' => 1, 'name' => 'Study', 'color' => '#96ceb4', 'description' => 'Focus music for studying', 'is_system_tag' => true, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-09-04 17:18:47'],
            ['id' => 5, 'user_id' => 1, 'name' => 'Party', 'color' => '#feca57', 'description' => 'Upbeat music for social gatherings', 'is_system_tag' => true, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-09-04 17:18:47'],
            ['id' => 6, 'user_id' => 1, 'name' => 'Nostalgic', 'color' => '#ff9ff3', 'description' => 'Music that brings back memories', 'is_system_tag' => true, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-09-04 17:18:47'],
            ['id' => 7, 'user_id' => 1, 'name' => 'Discover', 'color' => '#00d4ff', 'description' => 'Recently discovered tracks', 'is_system_tag' => true, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-09-04 17:18:47'],
            ['id' => 8, 'user_id' => 1, 'name' => 'Top Rated', 'color' => '#8a2be2', 'description' => '5-star personal ratings', 'is_system_tag' => true, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-09-04 17:18:47'],
            // User 3 tags
            ['id' => 17, 'user_id' => 3, 'name' => 'Favorites', 'color' => '#ff6b6b', 'description' => 'Personal favorite tracks', 'is_system_tag' => true, 'created_at' => '2025-09-04 18:25:13', 'updated_at' => '2025-09-04 18:25:13'],
            ['id' => 18, 'user_id' => 3, 'name' => 'Chill', 'color' => '#4ecdc4', 'description' => 'Relaxing and calm music', 'is_system_tag' => true, 'created_at' => '2025-09-04 18:25:13', 'updated_at' => '2025-09-04 18:25:13'],
            ['id' => 19, 'user_id' => 3, 'name' => 'Workout', 'color' => '#45b7d1', 'description' => 'High energy tracks for exercise', 'is_system_tag' => true, 'created_at' => '2025-09-04 18:25:13', 'updated_at' => '2025-09-04 18:25:13'],
            ['id' => 20, 'user_id' => 3, 'name' => 'Study', 'color' => '#96ceb4', 'description' => 'Focus music for studying', 'is_system_tag' => true, 'created_at' => '2025-09-04 18:25:13', 'updated_at' => '2025-09-04 18:25:13'],
            ['id' => 21, 'user_id' => 3, 'name' => 'Party', 'color' => '#feca57', 'description' => 'Upbeat music for social gatherings', 'is_system_tag' => true, 'created_at' => '2025-09-04 18:25:13', 'updated_at' => '2025-09-04 18:25:13'],
            ['id' => 22, 'user_id' => 3, 'name' => 'Nostalgic', 'color' => '#ff9ff3', 'description' => 'Music that brings back memories', 'is_system_tag' => true, 'created_at' => '2025-09-04 18:25:13', 'updated_at' => '2025-09-04 18:25:13'],
            ['id' => 23, 'user_id' => 3, 'name' => 'Discover', 'color' => '#00d4ff', 'description' => 'Recently discovered tracks', 'is_system_tag' => true, 'created_at' => '2025-09-04 18:25:13', 'updated_at' => '2025-09-04 18:25:13'],
            ['id' => 24, 'user_id' => 3, 'name' => 'Top Rated', 'color' => '#8a2be2', 'description' => '5-star personal ratings', 'is_system_tag' => true, 'created_at' => '2025-09-04 18:25:13', 'updated_at' => '2025-09-04 18:25:13'],
            // User 6 tags
            ['id' => 41, 'user_id' => 6, 'name' => 'Favorites', 'color' => '#ff6b6b', 'description' => 'Personal favorite tracks', 'is_system_tag' => true, 'created_at' => '2025-09-05 07:54:34', 'updated_at' => '2025-09-05 07:54:34'],
            ['id' => 42, 'user_id' => 6, 'name' => 'Chill', 'color' => '#4ecdc4', 'description' => 'Relaxing and calm music', 'is_system_tag' => true, 'created_at' => '2025-09-05 07:54:34', 'updated_at' => '2025-09-05 07:54:34'],
            ['id' => 43, 'user_id' => 6, 'name' => 'Workout', 'color' => '#45b7d1', 'description' => 'High energy tracks for exercise', 'is_system_tag' => true, 'created_at' => '2025-09-05 07:54:34', 'updated_at' => '2025-09-05 07:54:34'],
            ['id' => 44, 'user_id' => 6, 'name' => 'Study', 'color' => '#96ceb4', 'description' => 'Focus music for studying', 'is_system_tag' => true, 'created_at' => '2025-09-05 07:54:34', 'updated_at' => '2025-09-05 07:54:34'],
            ['id' => 45, 'user_id' => 6, 'name' => 'Party', 'color' => '#feca57', 'description' => 'Upbeat music for social gatherings', 'is_system_tag' => true, 'created_at' => '2025-09-05 07:54:34', 'updated_at' => '2025-09-05 07:54:34'],
            ['id' => 46, 'user_id' => 6, 'name' => 'Nostalgic', 'color' => '#ff9ff3', 'description' => 'Music that brings back memories', 'is_system_tag' => true, 'created_at' => '2025-09-05 07:54:34', 'updated_at' => '2025-09-05 07:54:34'],
            ['id' => 47, 'user_id' => 6, 'name' => 'Discover', 'color' => '#00d4ff', 'description' => 'Recently discovered tracks', 'is_system_tag' => true, 'created_at' => '2025-09-05 07:54:34', 'updated_at' => '2025-09-05 07:54:34'],
            ['id' => 48, 'user_id' => 6, 'name' => 'Top Rated', 'color' => '#8a2be2', 'description' => '5-star personal ratings', 'is_system_tag' => true, 'created_at' => '2025-09-05 07:54:34', 'updated_at' => '2025-09-05 07:54:34'],
            // User 9 tags
            ['id' => 67, 'user_id' => 9, 'name' => 'Favorites', 'color' => '#ff6b6b', 'description' => 'Personal favorite tracks', 'is_system_tag' => true, 'created_at' => '2025-10-12 11:07:04', 'updated_at' => '2025-10-12 11:07:04'],
            ['id' => 68, 'user_id' => 9, 'name' => 'Chill', 'color' => '#4ecdc4', 'description' => 'Relaxing and calm music', 'is_system_tag' => true, 'created_at' => '2025-10-12 11:07:04', 'updated_at' => '2025-10-12 11:07:04'],
            ['id' => 69, 'user_id' => 9, 'name' => 'Workout', 'color' => '#45b7d1', 'description' => 'High energy tracks for exercise', 'is_system_tag' => true, 'created_at' => '2025-10-12 11:07:04', 'updated_at' => '2025-10-12 11:07:04'],
            ['id' => 70, 'user_id' => 9, 'name' => 'Study', 'color' => '#96ceb4', 'description' => 'Focus music for studying', 'is_system_tag' => true, 'created_at' => '2025-10-12 11:07:04', 'updated_at' => '2025-10-12 11:07:04'],
            ['id' => 71, 'user_id' => 9, 'name' => 'Party', 'color' => '#feca57', 'description' => 'Upbeat music for social gatherings', 'is_system_tag' => true, 'created_at' => '2025-10-12 11:07:04', 'updated_at' => '2025-10-12 11:07:04'],
            ['id' => 72, 'user_id' => 9, 'name' => 'Nostalgic', 'color' => '#ff9ff3', 'description' => 'Music that brings back memories', 'is_system_tag' => true, 'created_at' => '2025-10-12 11:07:04', 'updated_at' => '2025-10-12 11:07:04'],
            ['id' => 73, 'user_id' => 9, 'name' => 'Discover', 'color' => '#00d4ff', 'description' => 'Recently discovered tracks', 'is_system_tag' => true, 'created_at' => '2025-10-12 11:07:04', 'updated_at' => '2025-10-12 11:07:04'],
            ['id' => 74, 'user_id' => 9, 'name' => 'Top Rated', 'color' => '#8a2be2', 'description' => '5-star personal ratings', 'is_system_tag' => true, 'created_at' => '2025-10-12 11:07:04', 'updated_at' => '2025-10-12 11:07:04'],
            // User 11 tags
            ['id' => 83, 'user_id' => 11, 'name' => 'Favorites', 'color' => '#ff6b6b', 'description' => 'Personal favorite tracks', 'is_system_tag' => true, 'created_at' => '2025-10-12 12:21:14', 'updated_at' => '2025-10-12 12:21:14'],
            ['id' => 84, 'user_id' => 11, 'name' => 'Chill', 'color' => '#4ecdc4', 'description' => 'Relaxing and calm music', 'is_system_tag' => true, 'created_at' => '2025-10-12 12:21:14', 'updated_at' => '2025-10-12 12:21:14'],
            ['id' => 85, 'user_id' => 11, 'name' => 'Workout', 'color' => '#45b7d1', 'description' => 'High energy tracks for exercise', 'is_system_tag' => true, 'created_at' => '2025-10-12 12:21:14', 'updated_at' => '2025-10-12 12:21:14'],
            ['id' => 86, 'user_id' => 11, 'name' => 'Study', 'color' => '#96ceb4', 'description' => 'Focus music for studying', 'is_system_tag' => true, 'created_at' => '2025-10-12 12:21:14', 'updated_at' => '2025-10-12 12:21:14'],
            ['id' => 87, 'user_id' => 11, 'name' => 'Party', 'color' => '#feca57', 'description' => 'Upbeat music for social gatherings', 'is_system_tag' => true, 'created_at' => '2025-10-12 12:21:14', 'updated_at' => '2025-10-12 12:21:14'],
            ['id' => 88, 'user_id' => 11, 'name' => 'Nostalgic', 'color' => '#ff9ff3', 'description' => 'Music that brings back memories', 'is_system_tag' => true, 'created_at' => '2025-10-12 12:21:14', 'updated_at' => '2025-10-12 12:21:14'],
            ['id' => 89, 'user_id' => 11, 'name' => 'Discover', 'color' => '#00d4ff', 'description' => 'Recently discovered tracks', 'is_system_tag' => true, 'created_at' => '2025-10-12 12:21:14', 'updated_at' => '2025-10-12 12:21:14'],
            ['id' => 90, 'user_id' => 11, 'name' => 'Top Rated', 'color' => '#8a2be2', 'description' => '5-star personal ratings', 'is_system_tag' => true, 'created_at' => '2025-10-12 12:21:14', 'updated_at' => '2025-10-12 12:21:14'],
            // User 12 tags
            ['id' => 91, 'user_id' => 12, 'name' => 'Favorites', 'color' => '#ff6b6b', 'description' => 'Personal favorite tracks', 'is_system_tag' => true, 'created_at' => '2025-10-12 19:31:37', 'updated_at' => '2025-10-12 19:31:37'],
            ['id' => 92, 'user_id' => 12, 'name' => 'Chill', 'color' => '#4ecdc4', 'description' => 'Relaxing and calm music', 'is_system_tag' => true, 'created_at' => '2025-10-12 19:31:37', 'updated_at' => '2025-10-12 19:31:37'],
            ['id' => 93, 'user_id' => 12, 'name' => 'Workout', 'color' => '#45b7d1', 'description' => 'High energy tracks for exercise', 'is_system_tag' => true, 'created_at' => '2025-10-12 19:31:37', 'updated_at' => '2025-10-12 19:31:37'],
            ['id' => 94, 'user_id' => 12, 'name' => 'Study', 'color' => '#96ceb4', 'description' => 'Focus music for studying', 'is_system_tag' => true, 'created_at' => '2025-10-12 19:31:37', 'updated_at' => '2025-10-12 19:31:37'],
            ['id' => 95, 'user_id' => 12, 'name' => 'Party', 'color' => '#feca57', 'description' => 'Upbeat music for social gatherings', 'is_system_tag' => true, 'created_at' => '2025-10-12 19:31:37', 'updated_at' => '2025-10-12 19:31:37'],
            ['id' => 96, 'user_id' => 12, 'name' => 'Nostalgic', 'color' => '#ff9ff3', 'description' => 'Music that brings back memories', 'is_system_tag' => true, 'created_at' => '2025-10-12 19:31:37', 'updated_at' => '2025-10-12 19:31:37'],
            ['id' => 97, 'user_id' => 12, 'name' => 'Discover', 'color' => '#00d4ff', 'description' => 'Recently discovered tracks', 'is_system_tag' => true, 'created_at' => '2025-10-12 19:31:37', 'updated_at' => '2025-10-12 19:31:37'],
            ['id' => 98, 'user_id' => 12, 'name' => 'Top Rated', 'color' => '#8a2be2', 'description' => '5-star personal ratings', 'is_system_tag' => true, 'created_at' => '2025-10-12 19:31:37', 'updated_at' => '2025-10-12 19:31:37'],
        ];

        DB::table('tags')->insert($tags);
        $this->command->info('Imported ' . count($tags) . ' tags');
    }

    private function importSystemSettings(): void
    {
        $this->command->info('Importing system settings...');

        $settings = [
            ['id' => 1, 'setting_key' => 'app_name', 'setting_value' => 'Music Lockerr', 'setting_type' => 'string', 'description' => 'Application name', 'is_public' => true, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-10-12 13:13:22'],
            ['id' => 2, 'setting_key' => 'app_version', 'setting_value' => '1.0.0', 'setting_type' => 'string', 'description' => 'Current application version', 'is_public' => true, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-10-12 13:13:22'],
            ['id' => 3, 'setting_key' => 'max_music_entries_per_user', 'setting_value' => '10000', 'setting_type' => 'integer', 'description' => 'Maximum music entries per user', 'is_public' => false, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-10-12 13:13:22'],
            ['id' => 4, 'setting_key' => 'session_timeout', 'setting_value' => '3600', 'setting_type' => 'integer', 'description' => 'Session timeout in seconds', 'is_public' => false, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-10-12 13:13:22'],
            ['id' => 5, 'setting_key' => 'enable_spotify_integration', 'setting_value' => '1', 'setting_type' => 'boolean', 'description' => 'Enable Spotify API integration', 'is_public' => false, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-10-12 13:13:22'],
            ['id' => 6, 'setting_key' => 'default_items_per_page', 'setting_value' => '20', 'setting_type' => 'integer', 'description' => 'Default pagination limit', 'is_public' => true, 'created_at' => '2025-09-04 17:18:47', 'updated_at' => '2025-10-12 13:13:22'],
        ];

        DB::table('system_settings')->insert($settings);
        $this->command->info('Imported ' . count($settings) . ' system settings');
    }

    private function importMusicEntries(): void
    {
        $this->command->info('Importing music entries...');

        $entries = [
            ['id' => 1, 'user_id' => 3, 'title' => 'Doomer', 'artist' => 'Tokyo Manaka', 'album' => 'Doomer', 'genre' => '', 'release_year' => 2025, 'duration' => 148, 'spotify_id' => '3J8h8AUh100VIaFbSjCxB4', 'spotify_url' => 'https://open.spotify.com/track/3J8h8AUh100VIaFbSjCxB4', 'album_art_url' => 'https://i.scdn.co/image/ab67616d0000b273438093c80a894ce3391a327f', 'personal_rating' => 0, 'date_added' => '2025-09-04 22:34:10', 'date_discovered' => '2025-09-05', 'is_favorite' => false, 'created_at' => '2025-09-04 22:34:10', 'updated_at' => '2025-09-04 22:34:10'],
            ['id' => 2, 'user_id' => 3, 'title' => 'Retry Now', 'artist' => 'NAKISO', 'album' => 'Retry Now', 'genre' => '', 'release_year' => 2025, 'duration' => 122, 'spotify_id' => '7gKt5lOImlJ2bOOcrODFQY', 'spotify_url' => 'https://open.spotify.com/track/7gKt5lOImlJ2bOOcrODFQY', 'album_art_url' => 'https://i.scdn.co/image/ab67616d0000b2732429f90dbf06e9b229abcda1', 'personal_rating' => 4, 'date_added' => '2025-09-04 22:35:35', 'date_discovered' => '2025-09-05', 'is_favorite' => false, 'created_at' => '2025-09-04 22:35:35', 'updated_at' => '2025-09-04 22:35:35'],
            ['id' => 16, 'user_id' => 1, 'title' => 'キティ (feat. 宵崎奏&朝比奈まふゆ&東雲絵名&暁山瑞希&鏡音レン)', 'artist' => '25時、ナイトコードで。', 'album' => 'ザムザ/キティ', 'genre' => '', 'release_year' => 2023, 'duration' => 208, 'spotify_id' => '6Ho58lFgmMy2Frbwj5zfzY', 'spotify_url' => 'https://open.spotify.com/track/6Ho58lFgmMy2Frbwj5zfzY', 'album_art_url' => 'https://i.scdn.co/image/ab67616d0000b273cf4de70b65d447091979be0f', 'personal_rating' => 4, 'date_added' => '2025-10-09 19:41:14', 'date_discovered' => '2025-10-09', 'is_favorite' => false, 'created_at' => '2025-10-09 19:41:14', 'updated_at' => '2025-10-09 19:41:14'],
            ['id' => 17, 'user_id' => 1, 'title' => 'ザムザ (feat. 宵崎奏&朝比奈まふゆ&東雲絵名&暁山瑞希&KAITO)', 'artist' => '25時、ナイトコードで。', 'album' => 'ザムザ/キティ', 'genre' => '', 'release_year' => 2023, 'duration' => 214, 'spotify_id' => '62kWs1t5ncSoao7EY7yVBD', 'spotify_url' => 'https://open.spotify.com/track/62kWs1t5ncSoao7EY7yVBD', 'album_art_url' => 'https://i.scdn.co/image/ab67616d0000b273cf4de70b65d447091979be0f', 'personal_rating' => 4, 'date_added' => '2025-10-09 19:41:39', 'date_discovered' => '2025-10-09', 'is_favorite' => false, 'created_at' => '2025-10-09 19:41:39', 'updated_at' => '2025-10-09 19:41:39'],
            ['id' => 19, 'user_id' => 1, 'title' => 'PPPP (feat. Hatsune Miku, Kasane Teto)', 'artist' => 'TAK, Hatsune Miku, Kasane Teto', 'album' => 'PPPP', 'genre' => 'Vocaloid', 'release_year' => 2025, 'duration' => 155, 'spotify_id' => '6J3pPfXLujwsWQpvR6XMgC', 'spotify_url' => 'https://open.spotify.com/track/6J3pPfXLujwsWQpvR6XMgC', 'album_art_url' => 'https://i.scdn.co/image/ab67616d0000b273e2acdafe9b0c10beeda9a277', 'personal_rating' => 5, 'date_added' => '2025-10-09 23:05:05', 'date_discovered' => '2025-10-10', 'is_favorite' => false, 'created_at' => '2025-10-09 23:05:05', 'updated_at' => '2025-10-09 23:28:13'],
            ['id' => 20, 'user_id' => 1, 'title' => 'Medicine', 'artist' => 'Sasuke Haraguchi', 'album' => 'Medicine', 'genre' => 'Vocaloid', 'release_year' => 2024, 'duration' => 120, 'spotify_id' => '6oQd9KEbRMERESY1pftFyn', 'spotify_url' => 'https://open.spotify.com/track/6oQd9KEbRMERESY1pftFyn', 'album_art_url' => 'https://i.scdn.co/image/ab67616d0000b27369fc8b848dc3ccb2824b18de', 'personal_rating' => 5, 'date_added' => '2025-10-09 23:11:07', 'date_discovered' => '2025-10-10', 'is_favorite' => true, 'created_at' => '2025-10-09 23:11:07', 'updated_at' => '2025-10-10 08:07:47'],
            ['id' => 21, 'user_id' => 3, 'title' => 'Bohemian Rhapsody', 'artist' => 'Queen', 'album' => 'Bohemian Rhapsody (The Original Soundtrack)', 'genre' => 'Classic Rock', 'release_year' => 2018, 'duration' => 355, 'spotify_id' => '3z8h0TU7ReDPLIbEnYhWZb', 'spotify_url' => 'https://open.spotify.com/track/3z8h0TU7ReDPLIbEnYhWZb', 'album_art_url' => 'https://i.scdn.co/image/ab67616d0000b273e8b066f70c206551210d902b', 'personal_rating' => 3, 'date_added' => '2025-10-11 22:06:35', 'date_discovered' => '2025-10-12', 'is_favorite' => true, 'created_at' => '2025-10-11 22:06:35', 'updated_at' => '2025-10-11 22:06:35'],
            ['id' => 22, 'user_id' => 3, 'title' => 'Multo', 'artist' => 'Cup of Joe', 'album' => 'Multo', 'genre' => 'Opm', 'release_year' => 2024, 'duration' => 238, 'spotify_id' => '4cBm8rv2B5BJWU2pDaHVbF', 'spotify_url' => 'https://open.spotify.com/track/4cBm8rv2B5BJWU2pDaHVbF', 'album_art_url' => 'https://i.scdn.co/image/ab67616d0000b273394048503e3be0e65e962638', 'personal_rating' => 4, 'date_added' => '2025-10-11 23:11:18', 'date_discovered' => '2025-10-12', 'is_favorite' => false, 'created_at' => '2025-10-11 23:11:18', 'updated_at' => '2025-10-11 23:11:42'],
            ['id' => 23, 'user_id' => 1, 'title' => 'Welcome to The Internet', 'artist' => 'Bo Burnham', 'album' => 'INSIDE', 'genre' => 'Comedy', 'release_year' => 2021, 'duration' => 276, 'spotify_id' => '3s44Qv8x974tm0ueLexMWN', 'spotify_url' => 'https://open.spotify.com/track/3s44Qv8x974tm0ueLexMWN', 'album_art_url' => 'https://i.scdn.co/image/ab67616d0000b27388fed14b936c38007a302413', 'personal_rating' => 3, 'date_added' => '2025-10-11 23:30:41', 'date_discovered' => '2025-10-12', 'is_favorite' => false, 'created_at' => '2025-10-11 23:30:41', 'updated_at' => '2025-10-11 23:30:41'],
            ['id' => 30, 'user_id' => 9, 'title' => 'Multo', 'artist' => 'Cup of Joe', 'album' => 'Multo', 'genre' => 'Opm', 'release_year' => 2024, 'duration' => 238, 'spotify_id' => '4cBm8rv2B5BJWU2pDaHVbF', 'spotify_url' => 'https://open.spotify.com/track/4cBm8rv2B5BJWU2pDaHVbF', 'album_art_url' => 'https://i.scdn.co/image/ab67616d0000b273394048503e3be0e65e962638', 'personal_rating' => 3, 'date_added' => '2025-10-12 11:10:07', 'date_discovered' => '2025-10-12', 'is_favorite' => true, 'created_at' => '2025-10-12 11:10:07', 'updated_at' => '2025-10-12 11:12:38'],
            ['id' => 31, 'user_id' => 9, 'title' => 'a thousand bad times', 'artist' => 'killhussein', 'album' => 'a thousand bad times', 'genre' => 'Dark ambient', 'release_year' => 2022, 'duration' => 178, 'spotify_id' => '53aQjyHqKLg7f7XjiEmRhK', 'spotify_url' => 'https://open.spotify.com/track/53aQjyHqKLg7f7XjiEmRhK', 'album_art_url' => 'https://i.scdn.co/image/ab67616d0000b27322c328820eb111b2839b3cc2', 'personal_rating' => 3, 'date_added' => '2025-10-12 11:10:49', 'date_discovered' => '2025-10-12', 'is_favorite' => true, 'created_at' => '2025-10-12 11:10:49', 'updated_at' => '2025-10-12 11:12:37'],
            ['id' => 32, 'user_id' => 11, 'title' => 'Glimpse of Us', 'artist' => 'Joji', 'album' => 'Glimpse of Us', 'genre' => 'sadboi type', 'release_year' => 2022, 'duration' => 233, 'spotify_id' => '3aBGKDiAAvH2H7HLOyQ4US', 'spotify_url' => 'https://open.spotify.com/track/3aBGKDiAAvH2H7HLOyQ4US', 'album_art_url' => 'https://i.scdn.co/image/ab67616d0000b273f3f7d2ea2ad435b57d6697df', 'personal_rating' => 3, 'date_added' => '2025-10-12 12:27:10', 'date_discovered' => '2025-10-12', 'is_favorite' => false, 'created_at' => '2025-10-12 12:27:10', 'updated_at' => '2025-10-12 12:27:10'],
            ['id' => 33, 'user_id' => 12, 'title' => 'TruE', 'artist' => 'HOYO-MiX, 黄龄', 'album' => 'TruE (Honkai Impact 3rd "Because of You" Animated Short Theme Song)', 'genre' => 'Soundtrack', 'release_year' => 2022, 'duration' => 188, 'spotify_id' => '56aR8fCNORk8XIrQGo75IQ', 'spotify_url' => 'https://open.spotify.com/track/56aR8fCNORk8XIrQGo75IQ', 'album_art_url' => 'https://i.scdn.co/image/ab67616d0000b2736d2a60f14703d1ddf9b5334e', 'personal_rating' => 5, 'date_added' => '2025-10-12 19:33:00', 'date_discovered' => '2025-10-12', 'is_favorite' => false, 'created_at' => '2025-10-12 19:33:00', 'updated_at' => '2025-10-12 19:33:10'],
        ];

        DB::table('music_entries')->insert($entries);
        $this->command->info('Imported ' . count($entries) . ' music entries');
    }

    private function importMusicNotes(): void
    {
        $this->command->info('Importing music notes...');

        $notes = [
            ['id' => 3, 'music_entry_id' => 19, 'user_id' => 1, 'note_text' => 'ssdsd', 'mood' => '', 'memory_context' => '', 'listening_context' => '', 'created_at' => '2025-10-09 23:05:05', 'updated_at' => '2025-10-09 23:28:13'],
            ['id' => 4, 'music_entry_id' => 20, 'user_id' => 1, 'note_text' => 'ssds', 'mood' => 'dsds', 'memory_context' => 'sdsd', 'listening_context' => 'sdsds', 'created_at' => '2025-10-09 23:11:07', 'updated_at' => '2025-10-10 08:07:47'],
            ['id' => 5, 'music_entry_id' => 21, 'user_id' => 3, 'note_text' => 'comfort song', 'mood' => '', 'memory_context' => '', 'listening_context' => '', 'created_at' => '2025-10-11 22:06:35', 'updated_at' => '2025-10-11 22:06:35'],
            ['id' => 6, 'music_entry_id' => 22, 'user_id' => 3, 'note_text' => 'minumulto na ko nang damdamin ko~', 'mood' => '', 'memory_context' => '', 'listening_context' => '', 'created_at' => '2025-10-11 23:11:18', 'updated_at' => '2025-10-11 23:11:42'],
            ['id' => 8, 'music_entry_id' => 32, 'user_id' => 11, 'note_text' => 'adsadasdasdasdsadasdsad', 'mood' => '', 'memory_context' => '', 'listening_context' => '', 'created_at' => '2025-10-12 12:27:10', 'updated_at' => '2025-10-12 12:27:10'],
            ['id' => 9, 'music_entry_id' => 33, 'user_id' => 12, 'note_text' => 'cyrene', 'mood' => '', 'memory_context' => '', 'listening_context' => '', 'created_at' => '2025-10-12 19:33:00', 'updated_at' => '2025-10-12 19:33:00'],
        ];

        DB::table('music_notes')->insert($notes);
        $this->command->info('Imported ' . count($notes) . ' music notes');
    }

    private function importMusicEntryTags(): void
    {
        $this->command->info('Importing music entry tags...');

        $entryTags = [
            ['id' => 5, 'music_entry_id' => 19, 'tag_id' => 7, 'created_at' => '2025-10-09 23:28:13'],
            ['id' => 6, 'music_entry_id' => 19, 'tag_id' => 1, 'created_at' => '2025-10-09 23:28:13'],
            ['id' => 7, 'music_entry_id' => 19, 'tag_id' => 6, 'created_at' => '2025-10-09 23:28:13'],
            ['id' => 8, 'music_entry_id' => 20, 'tag_id' => 2, 'created_at' => '2025-10-10 08:07:47'],
            ['id' => 9, 'music_entry_id' => 20, 'tag_id' => 7, 'created_at' => '2025-10-10 08:07:47'],
            ['id' => 10, 'music_entry_id' => 20, 'tag_id' => 1, 'created_at' => '2025-10-10 08:07:47'],
            ['id' => 11, 'music_entry_id' => 21, 'tag_id' => 18, 'created_at' => '2025-10-11 22:06:35'],
            ['id' => 12, 'music_entry_id' => 21, 'tag_id' => 22, 'created_at' => '2025-10-11 22:06:35'],
            ['id' => 13, 'music_entry_id' => 22, 'tag_id' => 22, 'created_at' => '2025-10-11 23:11:42'],
            ['id' => 14, 'music_entry_id' => 22, 'tag_id' => 19, 'created_at' => '2025-10-11 23:11:42'],
            ['id' => 32, 'music_entry_id' => 32, 'tag_id' => 83, 'created_at' => '2025-10-12 12:27:10'],
            ['id' => 33, 'music_entry_id' => 33, 'tag_id' => 96, 'created_at' => '2025-10-12 19:33:00'],
            ['id' => 34, 'music_entry_id' => 33, 'tag_id' => 98, 'created_at' => '2025-10-12 19:33:00'],
        ];

        DB::table('music_entry_tags')->insert($entryTags);
        $this->command->info('Imported ' . count($entryTags) . ' music entry tags');
    }

    private function importPlaylists(): void
    {
        $this->command->info('Importing playlists...');

        $playlists = [
            ['id' => 2, 'user_id' => 3, 'name' => 'egeqg', 'description' => 'dgdgdgs', 'is_public' => false, 'cover_image_url' => null, 'created_at' => '2025-10-11 22:06:55', 'updated_at' => '2025-10-12 11:23:27'],
            ['id' => 3, 'user_id' => 1, 'name' => 'playlist1', 'description' => 'sdasdas', 'is_public' => false, 'cover_image_url' => null, 'created_at' => '2025-10-11 23:48:24', 'updated_at' => '2025-10-12 13:10:51'],
            ['id' => 7, 'user_id' => 11, 'name' => 'nigga', 'description' => 'asdasdsa', 'is_public' => false, 'cover_image_url' => null, 'created_at' => '2025-10-13 13:32:54', 'updated_at' => '2025-10-13 13:32:59'],
        ];

        DB::table('playlists')->insert($playlists);
        $this->command->info('Imported ' . count($playlists) . ' playlists');
    }

    private function importPlaylistEntries(): void
    {
        $this->command->info('Importing playlist entries...');

        $playlistEntries = [
            ['id' => 4, 'playlist_id' => 2, 'music_entry_id' => 21, 'position' => 1, 'added_by_user_id' => 3, 'created_at' => '2025-10-12 11:23:26'],
            ['id' => 5, 'playlist_id' => 2, 'music_entry_id' => 22, 'position' => 2, 'added_by_user_id' => 3, 'created_at' => '2025-10-12 11:23:27'],
            ['id' => 6, 'playlist_id' => 3, 'music_entry_id' => 17, 'position' => 1, 'added_by_user_id' => 1, 'created_at' => '2025-10-12 13:10:50'],
            ['id' => 7, 'playlist_id' => 3, 'music_entry_id' => 16, 'position' => 2, 'added_by_user_id' => 1, 'created_at' => '2025-10-12 13:10:50'],
            ['id' => 8, 'playlist_id' => 3, 'music_entry_id' => 23, 'position' => 3, 'added_by_user_id' => 1, 'created_at' => '2025-10-12 13:10:50'],
            ['id' => 9, 'playlist_id' => 3, 'music_entry_id' => 19, 'position' => 4, 'added_by_user_id' => 1, 'created_at' => '2025-10-12 13:10:51'],
            ['id' => 10, 'playlist_id' => 7, 'music_entry_id' => 32, 'position' => 1, 'added_by_user_id' => 11, 'created_at' => '2025-10-13 13:32:59'],
        ];

        DB::table('playlist_entries')->insert($playlistEntries);
        $this->command->info('Imported ' . count($playlistEntries) . ' playlist entries');
    }

    private function importUserSessions(): void
    {
        $this->command->info('Importing user sessions...');

        $sessions = [
            ['id' => '08c438lp73a18csurmvgq1dhgb', 'user_id' => 1, 'ip_address' => '::1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'csrf_token' => '89d1fad15f9c965ff6c5d726c9cb5908524c471d7e0f6a4251e763937d72d0f5', 'created_at' => '2025-10-12 13:10:08', 'expires_at' => '2025-10-12 08:10:08', 'last_activity' => '2025-10-12 13:12:30', 'is_active' => false],
            ['id' => '2sr4qnkf3jbojh6fd63oubj0ml', 'user_id' => 1, 'ip_address' => '::1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'csrf_token' => '1cfc9d43e8be80b5c2d21946a1e038c97a5642d652e09cd47b8601354358c4ce', 'created_at' => '2025-10-12 13:13:07', 'expires_at' => '2025-10-12 08:13:07', 'last_activity' => '2025-10-12 19:34:43', 'is_active' => false],
            ['id' => 'b8gq4fd2hpa7tqmq39lc0ufkui', 'user_id' => 12, 'ip_address' => '::1', 'user_agent' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', 'csrf_token' => '8ed2305751c754023f2a574874f531fa87221d181f0f1dbeb7d7e24612fe5750', 'created_at' => '2025-10-12 19:31:49', 'expires_at' => '2025-11-11 12:31:49', 'last_activity' => '2025-10-12 19:34:01', 'is_active' => false],
            ['id' => 'dnmnl5ch5uf1nbbtectcvopbm1', 'user_id' => 1, 'ip_address' => '::1', 'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', 'csrf_token' => '516f0f8a2e885dbf8efee8ebb6dae3a8c6de09f7b97892bf6cc527f42e00ad6b', 'created_at' => '2025-10-13 13:28:26', 'expires_at' => '2025-11-12 06:28:26', 'last_activity' => '2025-10-13 13:29:39', 'is_active' => false],
            ['id' => 'gsjde5gcojjp3bcdb1j82k1joh', 'user_id' => 11, 'ip_address' => '::1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'csrf_token' => '79d6ae39fa687172a9b79853a1e069eff40997b68c4c8f237c29e66f27b6601c', 'created_at' => '2025-10-13 13:44:06', 'expires_at' => '2025-10-13 08:44:06', 'last_activity' => '2025-10-13 13:44:55', 'is_active' => false],
            ['id' => 'outbtc5binh4mletphsi3kt60t', 'user_id' => 9, 'ip_address' => '::1', 'user_agent' => 'Mozilla/5.0 (Linux; Android 13; CPH2237 Build/TP1A.220905.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/140.0.7339.207 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/528.0.0.62.107;]', 'csrf_token' => '7e5076213372349c70c7b6a07115469272e0b1c142ece4919bfcd3aba8967280', 'created_at' => '2025-10-12 19:38:57', 'expires_at' => '2025-10-12 14:38:57', 'last_activity' => '2025-10-12 19:39:51', 'is_active' => false],
            ['id' => 'v1t2c83k7rfmae5cpfj193pivj', 'user_id' => 11, 'ip_address' => '::1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'csrf_token' => '0b6d06603f0068ec6bffc84ad00534cb6b41b6ad23e78a75cd98d3bddca73f48', 'created_at' => '2025-10-13 13:32:33', 'expires_at' => '2025-10-13 08:32:33', 'last_activity' => '2025-10-13 13:35:48', 'is_active' => false],
        ];

        DB::table('user_sessions')->insert($sessions);
        $this->command->info('Imported ' . count($sessions) . ' user sessions');
    }

    private function importActivityLog(): void
    {
        $this->command->info('Importing activity log...');

        $logs = [
            ['id' => 6, 'user_id' => 3, 'action' => 'login', 'target_type' => 'user', 'target_id' => 3, 'description' => 'User logged in successfully', 'ip_address' => '::1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'created_at' => '2025-09-04 18:25:20'],
            ['id' => 7, 'user_id' => 3, 'action' => 'music_entry_add', 'target_type' => 'music_entry', 'target_id' => 1, 'description' => 'Added music entry: Doomer by Tokyo Manaka', 'ip_address' => '::1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'created_at' => '2025-09-04 22:34:10'],
            ['id' => 8, 'user_id' => 3, 'action' => 'music_entry_add', 'target_type' => 'music_entry', 'target_id' => 2, 'description' => 'Added music entry: Retry Now by NAKISO', 'ip_address' => '::1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'created_at' => '2025-09-04 22:35:35'],
            ['id' => 9, 'user_id' => 3, 'action' => 'music_entry_add', 'target_type' => 'music_entry', 'target_id' => 3, 'description' => 'Added music entry: Bohemian Rhapsody by Queen', 'ip_address' => '::1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'created_at' => '2025-09-04 23:18:58'],
            ['id' => 10, 'user_id' => 3, 'action' => 'delete_music_entry', 'target_type' => 'music_entry', 'target_id' => 3, 'description' => 'Deleted music entry: Bohemian Rhapsody', 'ip_address' => '::1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'created_at' => '2025-09-04 23:19:10'],
        ];

        DB::table('activity_log')->insert($logs);
        $this->command->info('Imported ' . count($logs) . ' activity log entries');
    }

    private function resetSequences(): void
    {
        $this->command->info('Resetting database sequences...');

        DB::select("SELECT setval('users_id_seq', (SELECT COALESCE(MAX(id), 1) FROM users))");
        DB::select("SELECT setval('music_entries_id_seq', (SELECT COALESCE(MAX(id), 1) FROM music_entries))");
        DB::select("SELECT setval('music_entry_tags_id_seq', (SELECT COALESCE(MAX(id), 1) FROM music_entry_tags))");
        DB::select("SELECT setval('music_notes_id_seq', (SELECT COALESCE(MAX(id), 1) FROM music_notes))");
        DB::select("SELECT setval('playlists_id_seq', (SELECT COALESCE(MAX(id), 1) FROM playlists))");
        DB::select("SELECT setval('playlist_entries_id_seq', (SELECT COALESCE(MAX(id), 1) FROM playlist_entries))");
        DB::select("SELECT setval('system_settings_id_seq', (SELECT COALESCE(MAX(id), 1) FROM system_settings))");
        DB::select("SELECT setval('tags_id_seq', (SELECT COALESCE(MAX(id), 1) FROM tags))");
        DB::select("SELECT setval('activity_log_id_seq', (SELECT COALESCE(MAX(id), 1) FROM activity_log))");

        $this->command->info('Database sequences reset successfully');
    }
}
