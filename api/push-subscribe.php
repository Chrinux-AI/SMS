<?php

/**
 * Push Subscription API
 * Handles push notification subscription management
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

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Require CSRF token
csrf_require();

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

// Ensure push subscriptions table
try {
    db()->query("CREATE TABLE IF NOT EXISTS push_subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        endpoint TEXT NOT NULL,
        p256dh_key VARCHAR(255),
        auth_key VARCHAR(255),
        user_agent TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_used TIMESTAMP NULL,
        is_active TINYINT(1) DEFAULT 1,
        INDEX idx_user (user_id),
        INDEX idx_active (is_active)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {
    // Table exists
}

switch ($action) {
    case 'subscribe':
        $subscription = $input['subscription'] ?? [];

        if (empty($subscription['endpoint'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid subscription']);
            exit;
        }

        $endpoint = $subscription['endpoint'];
        $p256dh = $subscription['keys']['p256dh'] ?? '';
        $auth = $subscription['keys']['auth'] ?? '';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);

        // Remove existing subscription for this endpoint
        db()->query("DELETE FROM push_subscriptions WHERE endpoint = ?", [$endpoint]);

        // Insert new subscription
        db()->insert('push_subscriptions', [
            'user_id' => $user_id,
            'endpoint' => $endpoint,
            'p256dh_key' => $p256dh,
            'auth_key' => $auth,
            'user_agent' => $ua,
            'is_active' => 1
        ]);

        // Log subscription
        try {
            db()->insert('activity_logs', [
                'user_id' => $user_id,
                'action' => 'push_subscribe',
                'description' => 'Enabled push notifications',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
            ]);
        } catch (Exception $e) {
            // Activity log table might not exist
        }

        echo json_encode(['success' => true, 'message' => 'Subscribed successfully']);
        break;

    case 'unsubscribe':
        $endpoint = $input['endpoint'] ?? '';

        if (!empty($endpoint)) {
            db()->query("DELETE FROM push_subscriptions WHERE user_id = ? AND endpoint = ?", [$user_id, $endpoint]);
        } else {
            // Unsubscribe all
            db()->query("UPDATE push_subscriptions SET is_active = 0 WHERE user_id = ?", [$user_id]);
        }

        echo json_encode(['success' => true, 'message' => 'Unsubscribed successfully']);
        break;

    case 'status':
        $count = db()->count('push_subscriptions', 'user_id = ? AND is_active = 1', [$user_id]);
        echo json_encode([
            'success' => true,
            'enabled' => $count > 0,
            'subscriptions' => $count
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
