<?php

/**
 * Chat Search API
 * Search messages in conversations
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
$query = trim($_GET['q'] ?? '');
$conversation_id = intval($_GET['conversation_id'] ?? 0);

if (empty($query)) {
    echo json_encode(['success' => false, 'error' => 'Search query required']);
    exit;
}

try {
    $where = "cm.is_deleted = 0 AND cp.user_id = ?";
    $params = [$user_id];

    if ($conversation_id > 0) {
        $where .= " AND cm.conversation_id = ?";
        $params[] = $conversation_id;
    }

    $where .= " AND (cm.content LIKE ? OR cm.content LIKE ?)";
    $search_term = "%{$query}%";
    $params[] = $search_term;
    $params[] = $search_term;

    $messages = db()->fetchAll("
        SELECT 
            cm.*,
            u.first_name, u.last_name, u.profile_picture,
            c.name as conversation_name,
            c.conversation_type
        FROM chat_messages cm
        JOIN chat_participants cp ON cm.conversation_id = cp.conversation_id
        JOIN users u ON cm.sender_id = u.id
        JOIN chat_conversations c ON cm.conversation_id = c.id
        WHERE $where
        ORDER BY cm.created_at DESC
        LIMIT 50
    ", $params);

    echo json_encode([
        'success' => true,
        'messages' => $messages,
        'query' => $query
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to search: ' . $e->getMessage()
    ]);
}


