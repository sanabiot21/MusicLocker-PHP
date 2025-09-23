-- Music Locker Database Schema
-- Created: August 28, 2025
-- Team: NaturalStupidity
-- Compatible with MySQL 8.0+ and MariaDB 10.5+

CREATE DATABASE IF NOT EXISTS music_locker 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE music_locker;

-- Users table for authentication and user management
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_token_expires TIMESTAMP NULL,
    spotify_access_token TEXT NULL,
    spotify_refresh_token TEXT NULL,
    spotify_token_expires TIMESTAMP NULL,
    spotify_user_id VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_spotify_user_id (spotify_user_id)
);

-- Music entries table for storing user's music catalog
CREATE TABLE music_entries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    album VARCHAR(255),
    genre VARCHAR(100),
    release_year YEAR,
    duration INT, -- in seconds
    spotify_id VARCHAR(255),
    spotify_url VARCHAR(500),
    album_art_url VARCHAR(500),
    preview_url VARCHAR(500),
    external_urls JSON,
    personal_rating TINYINT CHECK (personal_rating BETWEEN 1 AND 5),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_discovered DATE,
    times_played INT DEFAULT 0,
    last_played TIMESTAMP NULL,
    is_favorite BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_artist (artist),
    INDEX idx_genre (genre),
    INDEX idx_title (title),
    INDEX idx_date_added (date_added),
    INDEX idx_personal_rating (personal_rating),
    INDEX idx_is_favorite (is_favorite),
    UNIQUE KEY unique_user_spotify (user_id, spotify_id)
);

-- Personal notes for music entries
CREATE TABLE music_notes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    music_entry_id INT NOT NULL,
    user_id INT NOT NULL,
    note_text TEXT NOT NULL,
    mood VARCHAR(50),
    memory_context VARCHAR(255),
    listening_context VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (music_entry_id) REFERENCES music_entries(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_music_entry_id (music_entry_id),
    INDEX idx_user_id (user_id),
    INDEX idx_mood (mood),
    INDEX idx_created_at (created_at)
);

-- Tags system for categorizing music
CREATE TABLE tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(7) DEFAULT '#6c757d', -- Hex color code
    description TEXT,
    is_system_tag BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_tag (user_id, name),
    INDEX idx_user_id (user_id),
    INDEX idx_is_system_tag (is_system_tag)
);

-- Many-to-many relationship between music entries and tags
CREATE TABLE music_entry_tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    music_entry_id INT NOT NULL,
    tag_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (music_entry_id) REFERENCES music_entries(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    UNIQUE KEY unique_entry_tag (music_entry_id, tag_id),
    INDEX idx_music_entry_id (music_entry_id),
    INDEX idx_tag_id (tag_id)
);

-- User sessions for security
CREATE TABLE user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    csrf_token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at),
    INDEX idx_is_active (is_active)
);

-- Activity log for user actions
CREATE TABLE activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_type VARCHAR(50), -- 'music_entry', 'tag', 'note', etc.
    target_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_target_type (target_type),
    INDEX idx_created_at (created_at)
);

-- Playlists (future feature)
CREATE TABLE playlists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    cover_image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_public (is_public),
    INDEX idx_updated_at (updated_at)
);

-- Playlist entries (future feature)
CREATE TABLE playlist_entries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    playlist_id INT NOT NULL,
    music_entry_id INT NOT NULL,
    position INT NOT NULL,
    added_by_user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (music_entry_id) REFERENCES music_entries(id) ON DELETE CASCADE,
    FOREIGN KEY (added_by_user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_playlist_position (playlist_id, position),
    INDEX idx_playlist_id (playlist_id),
    INDEX idx_music_entry_id (music_entry_id)
);

-- System settings for application configuration
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_setting_key (setting_key),
    INDEX idx_is_public (is_public)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (first_name, last_name, email, password_hash, email_verified, status) 
VALUES ('Admin', 'User', 'admin@musiclocker.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE, 'active');

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

-- Create indexes for better performance
CREATE INDEX idx_music_entries_search ON music_entries (title, artist, album);
CREATE INDEX idx_music_entries_date_rating ON music_entries (date_added, personal_rating);
CREATE INDEX idx_activity_log_recent ON activity_log (user_id, created_at DESC);

-- Create views for common queries
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

-- Stored procedure for user activity logging
DELIMITER //

CREATE PROCEDURE LogUserActivity(
    IN p_user_id INT,
    IN p_action VARCHAR(100),
    IN p_target_type VARCHAR(50),
    IN p_target_id INT,
    IN p_description TEXT,
    IN p_ip_address VARCHAR(45),
    IN p_user_agent TEXT
)
BEGIN
    INSERT INTO activity_log (
        user_id, action, target_type, target_id, 
        description, ip_address, user_agent
    ) VALUES (
        p_user_id, p_action, p_target_type, p_target_id,
        p_description, p_ip_address, p_user_agent
    );
END //

DELIMITER ;

-- Trigger to automatically update user's last activity
DELIMITER //

CREATE TRIGGER update_user_last_activity
AFTER UPDATE ON user_sessions
FOR EACH ROW
BEGIN
    IF NEW.last_activity > OLD.last_activity THEN
        UPDATE users SET updated_at = NOW() WHERE id = NEW.user_id;
    END IF;
END //

DELIMITER ;

-- Function to calculate user's music discovery rate
DELIMITER //

CREATE FUNCTION GetUserDiscoveryRate(p_user_id INT, p_days INT)
RETURNS DECIMAL(10,2)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE discovery_rate DECIMAL(10,2);
    
    SELECT COUNT(*) / p_days INTO discovery_rate
    FROM music_entries 
    WHERE user_id = p_user_id 
    AND date_added >= DATE_SUB(NOW(), INTERVAL p_days DAY);
    
    RETURN IFNULL(discovery_rate, 0);
END //

DELIMITER ;

-- Create full-text search indexes for better search performance
-- ALTER TABLE music_entries ADD FULLTEXT(title, artist, album);
-- ALTER TABLE music_notes ADD FULLTEXT(note_text, memory_context);

-- Performance optimization: Clean up expired sessions daily
CREATE EVENT IF NOT EXISTS cleanup_expired_sessions
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
DELETE FROM user_sessions WHERE expires_at < NOW() OR is_active = FALSE;

-- Set event scheduler to ON
SET GLOBAL event_scheduler = ON;