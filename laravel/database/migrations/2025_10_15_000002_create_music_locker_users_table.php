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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);
            $table->boolean('email_verified')->default(false);
            $table->string('verification_token', 255)->nullable();
            $table->string('reset_token', 255)->nullable();
            $table->rememberToken();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('last_login')->nullable();
            $table->string('status', 20)->default('active');
            $table->string('role', 10)->default('user');

            // Add check constraints using raw SQL
            $table->index('email', 'idx_email');
        });

        // Add check constraints
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_check CHECK (status IN ('active', 'inactive', 'suspended'))");
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('user', 'admin'))");

        // Create indexes
        DB::statement('CREATE INDEX idx_users_email ON users (email)');
        DB::statement('CREATE INDEX idx_users_status ON users (status)');
        DB::statement('CREATE INDEX idx_users_created_at ON users (created_at)');
        DB::statement('CREATE INDEX idx_users_role ON users (role)');

        // Create trigger
        DB::statement('
            CREATE TRIGGER update_users_updated_at
                BEFORE UPDATE ON users
                FOR EACH ROW
                EXECUTE FUNCTION update_updated_at_column()
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
