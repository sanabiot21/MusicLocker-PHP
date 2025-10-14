<?php

namespace MusicLocker\Models;

use MusicLocker\Services\Database;
use PDO;
use Exception;

/**
 * System Setting Model
 * Music Locker - Team NaturalStupidity
 * 
 * Handles system-wide configuration settings
 */
class SystemSetting
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all system settings
     */
    public function getAll(): array
    {
        try {
            $sql = "SELECT * FROM system_settings ORDER BY setting_key ASC";
            return $this->db->query($sql);
            
        } catch (Exception $e) {
            error_log("Get all settings error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get public settings only
     */
    public function getPublicSettings(): array
    {
        try {
            $sql = "SELECT setting_key, setting_value, setting_type 
                    FROM system_settings 
                    WHERE is_public = TRUE 
                    ORDER BY setting_key ASC";
            return $this->db->query($sql);
            
        } catch (Exception $e) {
            error_log("Get public settings error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get setting by key
     */
    public function get(string $key, $default = null)
    {
        try {
            $sql = "SELECT setting_value, setting_type FROM system_settings WHERE setting_key = ? LIMIT 1";
            $result = $this->db->queryOne($sql, [$key]);
            
            if (!$result) {
                return $default;
            }
            
            return $this->castValue($result['setting_value'], $result['setting_type']);
            
        } catch (Exception $e) {
            error_log("Get setting error: " . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * Set setting value
     */
    public function set(string $key, $value, string $type = 'string', string $description = '', bool $isPublic = false): bool
    {
        try {
            // Check if setting exists
            $existing = $this->db->queryOne("SELECT id FROM system_settings WHERE setting_key = ?", [$key]);
            
            if ($existing) {
                // Update existing
                $sql = "UPDATE system_settings 
                        SET setting_value = ?, setting_type = ?, description = ?, is_public = ?, updated_at = NOW() 
                        WHERE setting_key = ?";
                return $this->db->execute($sql, [$value, $type, $description, $isPublic, $key]);
            } else {
                // Insert new
                $sql = "INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_public) 
                        VALUES (?, ?, ?, ?, ?)";
                return $this->db->execute($sql, [$key, $value, $type, $description, $isPublic]);
            }
            
        } catch (Exception $e) {
            error_log("Set setting error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update multiple settings
     */
    public function updateMultiple(array $settings): bool
    {
        try {
            foreach ($settings as $key => $value) {
                // Get existing setting to preserve type
                $existing = $this->db->queryOne(
                    "SELECT setting_type, description, is_public FROM system_settings WHERE setting_key = ?", 
                    [$key]
                );
                
                if ($existing) {
                    $this->set(
                        $key, 
                        $value, 
                        $existing['setting_type'], 
                        $existing['description'], 
                        (bool)$existing['is_public']
                    );
                }
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Update multiple settings error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete setting
     */
    public function delete(string $key): bool
    {
        try {
            $sql = "DELETE FROM system_settings WHERE setting_key = ?";
            return $this->db->execute($sql, [$key]);
            
        } catch (Exception $e) {
            error_log("Delete setting error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cast value based on type
     */
    private function castValue($value, string $type)
    {
        switch ($type) {
            case 'integer':
                return (int)$value;
            case 'boolean':
                return (bool)$value || $value === '1' || $value === 'true';
            case 'json':
                return json_decode($value, true);
            case 'string':
            default:
                return (string)$value;
        }
    }
    
    /**
     * Get settings grouped by category
     */
    public function getGrouped(): array
    {
        $settings = $this->getAll();
        $grouped = [
            'application' => [],
            'limits' => [],
            'features' => [],
            'other' => []
        ];
        
        foreach ($settings as $setting) {
            $key = $setting['setting_key'];
            
            if (str_starts_with($key, 'app_')) {
                $grouped['application'][] = $setting;
            } elseif (str_starts_with($key, 'max_') || str_starts_with($key, 'default_')) {
                $grouped['limits'][] = $setting;
            } elseif (str_starts_with($key, 'enable_')) {
                $grouped['features'][] = $setting;
            } else {
                $grouped['other'][] = $setting;
            }
        }
        
        return $grouped;
    }
    
    /**
     * Validate setting value based on type
     */
    public function validate(string $key, $value, string $type): ?string
    {
        switch ($type) {
            case 'integer':
                if (!is_numeric($value)) {
                    return "Value must be a number";
                }
                break;
            case 'boolean':
                if (!in_array($value, ['0', '1', 'true', 'false', true, false], true)) {
                    return "Value must be a boolean (0/1 or true/false)";
                }
                break;
            case 'json':
                if (!is_string($value)) {
                    $value = json_encode($value);
                }
                if (json_decode($value) === null && json_last_error() !== JSON_ERROR_NONE) {
                    return "Value must be valid JSON";
                }
                break;
        }
        
        return null;
    }
}





