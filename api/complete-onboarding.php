<?php

/**
 * Complete Onboarding API Endpoint
 * Marks user's onboarding tour as completed
 * Verdant SMS v3.0
 */

session_start();
header('Content-Type: application/json');

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Require authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Require CSRF token
csrf_require();

try {
    // First check if column exists, if not create it
    $columns = db()->fetchAll("SHOW COLUMNS FROM users LIKE 'onboarding_completed'");

    if (empty($columns)) {
        db()->query("ALTER TABLE users ADD COLUMN onboarding_completed TINYINT(1) DEFAULT 0");
    }

    // Mark onboarding as completed
    db()->query("UPDATE users SET onboarding_completed = 1 WHERE id = ?", [$_SESSION['user_id']]);

    echo json_encode([
        'success' => true,
        'message' => 'Onboarding completed'
    ]);
} catch (Exception $e) {
    error_log("Onboarding completion error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save onboarding status'
    ]);
}
