-- ============================================================================
-- VERDANT SMS - CHAT SYSTEM DATABASE SCHEMA
-- WhatsApp/Telegram-style messaging with voice notes and calling
-- Version: 1.0.0
-- ============================================================================

-- Conversations (1-on-1, groups, broadcasts)
CREATE TABLE IF NOT EXISTS chat_conversations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('direct', 'group', 'broadcast', 'class', 'announcement') NOT NULL DEFAULT 'direct',
    name VARCHAR(200) NULL COMMENT 'Group name (null for direct chats)',
    description TEXT NULL,
    avatar_url VARCHAR(500) NULL,
    created_by INT NOT NULL,
    is_archived BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_created_by (created_by),
    INDEX idx_updated_at (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Conversation participants
CREATE TABLE IF NOT EXISTS chat_participants (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    conversation_id BIGINT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('admin', 'member') DEFAULT 'member',
    nickname VARCHAR(100) NULL,
    is_muted BOOLEAN DEFAULT FALSE,
    muted_until TIMESTAMP NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_read_at TIMESTAMP NULL,
    last_read_message_id BIGINT NULL,
    is_archived BOOLEAN DEFAULT FALSE,
    is_pinned BOOLEAN DEFAULT FALSE,
    notification_enabled BOOLEAN DEFAULT TRUE,
    UNIQUE KEY unique_participant (conversation_id, user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_conversation_id (conversation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages
CREATE TABLE IF NOT EXISTS chat_messages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    conversation_id BIGINT NOT NULL,
    sender_id INT NOT NULL,
    reply_to_id BIGINT NULL COMMENT 'For reply threads',
    message_type ENUM('text', 'image', 'video', 'audio', 'voice_note', 'document', 'location', 'contact', 'sticker', 'system') NOT NULL DEFAULT 'text',
    content TEXT NULL COMMENT 'Text content or caption',
    media_url VARCHAR(500) NULL,
    media_thumbnail VARCHAR(500) NULL,
    media_filename VARCHAR(255) NULL,
    media_size INT NULL COMMENT 'File size in bytes',
    media_duration INT NULL COMMENT 'Duration in seconds for audio/video',
    media_mimetype VARCHAR(100) NULL,
    metadata JSON NULL COMMENT 'Additional data (coordinates, etc.)',
    is_forwarded BOOLEAN DEFAULT FALSE,
    forwarded_from_id BIGINT NULL,
    is_edited BOOLEAN DEFAULT FALSE,
    edited_at TIMESTAMP NULL,
    is_deleted BOOLEAN DEFAULT FALSE,
    deleted_at TIMESTAMP NULL,
    deleted_for_everyone BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_conversation_created (conversation_id, created_at),
    INDEX idx_sender_id (sender_id),
    INDEX idx_message_type (message_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Message delivery status (sent, delivered, read)
CREATE TABLE IF NOT EXISTS chat_message_status (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    message_id BIGINT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('sent', 'delivered', 'read') DEFAULT 'sent',
    delivered_at TIMESTAMP NULL,
    read_at TIMESTAMP NULL,
    UNIQUE KEY unique_status (message_id, user_id),
    INDEX idx_message_id (message_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Message reactions (emoji)
CREATE TABLE IF NOT EXISTS chat_reactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    message_id BIGINT NOT NULL,
    user_id INT NOT NULL,
    emoji VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_reaction (message_id, user_id),
    INDEX idx_message_id (message_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Voice/Video calls
CREATE TABLE IF NOT EXISTS chat_calls (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    conversation_id BIGINT NOT NULL,
    caller_id INT NOT NULL,
    call_type ENUM('voice', 'video') NOT NULL,
    status ENUM('ringing', 'ongoing', 'ended', 'missed', 'declined', 'busy', 'failed') DEFAULT 'ringing',
    started_at TIMESTAMP NULL,
    ended_at TIMESTAMP NULL,
    duration INT NULL COMMENT 'Duration in seconds',
    end_reason VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_conversation_id (conversation_id),
    INDEX idx_caller_id (caller_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Call participants
CREATE TABLE IF NOT EXISTS chat_call_participants (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    call_id BIGINT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('ringing', 'joined', 'left', 'declined', 'missed', 'busy') DEFAULT 'ringing',
    joined_at TIMESTAMP NULL,
    left_at TIMESTAMP NULL,
    is_muted BOOLEAN DEFAULT FALSE,
    is_video_enabled BOOLEAN DEFAULT TRUE,
    UNIQUE KEY unique_call_participant (call_id, user_id),
    INDEX idx_call_id (call_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User online presence
CREATE TABLE IF NOT EXISTS chat_presence (
    user_id INT PRIMARY KEY,
    is_online BOOLEAN DEFAULT FALSE,
    last_seen_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status_text VARCHAR(200) NULL COMMENT 'Custom status message',
    status_emoji VARCHAR(10) NULL,
    typing_in_conversation_id BIGINT NULL,
    typing_started_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chat media uploads
CREATE TABLE IF NOT EXISTS chat_media (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    message_id BIGINT NULL,
    user_id INT NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    media_type ENUM('image', 'video', 'audio', 'voice_note', 'document') NOT NULL,
    thumbnail_path VARCHAR(500) NULL,
    duration INT NULL,
    width INT NULL,
    height INT NULL,
    is_processed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_message_id (message_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blocked users
CREATE TABLE IF NOT EXISTS chat_blocked_users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    blocked_user_id INT NOT NULL,
    reason VARCHAR(255) NULL,
    blocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_block (user_id, blocked_user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_blocked_user_id (blocked_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SAMPLE TRIGGERS AND PROCEDURES
-- ============================================================================

-- Trigger to update conversation updated_at when new message is sent
DELIMITER //
CREATE TRIGGER IF NOT EXISTS update_conversation_timestamp
AFTER INSERT ON chat_messages
FOR EACH ROW
BEGIN
    UPDATE chat_conversations
    SET updated_at = NOW()
    WHERE id = NEW.conversation_id;
END//
DELIMITER ;

-- Trigger to auto-create presence record when user is added
DELIMITER //
CREATE TRIGGER IF NOT EXISTS auto_create_presence
AFTER INSERT ON chat_participants
FOR EACH ROW
BEGIN
    INSERT IGNORE INTO chat_presence (user_id, is_online, last_seen_at)
    VALUES (NEW.user_id, FALSE, NOW());
END//
DELIMITER ;
