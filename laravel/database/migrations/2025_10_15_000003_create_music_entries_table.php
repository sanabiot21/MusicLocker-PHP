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
        Schema::create('music_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 255);
            $table->string('artist', 255);
            $table->string('album', 255)->nullable();
            $table->string('genre', 100);
            $table->integer('release_year')->nullable();
            $table->integer('duration')->nullable()->comment('Duration in seconds');
            $table->string('spotify_id', 255)->nullable();
            $table->string('spotify_url', 500)->nullable();
            $table->string('album_art_url', 500)->nullable();
            $table->smallInteger('personal_rating')->default(0);
            $table->timestamp('date_added')->useCurrent();
            $table->date('date_discovered')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            // Unique constraint
            $table->unique(['user_id', 'spotify_id'], 'unique_user_spotify');
        });

        // Add check constraint for personal_rating
        DB::statement("ALTER TABLE music_entries ADD CONSTRAINT music_entries_rating_check CHECK (personal_rating BETWEEN 0 AND 5)");

        // Create indexes
        DB::statement('CREATE INDEX idx_music_entries_user_id ON music_entries (user_id)');
        DB::statement('CREATE INDEX idx_music_entries_artist ON music_entries (artist)');
        DB::statement('CREATE INDEX idx_music_entries_genre ON music_entries (genre)');
        DB::statement('CREATE INDEX idx_music_entries_title ON music_entries (title)');
        DB::statement('CREATE INDEX idx_music_entries_date_added ON music_entries (date_added)');
        DB::statement('CREATE INDEX idx_music_entries_personal_rating ON music_entries (personal_rating)');
        DB::statement('CREATE INDEX idx_music_entries_is_favorite ON music_entries (is_favorite)');
        DB::statement('CREATE INDEX idx_music_entries_search ON music_entries (title, artist, album)');
        DB::statement('CREATE INDEX idx_music_entries_date_rating ON music_entries (date_added, personal_rating)');

        // Create trigger
        DB::statement('
            CREATE TRIGGER update_music_entries_updated_at
                BEFORE UPDATE ON music_entries
                FOR EACH ROW
                EXECUTE FUNCTION update_updated_at_column()
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('music_entries');
    }
};
