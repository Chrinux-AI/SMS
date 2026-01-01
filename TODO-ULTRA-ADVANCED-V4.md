# 🚀 VERDANT SMS v4.0 — ULTRA-ADVANCED COMPREHENSIVE TODO

**Status:** 🔴 IMPLEMENTATION READY
**Last Updated:** 30 December 2025
**Broken Links Found:** 140/663 links (21% failure rate) — **CRITICAL**
**Missing Pages:** 50+ identified
**Existing Files:** Admin: 78 | Student: 32 | Teacher: 21

---

## 📊 PROJECT HEALTH ANALYSIS

### 🔴 CRITICAL ISSUES FOUND (Must Fix Immediately)

1. **140 BROKEN LINKS** (21% of all links)

   - Health module pages (new-visit.php, visits.php, medical-records.php, vaccinations.php, growth-charts.php)
   - Library pages (add-book.php, issue-book.php, overdue.php)
   - Transport, Hostel, HR, Inventory modules

2. **NO FAVICON/BRANDING ASSETS**

   - Only favicon.svg exists (705 bytes)
   - Missing: 16x16, 32x32, 192x192, 512x512 PNG versions
   - No apple-touch-icon
   - No PWA splash screens

3. **NO REAL-TIME MESSAGING**

   - chat.php exists but has TODOs (line 1148, 1154, 1160, 1165, 1170)
   - No WebSocket server
   - No voice notes, video calling, file previews

4. **UI INCONSISTENCY ISSUES**
   - overflow: hidden in multiple CSS files (causing scroll problems)
   - Sidebar implementations vary across files
   - No consistent error/loading states

---

## 🎯 COMPREHENSIVE FEATURE IMPLEMENTATION PLAN

### PHASE 1: EMERGENCY FIXES (Day 1 - 8 hours) 🔴

#### 1.1 CREATE ALL 140 MISSING PAGES

**Priority:** P1 🔴 CRITICAL
**Time:** 6 hours
**Automation:** Use generator script

```bash
# Create missing page generator
php scripts/generate-missing-pages.php
```

**Missing Page Categories:**

1. **Health Module** (15 pages)

   - admin/health/new-visit.php
   - admin/health/visits.php
   - admin/health/medical-records.php
   - admin/health/vaccinations.php
   - admin/health/growth-charts.php
   - admin/health/medications.php
   - admin/health/emergency-contacts.php
   - admin/health/allergies.php
   - admin/health/chronic-conditions.php
   - admin/health/dental-records.php
   - admin/health/vision-screening.php
   - admin/health/immunization-schedule.php
   - admin/health/sports-clearance.php
   - admin/health/health-reports.php
   - admin/health/nurse-schedule.php

2. **Library Module** (12 pages)

   - admin/library/add-book.php
   - admin/library/issue-book.php
   - admin/library/overdue.php
   - admin/library/reservations.php
   - admin/library/catalog-search.php
   - admin/library/barcode-scanner.php
   - admin/library/fine-collection.php
   - admin/library/lost-damaged.php
   - admin/library/reading-analytics.php
   - admin/library/book-recommendations.php
   - admin/library/digital-library.php
   - admin/library/library-settings.php

3. **Transport Module** (10 pages)

   - admin/transport/gps-tracking.php
   - admin/transport/route-optimization.php
   - admin/transport/driver-schedule.php
   - admin/transport/vehicle-maintenance.php
   - admin/transport/fuel-management.php
   - admin/transport/parent-notifications.php
   - admin/transport/emergency-contacts-transport.php
   - admin/transport/transport-fees.php
   - admin/transport/route-reports.php
   - admin/transport/live-map.php

4. **Hostel Module** (13 pages)

   - admin/hostel/room-allocation-wizard.php
   - admin/hostel/warden-dashboard.php
   - admin/hostel/visitor-log.php
   - admin/hostel/night-attendance.php
   - admin/hostel/complaint-system.php
   - admin/hostel/mess-menu-planner.php
   - admin/hostel/hostel-fees.php
   - admin/hostel/laundry-management.php
   - admin/hostel/inventory-hostel.php
   - admin/hostel/curfew-violations.php
   - admin/hostel/room-inspection.php
   - admin/hostel/maintenance-requests.php
   - admin/hostel/hostel-events.php

