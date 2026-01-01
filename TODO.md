# TODO.md — THE ONE AND ONLY FINAL MASTER CHECKLIST (LAUNCH NOW)

<!--
╔══════════════════════════════════════════════════════════════════════════════╗
║  🌿 VERDANT SCHOOL MANAGEMENT SYSTEM v5.0 — ULTIMATE ENTERPRISE EDITION      ║
║  THE MOST COMPREHENSIVE SCHOOL ERP PLATFORM IN AFRICA                        ║
║  Status: ENTERPRISE LAUNCH READY | Last Updated: 13 December 2025            ║
║  Maintainer: Chrinux-AI | License: Proprietary                               ║
║  Repository: https://github.com/Chrinux-AI/SMS.git                           ║
║  Target: 100,000+ Schools | 25M+ Students | 54 African Countries             ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->

> **🎯 MISSION**: Build the world's most powerful, AI-driven, feature-complete School Management System — from nursery to university — serving millions across Africa and beyond.

---

## 🔥 CRITICAL FIXES — EXECUTE IMMEDIATELY (BEFORE ANY NEW FEATURES)

### CF1. FAVICON & BRANDING ICON — PROJECT IDENTITY

- [ ] **Create Project Favicon** (`favicon.ico`, `favicon.png`):
  ```
  Design Specifications:
  ├── Primary Icon: Green leaf with "V" integrated
  ├── Colors: #22C55E (Nature Green) + #00D9FF (Cyber Blue)
  ├── Sizes: 16x16, 32x32, 48x48, 64x64, 128x128, 256x256, 512x512
  ├── Formats: .ico (multi-size), .png, .svg
  └── Apple Touch Icon: 180x180 PNG
  ```
- [ ] Place icons in `/assets/icons/`:
  ```
  assets/icons/
  ├── favicon.ico
  ├── favicon-16x16.png
  ├── favicon-32x32.png
  ├── favicon-96x96.png
  ├── favicon-192x192.png
  ├── favicon-512x512.png
  ├── apple-touch-icon.png
  ├── android-chrome-192x192.png
  ├── android-chrome-512x512.png
  └── mstile-150x150.png
  ```
- [ ] Create `includes/head-meta.php` — Universal head include:
  ```php
  <!-- Favicon Suite -->
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/assets/icons/favicon.ico">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/assets/icons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= BASE_URL ?>/assets/icons/favicon-16x16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL ?>/assets/icons/apple-touch-icon.png">
  <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
  <meta name="theme-color" content="#22C55E">
  <meta name="msapplication-TileColor" content="#22C55E">
  ```
- [ ] Include `head-meta.php` in EVERY page

### CF2. PAGE LINKING AUDIT — ZERO DEAD LINKS

- [ ] **Create `scripts/link-audit.php`** — Automated link checker:

  ```php
  <?php
  // Scans all PHP files for href/src attributes
  // Validates each link exists
  // Reports: broken links, 404 pages, orphan pages

  function auditLinks($directory) {
      $broken = [];
      $files = glob($directory . '/**/*.php', GLOB_BRACE);
      foreach ($files as $file) {
          $content = file_get_contents($file);
          preg_match_all('/href=["\']([^"\']+)["\']/', $content, $matches);
          foreach ($matches[1] as $link) {
              if (!isValidLink($link)) {
                  $broken[] = ['file' => $file, 'link' => $link];
              }
          }
      }
      return $broken;
  }
  ```

- [ ] Run audit and fix ALL broken links
- [ ] Create `404.php` — Custom error page with:
  - Search bar
  - Common links
  - "Report this issue" button
  - AI suggestion: "Did you mean...?"

### CF3. SIDEBAR/NAVIGATION PERFECTION — ZERO FLAWS

- [ ] **Standardize ALL navigation files**:

  ```
  includes/
  ├── nav-admin.php        — Admin sidebar (ALL admin modules)
  ├── nav-teacher.php      — Teacher sidebar
  ├── nav-student.php      — Student sidebar
  ├── nav-parent.php       — Parent sidebar
  ├── nav-principal.php    — Principal sidebar
  ├── nav-librarian.php    — Librarian sidebar
  ├── nav-accountant.php   — Accountant sidebar
  ├── nav-transport.php    — Transport officer sidebar
  ├── nav-hostel.php       — Hostel warden sidebar
  ├── nav-nurse.php        — School nurse sidebar
  ├── nav-counselor.php    — Counselor sidebar
  ├── nav-visitor.php      — Public pages header
  └── nav-common.php       — Shared navigation components
  ```

- [ ] **Navigation Structure Template** (apply to ALL nav files):

  ```php
  <?php
  // includes/nav-admin.php
  $nav_sections = [
      'dashboard' => [
          'icon' => 'fas fa-tachometer-alt',
          'label' => 'Dashboard',
          'link' => '/admin/dashboard.php',
          'badge' => null
      ],
      'users' => [
          'icon' => 'fas fa-users',
          'label' => 'User Management',
          'children' => [
              ['link' => '/admin/users.php', 'label' => 'All Users'],
              ['link' => '/admin/pending-approvals.php', 'label' => 'Pending Approvals', 'badge' => $pending_count],
              ['link' => '/admin/create-user.php', 'label' => 'Create User'],
              ['link' => '/admin/bulk-import.php', 'label' => 'Bulk Import'],
          ]
      ],
      // ... all sections
  ];

  function renderNavigation($sections, $current_page) {
      foreach ($sections as $key => $section) {
          $is_active = isActiveSection($section, $current_page);
          // Render with proper active states, animations, accessibility
      }
  }
  ```

- [ ] **Sidebar Scroll Fix** — Apply to ALL themes:

  ```css
  /* CRITICAL: Sidebar must scroll independently */
  .sidebar,
  .cyber-sidebar,
  .nature-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 260px;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1000;
    scrollbar-width: thin;
  }

  .sidebar::-webkit-scrollbar {
    width: 6px;
  }

  .sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
  }

  .main-content,
  .cyber-main {
    margin-left: 260px;
    min-height: 100vh;
    overflow-y: auto;
  }

  @media (max-width: 1024px) {
    .sidebar {
      transform: translateX(-100%);
    }
    .sidebar.active {
      transform: translateX(0);
    }
    .main-content {
      margin-left: 0;
    }
  }
  ```

### CF4. REMOVE DUPLICATE CHATBOTS — SINGLE INSTANCE

- [ ] **Create `includes/chatbot-singleton.php`**:
  ```php
  <?php
  // Ensures only ONE chatbot instance per page
  if (!defined('CHATBOT_LOADED')) {
      define('CHATBOT_LOADED', true);
      ?>
      <div id="verdant-chatbot" class="chatbot-container">
          <!-- Single chatbot widget -->
      </div>
      <script src="<?= BASE_URL ?>/assets/js/chatbot.js" defer></script>
      <?php
  }
  ```
- [ ] Replace ALL chatbot includes with single include
- [ ] Chatbot state persists across page navigation

### CF5. PAGE LOAD OPTIMIZATION — SUB-2-SECOND LOADS

- [ ] **CSS Optimization**:
  ```php
  // includes/optimized-styles.php
  // Combine and minify theme CSS
  // Load critical CSS inline
  // Defer non-critical CSS
  ```
- [ ] **JavaScript Optimization**:

  ```html
  <!-- Load scripts with defer/async -->
  <script src="app.js" defer></script>

  <!-- Lazy load heavy libraries -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      if (document.querySelector(".chart-container")) {
        loadScript("/assets/js/chart.min.js");
      }
    });
  </script>
  ```

- [ ] **Image Optimization**:
  - Convert all images to WebP format
  - Implement lazy loading: `loading="lazy"`
  - Use responsive images: `srcset`
- [ ] **Database Query Optimization**:
  ```php
  // Add indexes to frequently queried columns
  // Use query caching
  // Implement pagination on large datasets
  ```
- [ ] **Enable Gzip Compression** in `.htaccess`:
  ```apache
  <IfModule mod_deflate.c>
      AddOutputFilterByType DEFLATE text/html text/plain text/css application/javascript
  </IfModule>
  ```

### CF6. UI CONSISTENCY — UNIVERSAL THEME APPLICATION

- [ ] **Create `includes/page-wrapper.php`**:

  ```php
  <?php
  function renderPage($title, $content, $options = []) {
      $theme = $_SESSION['theme'] ?? 'cyberpunk';
      $role = $_SESSION['role'] ?? 'visitor';

      include 'includes/head-meta.php';
      include "includes/themes/{$theme}-header.php";
      include "includes/nav-{$role}.php";

      echo '<main class="main-content">';
      echo $content;
      echo '</main>';

      include 'includes/chatbot-singleton.php';
      include "includes/themes/{$theme}-footer.php";
  }
  ```

- [ ] Convert ALL pages to use wrapper
- [ ] Theme CSS variables in `:root` for consistency

---

## 📱 VERDANT CHAT — WHATSAPP/TELEGRAM CLONE

### VC1. CHAT SYSTEM DATABASE SCHEMA

- [ ] **Create comprehensive chat tables**:

  ```sql
  -- Conversations (1-on-1 and groups)
  CREATE TABLE chat_conversations (
      id BIGINT PRIMARY KEY AUTO_INCREMENT,
      school_id INT NOT NULL,
      type ENUM('direct', 'group', 'broadcast', 'class', 'announcement') NOT NULL,
      name VARCHAR(200) NULL, -- For groups
      description TEXT NULL,
      avatar_url VARCHAR(255) NULL,
      created_by INT NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (school_id) REFERENCES schools(id),
      FOREIGN KEY (created_by) REFERENCES users(id)
  );

  -- Conversation participants
  CREATE TABLE chat_participants (
      id BIGINT PRIMARY KEY AUTO_INCREMENT,
      conversation_id BIGINT NOT NULL,
      user_id INT NOT NULL,
      role ENUM('admin', 'member') DEFAULT 'member',
      nickname VARCHAR(100) NULL,
      is_muted BOOLEAN DEFAULT FALSE,
      muted_until TIMESTAMP NULL,
      joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      last_read_at TIMESTAMP NULL,
      is_archived BOOLEAN DEFAULT FALSE,
      is_pinned BOOLEAN DEFAULT FALSE,
      FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
      UNIQUE KEY (conversation_id, user_id)
  );

  -- Messages
  CREATE TABLE chat_messages (
      id BIGINT PRIMARY KEY AUTO_INCREMENT,
      conversation_id BIGINT NOT NULL,
      sender_id INT NOT NULL,
      reply_to_id BIGINT NULL, -- For reply threads
      message_type ENUM('text', 'image', 'video', 'audio', 'voice_note', 'document', 'location', 'contact', 'sticker', 'system') NOT NULL,
      content TEXT NULL, -- Text content or caption
      media_url VARCHAR(500) NULL,
      media_thumbnail VARCHAR(500) NULL,
      media_size INT NULL, -- Bytes
      media_duration INT NULL, -- Seconds for audio/video
      metadata JSON NULL, -- Additional data (filename, coordinates, etc.)
      is_edited BOOLEAN DEFAULT FALSE,
      edited_at TIMESTAMP NULL,
      is_deleted BOOLEAN DEFAULT FALSE,
      deleted_at TIMESTAMP NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
      FOREIGN KEY (sender_id) REFERENCES users(id),
      FOREIGN KEY (reply_to_id) REFERENCES chat_messages(id) ON DELETE SET NULL,
      INDEX idx_conversation_created (conversation_id, created_at)
  );

  -- Message read receipts
  CREATE TABLE chat_read_receipts (
      id BIGINT PRIMARY KEY AUTO_INCREMENT,
      message_id BIGINT NOT NULL,
      user_id INT NOT NULL,
      read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
      UNIQUE KEY (message_id, user_id)
  );

  -- Message reactions
  CREATE TABLE chat_reactions (
      id BIGINT PRIMARY KEY AUTO_INCREMENT,
      message_id BIGINT NOT NULL,
      user_id INT NOT NULL,
      emoji VARCHAR(10) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
      UNIQUE KEY (message_id, user_id)
  );

  -- Voice/Video calls
  CREATE TABLE chat_calls (
      id BIGINT PRIMARY KEY AUTO_INCREMENT,
      conversation_id BIGINT NOT NULL,
      caller_id INT NOT NULL,
      call_type ENUM('voice', 'video') NOT NULL,
      status ENUM('ringing', 'ongoing', 'ended', 'missed', 'declined', 'busy') DEFAULT 'ringing',
      started_at TIMESTAMP NULL,
      ended_at TIMESTAMP NULL,
      duration INT NULL, -- Seconds
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id),
      FOREIGN KEY (caller_id) REFERENCES users(id)
  );

  -- Call participants
  CREATE TABLE chat_call_participants (
      id BIGINT PRIMARY KEY AUTO_INCREMENT,
      call_id BIGINT NOT NULL,
      user_id INT NOT NULL,
      status ENUM('ringing', 'joined', 'left', 'declined', 'missed') DEFAULT 'ringing',
      joined_at TIMESTAMP NULL,
      left_at TIMESTAMP NULL,
      FOREIGN KEY (call_id) REFERENCES chat_calls(id) ON DELETE CASCADE,
      FOREIGN KEY (user_id) REFERENCES users(id)
  );

  -- User online status
  CREATE TABLE chat_presence (
      user_id INT PRIMARY KEY,
      is_online BOOLEAN DEFAULT FALSE,
      last_seen_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      status_text VARCHAR(200) NULL, -- "Available", "Busy", custom
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  );
  ```

### VC2. CHAT UI — WHATSAPP-STYLE INTERFACE

- [ ] **Create `chat/index.php`** — Main chat application:

  ```
  ┌─────────────────────────────────────────────────────────────────────────────┐
  │  🌿 Verdant Chat                                              👤 ⚙️ 🔔     │
  ├──────────────────────┬──────────────────────────────────────────────────────┤
  │                      │                                                      │
  │  🔍 Search chats...  │   📞 Mrs. Adeyemi (Class Teacher)              •••   │
  │  ──────────────────  │   Online • Last seen today at 2:45 PM                │
  │                      │   ─────────────────────────────────────────────────  │
  │  📌 PINNED           │                                                      │
  │  ┌────────────────┐  │          [Today, 10:30 AM]                           │
  │  │👩‍🏫 Mrs. Adeyemi │  │                                                      │
  │  │Good morning... │  │   Good morning! How is Adaeze               ✓✓       │
  │  │10:30 AM    ✓✓ │  │   doing in Mathematics?                              │
  │  └────────────────┘  │                                                      │
  │                      │                       She's improving! 📈  ✓✓       │
  │  ALL CHATS           │                       I noticed her last test       │
  │  ┌────────────────┐  │                       score went up by 15%          │
  │  │👨‍👩‍👧 Parent Group │  │                                                      │
  │  │Admin: Meeting..│  │   That's wonderful news! Can we                     │
  │  │9:15 AM     42 │  │   schedule a meeting to discuss                     │
  │  └────────────────┘  │   her progress?                           ✓✓       │
  │  ┌────────────────┐  │                                                      │
  │  │🏫 JSS 2A Class │  │                                    [Typing...]      │
  │  │Teacher: Submit.│  │                                                      │
  │  │Yesterday       │  │   ─────────────────────────────────────────────────  │
  │  └────────────────┘  │                                                      │
  │  ┌────────────────┐  │   ┌───────────────────────────────────────────────┐  │
  │  │📚 Library      │  │   │ 😊 📎 🎤      Type a message...        📷 🎙️ │  │
  │  │Book due soon..│  │   └───────────────────────────────────────────────┘  │
  │  │Mon            │  │                                                      │
  │  └────────────────┘  │                                                      │
  │                      │                                                      │
  │  ➕ New Chat         │                                                      │
  │  👥 New Group        │                                                      │
  └──────────────────────┴──────────────────────────────────────────────────────┘
  ```

- [ ] **Chat Features Checklist**:
  - [ ] Real-time messaging (WebSocket via Ratchet PHP or Pusher)
  - [ ] Message status: Sent (✓), Delivered (✓✓), Read (✓✓ blue)
  - [ ] Typing indicators
  - [ ] Online/offline status with last seen
  - [ ] Reply to specific messages
  - [ ] Forward messages
  - [ ] Delete messages (for me / for everyone)
  - [ ] Edit sent messages (within 15 minutes)
  - [ ] Message reactions (emoji)
  - [ ] Pin/unpin conversations
  - [ ] Archive conversations
  - [ ] Mute notifications
  - [ ] Search within chat
  - [ ] Global message search

### VC3. MEDIA SHARING

- [ ] **Image Sharing**:

  - Multiple image upload (up to 10)
  - Image compression before upload
  - Gallery view in chat
  - Image preview modal
  - Download option

- [ ] **Document Sharing**:

  - PDF, Word, Excel, PowerPoint
  - File preview
  - Max size: 50MB

