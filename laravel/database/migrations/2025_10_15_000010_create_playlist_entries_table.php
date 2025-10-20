<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('playlist_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playlist_id')->constrained('playlists')->onDelete('cascade');
            $table->foreignId('music_entry_id')->constrained('music_entries')->onDelete('cascade');
            $table->integer('position');
            $table->foreignId('added_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();

            // Unique constraint
            $table->unique(['playlist_id', 'position'], 'unique_playlist_position');
        });

        // Create indexes
        DB::statement('CREATE INDEX idx_playlist_entries_playlist_id ON playlist_entries (playlist_id)');
        DB::statement('CREATE INDEX idx_playlist_entries_music_entry_id ON playlist_entries (music_entry_id)');
        DB::statement('CREATE INDEX idx_playlist_entries_added_by_user_id ON playlist_entries (added_by_user_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist_entries');
    }
};