5. **Student Portal Pages** (18 pages)

   - student/virtual-id-card.php (with QR code)
   - student/digital-transcript.php
   - student/scholarship-portal.php
   - student/career-counseling.php
   - student/internship-opportunities.php
   - student/alumni-network.php
   - student/skill-development.php
   - student/extracurricular.php
   - student/sports-registration.php
   - student/clubs-societies.php
   - student/community-service.php
   - student/mental-health-support.php
   - student/grievance-redressal.php
   - student/feedback-system.php
   - student/course-evaluation.php
   - student/peer-tutoring.php
   - student/study-planner.php
   - student/goal-tracker.php

6. **Teacher Pages** (15 pages)

   - teacher/smart-grading.php (AI-assisted)
   - teacher/plagiarism-checker.php
   - teacher/video-lectures.php
   - teacher/virtual-classroom.php
   - teacher/whiteboard.php (interactive)
   - teacher/quiz-builder.php
   - teacher/attendance-analytics.php
   - teacher/parent-conference-scheduler.php
   - teacher/lesson-planner.php
   - teacher/rubric-builder.php
   - teacher/peer-observation.php
   - teacher/professional-development.php
   - teacher/resource-sharing.php
   - teacher/student-behavior-tracker.php
   - teacher/differentiated-instruction.php

7. **Parent Pages** (12 pages)
   - parent/real-time-location.php (student tracking)
   - parent/pickup-dropoff.php
   - parent/photo-gallery.php
   - parent/milestone-tracker.php
   - parent/health-dashboard.php
   - parent/academic-progress-detailed.php
   - parent/behavioral-insights.php
   - parent/extracurricular-enrollment.php
   - parent/payment-history-detailed.php
   - parent/parent-community.php
   - parent/volunteer-opportunities.php
   - parent/survey-participation.php

#### 1.2 FIX ALL BROKEN NAVIGATION LINKS

**Script to auto-fix paths:**

```php
<?php
// scripts/fix-navigation-paths.php

$nav_files = glob('includes/*-nav.php');

foreach ($nav_files as $file) {
    $content = file_get_contents($file);

    // Fix common path issues
    $fixes = [
        "'dashboard.php'" => "'./{$role}/dashboard.php'",
        "'../messages.php'" => "'../messages.php'", // Already correct
        // Add more fixes
    ];

    foreach ($fixes as $old => $new) {
        $content = str_replace($old, $new, $content);
    }

    file_put_contents($file, $content);
    echo "✅ Fixed: $file\n";
}
?>
```

#### 1.3 IMMEDIATE UI FIXES

**Fix overflow issues in ALL CSS files:**

```bash
# Find all overflow: hidden that breaks scrolling
grep -rn "overflow.*hidden" assets/css/ --include="*.css"

# Replace with proper scrolling
find assets/css/ -name "*.css" -exec sed -i 's/overflow: hidden;/overflow-y: auto;/g' {} \;
```

---

### PHASE 2: WHATSAPP/TELEGRAM CLONE (Days 2-5 - 32 hours) 🟠

#### 2.1 REAL-TIME MESSAGING SYSTEM

**Complete Implementation with ALL Features:**

1. **WebSocket Server (PHP Ratchet)**