- [ ] **Voice Notes**:

  ```javascript
  class VoiceRecorder {
    constructor() {
      this.mediaRecorder = null;
      this.audioChunks = [];
    }

    async startRecording() {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      this.mediaRecorder = new MediaRecorder(stream);
      this.mediaRecorder.ondataavailable = (e) => this.audioChunks.push(e.data);
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

  - Hold to record, release to send
  - Swipe left to cancel
  - Playback with waveform visualization
  - Duration display

- [ ] **Location Sharing**:
  - Share current location
  - Share pinned location from map
  - Location preview in chat
  - Open in Google Maps

### VC4. VOICE & VIDEO CALLING (WebRTC)

- [ ] **Create `includes/webrtc-handler.php`**:

  ```php
  class WebRTCSignaling {
      // TURN/STUN server configuration
      // ICE candidate exchange
      // Session Description Protocol (SDP) handling
  }
  ```

- [ ] **Call Interface**:

  ```
  ┌──────────────────────────────────────────┐
  │                                          │
  │              👤                           │
  │          Mrs. Adeyemi                    │
  │                                          │
  │           Calling...                     │
  │            00:00                         │
  │                                          │
  │                                          │
  │    ┌─────┐   ┌─────┐   ┌─────┐          │
  │    │ 🔇  │   │ 📹  │   │ 🔊  │          │
  │    │Mute │   │Video│   │Speaker│         │
  │    └─────┘   └─────┘   └─────┘          │
  │                                          │
  │           ┌───────────┐                  │
  │           │    🔴     │                  │
  │           │   End     │                  │
  │           └───────────┘                  │
  │                                          │
  └──────────────────────────────────────────┘
  ```

- [ ] **Call Features**:
  - 1-on-1 voice calls
  - 1-on-1 video calls
  - Group voice calls (up to 8 participants)
  - Group video calls (up to 4 participants)
  - Call waiting/hold
  - Mute/unmute
  - Speaker toggle
  - Camera flip (front/back)
  - Screen sharing (video calls)
  - Picture-in-picture mode
  - Call history with duration

### VC5. GROUP CHAT FEATURES

- [ ] **Group Management**:

  - Create group with name, icon, description
  - Add/remove participants
  - Admin/member roles
  - Group settings (who can send messages, edit info)
  - Leave group
  - Report group

- [ ] **Pre-built Groups** (auto-created):
  - Class groups (JSS 2A Parents, SSS 1B Students)
  - Subject groups (Mathematics Teachers)
  - Department groups (Science Department)
  - Staff group (All Teachers)
  - Announcement channel (Admin only can post)

### VC6. CHAT ROLE-BASED PERMISSIONS

- [ ] **Permission Matrix**:
      | Role | Can Chat With | Can Create Groups | Can Make Calls |
      |------|---------------|-------------------|----------------|
      | Admin | Everyone | Yes | Yes |
      | Principal | Everyone | Yes | Yes |
      | Teacher | Teachers, Parents, Students | Class groups only | Yes |
      | Parent | Teachers, Admin | No | Voice only |
      | Student | Teachers, Classmates | No | No |

### VC7. CHAT API ENDPOINTS

- [ ] **Create `api/chat/` endpoints**:
  ```
  api/chat/
  ├── conversations.php    — GET (list), POST (create)
  ├── messages.php         — GET (history), POST (send)
  ├── media.php            — POST (upload), GET (download)
  ├── calls.php            — POST (initiate), PUT (answer/decline)
  ├── presence.php         — PUT (update status)
  └── websocket.php        — WebSocket handler
  ```

---

## 🤖 AI INTELLIGENCE LAYER — COMPREHENSIVE

### AI1. CENTRAL AI ENGINE

- [ ] **Create `includes/ai-engine.php`**:

  ```php
  class VerdantAI {
      private $provider; // 'openai', 'anthropic', 'local'

      public function __construct() {
          $this->provider = getenv('AI_PROVIDER') ?: 'openai';
      }

      public function chat(string $prompt, array $context = []): string {
          // Route to appropriate provider
      }

      public function analyzeDocument(string $filePath): array {
          // OCR + data extraction
      }

      public function generateReport(string $type, array $data): string {
          // Generate formatted reports
      }

      public function predictRisk(string $type, int $studentId): array {
          // Dropout, academic, behavioral risk prediction
      }

      public function transcribeAudio(string $audioPath): string {
          // Voice to text (Whisper API)
      }

      public function translateText(string $text, string $targetLang): string {
          // Multi-language support
      }
  }
  ```

### AI2. AI CHATBOT — UNIFIED ACROSS ALL ROLES

- [ ] **Chatbot Capabilities by Role**:

  **Admin Bot**:

  - "Show me all pending approvals"
  - "Generate fee collection report for November"
  - "How many students are enrolled in SSS 2?"
  - "Which teachers have the highest attendance?"
  - "Show schools with expiring subscriptions"

  **Teacher Bot**:

  - "Mark Primary 3A present for today"
  - "Show JSS 1B exam results"
  - "Generate report cards for my class"
  - "Which students are at risk of failing?"
  - "Schedule parent meeting with Mrs. Okonkwo"

  **Student Bot**:

  - "What's my next class?"
  - "Show my Mathematics grades"
  - "When is my assignment due?"
  - "What books do I have checked out?"
  - "Show my attendance record"

  **Parent Bot**:

  - "Is Adaeze in school today?"
  - "Show her recent grades"
  - "What fees are outstanding?"
  - "Message her class teacher"
  - "Show upcoming events"

### AI3. AI FEATURES IMPLEMENTATION

- [ ] **Smart Report Generation**:

  ```php
  // Auto-generate teacher comments for report cards
  $ai->generateReportComment([
      'student_name' => 'Adaeze Okonkwo',
      'subject' => 'Mathematics',
      'score' => 78,
      'class_average' => 65,
      'improvement' => '+12 from last term',
      'attendance' => '95%'
  ]);
  // Output: "Adaeze has demonstrated remarkable growth in Mathematics this term,
  // scoring well above the class average. Her consistent attendance reflects her
  // dedication to learning. I encourage her to maintain this excellent trajectory."
  ```

- [ ] **Dropout Risk Prediction**:

  ```php
  $risk = $ai->predictRisk('dropout', $student_id);
  // Factors: attendance, grades, fee payment, behavior, family circumstances
  // Returns: { score: 72, factors: [...], recommendations: [...] }
  ```

- [ ] **Academic Performance Prediction**:

  - Predict end-of-term grades from CA scores
  - Identify students needing intervention
  - Recommend tutoring subjects

- [ ] **Fee Default Prediction**:

  - Analyze payment history patterns
  - Identify at-risk accounts
  - Suggest payment plans

- [ ] **Document OCR & Extraction**:

  - Birth certificate → Extract DOB, name, LGA
  - Transfer certificate → Extract previous school, grades
  - WAEC result → Extract subjects, grades

- [ ] **AI Translation** (Nigerian Languages):
  - English ↔ Yoruba
  - English ↔ Igbo
  - English ↔ Hausa
  - English ↔ Pidgin

### AI4. AI EMAIL PROCESSOR

- [ ] **Automated Email Handling**:
  ```php
  class AIEmailProcessor {
      public function processInbox() {
          $emails = $this->fetchUnread();
          foreach ($emails as $email) {
              $category = $this->ai->categorize($email);
              switch ($category) {
                  case 'inquiry':
                      $this->createLead($email);
                      $this->sendAutoReply($email, 'inquiry_template');
                      break;
                  case 'support':
                      $this->createTicket($email);
                      break;
                  case 'complaint':
                      $this->escalate($email);
                      break;
                  case 'spam':
                      $this->archive($email);
                      break;
              }
          }
      }
  }
  ```

---

## 🔐 SECURITY FORTRESS — ENTERPRISE GRADE

### SEC1. AUTHENTICATION SYSTEM

- [ ] **Multi-Factor Authentication (MFA)**:

  ```sql
  CREATE TABLE mfa_methods (
      id INT PRIMARY KEY AUTO_INCREMENT,
      user_id INT NOT NULL,
      method ENUM('totp', 'sms', 'email', 'biometric', 'hardware_key', 'backup_codes') NOT NULL,
      secret_encrypted BLOB,
      is_primary BOOLEAN DEFAULT FALSE,
      is_verified BOOLEAN DEFAULT FALSE,
      last_used_at TIMESTAMP NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  );
  ```

- [ ] **WebAuthn Biometric Login**:

  - Fingerprint (Touch ID, Windows Hello)
  - Face recognition (Face ID)
  - Security keys (YubiKey)
  - Passkeys

- [ ] **Adaptive Authentication**:

  - Risk scoring based on: IP, device, time, location, behavior
  - Low risk: Standard login
  - Medium risk: Require MFA
  - High risk: Block + admin notification

- [ ] **Session Management**:
  - View all active sessions
  - Revoke individual or all sessions
  - Auto-expire after inactivity
  - Concurrent session limits per role

### SEC2. ACCESS CONTROL

- [ ] **Granular Permission System** (500+ permissions):
  ```
  students.*         — create, read, update, delete, export, bulk_import
  teachers.*         — create, read, update, delete, assign_class, view_salary
  attendance.*       — mark, view_own, view_class, view_school, edit, reports
  grades.*           — enter, view, edit, approve, publish, generate_reports
  fees.*             — create_invoice, view, collect, waive, reports, settings
  library.*          — issue, return, add_book, remove_book, reports
  transport.*        — assign_route, track, manage_vehicles, reports
  hostel.*           — allocate, checkout, manage_rooms, reports
  hr.*               — hire, terminate, payroll, leave_approval, appraisal
  chat.*             — send, create_group, make_calls, delete_messages
  ```

### SEC3. AUDIT & COMPLIANCE

- [ ] **Complete Audit Trail**:

  ```sql
  CREATE TABLE audit_logs (
      id BIGINT PRIMARY KEY AUTO_INCREMENT,
      school_id INT,
      user_id INT,
      action VARCHAR(100),
      resource_type VARCHAR(50),
      resource_id INT,
      old_values JSON,
      new_values JSON,
      ip_address VARCHAR(45),
      user_agent TEXT,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_school_created (school_id, created_at),
      INDEX idx_user_action (user_id, action)
  );
  ```

- [ ] **NDPR Compliance**:
  - Data processing consent
  - Right to erasure (account deletion)
  - Data export (user can download their data)
  - Privacy policy acceptance tracking

---

## 💰 FINANCIAL MANAGEMENT SUITE

### FIN1. COMPREHENSIVE FEE MANAGEMENT

- [ ] **Fee Structure**:

  ```sql
  CREATE TABLE fee_structures (
      id INT PRIMARY KEY AUTO_INCREMENT,
      school_id INT NOT NULL,
      name VARCHAR(100), -- 'JSS Tuition 2024/2025'
      academic_session_id INT,
      class_level_id INT,
      amount DECIMAL(12, 2),
      due_date DATE,
      late_fee DECIMAL(12, 2) DEFAULT 0,
      late_fee_type ENUM('fixed', 'percentage', 'daily') DEFAULT 'fixed',
      is_mandatory BOOLEAN DEFAULT TRUE,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE fee_invoices (
      id BIGINT PRIMARY KEY AUTO_INCREMENT,
      school_id INT NOT NULL,
      student_id INT NOT NULL,
      invoice_number VARCHAR(50) UNIQUE,
      academic_session_id INT,
      term_id INT,
      total_amount DECIMAL(12, 2),
      discount_amount DECIMAL(12, 2) DEFAULT 0,
      discount_reason VARCHAR(200),
      paid_amount DECIMAL(12, 2) DEFAULT 0,
      balance DECIMAL(12, 2),
      status ENUM('draft', 'pending', 'partial', 'paid', 'overdue', 'cancelled') DEFAULT 'pending',
      due_date DATE,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE fee_payments (
      id BIGINT PRIMARY KEY AUTO_INCREMENT,
      invoice_id BIGINT NOT NULL,
      amount DECIMAL(12, 2),
      payment_method ENUM('cash', 'bank_transfer', 'card', 'ussd', 'mobile_money') NOT NULL,
      payment_reference VARCHAR(100),
      gateway VARCHAR(50), -- 'paystack', 'flutterwave', 'monnify'
      gateway_reference VARCHAR(100),
      receipt_number VARCHAR(50),
      received_by INT,
      payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      notes TEXT
  );
  ```

### FIN2. PAYMENT GATEWAY INTEGRATION

- [ ] **Multi-Gateway Support**:

  - Paystack (Primary)
  - Flutterwave
  - Monnify (Virtual accounts)
  - Bank Transfer
  - Cash collection

- [ ] **Virtual Account per Student**:

  - Dedicated NUBAN for each student
  - Auto-credit on transfer
  - Instant notification

- [ ] **Payment Plans**:
  - Full payment
  - 2 installments
  - 3 installments
  - Custom plans

### FIN3. HR & PAYROLL

- [ ] **Staff Salary Management**:

  ```sql
  CREATE TABLE staff_salaries (
      id INT PRIMARY KEY AUTO_INCREMENT,
      staff_id INT NOT NULL,
      basic_salary DECIMAL(12, 2),
      housing_allowance DECIMAL(12, 2),
      transport_allowance DECIMAL(12, 2),
      medical_allowance DECIMAL(12, 2),
      pension_contribution DECIMAL(12, 2), -- Employee's 8%
      employer_pension DECIMAL(12, 2), -- Employer's 10%
      tax_paye DECIMAL(12, 2),
      net_salary DECIMAL(12, 2),
      effective_date DATE,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE payroll_runs (
      id INT PRIMARY KEY AUTO_INCREMENT,
      school_id INT NOT NULL,
      month INT,
      year INT,
      total_gross DECIMAL(14, 2),
      total_deductions DECIMAL(14, 2),
      total_net DECIMAL(14, 2),
      status ENUM('draft', 'approved', 'processing', 'completed') DEFAULT 'draft',
      approved_by INT,
      processed_at TIMESTAMP NULL
  );
  ```

- [ ] **Payroll Features**:
  - Monthly payroll processing
  - Automatic tax calculation (PAYE)
  - Pension deductions
  - Payslip generation (PDF)
  - Bank upload file generation
  - Year-end tax reports

---

## 📚 LEARNING MANAGEMENT SYSTEM (LMS)

### LMS1. COURSE MANAGEMENT

- [ ] **Course Structure**:

  ```sql
  CREATE TABLE lms_courses (
      id INT PRIMARY KEY AUTO_INCREMENT,
      school_id INT NOT NULL,
      subject_id INT NOT NULL,
      class_id INT NOT NULL,
      teacher_id INT NOT NULL,
      title VARCHAR(200),
      description TEXT,
      thumbnail_url VARCHAR(255),
      is_published BOOLEAN DEFAULT FALSE,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE lms_modules (
      id INT PRIMARY KEY AUTO_INCREMENT,
      course_id INT NOT NULL,
      title VARCHAR(200),
      description TEXT,
      order_index INT DEFAULT 0,
      is_published BOOLEAN DEFAULT FALSE
  );

  CREATE TABLE lms_lessons (
      id INT PRIMARY KEY AUTO_INCREMENT,
      module_id INT NOT NULL,
      title VARCHAR(200),
      content_type ENUM('video', 'document', 'quiz', 'assignment', 'live_class') NOT NULL,
      content_url VARCHAR(500),
      content_text LONGTEXT,
      duration_minutes INT,
      order_index INT DEFAULT 0,
      is_published BOOLEAN DEFAULT FALSE
  );

  CREATE TABLE lms_progress (
      id BIGINT PRIMARY KEY AUTO_INCREMENT,
      student_id INT NOT NULL,
      lesson_id INT NOT NULL,
      status ENUM('not_started', 'in_progress', 'completed') DEFAULT 'not_started',
      progress_percent INT DEFAULT 0,
      time_spent_seconds INT DEFAULT 0,
      completed_at TIMESTAMP NULL,
      UNIQUE KEY (student_id, lesson_id)
  );
  ```

### LMS2. ASSESSMENT ENGINE

- [ ] **Quiz Builder**:

  - Multiple choice (single/multiple answer)
  - True/False
  - Fill in the blank
  - Matching
  - Short answer (AI graded)
  - Essay (teacher graded)
  - Drag and drop
  - Image-based questions

- [ ] **Assignment System**:
  - File upload submissions
  - Plagiarism check (basic)
  - Rubric-based grading
  - Peer review option
  - Late submission handling

### LMS3. VIRTUAL CLASSROOM

- [ ] **Live Class Features**:
  - Video conferencing (Jitsi Meet integration)
  - Screen sharing
  - Interactive whiteboard
  - Breakout rooms
  - Attendance tracking
  - Recording & playback
  - Chat during class
  - Hand raise feature
  - Polls and quizzes

---

## 📊 ANALYTICS & BUSINESS INTELLIGENCE

### BI1. DASHBOARD WIDGETS

- [ ] **Admin Dashboard**:
  ```
  ┌─────────────────────────────────────────────────────────────────────────────┐
  │  📊 School Overview                                    🗓️ This Term         │
  ├───────────────┬───────────────┬───────────────┬───────────────────────────┤
  │   👨‍🎓 1,250    │   👩‍🏫 85       │   📚 42       │   💰 ₦12.5M Collected     │
  │   Students    │   Teachers    │   Classes     │   (67% of target)         │
  ├───────────────┴───────────────┴───────────────┴───────────────────────────┤
  │                                                                           │
  │  📈 Attendance Trend (Last 30 Days)                                       │
  │  ████████████████████████████████████████████ 94.2%                       │
  │                                                                           │
  │  📊 Fee Collection by Class          │  🎯 Top Performing Classes        │
  │  ┌────────────────────────────────┐  │  1. SSS 3A - 82.5% avg           │
  │  │ [Bar Chart: JSS1-SSS3]        │  │  2. JSS 2B - 79.3% avg           │
  │  └────────────────────────────────┘  │  3. Primary 6 - 78.1% avg        │
  │                                                                           │
  │  ⚠️ Alerts                           │  📅 Upcoming Events               │
  │  • 23 students with low attendance  │  • Dec 15: Carol Service          │
  │  • 45 unpaid invoices (₦2.3M)       │  • Dec 20: Term ends              │
  │  • 3 teacher leave requests pending │  • Jan 8: New term begins         │
  └───────────────────────────────────────────────────────────────────────────┘
  ```

### BI2. REPORT BUILDER

- [ ] **Custom Report Generator**:
  - Drag-and-drop field selection
  - Filter builder
  - Grouping and aggregation
  - Chart type selection
  - Export to PDF/Excel/CSV
  - Save and share reports
  - Schedule automated reports

### BI3. PREDICTIVE ANALYTICS

- [ ] **AI-Powered Insights**:
  - Enrollment forecasting
  - Revenue projections
  - Dropout risk analysis
  - Academic performance trends
  - Staff workload optimization

---

## 🌍 MULTI-TENANCY & SCALABILITY

### MT1. TENANT ISOLATION

- [ ] **School Isolation**:
  - Every data table has `school_id`
  - All queries filtered by tenant
  - Subdomain routing (myschool.verdantsms.com)
  - Custom domain support (school.edu.ng)

### MT2. SUBSCRIPTION MANAGEMENT

- [ ] **Pricing Tiers**:
      | Plan | Monthly (₦) | Yearly (₦) | Students | Features |
      |------|-------------|------------|----------|----------|
      | Free | ₦0 | ₦0 | 50 | Core modules |
      | Starter | ₦5,000 | ₦50,000 | 200 | Email support |
      | Growth | ₦15,000 | ₦150,000 | 500 | AI, SMS, Priority |
      | Professional | ₦30,000 | ₦300,000 | 1,000 | API, Account Manager |
      | Enterprise | Custom | Custom | Unlimited | On-premise, SLA |

### MT3. PLATFORM ADMIN

- [ ] **Verdant Central Dashboard**:
  - All schools overview
  - Subscription management
  - Revenue analytics
  - Support ticket system
  - School onboarding pipeline
  - Marketing campaigns

---

## 📱 MOBILE & OFFLINE

### MOB1. PWA OPTIMIZATION

- [ ] **Service Worker Enhancements**:

  - Offline page caching
  - Background sync for forms
  - Push notification handling
  - App install prompt

- [ ] **Mobile-Specific Features**:
  - Touch-optimized UI
  - Swipe gestures
  - Pull-to-refresh
  - Bottom navigation bar
  - Quick actions (FAB)

### MOB2. OFFLINE CAPABILITIES

- [ ] **Offline Mode for Rural Areas**:
  - Cache attendance forms
  - Cache student lists
  - Background sync when online
  - Conflict resolution
  - Low-bandwidth mode

---

## 🔗 INTEGRATIONS

### INT1. PAYMENT GATEWAYS

- [ ] Paystack
- [ ] Flutterwave
- [ ] Monnify
- [ ] Bank Transfer

### INT2. COMMUNICATION

- [ ] SMS (Termii, Africa's Talking)
- [ ] Email (SMTP, SendGrid)
- [ ] WhatsApp Business API
- [ ] Push Notifications (Firebase)

### INT3. EXTERNAL SYSTEMS

- [ ] WAEC/NECO data export
- [ ] JAMB registration export
- [ ] Google Workspace integration
- [ ] Microsoft 365 integration
- [ ] Zoom/Google Meet

---

## ✅ COMPLETE MODULE LIST (75+ MODULES)

### Core Modules

- [ ] Dashboard (per role)
- [ ] User Management
- [ ] Role & Permission Management
- [ ] School Settings
- [ ] Academic Session Management
- [ ] Class/Grade Management
- [ ] Subject Management

### Student Management

- [ ] Student Registration
- [ ] Student Profiles
- [ ] Student Documents
- [ ] Student Transfer
- [ ] Graduation Management
- [ ] Alumni Tracking

### Academic

- [ ] Attendance Management
- [ ] Grade/Mark Entry
- [ ] Report Card Generation
- [ ] Exam Management
- [ ] Timetable Management
- [ ] Homework/Assignment
- [ ] Lesson Plans

### Finance

- [ ] Fee Structure
- [ ] Invoice Generation
- [ ] Payment Collection
- [ ] Payment Plans
- [ ] Financial Reports
- [ ] Scholarship Management

### HR & Staff

- [ ] Staff Profiles
- [ ] Recruitment
- [ ] Payroll Processing
- [ ] Leave Management
- [ ] Performance Appraisal
- [ ] Staff Attendance

### Communication

- [ ] Verdant Chat (WhatsApp clone)
- [ ] Announcements
- [ ] Notice Board
- [ ] Email Notifications
- [ ] SMS Notifications
- [ ] Push Notifications

### Library

- [ ] Book Catalog
- [ ] Issue/Return
- [ ] E-Books
- [ ] Past Questions Bank
- [ ] Overdue Management

### Transport

- [ ] Route Management
- [ ] Vehicle Management
- [ ] Driver Management
- [ ] Student Assignment
- [ ] GPS Tracking

### Hostel

- [ ] Room Management
- [ ] Bed Allocation
- [ ] Hostel Attendance
- [ ] Visitor Management
- [ ] Hostel Fees

### Health

- [ ] Student Health Records
- [ ] Clinic Visits
- [ ] Medication Log
- [ ] Immunization Records
- [ ] Emergency Contacts

### Extras

- [ ] Canteen Management
- [ ] Inventory Management
- [ ] Event Management
- [ ] Visitor Management
- [ ] Complaint Management
- [ ] Certificate Generation

---

## 📋 EXECUTION PRIORITY

| Priority | Category       | Tasks                            | Status |
| -------- | -------------- | -------------------------------- | ------ |
| 🔴 P0    | Critical Fixes | Favicon, Link Audit, Sidebar Fix | [ ]    |
| 🔴 P0    | Critical Fixes | Duplicate Chatbot Removal        | [ ]    |
| 🔴 P0    | Critical Fixes | Page Load Optimization           | [ ]    |
| 🟠 P1    | Core Feature   | Verdant Chat System              | [ ]    |
| 🟠 P1    | Core Feature   | Voice/Video Calling              | [ ]    |
| 🟠 P1    | Security       | MFA & Biometric Auth             | [ ]    |
| 🟡 P2    | AI             | Unified AI Chatbot               | [ ]    |
| 🟡 P2    | Finance        | Payment Gateway Integration      | [ ]    |
| 🟡 P2    | LMS            | Course & Assessment Engine       | [ ]    |
| 🟢 P3    | Analytics      | Dashboard & Reports              | [ ]    |
| 🟢 P3    | Multi-Tenancy  | Subscription System              | [ ]    |
| 🟢 P3    | Mobile         | PWA Optimization                 | [ ]    |

---

**Verdant SMS v5.0 — The Ultimate African School Management Platform** 🌍🚀

**Built for Excellence. Designed for Scale. Ready for Tomorrow.**

**Last Updated: 13 December 2025**

<!--
╔══════════════════════════════════════════════════════════════════════════════╗
║  VERDANT SCHOOL MANAGEMENT SYSTEM v5.0 — ULTIMATE ENTERPRISE EDITION         ║
║  THE MOST COMPREHENSIVE SCHOOL ERP IN AFRICA                                  ║
║  Status: ENTERPRISE LAUNCH | Last Updated: 13 December 2025                  ║
║  Maintainer: Chrinux-AI | License: Proprietary                               ║
║  Repository: https://github.com/Chrinux-AI/SMS.git                           ║
║  Target: 50,000+ Schools | 10M+ Students | 54 African Countries              ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->

> **🎯 MISSION**: Build Africa's most powerful, AI-driven, feature-rich School Management System that handles everything from nursery to university, serving millions of students across the continent.

---

## 📊 PROJECT ANALYSIS & ENHANCEMENT SUMMARY

### Current State Assessment

- ✅ 42 modular modules (solid foundation)
- ✅ 8 beautiful themes with dual UI
- ✅ 18 user roles with RBAC
- ✅ Nigerian localization started
- ✅ PWA-ready architecture
- ⚠️ Missing: Advanced AI integration
- ⚠️ Missing: Real-time collaboration features
- ⚠️ Missing: Comprehensive parent/student mobile experience
- ⚠️ Missing: Advanced financial/accounting system
- ⚠️ Missing: Learning Management System (LMS) depth
- ⚠️ Missing: Staff performance & development
- ⚠️ Missing: Facility & resource management
- ⚠️ Missing: Advanced security features

### New Features Added (200+ enhancements)

This TODO adds **200+ new features** organized into:

1. **Core Security Overhaul** (25 tasks)
2. **AI Intelligence Layer** (40 tasks)
3. **Financial Management Suite** (35 tasks)
4. **Learning Management System** (45 tasks)
5. **Human Resources Module** (30 tasks)
6. **Communication Hub** (25 tasks)
7. **Analytics & Business Intelligence** (35 tasks)
8. **Mobile & Offline Capabilities** (20 tasks)
9. **Integration Ecosystem** (25 tasks)
10. **Infrastructure & DevOps** (20 tasks)

---

## 🔐 SECTION A: SECURITY FORTRESS — MILITARY-GRADE PROTECTION

### A1. AUTHENTICATION OVERHAUL

- [ ] **Multi-Factor Authentication (MFA) Engine**:

  ```sql
  CREATE TABLE mfa_methods (
      id INT PRIMARY KEY AUTO_INCREMENT,
      user_id INT NOT NULL,
      method ENUM('totp', 'sms', 'email', 'biometric', 'hardware_key', 'backup_codes') NOT NULL,
      secret_encrypted BLOB,
      is_primary BOOLEAN DEFAULT FALSE,
      is_verified BOOLEAN DEFAULT FALSE,
      last_used_at TIMESTAMP NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  );

  CREATE TABLE backup_codes (
      id INT PRIMARY KEY AUTO_INCREMENT,
      user_id INT NOT NULL,
      code_hash VARCHAR(255) NOT NULL,
      is_used BOOLEAN DEFAULT FALSE,
      used_at TIMESTAMP NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  );
  ```

- [ ] **Adaptive Authentication**:

  - Risk scoring based on: IP, device, time, location, behavior
  - Low risk (score < 30): Standard login
  - Medium risk (30-70): Require MFA
  - High risk (> 70): Block + admin notification
  - Machine learning model for anomaly detection

- [ ] **Session Management**:

  ```sql
  CREATE TABLE user_sessions (
      id VARCHAR(128) PRIMARY KEY,
      user_id INT NOT NULL,
      ip_address VARCHAR(45),
      user_agent TEXT,
      device_fingerprint VARCHAR(64),
      location_country VARCHAR(50),
      location_city VARCHAR(100),
      is_active BOOLEAN DEFAULT TRUE,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      last_activity_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      expires_at TIMESTAMP NOT NULL,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  );
  ```

  - View all active sessions
  - Revoke individual or all sessions
  - Auto-expire after inactivity
  - Concurrent session limits per role

- [ ] **Password Security**:
  - Argon2id hashing (upgrade from bcrypt)
  - Password breach checking (HaveIBeenPwned API)
  - Password strength meter with real-time feedback
  - Password history (prevent reuse of last 10)
  - Configurable password policies per role

### A2. ACCESS CONTROL & PERMISSIONS

- [ ] **Granular Permission System**:

  ```sql
  CREATE TABLE permissions (
      id INT PRIMARY KEY AUTO_INCREMENT,
      name VARCHAR(100) UNIQUE NOT NULL, -- 'students.create', 'fees.view'
      description TEXT,
      module VARCHAR(50),
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE role_permissions (
      role_id INT NOT NULL,
      permission_id INT NOT NULL,
      granted_by INT,
      granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (role_id, permission_id)
  );

  CREATE TABLE user_permissions (
      user_id INT NOT NULL,
      permission_id INT NOT NULL,
      is_grant BOOLEAN DEFAULT TRUE, -- TRUE = grant, FALSE = deny (override)
      granted_by INT,
      granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (user_id, permission_id)
  );
  ```

- [ ] **Permission Categories** (500+ permissions):
  ```
  students.*         — create, read, update, delete, export, bulk_import
  teachers.*         — create, read, update, delete, assign_class, view_salary
  attendance.*       — mark, view_own, view_class, view_school, edit, reports
  grades.*           — enter, view, edit, approve, publish, generate_reports
  fees.*             — create_invoice, view, collect, waive, reports, settings
  library.*          — issue, return, add_book, remove_book, reports
  transport.*        — assign_route, track, manage_vehicles, reports
  hostel.*           — allocate, checkout, manage_rooms, reports
  hr.*               — hire, terminate, payroll, leave_approval, appraisal
  ```

<!--
╔══════════════════════════════════════════════════════════════════════════════╗
║  VERDANT SCHOOL MANAGEMENT SYSTEM v3.0 — ULTIMATE SECURE MASTER CHECKLIST    ║
║  Status: FINAL SECURE LAUNCH | Last Updated: 13 December 2025                ║
║  Maintainer: Chrinux-AI | License: Proprietary                               ║
║  Repository: https://github.com/Chrinux-AI/SMS.git                           ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->

> **🔐 SECURITY NOTICE**: This TODO enforces production-grade security. NO test accounts. NO exposed admin links. Biometric-first authentication. Verified emails only.

---

## 🚨 CRITICAL SECURITY OVERHAUL — EXECUTE FIRST

### 0. PURGE ALL TESTING ACCOUNTS — IMMEDIATE ACTION

- [ ] **DELETE** all 25 testing accounts from `users` table:
  ```sql
  DELETE FROM users WHERE email LIKE '%@verdant.edu';
  DELETE FROM users WHERE email LIKE '%@test.%';
  DELETE FROM users WHERE email LIKE 'test%@%';
  DELETE FROM users WHERE email_verified_at IS NULL AND created_at < NOW() - INTERVAL 7 DAY;
  ```
- [ ] **VERIFY** zero test accounts remain:
  ```sql
  SELECT id, email, role FROM users; -- Should show ONLY real accounts
  ```
- [ ] Remove `docs/VERDANT-LOGIN-CREDENTIALS.md` test credentials section
- [ ] Create new credentials doc with ONLY production accounts

### 0.1 CREATE REAL VERIFIED ADMIN ACCOUNT

- [ ] Create single Admin with **YOUR REAL EMAIL**:
  ```sql
  INSERT INTO users (
      first_name, last_name, email, password, role,
      email_verified_at, status, created_at
  ) VALUES (
      'System', 'Administrator',
      'christolabiyi35@gmail.com',
      '$2y$12$[BCRYPT_HASH_OF_Verdant2025!]',
      'admin',
      NOW(), -- Pre-verified (Admin only exception)
      'active',
      NOW()
  );
  ```
- [ ] **ONLY THIS ADMIN** is pre-verified — all others must verify via email+OTP
- [ ] Update `includes/config.php`:
  ```php
  define('ADMIN_EMAIL', 'christolabiyi35@gmail.com');
  define('ADMIN_PROTECTED', true); // Cannot be deleted/modified via UI
  ```

---

## 🏠 SECURE HOMEPAGE ARCHITECTURE — NO ROLE EXPOSURE

### 1. HOMEPAGE SECURITY LOCKDOWN (index.php)

- [ ] **REMOVE** all role orbs/cards displaying user types
- [ ] **REMOVE** all direct links to admin/teacher/student dashboards
- [ ] **REMOVE** role selection dropdown or any role enumeration
- [ ] **REMOVE** "Quick Access" sections exposing internal pages
- [ ] New homepage structure:
  ```
  ┌─────────────────────────────────────────────────────────────┐
  │                    VERDANT SMS v3.0                         │
  │         "Empowering Nigerian Education Excellence"          │
  ├─────────────────────────────────────────────────────────────┤
  │  [Hero Banner - Animated Cyberpunk/Nature Theme]            │
  │                                                             │
  │  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐        │
  │  │  About  │  │Features │  │  Demo   │  │ Contact │        │
  │  └─────────┘  └─────────┘  └─────────┘  └─────────┘        │
  │                                                             │
  │           [ LOGIN ]    [ STUDENT REGISTRATION ]             │
  │                                                             │
  │  Footer: © 2025 Verdant SMS | Privacy | Terms              │
  └─────────────────────────────────────────────────────────────┘
  ```

### 2. VISITOR PAGES STRUCTURE — NEW FOLDER

- [ ] Create `visitor/` directory with public-facing pages:

  ```
  visitor/
  ├── about.php           — School system overview, mission, values
  ├── features.php        — Module highlights (NO admin details)
  ├── demo-request.php    — Demo access request form
  ├── contact.php         — Contact form + WhatsApp link
  ├── faq.php             — Frequently Asked Questions
  ├── blog.php            — News/announcements (public only)
  ├── gallery.php         — School photos showcase
  ├── testimonials.php    — Parent/student testimonials
  ├── pricing.php         — Fee structure overview (generic)
  └── privacy-policy.php  — GDPR/NDPR compliance page
  ```

- [ ] Each visitor page includes:
  ```php
  <?php
  // NO session_start() required for public pages
  require_once '../includes/config.php';
  // NO auth requirements
  $page_title = 'About Us - Verdant SMS';
  ?>
  ```

### 3. VISITOR NAVIGATION COMPONENT

- [ ] Create `includes/visitor-nav.php`:
  ```php
  <header class="visitor-header">
      <nav class="visitor-nav">
          <a href="/" class="logo">Verdant SMS</a>
          <ul class="nav-links">
              <li><a href="/visitor/about.php">About</a></li>
              <li><a href="/visitor/features.php">Features</a></li>
              <li><a href="/visitor/demo-request.php">Request Demo</a></li>
              <li><a href="/visitor/faq.php">FAQ</a></li>
              <li><a href="/visitor/contact.php">Contact</a></li>
          </ul>
          <div class="nav-auth">
              <a href="/login.php" class="btn-login">Login</a>
              <a href="/register.php" class="btn-register">Student Registration</a>
          </div>
      </nav>
  </header>
  ```
- [ ] Mobile hamburger menu for visitor pages
- [ ] Theme switcher available on visitor pages

### 4. SECURE LOGIN PAGE ENHANCEMENTS

- [ ] `login.php` — NO role hints or user enumeration:
  ```php
  // BAD: "Invalid email" vs "Invalid password" (reveals email exists)
  // GOOD: "Invalid credentials" (no information leakage)
  $error = 'Invalid email or password. Please try again.';
  ```
- [ ] Rate limiting: 5 failed attempts → 15-minute lockout
- [ ] CAPTCHA after 3 failed attempts (reCAPTCHA v3)
- [ ] No "Forgot Password" link until email verified
- [ ] Honeypot field for bot detection

---

## 🔐 BIOMETRIC-FIRST AUTHENTICATION SYSTEM

### 5. WEBAUTHN IMPLEMENTATION — BIOMETRIC PRIORITY

- [ ] Create `webauthn_credentials` table:

  ```sql
  CREATE TABLE webauthn_credentials (
      id INT PRIMARY KEY AUTO_INCREMENT,
      user_id INT NOT NULL,
      credential_id VARBINARY(255) NOT NULL,
      public_key BLOB NOT NULL,
      credential_type ENUM('biometric', 'passkey', 'security_key') NOT NULL,
      device_name VARCHAR(100),
      last_used_at TIMESTAMP NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
      UNIQUE KEY (credential_id)
  );
  ```

- [ ] Create `includes/webauthn.php`:

  ```php
  class WebAuthnManager {
      public function detectCapabilities(): array {
          // Returns: ['biometric' => true/false, 'passkey' => true/false]
      }

      public function registerBiometric(int $userId): array {
          // Prioritize platform authenticator (fingerprint/Face ID)
      }

      public function registerPasskey(int $userId): array {
          // Fallback to roaming authenticator (security key/phone)
      }

      public function authenticate(string $credentialId): ?int {
          // Returns user_id on success, null on failure
      }
  }
  ```

### 6. BIOMETRIC REGISTRATION FLOW (ALL ROLES)

- [ ] In every user profile (`*/profile.php`):

  ```html
  <section class="security-settings">
    <h3>🔐 Passwordless Login Setup</h3>

    <!-- Step 1: Biometric (Priority) -->
    <div id="biometric-section">
      <button id="registerBiometric" class="btn-primary">
        <i class="fas fa-fingerprint"></i> Register Fingerprint / Face ID
      </button>
      <p class="hint">Recommended for fastest, most secure login</p>
    </div>

    <!-- Step 2: Passkey (Fallback) -->
    <div id="passkey-section" style="display: none;">
      <button id="registerPasskey" class="btn-secondary">
        <i class="fas fa-key"></i> Register Security Key / Phone
      </button>
      <p class="hint">Use if your device doesn't support biometrics</p>
    </div>

    <!-- Registered Methods -->
    <div id="registered-methods">
      <!-- Dynamic list of registered authenticators -->
    </div>
  </section>
  ```

- [ ] JavaScript detection logic:

  ```javascript
  async function checkBiometricSupport() {
    if (!window.PublicKeyCredential) {
      showPasskeyOnly();
      return;
    }

    const available =
      await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
    if (available) {
      showBiometricPriority();
    } else {
      showPasskeyFallback();
    }
  }
  ```

### 7. LOGIN FLOW — BIOMETRIC → PASSKEY → OTP → PASSWORD

- [ ] Update `login.php` authentication cascade:

  ```
  1. Check for registered WebAuthn credentials
     ├── Biometric available? → Prompt fingerprint/Face ID
     ├── Passkey available? → Prompt security key
     └── Neither? → Show email/password form

  2. Email/Password fallback:
     ├── Validate credentials
     ├── Check if email verified
     ├── Send OTP to email
     └── Verify OTP → Login success

  3. Account locked? → Show lockout message + support contact
  ```

- [ ] Create `assets/js/webauthn-login.js`:

  ```javascript
  class BiometricLogin {
    async attemptBiometric(email) {
      // 1. Fetch challenge from server
      // 2. Call navigator.credentials.get() with biometric preference
      // 3. Send assertion to server
      // 4. On success → redirect to dashboard
      // 5. On failure → show passkey option
    }

    async attemptPasskey(email) {
      // Similar flow but without platform authenticator requirement
    }

    showPasswordFallback() {
      // Display traditional login form
    }
  }
  ```

### 8. ADMIN BIOMETRIC ENFORCEMENT

- [ ] Admin setting to enforce biometric for roles:
  ```sql
  CREATE TABLE biometric_policies (
      id INT PRIMARY KEY AUTO_INCREMENT,
      role VARCHAR(50) NOT NULL,
      require_biometric BOOLEAN DEFAULT FALSE,
      require_passkey BOOLEAN DEFAULT FALSE,
      allow_password_fallback BOOLEAN DEFAULT TRUE,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      UNIQUE KEY (role)
  );
  ```
- [ ] Admin panel: `admin/security-policies.php`
  - Toggle biometric requirement per role
  - Set grace period for registration
  - View users without biometric registered

---

## 📧 EMAIL + OTP VERIFICATION SYSTEM

### 9. DATABASE SCHEMA FOR VERIFICATION

- [ ] Add columns to `users` table:
  ```sql
  ALTER TABLE users ADD COLUMN email_verified_at TIMESTAMP NULL;
  ALTER TABLE users ADD COLUMN otp_code VARCHAR(6) NULL;
  ALTER TABLE users ADD COLUMN otp_expires_at TIMESTAMP NULL;
  ALTER TABLE users ADD COLUMN verification_token VARCHAR(64) NULL;
  ALTER TABLE users ADD COLUMN verification_expires_at TIMESTAMP NULL;
  ALTER TABLE users ADD COLUMN failed_login_attempts INT DEFAULT 0;
  ALTER TABLE users ADD COLUMN locked_until TIMESTAMP NULL;
  ```

### 10. VERIFICATION FLOW — ALL NEW ACCOUNTS

- [ ] On account creation (any role):

  ```php
  function createAccount($data) {
      // 1. Insert user with status='pending', email_verified_at=NULL
      // 2. Generate OTP (6 digits) + verification token
      // 3. Send email with:
      //    - OTP code
      //    - Verification link: verify.php?token=xxx
      //    - Expires in 10 minutes (OTP) / 24 hours (link)
      // 4. Return user_id
  }
  ```

- [ ] Create `verify.php`:

  ```php
  // Accepts: ?token=xxx OR POST otp_code
  // Validates against users table
  // On success: email_verified_at = NOW(), status = 'active'
  // Redirect to login with success message
  ```

- [ ] Create `resend-otp.php`:
  ```php
  // Rate limited: 1 resend per 60 seconds
  // Max 5 resends per email per day
  // Generates new OTP, invalidates old one
  ```

### 11. LOGIN BLOCKED UNTIL VERIFIED

- [ ] Update `login.php`:

  ```php
  $user = db()->fetchRow("SELECT * FROM users WHERE email = ?", [$email]);

  if ($user && $user['email_verified_at'] === null) {
      // Exception: Admin account (pre-verified)
      if ($user['role'] !== 'admin') {
          $_SESSION['unverified_email'] = $email;
          redirect('verify.php?pending=1');
      }
  }
  ```

---

## 🎓 STUDENT REGISTRATION — SECURE + VERIFIED

### 12. REGISTER.PHP HARDENING

- [ ] Remove ALL role selection UI
- [ ] Hardcode role as 'student':

  ```php
  $role = 'student'; // NEVER from user input
  ```

- [ ] Entrance Exam ID validation:

  ```php
  $exam_id = trim($_POST['entrance_exam_id']);
  $valid = db()->fetchRow(
      "SELECT * FROM entrance_exam_results
       WHERE entrance_id = ? AND passed = 1 AND used = 0",
      [$exam_id]
  );
  if (!$valid) {
      $error = 'Invalid or already used Entrance Exam ID';
  }
  ```

- [ ] Full registration flow:
  ```
  1. Validate Entrance Exam ID
  2. Validate all form fields
  3. Create user with status='pending'
  4. Mark entrance_id as used
  5. Send verification email + OTP
  6. Show: "Check your email to verify. Admin will approve your account."
  7. Admin approves → Email notification → User verifies → Login enabled
  ```

---

## 🛡️ VISITOR PAGE IMPLEMENTATIONS

### 13. ABOUT PAGE (visitor/about.php)

- [ ] Content sections:
  - School history/mission
  - Core values
  - Leadership team (names only, no contact)
  - Accreditations
  - Statistics (students, teachers, pass rates)

### 14. FEATURES PAGE (visitor/features.php)

- [ ] Module highlights (NO admin-specific details):
  ```
  ✅ Student Information System
  ✅ Online Admission & Enrollment
  ✅ Attendance Management
  ✅ Grade & Report Cards
  ✅ Fee Payment (Paystack/Flutterwave)
  ✅ Library Management
  ✅ Transport Tracking
  ✅ Parent Portal
  ✅ SMS & Email Notifications
  ✅ 8 Beautiful Themes
  ```
- [ ] NO mention of admin panel, database structure, or technical details

### 15. DEMO REQUEST PAGE (visitor/demo-request.php)

- [ ] Form fields:
  - School Name
  - Contact Person Name
  - Email
  - Phone (Nigerian format validation)
  - Number of Students (dropdown ranges)
  - Message/Requirements
- [ ] On submit:
  - Store in `demo_requests` table
  - Send notification to Admin email
  - Show confirmation message
- [ ] Rate limit: 1 request per email per 24 hours

### 16. CONTACT PAGE (visitor/contact.php)

- [ ] Contact form (rate limited)
- [ ] Display:
  ```
  📧 Email: christolabiyi35@gmail.com
  📱 Phone: +234 816 771 4860
  💬 WhatsApp: wa.me/2348167714860
  ```
- [ ] Google Maps embed (optional)
- [ ] Office hours

### 17. FAQ PAGE (visitor/faq.php)

- [ ] Accordion-style Q&A:

  ```
  Q: How do I register my child?
  A: Students must pass the entrance exam first...

  Q: What payment methods are accepted?
  A: We accept Paystack, Flutterwave, bank transfer...

  Q: Can I access the system on mobile?
  A: Yes, Verdant SMS is a Progressive Web App...
  ```

### 18. PRIVACY POLICY PAGE (visitor/privacy-policy.php)

- [ ] NDPR (Nigeria Data Protection Regulation) compliant
- [ ] Sections:
  - Data we collect
  - How we use data
  - Data retention
  - Your rights
  - Contact for data requests

---

## 🎨 ALL 8 THEMES — UNIVERSAL COMPATIBILITY

### 19. THEME VERIFICATION CHECKLIST

- [ ] **Cyberpunk** — Neon gradients, dark background, glowing elements
- [ ] **Nature** — Green tones, organic shapes, earth colors
- [ ] **Matrix** — Black/green, falling code effect
- [ ] **Ocean** — Blue gradients, wave animations
- [ ] **Sunset** — Orange/pink gradients, warm tones
- [ ] **Purple** — Purple/violet gradients, royal aesthetic
- [ ] **Minimal** — Clean white, subtle shadows, professional
- [ ] **High-Contrast** — Accessibility-focused, stark colors

- [ ] Every page tested in all 8 themes:
  - [ ] index.php (homepage)
  - [ ] visitor/\* (all visitor pages)
  - [ ] login.php
  - [ ] register.php
  - [ ] verify.php
  - [ ] admin/\* (all admin pages)
  - [ ] teacher/\* (all teacher pages)
  - [ ] student/\* (all student pages)
  - [ ] parent/\* (all parent pages)

### 20. THEME SWITCHER ON VISITOR PAGES

- [ ] Add theme toggle to visitor-nav.php:
  ```html
  <div class="theme-switcher">
    <button id="themeToggle" aria-label="Switch Theme">
      <i class="fas fa-palette"></i>
    </button>
    <div class="theme-dropdown" id="themeDropdown">
      <button data-theme="cyberpunk">Cyberpunk</button>
      <button data-theme="nature">Nature</button>
      <button data-theme="matrix">Matrix</button>
      <button data-theme="ocean">Ocean</button>
      <button data-theme="sunset">Sunset</button>
      <button data-theme="purple">Purple</button>
      <button data-theme="minimal">Minimal</button>
      <button data-theme="high-contrast">High Contrast</button>
    </div>
  </div>
  ```
- [ ] Save preference to localStorage (no login required)

### 20.1 SIDEBAR SCROLLING FIX — CRITICAL CSS PATTERN

- [ ] Ensure all sidebars use `position: fixed` with proper structure:

  ```css
  /* Sidebar must be fixed, not absolute */
  .cyber-sidebar,
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 260px;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1000;
  }

  /* Main content must have left margin to avoid overlap */
  .cyber-main,
  .main-content {
    margin-left: 260px;
    min-height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
  }

  /* Body and HTML must allow scrolling */
  html {
    overflow-y: scroll;
    overflow-x: hidden;
    scroll-behavior: smooth;
  }

  body {
    overflow-y: auto;
    overflow-x: hidden;
    min-height: 100vh;
  }

  /* Mobile: Sidebar overlay mode */
  @media (max-width: 1024px) {
    .cyber-sidebar,
    .sidebar {
      transform: translateX(-100%);
      transition: transform 0.3s ease;
    }

    .cyber-sidebar.active,
    .sidebar.active {
      transform: translateX(0);
    }

    .cyber-main,
    .main-content {
      margin-left: 0;
      width: 100%;
    }
  }
  ```

- [ ] Apply this pattern to ALL theme CSS files:
  - `assets/css/cyberpunk-ui.css`
  - `assets/css/nature-theme.css`
  - `assets/css/matrix-theme.css`
  - `assets/css/ocean-theme.css`
  - `assets/css/sunset-theme.css`
  - `assets/css/purple-theme.css`
  - `assets/css/minimal-theme.css`
  - `assets/css/high-contrast-theme.css`
- [ ] Verify sidebar scrolls independently from main content
- [ ] Verify main content scrolls without affecting sidebar position

---

## 🐛 ZERO ERRORS — TOTAL PERFECTION

### 21. PHP ERROR FIXES

- [ ] Fix `Database::fetchColumn()` missing method:

  ```php
  // In includes/database.php
  public function fetchColumn(string $sql, array $params = [], int $column = 0) {
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute($params);
      return $stmt->fetchColumn($column);
  }
  ```

- [ ] Fix undefined variable errors in all files
- [ ] Fix deprecated function warnings
- [ ] Ensure all `require_once` paths are correct

### 22. JAVASCRIPT ERROR FIXES

- [ ] Console should show ZERO errors on every page
- [ ] Fix undefined function calls
- [ ] Fix null reference errors
- [ ] Ensure all event listeners attached after DOM ready

### 23. CSS FIXES

- [ ] No layout breaks on any screen size
- [ ] Scrolling works everywhere (no `overflow: hidden` blocking)
- [ ] All animations smooth (60fps)
- [ ] Print styles for report cards

---

## 📦 PRODUCTION DEPLOYMENT

### 24. PRE-LAUNCH SECURITY AUDIT

- [ ] Remove ALL test accounts from database
- [ ] Remove ALL hardcoded credentials from code
- [ ] Verify `.env` is in `.gitignore`
- [ ] Scan for exposed secrets:
  ```bash
  grep -r "password" --include="*.php" . | grep -v "password'" | grep -v "getenv"
  grep -r "@gmail.com" --include="*.php" .
  ```
- [ ] Enable HTTPS only (force redirect)
- [ ] Set secure session cookies:
  ```php
  ini_set('session.cookie_secure', 1);
  ini_set('session.cookie_httponly', 1);
  ini_set('session.cookie_samesite', 'Strict');
  ```

### 25. FINAL .ENV CONFIGURATION

```env
# Production Environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_HOST=localhost
DB_SOCKET=/opt/lampp/var/mysql/mysql.sock
DB_DATABASE=attendance_system
DB_USERNAME=verdant_user
DB_PASSWORD=[SECURE_RANDOM_PASSWORD]

# Email (Real Credentials)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=christolabiyi35@gmail.com
SMTP_PASSWORD=[APP_SPECIFIC_PASSWORD]
SMTP_FROM_EMAIL=christolabiyi35@gmail.com
SMTP_FROM_NAME=Verdant School Management System

# Contact
CONTACT_EMAIL=christolabiyi35@gmail.com
CONTACT_PHONE=+2348167714860
WHATSAPP_NUMBER=2348167714860

# Payment Gateways
PAYSTACK_PUBLIC_KEY=[YOUR_KEY]
PAYSTACK_SECRET_KEY=[YOUR_KEY]
FLUTTERWAVE_PUBLIC_KEY=[YOUR_KEY]
FLUTTERWAVE_SECRET_KEY=[YOUR_KEY]

# Security
OTP_EXPIRY_MINUTES=10
SESSION_LIFETIME=30
MAX_LOGIN_ATTEMPTS=5
LOCKOUT_DURATION=15
```

### 26. FINAL GIT COMMANDS

```bash
# Verify no secrets in staged files
git diff --cached --name-only | xargs grep -l "password\|secret\|key" 2>/dev/null

# Add all changes
git add .

# Commit with comprehensive message
git commit -m "🚀 Verdant SMS v3.0 FINAL SECURE LAUNCH

✅ Security Overhaul:
- Removed ALL 25 test accounts
- Real verified Admin: christolabiyi35@gmail.com
- Biometric-first authentication (WebAuthn)
- Passkey fallback for non-biometric devices
- Email + OTP verification for all accounts
- Rate limiting on login (5 attempts/15min lockout)
- No role exposure on homepage

✅ Secure Homepage Architecture:
- New visitor/ directory with public pages
- About, Features, Demo Request, FAQ, Contact, Privacy
- NO admin/role links exposed
- Theme switcher for visitors

✅ Authentication Flow:
- Biometric → Passkey → OTP → Password cascade
- Admin biometric enforcement policies
- Session security hardened

✅ All 8 Themes Verified:
- Cyberpunk, Nature, Matrix, Ocean, Sunset, Purple, Minimal, High-Contrast
- Every page tested in every theme

✅ Zero Errors:
- Fixed Database::fetchColumn()
- Zero PHP/JS/Console errors
- All forms functional

Ready for production deployment."

# Push to remote
git push origin main --force
```

### 27. GITHUB RELEASE

```bash
# Create annotated tag
git tag -a v3.0-evergreen -m "Verdant SMS v3.0 Evergreen - Secure Production Release"

# Push tag
git push origin v3.0-evergreen
```

Then on GitHub:

1. Create Release from tag `v3.0-evergreen`
2. Title: "Verdant SMS v3.0 — Secure, Verified, Biometric-Ready"
3. Description: Copy commit message above
4. Attach:
   - `verdant-sms-v3.0.zip` (source without .env)
   - `INSTALL_GUIDE.pdf`
   - Screenshots of all themes

---

## 📋 PRODUCTION CREDENTIALS DOCUMENT

### 28. CREATE `docs/PRODUCTION-CREDENTIALS.md`

```markdown
# Verdant SMS v3.0 — Production Credentials

## ADMIN ACCOUNT (ONLY ONE)

- **Email:** christolabiyi35@gmail.com
- **Password:** [Set on first login via password reset]
- **Role:** admin (supreme access)
- **Status:** Pre-verified, Active

## HOW TO CREATE OTHER ACCOUNTS

1. Login as Admin
2. Go to Admin → Account Management
3. Use "Create Account" tab
4. Fill in real email address
5. Select role
6. User receives verification email
7. User verifies → Account active

## BIOMETRIC SETUP

1. User logs in with password first time
2. Go to Profile → Security Settings
3. Click "Register Fingerprint / Face ID"
4. Follow browser prompts
5. Future logins: Biometric only

## STUDENT REGISTRATION

1. Student passes Entrance Exam
2. Receives Entrance ID (VERDANT-EXAM-XXXXXXXX)
3. Goes to /register.php
4. Enters Entrance ID + personal details
5. Receives verification email
6. Admin approves in Account Management
7. Student verifies email → Can login

## SUPPORT CONTACT

- Email: christolabiyi35@gmail.com
- WhatsApp: +234 816 771 4860
```

---

## 🎯 EXECUTION SUMMARY

| Priority    | Task                                | Status |
| ----------- | ----------------------------------- | ------ |
| 🔴 CRITICAL | Delete all test accounts            | [ ]    |
| 🔴 CRITICAL | Create real Admin account           | [ ]    |
| 🔴 CRITICAL | Secure homepage (remove role links) | [ ]    |
| 🔴 CRITICAL | Create visitor pages                | [ ]    |
| 🟠 HIGH     | Implement WebAuthn biometric login  | [ ]    |
| 🟠 HIGH     | Email + OTP verification system     | [ ]    |
| 🟠 HIGH     | Fix Database::fetchColumn()         | [ ]    |
| 🟡 MEDIUM   | Test all 8 themes                   | [ ]    |
| 🟡 MEDIUM   | Rate limiting on login              | [ ]    |
| 🟢 LOW      | Privacy policy page                 | [ ]    |
| 🟢 LOW      | Demo request form                   | [ ]    |

---

**Verdant SMS v3.0 — Built for Security, Designed for Nigeria, Ready for the World** 🇳🇬🔐

**13 December 2025 — FINAL SECURE LAUNCH**

---

<!--
╔══════════════════════════════════════════════════════════════════════════════╗
║  VERDANT SCHOOL MANAGEMENT SYSTEM v3.0 — MASTER TODO CHECKLIST               ║
║  Status: PRODUCTION-READY | Last Updated: 12 December 2025                   ║
║  Maintainer: Chrinux-AI | License: Proprietary                               ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->

**Verdant School Management System v3.0 — Chrinux-AI**
https://github.com/Chrinux-AI/SMS.git
**Date:** 12 December 2025 — OFFICIAL GLOBAL LAUNCH

IMMEDIATELY. THIS IS THE DEFINITIVE FINAL ONE.\*\*

> **⚠️ CRITICAL**: This is the FINAL, AUTHORITATIVE checklist. All previous TODO files are OBSOLETE.
> **GitHub Copilot**: Execute each section in order. Do NOT skip any task.

---

## ✅ COMPLETED

### 1. ROLE SYSTEM — ADMIN IS GOD ✅

- [x] Only **ONE** Admin account exists (`admin@verdant.edu`)
- [x] SuperAdmin/Owner folders completely **DELETED**
- [x] All other 24 roles created **only** by Admin
- [x] Admin has ALL privileges

### 2. ENTRANCE EXAMINATION MODULE ✅

- [x] `exam-register.php` — Public exam sign-up
- [x] `student/exam.php` — Timed MCQ with auto-grading
- [x] `admin/exam-management.php` — Full exam admin panel
- [x] Auto-generates Entrance ID on pass: `VERDANT-EXAM-XXXXXXXX`
- [x] Database tables created (5 tables)

### 3. register.php — STUDENT-ONLY + ENTRANCE ID ✅

- [x] Only "Student" role can self-register
- [x] Requires valid Entrance Exam ID (verified against DB)
- [x] All registrations go to "pending" for Admin approval
- [x] Cyberpunk UI with animated grid

### 4. ADMIN ACCOUNT MANAGEMENT ✅

- [x] `admin/account-management.php` — Create any role
- [x] Pending approvals tab
- [x] All users management
- [x] AI Bulk Registration section

### 5. ALL 8 THEMES — WORKING ✅

- [x] Cyberpunk, Nature, Matrix, Ocean, Sunset, Purple, Minimal, High-Contrast
- [x] All new pages styled correctly

### 6. ZERO ERRORS ✅

- [x] All PHP syntax validated
- [x] Database schema applied
- [x] All forms functional

---

## 🚀 IN PROGRESS — EXECUTING NOW

### 7. EMAIL + OTP VERIFICATION (ALL USERS)

- [ ] Add `email_verified_at`, `otp_code`, `otp_expires_at` to `users` table
- [ ] After any account creation → send:
      • Verification link (`verify.php?token=...`)
      • 6-digit OTP via email
- [ ] `verify.php` validates token or OTP → marks verified
- [ ] Login blocked until verified (Admin exempt)
- [ ] "Resend OTP" button on login

### 8. BIOMETRIC / PASSKEY (WEB AUTHN) LOGIN — EVERY ROLE

- [ ] Create `webauthn_credentials` table
- [ ] In every user profile → "Register Fingerprint / Face ID / Passkey" button
- [ ] Uses WebAuthn API (native fingerprint, Face ID, Windows Hello, Android)
- [ ] Once registered → login with biometrics (no password needed)
- [ ] Fallback: password + OTP always available
- [ ] Admin can enforce biometric for any role

---

## 🔐 ADMIN CREDENTIALS (ONLY ONE)

```
ADMIN → admin@verdant.edu → Verdant2025!
(All other accounts created by Admin)
```

---

## 📦 FINAL PUSH COMMANDS

```bash
git add .
git commit -m "Verdant SMS v3.0 Evergreen — Email+OTP + Biometric Login + Admin-Only + Perfect UI"
git push origin master
```

Then create GitHub Release `v3.0-evergreen`

---

**Verdant v3.0 Evergreen — OFFICIALLY LIVE & IMMORTAL**
**12 December 2025**

---

## 🔧 ARCHITECTURE DECISIONS — ADMIN SUPREMACY

### 9. ADMIN IS THE ONLY GOD — NO SUPERADMIN, NO EXCEPTIONS

- [ ] **Delete** `superadmin/` and `owner/` folders completely
- [ ] Remove SuperAdmin, Owner roles from database `roles` table
- [ ] Remove all references to SuperAdmin/Owner in `includes/*-nav.php`
- [ ] Only ONE Admin account forever: `admin@verdant.edu`
- [ ] Admin has **ALL** privileges:
  - Approve/Decline pending registrations
  - Create accounts for ANY role
  - Suspend/Delete any user
  - Access every module
  - Manage all system settings
  - Configure email/SMS notifications

### CONTACT & COMMUNICATION SETTINGS

- [ ] **Primary Contact Email:** `christolabiyi35@gmail.com`
- [ ] **Primary Contact Phone:** `+2348167714860`
- [ ] **OTP/Verification Sender Email:** `christolabiyi35@gmail.com`
- [ ] Update `.env` file with:
  ```env
  SMTP_FROM_EMAIL=christolabiyi35@gmail.com
  SMTP_FROM_NAME=Verdant School Management System
  CONTACT_EMAIL=christolabiyi35@gmail.com
  CONTACT_PHONE=+2348167714860
  ```
- [x] Update `includes/config.php`:

  ```php
  define('SYSTEM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: 'christolabiyi35@gmail.com');
  define('CONTACT_EMAIL', getenv('CONTACT_EMAIL') ?: 'christolabiyi35@gmail.com');
  define('CONTACT_PHONE', getenv('CONTACT_PHONE') ?: '+2348167714860');
  define('WHATSAPP_LINK', 'https://wa.me/2348167714860');
  define('SYSTEM_NAME', 'Verdant School Management System');
  define('OTP_EXPIRY_MINUTES', 10);
  define('VERIFICATION_TOKEN_EXPIRY_HOURS', 24);
  define('COUNTRY', 'Nigeria');
  define('CURRENCY', 'NGN');
  define('CURRENCY_SYMBOL', '₦');
  define('TIMEZONE', 'Africa/Lagos');
  define('DATE_FORMAT', 'd/m/Y');
  define('ACADEMIC_CALENDAR', 'Nigerian'); // 3 terms: First, Second, Third
  ```

### 15. NIGERIAN CLASS/GRADE SYSTEM IMPLEMENTATION

- [ ] Implement Nigerian education class levels (not American grades):

  ```
  PRIMARY SECTION:
  - Primary 1 (P1)
  - Primary 2 (P2)
  - Primary 3 (P3)
  - Primary 4 (P4)
  - Primary 5 (P5)
  - Primary 6 (P6)

  JUNIOR SECONDARY SECTION:
  - JSS 1 (Junior Secondary School 1)
  - JSS 2 (Junior Secondary School 2)
  - JSS 3 (Junior Secondary School 3)

  SENIOR SECONDARY SECTION:
  - SSS 1 (Senior Secondary School 1)
  - SSS 2 (Senior Secondary School 2)
  - SSS 3 (Senior Secondary School 3)
  ```

- [ ] Update `classes` table schema:
  ```sql
  CREATE TABLE classes (
      id INT PRIMARY KEY AUTO_INCREMENT,
      name VARCHAR(50), -- 'Primary 1', 'JSS 1', 'SSS 3'
      short_code VARCHAR(10), -- 'P1', 'JSS1', 'SSS3'
      section ENUM('primary', 'junior_secondary', 'senior_secondary'),
      level INT, -- 1-6 for Primary, 1-3 for JSS/SSS
      arm VARCHAR(10) DEFAULT NULL, -- 'A', 'B', 'C' for multiple streams
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );
  ```
- [ ] Seed default Nigerian classes in database
- [ ] Update all class dropdowns across modules (registration, enrollment, grading)
- [ ] Update report card templates for Nigerian format

### 16. NIGERIAN GRADING SYSTEM

- [ ] Implement Nigerian grading scale:
  ```
  A1 = 75-100 (Excellent)
  B2 = 70-74 (Very Good)
  B3 = 65-69 (Good)
  C4 = 60-64 (Credit)
  C5 = 55-59 (Credit)
  C6 = 50-54 (Credit)
  D7 = 45-49 (Pass)
  E8 = 40-44 (Pass)
  F9 = 0-39 (Fail)
  ```
- [ ] Create `grading_scales` table for customization
- [ ] Update report card calculations for Nigerian format
- [ ] Include Position in Class, Class Average, Term Summary

### 17. NIGERIAN TERM/SESSION STRUCTURE

- [ ] Implement 3-term academic calendar:
  ```
  First Term: September - December
  Second Term: January - April
  Third Term: May - July
  ```
- [ ] Create `academic_sessions` table:
  ```sql
  CREATE TABLE academic_sessions (
      id INT PRIMARY KEY AUTO_INCREMENT,
      name VARCHAR(50), -- '2024/2025'
      start_date DATE,
      end_date DATE,
      is_current BOOLEAN DEFAULT FALSE
  );
  ```
- [ ] Create `academic_terms` table:
  ```sql
  CREATE TABLE academic_terms (
      id INT PRIMARY KEY AUTO_INCREMENT,
      session_id INT,
      term_name ENUM('First Term', 'Second Term', 'Third Term'),
      start_date DATE,
      end_date DATE,
      is_current BOOLEAN DEFAULT FALSE,
      FOREIGN KEY (session_id) REFERENCES academic_sessions(id)
  );
  ```
- [ ] All modules reference current term/session

### 18. NIGERIAN SUBJECT STRUCTURE

- [ ] Implement standard Nigerian curriculum subjects:

  ```
  PRIMARY:
  - English Language, Mathematics, Basic Science
  - Social Studies, Nigerian Languages (Yoruba/Igbo/Hausa)
  - Christian/Islamic Religious Studies, Agricultural Science
  - Physical & Health Education, Creative Arts
  - Computer Studies, Music, Fine Arts
  JSS:
  - English, Mathematics, Basic Science, Basic Technology
  - Social Studies, Civic Education, Business Studies
  - Agricultural Science, Home Economics, PHE
  - Nigerian Language, CRS/IRS, French (optional)

  SSS:
  - English, Mathematics, Physics, Chemistry, Biology
  - Economics, Government, Literature, Geography
  - Further Mathematics, Agricultural Science, Commerce
  - Civic Education, Data Processing, Technical Drawing
  ```

- [ ] Subject categorization: Core vs Elective
- [ ] Science/Arts/Commercial track for SSS

### 19. WAEC/NECO INTEGRATION PREPARATION

- [ ] Create `external_exams` table for WAEC/NECO/JAMB records
- [ ] Student profile includes external exam results section
- [ ] Support for uploading result slips (PDF/image)

### 20. NIGERIAN SCHOOL FEES STRUCTURE

- [ ] Fee categories:
  ```
  - Tuition Fee (per term)
  - Development Levy (annual)
  - PTA Levy (annual)
  - Sports/Games Fee
  - Library Fee
  - Laboratory Fee (JSS/SSS)
  - WAEC/NECO Fee (SSS 3 only)
  - Uniform Fee
  - ID Card Fee
  ```
- [ ] Different fee structures per section (Primary/JSS/SSS)
- [ ] Payment plans: Full payment, Installments
- [ ] Generate fee invoices with Nigerian bank details

```php
define('SYSTEM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: 'christolabiyi35@gmail.com');
define('CONTACT_EMAIL', getenv('CONTACT_EMAIL') ?: 'christolabiyi35@gmail.com');
define('CONTACT_PHONE', getenv('CONTACT_PHONE') ?: '+2348167714860');
```

- [ ] Configure PHPMailer in `includes/functions.php`:
  - Set `$mail->setFrom('christolabiyi35@gmail.com', 'Verdant SMS')`
  - All OTP emails sent FROM this address
  - All verification links sent FROM this address
  - All password reset emails FROM this address
- [ ] Update footer/contact pages with:
  - Email: `christolabibi35@gmail.com`
  - Phone: `+2348167714860`
  - WhatsApp link: `https://wa.me/2348167714860`

### 10. register.php — STUDENT ROLE ONLY

- [ ] Remove role dropdown completely — hardcode `role = 'student'`
- [ ] Require valid Entrance Exam ID before form submission
- [ ] All registrations go to `status = 'pending'`
- [ ] Admin must approve before student can login
- [ ] Prevents malicious students registering as teachers/principals

### 11. ADMIN PANEL — ACCOUNT MANAGEMENT HUB

- [ ] `admin/account-management.php` — Single page for all account operations:
  - **Tab 1:** Pending Approvals (approve/decline student registrations)
  - **Tab 2:** All Users (view, edit, suspend, delete any user)
  - **Tab 3:** Create Account (Admin manually creates any role)
  - **Tab 4:** AI Bulk Registration (for parents/staff via Google Form)

### 12. AI-POWERED BULK REGISTRATION SYSTEM (GOOGLE FORM INTEGRATION)

- [ ] Create `admin/bulk-registration.php` — Admin-only page
- [ ] Admin inputs Google Form response spreadsheet link
- [ ] Admin sets registration window duration (e.g., 5 days)
- [ ] Store config in `bulk_registration_config` table:
  ```sql
  CREATE TABLE bulk_registration_config (
      id INT PRIMARY KEY AUTO_INCREMENT,
      google_sheet_url VARCHAR(500),
      start_date DATETIME,
      end_date DATETIME,
      target_roles JSON, -- ['parent', 'teacher', 'librarian', etc.]
      status ENUM('pending', 'processing', 'completed'),
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );
  ```
- [ ] After duration expires → AI integration triggers:
  - Fetches Google Sheet data via API
  - Extracts: Name, Phone, Email, Role, Children Names (for parents)
  - Creates accounts with random secure passwords
  - Sends welcome emails with credentials + OTP
- [ ] Roles supported for bulk creation:
  - `parent` (with linked children info)
  - `teacher`, `librarian`, `transport`, `hostel`, `canteen`
  - `nurse`, `counselor`, `accountant`, `admin-officer`
- [ ] **NOT for bulk:** `principal`, `vice-principal`, `class-teacher` (Admin creates manually)

### 13. GOOGLE FORM FIELDS REQUIRED

- [ ] Document required form fields for each role:

  ```
  PARENT FORM:
  - Full Name, Email, Phone
  - Child 1 Name, Child 1 Class
  - Child 2 Name, Child 2 Class (optional)
  - Relationship (Mother/Father/Guardian)

  STAFF FORM:
  - Full Name, Email, Phone
  - Role (dropdown: Teacher, Librarian, etc.)
  - Department (if applicable)
  - Qualifications
  ```

### 14. AI INTEGRATION — CRON JOB / SCHEDULED TASK

- [ ] Create `cron/process-bulk-registrations.php`
- [ ] Runs daily, checks `bulk_registration_config` for expired windows
- [ ] Uses Google Sheets API or CSV export to fetch data
- [ ] Validates data, creates users, logs errors
- [ ] Admin notified via email when processing complete

---

## 🎯 HIERARCHY ENFORCEMENT

```

```

         │

```
    └── Principal (created by Admin)
    └── Vice-Principal (created by Admin)
    └── Teachers (created by Admin or AI Bulk)
    └── Class Teachers (created by Admin)
    └── Support Staff (created by Admin or AI Bulk)
    └── Parents (created by AI Bulk via Google Form)
    └── Students (self-register → Admin approves)
```

**NO SUPERADMIN. NO OWNER. ADMIN IS SUPREME.**

---

## 🔄 ADDITIONAL FEATURES — PHASE 2

### 21. NIGERIAN PAYMENT GATEWAY INTEGRATION

- [ ] Integrate Paystack (primary Nigerian gateway):
  ```php
  // includes/paystack.php
  define('PAYSTACK_PUBLIC_KEY', getenv('PAYSTACK_PUBLIC_KEY'));
  define('PAYSTACK_SECRET_KEY', getenv('PAYSTACK_SECRET_KEY'));
  ```
- [ ] Integrate Flutterwave as backup gateway
- [ ] Support bank transfer with auto-verification
- [ ] Generate receipts with Nigerian tax format
- [ ] Parent portal: View fees, pay online, download receipts

### 22. SMS NOTIFICATION SYSTEM (NIGERIAN NETWORKS)

- [ ] Integrate with Nigerian SMS providers:
  - Termii (recommended)
  - Africa's Talking
  - Twilio (international fallback)
- [ ] SMS triggers:
  - Student absence notification to parents
  - Fee payment reminders
  - Exam schedule alerts
  - Emergency broadcasts
  - OTP for password reset
- [ ] Bulk SMS for announcements (Admin only)

### 23. PARENT PORTAL ENHANCEMENTS

- [ ] Real-time child attendance view
- [ ] View child's grades per term
- [ ] Download report cards (PDF)
- [ ] Fee payment history
- [ ] Communication with class teacher
- [ ] View school calendar/events
- [ ] Request meeting with teacher/counselor

### 24. TEACHER PORTAL ENHANCEMENTS

- [ ] Mark attendance (per subject or daily)
- [ ] Enter continuous assessment (CA) scores
- [ ] Enter exam scores
- [ ] Auto-calculate term totals
- [ ] Generate class reports
- [ ] View student profiles
- [ ] Communication with parents

### 25. CLASSROOM & SUBJECT ASSIGNMENT

- [ ] Assign class teacher to each class arm (e.g., JSS 2A → Mrs. Adeyemi)
- [ ] Assign subject teachers to classes
- [ ] Teacher workload management
- [ ] Substitute teacher assignment

### 41. STAFF HR & PAYROLL MODULE

- [ ] Staff profiles with employment details
- [ ] Salary structure (Basic, Allowances, Deductions)
- [ ] Monthly payroll processing
- [ ] Payslip generation (PDF)
- [ ] Tax calculation (PAYE - Nigerian tax)
- [ ] Pension deductions (contributory pension)
- [ ] Leave management (annual, sick, maternity)
- [ ] Staff attendance tracking
- [ ] Performance appraisal system
- [ ] Promotion history tracking

### 42. EXAMINATION MANAGEMENT

- [ ] Exam timetable creation
- [ ] Exam hall allocation
- [ ] Invigilator assignment
- [ ] Question paper management (encrypted storage)
- [ ] Online CBT module for internal exams
- [ ] Exam results compilation
- [ ] Grade moderation tools
- [ ] Result verification portal

### 43. STUDENT DISCIPLINE MODULE

- [ ] Incident reporting system
- [ ] Offense categories (minor, major, critical)
- [ ] Disciplinary action tracking
- [ ] Parent notification on incidents
- [ ] Suspension/expulsion workflow
- [ ] Behavior points system
- [ ] Counselor referral integration
- [ ] Rehabilitation progress tracking

### 44. HEALTH & MEDICAL RECORDS

- [ ] Student health profiles
- [ ] Immunization records
- [ ] Medical history tracking
- [ ] Clinic visit logs
- [ ] Medication administration log
- [ ] Emergency contact quick-dial
- [ ] Allergy alerts (display on student card)
- [ ] Health insurance details (NHIS)
- [ ] Nurse dashboard with daily stats

### 45. CANTEEN & MEAL MANAGEMENT

- [ ] Meal plan subscriptions
- [ ] Daily menu management
- [ ] Student meal credits/wallet
- [ ] QR code meal redemption
- [ ] Dietary restrictions tracking
- [ ] Meal consumption reports
- [ ] Vendor payment management
- [ ] Nutritional information display

### 46. EVENT & CALENDAR MANAGEMENT

- [ ] School calendar (public holidays, exams, events)
- [ ] Event creation with RSVP
- [ ] Parent permission slips (digital)
- [ ] Sports day management
- [ ] Cultural day planning
- [ ] Inter-house competitions
- [ ] External event registrations
- [ ] Automated event reminders

### 47. ALUMNI MANAGEMENT

- [ ] Alumni registration portal
- [ ] Graduation year tracking
- [ ] Alumni directory (searchable)
- [ ] Alumni events and reunions
- [ ] Donation/fundraising campaigns
- [ ] Success stories showcase
- [ ] Alumni mentorship program
- [ ] Job posting board for alumni

### 48. INVENTORY & ASSET MANAGEMENT

- [ ] School assets register
- [ ] Classroom equipment tracking
- [ ] Lab equipment inventory
- [ ] Furniture/fixture management
- [ ] Maintenance request system
- [ ] Asset depreciation tracking
- [ ] Procurement workflow
- [ ] Vendor management

### 49. ADMISSION MANAGEMENT

- [ ] Online admission forms
- [ ] Document upload (birth cert, photos, etc.)
- [ ] Application fee payment
- [ ] Application status tracking
- [ ] Admission interview scheduling
- [ ] Offer letter generation
- [ ] Admission acceptance workflow
- [ ] Wait-list management
- [ ] Class capacity monitoring

### 50. COMMUNICATION HUB

- [ ] Internal messaging system
- [ ] Broadcast announcements (by role/class)
- [ ] Email newsletter builder
- [ ] Notice board (digital)
- [ ] Emergency alert system
- [ ] Parent-teacher chat
- [ ] Complaint/suggestion box
- [ ] Survey/poll creation

### 51. E-LEARNING INTEGRATION

- [ ] Video lesson uploads
- [ ] Assignment submission portal
- [ ] Online quiz builder
- [ ] Discussion forums per class
- [ ] Resource library (notes, past questions)
- [ ] Live class integration (Zoom/Google Meet links)
- [ ] Progress tracking for online courses
- [ ] Certificate generation for completions

### 52. CERTIFICATE & DOCUMENT GENERATION

- [ ] Testimonial generation
- [ ] Transfer certificate
- [ ] Character certificate
- [ ] Bonafide certificate
- [ ] Fee clearance certificate
- [ ] ID card generation (with photo, barcode)
- [ ] Custom certificate templates
- [ ] Digital signature integration
- [ ] QR code verification for certificates

### 53. MULTI-BRANCH/CAMPUS SUPPORT

- [ ] Multiple school branches under one admin
- [ ] Branch-specific configurations
- [ ] Cross-branch student transfer
- [ ] Consolidated reports across branches
- [ ] Branch performance comparison
- [ ] Centralized fee structure with branch variations
- [ ] Lesson plan uploads

### 25. REPORT CARD GENERATION

- [ ] Nigerian report card template:
  ```
  ┌────────────────────────────────────────┐
  │     VERDANT SCHOOL - REPORT CARD       │
  │     Session: 2024/2025                 │
  │     Term: First Term                   │
  ├────────────────────────────────────────┤
  │ Student: John Doe                      │
  │ Class: JSS 2A                          │
  │ Position: 5th out of 45                │
  ├────────────────────────────────────────┤
  │ Subject    | CA | Exam | Total | Grade │
  │ English    | 28 |  58  |  86   |  A1   │
  │ Maths      | 25 |  52  |  77   |  A1   │
  │ ...        |    |      |       |       │
  ├────────────────────────────────────────┤
  │ Total: 650/800  Average: 81.25%        │
  │ Class Average: 65.4%                   │
  │ Principal's Remark: Excellent!         │
  └────────────────────────────────────────┘
  ```
- [ ] PDF export with school logo
- [ ] Bulk print for entire class
- [ ] Digital signature support

### 26. ATTENDANCE SYSTEM

- [ ] Daily attendance (class teacher)
- [ ] Subject-wise attendance (subject teacher)
- [ ] Biometric attendance integration (optional hardware)
- [ ] QR code check-in (PWA feature)
- [ ] Late arrival tracking
- [ ] Absence reason logging
- [ ] Auto-SMS to parents on absence
- [ ] Monthly attendance reports

### 27. LIBRARY MODULE (NIGERIAN CONTEXT)

- [ ] Book cataloging with ISBN
- [ ] Student library cards (virtual)
- [ ] Book issue/return tracking
- [ ] Overdue fine calculation (₦)
- [ ] Popular books analytics
- [ ] E-books section (PDF uploads)
- [ ] Past questions repository (WAEC/NECO/JAMB)

### 28. TRANSPORT MODULE

- [ ] Bus routes management
- [ ] Student-bus assignment
- [ ] Driver/conductor profiles
- [ ] Transport fee separate billing
- [ ] GPS tracking integration (future)
- [ ] Trip logs

### 29. HOSTEL MODULE

- [ ] Room/bed allocation
- [ ] Hostel fee management
- [ ] Hostel attendance (nightly check)
- [ ] Visitor log
- [ ] Meal preferences
- [ ] Hostel complaints system

### 30. TIMETABLE MANAGEMENT

- [ ] Class timetable generator
- [ ] Teacher timetable view
- [ ] Room allocation
- [ ] Conflict detection
- [ ] Substitution management
- [ ] Export to PDF/Excel

---

## 🛡️ SECURITY HARDENING

### 31. ADVANCED SECURITY MEASURES

- [ ] Rate limiting on login (5 attempts, 15-min lockout)
- [ ] IP whitelisting for Admin panel (optional)
- [ ] Session timeout: 30 mins idle
- [ ] Force password change every 90 days (staff)
- [ ] Audit log for sensitive actions:
  ```sql
  CREATE TABLE audit_logs (
      id INT PRIMARY KEY AUTO_INCREMENT,
      user_id INT,
      action VARCHAR(100),
      target_table VARCHAR(50),
      target_id INT,
      old_values JSON,
      new_values JSON,
      ip_address VARCHAR(45),
      user_agent TEXT,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );
  ```
- [ ] Two-factor authentication (2FA) via authenticator app
- [ ] Login notifications via email

### 32. DATA BACKUP & RECOVERY

- [ ] Daily automated database backups
- [ ] Backup to external location (Google Drive/S3)
- [ ] One-click restore (Admin only)
- [ ] Backup encryption
- [ ] 30-day backup retention

---

## 📱 PWA & MOBILE ENHANCEMENTS

### 33. OFFLINE FUNCTIONALITY

- [ ] Cache attendance forms for offline entry
- [ ] Sync when connection restored
- [ ] Offline grade entry for teachers
- [ ] Push notifications for:
  - New announcements
  - Fee due reminders
  - Child absence alerts
  - Exam schedules

### 34. MOBILE-OPTIMIZED VIEWS

- [ ] Parent app-like experience
- [ ] Quick actions dashboard
- [ ] Swipe gestures for navigation
- [ ] Touch-friendly grade entry

---

## 📊 ANALYTICS & REPORTING

### 35. ADMIN DASHBOARD ANALYTICS

- [ ] Total students by class/section
- [ ] Attendance trends (graphs)
- [ ] Fee collection summary
- [ ] Outstanding fees report
- [ ] Teacher performance metrics
- [ ] Student performance trends
- [ ] Enrollment statistics

### 36. EXPORT CAPABILITIES

- [ ] All reports exportable to:
  - PDF (with school letterhead)
  - Excel (.xlsx)
  - CSV
- [ ] Bulk data export for WAEC registration
- [ ] JAMB registration data export

---

## 🎨 UI/UX POLISH

### 37. ACCESSIBILITY IMPROVEMENTS

- [ ] Screen reader support (ARIA labels)
- [ ] Keyboard navigation
- [ ] High contrast mode (existing theme)
- [ ] Font size adjustment
- [ ] RTL support (for Arabic-speaking regions)

### 38. LOADING & FEEDBACK

- [ ] Loading spinners on all AJAX calls
- [ ] Toast notifications for actions
- [ ] Confirmation modals for destructive actions
- [ ] Form validation messages (inline)
- [ ] Success/error animations

---

## 🚀 DEPLOYMENT CHECKLIST

### 39. PRE-LAUNCH VERIFICATION

- [ ] All 8 themes tested on all pages
- [ ] Mobile responsiveness verified
- [ ] All forms submit correctly
- [ ] Email sending verified (OTP, notifications)
- [ ] Payment gateway test transactions
- [ ] PDF generation working
- [ ] Backup system operational
- [ ] SSL certificate installed
- [ ] Error logging configured
- [ ] Performance optimization (caching)

### 40. GO-LIVE TASKS

- [ ] Set production `.env` values
- [ ] Disable PHP error display
- [ ] Enable opcache
- [ ] Configure CDN for assets (optional)
- [ ] Set up monitoring (uptime checks)
- [ ] Document API endpoints
- [ ] Create user manual (PDF)
- [ ] Training videos for staff

---

## 📅 TIMELINE

| Phase     | Features                           | Duration    |
| --------- | ---------------------------------- | ----------- |
| Phase 1   | Email+OTP, Biometrics, Admin Panel | 1 week      |
| Phase 2   | Nigerian Localization, Payments    | 2 weeks     |
| Phase 3   | Parent/Teacher Portals             | 2 weeks     |
| Phase 4   | Reports, Analytics                 | 1 week      |
| Phase 5   | Security, Backup, Polish           | 1 week      |
| **TOTAL** | **Full System**                    | **7 weeks** |

---

## **Verdant SMS v3.0 — Built for Nigerian Schools, Powered by Innovation** 🇳🇬

## 🇳🇬 NATIONAL-SCALE EXPANSION — MULTI-TENANCY ARCHITECTURE

### 54. MULTI-TENANCY DATABASE ARCHITECTURE

- [ ] Create `schools` table for tenant isolation:
  ```sql
  CREATE TABLE schools (
      id INT PRIMARY KEY AUTO_INCREMENT,
      name VARCHAR(200) NOT NULL,
      subdomain VARCHAR(50) UNIQUE NOT NULL, -- myschool.verdantsms.com
      admin_user_id INT NULL, -- Created during signup
      logo_url VARCHAR(255) NULL,
      address TEXT,
      lga VARCHAR(100), -- Local Government Area
      state VARCHAR(50), -- Nigerian state
      phone VARCHAR(20),
      email VARCHAR(100),
      website VARCHAR(255) NULL,
      subscription_plan ENUM('free', 'basic', 'pro', 'enterprise') DEFAULT 'free',
      subscription_expires_at DATE NULL,
      status ENUM('pending', 'active', 'suspended') DEFAULT 'pending',
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (admin_user_id) REFERENCES users(id) ON DELETE SET NULL
  );
  ```
- [ ] Add `school_id` foreign key to ALL data tables:
  ```sql
  ALTER TABLE users ADD COLUMN school_id INT NULL;
  ALTER TABLE students ADD COLUMN school_id INT NOT NULL;
  ALTER TABLE classes ADD COLUMN school_id INT NOT NULL;
  ALTER TABLE attendance ADD COLUMN school_id INT NOT NULL;
  -- Repeat for ALL 50+ tables
  ```
- [ ] Create tenant isolation helper:

  ```php
  // includes/tenant.php
  function current_school_id(): int {
      return $_SESSION['school_id'] ?? 0;
  }

  function tenant_query(string $sql, array $params = []): array {
      $sql = str_replace('{school_id}', current_school_id(), $sql);
      return db()->fetchAll($sql, $params);
  }
  ```

- [ ] **CRITICAL**: ALL queries MUST include `WHERE school_id = ?`

### 55. SCHOOL SIGNUP FLOW — SELF-SERVICE ONBOARDING

- [ ] Create `visitor/school-signup.php`:

  ```
  Step 1: School Information
  ├── School Name
  ├── Preferred Subdomain (auto-check availability)
  ├── State (36 states + FCT dropdown)
  ├── LGA (dynamic based on state)
  ├── School Type (Primary/Secondary/Both)
  └── Estimated Student Count

  Step 2: Admin Account
  ├── Full Name
  ├── Email (becomes Admin email)
  ├── Phone (Nigerian format: 080/090/070/081)
  └── Password (strong: 8+ chars, mixed)

  Step 3: Verification
  ├── OTP sent to email
  ├── Verify → School created with status='pending'
  └── Platform Admin approves → School goes 'active'
  ```

- [ ] On approval:
  - Generate unique subdomain
  - Create school record
  - Create Admin user linked to school
  - Send welcome email with login URL
- [ ] Subdomain routing:
  ```php
  // includes/router.php
  $subdomain = explode('.', $_SERVER['HTTP_HOST'])[0];
  $school = db()->fetchRow("SELECT * FROM schools WHERE subdomain = ?", [$subdomain]);
  $_SESSION['school_id'] = $school['id'];
  ```

### 56. PLATFORM SUPER-ADMIN (VERDANT CENTRAL)

- [ ] Create `platform_admins` table:
  ```sql
  CREATE TABLE platform_admins (
      id INT PRIMARY KEY AUTO_INCREMENT,
      email VARCHAR(100) UNIQUE NOT NULL,
      password VARCHAR(255) NOT NULL,
      name VARCHAR(100),
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );
  ```
- [ ] `platform-admin/` folder for Verdant-level management:
  - `platform-admin/schools.php` — View/approve/suspend all schools
  - `platform-admin/subscriptions.php` — Manage billing
  - `platform-admin/analytics.php` — National usage stats
- [ ] Platform Admin ≠ School Admin: Separate login at `/platform-admin/login.php`

---

## 💰 NIGERIAN PRICING MODEL — IN NAIRA (₦)

### 57. SUBSCRIPTION PLANS

- [ ] Create `subscription_plans` table:
  ```sql
  CREATE TABLE subscription_plans (
      id INT PRIMARY KEY AUTO_INCREMENT,
      name VARCHAR(50) NOT NULL,
      price_naira DECIMAL(12, 2) NOT NULL,
      billing_cycle ENUM('monthly', 'yearly') DEFAULT 'yearly',
      max_students INT NULL, -- NULL = unlimited
      max_staff INT NULL,
      features JSON, -- ['biometric', 'ai_bulk', 'custom_subdomain']
      is_active BOOLEAN DEFAULT TRUE
  );
  ```
- [ ] Seed default plans:
      | Plan | Price (₦/year) | Students | Features |
      |------|----------------|----------|----------|
      | **Free Forever** | ₦0 | 100 | Core modules, self-hosted |
      | **Starter** | ₦50,000 | 300 | Cloud hosting, email support |
      | **Growth** | ₦150,000 | 1,000 | AI bulk, biometric, priority support |
      | **Enterprise** | ₦500,000+ | Unlimited | Dedicated server, training, custom dev |

### 58. PAYMENT INTEGRATION FOR SUBSCRIPTIONS

- [ ] `visitor/pricing.php` — Display plans with Paystack checkout
- [ ] After payment → update `schools.subscription_plan` and `subscription_expires_at`
- [ ] Auto-email receipt to school Admin
- [ ] Renewal reminders at 30/14/7/1 days before expiry
- [ ] Grace period: 7 days after expiry → suspend if not renewed

---

## 🌐 VISITOR PAGES — NATIONAL SHOWCASE

### 59. HOMEPAGE REDESIGN FOR NATIONAL APPEAL

- [ ] Hero section content:
  ```html
  <h1>The National School Management System for Nigeria</h1>
  <p>
    Trusted by 1,000+ schools across 36 states. Free forever for core features.
  </p>
  <div class="cta-buttons">
    <a href="/visitor/school-signup.php" class="btn-primary"
      >Register Your School Free</a
    >
    <a href="/visitor/demo.php" class="btn-secondary">Try Live Demo</a>
  </div>
  ```
- [ ] Statistics section (dynamic from DB):
  - Total schools registered
  - Total students managed
  - States covered
- [ ] Testimonials carousel with Nigerian school logos
- [ ] Map of Nigeria with pins for registered schools (anonymous)

### 60. EXPANDED VISITOR PAGES

- [ ] `visitor/features.php` — Module showcase:
  - Academics (Nigerian curriculum, WAEC prep)
  - Finance (Naira billing, Paystack/Flutterwave)
  - Communication (SMS via Termii, WhatsApp)
  - Security (Biometric, OTP, Admin isolation)
- [ ] `visitor/schools.php` — "Join the Verdant Network":
  - List of schools by state (opt-in visibility)
  - "Find a Verdant School Near You" search
- [ ] `visitor/demo.php` — Interactive sandbox:
  - Pre-created demo school with fake data
  - Guest login as Admin/Teacher/Student/Parent
  - Data resets every 6 hours
  - No real email/SMS sent
- [ ] `visitor/blog.php` — Success stories:
  - "How Lagos Model School Digitized in 30 Days"
  - "Rural Schools Go Paperless with Verdant"
- [ ] `visitor/support.php` — Help center:
  - Video tutorials
  - Documentation links
  - WhatsApp support chat widget

---

## 🔧 TECHNICAL IMPLEMENTATION FOR NATIONAL SCALE

### 61. SUBDOMAIN ROUTING

- [ ] Apache VirtualHost wildcard:
  ```apache
  <VirtualHost *:80>
      ServerName verdantsms.com
      ServerAlias *.verdantsms.com
      DocumentRoot /opt/lampp/htdocs/attendance
  </VirtualHost>
  ```
- [ ] PHP subdomain detection:
  ```php
  function get_current_school(): ?array {
      $host = $_SERVER['HTTP_HOST'];
      if (strpos($host, '.verdantsms.com') !== false) {
          $subdomain = explode('.', $host)[0];
          return db()->fetchRow("SELECT * FROM schools WHERE subdomain = ?", [$subdomain]);
      }
      return null; // Main site
  }
  ```

### 62. MULTI-LANGUAGE SUPPORT

- [ ] Create `languages` table:
  ```sql
  CREATE TABLE languages (
      code VARCHAR(5) PRIMARY KEY, -- 'en', 'yo', 'ig', 'ha'
      name VARCHAR(50),
      is_active BOOLEAN DEFAULT TRUE
  );
  ```
- [ ] Add language files in `lang/`:
  ```
  lang/
  ├── en.php (English - default)
  ├── yo.php (Yoruba)
  ├── ig.php (Igbo)
  └── ha.php (Hausa)
  ```
- [ ] Translation helper:
  ```php
  function __($key): string {
      global $lang;
      return $lang[$key] ?? $key;
  }
  ```
- [ ] Language selector in visitor navbar + user profile

### 63. NATIONAL COMPLIANCE

- [ ] NDPR (Nigeria Data Protection Regulation) checklist:
  - Data processing consent on signup
  - Right to erasure (account deletion)
  - Data export (user can download their data)
- [ ] NERDC curriculum alignment verification
- [ ] CAC (Corporate Affairs Commission) registration display for schools

---

## 📊 NATIONAL ANALYTICS DASHBOARD

### 64. PLATFORM-LEVEL ANALYTICS

- [ ] `platform-admin/analytics.php`:
  - Total schools by state (pie chart)
  - Student enrollment trends (line chart)
  - Revenue from subscriptions (bar chart)
  - Active vs dormant schools
  - Top 10 schools by student count

### 65. SCHOOL-LEVEL BENCHMARKING

- [ ] School Admin dashboard widget:
  - "Your School vs National Average"
  - Attendance rate comparison
  - Academic performance percentile
  - Anonymous — no school names revealed

---

## 🚀 NATIONAL LAUNCH CHECKLIST

### 66. PRE-LAUNCH TASKS

- [ ] Domain: verdantsms.com (or .ng)
- [ ] SSL wildcard certificate for subdomains
- [ ] CDN setup (Cloudflare) for national performance
- [ ] Load testing for 10,000 concurrent users
- [ ] Backup strategy: Daily to AWS S3/Google Cloud

### 67. MARKETING FOR NATIONAL ADOPTION

- [ ] Press release: "Nigerian Ed-Tech Startup Launches Free School Management System"
- [ ] Partnership outreach:
  - State Ministries of Education
  - Private school associations
  - Teacher unions
- [ ] Social media campaign:
  - Twitter/X: @VerdantSMS
  - LinkedIn: Verdant School Management
  - Facebook: Target Nigerian education groups

### 68. FINAL GIT COMMANDS FOR NATIONAL LAUNCH

```bash
git add .
git commit -m "🇳🇬 Verdant SMS v3.0 NATIONAL LAUNCH

✅ Multi-Tenancy Architecture:
- Each school = isolated tenant with unique subdomain
- Admin per school with full control
- Zero data clashes between schools

✅ Nigerian Pricing in Naira:
- Free Forever tier for 100 students
- Starter ₦50k | Growth ₦150k | Enterprise ₦500k+
- Paystack/Flutterwave integration

✅ National Visitor Pages:
- School signup with OTP verification
- Interactive demo sandbox
- Features, pricing, schools directory
- Multi-language (English, Yoruba, Igbo, Hausa)

✅ Platform Admin:
- Manage all schools nationwide
- Subscription billing
- National analytics

Ready for 10,000+ schools across Nigeria."

git push origin main
git tag -a v3.0-national -m "National Launch - Multi-Tenancy + Naira Pricing"
git push origin v3.0-national
```

---

## 📋 NATIONAL SCALE EXECUTION SUMMARY

| Priority    | Task                       | Status |
| ----------- | -------------------------- | ------ |
| 🔴 CRITICAL | Multi-tenancy architecture | [ ]    |
| 🔴 CRITICAL | School signup flow         | [ ]    |
| 🔴 CRITICAL | Subdomain routing          | [ ]    |
| 🟠 HIGH     | Naira pricing + Paystack   | [ ]    |
| 🟠 HIGH     | Homepage national redesign | [ ]    |
| 🟠 HIGH     | Demo sandbox               | [ ]    |
| 🟡 MEDIUM   | Platform Admin dashboard   | [ ]    |
| 🟡 MEDIUM   | Multi-language support     | [ ]    |
| 🟢 LOW      | School directory/map       | [ ]    |
| 🟢 LOW      | National analytics         | [ ]    |

---

## 🌍 AFRICA-WIDE EXPANSION — PAN-AFRICAN VISION

### 69. MULTI-COUNTRY SUPPORT

- [ ] Create `countries` table:
  ```sql
  CREATE TABLE countries (
      code VARCHAR(3) PRIMARY KEY, -- 'NGA', 'GHA', 'KEN', 'ZAF'
      name VARCHAR(100),
      currency_code VARCHAR(3),
      currency_symbol VARCHAR(5),
      timezone VARCHAR(50),
      education_system VARCHAR(50), -- 'Nigerian', 'British', 'French'
      is_active BOOLEAN DEFAULT TRUE
  );
  ```
- [ ] Seed African countries:
      | Country | Currency | System |
      |---------|----------|--------|
      | Nigeria | NGN (₦) | 6-3-3-4 |
      | Ghana | GHS (₵) | 6-3-3-4 |
      | Kenya | KES (KSh) | 8-4-4 |
      | South Africa | ZAR (R) | CAPS |
      | Rwanda | RWF (FRw) | 6-3-3-4 |
      | Tanzania | TZS (TSh) | 7-4-2-3+ |
- [ ] Country-specific grading scales
- [ ] Localized curriculum templates per country

### 70. AI-POWERED FEATURES

- [ ] **AI Attendance Predictor**:
  - Predict students at risk of chronic absenteeism
  - Auto-alert class teacher and parents
  - Factors: weather, past patterns, day of week
- [ ] **AI Academic Advisor**:
  - Recommend subjects based on student performance
  - Suggest remedial classes for struggling students
  - University course recommendations for SSS 3
- [ ] **AI-Generated Report Comments**:
  ```php
  // Uses GPT/Claude API
  generateReportComment($student_id, $term_id);
  // Output: "John has shown remarkable improvement in Mathematics this term..."
  ```
- [ ] **Chatbot for Parents/Students**:
  - "What is my child's attendance this week?"
  - "When are school fees due?"
  - WhatsApp Business API integration

### 71. GAMIFICATION FOR STUDENTS

- [ ] Create `achievements` table:
  ```sql
  CREATE TABLE achievements (
      id INT PRIMARY KEY AUTO_INCREMENT,
      name VARCHAR(100), -- 'Perfect Attendance', 'Math Wizard'
      description TEXT,
      icon VARCHAR(50), -- Font Awesome class
      badge_color VARCHAR(7), -- Hex color
      points INT DEFAULT 0,
      criteria JSON -- Unlock conditions
  );
  ```
- [ ] Achievement categories:
  - 📚 Academic (Top scorer, Most improved)
  - 🎯 Attendance (Perfect week/month/term)
  - 📖 Library (Bookworm, Speed reader)
  - 🏅 Sports (MVP, Team captain)
  - 🤝 Social (Helper, Peer tutor)
- [ ] Leaderboard per class/school
- [ ] Digital badge display on student profile
- [ ] Weekly "Star Student" announcement

### 72. PARENT ENGAGEMENT TOOLKIT

- [ ] **Parent App (PWA)**:
  - Daily push notification with child's activities
  - Real-time location during transport
  - Homework reminders
  - Fee payment one-tap
- [ ] **Parent Dashboard Widgets**:
  - Child's mood tracker (optional teacher input)
  - Upcoming events calendar
  - Direct message to class teacher
  - Report card comparison across terms
- [ ] **PTA Module**:
  - PTA meeting scheduling
  - Voting on proposals
  - Contribution tracking
  - PTA financial reports

### 73. ADVANCED TIMETABLE ENGINE

- [ ] Auto-generate optimal timetable:
  - No teacher double-booking
  - Lab/room availability checks
  - Teacher preference slots
  - Subject distribution balance (no 3 Maths in a row)
- [ ] Constraint solver algorithm
- [ ] One-click regeneration
- [ ] Export to Google Calendar
- [ ] Student/teacher personal timetable view

### 74. DIGITAL HOMEWORK & ASSIGNMENTS

- [ ] Teacher creates assignment with:
  - Description, due date, attachments (PDF/video)
  - Point value, rubric
  - Class/individual assignment toggle
- [ ] Student submission portal:
  - File upload (PDF, Word, images)
  - Plagiarism indicator (basic)
  - Late submission tracking
- [ ] Teacher grading interface:
  - Inline PDF annotation
  - Voice feedback recording
  - Bulk grade entry
- [ ] Parent visibility of pending homework

### 75. VIRTUAL CLASSROOM INTEGRATION

- [ ] **Zoom/Google Meet** scheduling:
  - One-click meeting creation
  - Auto-add to class calendar
  - Attendance tracking from meeting participants
- [ ] **Whiteboard** feature (embedded Canvas API)
- [ ] **Screen recording** for lesson replays
- [ ] **Live quiz** during virtual class (like Kahoot)

### 76. STUDENT WELLNESS MODULE

- [ ] Mood check-in (daily emoji selector)
- [ ] Counselor dashboard:
  - Students flagged for low mood patterns
  - Private messaging with students
  - Session notes (encrypted)
- [ ] Mental health resources library
- [ ] Anonymous concern reporting
- [ ] Bullying incident tracker

### 77. SMART NOTIFICATIONS ENGINE

- [ ] Notification preferences per user:
  - Email, SMS, WhatsApp, Push
  - Frequency: Instant, Daily digest, Weekly summary
- [ ] Event-based triggers:
      | Event | Recipients | Channels |
      |-------|------------|----------|
      | Student absent | Parent + Class teacher | SMS + WhatsApp |
      | Fee overdue | Parent | Email + SMS |
      | Exam results published | Parent + Student | Push + Email |
      | Assignment due tomorrow | Student | Push |
      | Emergency announcement | All | All channels |
- [ ] Template customization by Admin

### 78. FINANCIAL AID & SCHOLARSHIPS

- [ ] Scholarship database:
  ```sql
  CREATE TABLE scholarships (
      id INT PRIMARY KEY AUTO_INCREMENT,
      name VARCHAR(200),
      sponsor VARCHAR(200),
      eligibility_criteria TEXT,
      amount DECIMAL(12, 2),
      coverage ENUM('full', 'partial', 'fixed'),
      slots_available INT,
      application_deadline DATE
  );
  ```
- [ ] Student scholarship application form
- [ ] Admin review/approval workflow
- [ ] Auto-apply discount to fee invoices
- [ ] Scholarship renewal tracking

### 79. SMART ADMISSION SCORING

- [ ] Entrance exam weighted scoring:
  - Academic score: 60%
  - Interview score: 20%
  - Extracurricular: 10%
  - Recommendation letter: 10%
- [ ] Auto-rank applicants
- [ ] Cutoff threshold configuration
- [ ] Waitlist management with auto-upgrade

### 80. SCHOOL BRANDING CUSTOMIZATION

- [ ] Per-school branding:
  - Custom logo (header + watermark)
  - Primary/secondary colors
  - School motto display
  - Custom login page background
- [ ] Report card customization:
  - School letterhead
  - Signature images (Principal, Class teacher)
  - Custom sections (behavioral, extracurricular)
- [ ] Certificate templates with school branding

### 81. API MARKETPLACE

- [ ] RESTful API for third-party integrations:
  ```
  /api/v1/students       — CRUD student records
  /api/v1/attendance     — Mark/retrieve attendance
  /api/v1/grades         — Grade management
  /api/v1/fees           — Fee status and payments
  /api/v1/webhooks       — Event notifications
  ```
- [ ] API key management per school
- [ ] Rate limiting (1000 req/hour)
- [ ] OAuth 2.0 authentication
- [ ] Developer portal: `developers.verdantsms.com`

### 82. OFFLINE SYNC FOR RURAL SCHOOLS

- [ ] IndexedDB local storage for:
  - Attendance entry
  - Grade entry
  - Student registration
- [ ] Background sync when internet restored
- [ ] Conflict resolution (last-write-wins or manual merge)
- [ ] Low-bandwidth mode (text-only, compressed images)
- [ ] USSD integration for feature phones

### 83. ADVANCED REPORTING ENGINE

- [ ] Custom report builder:
  - Drag-and-drop fields
  - Filters (date range, class, section)
  - Grouping and aggregation
  - Chart type selection
- [ ] Scheduled reports:
  - Daily attendance summary to Principal
  - Weekly fee collection to Accountant
  - Monthly academic performance to Parents
- [ ] Report sharing via link (expiring)
- [ ] Export: PDF, Excel, Google Sheets

### 84. INTER-SCHOOL COMPETITIONS

- [ ] Sports competitions module:
  - Tournament bracket generator
  - Score tracking
  - Medal/trophy assignment
- [ ] Academic olympiads:
  - Quiz bowl scoring
  - Debate judging
- [ ] Art/cultural competitions:
  - Photo/video submissions
  - Voting system
- [ ] Leaderboard across schools (opt-in)

### 85. CARBON FOOTPRINT TRACKER

- [ ] Track paper saved by going digital:
  - Estimated sheets saved per month
  - CO2 equivalent
- [ ] Display on school dashboard
- [ ] "Green School" badge for eco-milestones
- [ ] Annual sustainability report

---

## 🔮 FUTURE-PROOF TECHNOLOGIES

### 86. BLOCKCHAIN CERTIFICATE VERIFICATION

- [ ] Issue certificates on blockchain:
  - Tamper-proof academic records
  - QR code links to verification
  - Universities can verify instantly
- [ ] Partnership with Nigerian universities

### 87. VOICE INTERFACE (ACCESSIBILITY)

- [ ] Voice commands:
  - "Mark Adaobi present"
  - "What is Chinedu's math score?"
  - "Schedule PTA meeting for Friday"
- [ ] Voice-to-text for report comments
- [ ] Text-to-speech for announcements

### 88. AUGMENTED REALITY (AR) FEATURES

- [ ] AR school tour for prospective parents
- [ ] AR lab simulations
- [ ] AR graduation ceremony (virtual audience)

---

## 📋 EXPANDED EXECUTION SUMMARY

| Priority    | Task                          | Status |
| ----------- | ----------------------------- | ------ |
| 🔴 CRITICAL | AI Report Comments            | [ ]    |
| 🔴 CRITICAL | Smart Notifications Engine    | [ ]    |
| 🟠 HIGH     | Gamification/Achievements     | [ ]    |
| 🟠 HIGH     | Digital Homework Portal       | [ ]    |
| 🟠 HIGH     | Offline Sync for Rural        | [ ]    |
| 🟡 MEDIUM   | Virtual Classroom Integration | [ ]    |
| 🟡 MEDIUM   | API Marketplace               | [ ]    |
| 🟡 MEDIUM   | Custom Report Builder         | [ ]    |
| 🟢 LOW      | Blockchain Certificates       | [ ]    |
| 🟢 LOW      | Voice Interface               | [ ]    |
| 🟢 LOW      | AR Features                   | [ ]    |

---

**Verdant SMS v4.0 Roadmap — AI-Powered, Africa-Ready, Future-Proof** 🚀🌍

## **The Vision: Every African Child, One Connected Platform**

**Verdant SMS v3.0 — From One School to One Nation** 🇳🇬🌿

**Built for Nigeria. Scaled for Africa. Ready for the World.**

## **13 December 2025 — NATIONAL LAUNCH DAY**

## 🤖 AI INTEGRATION — PERVASIVE INTELLIGENCE LAYER

### 89. AI CHATBOT — OMNIPRESENT ASSISTANT

- [ ] **Homepage Chatbot Widget** (visitor-facing):

  ```
  ┌────────────────────────────────────────┐
  │  💬 VerdantBot                    ─ ✕  │
  ├────────────────────────────────────────┤
  │                                        │
  │  👤 Hi! I'm VerdantBot, your AI        │
  │  assistant. What can I help you with?  │
  │                                        │
  │                                        │
  │  ○ Speak with a human                  │
  │  👋 Welcome! I'm VerdantBot.           │
  │  How can I help you today?             │
  │                                        │
  │  ○ Learn about features                │
  │  ○ Request a demo                      │
  │  ○ Pricing information                 │
  │  ○ Register your school                │
  │  ○ Contact support                     │
  ├────────────────────────────────────────┤
  │  Type your question...         [Send]  │
  └────────────────────────────────────────┘
  ```

  Queries supported:

  - "What is Verdant SMS?"
  - "How much does it cost?"
  - "How do I register my school?"
  - "I need to speak to support"

  ```

  ```

- [ ] **Role-Specific Chatbots**:
  - **Admin Bot**: "Show pending registrations", "Generate fee report"
  - **Teacher Bot**: "Mark JSS 2A present", "Show today's timetable"
  - **Student Bot**: "What's my next class?", "Show my grades"
  - **Parent Bot**: "Is my child in school?", "Pay school fees"
- [ ] Create `includes/chatbot.php` — Universal chatbot component
- [ ] Integrate with Claude/GPT API for natural language understanding
- [ ] Fallback to keyword matching for offline/low-cost mode
- [ ] Conversation history stored in `chatbot_sessions` table

### 90. AI EMAIL READER & LEAD PUSHER

- [ ] Create `ai/email-monitor.php`:
  ```php
  class AIEmailMonitor {
      // Connect to IMAP inbox
      // Scan for school registration inquiries
      // Extract: School name, contact person, email, phone, location
      // AI categorizes: Hot lead, Warm lead, Spam
      // Store in leads table
      // Push notification to Admin dashboard + SMS/WhatsApp
  }
  ```
- [ ] Create `leads` table:
  ```sql
  CREATE TABLE leads (
      id INT PRIMARY KEY AUTO_INCREMENT,
      school_name VARCHAR(200),
      contact_name VARCHAR(100),
      email VARCHAR(100),
      phone VARCHAR(20),
      state VARCHAR(50),
      message TEXT,
      source ENUM('email', 'form', 'whatsapp', 'referral') DEFAULT 'email',
      priority ENUM('hot', 'warm', 'cold') DEFAULT 'warm',
      status ENUM('new', 'contacted', 'demo_scheduled', 'converted', 'lost') DEFAULT 'new',
      ai_summary TEXT, -- AI-generated summary
      assigned_to INT NULL, -- Sales rep
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      followed_up_at TIMESTAMP NULL
  );
  ```
- [ ] AI auto-response email:

  ```
  Subject: Thank You for Your Interest in Verdant SMS

  Dear [Contact Name],

  Thank you for reaching out about Verdant School Management System!

  We've received your inquiry and a representative will contact you
  within 24 hours to discuss how Verdant SMS can transform [School Name].

  In the meantime, you can:
  ✅ Try our live demo: https://demo.verdantsms.com
  ✅ View pricing: https://verdantsms.com/pricing
  ✅ WhatsApp us: +234 816 771 4860

  Best regards,
  VerdantBot 🤖
  Verdant School Management System
  ```

- [ ] Daily lead digest email to Admin at 8 AM WAT

### 91. AI-POWERED SMART SEARCH

- [ ] Global search bar on every authenticated page:
  ```
  ┌─────────────────────────────────────────────────────────┐
  │  🔍 Search students, teachers, classes, reports...     │
  └─────────────────────────────────────────────────────────┘
  ```
- [ ] Natural language queries:
  - "Students who failed Mathematics last term"
  - "Teachers on leave this week"
  - "Unpaid fees above ₦50,000"
  - "Classes without assigned teachers"
- [ ] AI parses query → generates SQL → returns results
- [ ] Recent searches + suggestions

### 92. AI DOCUMENT ANALYZER

- [ ] Upload student documents (birth cert, transfer cert, etc.)
- [ ] AI extracts:
  - Full name
  - Date of birth
  - Previous school
  - LGA/State of origin
- [ ] Auto-populates registration form
- [ ] Fraud detection: Flag suspicious documents
- [ ] OCR for scanned documents

### 93. AI VOICE ASSISTANT

- [ ] Voice input button on all forms:
  ```html
  <button id="voiceInput" aria-label="Voice Input">
    <i class="fas fa-microphone"></i>
  </button>
  ```
- [ ] Commands:
  - "Take attendance for Primary 3"
  - "Add new student named Adaeze Okonkwo"
  - "Show today's schedule"
- [ ] Web Speech API for browser-native recognition
- [ ] African accent optimization

### 94. AI PREDICTIVE ANALYTICS

- [ ] **Dropout Risk Predictor**:
  - Factors: Attendance, grades, fee payment, behavior
  - Risk score 0-100
  - Auto-alert counselor for scores > 70
- [ ] **Academic Performance Predictor**:
  - Predict end-of-term grades based on CA scores
  - Recommend intervention for struggling students
- [ ] **Fee Default Predictor**:
  - Identify parents likely to default
  - Proactive payment reminders
- [ ] **Enrollment Forecaster**:
  - Predict next session enrollment
  - Class capacity planning

### 95. AI CONTENT GENERATOR

- [ ] Auto-generate school announcements:
  - "School resumes on [date]"
  - "Exam timetable released"
  - "PTA meeting reminder"
- [ ] AI drafts parent communication:
  - Progress reports
  - Absence notifications
  - Fee reminders
- [ ] Newsletter builder with AI suggestions

---

## 💳 ADVANCED PAYMENT GATEWAY INTEGRATION

### 96. MULTI-GATEWAY ARCHITECTURE

- [ ] Create `payment_gateways` table:
  ```sql
  CREATE TABLE payment_gateways (
      id INT PRIMARY KEY AUTO_INCREMENT,
      name VARCHAR(50), -- 'paystack', 'flutterwave', 'monnify', 'bank_transfer'
      is_active BOOLEAN DEFAULT FALSE,
      config JSON, -- API keys (encrypted)
      priority INT DEFAULT 0, -- Fallback order
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );
  ```
- [ ] Supported gateways:
  - **Paystack** (Primary) — Cards, Bank Transfer, USSD
  - **Flutterwave** (Backup) — Cards, Mobile Money
  - **Monnify** — Virtual accounts, dedicated NUBANs
  - **Manual Bank Transfer** — With receipt upload verification
- [ ] Gateway abstraction layer:
  ```php
  interface PaymentGateway {
      public function initiate(float $amount, array $metadata): array;
      public function verify(string $reference): bool;
      public function refund(string $reference, float $amount): bool;
  }
  ```

### 97. VIRTUAL ACCOUNT PER STUDENT

- [ ] Integration with Monnify/Paystack for dedicated virtual accounts:
  ```
  Student: Adaeze Okonkwo
  Virtual Account: 9900123456 (Wema Bank)
  Account Name: VSS/ADZ/OKONKWO
  ```
- [ ] Any transfer to this account auto-credits student's fee balance
- [ ] Instant SMS notification to parent + school
- [ ] Eliminates manual reconciliation

### 98. SPLIT PAYMENTS

- [ ] For multi-branch schools:
  ```
  Total Fee: ₦150,000
  ├── Branch Account: ₦120,000 (80%)
  ├── Central Admin: ₦22,500 (15%)
  └── Verdant Platform: ₦7,500 (5%)
  ```
- [ ] Configurable split ratios per school
- [ ] Automated payout settlement

### 99. PAYMENT PLANS & INSTALLMENTS

- [ ] Create `payment_plans` table:
  ```sql
  CREATE TABLE payment_plans (
      id INT PRIMARY KEY AUTO_INCREMENT,
      name VARCHAR(100), -- '3-Month Plan', '50-50 Split'
      installments INT,
      schedule JSON, -- [{"percent": 50, "due_days": 0}, {"percent": 50, "due_days": 45}]
      is_active BOOLEAN DEFAULT TRUE
  );
  ```
- [ ] Parent selects plan at checkout
- [ ] Auto-debit reminders before due dates
- [ ] Late payment penalties (configurable)

### 100. SUBSCRIPTION BILLING FOR PLATFORM

- [ ] Automated recurring billing for schools:
  - Monthly/Yearly subscription
  - Auto-charge on renewal date
  - Grace period handling
  - Downgrade on failed payment
- [ ] Invoice generation with Nigerian tax (VAT 7.5%)
- [ ] Receipt PDF with Verdant branding

---

## 🏫 NATIONAL SCHOOL NETWORK — ADVANCED FEATURES

### 101. SCHOOL ONBOARDING PIPELINE

- [ ] Lead stages with AI automation:
  ```
  1. INQUIRY (AI Email Reader captures)
       ↓
  2. QUALIFIED (AI scores lead, assigns priority)
       ↓
  3. DEMO SCHEDULED (Calendar integration)
       ↓
  4. PROPOSAL SENT (Auto-generate pricing doc)
       ↓
  5. NEGOTIATION (Legal terms discussion)
       ↓
  6. CONTRACT SIGNED (E-signature integration)
       ↓
  7. ONBOARDING (Automated setup wizard)
       ↓
  8. LIVE (School active on platform)
  ```
- [ ] CRM-style pipeline view for platform admin
- [ ] AI follow-up suggestions

### 102. LEGAL DOCUMENT AUTOMATION

- [ ] Auto-generate contracts:
  ```
  SERVICE LEVEL AGREEMENT
  Between: Verdant SMS Limited
  And: [School Name]
  Subscription Tier: [Plan]
  Duration: [12 months]
  Start Date: [Auto-filled]
  Monthly Fee: ₦[Amount]
  ...
  ```
- [ ] E-signature integration (DocuSign/SignNow or custom)
- [ ] Document storage with version history
- [ ] Renewal reminders 60 days before expiry

### 103. SCHOOL VERIFICATION SYSTEM

- [ ] Document requirements for school registration:
  - CAC Certificate
  - Ministry of Education approval
  - TIN (Tax Identification Number)
  - Utility bill (address verification)
- [ ] AI verification:
  - OCR extraction
  - Cross-reference with CAC database (if API available)
  - Flag discrepancies
- [ ] Badge system: "Verified School" ✓

### 104. REFERRAL & AFFILIATE PROGRAM

- [ ] Create `referrals` table:
  ```sql
  CREATE TABLE referrals (
      id INT PRIMARY KEY AUTO_INCREMENT,
      referrer_school_id INT,
      referred_school_id INT,
      status ENUM('pending', 'converted', 'paid') DEFAULT 'pending',
      commission_amount DECIMAL(12, 2),
      paid_at TIMESTAMP NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );
  ```
- [ ] Referral commission: 10% of first-year subscription
- [ ] Referral dashboard for schools
- [ ] Auto-payout to school's bank account

### 105. INTER-SCHOOL DATA EXCHANGE

- [ ] Secure student transfer between Verdant schools:
  - Sending school initiates transfer
  - Receiving school approves
  - Academic records, health records transferred
  - Original school access revoked
- [ ] Data privacy compliance (NDPR)
- [ ] Audit trail of all transfers

### 106. NATIONAL EXAMINATIONS INTEGRATION

- [ ] WAEC/NECO candidate registration:
  - Auto-populate from student database
  - Export in WAEC format
  - Track registration status
- [ ] JAMB profile linking:
  - Store JAMB registration numbers
  - Mock JAMB CBT practice
- [ ] National exam results import:
  - Upload WAEC result PDF
  - AI extracts grades
  - Store in student profile

---

## 🎨 UI/UX PERFECTION — ZERO FLAWS

### 107. DESIGN SYSTEM DOCUMENTATION

- [ ] Create `docs/DESIGN_SYSTEM.md`:

  ```
  # Verdant SMS Design System

  ## Colors
  - Primary: #00D9FF (Cyberpunk) / #22C55E (Nature)
  - Background: #0A0E17 (Dark) / #F0FDF4 (Light)
  - Text: #E2E8F0 / #1E293B
  - Accent: #FF2E97 / #4ADE80
  - Error: #EF4444
  - Warning: #F59E0B
  - Success: #10B981

  ## Typography
  - Headings: 'Orbitron', sans-serif (Cyberpunk) / 'Poppins', sans-serif (Nature)
  - Body: 'Inter', sans-serif
  - Monospace: 'JetBrains Mono', monospace

  ## Spacing Scale
  - xs: 4px, sm: 8px, md: 16px, lg: 24px, xl: 32px, 2xl: 48px

  ## Border Radius
  - none: 0, sm: 4px, md: 8px, lg: 16px, full: 9999px

  ## Shadows
  - sm: 0 1px 2px rgba(0,0,0,0.05)
  - md: 0 4px 6px rgba(0,0,0,0.1)
  - lg: 0 10px 15px rgba(0,0,0,0.1)
  - glow: 0 0 20px rgba(0,217,255,0.3)
  ```

### 108. COMPONENT LIBRARY

- [ ] Create reusable UI components in `assets/css/components/`:
  ```
  components/
  ├── buttons.css       — Primary, secondary, danger, ghost
  ├── cards.css         — Info cards, stat cards, profile cards
  ├── forms.css         — Inputs, selects, checkboxes, radios
  ├── modals.css        — Confirmation, forms, alerts
  ├── tables.css        — Data tables with sorting, pagination
  ├── navigation.css    — Sidebar, topbar, breadcrumbs
  ├── alerts.css        — Toast, banner, inline alerts
  ├── badges.css        — Status, role, count badges
  ├── avatars.css       — User avatars with status indicator
  ├── progress.css      — Progress bars, loaders, spinners
  └── charts.css        — Chart containers, legends
  ```

### 109. MICRO-INTERACTIONS & ANIMATIONS

- [ ] Button hover effects:
  ```css
  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 217, 255, 0.4);
  }
  ```
- [ ] Form field focus glow
- [ ] Card hover lift effect
- [ ] Skeleton loading states for async content
- [ ] Success checkmark animation on form submit
- [ ] Page transition fade-in
- [ ] Sidebar collapse animation

### 110. RESPONSIVE BREAKPOINTS

- [ ] Standardized breakpoints:
  ```css
  /* Mobile first */
  @media (min-width: 640px) {
  } /* sm */
  @media (min-width: 768px) {
  } /* md */
  @media (min-width: 1024px) {
  } /* lg */
  @media (min-width: 1280px) {
  } /* xl */
  @media (min-width: 1536px) {
  } /* 2xl */
  ```
- [ ] Test on: iPhone SE, iPhone 14, iPad, MacBook, 27" monitor

### 111. ACCESSIBILITY AUDIT

- [ ] WCAG 2.1 AA compliance:
  - Color contrast ratio ≥ 4.5:1
  - Focus indicators visible
  - Alt text on all images
  - ARIA labels on interactive elements
  - Skip-to-content link
  - Keyboard navigation complete
- [ ] Screen reader testing (NVDA/VoiceOver)
- [ ] Reduced motion support:
  ```css
  @media (prefers-reduced-motion: reduce) {
    * {
      animation: none !important;
      transition: none !important;
    }
  }
  ```

### 112. ERROR STATE DESIGNS

- [ ] 404 Page:
  ```
  ┌────────────────────────────────────────┐
  │           🔍 Page Not Found            │
  │                                        │
  │   The page you're looking for          │
  │   doesn't exist or has been moved.     │
  │                                        │
  │   [Go to Dashboard]  [Contact Support] │
  └────────────────────────────────────────┘
  ```
- [ ] 500 Error Page
- [ ] Maintenance Mode Page
- [ ] Session Expired Page
- [ ] Access Denied Page (403)
- [ ] Form validation error states

### 113. DARK/LIGHT MODE TOGGLE

- [ ] System preference detection:
  ```javascript
  const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
  ```
- [ ] Manual toggle with localStorage persistence
- [ ] Smooth transition between modes
- [ ] All 8 themes have light/dark variants

---

## 💰 CORRECTED PRICING STRUCTURE

### 114. TRANSPARENT NIGERIAN PRICING

| Plan             | Monthly (₦) | Yearly (₦) | Students  | Features                                   |
| ---------------- | ----------- | ---------- | --------- | ------------------------------------------ |
| **Free**         | ₦0          | ₦0         | 50        | Core modules, community support            |
| **Starter**      | ₦5,000      | ₦50,000    | 200       | Email support, basic reports               |
| **Growth**       | ₦15,000     | ₦150,000   | 500       | Priority support, AI features, SMS         |
| **Professional** | ₦30,000     | ₦300,000   | 1,000     | Dedicated account manager, API access      |
| **Enterprise**   | Custom      | Custom     | Unlimited | On-premise option, SLA, custom development |

### 115. ADD-ONS PRICING

| Add-On                    | Price (₦/month) |
| ------------------------- | --------------- |
| SMS Bundle (1,000 SMS)    | ₦5,000          |
| Biometric Hardware        | ₦150,000 (once) |
| Custom Subdomain          | ₦2,000          |
| White-label Branding      | ₦20,000         |
| Additional Storage (10GB) | ₦3,000          |
| Priority Support          | ₦10,000         |

### 116. PAYMENT OPTIONS

- [ ] Bank Transfer: Display dedicated account
- [ ] Card Payment: Paystack/Flutterwave
- [ ] USSD: *737# (GTBank), *919# (UBA), etc.
- [ ] Mobile Money: OPay, PalmPay, Kuda
- [ ] Installments: 2/3/4 monthly payments (for yearly plans)

---

## 🔮 ADVANCED FUTURE FEATURES

### 117. BLOCKCHAIN TRANSCRIPT VERIFICATION

- [ ] Issue immutable academic transcripts
- [ ] QR code links to blockchain record
- [ ] Verification portal for universities/employers
- [ ] Partnership with NYSC for verification

### 118. SMART CLASSROOM IoT

- [ ] Integrate with smart boards
- [ ] Automatic attendance via Bluetooth beacons
- [ ] Climate monitoring (temperature, air quality)
- [ ] Energy usage dashboard

### 119. MENTAL HEALTH AI

- [ ] Analyze student behavior patterns
- [ ] Early depression/anxiety indicators
- [ ] Counselor alert system
- [ ] Anonymous peer support chatbot

### 120. PARENT-TEACHER CONFERENCE SCHEDULER

- [ ] AI suggests optimal meeting times
- [ ] Video conference integration
- [ ] Meeting notes with AI summary
- [ ] Action items tracking

### 121. ALUMNI CAREER TRACKING

- [ ] Graduate employment outcomes
- [ ] University admission rates
- [ ] Success stories for marketing
- [ ] Alumni donation campaigns

### 122. GOVERNMENT COMPLIANCE REPORTS

- [ ] Auto-generate Ministry of Education reports
- [ ] ASC (Annual School Census) data export
- [ ] UBEC compliance tracking
- [ ] NERDC curriculum alignment reports

---

## 📋 ULTIMATE EXECUTION SUMMARY

| Priority    | Task                          | Status |
| ----------- | ----------------------------- | ------ |
| 🔴 CRITICAL | AI Chatbot on all pages       | [ ]    |
| 🔴 CRITICAL | AI Email Reader + Lead Pusher | [ ]    |
| 🔴 CRITICAL | Payment gateway integration   | [ ]    |
| 🔴 CRITICAL | Virtual accounts per student  | [ ]    |
| 🟠 HIGH     | School onboarding pipeline    | [ ]    |
| 🟠 HIGH     | Legal document automation     | [ ]    |
| 🟠 HIGH     | Design system documentation   | [ ]    |
| 🟠 HIGH     | Component library             | [ ]    |
| 🟡 MEDIUM   | AI predictive analytics       | [ ]    |
| 🟡 MEDIUM   | Voice assistant               | [ ]    |
| 🟡 MEDIUM   | Referral program              | [ ]    |
| 🟢 LOW      | Blockchain transcripts        | [ ]    |
| 🟢 LOW      | IoT smart classroom           | [ ]    |
| 🟢 LOW      | Alumni career tracking        | [ ]    |

---

**Verdant SMS v4.0 — AI-Powered, Enterprise-Ready, Nation-Scale** 🚀🤖🇳🇬

**Built with Intelligence. Designed for Excellence. Scaled for Africa.**

**The Future of African Education Management Starts Here.**
