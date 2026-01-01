<?php

/**
 * Chat Conversations API
 * List and create conversations
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
$user_role = $_SESSION['role'];
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

switch ($action) {
    case 'list':
        getConversations($user_id);
        break;
    case 'create':
        createConversation($user_id, $user_role);
        break;
    case 'get':
        getConversation($user_id);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

function getConversations($user_id) {
    try {
        $conversations = db()->fetchAll("
            SELECT 
                c.id,
                c.conversation_type,
                c.name,
                c.avatar_url,
                c.last_message_at,
                c.created_at,
                cp.is_pinned,
                cp.is_archived,
                cp.last_read_at,
                (SELECT COUNT(*) FROM chat_messages cm 
                 WHERE cm.conversation_id = c.id 
                 AND cm.created_at > cp.last_read_at 
                 AND cm.sender_id != ?) as unread_count,
                (SELECT cm.content FROM chat_messages cm 
                 WHERE cm.conversation_id = c.id 
                 ORDER BY cm.created_at DESC LIMIT 1) as last_message,
                (SELECT cm.message_type FROM chat_messages cm 
                 WHERE cm.conversation_id = c.id 
                 ORDER BY cm.created_at DESC LIMIT 1) as last_message_type,
                (SELECT cm.sender_id FROM chat_messages cm 
                 WHERE cm.conversation_id = c.id 
                 ORDER BY cm.created_at DESC LIMIT 1) as last_message_sender_id
            FROM chat_conversations c
            JOIN chat_participants cp ON c.id = cp.conversation_id
            WHERE cp.user_id = ? AND cp.is_archived = 0
            ORDER BY cp.is_pinned DESC, c.last_message_at DESC
            LIMIT 100
        ", [$user_id, $user_id]);

        // Get other participant info for direct conversations
        foreach ($conversations as &$conv) {
            if ($conv['conversation_type'] === 'direct') {
                $other_participant = db()->fetchOne("
                    SELECT u.id, u.first_name, u.last_name, u.email, u.profile_picture, u.role,
                           cp.is_online, cp.last_seen
                    FROM chat_participants cp
                    JOIN users u ON cp.user_id = u.id
                    LEFT JOIN chat_user_presence cp ON u.id = cp.user_id
                    WHERE cp.conversation_id = ? AND cp.user_id != ?
                ", [$conv['id'], $user_id]);

                if ($other_participant) {
                    $conv['other_user'] = $other_participant;
                }
            }

            // Get participant count for groups
            if ($conv['conversation_type'] === 'group') {
                $conv['participant_count'] = db()->count('chat_participants', 'conversation_id = ?', [$conv['id']]);
            }
        }

        echo json_encode([
            'success' => true,
            'conversations' => $conversations
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to fetch conversations: ' . $e->getMessage()
        ]);
    }
}

function createConversation($user_id, $user_role) {
    $input = json_decode(file_get_contents('php://input'), true);
    $type = $input['type'] ?? 'direct';
    $participant_ids = $input['participants'] ?? [];
    $name = $input['name'] ?? null;
    $description = $input['description'] ?? null;

    if ($type === 'direct' && count($participant_ids) !== 1) {
        echo json_encode(['success' => false, 'error' => 'Direct conversation requires exactly one participant']);
        exit;
    }

    if ($type === 'group' && empty($name)) {
        echo json_encode(['success' => false, 'error' => 'Group conversation requires a name']);
        exit;
    }

    try {
        db()->beginTransaction();

        // Create conversation
        $conversation_id = db()->insert('chat_conversations', [
            'conversation_type' => $type,
            'name' => $name,
            'description' => $description,
            'created_by' => $user_id
        ]);

        // Add creator as participant
        db()->insert('chat_participants', [
            'conversation_id' => $conversation_id,
            'user_id' => $user_id,
            'role' => 'admin'
        ]);

        // Add other participants
        foreach ($participant_ids as $participant_id) {
            if ($participant_id != $user_id) {
                db()->insert('chat_participants', [
                    'conversation_id' => $conversation_id,
                    'user_id' => $participant_id,
                    'role' => 'member'
                ]);
            }
        }

        db()->commit();

        echo json_encode([
            'success' => true,
            'conversation_id' => $conversation_id
        ]);
    } catch (Exception $e) {
        db()->rollBack();
        echo json_encode([
            'success' => false,
            'error' => 'Failed to create conversation: ' . $e->getMessage()
        ]);
    }
}

function getConversation($user_id) {
    $conversation_id = intval($_GET['id'] ?? 0);

    if (!$conversation_id) {
        echo json_encode(['success' => false, 'error' => 'Conversation ID required']);
        exit;
    }

    try {
        // Verify user is participant
        $is_participant = db()->fetchOne("
            SELECT COUNT(*) as count FROM chat_participants
            WHERE conversation_id = ? AND user_id = ?
        ", [$conversation_id, $user_id]);

        if (!$is_participant || $is_participant['count'] == 0) {
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            exit;
        }

        $conversation = db()->fetchOne("
            SELECT * FROM chat_conversations WHERE id = ?
        ", [$conversation_id]);

        // Get participants
        $participants = db()->fetchAll("
            SELECT 
                cp.*,
                u.first_name, u.last_name, u.email, u.profile_picture, u.role,
                cup.is_online, cup.last_seen, cup.status
            FROM chat_participants cp
            JOIN users u ON cp.user_id = u.id
            LEFT JOIN chat_user_presence cup ON u.id = cup.user_id
            WHERE cp.conversation_id = ?
        ", [$conversation_id]);

        $conversation['participants'] = $participants;

        echo json_encode([
            'success' => true,
            'conversation' => $conversation
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to fetch conversation: ' . $e->getMessage()
        ]);
    }
}


