<?php

/**
 * Voice Note Upload API
 * Handle voice note recording uploads
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$audio_data = $input['audio_data'] ?? null;
$duration = intval($input['duration'] ?? 0);
$waveform = $input['waveform'] ?? [];
$conversation_id = intval($input['conversation_id'] ?? 0);

if (!$audio_data) {
    echo json_encode(['success' => false, 'error' => 'Audio data required']);
    exit;
}

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
    // Decode base64 audio data
    $audio_data = str_replace('data:audio/webm;base64,', '', $audio_data);
    $audio_data = str_replace('data:audio/ogg;base64,', '', $audio_data);
    $audio_binary = base64_decode($audio_data);

    if (!$audio_binary) {
        throw new Exception('Invalid audio data');
    }

    // Create upload directory
    $upload_dir = BASE_PATH . '/uploads/chat/voice-notes/' . date('Y/m/');
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generate filename
    $filename = uniqid('voice_') . '_' . time() . '.webm';
    $file_path = $upload_dir . $filename;
    $relative_path = '/uploads/chat/voice-notes/' . date('Y/m/') . $filename;

    // Save audio file
    file_put_contents($file_path, $audio_binary);

    // Create message
    db()->beginTransaction();

    $message_id = db()->insert('chat_messages', [
        'conversation_id' => $conversation_id,
        'sender_id' => $user_id,
        'message_type' => 'voice_note',
        'content' => 'Voice note',
        'media_url' => APP_URL . $relative_path,
        'media_duration' => $duration
    ]);

    // Save voice note metadata
    db()->insert('chat_voice_notes', [
        'message_id' => $message_id,
        'audio_url' => APP_URL . $relative_path,
        'duration' => $duration,
        'waveform_data' => json_encode($waveform)
    ]);

    // Update conversation
    db()->update('chat_conversations', [
        'last_message_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [$conversation_id]);

    // Create read receipt for sender
    db()->insert('chat_read_receipts', [
        'message_id' => $message_id,
        'user_id' => $user_id
    ]);

    db()->commit();

    // Get full message
    $message = db()->fetchOne("
        SELECT 
            cm.*,
            u.first_name, u.last_name, u.profile_picture,
            vn.audio_url, vn.duration, vn.waveform_data
        FROM chat_messages cm
        JOIN users u ON cm.sender_id = u.id
        LEFT JOIN chat_voice_notes vn ON cm.id = vn.message_id
        WHERE cm.id = ?
    ", [$message_id]);

    echo json_encode([
        'success' => true,
        'message' => $message
    ]);
} catch (Exception $e) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    echo json_encode([
        'success' => false,
        'error' => 'Failed to upload voice note: ' . $e->getMessage()
    ]);
}


