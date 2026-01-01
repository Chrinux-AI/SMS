<?php

/**
 * Chat Messages API
 * Get and send messages
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
$action = $_GET['action'] ?? $_POST['action'] ?? 'get';

switch ($action) {
    case 'get':
        getMessages($user_id);
        break;
    case 'send':
        sendMessage($user_id);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

function getMessages($user_id) {
    $conversation_id = intval($_GET['conversation_id'] ?? 0);
    $limit = intval($_GET['limit'] ?? 50);
    $before_id = intval($_GET['before_id'] ?? 0);

    if (!$conversation_id) {
        echo json_encode(['success' => false, 'error' => 'Conversation ID required']);
        exit;
    }

    // Verify user is participant
    $is_participant = db()->fetchOne("
        SELECT COUNT(*) as count FROM chat_participants
        WHERE conversation_id = ? AND user_id = ?
    ", [$conversation_id, $user_id]);

    if (!$is_participant || $is_participant['count'] == 0) {
        echo json_encode(['success' => false, 'error' => 'Access denied']);
        exit;
    }

    try {
        $where = "conversation_id = ? AND is_deleted = 0";
        $params = [$conversation_id];

        if ($before_id > 0) {
            $where .= " AND id < ?";
            $params[] = $before_id;
        }

        $messages = db()->fetchAll("
            SELECT 
                cm.*,
                u.first_name, u.last_name, u.profile_picture,
                (SELECT COUNT(*) FROM chat_read_receipts crr 
                 WHERE crr.message_id = cm.id) as read_count,
                (SELECT COUNT(*) FROM chat_read_receipts crr 
                 WHERE crr.message_id = cm.id AND crr.user_id = ?) as is_read_by_me,
                (SELECT GROUP_CONCAT(reaction) FROM chat_message_reactions cmr 
                 WHERE cmr.message_id = cm.id) as reactions,
                vn.audio_url as voice_note_url, vn.duration as voice_note_duration,
                vn.waveform_data as voice_note_waveform
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.id
            LEFT JOIN chat_voice_notes vn ON cm.id = vn.message_id
            WHERE $where
            ORDER BY cm.created_at DESC
            LIMIT ?
        ", array_merge($params, [$user_id, $limit]));

        // Reverse to show oldest first
        $messages = array_reverse($messages);

        // Get reply info if exists
        foreach ($messages as &$msg) {
            if ($msg['reply_to_id']) {
                $reply_msg = db()->fetchOne("
                    SELECT cm.content, cm.message_type, u.first_name, u.last_name
                    FROM chat_messages cm
                    JOIN users u ON cm.sender_id = u.id
                    WHERE cm.id = ?
                ", [$msg['reply_to_id']]);
                $msg['reply_to'] = $reply_msg;
            }
        }

        echo json_encode([
            'success' => true,
            'messages' => $messages,
            'has_more' => count($messages) === $limit
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to fetch messages: ' . $e->getMessage()
        ]);
    }
}

function sendMessage($user_id) {
    $input = json_decode(file_get_contents('php://input'), true);
    $conversation_id = intval($input['conversation_id'] ?? 0);
    $content = trim($input['content'] ?? '');
    $message_type = $input['message_type'] ?? 'text';
    $reply_to_id = isset($input['reply_to_id']) ? intval($input['reply_to_id']) : null;
    $media_url = $input['media_url'] ?? null;
    $media_thumbnail = $input['media_thumbnail'] ?? null;
    $media_size = isset($input['media_size']) ? intval($input['media_size']) : null;
    $media_duration = isset($input['media_duration']) ? intval($input['media_duration']) : null;

    if (!$conversation_id) {
        echo json_encode(['success' => false, 'error' => 'Conversation ID required']);
        exit;
    }

    if (empty($content) && $message_type === 'text') {
        echo json_encode(['success' => false, 'error' => 'Message content required']);
        exit;
    }

    // Verify user is participant
    $is_participant = db()->fetchOne("
        SELECT COUNT(*) as count FROM chat_participants
        WHERE conversation_id = ? AND user_id = ?
    ", [$conversation_id, $user_id]);

    if (!$is_participant || $is_participant['count'] == 0) {
        echo json_encode(['success' => false, 'error' => 'Access denied']);
        exit;
    }

    try {
        db()->beginTransaction();

        // Insert message
        $message_id = db()->insert('chat_messages', [
            'conversation_id' => $conversation_id,
            'sender_id' => $user_id,
            'reply_to_id' => $reply_to_id,
            'message_type' => $message_type,
            'content' => $content,
            'media_url' => $media_url,
            'media_thumbnail' => $media_thumbnail,
            'media_size' => $media_size,
            'media_duration' => $media_duration
        ]);

        // Update conversation last_message_at
        db()->update('chat_conversations', [
            'last_message_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$conversation_id]);

        // Create read receipt for sender
        db()->insert('chat_read_receipts', [
            'message_id' => $message_id,
            'user_id' => $user_id
        ]);

        // If voice note, save voice note data
        if ($message_type === 'voice_note' && isset($input['voice_note'])) {
            $voice_data = $input['voice_note'];
            db()->insert('chat_voice_notes', [
                'message_id' => $message_id,
                'audio_url' => $voice_data['audio_url'],
                'duration' => $voice_data['duration'],
                'waveform_data' => json_encode($voice_data['waveform'] ?? [])
            ]);
        }

        db()->commit();

        // Get full message with sender info
        $message = db()->fetchOne("
            SELECT 
                cm.*,
                u.first_name, u.last_name, u.profile_picture
            FROM chat_messages cm
            JOIN users u ON cm.sender_id = u.id
            WHERE cm.id = ?
        ", [$message_id]);

        echo json_encode([
            'success' => true,
            'message' => $message
        ]);
    } catch (Exception $e) {
        db()->rollBack();
        echo json_encode([
            'success' => false,
            'error' => 'Failed to send message: ' . $e->getMessage()
        ]);
    }
}


