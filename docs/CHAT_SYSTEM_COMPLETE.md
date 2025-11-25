# Enhanced Chat System - WhatsApp/Telegram Style

**Status**: ✅ COMPLETE
**Date**: December 2024

---

## Overview

The SAMS attendance system now includes a **modern, real-time chat platform** similar to WhatsApp and Telegram, with advanced messaging features including:

- 💬 **Real-time messaging** with live updates
- 🔄 **Message threading** and replies
- 😊 **Emoji reactions** on messages
- 📎 **File attachments** (images, documents)
- ✏️ **Message editing** and deletion
- ✅ **Read receipts** (delivered/read status)
- 👁️ **Typing indicators**
- 🟢 **Online/offline status**
- 📱 **Mobile-responsive design**
- 🔍 **User search** and conversation filtering
- 👥 **Group chats** support
- 🔔 **Push notifications** (via PWA)

---

## Features Breakdown

### 1. Real-Time Messaging

- **Live message updates** every 3 seconds
- **Instant delivery** with status tracking
- **Auto-scroll** to latest messages
- **Conversation list** with unread counts
- **Last message preview** in conversation list

### 2. Message Threading & Replies

- Reply to specific messages
- Visual reply indicators
- Thread context preservation
- Quick reply navigation

### 3. Reactions & Engagement

- Click to add emoji reactions (👍 ❤️ 😂 😮 😢 🙏)
- Multiple reactions per message
- Reaction counts with user lists
- Toggle reactions on/off

### 4. File Sharing

- Upload multiple files
- Support for images, PDFs, documents
- File preview and download
- Attachment size tracking

### 5. Message Management

- **Edit messages** (shows "Edited" indicator)
- **Delete messages** (soft delete with "[Message deleted]" placeholder)
- **Edit history** tracking
- **Context menu** for message actions

### 6. Status & Presence

- **Online/Offline indicators**
- **Last seen** timestamps
- **Typing indicators** ("John is typing...")
- **Read receipts** (single/double checkmarks)

### 7. User Experience

- **Cyberpunk-themed UI** matching SAMS design
- **Smooth animations** and transitions
- **Responsive layout** (desktop & mobile)
- **Empty states** with helpful prompts
- **Loading indicators**

---

## Database Schema

### New Tables Created

1. **`conversations`** - Chat conversation threads

   - `id`, `subject`, `started_by`, `participants` (JSON), `is_group`, `last_message_at`
   - Supports 1-on-1 and group chats

2. **`conversation_messages`** - Individual messages

   - `id`, `conversation_id`, `sender_id`, `message_text`, `attachments`, `is_read_by` (JSON)
   - Tracks message content and read status

3. **`conversation_participants`** - User participation

   - `id`, `conversation_id`, `user_id`, `last_read_at`, `is_muted`, `is_archived`, `is_pinned`
   - Per-user conversation settings

4. **`message_reactions`** - Emoji reactions

   - `id`, `message_id`, `user_id`, `reaction`
   - Track who reacted with what

5. **`message_attachments`** - File uploads

   - `id`, `message_id`, `file_name`, `file_path`, `file_type`, `file_size`
   - Stores attachment metadata

6. **`typing_indicators`** - Real-time typing status

   - `id`, `user_id`, `conversation_id`, `is_typing`, `updated_at`
   - Auto-expires after 10 seconds

7. **`user_online_status`** - Presence tracking

   - `user_id`, `is_online`, `last_seen`, `last_activity`
   - Updates every 30 seconds

8. **`message_edit_history`** - Edit tracking

   - `id`, `message_id`, `original_text`, `edited_at`
   - Preserves original message content

9. **`message_delivery_status`** - Delivery tracking

   - `id`, `message_id`, `recipient_id`, `status`, `delivered_at`, `read_at`
   - Tracks sent/delivered/read states

10. **`voice_messages`** - Voice note support

    - `id`, `message_id`, `file_path`, `duration_seconds`, `waveform_data` (JSON)
    - Ready for future voice messaging

11. **`message_mentions`** - @username mentions

    - `id`, `message_id`, `mentioned_user_id`
    - Track user mentions in messages

12. **`message_forwards`** - Message forwarding
    - `id`, `original_message_id`, `forwarded_message_id`, `forwarded_by_user_id`
    - Track forwarded messages

---

## API Endpoints

### `/api/chat.php`

All endpoints require authentication via session.

#### GET Endpoints

| Action              | Parameters                              | Description                                          |
| ------------------- | --------------------------------------- | ---------------------------------------------------- |
| `get_conversations` | -                                       | List all user's conversations with unread counts     |
| `get_messages`      | `conversation_id`, `limit`, `before_id` | Get messages for a conversation (pagination support) |
| `get_typing`        | `conversation_id`                       | Get users currently typing                           |
| `search_users`      | `q` (query string)                      | Search for users to start new chat                   |
| `get_online_status` | `user_ids` (JSON array)                 | Get online status for multiple users                 |

#### POST Endpoints

