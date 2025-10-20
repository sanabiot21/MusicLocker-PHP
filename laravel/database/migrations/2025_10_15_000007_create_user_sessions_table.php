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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->string('id', 128)->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('csrf_token', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expires_at');
            $table->timestamp('last_activity')->useCurrent();
            $table->boolean('is_active')->default(true);
        });

        // Create indexes
        DB::statement('CREATE INDEX idx_user_sessions_user_id ON user_sessions (user_id)');
        DB::statement('CREATE INDEX idx_user_sessions_expires_at ON user_sessions (expires_at)');
        DB::statement('CREATE INDEX idx_user_sessions_is_active ON user_sessions (is_active)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
