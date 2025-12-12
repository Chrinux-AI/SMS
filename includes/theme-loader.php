<?php

/**
 * Theme Loader Component
 * Dynamically loads the user's selected theme
 * Verdant SMS v3.0
 */

// Get current user's theme preference
function get_user_theme()
{
    // Default theme
    $default_theme = 'verdant-nature';

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Check for cookie preference
        if (isset($_COOKIE['verdant_theme'])) {
            return validate_theme($_COOKIE['verdant_theme']);
        }
        // Check for system preference via JS (handled client-side)
        return $default_theme;
    }

    // Get theme from database
    try {
        $user = db()->fetchOne("SELECT theme FROM users WHERE id = ?", [$_SESSION['user_id']]);
        if ($user && !empty($user['theme'])) {
            return validate_theme($user['theme']);
        }
    } catch (Exception $e) {
        error_log("Error loading user theme: " . $e->getMessage());
    }

    return $default_theme;
}

// Validate theme name
function validate_theme($theme)
{
    $valid_themes = [
        'verdant-nature',
        'dark-cyber',
        'ocean-blue',
        'sunset-warm',
        'minimal-white',
        'matrix-green',
        'purple-galaxy',
        'high-contrast'
    ];

    return in_array($theme, $valid_themes) ? $theme : 'verdant-nature';
}

// Get all available themes with metadata
function get_available_themes()
{
    return [
        'verdant-nature' => [
            'name' => 'Verdant Nature',
            'description' => 'Emerald green, nature-inspired, calming for schools',
            'icon' => 'leaf',
            'colors' => ['#10b981', '#059669', '#065f46'],
            'type' => 'dark',
            'preview' => 'verdant-nature-preview.png'
        ],
        'dark-cyber' => [
            'name' => 'Dark Cyber',
            'description' => 'Deep black/purple with neon cyan accents',
            'icon' => 'robot',
            'colors' => ['#00ffff', '#8b5cf6', '#f0abfc'],
            'type' => 'dark',
            'preview' => 'dark-cyber-preview.png'
        ],
        'ocean-blue' => [
            'name' => 'Ocean Blue',
            'description' => 'Clean professional blue tones',
            'icon' => 'water',
            'colors' => ['#3b82f6', '#0ea5e9', '#06b6d4'],
            'type' => 'dark',
            'preview' => 'ocean-blue-preview.png'
        ],
        'sunset-warm' => [
            'name' => 'Sunset Warm',
            'description' => 'Warm oranges and soft gradients',
            'icon' => 'sun',
            'colors' => ['#f97316', '#dc2626', '#fbbf24'],
            'type' => 'dark',
            'preview' => 'sunset-warm-preview.png'
        ],
        'minimal-white' => [
            'name' => 'Minimal White',
            'description' => 'Ultra-clean light theme with high contrast',
            'icon' => 'circle',
            'colors' => ['#2563eb', '#f8fafc', '#e2e8f0'],
            'type' => 'light',
            'preview' => 'minimal-white-preview.png'
        ],
        'matrix-green' => [
            'name' => 'Matrix Green',
            'description' => 'Classic hacker green-on-black terminal style',
            'icon' => 'terminal',
            'colors' => ['#00ff00', '#00aa00', '#000000'],
            'type' => 'dark',
            'preview' => 'matrix-green-preview.png'
        ],
        'purple-galaxy' => [
            'name' => 'Purple Galaxy',
            'description' => 'Deep purple with starry accents',
            'icon' => 'star',
            'colors' => ['#a855f7', '#ec4899', '#818cf8'],
            'type' => 'dark',
            'preview' => 'purple-galaxy-preview.png'
        ],
        'high-contrast' => [
            'name' => 'High Contrast',
            'description' => 'WCAG AAA compliant for accessibility',
            'icon' => 'eye',
            'colors' => ['#ffff00', '#ffffff', '#000000'],
            'type' => 'dark',
            'accessibility' => true,
            'preview' => 'high-contrast-preview.png'
        ]
    ];
}

// Update user theme in database
function update_user_theme($user_id, $theme)
{
    $theme = validate_theme($theme);

    try {
        db()->query("UPDATE users SET theme = ? WHERE id = ?", [$theme, $user_id]);

        // Also set cookie for non-logged in pages
        setcookie('verdant_theme', $theme, [
            'expires' => time() + (365 * 24 * 60 * 60),
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => false, // Allow JS access for live preview
            'samesite' => 'Lax'
        ]);

        return true;
    } catch (Exception $e) {
        error_log("Error updating user theme: " . $e->getMessage());
        return false;
    }
}

// Output theme CSS link
function output_theme_css($theme = null)
{
    if ($theme === null) {
        $theme = get_user_theme();
    }

    $theme = validate_theme($theme);

    // Use APP_URL for absolute path (most reliable)
    if (defined('APP_URL')) {
        $path = rtrim(APP_URL, '/') . '/assets/css/themes/' . $theme . '.css';
    } else {
        // Fallback to relative path calculation
        $current_path = $_SERVER['PHP_SELF'];
        $base_parts = explode('/', trim($current_path, '/'));
        array_pop($base_parts); // Remove filename

        // Calculate how many directories deep we are from /attendance/
        $attendance_index = array_search('attendance', $base_parts);
        if ($attendance_index !== false) {
            $depth = count($base_parts) - $attendance_index - 1;
            $base_path = $depth > 0 ? str_repeat('../', $depth) : '';
        } else {
            $base_path = '../';
        }
        $path = $base_path . 'assets/css/themes/' . $theme . '.css';
    }

    echo '<link rel="stylesheet" href="' . htmlspecialchars($path) . '" id="theme-stylesheet">';
}

// Output body class for theme
function get_theme_body_class()
{
    return 'theme-' . get_user_theme();
}