| Action            | Parameters                                                                           | Description                               |
| ----------------- | ------------------------------------------------------------------------------------ | ----------------------------------------- |
| `send_message`    | `conversation_id`, `message`, `recipient_id`, `reply_to_message_id`, `attachments[]` | Send a new message                        |
| `mark_as_read`    | `conversation_id`                                                                    | Mark all messages in conversation as read |
| `add_reaction`    | `message_id`, `reaction`                                                             | Add emoji reaction to message             |
| `remove_reaction` | `message_id`, `reaction`                                                             | Remove emoji reaction                     |
| `typing`          | `conversation_id`, `is_typing`                                                       | Update typing indicator                   |
| `delete_message`  | `message_id`                                                                         | Delete a message (sender only)            |
| `edit_message`    | `message_id`, `message_text`                                                         | Edit a message (sender only)              |
| `create_group`    | `group_name`, `participant_ids` (JSON)                                               | Create group conversation                 |

---

## File Structure

```
/attendance/
├── chat.php                              # Main chat interface
├── messages.php                          # Redirects to chat.php
├── api/
│   └── chat.php                          # Chat API endpoints
├── database/
│   └── migrations/
│       ├── create_communication_platform.sql    # Base tables
│       └── upgrade_chat_system.sql              # Enhanced features
└── uploads/
    └── chat_attachments/                 # File upload directory
```

---

## Usage Guide

### For Users

#### Starting a New Chat

1. Click the **"+" button** (bottom right)
2. Search for user by name or email
3. Select user and start chatting

#### Sending Messages

- Type in the input box at bottom
- Press **Enter** to send (Shift+Enter for new line)
- Click **paperclip icon** to attach files
- Click **smile icon** for emoji picker

#### Replying to Messages

- Right-click on a message
- Select "Reply"
- Your reply will show context of original message

#### Adding Reactions

- Click on a message
- Select an emoji reaction
- Click again to remove your reaction

#### Editing Messages

- Right-click on your own message
- Select "Edit"
- Make changes and save
- Message will show "Edited" indicator

#### Deleting Messages

- Right-click on your own message
- Select "Delete"
- Message will be replaced with "[Message deleted]"

### For Administrators

#### Monitoring Conversations

```sql
-- Get most active conversations
SELECT c.*, COUNT(cm.id) as message_count
FROM conversations c
LEFT JOIN conversation_messages cm ON c.id = cm.conversation_id
GROUP BY c.id
ORDER BY message_count DESC
LIMIT 10;
```

#### User Engagement Stats

```sql
-- Get user message counts
SELECT u.first_name, u.last_name, u.role,
       COUNT(cm.id) as messages_sent
FROM users u
LEFT JOIN conversation_messages cm ON u.id = cm.sender_id
GROUP BY u.id
ORDER BY messages_sent DESC;
```

#### Cleanup Old Typing Indicators

```sql
-- Remove stale typing indicators (older than 1 minute)
DELETE FROM typing_indicators
WHERE updated_at < DATE_SUB(NOW(), INTERVAL 1 MINUTE);
```

---

## Technical Implementation

### Real-Time Updates

The system uses **polling** (every 3 seconds) to simulate real-time updates:

```javascript
setInterval(() => {
  if (currentConversationId) {
    loadMessages(currentConversationId);
    checkTypingIndicator();
  }
  loadConversations();
}, 3000);
```

**Note**: For true real-time, consider upgrading to WebSockets (Socket.io or Pusher).

### Online Status Tracking

User online status is updated:

- On every API call (automatic via `updateOnlineStatus()`)
- Every 30 seconds (heartbeat)
- Set to offline after 2 minutes of inactivity

```php
function updateOnlineStatus($user_id) {
    db()->execute("
        INSERT INTO user_online_status (user_id, is_online, last_seen, last_activity)
        VALUES (?, 1, NOW(), NOW())
        ON DUPLICATE KEY UPDATE is_online = 1, last_activity = NOW()
    ", [$user_id]);
}
```

### Read Receipts

Messages track read status using JSON array:

```json
{
  "is_read_by": [1, 5, 12] // User IDs who have read the message
}
```

Double checkmark (✓✓) turns green when all recipients have read.

### Typing Indicators

Typing status is sent on every keystroke with auto-timeout:

```javascript
function handleTyping() {
  clearTimeout(typingTimeout);
  sendTypingIndicator(true);

  typingTimeout = setTimeout(() => {
    sendTypingIndicator(false);
  }, 3000);
}
```

Backend filters typing indicators older than 10 seconds.

---

## Security Features

### 1. Authentication

- All API endpoints require valid session
- User ID verified on every request

### 2. Authorization

- Users can only edit/delete their own messages
- Conversation access checked via participants table
- File uploads validated by type and size

### 3. Input Sanitization

- All text inputs escaped before display
- File names sanitized to prevent directory traversal
- SQL injection prevented via prepared statements

### 4. XSS Protection

```javascript
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}
```

---

## Performance Optimizations

### 1. Database Indexes

All tables have appropriate indexes:

- `idx_conversation` on conversation_id
- `idx_sender` on sender_id
- `idx_created` on created_at
- `idx_online` on is_online status

### 2. Pagination

Messages loaded in batches (default 50):

