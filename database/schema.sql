-- Music Locker Database Schema
-- Created: August 28, 2025
-- Team: NaturalStupidity
-- Compatible with PostgreSQL 13+

-- Create database (run this separately if needed)
-- CREATE DATABASE music_locker;

-- Users table for authentication and user management
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'suspended')),
    role VARCHAR(10) NOT NULL DEFAULT 'user' CHECK (role IN ('user', 'admin')),
    
    CONSTRAINT idx_email UNIQUE (email)
);

-- Create indexes for users table
CREATE INDEX idx_users_email ON users (email);
CREATE INDEX idx_users_status ON users (status);
CREATE INDEX idx_users_created_at ON users (created_at);
CREATE INDEX idx_users_role ON users (role);

-- Create trigger to update updated_at column
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_users_updated_at 
    BEFORE UPDATE ON users 
    FOR EACH ROW 
    EXECUTE FUNCTION update_updated_at_column();

-- Music entries table for storing user's music catalog
CREATE TABLE music_entries (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    album VARCHAR(255),
    genre VARCHAR(100) NOT NULL,
    release_year INT,
    duration INT, -- in seconds
    spotify_id VARCHAR(255),
    spotify_url VARCHAR(500),
    album_art_url VARCHAR(500),
    personal_rating SMALLINT NOT NULL CHECK (personal_rating BETWEEN 1 AND 5),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_discovered DATE,
    is_favorite BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT unique_user_spotify UNIQUE (user_id, spotify_id)
);

-- Create indexes for music_entries table
CREATE INDEX idx_music_entries_user_id ON music_entries (user_id);
CREATE INDEX idx_music_entries_artist ON music_entries (artist);
CREATE INDEX idx_music_entries_genre ON music_entries (genre);
CREATE INDEX idx_music_entries_title ON music_entries (title);
CREATE INDEX idx_music_entries_date_added ON music_entries (date_added);
CREATE INDEX idx_music_entries_personal_rating ON music_entries (personal_rating);
CREATE INDEX idx_music_entries_is_favorite ON music_entries (is_favorite);
CREATE INDEX idx_music_entries_search ON music_entries (title, artist, album);
CREATE INDEX idx_music_entries_date_rating ON music_entries (date_added, personal_rating);

-- Create trigger for music_entries updated_at
CREATE TRIGGER update_music_entries_updated_at 
    BEFORE UPDATE ON music_entries 
    FOR EACH ROW 
    EXECUTE FUNCTION update_updated_at_column();