```php
<?php
// server/websocket-chat-server.php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require __DIR__ . '/../vendor/autoload.php';

class ChatServer implements MessageComponentInterface {
    protected $clients;
    protected $users;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->users = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        switch ($data['type']) {
            case 'auth':
                $this->users[$from->resourceId] = [
                    'user_id' => $data['user_id'],
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'conn' => $from
                ];
                $this->broadcastOnlineUsers();
                break;

            case 'message':
                $this->handleMessage($from, $data);
                break;

            case 'typing':
                $this->handleTyping($from, $data);
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

            case 'read_receipt':
                $this->handleReadReceipt($from, $data);
                break;

            case 'reaction':
                $this->handleReaction($from, $data);
                break;
        }
    }

    private function handleMessage($from, $data) {
        // Save to database
        db()->insert('chat_messages', [
            'conversation_id' => $data['conversation_id'],
            'sender_id' => $data['sender_id'],
            'message' => $data['message'],
            'type' => $data['message_type'] ?? 'text',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Get conversation participants
        $participants = db()->fetchAll(
            "SELECT user_id FROM conversation_participants WHERE conversation_id = ?",
            [$data['conversation_id']]
        );

        // Send to all participants
        foreach ($this->users as $user) {
            foreach ($participants as $participant) {
                if ($user['user_id'] == $participant['user_id']) {
                    $user['conn']->send(json_encode([
                        'type' => 'new_message',
                        'data' => $data
                    ]));
                }
            }
        }
    }

    private function handleVoiceNote($from, $data) {
        // Process voice note (convert to MP3, save, generate waveform)
        $audio_path = $this->saveVoiceNote($data['audio_data']);
        $waveform = $this->generateWaveform($audio_path);

        // Save to database
        db()->insert('chat_messages', [
            'conversation_id' => $data['conversation_id'],
            'sender_id' => $data['sender_id'],
            'message' => null,
            'type' => 'voice',
            'file_url' => $audio_path,
            'metadata' => json_encode([
                'duration' => $data['duration'],
                'waveform' => $waveform
            ]),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Broadcast to conversation
        $this->broadcastToConversation($data['conversation_id'], [
            'type' => 'voice_note',
            'data' => [
                'message_id' => db()->lastInsertId(),
                'sender_id' => $data['sender_id'],
                'audio_url' => $audio_path,
                'duration' => $data['duration'],
                'waveform' => $waveform
            ]
        ]);
    }

    private function handleVideoCall($from, $data) {
        // WebRTC signaling for video calls
        $target_user = $this->getUserById($data['target_user_id']);

        if ($target_user) {
            $target_user['conn']->send(json_encode([
                'type' => 'incoming_call',
                'data' => [
                    'caller_id' => $data['caller_id'],
                    'caller_name' => $data['caller_name'],
                    'call_type' => $data['call_type'], // 'video' or 'voice'
                    'offer' => $data['offer'] // WebRTC offer
                ]
            ]));
        }
    }

    private function handleFileUpload($from, $data) {
        // Handle file uploads with previews
        $file_info = $this->processFile($data['file_data'], $data['file_name']);

        db()->insert('chat_messages', [
            'conversation_id' => $data['conversation_id'],
            'sender_id' => $data['sender_id'],
            'message' => $data['caption'] ?? null,
            'type' => 'file',
            'file_url' => $file_info['path'],
            'metadata' => json_encode([
                'filename' => $file_info['name'],
                'size' => $file_info['size'],
                'mime_type' => $file_info['mime'],
                'thumbnail' => $file_info['thumbnail'] ?? null
            ]),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->broadcastToConversation($data['conversation_id'], [
            'type' => 'file_message',
            'data' => [
                'message_id' => db()->lastInsertId(),
                'sender_id' => $data['sender_id'],
                'file_info' => $file_info
            ]
        ]);
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
        $this->broadcastOnlineUsers();
        echo "Connection closed: {$conn->resourceId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    private function broadcastOnlineUsers() {
        $online_users = array_map(function($user) {
            return [
                'user_id' => $user['user_id'],
                'name' => $user['name'],
                'role' => $user['role']
            ];
        }, $this->users);

        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'type' => 'online_users',
                'users' => array_values($online_users)
            ]));
        }
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    8080
);

echo "🚀 Chat WebSocket Server running on port 8080\n";
$server->run();
```

2. **Frontend Chat UI (WhatsApp Clone)**

