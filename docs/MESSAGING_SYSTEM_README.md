# Verdant SMS - Real-Time Messaging System

## WhatsApp/Telegram Clone Implementation

Complete real-time messaging system with voice notes, video calling, and file sharing capabilities.

---

## 🎯 Features

### ✅ Core Messaging

- Real-time bidirectional communication via WebSockets
- Text messages with formatting
- Message delivery and read receipts
- Typing indicators
- Message reactions (emoji)
- Reply to messages
- Edit and delete messages
- Message search

### 🎤 Voice Notes

- Record voice messages (hold button)
- Audio waveform visualization
- Playback controls
- Duration display

### 📹 Video Calling

- WebRTC-based video/audio calls
- Screen sharing
- Call history
- Group video calls (up to 8 participants)

### 📎 File Sharing

- Upload and send files (up to 50MB)
- Image previews
- Document support (PDF, DOCX, etc.)
- Video and audio file previews
- Multiple files per message

### 👥 Group Chats

- Create and manage groups
- Group admins and members
- Group info and settings
- Group avatars

### 🔔 Notifications

- Desktop notifications
- Sound alerts
- Unread message counter
- Notification settings

### 🔒 Security

- End-to-end message encryption (optional)
- User blocking
- Report/mute conversations
- Secure file uploads

---

## 📁 File Structure

```
/opt/lampp/htdocs/attendance/
│
├── server/
│   └── websocket-chat-server.php      # WebSocket server (Port 8080)
│
├── assets/
│   └── js/
│       └── verdant-chat-client.js     # Frontend JavaScript client
│
├── includes/
│   └── chat-widget.php                # UI component (include in pages)
│
├── database/
│   └── messaging_system_schema.sql    # Database schema (11 tables)
│
├── scripts/
│   ├── install-messaging-system.sh    # Installation script
│   ├── start-chat-server.sh           # Start server
│   └── stop-chat-server.sh            # Stop server
│
└── uploads/
    ├── voice_notes/                   # Voice note storage
    ├── chat_files/                    # File uploads
    └── chat_thumbnails/               # Image thumbnails
```

---

## 🚀 Installation

### Step 1: Install Dependencies

```bash
cd /opt/lampp/htdocs/attendance
./scripts/install-messaging-system.sh
```

This script will:

- Create database tables (11 tables)
- Install PHP Ratchet library
- Create upload directories
- Set proper permissions

### Step 2: Verify Installation

```bash
# Check database tables
mysql -u root --socket=/opt/lampp/var/mysql/mysql.sock attendance_system -e "SHOW TABLES LIKE 'chat_%'"

# Check Ratchet installation
composer show cboden/ratchet

# Check directories
ls -la uploads/
```

### Step 3: Start WebSocket Server

```bash
# Start server
./scripts/start-chat-server.sh

# Check if running
ps aux | grep websocket-chat-server

# Check logs
tail -f logs/chat-server.log
```

### Step 4: Include Chat Widget in Pages

Add to any PHP page:

```php
<?php include '../includes/chat-widget.php'; ?>
```

Or add before closing `</body>` tag:

```php
<!-- Chat Widget -->
<?php
if (isset($_SESSION['user_id'])) {
    include '../includes/chat-widget.php';
}
?>
```

---

## 🔧 Configuration

### WebSocket Server Settings

Edit `server/websocket-chat-server.php`:

```php
// Port configuration
$port = 8080; // Change if needed

// Database configuration (uses existing config)
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
```

### Frontend Configuration

Edit `assets/js/verdant-chat-client.js`:

```javascript
// WebSocket URL
this.ws = new WebSocket("ws://localhost:8080");

// Change for production:
// this.ws = new WebSocket('wss://yourdomain.com:8080');
```

---

## 📊 Database Schema

### 11 Tables Created

1. **conversations** - Chat conversations (direct/group)
2. **conversation_participants** - User memberships
3. **chat_messages** - All message content
4. **message_read_receipts** - Read status tracking
5. **message_reactions** - Emoji reactions
6. **video_call_sessions** - Call metadata
7. **call_participants** - Call attendees
8. **message_attachments** - File attachments
9. **user_online_status** - Online/offline status
10. **blocked_users** - User blocking
11. **message_delivery_status** - Delivery tracking

### Schema Diagram

```
conversations
├── conversation_participants → users
├── chat_messages
│   ├── message_read_receipts → users
│   ├── message_reactions → users
│   ├── message_attachments
│   └── message_delivery_status → users
└── video_call_sessions
    └── call_participants → users
```

---

## 🎮 Usage Examples

### Send Text Message

```javascript
chatClient.sendMessage(conversationId, "Hello World!");
```

### Send Voice Note

```javascript
// Hold button to record
document
  .getElementById("voice-record-btn")
  .addEventListener("mousedown", () => {
    chatClient.startVoiceRecording();
  });

// Release to send
document.getElementById("voice-record-btn").addEventListener("mouseup", () => {
  chatClient.stopVoiceRecording();
});
```

### Initiate Video Call

```javascript
// Video call
chatClient.initiateVideoCall(conversationId, "video");

// Audio call only
chatClient.initiateVideoCall(conversationId, "audio");
```

