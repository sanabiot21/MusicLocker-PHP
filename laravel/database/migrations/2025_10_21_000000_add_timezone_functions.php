<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create function to get current timestamp in Manila timezone
        DB::statement("
            CREATE OR REPLACE FUNCTION current_timestamp_manila()
            RETURNS TIMESTAMP WITH TIME ZONE AS $$
            BEGIN
                RETURN CURRENT_TIMESTAMP AT TIME ZONE 'Asia/Manila';
            END;
            $$ LANGUAGE plpgsql IMMUTABLE;
        ");

        // Create function to convert UTC timestamp to Manila timezone
        DB::statement("
            CREATE OR REPLACE FUNCTION to_manila_timezone(ts TIMESTAMP WITH TIME ZONE)
            RETURNS TIMESTAMP WITH TIME ZONE AS $$
            BEGIN
                RETURN ts AT TIME ZONE 'Asia/Manila';
            END;
            $$ LANGUAGE plpgsql IMMUTABLE;
        ");

        // Create function to convert Manila timestamp to UTC
        DB::statement("
            CREATE OR REPLACE FUNCTION to_utc_timezone(ts TIMESTAMP WITH TIME ZONE)
            RETURNS TIMESTAMP WITH TIME ZONE AS $$
            BEGIN
                RETURN ts AT TIME ZONE 'UTC';
            END;
            $$ LANGUAGE plpgsql IMMUTABLE;
        ");

        // Add comment explaining the timezone setup
        DB::statement("
            COMMENT ON FUNCTION current_timestamp_manila() IS
            'Returns current timestamp in Asia/Manila timezone (UTC+8).
             Use this for all new timestamp operations that need Manila timezone.';
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP FUNCTION IF EXISTS to_utc_timezone(TIMESTAMP WITH TIME ZONE)");
        DB::statement("DROP FUNCTION IF EXISTS to_manila_timezone(TIMESTAMP WITH TIME ZONE)");
        DB::statement("DROP FUNCTION IF EXISTS current_timestamp_manila()");
    }
};