```javascript
// assets/js/chat-client.js

class VerdantChat {
  constructor(userId, userName, userRole, authToken) {
    this.userId = userId;
    this.userName = userName;
    this.userRole = userRole;
    this.authToken = authToken;
    this.ws = null;
    this.currentConversation = null;
    this.conversations = [];
    this.mediaRecorder = null;
    this.peerConnection = null;

    this.init();
  }

  init() {
    this.connectWebSocket();
    this.setupUI();
    this.setupMediaRecorder();
    this.loadConversations();
  }

  connectWebSocket() {
    this.ws = new WebSocket("ws://localhost:8080");

    this.ws.onopen = () => {
      console.log("✅ Connected to chat server");
      this.authenticate();
    };

    this.ws.onmessage = (event) => {
      const data = JSON.parse(event.data);
      this.handleMessage(data);
    };

    this.ws.onclose = () => {
      console.log("❌ Disconnected from chat server");
      setTimeout(() => this.connectWebSocket(), 5000); // Reconnect
    };

    this.ws.onerror = (error) => {
      console.error("WebSocket error:", error);
    };
  }

  authenticate() {
    this.send({
      type: "auth",
      user_id: this.userId,
      name: this.userName,
      role: this.userRole,
      token: this.authToken,
    });
  }

  handleMessage(data) {
    switch (data.type) {
      case "new_message":
        this.displayNewMessage(data.data);
        this.playNotificationSound();
        break;

      case "voice_note":
        this.displayVoiceNote(data.data);
        break;

      case "incoming_call":
        this.showIncomingCallUI(data.data);
        break;

      case "file_message":
        this.displayFileMessage(data.data);
        break;

      case "typing":
        this.showTypingIndicator(data.data);
        break;

      case "read_receipt":
        this.updateReadStatus(data.data);
        break;

      case "online_users":
        this.updateOnlineUsers(data.users);
        break;

      case "reaction":
        this.displayReaction(data.data);
        break;
    }
  }

  sendMessage(text, type = "text") {
    if (!text.trim() && type === "text") return;

    const message = {
      type: "message",
      conversation_id: this.currentConversation.id,
      sender_id: this.userId,
      message: text,
      message_type: type,
      timestamp: Date.now(),
    };

    this.send(message);
    this.displayOwnMessage(message);
  }

  startVoiceRecording() {
    navigator.mediaDevices
      .getUserMedia({ audio: true })
      .then((stream) => {
        this.mediaRecorder = new MediaRecorder(stream);
        this.audioChunks = [];

        this.mediaRecorder.ondataavailable = (event) => {
          this.audioChunks.push(event.data);
        };

        this.mediaRecorder.onstop = () => {
          const audioBlob = new Blob(this.audioChunks, { type: "audio/webm" });
          this.sendVoiceNote(audioBlob);
        };

        this.mediaRecorder.start();
        this.showRecordingUI();
      })
      .catch((err) => {
        console.error("Error accessing microphone:", err);
        this.showError("Microphone access denied");
      });
  }

  stopVoiceRecording() {
    if (this.mediaRecorder && this.mediaRecorder.state === "recording") {
      this.mediaRecorder.stop();
      this.hideRecordingUI();
    }
  }

  sendVoiceNote(audioBlob) {
    const reader = new FileReader();
    reader.readAsDataURL(audioBlob);
    reader.onloadend = () => {
      const audioData = reader.result.split(",")[1]; // Remove data URL prefix

      this.send({
        type: "voice_note",
        conversation_id: this.currentConversation.id,
        sender_id: this.userId,
        audio_data: audioData,
        duration: this.getAudioDuration(audioBlob),
      });
    };
  }

  startVideoCall(targetUserId) {
    // Initialize WebRTC
    this.peerConnection = new RTCPeerConnection({
      iceServers: [
        { urls: "stun:stun.l.google.com:19302" },
        { urls: "stun:stun1.l.google.com:19302" },
      ],
    });

    // Get user media
    navigator.mediaDevices
      .getUserMedia({ video: true, audio: true })
      .then((stream) => {
        this.localStream = stream;
        document.getElementById("localVideo").srcObject = stream;

        stream.getTracks().forEach((track) => {
          this.peerConnection.addTrack(track, stream);
        });

        // Create offer
        return this.peerConnection.createOffer();
      })
      .then((offer) => {
        return this.peerConnection.setLocalDescription(offer);
      })
      .then(() => {
        // Send offer through WebSocket
        this.send({
          type: "video_call",
          caller_id: this.userId,
          caller_name: this.userName,
          target_user_id: targetUserId,
          call_type: "video",
          offer: this.peerConnection.localDescription,
        });

        this.showOutgoingCallUI();
      })
      .catch((err) => {
        console.error("Error starting video call:", err);
        this.showError("Failed to start video call");
      });
  }

  sendFile(file) {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onloadend = () => {
      const fileData = reader.result.split(",")[1];

      this.send({
        type: "file_upload",
        conversation_id: this.currentConversation.id,
        sender_id: this.userId,
        file_data: fileData,
        file_name: file.name,
        caption: null,
      });

      this.showFileSending(file.name);
    };
  }

  sendTypingIndicator() {
    this.send({
      type: "typing",
      conversation_id: this.currentConversation.id,
      sender_id: this.userId,
    });
  }

  sendReadReceipt(messageId) {
    this.send({
      type: "read_receipt",
      message_id: messageId,
      user_id: this.userId,
      timestamp: Date.now(),
    });
  }

  addReaction(messageId, emoji) {
    this.send({
      type: "reaction",
      message_id: messageId,
      user_id: this.userId,
      emoji: emoji,
    });
  }

  displayNewMessage(message) {
    const messageEl = this.createMessageElement(message);
    document.getElementById("chatMessages").appendChild(messageEl);
    this.scrollToBottom();

    // Send read receipt if conversation is open
    if (
      this.currentConversation &&
      this.currentConversation.id === message.conversation_id
    ) {
      this.sendReadReceipt(message.id);
    }
  }

  displayVoiceNote(data) {
    const voiceEl = document.createElement("div");
    voiceEl.className = "message voice-message";
    voiceEl.innerHTML = `
            <div class="voice-player">
                <button class="play-btn" onclick="chat.playVoiceNote('${
                  data.audio_url
                }')">
                    <i class="fas fa-play"></i>
                </button>
                <div class="waveform">
                    ${this.renderWaveform(data.waveform)}
                </div>
                <span class="duration">${this.formatDuration(
                  data.duration
                )}</span>
            </div>
        `;
    document.getElementById("chatMessages").appendChild(voiceEl);
    this.scrollToBottom();
  }

  showIncomingCallUI(callData) {
    const callModal = document.createElement("div");
    callModal.className = "call-modal";
    callModal.innerHTML = `
            <div class="call-content">
                <div class="caller-info">
                    <div class="caller-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3>${callData.caller_name}</h3>
                    <p>Incoming ${callData.call_type} call...</p>
                </div>
                <div class="call-actions">
                    <button class="btn-decline" onclick="chat.declineCall()">
                        <i class="fas fa-phone-slash"></i> Decline
                    </button>
                    <button class="btn-accept" onclick="chat.acceptCall('${
                      callData.caller_id
                    }', ${JSON.stringify(callData.offer)})">
                        <i class="fas fa-phone"></i> Accept
                    </button>
                </div>
            </div>
        `;
    document.body.appendChild(callModal);

    // Play ringtone
    this.playRingtone();
  }

  setupUI() {
    const chatHTML = `
            <div class="chat-container">
                <!-- Conversation List -->
                <div class="conversation-list">
                    <div class="conversation-header">
                        <h2><i class="fas fa-comments"></i> Chats</h2>
                        <button class="btn-new-chat" onclick="chat.startNewConversation()">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search conversations..."
                               oninput="chat.searchConversations(this.value)">
                    </div>
                    <div class="conversations" id="conversationsList">
                        <!-- Conversations loaded dynamically -->
                    </div>
                </div>

                <!-- Chat Window -->
                <div class="chat-window">
                    <div class="chat-header">
                        <div class="chat-user-info">
                            <div class="avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="user-details">
                                <h3 id="chatUserName">Select a conversation</h3>
                                <span class="status" id="chatUserStatus">offline</span>
                            </div>
                        </div>
                        <div class="chat-actions">
                            <button onclick="chat.startVoiceCall()" title="Voice Call">
                                <i class="fas fa-phone"></i>
                            </button>
                            <button onclick="chat.startVideoCall()" title="Video Call">
                                <i class="fas fa-video"></i>
                            </button>
                            <button onclick="chat.showChatInfo()" title="Info">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>

                    <div class="chat-messages" id="chatMessages">
                        <!-- Messages loaded dynamically -->
                    </div>

                    <div class="typing-indicator" id="typingIndicator" style="display: none;">
                        <span></span><span></span><span></span>
                    </div>

                    <div class="chat-input">
                        <button class="btn-emoji" onclick="chat.toggleEmojiPicker()">
                            <i class="far fa-smile"></i>
                        </button>
                        <button class="btn-attach" onclick="chat.showAttachMenu()">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <input type="file" id="fileInput" style="display: none;"
                               onchange="chat.handleFileSelect(event)">
                        <textarea id="messageInput" placeholder="Type a message..."
                                  oninput="chat.sendTypingIndicator()"
                                  onkeydown="if(event.key==='Enter' && !event.shiftKey) { event.preventDefault(); chat.sendMessage(this.value); this.value=''; }"></textarea>
                        <button class="btn-voice"
                                onmousedown="chat.startVoiceRecording()"
                                onmouseup="chat.stopVoiceRecording()"
                                ontouchstart="chat.startVoiceRecording()"
                                ontouchend="chat.stopVoiceRecording()">
                            <i class="fas fa-microphone"></i>
                        </button>
                        <button class="btn-send" onclick="chat.sendMessage(document.getElementById('messageInput').value); document.getElementById('messageInput').value='';">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

    // Inject into page
    const container = document.getElementById("chatAppContainer");
    if (container) {
      container.innerHTML = chatHTML;
    }
  }

  send(data) {
    if (this.ws && this.ws.readyState === WebSocket.OPEN) {
      this.ws.send(JSON.stringify(data));
    }
  }
}