### Upload File

```javascript
document.getElementById("file-input").addEventListener("change", (e) => {
  const files = e.target.files;
  for (let file of files) {
    chatClient.uploadFile(file);
  }
});
```

### React to Message

```javascript
chatClient.sendReaction(messageId, "👍");
```

---

## 🔌 WebSocket API

### Message Types

#### Authentication

```json
{
  "type": "auth",
  "user_id": 123,
  "name": "John Doe",
  "role": "student",
  "token": "auth_token_here"
}
```

#### Send Message

```json
{
  "type": "message",
  "conversation_id": 456,
  "content": "Hello!",
  "reply_to": null
}
```

#### Voice Note

```json
{
  "type": "voice_note",
  "conversation_id": 456,
  "audio_data": "base64_encoded_audio",
  "duration": 5
}
```

#### Video Call

```json
{
  "type": "video_call",
  "action": "initiate",
  "conversation_id": 456,
  "call_type": "video"
}
```

#### File Upload

```json
{
  "type": "file",
  "conversation_id": 456,
  "file_data": "base64_encoded_file",
  "file_name": "document.pdf",
  "file_size": 102400,
  "mime_type": "application/pdf"
}
```

#### Typing Indicator

```json
{
  "type": "typing",
  "conversation_id": 456
}
```

#### Read Receipt

```json
{
  "type": "read_receipt",
  "message_id": 789
}
```

#### Reaction

```json
{
  "type": "reaction",
  "message_id": 789,
  "emoji": "👍"
}
```

---

## 🐛 Troubleshooting

### Server Won't Start

```bash
# Check if port is already in use
lsof -i :8080

# Kill existing process
kill -9 $(lsof -t -i:8080)

# Restart server
./scripts/start-chat-server.sh
```

### Connection Issues

1. **Check server is running**:

   ```bash
   ps aux | grep websocket-chat-server
   ```

2. **Check logs**:

   ```bash
   tail -f logs/chat-server.log
   ```

3. **Test WebSocket connection**:
   ```bash
   curl --include \
        --no-buffer \
        --header "Connection: Upgrade" \
        --header "Upgrade: websocket" \
        --header "Sec-WebSocket-Key: SGVsbG8sIHdvcmxkIQ==" \
        --header "Sec-WebSocket-Version: 13" \
        http://localhost:8080/
   ```

### Database Issues

```bash
# Check tables exist
mysql -u root --socket=/opt/lampp/var/mysql/mysql.sock attendance_system -e "SHOW TABLES LIKE 'chat_%'"

# Re-import schema
mysql -u root --socket=/opt/lampp/var/mysql/mysql.sock attendance_system < database/messaging_system_schema.sql
```

### File Upload Issues

```bash
# Check directory permissions
ls -la uploads/

# Fix permissions
chmod 755 uploads/voice_notes
chmod 755 uploads/chat_files
chmod 755 uploads/chat_thumbnails
```

---

## 🚀 Production Deployment

### 1. Use Secure WebSocket (WSS)

Install SSL certificate and configure nginx:

```nginx
server {
    listen 443 ssl;
    server_name yourdomain.com;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    location /chat {
        proxy_pass http://localhost:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

### 2. Use Process Manager

Install and configure Supervisor:

```bash
sudo apt-get install supervisor
```

Create `/etc/supervisor/conf.d/verdant-chat.conf`:

```ini
[program:verdant-chat-server]
command=/usr/bin/php /opt/lampp/htdocs/attendance/server/websocket-chat-server.php
autostart=true
autorestart=true
stderr_logfile=/var/log/verdant-chat.err.log
stdout_logfile=/var/log/verdant-chat.out.log
user=www-data
```

Start:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start verdant-chat-server
```

### 3. Set Up Log Rotation

Create `/etc/logrotate.d/verdant-chat`:

```
/opt/lampp/htdocs/attendance/logs/chat-server.log {
    daily
    rotate 7
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
```

---

## 📈 Performance Tips

1. **Use Redis for sessions** (optional):

   ```bash
   composer require predis/predis
   ```

2. **Enable message caching**
3. **Implement message pagination** (load 50 messages at a time)
4. **Compress file uploads**
5. **Use CDN for uploaded files**

---

## 🔐 Security Considerations

1. **Validate all user input**
2. **Sanitize file uploads**
3. **Implement rate limiting**
4. **Use CSRF tokens**
5. **Enable CORS properly**
6. **Encrypt sensitive data**
7. **Regular security audits**

---

## 📝 API Documentation

Full API documentation available at:
`docs/MESSAGING_API.md`

---

## 🤝 Support

For issues or questions:

1. Check logs: `logs/chat-server.log`
2. Check database: `mysql -u root ... attendance_system`
3. Review configuration files
4. Contact system administrator

---

## 📄 License

Part of Verdant SMS - School Management System
© 2025 All Rights Reserved

---

## ✨ Credits

Built using:

- **Ratchet** - WebSocket library for PHP
- **WebRTC** - Real-time video/audio communication
- **MediaRecorder API** - Voice note recording

---

**Last Updated**: December 30, 2024
**Version**: 1.0.0
**Status**: Production Ready ✅
