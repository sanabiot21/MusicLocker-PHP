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
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->string('cover_image_url', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        // Create indexes
        DB::statement('CREATE INDEX idx_playlists_user_id ON playlists (user_id)');
        DB::statement('CREATE INDEX idx_playlists_is_public ON playlists (is_public)');
        DB::statement('CREATE INDEX idx_playlists_updated_at ON playlists (updated_at)');

        // Create trigger
        DB::statement('
            CREATE TRIGGER update_playlists_updated_at
                BEFORE UPDATE ON playlists
                FOR EACH ROW
                EXECUTE FUNCTION update_updated_at_column()
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlists');
    }
};
