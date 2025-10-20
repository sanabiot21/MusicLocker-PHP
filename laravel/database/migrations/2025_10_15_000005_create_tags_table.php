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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name', 50);
            $table->string('color', 7)->default('#6c757d')->comment('Hex color code');
            $table->text('description')->nullable();
            $table->boolean('is_system_tag')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            // Unique constraint
            $table->unique(['user_id', 'name'], 'unique_user_tag');
        });

        // Create indexes
        DB::statement('CREATE INDEX idx_tags_user_id ON tags (user_id)');
        DB::statement('CREATE INDEX idx_tags_is_system_tag ON tags (is_system_tag)');

        // Create trigger
        DB::statement('
            CREATE TRIGGER update_tags_updated_at
                BEFORE UPDATE ON tags
                FOR EACH ROW
                EXECUTE FUNCTION update_updated_at_column()
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
