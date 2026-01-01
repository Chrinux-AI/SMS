<?php

/**
 * VERDANT SMS - WHATSAPP/TELEGRAM CLONE
 * Real-time Messaging System with Voice Notes, Video Calling, File Sharing
 *
 * Features:
 * - Real-time messaging (WebSocket)
 * - Voice notes with waveform
 * - Video calling (WebRTC)
 * - File sharing with previews
 * - Message reactions
 * - Read receipts
 * - Typing indicators
 * - Group chats
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class VerdantChatServer implements MessageComponentInterface
{
  protected $clients;
  protected $users;
  protected $rooms;

  public function __construct()
  {
    $this->clients = new \SplObjectStorage;
    $this->users = [];
    $this->rooms = [];

    echo "🚀 Verdant Chat Server Started\n";
  }

  public function onOpen(ConnectionInterface $conn)
  {
    $this->clients->attach($conn);
    echo "✅ New connection: {$conn->resourceId}\n";
  }

  public function onMessage(ConnectionInterface $from, $msg)
  {
    $data = json_decode($msg, true);

    if (!$data || !isset($data['type'])) {
      return;
    }

    switch ($data['type']) {
      case 'auth':
        $this->handleAuth($from, $data);
        break;

      case 'message':
        $this->handleMessage($from, $data);
        break;

      case 'voice_note':
        $this->handleVoiceNote($from, $data);
        break;

      case 'video_call':
        $this->handleVideoCall($from, $data);
        break;

      case 'file_upload':
        $this->handleFileUpload($from, $data);
        break;

      case 'typing':
        $this->handleTyping($from, $data);
        break;

      case 'read_receipt':
        $this->handleReadReceipt($from, $data);
        break;

      case 'reaction':
        $this->handleReaction($from, $data);
        break;

      case 'join_room':
        $this->handleJoinRoom($from, $data);
        break;

      case 'leave_room':
        $this->handleLeaveRoom($from, $data);
        break;
    }
  }

  public function onClose(ConnectionInterface $conn)
  {
    $this->clients->detach($conn);

    // Remove user from online list
    if (isset($this->users[$conn->resourceId])) {
      $userId = $this->users[$conn->resourceId]['user_id'];
      unset($this->users[$conn->resourceId]);

      // Notify others user went offline
      $this->broadcast([
        'type' => 'user_offline',
        'user_id' => $userId
      ]);
    }

    echo "❌ Connection closed: {$conn->resourceId}\n";
  }

  public function onError(ConnectionInterface $conn, \Exception $e)
  {
    echo "⚠️ Error: {$e->getMessage()}\n";
    $conn->close();
  }

  // Handler Methods

  private function handleAuth($conn, $data)
  {
    $userId = $data['user_id'] ?? null;
    $authToken = $data['auth_token'] ?? null;

    // Verify authentication
    if ($this->verifyAuth($userId, $authToken)) {
      $this->users[$conn->resourceId] = [
        'user_id' => $userId,
        'name' => $data['user_name'] ?? 'Unknown',
        'role' => $data['user_role'] ?? 'general',
        'conn' => $conn
      ];

      $conn->send(json_encode([
        'type' => 'auth_success',
        'message' => 'Authenticated successfully'
      ]));

      // Broadcast user online
      $this->broadcast([
        'type' => 'user_online',
        'user_id' => $userId,
        'name' => $data['user_name'] ?? 'Unknown'
      ]);

      echo "✅ User authenticated: {$userId}\n";
    } else {
      $conn->send(json_encode([
        'type' => 'auth_error',
        'message' => 'Authentication failed'
      ]));
      $conn->close();
    }
  }

  private function handleMessage($conn, $data)
  {
    $userId = $this->users[$conn->resourceId]['user_id'] ?? null;
    if (!$userId) return;

    $messageId = $this->saveMessage([
      'conversation_id' => $data['conversation_id'],
      'sender_id' => $userId,
      'content' => $data['content'],
      'type' => 'text'
    ]);

    $message = [
      'type' => 'new_message',
      'data' => [
        'id' => $messageId,
        'conversation_id' => $data['conversation_id'],
        'sender_id' => $userId,
        'sender_name' => $this->users[$conn->resourceId]['name'],
        'content' => $data['content'],
        'timestamp' => date('Y-m-d H:i:s'),
        'message_type' => 'text'
      ]
    ];

    $this->broadcastToConversation($data['conversation_id'], $message, $userId);
  }

  private function handleVoiceNote($conn, $data)
  {
    $userId = $this->users[$conn->resourceId]['user_id'] ?? null;
    if (!$userId) return;

    // Save voice note file
    $filePath = $this->saveVoiceNote($data['audio_data'], $userId);

    $messageId = $this->saveMessage([
      'conversation_id' => $data['conversation_id'],
      'sender_id' => $userId,
      'content' => $filePath,
      'type' => 'voice_note',
      'duration' => $data['duration'] ?? 0
    ]);

    $message = [
      'type' => 'voice_note',
      'data' => [
        'id' => $messageId,
        'conversation_id' => $data['conversation_id'],
        'sender_id' => $userId,
        'sender_name' => $this->users[$conn->resourceId]['name'],
        'file_url' => $filePath,
        'duration' => $data['duration'] ?? 0,
        'timestamp' => date('Y-m-d H:i:s')
      ]
    ];

    $this->broadcastToConversation($data['conversation_id'], $message, $userId);
  }

  private function handleVideoCall($conn, $data)
  {
    $userId = $this->users[$conn->resourceId]['user_id'] ?? null;
    if (!$userId) return;

    $action = $data['action'] ?? null;

    switch ($action) {
      case 'initiate':
        $callId = $this->createVideoCall($userId, $data['recipient_id']);
        $this->sendToUser($data['recipient_id'], [
          'type' => 'incoming_call',
          'data' => [
            'call_id' => $callId,
            'caller_id' => $userId,
            'caller_name' => $this->users[$conn->resourceId]['name']
          ]
        ]);
        break;

      case 'accept':
        $this->updateCallStatus($data['call_id'], 'active');
        $this->sendToUser($data['caller_id'], [
          'type' => 'call_accepted',
          'data' => ['call_id' => $data['call_id']]
        ]);
        break;

      case 'reject':
        $this->updateCallStatus($data['call_id'], 'rejected');
        $this->sendToUser($data['caller_id'], [
          'type' => 'call_rejected',
          'data' => ['call_id' => $data['call_id']]
        ]);
        break;

      case 'end':
        $this->endVideoCall($data['call_id']);
        break;

      case 'offer':
      case 'answer':
      case 'ice_candidate':
        // Forward WebRTC signaling
        $this->sendToUser($data['recipient_id'], [
          'type' => 'webrtc_signal',
          'data' => $data
        ]);
        break;
    }
  }

  private function handleFileUpload($conn, $data)
  {
    $userId = $this->users[$conn->resourceId]['user_id'] ?? null;
    if (!$userId) return;

    $filePath = $this->saveUploadedFile($data['file_data'], $data['file_name'], $userId);

    $messageId = $this->saveMessage([
      'conversation_id' => $data['conversation_id'],
      'sender_id' => $userId,
      'content' => $filePath,
      'type' => 'file',
      'file_name' => $data['file_name'],
      'file_size' => $data['file_size'] ?? 0,
      'mime_type' => $data['mime_type'] ?? 'application/octet-stream'
    ]);

    $message = [
      'type' => 'file_message',
      'data' => [
        'id' => $messageId,
        'conversation_id' => $data['conversation_id'],
        'sender_id' => $userId,
        'sender_name' => $this->users[$conn->resourceId]['name'],
        'file_url' => $filePath,
        'file_name' => $data['file_name'],
        'file_size' => $data['file_size'] ?? 0,
        'mime_type' => $data['mime_type'] ?? 'application/octet-stream',
        'timestamp' => date('Y-m-d H:i:s')
      ]
    ];

    $this->broadcastToConversation($data['conversation_id'], $message, $userId);
  }

  private function handleTyping($conn, $data)
  {
    $userId = $this->users[$conn->resourceId]['user_id'] ?? null;
    if (!$userId) return;

    $this->broadcastToConversation($data['conversation_id'], [
      'type' => 'user_typing',
      'data' => [
        'user_id' => $userId,
        'user_name' => $this->users[$conn->resourceId]['name'],
        'is_typing' => $data['is_typing'] ?? true
      ]
    ], $userId);
  }

  private function handleReadReceipt($conn, $data)
  {
    $userId = $this->users[$conn->resourceId]['user_id'] ?? null;
    if (!$userId) return;

    $this->markMessageRead($data['message_id'], $userId);

    $this->broadcastToConversation($data['conversation_id'], [
      'type' => 'message_read',
      'data' => [
        'message_id' => $data['message_id'],
        'user_id' => $userId,
        'read_at' => date('Y-m-d H:i:s')
      ]
    ]);
  }

  private function handleReaction($conn, $data)
  {
    $userId = $this->users[$conn->resourceId]['user_id'] ?? null;
    if (!$userId) return;

    $this->saveReaction($data['message_id'], $userId, $data['emoji']);

    $this->broadcastToConversation($data['conversation_id'], [
      'type' => 'message_reaction',
      'data' => [
        'message_id' => $data['message_id'],
        'user_id' => $userId,
        'emoji' => $data['emoji']
      ]
    ]);
  }

  private function handleJoinRoom($conn, $data)
  {
    $userId = $this->users[$conn->resourceId]['user_id'] ?? null;
    if (!$userId) return;

    $roomId = $data['room_id'];

    if (!isset($this->rooms[$roomId])) {
      $this->rooms[$roomId] = [];
    }

    $this->rooms[$roomId][$userId] = $conn;

    $this->broadcastToRoom($roomId, [
      'type' => 'user_joined',
      'data' => [
        'user_id' => $userId,
        'user_name' => $this->users[$conn->resourceId]['name']
      ]
    ], $userId);
  }

  private function handleLeaveRoom($conn, $data)
  {
    $userId = $this->users[$conn->resourceId]['user_id'] ?? null;
    if (!$userId) return;

    $roomId = $data['room_id'];

    if (isset($this->rooms[$roomId][$userId])) {
      unset($this->rooms[$roomId][$userId]);

      $this->broadcastToRoom($roomId, [
        'type' => 'user_left',
        'data' => ['user_id' => $userId]
      ], $userId);
    }
  }

  // Helper Methods

  private function broadcast($message, $excludeId = null)
  {
    $json = json_encode($message);
    foreach ($this->clients as $client) {
      if (
        $excludeId && isset($this->users[$client->resourceId]) &&
        $this->users[$client->resourceId]['user_id'] == $excludeId
      ) {
        continue;
      }
      $client->send($json);
    }
  }

  private function broadcastToConversation($conversationId, $message, $excludeUserId = null)
  {
    // Get all participants in conversation
    $participants = $this->getConversationParticipants($conversationId);
    $json = json_encode($message);

    foreach ($this->users as $resourceId => $user) {
      if (in_array($user['user_id'], $participants) && $user['user_id'] != $excludeUserId) {
        $user['conn']->send($json);
      }
    }
  }

  private function broadcastToRoom($roomId, $message, $excludeUserId = null)
  {
    if (!isset($this->rooms[$roomId])) return;

    $json = json_encode($message);
    foreach ($this->rooms[$roomId] as $userId => $conn) {
      if ($userId != $excludeUserId) {
        $conn->send($json);
      }
    }
  }

  private function sendToUser($userId, $message)
  {
    foreach ($this->users as $user) {
      if ($user['user_id'] == $userId) {
        $user['conn']->send(json_encode($message));
        break;
      }
    }
  }

  private function verifyAuth($userId, $authToken)
  {
    // Verify JWT token or session
    return true; // Implement proper auth verification
  }

  private function saveMessage($data)
  {
    try {
      db()->insert('chat_messages', [
        'conversation_id' => $data['conversation_id'],
        'sender_id' => $data['sender_id'],
        'content' => $data['content'],
        'message_type' => $data['type'],
        'created_at' => date('Y-m-d H:i:s')
      ]);
      return db()->lastInsertId();
    } catch (Exception $e) {
      error_log("Error saving message: " . $e->getMessage());
      return null;
    }
  }

  private function saveVoiceNote($audioData, $userId)
  {
    $uploadDir = __DIR__ . '/../uploads/voice_notes/';
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid('voice_') . '_' . $userId . '.webm';
    $filepath = $uploadDir . $filename;

    file_put_contents($filepath, base64_decode($audioData));

    return '/uploads/voice_notes/' . $filename;
  }

  private function saveUploadedFile($fileData, $fileName, $userId)
  {
    $uploadDir = __DIR__ . '/../uploads/chat_files/';
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid('file_') . '_' . $userId . '_' . $fileName;
    $filepath = $uploadDir . $filename;

    file_put_contents($filepath, base64_decode($fileData));

    return '/uploads/chat_files/' . $filename;
  }

  private function createVideoCall($callerId, $recipientId)
  {
    try {
      db()->insert('video_call_sessions', [
        'caller_id' => $callerId,
        'status' => 'ringing',
        'created_at' => date('Y-m-d H:i:s')
      ]);

      $callId = db()->lastInsertId();

      db()->insert('call_participants', [
        'call_id' => $callId,
        'user_id' => $recipientId,
        'joined_at' => null
      ]);

      return $callId;
    } catch (Exception $e) {
      error_log("Error creating video call: " . $e->getMessage());
      return null;
    }
  }

  private function updateCallStatus($callId, $status)
  {
    try {
      db()->update('video_call_sessions', [
        'status' => $status,
        'updated_at' => date('Y-m-d H:i:s')
      ], ['id' => $callId]);
    } catch (Exception $e) {
      error_log("Error updating call status: " . $e->getMessage());
    }
  }

  private function endVideoCall($callId)
  {
    try {
      db()->update('video_call_sessions', [
        'status' => 'ended',
        'ended_at' => date('Y-m-d H:i:s')
      ], ['id' => $callId]);
    } catch (Exception $e) {
      error_log("Error ending video call: " . $e->getMessage());
    }
  }

  private function markMessageRead($messageId, $userId)
  {
    try {
      db()->insert('message_read_receipts', [
        'message_id' => $messageId,
        'user_id' => $userId,
        'read_at' => date('Y-m-d H:i:s')
      ]);
    } catch (Exception $e) {
      error_log("Error marking message read: " . $e->getMessage());
    }
  }

  private function saveReaction($messageId, $userId, $emoji)
  {
    try {
      db()->insert('message_reactions', [
        'message_id' => $messageId,
        'user_id' => $userId,
        'emoji' => $emoji,
        'created_at' => date('Y-m-d H:i:s')
      ]);
    } catch (Exception $e) {
      error_log("Error saving reaction: " . $e->getMessage());
    }
  }

  private function getConversationParticipants($conversationId)
  {
    try {
      $result = db()->fetchAll(
        "SELECT user_id FROM conversation_participants WHERE conversation_id = ?",
        [$conversationId]
      );
      return array_column($result, 'user_id');
    } catch (Exception $e) {
      error_log("Error getting participants: " . $e->getMessage());
      return [];
    }
  }
}

// Start WebSocket Server
require __DIR__ . '/../vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
  new HttpServer(
    new WsServer(
      new VerdantChatServer()
    )
  ),
  8080
);

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║       VERDANT SMS - CHAT SERVER RUNNING ON PORT 8080       ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";

$server->run();
