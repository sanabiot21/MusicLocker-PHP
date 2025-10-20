<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Static Helper Methods

    /**
     * Get setting value with automatic type casting
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->setting_value, $setting->setting_type);
    }

    /**
     * Set setting value
     */
    public static function set(string $key, $value, ?string $type = null): bool
    {
        $type = $type ?? self::inferType($value);

        $setting = self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => is_array($value) || is_object($value) ? json_encode($value) : $value,
                'setting_type' => $type,
            ]
        );

        Cache::forget("setting.{$key}");

        return (bool) $setting;
    }

    /**
     * Delete a setting
     */
    public static function remove(string $key): bool
    {
        Cache::forget("setting.{$key}");
        return (bool) self::where('setting_key', $key)->delete();
    }

    /**
     * Get all public settings
     */
    public static function getPublicSettings(): array
    {
        return self::where('is_public', true)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->setting_key => self::castValue($setting->setting_value, $setting->setting_type)];
            })
            ->toArray();
    }

    // Private Helper Methods

    /**
     * Cast value based on type
     */
    private static function castValue($value, string $type)
    {
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => (bool) $value || $value === '1' || $value === 'true',
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Infer type from value
     */
    private static function inferType($value): string
    {
        if (is_int($value)) {
            return 'integer';
        }

        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_array($value) || is_object($value)) {
            return 'json';
        }

        return 'string';
    }
}
