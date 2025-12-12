<?php

/**
 * Save Theme API Endpoint
 * Verdant SMS v3.0
 */

session_start();
header('Content-Type: application/json');

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/theme-loader.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated'
    ]);
    exit;
}

// Require valid CSRF token for this API call
csrf_require();

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input) || !isset($input['theme'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No theme specified'
    ]);
    exit;
}

$theme = validate_theme($input['theme']);

// Update user theme
if (update_user_theme($_SESSION['user_id'], $theme)) {
    // Update session
    $_SESSION['theme'] = $theme;

    echo json_encode([
        'success' => true,
        'message' => 'Theme saved successfully',
        'theme' => $theme
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save theme'
    ]);
}
