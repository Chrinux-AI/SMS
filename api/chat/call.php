<?php

/**
 * Chat Call API
 * Handle voice and video calls
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
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'initiate':
        initiateCall($user_id);
        break;
    case 'answer':
        answerCall($user_id);
        break;
    case 'reject':
        rejectCall($user_id);
        break;
    case 'end':
        endCall($user_id);
        break;
    case 'history':
        getCallHistory($user_id);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

function initiateCall($user_id) {
    $input = json_decode(file_get_contents('php://input'), true);
    $conversation_id = intval($input['conversation_id'] ?? 0);
    $call_type = $input['call_type'] ?? 'voice'; // voice or video

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
        $call_id = db()->insert('chat_calls', [
            'conversation_id' => $conversation_id,
            'caller_id' => $user_id,
            'call_type' => $call_type,
            'status' => 'initiated',
            'started_at' => date('Y-m-d H:i:s')
        ]);

        // Add caller as participant
        db()->insert('chat_call_participants', [
            'call_id' => $call_id,
            'user_id' => $user_id,
            'joined_at' => date('Y-m-d H:i:s')
        ]);

        // Get other participants
        $participants = db()->fetchAll("
            SELECT user_id FROM chat_participants
            WHERE conversation_id = ? AND user_id != ?
        ", [$conversation_id, $user_id]);

        echo json_encode([
            'success' => true,
            'call_id' => $call_id,
            'participants' => array_column($participants, 'user_id')
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to initiate call: ' . $e->getMessage()
        ]);
    }
}

function answerCall($user_id) {
    $input = json_decode(file_get_contents('php://input'), true);
    $call_id = intval($input['call_id'] ?? 0);

    if (!$call_id) {
        echo json_encode(['success' => false, 'error' => 'Call ID required']);
        exit;
    }

    try {
        db()->beginTransaction();

        // Update call status
        db()->update('chat_calls', [
            'status' => 'answered'
        ], 'id = ?', [$call_id]);

        // Add user as participant
        db()->insert('chat_call_participants', [
            'call_id' => $call_id,
            'user_id' => $user_id,
            'joined_at' => date('Y-m-d H:i:s')
        ]);

        db()->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        db()->rollBack();
        echo json_encode([
            'success' => false,
            'error' => 'Failed to answer call: ' . $e->getMessage()
        ]);
    }
}

function rejectCall($user_id) {
    $input = json_decode(file_get_contents('php://input'), true);
    $call_id = intval($input['call_id'] ?? 0);

    if (!$call_id) {
        echo json_encode(['success' => false, 'error' => 'Call ID required']);
        exit;
    }

    try {
        db()->update('chat_calls', [
            'status' => 'rejected',
            'ended_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$call_id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to reject call: ' . $e->getMessage()
        ]);
    }
}

function endCall($user_id) {
    $input = json_decode(file_get_contents('php://input'), true);
    $call_id = intval($input['call_id'] ?? 0);
    $duration = intval($input['duration'] ?? 0);

    if (!$call_id) {
        echo json_encode(['success' => false, 'error' => 'Call ID required']);
        exit;
    }

    try {
        db()->beginTransaction();

        // Update call
        $call = db()->fetchOne("SELECT started_at FROM chat_calls WHERE id = ?", [$call_id]);
        $started = strtotime($call['started_at']);
        $ended = time();
        $actual_duration = $ended - $started;

        db()->update('chat_calls', [
            'status' => 'ended',
            'ended_at' => date('Y-m-d H:i:s'),
            'duration' => $duration > 0 ? $duration : $actual_duration
        ], 'id = ?', [$call_id]);

        // Update participant left_at
        db()->update('chat_call_participants', [
            'left_at' => date('Y-m-d H:i:s')
        ], 'call_id = ? AND user_id = ?', [$call_id, $user_id]);

        db()->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        db()->rollBack();
        echo json_encode([
            'success' => false,
            'error' => 'Failed to end call: ' . $e->getMessage()
        ]);
    }
}

function getCallHistory($user_id) {
    $conversation_id = intval($_GET['conversation_id'] ?? 0);
    $limit = intval($_GET['limit'] ?? 20);

    try {
        $where = "cp.user_id = ?";
        $params = [$user_id];

        if ($conversation_id > 0) {
            $where .= " AND cc.conversation_id = ?";
            $params[] = $conversation_id;
        }

        $calls = db()->fetchAll("
            SELECT 
                cc.*,
                u.first_name, u.last_name, u.profile_picture
            FROM chat_calls cc
            JOIN chat_call_participants cp ON cc.id = cp.call_id
            JOIN users u ON cc.caller_id = u.id
            WHERE $where
            ORDER BY cc.created_at DESC
            LIMIT ?
        ", array_merge($params, [$limit]));

        echo json_encode([
            'success' => true,
            'calls' => $calls
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to fetch call history: ' . $e->getMessage()
        ]);
    }
}


