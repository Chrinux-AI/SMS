# TODO.md — VERDANT SMS v4.0 COMPLETE ADVANCED PROJECT ROADMAP

<!--
╔══════════════════════════════════════════════════════════════════════════════╗
║  VERDANT SCHOOL MANAGEMENT SYSTEM v4.0 — PRODUCTION-READY COMPLETE ROADMAP  ║
║  Status: ADVANCED FEATURE DEVELOPMENT | Updated: 25 December 2025            ║
║  Maintainer: Chrinux-AI | License: Proprietary                               ║
║  Repository: https://github.com/Chrinux-AI/SMS.git                           ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->

> **🚀 PROJECT GOALS**: Zero errors, complete feature set, WhatsApp/Telegram-style messaging, perfect UI consistency, lightning-fast performance, and production-ready launch.

---

## 📋 TABLE OF CONTENTS

1. [🔥 CRITICAL FIXES (Do First)](#-critical-fixes-do-first)
2. [💬 Communication System (WhatsApp Clone)](#-communication-system-whatsapp-clone)
3. [🎨 UI/UX Consistency & Performance](#-uiux-consistency--performance)
4. [🔗 Page Linking & Navigation](#-page-linking--navigation)
5. [🖼️ Branding & Icons](#%EF%B8%8F-branding--icons)
6. [⚡ Performance Optimization](#-performance-optimization)
7. [🔐 Security & Infrastructure](#-security--infrastructure)
8. [📱 Mobile & PWA Enhancements](#-mobile--pwa-enhancements)
9. [🤖 AI & Automation](#-ai--automation)
10. [🧪 Testing & Quality](#-testing--quality)
11. [📊 Analytics & Reporting](#-analytics--reporting)
12. [🎓 Academic Features](#-academic-features)
13. [💰 Finance & Payments](#-finance--payments)
14. [🚀 Deployment & DevOps](#-deployment--devops)
15. [📚 Documentation](#-documentation)

---

## 🔥 CRITICAL FIXES (Do First)

### CF1. DUPLICATE CHATBOT REMOVAL

**Priority**: 🔴 CRITICAL | **Effort**: 30 minutes

- [ ] Search for all chatbot implementations
  ```bash
  find . -name "*chatbot*.php" -o -name "*chat*.php" | grep -v node_modules
  ```
- [ ] Identify duplicates in:
  - `/chatbot/`
  - `/api/ai-copilot.php`
  - `/api/sams-bot.php`
  - Individual role folders
- [ ] Keep ONLY ONE: `api/ai-copilot.php` as master chatbot API
- [ ] Create `includes/chatbot-widget.php` component for universal embedding
- [ ] Update all pages to use single chatbot widget
- [ ] Delete all duplicate files

### CF2. NAVIGATION SIDEBAR FIXES

**Priority**: 🔴 CRITICAL | **Effort**: 2 hours

- [ ] **cyber-nav.php**: Fix all broken links

  - Audit all `href` attributes
  - Replace relative paths with absolute: `../folder/page.php` → `/attendance/folder/page.php`
  - Test every single link in all 25 roles

- [ ] **Remove Sidebar Duplicates**:

  ```bash
  # Find all PHP files with multiple sidebar includes
  grep -r "cyber-nav.php" --include="*.php" | cut -d: -f1 | sort | uniq -d
  ```

- [ ] **Fix Hamburger Menu Issues**:

  - Ensure ONE toggle button per page
  - Verify sidebar ID is unique: `id="cyberSidebar"`
  - Test on mobile (< 1024px width)

- [ ] **Fix Tab/Section Issues**:
  - Remove empty/placeholder sections
  - Ensure all menu items have valid destinations
  - Add loading states for tabs

### CF3. PAGE LINKING AUDIT — ZERO DEAD LINKS

**Priority**: 🔴 CRITICAL | **Effort**: 3 hours

- [ ] **Automated Link Checker**:

  ```php
  // Create scripts/check_links.php
  - Scan all PHP files
  - Extract href/action attributes
  - Test each link (file_exists or HTTP HEAD request)
  - Generate report: working vs broken
  ```

- [ ] **Create Missing Pages**:

  - 404.php (custom error page)
  - 403.php (access denied)
  - 500.php (server error)
  - maintenance.php

- [ ] **Fix Common Broken Links**:
  - `/visitor/about.php` → exists
  - `/visitor/features.php` → exists
  - `/visitor/demo-request.php` → exists
  - `/visitor/faq.php` → create
  - All footer links (case-studies.php, documentation.php, blog.php, etc.)

### CF4. UI CONSISTENCY — CYBERPUNK EVERYWHERE

**Priority**: 🔴 CRITICAL | **Effort**: 4 hours

- [ ] **Scan for Missing CSS**:

  ```bash
  # Find files missing cyberpunk-ui.css
  grep -L "cyberpunk-ui.css" **/*.php | grep -v vendor | grep -v _backups
  ```

- [ ] **Apply to ALL Pages**:

  ```html
  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title><?php echo $page_title; ?> - Verdant SMS</title>
      <link rel="icon" href="/attendance/assets/images/favicon.ico" />
      <link rel="stylesheet" href="/attendance/assets/css/cyberpunk-ui.css" />
      <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      />
    </head>
    <body class="cyber-bg">
      <div class="starfield"></div>
      <div class="cyber-grid"></div>
      <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>
        <main class="cyber-main">
          <!-- Page content -->
        </main>
      </div>
    </body>
  </html>
  ```

- [ ] **Fix Blank/White Pages**:
  - Remove inline `background: white` styles
  - Ensure `cyber-bg` class on body
  - Check for overriding CSS

### CF5. TEST_MODE SECURITY FIX

**Priority**: 🔴 CRITICAL | **Effort**: 15 minutes

- [ ] Set `TEST_MODE = false` in `includes/config.php`
- [ ] Add environment override:
  ```php
  define('TEST_MODE', getenv('DEV_MODE') === 'true' ? true : false);
  ```
- [ ] Update `.env.example` to include `DEV_MODE=false`
- [ ] Verify all auth checks work when `TEST_MODE=false`

---

## 💬 COMMUNICATION SYSTEM (WhatsApp Clone)

### CS1. REAL-TIME MESSAGING INFRASTRUCTURE

**Priority**: 🟠 HIGH | **Effort**: 3 days

- [ ] **WebSocket Server Setup**:

  ```bash
  # Install Ratchet PHP
  composer require cboden/ratchet
  ```

- [ ] **Create WebSocket Server**:

  - File: `server/websocket_server.php`
  - Handle connections, authentication, message broadcast
  - Run as daemon: `php server/websocket_server.php &`

- [ ] **Client-Side WebSocket Handler**:

  ```javascript
  // assets/js/websocket-client.js
  class ChatWebSocket {
    constructor(userId, authToken) {
      this.ws = new WebSocket("ws://localhost:8080");
      this.userId = userId;
      this.token = authToken;
    }

    sendMessage(recipientId, message) {
      this.ws.send(
        JSON.stringify({
          type: "message",
          to: recipientId,
          from: this.userId,
          content: message,
          timestamp: Date.now(),
        })
      );
    }

    onMessage(callback) {
      this.ws.onmessage = (event) => {
        const data = JSON.parse(event.data);
        callback(data);
      };
    }
  }
  ```

### CS2. CHAT UI COMPONENTS

**Priority**: 🟠 HIGH | **Effort**: 4 days

- [ ] **Chat Layout Structure**:

  ```
  ┌─────────────────────────────────────────────────────────────┐
  │  🟢 Verdant Chat                                    [•••]   │
  ├───────────────┬─────────────────────────────────────────────┤
  │               │  👤 John Doe (Teacher)                      │
  │  Conversations│  🟢 Online                          [📞][📹]│
  │               ├─────────────────────────────────────────────┤
  │  🟢 John      │                                             │
  │  🟡 Sarah     │  Hey! Can we discuss...                     │
  │  ⚫ Mark      │                                        15:23 │
  │  🟢 Lisa      │                                             │
  │               │  Sure, I'm free now.                        │
  │  [+ New Chat] │                                        15:25 │
  │               │                                             │
  │               ├─────────────────────────────────────────────┤
  │               │  [📎] Type a message...           [🎤] [😊] │
  └───────────────┴─────────────────────────────────────────────┘
  ```

- [ ] **Create Chat Components**:

  **File**: `chat/index.php` (Main chat interface)

  ```php
  <?php
  session_start();
  require_once '../includes/config.php';
  require_once '../includes/functions.php';
  require_once '../includes/database.php';
  require_login();

  $user_id = $_SESSION['user_id'];
  $user_role = $_SESSION['role'];
  ?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <title>Verdant Chat - WhatsApp-Style Messaging</title>
      <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
      <link rel="stylesheet" href="../assets/css/chat-ui.css">
  </head>
  <body class="cyber-bg">
      <div class="chat-container">
          <!-- Sidebar: Conversation List -->
          <aside class="chat-sidebar" id="chatSidebar">
              <header class="chat-header">
                  <h3>Chats</h3>
                  <div class="chat-actions">
                      <button onclick="newChat()" title="New Chat">
                          <i class="fas fa-plus"></i>
                      </button>
                      <button onclick="toggleSearch()" title="Search">
                          <i class="fas fa-search"></i>
                      </button>
                  </div>
              </header>

              <div class="search-box" id="searchBox" style="display:none;">
                  <input type="text" placeholder="Search conversations..."
                         oninput="searchConversations(this.value)">
              </div>

              <div class="conversation-list" id="conversationList">
                  <!-- Loaded via AJAX -->
              </div>
          </aside>

          <!-- Main: Active Chat -->
          <main class="chat-main" id="chatMain">
              <div class="empty-state">
                  <i class="fas fa-comments" style="font-size:4rem;opacity:0.3;"></i>
                  <p>Select a conversation to start chatting</p>
              </div>
          </main>
      </div>

      <script src="../assets/js/websocket-client.js"></script>
      <script src="../assets/js/chat-app.js"></script>
  </body>
  </html>
  ```

- [ ] **Message Bubble Component**:

  ```html
  <!-- Sent message -->
  <div class="message sent">
    <div class="message-bubble">
      <p>Hello! How are you?</p>
      <div class="message-meta">
        <span class="time">15:23</span>
        <span class="status">
          <i class="fas fa-check-double read"></i>
        </span>
      </div>
    </div>
  </div>

  <!-- Received message -->
  <div class="message received">
    <img src="avatar.jpg" class="avatar" alt="User" />
    <div class="message-bubble">
      <p>I'm good, thanks!</p>
      <div class="message-meta">
        <span class="time">15:25</span>
      </div>
    </div>
  </div>
  ```

### CS3. VOICE NOTES

**Priority**: 🟡 MEDIUM | **Effort**: 2 days

- [ ] **Browser Audio Recording**:

  ```javascript
  // assets/js/audio-recorder.js
  class AudioRecorder {
    constructor() {
      this.mediaRecorder = null;
      this.audioChunks = [];
    }

    async startRecording() {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      this.mediaRecorder = new MediaRecorder(stream);

      this.mediaRecorder.ondataavailable = (event) => {
        this.audioChunks.push(event.data);
      };

      this.mediaRecorder.start();
    }

    stopRecording() {
      return new Promise((resolve) => {
        this.mediaRecorder.onstop = () => {
          const audioBlob = new Blob(this.audioChunks, { type: "audio/webm" });
          resolve(audioBlob);
        };
        this.mediaRecorder.stop();
      });
    }
  }
  ```

- [ ] **Voice Note Upload API**:

  ```php
  // api/chat/upload_voice.php
  - Accept audio blob
  - Convert to MP3 (ffmpeg)
  - Store in uploads/voice_notes/
  - Return file URL
  ```

- [ ] **Voice Note Player UI**:
  ```html
  <div class="voice-note">
    <button class="play-btn" onclick="togglePlay(this)">
      <i class="fas fa-play"></i>
    </button>
    <div class="waveform"></div>
    <span class="duration">0:32</span>
  </div>
  ```

### CS4. VIDEO/VOICE CALLING

**Priority**: 🟡 MEDIUM | **Effort**: 4 days

- [ ] **WebRTC Integration**:

  ```javascript
  // Use SimpleWebRTC or PeerJS
  <script src="https://cdn.jsdelivr.net/npm/simple-peer@9/simplepeer.min.js"></script>;

  class VideoCall {
    constructor(localVideo, remoteVideo) {
      this.localVideo = localVideo;
      this.remoteVideo = remoteVideo;
      this.peer = null;
    }

    async initCall(initiator = false) {
      const stream = await navigator.mediaDevices.getUserMedia({
        video: true,
        audio: true,
      });

      this.localVideo.srcObject = stream;

      this.peer = new SimplePeer({
        initiator,
        stream,
        trickle: false,
      });

      this.peer.on("signal", (data) => {
        // Send signal data via WebSocket
        sendSignal(data);
      });

      this.peer.on("stream", (remoteStream) => {
        this.remoteVideo.srcObject = remoteStream;
      });
    }
  }
  ```

- [ ] **Call UI**:

  ```html
  <div class="call-screen" id="callScreen" style="display:none;">
    <video id="remoteVideo" autoplay></video>
    <video id="localVideo" autoplay muted></video>

    <div class="call-controls">
      <button onclick="toggleMic()" class="btn-control">
        <i class="fas fa-microphone"></i>
      </button>
      <button onclick="toggleCamera()" class="btn-control">
        <i class="fas fa-video"></i>
      </button>
      <button onclick="endCall()" class="btn-end-call">
        <i class="fas fa-phone-slash"></i>
      </button>
    </div>
  </div>
  ```

### CS5. FILE SHARING

**Priority**: 🟡 MEDIUM | **Effort**: 1 day

- [ ] **File Upload in Chat**:

  ```php
  // api/chat/upload_file.php
  - Validate file type (images, PDFs, docs)
  - Max size: 10MB
  - Store in uploads/chat_files/
  - Generate thumbnail for images
  - Return file metadata
  ```

- [ ] **File Preview**:

  ```html
  <!-- Image -->
  <div class="message sent">
    <div class="message-bubble image">
      <img
        src="uploads/chat_files/thumb_abc123.jpg"
        onclick="openLightbox('uploads/chat_files/abc123.jpg')"
      />
      <div class="message-meta">
        <span class="time">15:30</span>
      </div>
    </div>
  </div>

  <!-- Document -->
  <div class="message received">
    <div class="message-bubble file">
      <i class="fas fa-file-pdf"></i>
      <div class="file-info">
        <span class="filename">assignment.pdf</span>
        <span class="filesize">2.3 MB</span>
      </div>
      <a href="download.php?id=xyz" class="btn-download">
        <i class="fas fa-download"></i>
      </a>
    </div>
  </div>
  ```

### CS6. MESSAGE FEATURES

**Priority**: 🟡 MEDIUM | **Effort**: 2 days

- [ ] **Read Receipts**:

  - Track message status: sent → delivered → read
  - Display double check marks (✓✓)
  - Blue ticks when read

- [ ] **Typing Indicators**:

  ```javascript
  let typingTimer;
  messageInput.addEventListener("input", () => {
    clearTimeout(typingTimer);
    sendTypingStatus(recipientId, true);

    typingTimer = setTimeout(() => {
      sendTypingStatus(recipientId, false);
    }, 1000);
  });
  ```

- [ ] **Message Reactions**:

  ```html
  <div class="message-reactions">
    <button onclick="react('❤️')">❤️</button>
    <button onclick="react('👍')">👍</button>
    <button onclick="react('😂')">😂</button>
    <button onclick="react('😮')">😮</button>
  </div>
  ```

- [ ] **Reply to Message**:

  - Click on message to reply
  - Show quoted message above input
  - Link reply to original message

- [ ] **Message Search**:
  - Search within conversation
  - Filter by date, sender, media type

### CS7. GROUP CHATS

**Priority**: 🟡 MEDIUM | **Effort**: 3 days

- [ ] **Group Creation**:

  ```php
  // api/chat/create_group.php
  - Group name
  - Group description
  - Add members (multi-select)
  - Set group icon
  ```

- [ ] **Group Admin Features**:

  - Add/remove members
  - Change group name/icon
  - Promote members to admin
  - Delete group

- [ ] **Group Message UI**:
  - Show sender name in message
  - Member list sidebar
  - Group info page

### CS8. CHAT DATABASE SCHEMA

**Priority**: 🟠 HIGH | **Effort**: 1 hour

```sql
-- Conversations Table
CREATE TABLE conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('direct', 'group') NOT NULL DEFAULT 'direct',
    name VARCHAR(255) NULL COMMENT 'Group name',
    description TEXT NULL,
    icon_url VARCHAR(500) NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Conversation Members
CREATE TABLE conversation_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('member', 'admin') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_read_at TIMESTAMP NULL,
    is_muted TINYINT(1) DEFAULT 0,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY (conversation_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Messages Table
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    message_type ENUM('text', 'image', 'file', 'voice', 'video') DEFAULT 'text',
    content TEXT NULL COMMENT 'Message text',
    file_url VARCHAR(500) NULL,
    file_name VARCHAR(255) NULL,
    file_size INT NULL COMMENT 'Bytes',
    duration INT NULL COMMENT 'Seconds for voice/video',
    reply_to_id INT NULL COMMENT 'Message being replied to',
    is_deleted TINYINT(1) DEFAULT 0,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivered_at TIMESTAMP NULL,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (reply_to_id) REFERENCES chat_messages(id) ON DELETE SET NULL,
    INDEX idx_conversation (conversation_id, sent_at),
    INDEX idx_sender (sender_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Message Read Status
CREATE TABLE message_reads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    user_id INT NOT NULL,
    read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY (message_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Message Reactions
CREATE TABLE message_reactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    user_id INT NOT NULL,
    reaction VARCHAR(10) NOT NULL COMMENT 'Emoji',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY (message_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 🎨 UI/UX CONSISTENCY & Performance

### UI1. GLOBAL CSS VARIABLES & THEME

**Priority**: 🟠 HIGH | **Effort**: 2 hours

- [ ] **Ensure Global Theme Variables**:

  ```css
  /* assets/css/cyberpunk-ui.css */
  :root {
    /* Colors */
    --cyber-primary: #00ff9f;
    --cyber-secondary: #00d9ff;
    --cyber-accent: #ff00ea;
    --bg-primary: #0a0e27;
    --bg-secondary: #141829;
    --text-primary: #e0e0e0;
    --text-muted: #8b8b8b;

    /* Spacing */
    --spacing-xs: 4px;
    --spacing-sm: 8px;
    --spacing-md: 16px;
    --spacing-lg: 24px;
    --spacing-xl: 32px;

    /* Animation */
    --transition-fast: 0.2s ease;
    --transition-normal: 0.3s ease;
    --transition-slow: 0.5s ease;
  }
  ```

- [ ] **Remove Inline Styles**:
  - Find all `style=""` attributes
  - Replace with utility classes
  - Create utility classes if needed

### UI2. LOADING STATES

**Priority**: 🟡 MEDIUM | **Effort**: 1 day

- [ ] **Global Loader Component**:

  ```html
  <div class="loader-overlay" id="globalLoader" style="display:none;">
    <div class="cyber-loader">
      <div class="loader-ring"></div>
      <div class="loader-ring"></div>
      <div class="loader-ring"></div>
      <div class="loader-text">Loading...</div>
    </div>
  </div>
  ```

- [ ] **Page Transition Loader**:

  ```javascript
  // Show loader on navigation
  document.addEventListener("click", (e) => {
    if (e.target.tagName === "A" && !e.target.hasAttribute("download")) {
      showLoader();
    }
  });

  window.addEventListener("load", () => {
    hideLoader();
  });
  ```

- [ ] **Skeleton Screens**:
  - Use for data tables
  - Use for card grids
  - Use for chat loading

### UI3. RESPONSIVE DESIGN FIXES

**Priority**: 🟠 HIGH | **Effort**: 2 days

- [ ] **Mobile Breakpoints**:

  ```css
  /* Mobile: < 768px */
  @media (max-width: 767px) {
    .cyber-layout {
      flex-direction: column;
    }

    .cyber-sidebar {
      transform: translateX(-100%);
    }

    .cyber-sidebar.active {
      transform: translateX(0);
    }
  }

  /* Tablet: 768px - 1024px */
  @media (min-width: 768px) and (max-width: 1024px) {
    .cyber-sidebar {
      width: 200px;
    }
  }

  /* Desktop: > 1024px */
  @media (min-width: 1025px) {
    .hamburger-btn {
      display: none;
    }
  }
  ```

- [ ] **Touch-Friendly Elements**:
  - Minimum tap target: 44x44px
  - Increase button padding on mobile
  - Swipe gestures for sidebar

### UI4. ANIMATION & TRANSITIONS

**Priority**: 🟢 LOW | **Effort**: 1 day

- [ ] **Page Transitions**:

  ```css
  .page-enter {
    opacity: 0;
    transform: translateY(20px);
  }

  .page-enter-active {
    opacity: 1;
    transform: translateY(0);
    transition: all 0.3s ease;
  }
  ```

- [ ] **Micro-interactions**:
  - Button hover effects
  - Card hover lift
  - Input focus glow
  - Success/error animations

---

## 🔗 PAGE LINKING & NAVIGATION

### NAV1. NAVIGATION STRUCTURE OVERHAUL

**Priority**: 🔴 CRITICAL | **Effort**: 1 day

- [ ] **Create Master Navigation Config**:

  ```php
  // includes/navigation-config.php
  return [
      'admin' => [
          'Dashboard' => [
              'dashboard.php' => ['icon' => 'tachometer-alt', 'label' => 'Dashboard'],
          ],
          'Users' => [
              'users.php' => ['icon' => 'users', 'label' => 'All Users'],
              'account-management.php' => ['icon' => 'user-cog', 'label' => 'Account Management'],
              'approve-users.php' => ['icon' => 'user-check', 'label' => 'Approve Users'],
          ],
          // ... more
      ],
      'teacher' => [
          // ... teacher nav
      ],
      // ... more roles
  ];
  ```

- [ ] **Validate All Links on Load**:
  ```php
  // includes/nav-validator.php
  function validate_nav_links($nav_config) {
      $broken_links = [];
      foreach ($nav_config as $section => $items) {
          foreach ($items as $file => $meta) {
              if (!file_exists(__DIR__ . '/../' . $file)) {
                  $broken_links[] = $file;
              }
          }
      }
      return $broken_links;
  }
  ```

### NAV2. BREADCRUMB NAVIGATION

**Priority**: 🟡 MEDIUM | **Effort**: 4 hours

- [ ] **Auto-Generated Breadcrumbs**:

  ```php
  // includes/breadcrumbs.php
  function generate_breadcrumbs() {
      $path = $_SERVER['PHP_SELF'];
      $parts = explode('/', $path);

      $breadcrumbs = ['<a href="/attendance/">Home</a>'];

      $current_path = '/attendance';
      for ($i = 0; $i < count($parts) - 1; $i++) {
          if (empty($parts[$i])) continue;

          $current_path .= '/' . $parts[$i];
          $label = ucwords(str_replace('-', ' ', $parts[$i]));
          $breadcrumbs[] = '<a href="' . $current_path . '">' . $label . '</a>';
      }

      // Current page (no link)
      $current_page = end($parts);
      $breadcrumbs[] = '<span>' . ucwords(str_replace(['-', '.php'], [' ', ''], $current_page)) . '</span>';

      return implode(' <i class="fas fa-chevron-right"></i> ', $breadcrumbs);
  }
  ```

### NAV3. SEARCH FUNCTIONALITY

**Priority**: 🟡 MEDIUM | **Effort**: 2 days

- [ ] **Global Search Bar in Navigation**:

  ```html
  <div class="global-search">
    <input
      type="text"
      id="globalSearch"
      placeholder="Search pages, users, messages..."
      onkeyup="debounce(performSearch, 300)(this.value)"
    />
    <div class="search-results" id="searchResults"></div>
  </div>
  ```

- [ ] **Search API**:
  ```php
  // api/global_search.php
  - Search users (name, email, student ID)
  - Search pages (titles, descriptions)
  - Search messages (if has permission)
  - Search documents/resources
  - Return categorized results
  ```

---

## 🖼️ BRANDING & ICONS

### BR1. FAVICON & APP ICONS

**Priority**: 🟠 HIGH | **Effort**: 2 hours

- [ ] **Create Favicon Set**:

  - 16x16px (favicon.ico)
  - 32x32px
  - 180x180px (Apple Touch Icon)
  - 192x192px (Android Chrome)
  - 512x512px (PWA)

- [ ] **Design Elements**:

  - Use school logo or "V" letter
  - Cyberpunk aesthetic (neon green/cyan)
  - Simple, recognizable at small sizes

- [ ] **Implementation**:
  ```html
  <link
    rel="icon"
    type="image/x-icon"
    href="/attendance/assets/images/favicon.ico"
  />
  <link
    rel="apple-touch-icon"
    href="/attendance/assets/images/icons/apple-touch-icon.png"
  />
  <link
    rel="icon"
    type="image/png"
    sizes="32x32"
    href="/attendance/assets/images/icons/favicon-32x32.png"
  />
  <link
    rel="icon"
    type="image/png"
    sizes="16x16"
    href="/attendance/assets/images/icons/favicon-16x16.png"
  />
  ```

### BR2. PWA MANIFEST UPDATE

**Priority**: 🟠 HIGH | **Effort**: 30 minutes

- [ ] **Update manifest.json**:
  ```json
  {
    "name": "Verdant School Management System",
    "short_name": "Verdant SMS",
    "description": "Complete school ERP with 42 modules",
    "start_url": "/attendance/",
    "display": "standalone",
    "theme_color": "#00ff9f",
    "background_color": "#0a0e27",
    "icons": [
      {
        "src": "/attendance/assets/images/icons/icon-72x72.png",
        "sizes": "72x72",
        "type": "image/png"
      },
      {
        "src": "/attendance/assets/images/icons/icon-192x192.png",
        "sizes": "192x192",
        "type": "image/png"
      },
      {
        "src": "/attendance/assets/images/icons/icon-512x512.png",
        "sizes": "512x512",
        "type": "image/png",
        "purpose": "any maskable"
      }
    ]
  }
  ```

### BR3. SPLASH SCREENS

**Priority**: 🟢 LOW | **Effort**: 1 hour

- [ ] **Create Splash Screen Images**:

  - Various sizes for iOS (1170x2532, 1125x2436, 750x1334, etc.)
  - Android (depends on device)

- [ ] **Add to HTML**:
  ```html
  <link
    rel="apple-touch-startup-image"
    href="/attendance/assets/images/splash/iphone-x-splash.png"
    media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)"
  />
  ```

---

## ⚡ PERFORMANCE OPTIMIZATION

### PERF1. ASSET OPTIMIZATION

**Priority**: 🟠 HIGH | **Effort**: 1 day

- [ ] **Image Optimization**:

  ```bash
  # Compress all images
  find assets/images -name "*.png" -exec pngquant --ext .png --force {} \;
  find assets/images -name "*.jpg" -exec jpegoptim --max=85 {} \;
  ```

- [ ] **Lazy Loading Images**:

  ```html
  <img
    src="placeholder.jpg"
    data-src="actual-image.jpg"
    loading="lazy"
    class="lazy"
  />

  <script>
    if ("IntersectionObserver" in window) {
      let lazyImages = document.querySelectorAll(".lazy");
      let imageObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            let img = entry.target;
            img.src = img.dataset.src;
            img.classList.remove("lazy");
            imageObserver.unobserve(img);
          }
        });
      });
      lazyImages.forEach((img) => imageObserver.observe(img));
    }
  </script>
  ```

### PERF2. CSS/JS MINIFICATION

**Priority**: 🟠 HIGH | **Effort**: 2 hours

- [ ] **Install Build Tools**:

  ```bash
  npm install -g uglify-js clean-css-cli
  ```

- [ ] **Create Build Script**:

  ```bash
  #!/bin/bash
  # scripts/build_assets.sh

  # Minify CSS
  cleancss -o assets/css/cyberpunk-ui.min.css assets/css/cyberpunk-ui.css

  # Minify JavaScript
  uglifyjs assets/js/main.js -o assets/js/main.min.js
  uglifyjs assets/js/chat-app.js -o assets/js/chat-app.min.js

  # Combine common JS files
  cat assets/js/main.min.js assets/js/websocket-client.min.js > assets/js/bundle.min.js
  ```

### PERF3. DATABASE QUERY OPTIMIZATION

**Priority**: 🟠 HIGH | **Effort**: 2 days

- [ ] **Add Missing Indexes**:

  ```sql
  -- Users table
  CREATE INDEX idx_email ON users(email);
  CREATE INDEX idx_role_status ON users(role, status);

  -- Messages table
  CREATE INDEX idx_conversation_sent ON chat_messages(conversation_id, sent_at);
  CREATE INDEX idx_sender ON chat_messages(sender_id);

  -- Attendance table
  CREATE INDEX idx_date_class ON attendance_records(date, class_id);
  ```

- [ ] **Query Caching**:

  ```php
  // includes/cache-helper.php
  function cache_query($key, $callback, $ttl = 3600) {
      $cache_file = __DIR__ . '/../cache/' . md5($key) . '.json';

      if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $ttl) {
          return json_decode(file_get_contents($cache_file), true);
      }

      $result = $callback();
      file_put_contents($cache_file, json_encode($result));
      return $result;
  }

  // Usage
  $students = cache_query('all_students', function() {
      return db()->fetchAll("SELECT * FROM students");
  }, 1800);
  ```

### PERF4. ENABLE GZIP COMPRESSION

**Priority**: 🟠 HIGH | **Effort**: 15 minutes

- [ ] **Add to .htaccess**:
  ```apache
  <IfModule mod_deflate.c>
      AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
  </IfModule>
  ```

### PERF5. IMPLEMENT REDIS CACHING

**Priority**: 🟡 MEDIUM | **Effort**: 1 day

- [ ] **Install Redis**:

  ```bash
  sudo apt-get install redis-server php-redis
  ```

- [ ] **Redis Wrapper Class**:

  ```php
  // includes/redis-cache.php
  class RedisCache {
      private $redis;

      public function __construct() {
          $this->redis = new Redis();
          $this->redis->connect('127.0.0.1', 6379);
      }

      public function get($key) {
          $value = $this->redis->get($key);
          return $value ? json_decode($value, true) : null;
      }

      public function set($key, $value, $ttl = 3600) {
          return $this->redis->setex($key, $ttl, json_encode($value));
      }

      public function delete($key) {
          return $this->redis->del($key);
      }
  }
  ```

---

## 🔐 SECURITY & INFRASTRUCTURE

### SEC1. ENVIRONMENT CONFIGURATION

**Priority**: 🔴 CRITICAL | **Effort**: 1 hour

- [ ] **Create .env.example**:

  ```env
  # Database
  DB_HOST=/opt/lampp/var/mysql/mysql.sock
  DB_USER=root
  DB_PASS=
  DB_NAME=attendance_system

  # Application
  APP_NAME=Verdant SMS
  APP_URL=http://localhost/attendance
  APP_ENV=production
  DEV_MODE=false
  TIMEZONE=Africa/Lagos

  # Email
  SMTP_HOST=smtp.gmail.com
  SMTP_PORT=587
  SMTP_USER=your-email@gmail.com
  SMTP_PASS=your-app-password
  SMTP_FROM_EMAIL=noreply@verdantsms.com
  SMTP_FROM_NAME=Verdant SMS

  # SMS/WhatsApp (Twilio)
  TWILIO_SID=your-sid
  TWILIO_TOKEN=your-token
  TWILIO_PHONE=+1234567890

  # API Keys
  GROK_API_KEY=your-key
  OPENAI_API_KEY=your-key

  # Security
  SESSION_LIFETIME=1800
  PASSWORD_MIN_LENGTH=12
  JWT_SECRET=your-secret-key

  # Redis
  REDIS_HOST=127.0.0.1
  REDIS_PORT=6379

  # WebSocket
  WS_PORT=8080
  ```

### SEC2. CSRF PROTECTION

**Priority**: 🔴 CRITICAL | **Effort**: 2 hours

- [ ] **Generate CSRF Token**:

  ```php
  // includes/csrf.php
  function csrf_token() {
      if (!isset($_SESSION['csrf_token'])) {
          $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
      }
      return $_SESSION['csrf_token'];
  }

  function csrf_field() {
      return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
  }

  function csrf_verify() {
      $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
      if (!hash_equals($_SESSION['csrf_token'], $token)) {
          http_response_code(403);
          die('CSRF token mismatch');
      }
  }
  ```

- [ ] **Add to All Forms**:
  ```html
  <form method="POST">
    <?php echo csrf_field(); ?>
    <!-- Form fields -->
  </form>
  ```

### SEC3. RATE LIMITING

**Priority**: 🟠 HIGH | **Effort**: 1 day

- [ ] **Simple Rate Limiter**:

  ```php
  // includes/rate-limiter.php
  class RateLimiter {
      private $redis;

      public function __construct() {
          $this->redis = new RedisCache();
      }

      public function check($key, $max_attempts, $decay_minutes) {
          $attempts = (int)$this->redis->get($key) ?: 0;

          if ($attempts >= $max_attempts) {
              return false;
          }

          $this->redis->set($key, $attempts + 1, $decay_minutes * 60);
          return true;
      }
  }

  // Usage
  $limiter = new RateLimiter();
  if (!$limiter->check('login:' . $_SERVER['REMOTE_ADDR'], 5, 15)) {
      die('Too many attempts. Try again in 15 minutes.');
  }
  ```

### SEC4. API KEY MANAGEMENT

**Priority**: 🟡 MEDIUM | **Effort**: 1 day

- [ ] **API Keys Table**:

  ```sql
  CREATE TABLE api_keys (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      key_hash VARCHAR(255) NOT NULL,
      key_prefix VARCHAR(10) NOT NULL COMMENT 'First 8 chars for display',
      name VARCHAR(100) NOT NULL,
      scopes JSON COMMENT 'Array of allowed endpoints',
      rate_limit INT DEFAULT 100 COMMENT 'Requests per hour',
      last_used_at TIMESTAMP NULL,
      expires_at TIMESTAMP NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (user_id) REFERENCES users(id),
      UNIQUE KEY (key_hash)
  );
  ```

- [ ] **Generate API Key**:
  ```php
  // api/generate_key.php
  function generate_api_key() {
      $key = 'vsk_' . bin2hex(random_bytes(32));
      return $key;
  }
  ```

---

## 📱 MOBILE & PWA ENHANCEMENTS

### MOB1. OFFLINE FUNCTIONALITY

**Priority**: 🟡 MEDIUM | **Effort**: 2 days

- [ ] **Enhanced Service Worker**:

  ```javascript
  // sw.js
  const CACHE_VERSION = "v4.0.0";
  const CACHE_STATIC = "static-" + CACHE_VERSION;
  const CACHE_DYNAMIC = "dynamic-" + CACHE_VERSION;

  const STATIC_ASSETS = [
    "/attendance/",
    "/attendance/assets/css/cyberpunk-ui.css",
    "/attendance/assets/js/main.js",
    "/attendance/offline.html",
  ];

  self.addEventListener("install", (event) => {
    event.waitUntil(
      caches.open(CACHE_STATIC).then((cache) => {
        return cache.addAll(STATIC_ASSETS);
      })
    );
  });

  self.addEventListener("fetch", (event) => {
    event.respondWith(
      caches
        .match(event.request)
        .then((response) => {
          return (
            response ||
            fetch(event.request).then((fetchResponse) => {
              return caches.open(CACHE_DYNAMIC).then((cache) => {
                cache.put(event.request, fetchResponse.clone());
                return fetchResponse;
              });
            })
          );
        })
        .catch(() => {
          return caches.match("/attendance/offline.html");
        })
    );
  });
  ```

### MOB2. PUSH NOTIFICATIONS

**Priority**: 🟡 MEDIUM | **Effort**: 1 day

- [ ] **Enable Push Notifications**:

  ```javascript
  // Request permission
  Notification.requestPermission().then((permission) => {
    if (permission === "granted") {
      subscribeToPush();
    }
  });

  function subscribeToPush() {
    navigator.serviceWorker.ready.then((registration) => {
      registration.pushManager
        .subscribe({
          userVisibleOnly: true,
          applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
        })
        .then((subscription) => {
          // Send subscription to server
          fetch("/attendance/api/push-subscribe.php", {
            method: "POST",
            body: JSON.stringify(subscription),
            headers: { "Content-Type": "application/json" },
          });
        });
    });
  }
  ```

---

## 🤖 AI & AUTOMATION

### AI1. UNIFIED AI COPILOT

**Priority**: 🟡 MEDIUM | **Effort**: 2 days

- [ ] **Merge Duplicate Chatbots**:

  - Keep `api/ai-copilot.php` as master
  - Delete `api/sams-bot.php`
  - Update all references

- [ ] **Enhanced Natural Language Processing**:

  ```php
  // api/ai-copilot.php
  function processNLP($query, $user_role) {
      // Intent detection
      $intents = [
          'navigation' => ['go to', 'open', 'navigate', 'show me'],
          'search' => ['find', 'search', 'lookup', 'where is'],
          'action' => ['mark', 'create', 'add', 'delete', 'update'],
          'query' => ['what is', 'how many', 'show', 'list']
      ];

      foreach ($intents as $intent => $keywords) {
          foreach ($keywords as $keyword) {
              if (stripos($query, $keyword) !== false) {
                  return handleIntent($intent, $query, $user_role);
              }
          }
      }

      return handleDefaultIntent($query, $user_role);
  }
  ```

### AI2. SMART SUGGESTIONS

**Priority**: 🟢 LOW | **Effort**: 2 days

- [ ] **Context-Aware Suggestions**:
  - Suggest next actions based on current page
  - Recommend incomplete tasks
  - Predict user needs (e.g., "Did you mean to mark attendance?")

---

## 🧪 TESTING & QUALITY

### TEST1. AUTOMATED TESTING SUITE

**Priority**: 🟠 HIGH | **Effort**: 3 days

- [ ] **Unit Tests**:

  ```php
  // tests/unit/DatabaseTest.php
  // tests/unit/AuthTest.php
  // tests/unit/ValidationTest.php
  ```

- [ ] **Integration Tests**:

  ```php
  // tests/integration/LoginFlowTest.php
  // tests/integration/RegistrationTest.php
  // tests/integration/MessageSendTest.php
  ```

- [ ] **Run Tests in CI**:
  ```yaml
  # .github/workflows/tests.yml
  name: PHPUnit Tests
  on: [push, pull_request]
  jobs:
    test:
      runs-on: ubuntu-latest
      steps:
        - uses: actions/checkout@v2
        - name: Setup PHP
          uses: shivammathur/setup-php@v2
          with:
            php-version: "8.3"
        - name: Install dependencies
          run: composer install
        - name: Run tests
          run: ./vendor/bin/phpunit
  ```

---

## 📊 ANALYTICS & REPORTING

### ANALYTICS1. ADVANCED DASHBOARD

**Priority**: 🟡 MEDIUM | **Effort**: 3 days

- [ ] **Real-Time Analytics Dashboard**:

  - Active users (online now)
  - Messages sent today
  - Attendance rate
  - Fee collection status
  - Top performers (students)

- [ ] **Chart Library Integration**:

  ```html
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <canvas id="attendanceChart"></canvas>

  <script>
    new Chart(document.getElementById("attendanceChart"), {
      type: "line",
      data: {
        labels: ["Mon", "Tue", "Wed", "Thu", "Fri"],
        datasets: [
          {
            label: "Attendance %",
            data: [92, 88, 95, 90, 93],
            borderColor: "#00ff9f",
            tension: 0.4,
          },
        ],
      },
    });
  </script>
  ```

---

## 🎓 ACADEMIC FEATURES

### ACAD1. ONLINE EXAMINATION SYSTEM

**Priority**: 🟡 MEDIUM | **Effort**: 4 days

- [ ] **Exam Builder**:

  - MCQ, True/False, Short Answer
  - Timed exams
  - Random question order
  - Auto-grading

- [ ] **Proctoring Features**:
  - Browser lock (full screen)
  - Tab switching detection
  - Copy-paste prevention
  - Optional webcam monitoring

---

## 💰 FINANCE & PAYMENTS

### FIN1. PAYMENT GATEWAY INTEGRATION

**Priority**: 🟡 MEDIUM | **Effort**: 3 days

- [ ] **Paystack Integration**:

  ```php
  // api/payments/paystack.php
  function initiatePayment($email, $amount, $reference) {
      $url = "https://api.paystack.co/transaction/initialize";
      $fields = [
          'email' => $email,
          'amount' => $amount * 100, // Kobo
          'reference' => $reference,
          'callback_url' => APP_URL . '/api/payments/verify.php'
      ];

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Authorization: Bearer " . getenv('PAYSTACK_SECRET_KEY'),
          "Content-Type: application/json"
      ]);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $response = curl_exec($ch);
      curl_close($ch);

      return json_decode($response);
  }
  ```

---

## 🚀 DEPLOYMENT & DEVOPS

### DEPLOY1. DOCKER SETUP

**Priority**: 🟡 MEDIUM | **Effort**: 1 day

- [ ] **docker-compose.yml**:

  ```yaml
  version: "3.8"
  services:
    web:
      image: php:8.3-apache
      ports:
        - "8080:80"
      volumes:
        - ./:/var/www/html
      depends_on:
        - db
        - redis

    db:
      image: mysql:8.0
      environment:
        MYSQL_ROOT_PASSWORD: root
        MYSQL_DATABASE: attendance_system
      volumes:
        - db_data:/var/lib/mysql

    redis:
      image: redis:alpine
      ports:
        - "6379:6379"

  volumes:
    db_data:
  ```

### DEPLOY2. CI/CD PIPELINE

**Priority**: 🟡 MEDIUM | **Effort**: 1 day

- [ ] **GitHub Actions Workflow**:
  ```yaml
  # .github/workflows/deploy.yml
  name: Deploy to Production
  on:
    push:
      branches: [main]
  jobs:
    deploy:
      runs-on: ubuntu-latest
      steps:
        - uses: actions/checkout@v2
        - name: Deploy to server
          uses: appleboy/ssh-action@master
          with:
            host: ${{ secrets.SERVER_HOST }}
            username: ${{ secrets.SERVER_USER }}
            key: ${{ secrets.SSH_KEY }}
            script: |
              cd /opt/lampp/htdocs/attendance
              git pull origin main
              php scripts/migrate.php
              php scripts/clear_cache.php
  ```

---

## 📚 DOCUMENTATION

### DOC1. API DOCUMENTATION

**Priority**: 🟡 MEDIUM | **Effort**: 2 days

- [ ] **OpenAPI Specification**:

  ```yaml
  # api/openapi.yaml
  openapi: 3.0.0
  info:
    title: Verdant SMS API
    version: 4.0.0
  paths:
    /api/auth/login:
      post:
        summary: User login
        requestBody:
          required: true
          content:
            application/json:
              schema:
                type: object
                properties:
                  email:
                    type: string
                  password:
                    type: string
        responses:
          "200":
            description: Successful login
  ```

- [ ] **Swagger UI Integration**:
  ```html
  <!-- api/docs/index.html -->
  <!DOCTYPE html>
  <html>
    <head>
      <title>Verdant SMS API Docs</title>
      <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@4/swagger-ui.css"
      />
    </head>
    <body>
      <div id="swagger-ui"></div>
      <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@4/swagger-ui-bundle.js"></script>
      <script>
        SwaggerUIBundle({
          url: "/attendance/api/openapi.yaml",
          dom_id: "#swagger-ui",
        });
      </script>
    </body>
  </html>
  ```

---

## ✅ IMPLEMENTATION PRIORITY MATRIX

| Priority | Item                      | Effort  | Impact    | Start Date |
| -------- | ------------------------- | ------- | --------- | ---------- |
| 🔴 P1    | Duplicate Chatbot Removal | 30 min  | High      | Day 1      |
| 🔴 P1    | Navigation Sidebar Fixes  | 2 hours | Critical  | Day 1      |
| 🔴 P1    | Page Linking Audit        | 3 hours | Critical  | Day 1-2    |
| 🔴 P1    | UI Consistency            | 4 hours | High      | Day 2      |
| 🔴 P1    | TEST_MODE Security Fix    | 15 min  | Critical  | Day 2      |
| 🟠 P2    | WhatsApp Clone Messaging  | 7 days  | Very High | Day 3-9    |
| 🟠 P2    | Favicon & Branding        | 2 hours | Medium    | Day 10     |
| 🟠 P2    | Performance Optimization  | 2 days  | High      | Day 10-11  |
| 🟠 P2    | Security Hardening        | 1 day   | High      | Day 12     |
| 🟡 P3    | Mobile PWA Enhancements   | 3 days  | Medium    | Day 13-15  |
| 🟡 P3    | AI Copilot Improvements   | 2 days  | Medium    | Day 16-17  |
| 🟡 P3    | Testing Suite             | 3 days  | High      | Day 18-20  |
| 🟢 P4    | Advanced Analytics        | 3 days  | Medium    | Day 21-23  |
| 🟢 P4    | Payment Integration       | 3 days  | Medium    | Day 24-26  |
| 🟢 P4    | Documentation             | 2 days  | Low       | Day 27-28  |

---

## 🎯 QUICK START CHECKLIST (Launch in 7 Days)

### Day 1: Critical Fixes

- [ ] Remove duplicate chatbots
- [ ] Fix navigation sidebar
- [ ] Audit all page links
- [ ] Create 404/403/500 error pages

### Day 2: UI & Security

- [ ] Apply cyberpunk-ui.css to ALL pages
- [ ] Fix TEST_MODE
- [ ] Add CSRF protection
- [ ] Create favicon

### Day 3-4: Messaging Foundation

- [ ] Set up WebSocket server
- [ ] Create chat database schema
- [ ] Build basic chat UI

### Day 5-6: Messaging Features

- [ ] Implement real-time messaging
- [ ] Add read receipts
- [ ] Typing indicators
- [ ] File sharing

### Day 7: Testing & Launch

- [ ] Run automated tests
- [ ] Performance audit
- [ ] Security scan
- [ ] Deploy to production

---

## 📞 SUPPORT & CONTRIBUTION

For issues, suggestions, or contributions:

- **GitHub**: https://github.com/Chrinux-AI/SMS
- **Email**: christolabiyi35@gmail.com
- **WhatsApp**: +2348167714860

---

**Last Updated**: 25 December 2025
**Version**: 4.0.0
**Status**: In Active Development
