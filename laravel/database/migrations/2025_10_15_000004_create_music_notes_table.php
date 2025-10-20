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
        Schema::create('music_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('music_entry_id')->constrained('music_entries')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('note_text');
            $table->string('mood', 50)->nullable();
            $table->string('memory_context', 255)->nullable();
            $table->string('listening_context', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        // Create indexes
        DB::statement('CREATE INDEX idx_music_notes_music_entry_id ON music_notes (music_entry_id)');
        DB::statement('CREATE INDEX idx_music_notes_user_id ON music_notes (user_id)');
        DB::statement('CREATE INDEX idx_music_notes_mood ON music_notes (mood)');
        DB::statement('CREATE INDEX idx_music_notes_created_at ON music_notes (created_at)');

        // Create trigger
        DB::statement('
            CREATE TRIGGER update_music_notes_updated_at
                BEFORE UPDATE ON music_notes
                FOR EACH ROW
                EXECUTE FUNCTION update_updated_at_column()
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('music_notes');
    }
};
