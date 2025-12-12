<?php

/**
 * Messaging API - Send, Receive, Read Messages
 * Supports: Direct messages, Broadcast messages, Role-based messaging
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

header('Content-Type: application/json');

// CSRF protection for state-changing requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'send':
        $receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : null;
        $recipient_role = $_POST['recipient_role'] ?? null;
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($subject) || empty($message)) {
            echo json_encode(['success' => false, 'error' => 'Subject and message required']);
            exit;
        }

        // Validate broadcast permission (only admin can broadcast)
        if ($recipient_role && $recipient_role !== 'direct' && $user_role !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Only admins can send broadcast messages']);
            exit;
        }

        try {
            // Insert message
            $message_id = db()->insert('messages', [
                'sender_id' => $user_id,
                'receiver_id' => $receiver_id,
                'recipient_role' => $recipient_role,
                'subject' => $subject,
                'message' => $message,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if (!$message_id) {
                echo json_encode(['success' => false, 'error' => 'Failed to insert message']);
                exit;
            }

            // If broadcast or role-based, create recipients
            if ($recipient_role && $recipient_role !== 'direct') {
                $where_clause = '';
                $params = [];

                if ($recipient_role === 'all') {
                    $where_clause = 'id != ? AND status = ? AND approved = ?';
                    $params = [$user_id, 'active', 1];
                } else {
                    $where_clause = 'role = ? AND id != ? AND status = ? AND approved = ?';
                    $params = [$recipient_role, $user_id, 'active', 1];
                }

                $recipients = db()->fetchAll("SELECT id FROM users WHERE $where_clause", $params);

                if (empty($recipients)) {
                    echo json_encode(['success' => false, 'error' => 'No recipients found for this role']);
                    exit;
                }

                foreach ($recipients as $recipient) {
                    // Insert recipient record
                    db()->insert('message_recipients', [
                        'message_id' => $message_id,
                        'recipient_id' => $recipient['id'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    // Create notification
                    try {
                        db()->insert('notifications', [
                            'user_id' => $recipient['id'],
                            'title' => 'New Message: ' . $subject,
                            'message' => substr($message, 0, 100) . (strlen($message) > 100 ? '...' : ''),
                            'type' => 'message',
                            'link' => '/attendance/messages.php?id=' . $message_id,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    } catch (Exception $e) {
                        error_log("Failed to create notification: " . $e->getMessage());
                    }
                }

                echo json_encode([
                    'success' => true,
                    'message_id' => $message_id,
                    'recipients_count' => count($recipients),
                    'message' => 'Broadcast message sent to ' . count($recipients) . ' users'
                ]);
            } else if ($receiver_id) {
                // Direct message - create recipient record
                db()->insert('message_recipients', [
                    'message_id' => $message_id,
                    'recipient_id' => $receiver_id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                // Create notification
                try {
                    db()->insert('notifications', [
                        'user_id' => $receiver_id,
                        'title' => 'New Message: ' . $subject,
                        'message' => substr($message, 0, 100) . (strlen($message) > 100 ? '...' : ''),
                        'type' => 'message',
                        'link' => '/attendance/messages.php?id=' . $message_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                } catch (Exception $e) {
                    error_log("Failed to create notification: " . $e->getMessage());
                }

                echo json_encode(['success' => true, 'message_id' => $message_id, 'message' => 'Direct message sent']);
            } else {
                echo json_encode(['success' => false, 'error' => 'No recipient specified']);
            }
        } catch (Exception $e) {
            error_log("Messaging error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    case 'inbox':
        $messages = db()->fetchAll("
            SELECT DISTINCT m.*, u.first_name, u.last_name, u.role as sender_role,
                   COALESCE(mr.is_read, m.is_read, 0) as is_read,
                   COALESCE(mr.read_at, m.read_at) as read_at
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            LEFT JOIN message_recipients mr ON m.id = mr.message_id AND mr.recipient_id = ?
            WHERE (mr.recipient_id = ? AND mr.deleted_at IS NULL)
               OR (m.receiver_id = ? AND m.recipient_role IS NULL)
            ORDER BY m.created_at DESC
            LIMIT 100
        ", [$user_id, $user_id, $user_id]);

        echo json_encode(['success' => true, 'messages' => $messages]);
        break;

    case 'sent':
        $messages = db()->fetchAll("
            SELECT m.*, u.first_name, u.last_name, u.role as receiver_role
            FROM messages m
            LEFT JOIN users u ON m.receiver_id = u.id
            WHERE m.sender_id = ?
            ORDER BY m.created_at DESC
            LIMIT 100
        ", [$user_id]);

        echo json_encode(['success' => true, 'messages' => $messages]);
        break;

    case 'read':
        $message_id = intval($_POST['message_id'] ?? 0);

        try {
            // Update read status in message_recipients table (for broadcast messages)
            db()->query(
                "UPDATE message_recipients
                 SET is_read = 1, read_at = ?
                 WHERE message_id = ? AND recipient_id = ?",
                [date('Y-m-d H:i:s'), $message_id, $user_id]
            );

            // Update read status in messages table (for direct messages)
            db()->query(
                "UPDATE messages
                 SET is_read = 1, read_at = ?
                 WHERE id = ? AND receiver_id = ?",
                [date('Y-m-d H:i:s'), $message_id, $user_id]
            );

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Read message error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to mark as read']);
        }
        break;

    case 'delete':
        $message_id = intval($_POST['message_id'] ?? 0);

        try {
            // Soft delete - mark as deleted in message_recipients
            db()->query(
                "UPDATE message_recipients
                 SET deleted_at = ?
                 WHERE message_id = ? AND recipient_id = ?",
                [date('Y-m-d H:i:s'), $message_id, $user_id]
            );

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Delete message error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to delete message']);
        }

        echo json_encode(['success' => true]);
        break;

    case 'users':
        // Get all users for messaging (excluding current user)
        $users = db()->fetchAll("
            SELECT id, username, CONCAT(first_name, ' ', last_name) as full_name, role, email
            FROM users
            WHERE id != ? AND status = 'active' AND approved = 1
            ORDER BY role, first_name
        ", [$user_id]);

        echo json_encode(['success' => true, 'users' => $users]);
        break;

    case 'unread_count':
        try {
            $result = db()->fetchOne("
                SELECT COUNT(*) as count
                FROM message_recipients
                WHERE recipient_id = ? AND is_read = 0 AND deleted_at IS NULL
            ", [$user_id]);

            $count = $result['count'] ?? 0;

            echo json_encode(['success' => true, 'count' => $count]);
        } catch (Exception $e) {
            error_log("Unread count error: " . $e->getMessage());
            echo json_encode(['success' => true, 'count' => 0]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
