<?php

/**
 * Message Reactions API
 * Add/remove emoji reactions
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
$message_id = intval($input['message_id'] ?? 0);
$reaction = trim($input['reaction'] ?? '');

if (!$message_id || empty($reaction)) {
    echo json_encode(['success' => false, 'error' => 'Message ID and reaction required']);
    exit;
}

$action = $input['action'] ?? 'add'; // add or remove

try {
    if ($action === 'add') {
        // Check if reaction already exists
        $existing = db()->fetchOne("
            SELECT id FROM chat_message_reactions
            WHERE message_id = ? AND user_id = ? AND reaction = ?
        ", [$message_id, $user_id, $reaction]);

        if (!$existing) {
            db()->insert('chat_message_reactions', [
                'message_id' => $message_id,
                'user_id' => $user_id,
                'reaction' => $reaction
            ]);
        }
    } else {
        // Remove reaction
        db()->delete('chat_message_reactions', 'message_id = ? AND user_id = ? AND reaction = ?', [
            $message_id, $user_id, $reaction
        ]);
    }

    // Get all reactions for this message
    $reactions = db()->fetchAll("
        SELECT reaction, COUNT(*) as count,
               GROUP_CONCAT(DISTINCT u.first_name) as users
        FROM chat_message_reactions cmr
        JOIN users u ON cmr.user_id = u.id
        WHERE cmr.message_id = ?
        GROUP BY reaction
    ", [$message_id]);

    echo json_encode([
        'success' => true,
        'reactions' => $reactions
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update reaction: ' . $e->getMessage()
    ]);
}


