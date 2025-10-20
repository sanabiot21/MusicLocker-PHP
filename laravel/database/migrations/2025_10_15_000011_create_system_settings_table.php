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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value')->nullable();
            $table->string('setting_type', 20)->default('string');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        // Add check constraint
        DB::statement("ALTER TABLE system_settings ADD CONSTRAINT system_settings_type_check CHECK (setting_type IN ('string', 'integer', 'boolean', 'json'))");

        // Create indexes
        DB::statement('CREATE INDEX idx_system_settings_setting_key ON system_settings (setting_key)');
        DB::statement('CREATE INDEX idx_system_settings_is_public ON system_settings (is_public)');

        // Create trigger
        DB::statement('
            CREATE TRIGGER update_system_settings_updated_at
                BEFORE UPDATE ON system_settings
                FOR EACH ROW
                EXECUTE FUNCTION update_updated_at_column()
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
