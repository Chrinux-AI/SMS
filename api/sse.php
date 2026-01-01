<?php

/**
 * Server-Sent Events (SSE) for Real-time Updates
 * Fallback for WebSocket - provides real-time message delivery
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Set headers for SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Disable nginx buffering

if (!isset($_SESSION['user_id'])) {
    echo "data: " . json_encode(['error' => 'Unauthorized']) . "\n\n";
    flush();
    exit;
}

$user_id = $_SESSION['user_id'];
$conversation_id = intval($_GET['conversation_id'] ?? 0);

// Prevent timeout
set_time_limit(0);
ignore_user_abort(false);

$last_message_id = 0;
$last_typing_check = 0;

// Send initial connection message
echo "data: " . json_encode(['type' => 'connected', 'timestamp' => time()]) . "\n\n";
flush();

// Keep connection alive and send updates
while (true) {
    // Check for new messages
    if ($conversation_id > 0) {
        try {
            $new_messages = db()->fetchAll("
                SELECT cm.*, u.first_name, u.last_name, u.profile_picture
                FROM chat_messages cm
                JOIN users u ON cm.sender_id = u.id
                WHERE cm.conversation_id = ? AND cm.id > ? AND cm.is_deleted = 0
                ORDER BY cm.created_at ASC
                LIMIT 10
            ", [$conversation_id, $last_message_id]);

            foreach ($new_messages as $msg) {
                echo "data: " . json_encode([
                    'type' => 'new_message',
                    'message' => $msg
                ]) . "\n\n";
                flush();
                $last_message_id = $msg['id'];
            }
        } catch (Exception $e) {
            // Silent fail
        }

        // Check for typing indicators (every 2 seconds)
        if (time() - $last_typing_check >= 2) {
            try {
                $typing_users = db()->fetchAll("
                    SELECT u.id, u.first_name, u.last_name
                    FROM chat_typing_indicators cti
                    JOIN users u ON cti.user_id = u.id
                    WHERE cti.conversation_id = ? 
                    AND cti.user_id != ?
                    AND cti.is_typing = 1
                    AND cti.updated_at > DATE_SUB(NOW(), INTERVAL 5 SECOND)
                ", [$conversation_id, $user_id]);

                if (!empty($typing_users)) {
                    echo "data: " . json_encode([
                        'type' => 'typing',
                        'users' => $typing_users
                    ]) . "\n\n";
                    flush();
                } else {
                    echo "data: " . json_encode([
                        'type' => 'typing_stopped'
                    ]) . "\n\n";
                    flush();
                }
            } catch (Exception $e) {
                // Silent fail
            }
            $last_typing_check = time();
        }
    }

    // Check for new conversations
    try {
        $new_conversations = db()->fetchAll("
            SELECT c.id, c.last_message_at
            FROM chat_conversations c
            JOIN chat_participants cp ON c.id = cp.conversation_id
            WHERE cp.user_id = ? 
            AND c.last_message_at > DATE_SUB(NOW(), INTERVAL 5 SECOND)
            AND c.id != ?
        ", [$user_id, $conversation_id]);

        if (!empty($new_conversations)) {
            echo "data: " . json_encode([
                'type' => 'new_conversation',
                'conversations' => $new_conversations
            ]) . "\n\n";
            flush();
        }
    } catch (Exception $e) {
        // Silent fail
    }

    // Check for read receipts
    try {
        $read_receipts = db()->fetchAll("
            SELECT crr.message_id, crr.user_id, u.first_name, u.last_name
            FROM chat_read_receipts crr
            JOIN users u ON crr.user_id = u.id
            JOIN chat_messages cm ON crr.message_id = cm.id
            WHERE cm.conversation_id = ?
            AND crr.user_id != ?
            AND crr.read_at > DATE_SUB(NOW(), INTERVAL 5 SECOND)
        ", [$conversation_id, $user_id]);

        if (!empty($read_receipts)) {
            echo "data: " . json_encode([
                'type' => 'read_receipt',
                'receipts' => $read_receipts
            ]) . "\n\n";
            flush();
        }
    } catch (Exception $e) {
        // Silent fail
    }

    // Check for online status changes
    try {
        $presence_changes = db()->fetchAll("
            SELECT cup.user_id, cup.is_online, cup.status, cup.last_seen
            FROM chat_user_presence cup
            JOIN chat_participants cp ON cup.user_id = cp.user_id
            WHERE cp.conversation_id = ?
            AND cup.user_id != ?
            AND cup.updated_at > DATE_SUB(NOW(), INTERVAL 5 SECOND)
        ", [$conversation_id, $user_id]);

        if (!empty($presence_changes)) {
            echo "data: " . json_encode([
                'type' => 'presence_update',
                'presence' => $presence_changes
            ]) . "\n\n";
            flush();
        }
    } catch (Exception $e) {
        // Silent fail
    }

    // Send heartbeat every 30 seconds
    if (time() % 30 === 0) {
        echo "data: " . json_encode(['type' => 'heartbeat', 'timestamp' => time()]) . "\n\n";
        flush();
    }

    // Sleep for 1 second before next check
    sleep(1);

    // Check if client disconnected
    if (connection_aborted()) {
        break;
    }
}


