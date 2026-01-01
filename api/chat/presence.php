<?php

/**
 * User Presence API
 * Update and get online status
 */

session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? 'update';

switch ($action) {
    case 'update':
        updatePresence($user_id);
        break;
    case 'get':
        getPresence($user_id);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

function updatePresence($user_id) {
    $input = json_decode(file_get_contents('php://input'), true);
    $is_online = isset($input['is_online']) ? (bool)$input['is_online'] : true;
    $status = $input['status'] ?? ($is_online ? 'online' : 'offline');

    try {
        $existing = db()->fetchOne("
            SELECT id FROM chat_user_presence WHERE user_id = ?
        ", [$user_id]);

        if ($existing) {
            db()->update('chat_user_presence', [
                'is_online' => $is_online,
                'status' => $status,
                'last_seen' => $is_online ? null : date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ], 'user_id = ?', [$user_id]);
        } else {
            db()->insert('chat_user_presence', [
                'user_id' => $user_id,
                'is_online' => $is_online,
                'status' => $status,
                'last_seen' => $is_online ? null : date('Y-m-d H:i:s')
            ]);
        }

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update presence: ' . $e->getMessage()
        ]);
    }
}

function getPresence($user_id) {
    $user_ids = $_GET['user_ids'] ?? [];
    
    if (empty($user_ids)) {
        echo json_encode(['success' => false, 'error' => 'User IDs required']);
        exit;
    }

    if (is_string($user_ids)) {
        $user_ids = explode(',', $user_ids);
    }

    $user_ids = array_map('intval', $user_ids);
    $placeholders = implode(',', array_fill(0, count($user_ids), '?'));

    try {
        $presence = db()->fetchAll("
            SELECT 
                user_id,
                is_online,
                status,
                last_seen
            FROM chat_user_presence
            WHERE user_id IN ($placeholders)
        ", $user_ids);

        echo json_encode([
            'success' => true,
            'presence' => $presence
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to get presence: ' . $e->getMessage()
        ]);
    }
}