-- Personal notes for music entries
CREATE TABLE music_notes (
    id SERIAL PRIMARY KEY,
    music_entry_id INT NOT NULL,
    user_id INT NOT NULL,
    note_text TEXT NOT NULL,
    mood VARCHAR(50),
    memory_context VARCHAR(255),
    listening_context VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (music_entry_id) REFERENCES music_entries(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create indexes for music_notes table
CREATE INDEX idx_music_notes_music_entry_id ON music_notes (music_entry_id);
CREATE INDEX idx_music_notes_user_id ON music_notes (user_id);
CREATE INDEX idx_music_notes_mood ON music_notes (mood);
CREATE INDEX idx_music_notes_created_at ON music_notes (created_at);

-- Create trigger for music_notes updated_at
CREATE TRIGGER update_music_notes_updated_at 
    BEFORE UPDATE ON music_notes 
    FOR EACH ROW 
    EXECUTE FUNCTION update_updated_at_column();

-- Tags system for categorizing music
CREATE TABLE tags (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(7) DEFAULT '#6c757d', -- Hex color code
    description TEXT,
    is_system_tag BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT unique_user_tag UNIQUE (user_id, name)
);

-- Create indexes for tags table
CREATE INDEX idx_tags_user_id ON tags (user_id);
CREATE INDEX idx_tags_is_system_tag ON tags (is_system_tag);

-- Create trigger for tags updated_at
CREATE TRIGGER update_tags_updated_at 
    BEFORE UPDATE ON tags 
    FOR EACH ROW 
    EXECUTE FUNCTION update_updated_at_column();

-- Many-to-many relationship between music entries and tags
CREATE TABLE music_entry_tags (
    id SERIAL PRIMARY KEY,
    music_entry_id INT NOT NULL,
    tag_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (music_entry_id) REFERENCES music_entries(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    CONSTRAINT unique_entry_tag UNIQUE (music_entry_id, tag_id)
);

-- Create indexes for music_entry_tags table
CREATE INDEX idx_music_entry_tags_music_entry_id ON music_entry_tags (music_entry_id);
CREATE INDEX idx_music_entry_tags_tag_id ON music_entry_tags (tag_id);

-- User sessions for security
CREATE TABLE user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    csrf_token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create indexes for user_sessions table
CREATE INDEX idx_user_sessions_user_id ON user_sessions (user_id);
CREATE INDEX idx_user_sessions_expires_at ON user_sessions (expires_at);
CREATE INDEX idx_user_sessions_is_active ON user_sessions (is_active);

-- Activity log for user actions
CREATE TABLE activity_log (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_type VARCHAR(50), -- 'music_entry', 'tag', 'note', etc.
    target_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create indexes for activity_log table
CREATE INDEX idx_activity_log_user_id ON activity_log (user_id);
CREATE INDEX idx_activity_log_action ON activity_log (action);
CREATE INDEX idx_activity_log_target_type ON activity_log (target_type);
CREATE INDEX idx_activity_log_created_at ON activity_log (created_at);
CREATE INDEX idx_activity_log_recent ON activity_log (user_id, created_at DESC);

-- Playlists (future feature)
CREATE TABLE playlists (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    cover_image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create indexes for playlists table
CREATE INDEX idx_playlists_user_id ON playlists (user_id);
CREATE INDEX idx_playlists_is_public ON playlists (is_public);
CREATE INDEX idx_playlists_updated_at ON playlists (updated_at);

-- Create trigger for playlists updated_at
CREATE TRIGGER update_playlists_updated_at 
    BEFORE UPDATE ON playlists 
    FOR EACH ROW 
    EXECUTE FUNCTION update_updated_at_column();

-- Playlist entries (future feature)
CREATE TABLE playlist_entries (
    id SERIAL PRIMARY KEY,
    playlist_id INT NOT NULL,
    music_entry_id INT NOT NULL,
    position INT NOT NULL,
    added_by_user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (music_entry_id) REFERENCES music_entries(id) ON DELETE CASCADE,
    FOREIGN KEY (added_by_user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT unique_playlist_position UNIQUE (playlist_id, position)
);

-- Create indexes for playlist_entries table
CREATE INDEX idx_playlist_entries_playlist_id ON playlist_entries (playlist_id);
CREATE INDEX idx_playlist_entries_music_entry_id ON playlist_entries (music_entry_id);
CREATE INDEX idx_playlist_entries_added_by_user_id ON playlist_entries (added_by_user_id);

-- System settings for application configuration
CREATE TABLE system_settings (
    id SERIAL PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type VARCHAR(20) DEFAULT 'string' CHECK (setting_type IN ('string', 'integer', 'boolean', 'json')),
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for system_settings table
CREATE INDEX idx_system_settings_setting_key ON system_settings (setting_key);
CREATE INDEX idx_system_settings_is_public ON system_settings (is_public);

-- Create trigger for system_settings updated_at
CREATE TRIGGER update_system_settings_updated_at 
    BEFORE UPDATE ON system_settings 
    FOR EACH ROW 
    EXECUTE FUNCTION update_updated_at_column();

-- Insert default admin user (password: admin123)
INSERT INTO users (first_name, last_name, email, password_hash, email_verified, status, role) 
VALUES ('Admin', 'User', 'admin@musiclocker.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE, 'active', 'admin');

-- Create default system tags for the admin user
INSERT INTO tags (user_id, name, color, description, is_system_tag) VALUES 
(1, 'Favorites', '#ff6b6b', 'Personal favorite tracks', TRUE),
(1, 'Chill', '#4ecdc4', 'Relaxing and calm music', TRUE),
(1, 'Workout', '#45b7d1', 'High energy tracks for exercise', TRUE),
(1, 'Study', '#96ceb4', 'Focus music for studying', TRUE),
(1, 'Party', '#feca57', 'Upbeat music for social gatherings', TRUE),
(1, 'Nostalgic', '#ff9ff3', 'Music that brings back memories', TRUE),
(1, 'Discover', '#00d4ff', 'Recently discovered tracks', TRUE),
(1, 'Top Rated', '#8a2be2', '5-star personal ratings', TRUE);

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_public) VALUES 
('app_name', 'Music Locker', 'string', 'Application name', TRUE),
('app_version', '1.0.0', 'string', 'Current application version', TRUE),
('max_music_entries_per_user', '10000', 'integer', 'Maximum music entries per user', FALSE),
('session_timeout', '3600', 'integer', 'Session timeout in seconds', FALSE),
('enable_spotify_integration', '1', 'boolean', 'Enable Spotify API integration', FALSE),
('default_items_per_page', '20', 'integer', 'Default pagination limit', TRUE);

-- Create view for common queries
CREATE VIEW user_music_stats AS
SELECT 
    u.id as user_id,
    u.first_name,
    u.last_name,
    COUNT(me.id) as total_entries,
    COUNT(CASE WHEN me.personal_rating = 5 THEN 1 END) as five_star_entries,
    COUNT(CASE WHEN me.is_favorite = TRUE THEN 1 END) as favorite_entries,
    AVG(me.personal_rating) as average_rating,
    COUNT(DISTINCT me.artist) as unique_artists,
    COUNT(DISTINCT me.genre) as unique_genres
FROM users u
LEFT JOIN music_entries me ON u.id = me.user_id
WHERE u.status = 'active'
GROUP BY u.id, u.first_name, u.last_name;

-- Function for user activity logging
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

-- Function to calculate user's music discovery rate
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

-- Trigger to automatically update user's last activity
CREATE OR REPLACE FUNCTION update_user_last_activity()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.last_activity > OLD.last_activity THEN
        UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.user_id;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_user_last_activity_trigger
    AFTER UPDATE ON user_sessions
    FOR EACH ROW
    EXECUTE FUNCTION update_user_last_activity();