<?php

/**
 * Typing Indicator API
 * Update typing status
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
$input = json_decode(file_get_contents('php://input'), true);
$conversation_id = intval($input['conversation_id'] ?? 0);
$is_typing = isset($input['is_typing']) ? (bool)$input['is_typing'] : true;

if (!$conversation_id) {
    echo json_encode(['success' => false, 'error' => 'Conversation ID required']);
    exit;
}

try {
    // Upsert typing indicator
    $existing = db()->fetchOne("
        SELECT id FROM chat_typing_indicators
        WHERE conversation_id = ? AND user_id = ?
    ", [$conversation_id, $user_id]);

    if ($existing) {
        db()->update('chat_typing_indicators', [
            'is_typing' => $is_typing,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$existing['id']]);
    } else {
        db()->insert('chat_typing_indicators', [
            'conversation_id' => $conversation_id,
            'user_id' => $user_id,
            'is_typing' => $is_typing
        ]);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update typing status: ' . $e->getMessage()
    ]);
}


