-- Enhanced Chat System - WhatsApp/Telegram Style
-- Complete messaging platform with voice notes, calls, and real-time features
-- Verdant SMS v3.0+

-- Enhanced conversations table
CREATE TABLE IF NOT EXISTS chat_conversations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_type ENUM('direct', 'group', 'channel') DEFAULT 'direct',
    name VARCHAR(255) NULL COMMENT 'For groups/channels',
    description TEXT NULL,
    avatar_url VARCHAR(500) NULL,
    created_by INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_archived BOOLEAN DEFAULT FALSE,
    last_message_at TIMESTAMP NULL,
    INDEX idx_type (conversation_type),
    INDEX idx_created_by (created_by),
    INDEX idx_last_message (last_message_at),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Conversation participants
CREATE TABLE IF NOT EXISTS chat_participants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    role ENUM('admin', 'member') DEFAULT 'member',
    nickname VARCHAR(100) NULL,
    is_muted BOOLEAN DEFAULT FALSE,
    muted_until TIMESTAMP NULL,
    last_read_at TIMESTAMP NULL,
    is_archived BOOLEAN DEFAULT FALSE,
    is_pinned BOOLEAN DEFAULT FALSE,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_conversation (conversation_id),
    INDEX idx_user (user_id),
    INDEX idx_pinned (is_pinned),
    UNIQUE KEY unique_participant (conversation_id, user_id),
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages with all types
CREATE TABLE IF NOT EXISTS chat_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NOT NULL,
    sender_id INT UNSIGNED NOT NULL,
    reply_to_id BIGINT UNSIGNED NULL COMMENT 'For reply threads',
    message_type ENUM('text', 'image', 'video', 'audio', 'voice_note', 'document', 'location', 'contact', 'sticker', 'system') NOT NULL DEFAULT 'text',
    content TEXT NULL COMMENT 'Text content or caption',
    media_url VARCHAR(500) NULL,
    media_thumbnail VARCHAR(500) NULL,
    media_size INT UNSIGNED NULL COMMENT 'Bytes',
    media_duration INT UNSIGNED NULL COMMENT 'Seconds for audio/video',
    metadata JSON NULL COMMENT 'Additional data (filename, coordinates, etc.)',
    is_edited BOOLEAN DEFAULT FALSE,
    edited_at TIMESTAMP NULL,
    is_deleted BOOLEAN DEFAULT FALSE,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_conversation_created (conversation_id, created_at),
    INDEX idx_sender (sender_id),
    INDEX idx_reply_to (reply_to_id),
    INDEX idx_type (message_type),
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reply_to_id) REFERENCES chat_messages(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Read receipts
CREATE TABLE IF NOT EXISTS chat_read_receipts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    message_id BIGINT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_message (message_id),
    INDEX idx_user (user_id),
    UNIQUE KEY unique_read (message_id, user_id),
    FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Message reactions
CREATE TABLE IF NOT EXISTS chat_message_reactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    message_id BIGINT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    reaction VARCHAR(50) NOT NULL COMMENT 'emoji',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_message (message_id),
    INDEX idx_user (user_id),
    UNIQUE KEY unique_reaction (message_id, user_id, reaction),
    FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Typing indicators
CREATE TABLE IF NOT EXISTS chat_typing_indicators (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    is_typing BOOLEAN DEFAULT TRUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_conversation (conversation_id),
    INDEX idx_user (user_id),
    INDEX idx_updated (updated_at),
    UNIQUE KEY unique_typing (conversation_id, user_id),
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Voice notes
CREATE TABLE IF NOT EXISTS chat_voice_notes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    message_id BIGINT UNSIGNED NOT NULL,
    audio_url VARCHAR(500) NOT NULL,
    duration INT UNSIGNED NOT NULL COMMENT 'seconds',
    waveform_data TEXT NULL COMMENT 'JSON array for waveform visualization',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_message (message_id),
    FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Call logs
CREATE TABLE IF NOT EXISTS chat_calls (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NOT NULL,
    caller_id INT UNSIGNED NOT NULL,
    call_type ENUM('voice', 'video') NOT NULL,
    status ENUM('initiated', 'ringing', 'answered', 'missed', 'rejected', 'ended') DEFAULT 'initiated',
    started_at TIMESTAMP NULL,
    ended_at TIMESTAMP NULL,
    duration INT UNSIGNED NULL COMMENT 'seconds',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_conversation (conversation_id),
    INDEX idx_caller (caller_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (caller_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Call participants
CREATE TABLE IF NOT EXISTS chat_call_participants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    call_id BIGINT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    joined_at TIMESTAMP NULL,
    left_at TIMESTAMP NULL,
    INDEX idx_call (call_id),
    INDEX idx_user (user_id),
    FOREIGN KEY (call_id) REFERENCES chat_calls(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Media files metadata
CREATE TABLE IF NOT EXISTS chat_media (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    message_id BIGINT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    thumbnail_path VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_message (message_id),
    INDEX idx_type (file_type),
    FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User online status (for presence)
CREATE TABLE IF NOT EXISTS chat_user_presence (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    is_online BOOLEAN DEFAULT FALSE,
    last_seen TIMESTAMP NULL,
    status ENUM('online', 'away', 'busy', 'offline') DEFAULT 'offline',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user (user_id),
    INDEX idx_online (is_online),
    INDEX idx_last_seen (last_seen),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Message search index (for full-text search)
CREATE TABLE IF NOT EXISTS chat_message_search (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    message_id BIGINT UNSIGNED NOT NULL,
    search_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FULLTEXT INDEX idx_search (search_text),
    FOREIGN KEY (message_id) REFERENCES chat_messages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Group settings
CREATE TABLE IF NOT EXISTS chat_group_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_setting (conversation_id, setting_key),
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Message forwarding (track forwarded messages)
CREATE TABLE IF NOT EXISTS chat_message_forwards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    original_message_id BIGINT UNSIGNED NOT NULL,
    forwarded_message_id BIGINT UNSIGNED NOT NULL,
    forwarded_by INT UNSIGNED NOT NULL,
    forwarded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_original (original_message_id),
    INDEX idx_forwarded (forwarded_message_id),
    FOREIGN KEY (original_message_id) REFERENCES chat_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (forwarded_message_id) REFERENCES chat_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (forwarded_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for performance
CREATE INDEX idx_conversations_last_message ON chat_conversations(last_message_at DESC);
CREATE INDEX idx_messages_conversation_created ON chat_messages(conversation_id, created_at DESC);
CREATE INDEX idx_participants_user_conversation ON chat_participants(user_id, conversation_id);
CREATE INDEX idx_read_receipts_user_message ON chat_read_receipts(user_id, message_id);