```javascript
loadMessages(conversationId, (beforeId = 0));
```

Load more by passing last message ID.

### 3. Caching

- Conversation list cached for 3 seconds (polling interval)
- User online status cached for 30 seconds
- Message reactions fetched with messages (no separate queries)

### 4. Efficient Queries

```sql
-- Single query gets messages with reactions and attachments
SELECT cm.*, u.first_name, u.last_name,
       (SELECT JSON_ARRAYAGG(JSON_OBJECT(
           'reaction', mr.reaction,
           'user_id', mr.user_id
       )) FROM message_reactions mr WHERE mr.message_id = cm.id) as reactions
FROM conversation_messages cm
JOIN users u ON cm.sender_id = u.id
WHERE cm.conversation_id = ?
```

---

## Migration & Deployment

### Step 1: Run Database Migrations

```bash
# Create base communication tables
mysql -u root attendance_system < database/migrations/create_communication_platform.sql

# Add enhanced chat features
mysql -u root attendance_system < database/migrations/upgrade_chat_system.sql
```

### Step 2: Create Upload Directory

```bash
mkdir -p uploads/chat_attachments
chmod 755 uploads/chat_attachments
```

### Step 3: Configure File Uploads

Edit `php.ini`:

```ini
upload_max_filesize = 20M
post_max_size = 25M
max_file_uploads = 10
```

### Step 4: Test

1. Login as any user
2. Navigate to `/attendance/chat.php`
3. Start a conversation with another user
4. Send messages, reactions, attachments

---

## Future Enhancements

### Planned Features

1. **WebSocket Integration**

   - Replace polling with Socket.io
   - Instant message delivery
   - Reduced server load

2. **Voice Messages**

   - Record and send audio
   - Waveform visualization
   - Playback controls

3. **Video Calls**

   - WebRTC integration
   - 1-on-1 video chat
   - Screen sharing

4. **Advanced Search**

   - Full-text message search
   - Filter by date, user, attachments
   - Search within conversations

5. **Message Pinning**

   - Pin important messages
   - Quick access to pinned items
   - Announcement pinning

6. **Rich Media**

   - Image preview galleries
   - Video thumbnails
   - Link previews with metadata

7. **Custom Emoji**

   - School-specific emoji
   - Animated reactions
   - Emoji shortcuts

8. **Scheduled Messages**
   - Send messages at specific time
   - Recurring announcements
   - Reminder messages

---

## Troubleshooting

### Issue: Messages not loading

**Solution**:

1. Check browser console for errors
2. Verify API endpoint: `api/chat.php?action=get_conversations`
3. Check database connection in `includes/config.php`

### Issue: File uploads failing

**Solution**:

1. Check `uploads/chat_attachments/` directory exists
2. Verify permissions: `chmod 755 uploads/chat_attachments`
3. Check PHP upload settings in `php.ini`

### Issue: Typing indicator not showing

**Solution**:

1. Verify polling is running (check console)
2. Check `typing_indicators` table has entries
3. Ensure typing cleanup cron is not too aggressive

### Issue: Read receipts not updating

**Solution**:

1. Check `is_read_by` column is JSON type
2. Verify `markConversationAsRead()` function is called
3. Check participant's `last_read_at` timestamp

---

## Comparison with WhatsApp/Telegram

| Feature               | WhatsApp | Telegram | SAMS Chat | Status                      |
| --------------------- | -------- | -------- | --------- | --------------------------- |
| Real-time messaging   | ✅       | ✅       | ✅        | Complete                    |
| Message threads       | ❌       | ✅       | ✅        | Complete                    |
| Reactions             | ✅       | ✅       | ✅        | Complete                    |
| File sharing          | ✅       | ✅       | ✅        | Complete                    |
| Voice messages        | ✅       | ✅       | 🚧        | Planned                     |
| Video calls           | ✅       | ✅       | 🚧        | Planned                     |
| Group chats           | ✅       | ✅       | ✅        | Complete                    |
| Typing indicators     | ✅       | ✅       | ✅        | Complete                    |
| Online status         | ✅       | ✅       | ✅        | Complete                    |
| Read receipts         | ✅       | ✅       | ✅        | Complete                    |
| Message editing       | ❌       | ✅       | ✅        | Complete                    |
| Message forwarding    | ✅       | ✅       | 🚧        | Planned                     |
| Channels              | ❌       | ✅       | 🚧        | Planned                     |
| Bots                  | ❌       | ✅       | ✅        | Has SAMS Bot                |
| End-to-end encryption | ✅       | ⚠️       | ❌        | Not needed (school network) |

---

## Credits

**Developed by**: SAMS Development Team
**Design Pattern**: WhatsApp Web + Telegram Web
**UI Framework**: Custom Cyberpunk Theme
**Database**: MySQL 8.0+
**Backend**: PHP 8.x
**Frontend**: Vanilla JavaScript (ES6+)

---

## Support

For issues or feature requests:

1. Check this documentation
2. Review database migration logs
3. Check browser console for errors
4. Contact system administrator

---

**Last Updated**: December 2024
**Version**: 1.0.0
**License**: Proprietary (SAMS School Management System)