// Initialize chat when user logs in
let chat;
document.addEventListener("DOMContentLoaded", () => {
  if (USER_ID && USER_NAME && USER_ROLE) {
    chat = new VerdantChat(USER_ID, USER_NAME, USER_ROLE, AUTH_TOKEN);
  }
});
```

3. **Database Schema for Messaging**

```sql
-- Complete messaging database schema
CREATE TABLE IF NOT EXISTS `conversations` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `type` ENUM('direct', 'group') DEFAULT 'direct',
    `name` VARCHAR(255) NULL, -- For group chats
    `avatar` VARCHAR(255) NULL,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `conversation_participants` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `conversation_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `role` ENUM('member', 'admin') DEFAULT 'member',
    `joined_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_read_at` TIMESTAMP NULL,
    `muted` BOOLEAN DEFAULT FALSE,
    `archived` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_participant (conversation_id, user_id),
    INDEX idx_user (user_id),
    INDEX idx_conversation (conversation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `chat_messages` (
    `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `conversation_id` INT NOT NULL,
    `sender_id` INT NOT NULL,
    `message` TEXT NULL,
    `type` ENUM('text', 'voice', 'video', 'image', 'file', 'location', 'contact') DEFAULT 'text',
    `file_url` VARCHAR(500) NULL,
    `thumbnail_url` VARCHAR(500) NULL,
    `metadata` JSON NULL, -- Store duration, waveform, file size, etc.
    `reply_to` BIGINT NULL, -- For threaded replies
    `edited` BOOLEAN DEFAULT FALSE,
    `deleted` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reply_to) REFERENCES chat_messages(id) ON DELETE SET NULL,
    INDEX idx_conversation (conversation_id),
    INDEX idx_sender (sender_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `message_read_receipts` (
    `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `message_id` BIGINT NOT NULL,
    `user_id` INT NOT NULL,
    `read_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_receipt (message_id, user_id),
    INDEX idx_message (message_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `message_reactions` (
    `id` BIGINT PRIMARY KEY AUTO_INCREMENT,
    `message_id` BIGINT NOT NULL,
    `user_id` INT NOT NULL,
    `emoji` VARCHAR(10) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_reaction (message_id, user_id, emoji),
    INDEX idx_message (message_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `video_call_sessions` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `conversation_id` INT NOT NULL,
    `caller_id` INT NOT NULL,
    `call_type` ENUM('voice', 'video') DEFAULT 'voice',
    `status` ENUM('ringing', 'ongoing', 'ended', 'missed', 'declined') DEFAULT 'ringing',
    `started_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `ended_at` TIMESTAMP NULL,
    `duration_seconds` INT DEFAULT 0,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (caller_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_conversation (conversation_id),
    INDEX idx_caller (caller_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `call_participants` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `call_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `joined_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `left_at` TIMESTAMP NULL,
    `answered` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (call_id) REFERENCES video_call_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_call (call_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Total Implementation:** 32 hours

---

### PHASE 3: BRANDING & ASSETS (Day 6 - 4 hours) 🎨

#### 3.1 CREATE COMPLETE ICON SET

```bash
# Generate all required favicon sizes
convert assets/images/favicon.svg -resize 16x16 assets/images/icons/favicon-16x16.png
convert assets/images/favicon.svg -resize 32x32 assets/images/icons/favicon-32x32.png
convert assets/images/favicon.svg -resize 192x192 assets/images/icons/android-chrome-192x192.png
convert assets/images/favicon.svg -resize 512x512 assets/images/icons/android-chrome-512x512.png
convert assets/images/favicon.svg -resize 180x180 assets/images/icons/apple-touch-icon.png

# Generate PWA splash screens
convert assets/images/logo.svg -resize 640x1136 assets/images/splash/splash-640x1136.png
convert assets/images/logo.svg -resize 750x1334 assets/images/splash/splash-750x1334.png
convert assets/images/logo.svg -resize 1242x2208 assets/images/splash/splash-1242x2208.png
```

#### 3.2 UPDATE MANIFEST.JSON

```json
{
  "name": "Verdant School Management System",
  "short_name": "Verdant SMS",
  "description": "Complete School ERP with 42 Modules",
  "icons": [
    {
      "src": "/attendance/assets/images/icons/android-chrome-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/attendance/assets/images/icons/android-chrome-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ],
  "start_url": "/attendance/",
  "display": "standalone",
  "theme_color": "#00ff9f",
  "background_color": "#0a0e27",
  "orientation": "portrait-primary"
}
```

---

### PHASE 4: ADVANCED FEATURES (Days 7-14 - 56 hours) 🚀

#### 4.1 AI-POWERED FEATURES

1. **Smart Grade Prediction**
2. **Automated Attendance Insights**
3. **Behavior Pattern Analysis**
4. **Plagiarism Detection**
5. **Personalized Learning Recommendations**

#### 4.2 MOBILE APP (React Native)

1. **iOS & Android Native Apps**
2. **Push Notifications**
3. **Offline Mode**
4. **Biometric Login**

#### 4.3 ADVANCED ANALYTICS

1. **Predictive Analytics Dashboard**
2. **Student At-Risk Identification**
3. **Teacher Performance Metrics**
4. **Financial Forecasting**

#### 4.4 PARENT ENGAGEMENT

1. **Real-time GPS Student Tracking**
2. **Automated Report Cards**
3. **Parent-Teacher Conference Scheduler**
4. **Payment Reminders (WhatsApp/SMS)**

---

## 📱 IMPLEMENTATION COMMANDS

### Quick Start All Phases

```bash
# Phase 1: Emergency Fixes
php scripts/generate-missing-pages.php
php scripts/fix-navigation-paths.php
find assets/css/ -name "*.css" -exec sed -i 's/overflow: hidden;/overflow-y: auto;/g' {} \;

# Phase 2: Install WebSocket dependencies
composer require cboden/ratchet
php server/websocket-chat-server.php &

# Phase 3: Generate icons
bash scripts/generate-icons.sh

# Phase 4: Run tests
./vendor/bin/phpunit
```

---

## 🎯 SUCCESS METRICS

**v4.0 is COMPLETE when:**

- [ ] **ZERO broken links** (0/663)
- [ ] **All 140 missing pages created**
- [ ] **WhatsApp clone messaging fully functional**
- [ ] **All pages load <2 seconds**
- [ ] **Consistent UI across all 327 PHP files**
- [ ] **Favicon visible in all browsers**
- [ ] **90+ Lighthouse score**
- [ ] **Zero console errors**
- [ ] **Mobile responsive (100%)**
- [ ] **Voice notes working**
- [ ] **Video calling working**
- [ ] **File sharing working**

---

**ESTIMATED TOTAL:** 100 hours (2.5 weeks full-time)

**NEXT ACTION:** Run `php scripts/check_links.php` to see all 140 broken links, then start creating missing pages.
