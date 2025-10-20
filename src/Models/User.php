<?php

namespace MusicLocker\Models;

use MusicLocker\Services\Database;
use PDO;
use Exception;

/**
 * User Model
 * Handles user authentication and profile management
 */
class User
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create new user account
     */
    public function create(array $userData): ?int
    {
        try {
            $sql = "INSERT INTO users (first_name, last_name, email, password_hash, verification_token, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            $verificationToken = bin2hex(random_bytes(32));
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userData['first_name'],
                $userData['last_name'],
                $userData['email'],
                $hashedPassword,
                $verificationToken
            ]);
            
            $userId = (int)$this->db->lastInsertId();
            
            // Create default tags for new user
            $this->createDefaultTags($userId);
            
            return $userId;
            
        } catch (Exception $e) {
            error_log("User creation error: " . $e->getMessage());
            throw new Exception("Failed to create user account");
        }
    }
    
    /**
     * Find user by email address
     */
    public function findByEmail(string $email): ?array
    {
        try {
            $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
            $result = $this->db->queryOne($sql, [$email]);
            
            return $result;
            
        } catch (Exception $e) {
            error_log("User lookup error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Find user by ID
     */
    public function findById(int $userId): ?array
    {
        try {
            $sql = "SELECT * FROM users WHERE id = ? LIMIT 1";
            $result = $this->db->queryOne($sql, [$userId]);
            
            return $result;
            
        } catch (Exception $e) {
            error_log("User lookup error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Authenticate user with email and password
     */
    public function authenticate(string $email, string $password): ?array
    {
        try {
            $user = $this->findByEmail($email);
            
            if (!$user || !password_verify($password, $user['password_hash'])) {
                return null;
            }
            
            // Update last login
            $this->updateLastLogin($user['id']);
            
            // Remove sensitive data before returning
            unset($user['password_hash'], $user['verification_token'], $user['reset_token']);
            
            return $user;
            
        } catch (Exception $e) {
            error_log("Authentication error: " . $e->getMessage());
            throw new Exception("Authentication failed");
        }
    }
    
    /**
     * Update user's last login timestamp
     */
    public function updateLastLogin(int $userId): bool
    {
        try {
            $sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
            return $this->db->execute($sql, [$userId]);
            
        } catch (Exception $e) {
            error_log("Update last login error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user profile information
     */
    public function updateProfile(int $userId, array $data): bool
    {
        try {
            $fields = [];
            $values = [];
            
            $allowedFields = ['first_name', 'last_name', 'email'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "{$field} = ?";
                    $values[] = $data[$field];
                }
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $values[] = $userId;
            $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
            
            return $this->db->execute($sql, $values);
            
        } catch (Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            throw new Exception("Failed to update profile");
        }
    }
    
    /**
     * Change user password
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        try {
            $user = $this->findById($userId);
            
            if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
                throw new Exception("Current password is incorrect");
            }
            
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $sql = "UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?";
            return $this->db->execute($sql, [$hashedPassword, $userId]);
            
        } catch (Exception $e) {
            error_log("Password change error: " . $e->getMessage());
            throw new Exception("Failed to change password");
        }
    }
    
    /**
     * Generate password reset token
     */
    public function generateResetToken(string $email): ?string
    {
        try {
            $user = $this->findByEmail($email);
            
            if (!$user) {
                return null;
            }
            
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $sql = "UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?";
            $this->db->execute($sql, [$token, $expiry, $user['id']]);
            
            return $token;
            
        } catch (Exception $e) {
            error_log("Reset token error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Reset password using token
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        try {
            $sql = "SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW() LIMIT 1";
            $user = $this->db->queryOne($sql, [$token]);
            
            if (!$user) {
                return false;
            }
            
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $sql = "UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expires = NULL, updated_at = NOW() WHERE id = ?";
            return $this->db->execute($sql, [$hashedPassword, $user['id']]);
            
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if email already exists
     */
    public function emailExists(string $email): bool
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
            $result = $this->db->queryOne($sql, [$email]);
            
            return $result['count'] > 0;
            
        } catch (Exception $e) {
            error_log("Email check error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user statistics
     */
    public function getUserStats(int $userId): array
    {
        try {
            // PostgreSQL requires all non-aggregated columns in GROUP BY
            $sql = "SELECT
                        u.first_name,
                        u.last_name,
                        u.created_at,
                        COUNT(me.id) as total_entries,
                        COUNT(CASE WHEN me.personal_rating = 5 THEN 1 END) as five_star_entries,
                        COUNT(CASE WHEN me.is_favorite = TRUE THEN 1 END) as favorite_entries,
                        AVG(me.personal_rating) as average_rating,
                        COUNT(DISTINCT me.artist) as unique_artists,
                        COUNT(DISTINCT me.genre) as unique_genres
                    FROM users u
                    LEFT JOIN music_entries me ON u.id = me.user_id
                    WHERE u.id = ?
                    GROUP BY u.id, u.first_name, u.last_name, u.created_at";

            $result = $this->db->queryOne($sql, [$userId]);

            return $result ?: [
                'total_entries' => 0,
                'five_star_entries' => 0,
                'favorite_entries' => 0,
                'average_rating' => 0,
                'unique_artists' => 0,
                'unique_genres' => 0
            ];

        } catch (Exception $e) {
            error_log("User stats error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create default tags for new user
     */
    private function createDefaultTags(int $userId): void
    {
        $defaultTags = [
            ['Favorites', '#ff6b6b', 'Personal favorite tracks'],
            ['Chill', '#4ecdc4', 'Relaxing and calm music'],
            ['Workout', '#45b7d1', 'High energy tracks for exercise'],
            ['Study', '#96ceb4', 'Focus music for studying'],
            ['Party', '#feca57', 'Upbeat music for social gatherings'],
            ['Nostalgic', '#ff9ff3', 'Music that brings back memories'],
            ['Discover', '#00d4ff', 'Recently discovered tracks'],
            ['Top Rated', '#8a2be2', '5-star personal ratings']
        ];
        
        $sql = "INSERT INTO tags (user_id, name, color, description, is_system_tag) VALUES (?, ?, ?, ?, TRUE)";
        
        foreach ($defaultTags as $tag) {
            try {
                $this->db->execute($sql, [$userId, $tag[0], $tag[1], $tag[2]]);
            } catch (Exception $e) {
                error_log("Error creating default tag '{$tag[0]}': " . $e->getMessage());
            }
        }
    }
    
    /**
     * Get total number of users (Admin function)
     */
    public function getTotalUsers(): int
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM users";
            $result = $this->db->queryOne($sql);
            return (int)($result['count'] ?? 0);
        } catch (Exception $e) {
            error_log("Get total users error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get number of active users (Admin function)
     */
    public function getActiveUsers(): int
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM users WHERE status = 'active'";
            $result = $this->db->queryOne($sql);
            return (int)($result['count'] ?? 0);
        } catch (Exception $e) {
            error_log("Get active users error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get number of new users registered today (Admin function)
     */
    public function getNewUsersToday(): int
    {
        try {
            // Use DATE() function for cross-database compatibility
            $sql = "SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURRENT_DATE";
            $result = $this->db->queryOne($sql);
            return (int)($result['count'] ?? 0);
        } catch (Exception $e) {
            error_log("Get new users today error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get total number of music entries across all users (Admin function)
     */
    public function getTotalMusicEntries(): int
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM music_entries";
            $result = $this->db->queryOne($sql);
            return (int)($result['count'] ?? 0);
        } catch (Exception $e) {
            error_log("Get total music entries error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get all users with their music entry counts (Admin function)
     */
    public function getAllUsers(int $limit = 50, int $offset = 0): array
    {
        try {
            // PostgreSQL requires all non-aggregated columns in GROUP BY
            $sql = "SELECT u.id, u.first_name, u.last_name, u.email, u.status,
                           u.created_at, u.updated_at, u.last_login,
                           COUNT(me.id) as music_entries_count
                    FROM users u
                    LEFT JOIN music_entries me ON u.id = me.user_id
                    GROUP BY u.id, u.first_name, u.last_name, u.email, u.status,
                             u.created_at, u.updated_at, u.last_login
                    ORDER BY u.created_at DESC
                    LIMIT ? OFFSET ?";

            $results = $this->db->query($sql, [$limit, $offset]);

            // Remove sensitive data (not in query but just in case)
            return array_map(function($user) {
                unset($user['password_hash'], $user['verification_token'], $user['reset_token']);
                return $user;
            }, $results);

        } catch (Exception $e) {
            error_log("Get all users error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get recent user activity (Admin function)
     */
    public function getUserActivity(int $userId, int $limit = 20): array
    {
        try {
            $activity = [];

            // Get recent music entries - use || for PostgreSQL concatenation
            $sql = "SELECT 'music_add' as type, 'Added song' as action,
                           'Added \"' || title || '\" by ' || artist as description,
                           date_added as timestamp
                    FROM music_entries
                    WHERE user_id = ?
                    ORDER BY date_added DESC
                    LIMIT ?";

            $musicActivity = $this->db->query($sql, [$userId, $limit]);
            $activity = array_merge($activity, $musicActivity);
            
            // Get login activity (simplified - would need activity_log table in real implementation)
            $user = $this->findById($userId);
            if ($user && $user['last_login']) {
                $activity[] = [
                    'type' => 'login',
                    'action' => 'Login',
                    'description' => 'Logged in to account',
                    'timestamp' => $user['last_login']
                ];
            }
            
            // Sort by timestamp desc
            usort($activity, function($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });
            
            return array_slice($activity, 0, $limit);
            
        } catch (Exception $e) {
            error_log("Get user activity error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent system-wide activity (Admin function)
     * Optimized to use UNION instead of separate queries + PHP sorting
     */
    public function getRecentActivity(int $limit = 10): array
    {
        try {
            // Use UNION query to combine registrations and music entries, then sort in SQL
            $sql = "(SELECT 'registration' as type,
                            first_name || ' ' || last_name as user_name,
                            email,
                            created_at as timestamp,
                            'bi-person-plus' as icon,
                            'text-success' as color,
                            'success' as badge_color,
                            'New User Registration' as title,
                            first_name || ' ' || last_name || ' joined' as description
                     FROM users)
                    UNION ALL
                    (SELECT 'music_add' as type,
                            u.first_name || ' ' || u.last_name as user_name,
                            NULL as email,
                            me.date_added as timestamp,
                            'bi-music-note' as icon,
                            'text-info' as color,
                            'info' as badge_color,
                            'Music Entry Added' as title,
                            u.first_name || ' ' || u.last_name || ' added \"' || me.title || '\"' as description
                     FROM music_entries me
                     JOIN users u ON me.user_id = u.id)
                    ORDER BY timestamp DESC
                    LIMIT ?";

            $results = $this->db->query($sql, [$limit]);

            // Format results
            $activity = [];
            foreach ($results as $row) {
                $activity[] = [
                    'type' => $row['type'],
                    'icon' => $row['icon'],
                    'color' => $row['color'],
                    'badge_color' => $row['badge_color'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'user_name' => $row['user_name'],
                    'timestamp' => $row['timestamp']
                ];
            }

            return $activity;

        } catch (Exception $e) {
            error_log("Get recent activity error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Toggle user status (Admin function)
     */
    public function toggleStatus(int $userId): bool
    {
        try {
            $user = $this->findById($userId);
            if (!$user) {
                return false;
            }

            $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
            $sql = "UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?";

            return $this->db->execute($sql, [$newStatus, $userId]);

        } catch (Exception $e) {
            error_log("Toggle user status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user account (Admin function)
     */
    public function deleteUser(int $userId): bool
    {
        try {
            // Prevent deleting yourself or primary admin
            if ($userId === 1) {
                throw new Exception("Cannot delete primary admin account");
            }

            $sql = "DELETE FROM users WHERE id = ?";
            return $this->db->execute($sql, [$userId]);

        } catch (Exception $e) {
            error_log("Delete user error: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Log password reset request (for admin notification)
     */
    public function logPasswordResetRequest(int $userId, string $email): bool
    {
        try {
            $sql = "INSERT INTO activity_log (user_id, action, target_type, target_id, description, ip_address, user_agent, created_at)
                    VALUES (?, 'password_reset_request', 'user', ?, ?, ?, ?, NOW())";

            $description = "Password reset requested for email: $email";
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

            return $this->db->execute($sql, [$userId, $userId, $description, $ipAddress, $userAgent]);

        } catch (Exception $e) {
            error_log("Log password reset request error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get pending password reset requests (Admin function)
     */
    public function getPendingResetRequests(): array
    {
        try {
            $sql = "SELECT al.id, al.user_id, al.description, al.created_at,
                           u.first_name, u.last_name, u.email, u.status
                    FROM activity_log al
                    JOIN users u ON al.user_id = u.id
                    WHERE al.action = 'password_reset_request'
                    AND al.created_at >= (NOW() - INTERVAL '7 days')
                    ORDER BY al.created_at DESC";

            return $this->db->query($sql);

        } catch (Exception $e) {
            error_log("Get pending reset requests error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Count pending password reset requests (Admin function)
     */
    public function countPendingResetRequests(): int
    {
        try {
            $sql = "SELECT COUNT(*) as count
                    FROM activity_log
                    WHERE action = 'password_reset_request'
                    AND created_at >= (NOW() - INTERVAL '7 days')";

            $result = $this->db->queryOne($sql);
            return (int)($result['count'] ?? 0);

        } catch (Exception $e) {
            error_log("Count pending reset requests error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Admin reset user password (no email required)
     */
    public function adminResetPassword(int $userId, string $newPassword): bool
    {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?";

            error_log("Admin reset password - User ID: $userId, Hash length: " . strlen($hashedPassword));

            $result = $this->db->execute($sql, [$hashedPassword, $userId]);

            if ($result) {
                error_log("Password reset successful for user ID: $userId");
            } else {
                error_log("Password reset FAILED for user ID: $userId - execute() returned false");
            }

            return $result;

        } catch (Exception $e) {
            error_log("Admin reset password error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Approve password reset request (Admin function)
     */
    public function approveResetRequest(int $userId): bool
    {
        try {
            // PostgreSQL doesn't support ORDER BY/LIMIT in UPDATE, so use subquery
            $sql = "UPDATE activity_log
                    SET action = 'password_reset_approved', description = description || ' - APPROVED'
                    WHERE id = (
                        SELECT id FROM activity_log
                        WHERE user_id = ?
                        AND action = 'password_reset_request'
                        ORDER BY created_at DESC
                        LIMIT 1
                    )";

            return $this->db->execute($sql, [$userId]);

        } catch (Exception $e) {
            error_log("Approve reset request error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user has approved reset request
     */
    public function hasApprovedResetRequest(string $email): ?array
    {
        try {
            $sql = "SELECT al.id, al.user_id, al.created_at
                    FROM activity_log al
                    JOIN users u ON al.user_id = u.id
                    WHERE u.email = ?
                    AND al.action = 'password_reset_approved'
                    AND al.created_at >= (NOW() - INTERVAL '1 day')
                    ORDER BY al.created_at DESC
                    LIMIT 1";

            return $this->db->queryOne($sql, [$email]);

        } catch (Exception $e) {
            error_log("Check approved reset error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if user has pending reset request
     */
    public function hasPendingResetRequest(string $email): ?array
    {
        try {
            $sql = "SELECT al.id, al.user_id, al.created_at
                    FROM activity_log al
                    JOIN users u ON al.user_id = u.id
                    WHERE u.email = ?
                    AND al.action = 'password_reset_request'
                    AND al.created_at >= (NOW() - INTERVAL '7 days')
                    ORDER BY al.created_at DESC
                    LIMIT 1";

            return $this->db->queryOne($sql, [$email]);

        } catch (Exception $e) {
            error_log("Check pending reset error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Clear password reset request from activity log
     */
    public function clearPasswordResetRequest(int $userId): bool
    {
        try {
            $sql = "DELETE FROM activity_log
                    WHERE user_id = ?
                    AND action IN ('password_reset_request', 'password_reset_approved')
                    AND created_at >= (NOW() - INTERVAL '7 days')";

            return $this->db->execute($sql, [$userId]);

        } catch (Exception $e) {
            error_log("Clear password reset request error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get weekly user growth (Admin analytics)
     */
    public function getWeeklyUserGrowth(): int
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM users
                    WHERE created_at >= (NOW() - INTERVAL '7 days')";
            $result = $this->db->queryOne($sql);
            return (int)($result['count'] ?? 0);
        } catch (Exception $e) {
            error_log("Get weekly user growth error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get weekly music count (Admin analytics)
     */
    public function getWeeklyMusicCount(): int
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM music_entries
                    WHERE date_added >= (NOW() - INTERVAL '7 days')";
            $result = $this->db->queryOne($sql);
            return (int)($result['count'] ?? 0);
        } catch (Exception $e) {
            error_log("Get weekly music count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get most active user (Admin analytics)
     */
    public function getMostActiveUser(): ?array
    {
        try {
            // PostgreSQL requires all non-aggregated columns in GROUP BY
            $sql = "SELECT u.id, u.first_name, u.last_name, COUNT(me.id) as song_count
                    FROM users u
                    LEFT JOIN music_entries me ON u.id = me.user_id
                    GROUP BY u.id, u.first_name, u.last_name
                    ORDER BY song_count DESC
                    LIMIT 1";
            return $this->db->queryOne($sql);
        } catch (Exception $e) {
            error_log("Get most active user error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get popular tag (Admin analytics)
     */
    public function getPopularTag(): ?array
    {
        try {
            // PostgreSQL requires all non-aggregated columns in GROUP BY
            $sql = "SELECT t.id, t.name, COUNT(met.music_entry_id) as usage_count
                    FROM tags t
                    LEFT JOIN music_entry_tags met ON t.id = met.tag_id
                    GROUP BY t.id, t.name
                    ORDER BY usage_count DESC
                    LIMIT 1";
            return $this->db->queryOne($sql);
        } catch (Exception $e) {
            error_log("Get popular tag error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate user input data
     */
    public function validate(array $data, array $rules = []): array
    {
        $errors = [];
        
        // Email validation
        if (isset($rules['email']) && $rules['email']) {
            if (empty($data['email'])) {
                $errors['email'] = 'Email address is required';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Please provide a valid email address';
            } elseif ($this->emailExists($data['email'])) {
                $errors['email'] = 'This email address is already registered';
            }
        }
        
        // Password validation
        if (isset($rules['password']) && $rules['password']) {
            if (empty($data['password'])) {
                $errors['password'] = 'Password is required';
            } elseif (strlen($data['password']) < 8) {
                $errors['password'] = 'Password must be at least 8 characters long';
            }
        }
        
        // Name validation
        foreach (['first_name', 'last_name'] as $field) {
            if (isset($rules[$field]) && $rules[$field]) {
                if (empty($data[$field])) {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
                } elseif (strlen($data[$field]) < 2) {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be at least 2 characters';
                }
            }
        }
        
        return $errors;
    }
}