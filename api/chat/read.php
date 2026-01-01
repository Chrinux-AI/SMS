<?php

/**
 * Read Receipts API
 * Mark messages as read
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
$message_ids = $input['message_ids'] ?? [];
$conversation_id = intval($input['conversation_id'] ?? 0);

if (empty($message_ids) && !$conversation_id) {
    echo json_encode(['success' => false, 'error' => 'Message IDs or conversation ID required']);
    exit;
}

try {
    db()->beginTransaction();

    if ($conversation_id) {
        // Mark all messages in conversation as read
        $messages = db()->fetchAll("
            SELECT id FROM chat_messages
            WHERE conversation_id = ? AND sender_id != ?
            AND id NOT IN (SELECT message_id FROM chat_read_receipts WHERE user_id = ?)
        ", [$conversation_id, $user_id, $user_id]);

        foreach ($messages as $msg) {
            db()->insert('chat_read_receipts', [
                'message_id' => $msg['id'],
                'user_id' => $user_id
            ]);
        }

        // Update participant last_read_at
        db()->update('chat_participants', [
            'last_read_at' => date('Y-m-d H:i:s')
        ], 'conversation_id = ? AND user_id = ?', [$conversation_id, $user_id]);
    } else {
        // Mark specific messages as read
        foreach ($message_ids as $message_id) {
            $message_id = intval($message_id);
            if ($message_id > 0) {
                // Check if already read
                $exists = db()->fetchOne("
                    SELECT id FROM chat_read_receipts
                    WHERE message_id = ? AND user_id = ?
                ", [$message_id, $user_id]);

                if (!$exists) {
                    db()->insert('chat_read_receipts', [
                        'message_id' => $message_id,
                        'user_id' => $user_id
                    ]);
                }
            }
        }
    }

    db()->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    db()->rollBack();
    echo json_encode([
        'success' => false,
        'error' => 'Failed to mark as read: ' . $e->getMessage()
    ]);
}


