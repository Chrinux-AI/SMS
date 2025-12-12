-- Verdant SMS v3.0 Enhanced Features Schema
-- Polls, Study Groups, Widgets, and Community Features

-- =============================================
-- POLLS SYSTEM
-- =============================================

CREATE TABLE IF NOT EXISTS `polls` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `question` VARCHAR(500) NOT NULL,
    `created_by` INT NOT NULL,
    `status` ENUM('active', 'closed', 'draft') DEFAULT 'active',
    `expires_at` DATETIME NULL,
    `is_anonymous` TINYINT(1) DEFAULT 0,
    `allow_multiple` TINYINT(1) DEFAULT 0,
    `target_role` VARCHAR(50) NULL COMMENT 'Limit to specific role',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_polls_status` (`status`),
    INDEX `idx_polls_created_by` (`created_by`),
    INDEX `idx_polls_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `poll_options` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `poll_id` INT NOT NULL,
    `option_text` VARCHAR(255) NOT NULL,
    `display_order` INT DEFAULT 0,
    FOREIGN KEY (`poll_id`) REFERENCES `polls`(`id`) ON DELETE CASCADE,
    INDEX `idx_poll_options_poll` (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `poll_votes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `poll_id` INT NOT NULL,
    `option_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_vote` (`poll_id`, `user_id`),
    FOREIGN KEY (`poll_id`) REFERENCES `polls`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`option_id`) REFERENCES `poll_options`(`id`) ON DELETE CASCADE,
    INDEX `idx_poll_votes_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- STUDY GROUPS
-- =============================================

CREATE TABLE IF NOT EXISTS `study_groups` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(100) NULL,
    `description` TEXT NULL,
    `emoji` VARCHAR(10) DEFAULT '📚',
    `is_public` TINYINT(1) DEFAULT 1,
    `max_members` INT DEFAULT 20,
    `created_by` INT NOT NULL,
    `status` ENUM('active', 'archived') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_study_groups_public` (`is_public`),
    INDEX `idx_study_groups_status` (`status`),
    INDEX `idx_study_groups_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `study_group_members` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `group_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `role` ENUM('admin', 'moderator', 'member') DEFAULT 'member',
    `joined_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_membership` (`group_id`, `user_id`),
    FOREIGN KEY (`group_id`) REFERENCES `study_groups`(`id`) ON DELETE CASCADE,
    INDEX `idx_study_group_members_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `study_group_sessions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `group_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `scheduled_at` DATETIME NOT NULL,
    `duration_minutes` INT DEFAULT 60,
    `meeting_link` VARCHAR(500) NULL,
    `created_by` INT NOT NULL,
    `status` ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`group_id`) REFERENCES `study_groups`(`id`) ON DELETE CASCADE,
    INDEX `idx_study_sessions_group` (`group_id`),
    INDEX `idx_study_sessions_scheduled` (`scheduled_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `study_group_resources` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `group_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `resource_type` ENUM('link', 'file', 'note') DEFAULT 'link',
    `resource_url` VARCHAR(500) NULL,
    `file_path` VARCHAR(500) NULL,
    `uploaded_by` INT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`group_id`) REFERENCES `study_groups`(`id`) ON DELETE CASCADE,
    INDEX `idx_study_resources_group` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DASHBOARD WIDGETS
-- =============================================

CREATE TABLE IF NOT EXISTS `user_widgets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `widget_id` VARCHAR(50) NOT NULL,
    `position` INT DEFAULT 0,
    `is_visible` TINYINT(1) DEFAULT 1,
    `settings` JSON NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_user_widget` (`user_id`, `widget_id`),
    INDEX `idx_user_widgets_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- PUSH NOTIFICATIONS
-- =============================================

CREATE TABLE IF NOT EXISTS `push_subscriptions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `endpoint` VARCHAR(500) NOT NULL,
    `p256dh_key` VARCHAR(200) NOT NULL,
    `auth_key` VARCHAR(100) NOT NULL,
    `user_agent` VARCHAR(500) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `last_used` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_endpoint` (`endpoint`(255)),
    INDEX `idx_push_subscriptions_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- ONLINE USERS TRACKING
-- =============================================

CREATE TABLE IF NOT EXISTS `user_sessions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `session_id` VARCHAR(128) NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(500) NULL,
    `last_activity` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `page_url` VARCHAR(500) NULL,
    UNIQUE KEY `unique_session` (`session_id`),
    INDEX `idx_user_sessions_user` (`user_id`),
    INDEX `idx_user_sessions_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- ONBOARDING / TOURS
-- =============================================

CREATE TABLE IF NOT EXISTS `user_onboarding` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `completed_tours` JSON NULL COMMENT 'List of completed tour IDs',
    `dismissed_tips` JSON NULL COMMENT 'List of dismissed tip IDs',
    `onboarding_complete` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_onboarding_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- AI COPILOT LEARNING
-- =============================================

CREATE TABLE IF NOT EXISTS `ai_command_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `command` VARCHAR(500) NOT NULL,
    `intent` VARCHAR(50) NULL,
    `was_successful` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ai_commands_user` (`user_id`),
    INDEX `idx_ai_commands_intent` (`intent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- THEME PREFERENCES (if not already in users table)
-- =============================================

-- Ensure users table has theme column
-- ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `theme` VARCHAR(50) DEFAULT 'dark-cyber';

-- =============================================
-- SAMPLE DATA
-- =============================================

-- Insert sample poll
INSERT INTO `polls` (`question`, `created_by`, `status`, `expires_at`) VALUES
('What subject should we focus on for the upcoming study session?', 1, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY))
ON DUPLICATE KEY UPDATE `question` = VALUES(`question`);

-- Get the poll ID
SET @poll_id = LAST_INSERT_ID();

-- Insert poll options (only if poll was inserted)
INSERT IGNORE INTO `poll_options` (`poll_id`, `option_text`, `display_order`) VALUES
(@poll_id, 'Mathematics', 1),
(@poll_id, 'Science', 2),
(@poll_id, 'History', 3),
(@poll_id, 'English', 4);

-- Insert sample study group
INSERT INTO `study_groups` (`name`, `subject`, `description`, `emoji`, `is_public`, `created_by`) VALUES
('Math Masters', 'Mathematics', 'Study group for advanced mathematics topics', '🧮', 1, 1),
('Science Squad', 'Science', 'Explore physics, chemistry, and biology together', '🔬', 1, 1),
('History Buffs', 'History', 'Discuss historical events and prepare for exams', '📜', 1, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- =============================================
-- CLEANUP PROCEDURES
-- =============================================

-- Clean up old sessions (run daily)
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `cleanup_old_sessions`()
BEGIN
    DELETE FROM `user_sessions` WHERE `last_activity` < DATE_SUB(NOW(), INTERVAL 24 HOUR);
END //
DELIMITER ;

-- Close expired polls (run hourly)
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `close_expired_polls`()
BEGIN
    UPDATE `polls` SET `status` = 'closed'
    WHERE `status` = 'active' AND `expires_at` IS NOT NULL AND `expires_at` < NOW();
END //
DELIMITER ;
