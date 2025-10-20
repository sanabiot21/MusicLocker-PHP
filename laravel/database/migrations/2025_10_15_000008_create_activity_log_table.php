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
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action', 100);
            $table->string('target_type', 50)->nullable()->comment('music_entry, tag, note, etc.');
            $table->integer('target_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Create indexes
        DB::statement('CREATE INDEX idx_activity_log_user_id ON activity_log (user_id)');
        DB::statement('CREATE INDEX idx_activity_log_action ON activity_log (action)');
        DB::statement('CREATE INDEX idx_activity_log_target_type ON activity_log (target_type)');
        DB::statement('CREATE INDEX idx_activity_log_created_at ON activity_log (created_at)');
        DB::statement('CREATE INDEX idx_activity_log_recent ON activity_log (user_id, created_at DESC)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
