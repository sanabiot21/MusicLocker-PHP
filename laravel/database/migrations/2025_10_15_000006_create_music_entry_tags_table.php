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
        Schema::create('music_entry_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('music_entry_id')->constrained('music_entries')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();

            // Unique constraint
            $table->unique(['music_entry_id', 'tag_id'], 'unique_entry_tag');
        });

        // Create indexes
        DB::statement('CREATE INDEX idx_music_entry_tags_music_entry_id ON music_entry_tags (music_entry_id)');
        DB::statement('CREATE INDEX idx_music_entry_tags_tag_id ON music_entry_tags (tag_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('music_entry_tags');
    }
};
