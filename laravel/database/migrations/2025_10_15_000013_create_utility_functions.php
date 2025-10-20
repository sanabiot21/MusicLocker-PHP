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
        // Function for user activity logging
        DB::statement("
            CREATE OR REPLACE FUNCTION log_user_activity(
                p_user_id INT,
                p_action VARCHAR(100),
                p_target_type VARCHAR(50),
                p_target_id INT,
                p_description TEXT,
                p_ip_address VARCHAR(45),
                p_user_agent TEXT
            )
            RETURNS VOID AS $$
            BEGIN
                INSERT INTO activity_log (
                    user_id, action, target_type, target_id,
                    description, ip_address, user_agent
                ) VALUES (
                    p_user_id, p_action, p_target_type, p_target_id,
                    p_description, p_ip_address, p_user_agent
                );
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Function to calculate user's music discovery rate
        DB::statement("
            CREATE OR REPLACE FUNCTION get_user_discovery_rate(p_user_id INT, p_days INT)
            RETURNS DECIMAL(10,2) AS $$
            DECLARE
                discovery_rate DECIMAL(10,2);
            BEGIN
                SELECT COUNT(*)::DECIMAL / p_days INTO discovery_rate
                FROM music_entries
                WHERE user_id = p_user_id
                AND date_added >= CURRENT_TIMESTAMP - INTERVAL '1 day' * p_days;

                RETURN COALESCE(discovery_rate, 0);
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Function to update user's last activity
        DB::statement("
            CREATE OR REPLACE FUNCTION update_user_last_activity()
            RETURNS TRIGGER AS $$
            BEGIN
                IF NEW.last_activity > OLD.last_activity THEN
                    UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.user_id;
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Trigger on user_sessions to update user's last activity
        DB::statement("
            CREATE TRIGGER update_user_last_activity_trigger
                AFTER UPDATE ON user_sessions
                FOR EACH ROW
                EXECUTE FUNCTION update_user_last_activity()
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS update_user_last_activity_trigger ON user_sessions');
        DB::statement('DROP FUNCTION IF EXISTS update_user_last_activity()');
        DB::statement('DROP FUNCTION IF EXISTS get_user_discovery_rate(INT, INT)');
        DB::statement('DROP FUNCTION IF EXISTS log_user_activity(INT, VARCHAR, VARCHAR, INT, TEXT, VARCHAR, TEXT)');
    }
};
