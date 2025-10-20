<?php

/**
 * Global Helper Functions
 * Music Locker - Laravel Application
 */

if (!function_exists('formatDuration')) {
    /**
     * Format duration from seconds to MM:SS format
     *
     * @param int $seconds
     * @return string
     */
    function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) {
            return '0:00';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%d:%02d', $minutes, $remainingSeconds);
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format a date in a consistent format
     *
     * @param \Carbon\Carbon|string|null $date
     * @param string $format
     * @return string
     */
    function formatDate($date, string $format = 'F j, Y'): string
    {
        if (!$date) {
            return 'N/A';
        }

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        return $date->format($format);
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format a datetime in a consistent format
     *
     * @param \Carbon\Carbon|string|null $datetime
     * @return string
     */
    function formatDateTime($datetime): string
    {
        return formatDate($datetime, 'F j, Y g:i A');
    }
}

if (!function_exists('truncateText')) {
    /**
     * Truncate text to a specific length with ellipsis
     *
     * @param string|null $text
     * @param int $length
     * @param string $append
     * @return string
     */
    function truncateText(?string $text, int $length = 100, string $append = '...'): string
    {
        if (!$text) {
            return '';
        }

        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . $append;
    }
}

if (!function_exists('formatNumber')) {
    /**
     * Format a number with thousands separator
     *
     * @param int|float $number
     * @return string
     */
    function formatNumber($number): string
    {
        return number_format($number);
    }
}

if (!function_exists('getTagColorClass')) {
    /**
     * Get CSS class for tag color
     *
     * @param string|null $color
     * @return string
     */
    function getTagColorClass(?string $color): string
    {
        if (!$color) {
            return 'tag-default';
        }

        // Map color names to CSS classes
        $colorMap = [
            'blue' => 'tag-blue',
            'green' => 'tag-green',
            'red' => 'tag-red',
            'yellow' => 'tag-yellow',
            'purple' => 'tag-purple',
            'pink' => 'tag-pink',
            'orange' => 'tag-orange',
            'teal' => 'tag-teal',
            'indigo' => 'tag-indigo',
            'gray' => 'tag-gray',
        ];

        return $colorMap[$color] ?? 'tag-default';
    }
}

if (!function_exists('getRatingStars')) {
    /**
     * Get HTML for rating stars
     *
     * @param int $rating
     * @param int $max
     * @return string
     */
    function getRatingStars(int $rating, int $max = 5): string
    {
        $html = '';
        for ($i = 1; $i <= $max; $i++) {
            $filled = $i <= $rating ? 'filled' : '';
            $html .= '<svg class="star-icon ' . $filled . '" width="16" height="16" viewBox="0 0 24 24" fill="' . ($filled ? 'currentColor' : 'none') . '" stroke="currentColor" stroke-width="2">';
            $html .= '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>';
            $html .= '</svg>';
        }
        return $html;
    }
}

if (!function_exists('asset_versioned')) {
    /**
     * Get a versioned asset URL (for cache busting)
     *
     * @param string $path
     * @return string
     */
    function asset_versioned(string $path): string
    {
        $fullPath = public_path($path);
        $version = file_exists($fullPath) ? filemtime($fullPath) : time();

        return asset($path) . '?v=' . $version;
    }
}

if (!function_exists('isActiveRoute')) {
    /**
     * Check if the current route matches the given route name(s)
     *
     * @param string|array $routes
     * @param string $activeClass
     * @return string
     */
    function isActiveRoute($routes, string $activeClass = 'active'): string
    {
        $routes = is_array($routes) ? $routes : [$routes];

        foreach ($routes as $route) {
            if (request()->routeIs($route)) {
                return $activeClass;
            }
        }

        return '';
    }
}

if (!function_exists('pluralize')) {
    /**
     * Simple pluralization helper
     *
     * @param int $count
     * @param string $singular
     * @param string|null $plural
     * @return string
     */
    function pluralize(int $count, string $singular, ?string $plural = null): string
    {
        if ($count === 1) {
            return $singular;
        }

        return $plural ?? $singular . 's';
    }
}

if (!function_exists('countWithLabel')) {
    /**
     * Format count with singular/plural label
     *
     * @param int $count
     * @param string $singular
     * @param string|null $plural
     * @return string
     */
    function countWithLabel(int $count, string $singular, ?string $plural = null): string
    {
        return $count . ' ' . pluralize($count, $singular, $plural);
    }
}

if (!function_exists('log_activity')) {
    /**
     * Log admin activity to the activity_log table
     *
     * @param string $action
     * @param string $entity_type
     * @param int $entity_id
     * @param string $description
     * @return void
     */
    function log_activity(string $action, string $entity_type, int $entity_id, string $description): void
    {
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
