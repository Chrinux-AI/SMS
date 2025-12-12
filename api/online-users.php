<?php

/**
 * Online Users API
 * Returns online user counts and lists
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

// Ensure sessions table exists
try {
    db()->query("CREATE TABLE IF NOT EXISTS user_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        session_id VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45),
        user_agent TEXT,
        last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_session (session_id),
        INDEX idx_user (user_id),
        INDEX idx_activity (last_activity)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {
    // Table exists
}

$action = $_GET['action'] ?? 'count';
$user_role = $_SESSION['role'] ?? '';

switch ($action) {
    case 'count':
        $count = db()->fetchColumn(
            "SELECT COUNT(DISTINCT user_id) FROM user_sessions WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)"
        ) ?: 0;

        echo json_encode(['success' => true, 'count' => (int)$count]);
        break;

    case 'by_role':
        $can_view = in_array($user_role, ['admin', 'superadmin', 'principal', 'vice-principal', 'owner']);

        if (!$can_view) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            exit;
        }

        $by_role = db()->fetchAll(
            "SELECT u.role, COUNT(DISTINCT u.id) as count
             FROM user_sessions us
             JOIN users u ON us.user_id = u.id
             WHERE us.last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
             GROUP BY u.role
             ORDER BY count DESC"
        );

        echo json_encode(['success' => true, 'data' => $by_role]);
        break;

    case 'list':
        $can_view = in_array($user_role, ['admin', 'superadmin', 'principal', 'vice-principal', 'owner']);

        if (!$can_view) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            exit;
        }

        $limit = min((int)($_GET['limit'] ?? 20), 100);

        $users = db()->fetchAll(
            "SELECT u.id, u.first_name, u.last_name, u.email, u.role, u.profile_image,
                    us.last_activity, us.ip_address
             FROM user_sessions us
             JOIN users u ON us.user_id = u.id
             WHERE us.last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
             GROUP BY u.id
             ORDER BY us.last_activity DESC
             LIMIT ?",
            [$limit]
        );

        // Add time ago
        foreach ($users as &$user) {
            $user['time_ago'] = timeAgo($user['last_activity']);
            unset($user['ip_address']); // Don't expose IP to frontend
        }

        echo json_encode(['success' => true, 'users' => $users]);
        break;

    case 'ping':
        // Update current user's activity
        $session_id = session_id();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);

        db()->query(
            "INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, last_activity)
             VALUES (?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE last_activity = NOW()",
            [$_SESSION['user_id'], $session_id, $ip, $ua]
        );

        echo json_encode(['success' => true, 'message' => 'Activity updated']);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
