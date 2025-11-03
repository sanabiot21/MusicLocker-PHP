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
        // Make note_text nullable in PostgreSQL
        DB::statement('ALTER TABLE music_notes ALTER COLUMN note_text DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set any null values to empty string before making it NOT NULL
        DB::statement("UPDATE music_notes SET note_text = '' WHERE note_text IS NULL");
        // Make note_text NOT NULL again
        DB::statement('ALTER TABLE music_notes ALTER COLUMN note_text SET NOT NULL');
    }
};
