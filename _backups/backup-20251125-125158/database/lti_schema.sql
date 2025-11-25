-- LTI 1.3 Integration Database Schema
-- Created: November 24, 2025
-- Purpose: Support LMS integration via Learning Tools Interoperability standard

-- Table: lti_configurations
-- Stores LTI 1.3 configuration for different LMS platforms
CREATE TABLE IF NOT EXISTS `lti_configurations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `lms_platform` VARCHAR(50) NOT NULL COMMENT 'LMS type: moodle, canvas, blackboard, etc.',
  `platform_name` VARCHAR(255) NOT NULL COMMENT 'Friendly name for this LMS instance',
  `client_id` VARCHAR(255) NOT NULL COMMENT 'OAuth client ID from LMS',
  `issuer` VARCHAR(500) NOT NULL COMMENT 'LMS issuer URL',
  `auth_login_url` VARCHAR(500) NOT NULL COMMENT 'OIDC auth login endpoint',
  `auth_token_url` VARCHAR(500) NOT NULL COMMENT 'OAuth token endpoint',
  `key_set_url` VARCHAR(500) NOT NULL COMMENT 'JWKS endpoint for public keys',
  `deployment_id` VARCHAR(255) NOT NULL COMMENT 'Deployment identifier from LMS',
  `public_key` TEXT DEFAULT NULL COMMENT 'Tool public key (PEM format)',
  `private_key` TEXT DEFAULT NULL COMMENT 'Tool private key (PEM format)',
  `target_link_uri` VARCHAR(500) DEFAULT NULL COMMENT 'Default launch URL for this tool',
  `is_active` TINYINT(1) DEFAULT 1 COMMENT 'Whether this config is active',
  `sync_enabled` TINYINT(1) DEFAULT 1 COMMENT 'Enable auto-sync features',
  `grade_passback_enabled` TINYINT(1) DEFAULT 1 COMMENT 'Enable grade passback to LMS',
  `deep_linking_enabled` TINYINT(1) DEFAULT 1 COMMENT 'Enable deep linking support',
  `custom_params` TEXT DEFAULT NULL COMMENT 'JSON: Custom LTI parameters',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_platform_client` (`lms_platform`, `client_id`),
  INDEX `idx_platform` (`lms_platform`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='LTI 1.3 platform configurations';

-- Table: lti_sessions
-- Tracks LTI launch sessions for auditing and state management
CREATE TABLE IF NOT EXISTS `lti_sessions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `session_id` VARCHAR(255) NOT NULL COMMENT 'Unique session identifier',
  `config_id` INT(11) NOT NULL COMMENT 'FK to lti_configurations',
  `user_id` INT(11) DEFAULT NULL COMMENT 'FK to users table (after mapping)',
  `lms_user_id` VARCHAR(255) NOT NULL COMMENT 'User ID from LMS (sub claim)',
  `lms_context_id` VARCHAR(255) DEFAULT NULL COMMENT 'Course/context ID from LMS',
  `lms_resource_link_id` VARCHAR(255) DEFAULT NULL COMMENT 'Specific resource link ID',
  `lms_roles` TEXT DEFAULT NULL COMMENT 'JSON array of LTI roles',
  `launch_params` TEXT DEFAULT NULL COMMENT 'JSON: Full LTI launch parameters',
  `id_token` TEXT DEFAULT NULL COMMENT 'JWT ID token from launch',
  `access_token` TEXT DEFAULT NULL COMMENT 'OAuth access token for services',
  `token_expires_at` DATETIME DEFAULT NULL COMMENT 'When access token expires',
  `launch_presentation` TEXT DEFAULT NULL COMMENT 'JSON: Presentation claims',
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_session` (`session_id`),
  FOREIGN KEY (`config_id`) REFERENCES `lti_configurations`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_lms_user` (`lms_user_id`),
  INDEX `idx_context` (`lms_context_id`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='LTI launch sessions tracking';

-- Table: lti_resource_links
-- Maps LMS resources to internal SAMS resources for deep linking
CREATE TABLE IF NOT EXISTS `lti_resource_links` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `config_id` INT(11) NOT NULL COMMENT 'FK to lti_configurations',
  `resource_link_id` VARCHAR(255) NOT NULL COMMENT 'LMS resource link identifier',
  `context_id` VARCHAR(255) NOT NULL COMMENT 'Course/context from LMS',
  `resource_type` ENUM('attendance', 'report', 'assignment', 'grade', 'dashboard', 'custom') DEFAULT 'attendance',
  `internal_resource_id` INT(11) DEFAULT NULL COMMENT 'FK to internal resource (class_id, etc.)',
  `title` VARCHAR(500) DEFAULT NULL COMMENT 'Resource title',
  `description` TEXT DEFAULT NULL,
  `custom_params` TEXT DEFAULT NULL COMMENT 'JSON: Resource-specific params',
  `lineitem_url` VARCHAR(500) DEFAULT NULL COMMENT 'AGS lineitem URL for grade passback',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`config_id`) REFERENCES `lti_configurations`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_resource_link` (`config_id`, `resource_link_id`),
  INDEX `idx_context` (`context_id`),
  INDEX `idx_type` (`resource_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Deep linking resource mappings';

-- Table: lti_context_mappings
-- Maps LMS courses/contexts to internal classes
CREATE TABLE IF NOT EXISTS `lti_context_mappings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `config_id` INT(11) NOT NULL COMMENT 'FK to lti_configurations',
  `lms_context_id` VARCHAR(255) NOT NULL COMMENT 'Course ID from LMS',
  `class_id` INT(11) DEFAULT NULL COMMENT 'FK to classes table',
  `context_type` VARCHAR(100) DEFAULT NULL COMMENT 'CourseOffering, Group, etc.',
  `context_title` VARCHAR(500) DEFAULT NULL,
  `context_label` VARCHAR(100) DEFAULT NULL COMMENT 'Short course code',
  `sync_enabled` TINYINT(1) DEFAULT 1,
  `last_synced_at` DATETIME DEFAULT NULL,
  `sync_status` ENUM('pending', 'success', 'failed', 'partial') DEFAULT 'pending',
  `sync_error` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`config_id`) REFERENCES `lti_configurations`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE SET NULL,
  UNIQUE KEY `unique_context` (`config_id`, `lms_context_id`),
  INDEX `idx_class` (`class_id`),
  INDEX `idx_sync_status` (`sync_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='LMS course to class mappings';

-- Table: lti_user_mappings
-- Maps LMS users to internal system users
CREATE TABLE IF NOT EXISTS `lti_user_mappings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `config_id` INT(11) NOT NULL COMMENT 'FK to lti_configurations',
  `lms_user_id` VARCHAR(255) NOT NULL COMMENT 'User sub from LMS',
  `user_id` INT(11) DEFAULT NULL COMMENT 'FK to users table',
  `lms_email` VARCHAR(255) DEFAULT NULL,
  `lms_name` VARCHAR(255) DEFAULT NULL,
  `lms_roles` TEXT DEFAULT NULL COMMENT 'JSON: LTI role URIs',
  `auto_created` TINYINT(1) DEFAULT 0 COMMENT 'Whether user was auto-created',
  `last_login_via_lti` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`config_id`) REFERENCES `lti_configurations`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_lms_user` (`config_id`, `lms_user_id`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_email` (`lms_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='LMS to SAMS user mappings';

-- Table: lti_grade_sync_log
-- Tracks grade passback operations to LMS
CREATE TABLE IF NOT EXISTS `lti_grade_sync_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `config_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `resource_link_id` INT(11) DEFAULT NULL COMMENT 'FK to lti_resource_links',
  `attendance_id` INT(11) DEFAULT NULL COMMENT 'FK to attendance table',
  `grade_value` DECIMAL(5,2) DEFAULT NULL COMMENT 'Grade sent (0-100)',
  `lineitem_url` VARCHAR(500) DEFAULT NULL,
  `sync_status` ENUM('pending', 'success', 'failed') DEFAULT 'pending',
  `response_code` INT(11) DEFAULT NULL,
  `response_message` TEXT DEFAULT NULL,
  `attempt_count` INT(11) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `synced_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`config_id`) REFERENCES `lti_configurations`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`resource_link_id`) REFERENCES `lti_resource_links`(`id`) ON DELETE SET NULL,
  INDEX `idx_status` (`sync_status`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Grade passback audit log';

-- Table: lti_nonce_store
-- Prevents replay attacks by tracking used nonces
CREATE TABLE IF NOT EXISTS `lti_nonce_store` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nonce` VARCHAR(255) NOT NULL,
  `timestamp` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_nonce` (`nonce`),
  INDEX `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='LTI nonce tracking for replay prevention';

-- Add LMS-related columns to existing tables
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `lms_user_id` VARCHAR(255) DEFAULT NULL COMMENT 'External LMS user identifier',
  ADD COLUMN IF NOT EXISTS `lms_linked` TINYINT(1) DEFAULT 0 COMMENT 'Whether linked to LMS account',
  ADD INDEX IF NOT EXISTS `idx_lms_user` (`lms_user_id`);

ALTER TABLE `students`
  ADD COLUMN IF NOT EXISTS `lms_enrollment_id` VARCHAR(255) DEFAULT NULL COMMENT 'LMS enrollment identifier',
  ADD INDEX IF NOT EXISTS `idx_lms_enrollment` (`lms_enrollment_id`);

ALTER TABLE `teachers`
  ADD COLUMN IF NOT EXISTS `lms_instructor_id` VARCHAR(255) DEFAULT NULL COMMENT 'LMS instructor identifier',
  ADD INDEX IF NOT EXISTS `idx_lms_instructor` (`lms_instructor_id`);

ALTER TABLE `classes`
  ADD COLUMN IF NOT EXISTS `lms_course_id` VARCHAR(255) DEFAULT NULL COMMENT 'Linked LMS course ID',
  ADD COLUMN IF NOT EXISTS `lms_sync_enabled` TINYINT(1) DEFAULT 0 COMMENT 'Enable LMS sync for this class',
  ADD COLUMN IF NOT EXISTS `last_lms_sync` DATETIME DEFAULT NULL COMMENT 'Last successful sync timestamp',
  ADD INDEX IF NOT EXISTS `idx_lms_course` (`lms_course_id`);

ALTER TABLE `attendance`
  ADD COLUMN IF NOT EXISTS `exported_to_lms` TINYINT(1) DEFAULT 0 COMMENT 'Whether synced to LMS gradebook',
  ADD COLUMN IF NOT EXISTS `lms_export_date` DATETIME DEFAULT NULL COMMENT 'When exported to LMS',
  ADD COLUMN IF NOT EXISTS `lms_grade_value` DECIMAL(5,2) DEFAULT NULL COMMENT 'Calculated grade for LMS',
  ADD INDEX IF NOT EXISTS `idx_lms_export` (`exported_to_lms`, `lms_export_date`);

-- Cleanup procedure for expired nonces (run via cron)
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS cleanup_lti_nonces()
BEGIN
  -- Delete nonces older than 24 hours
  DELETE FROM lti_nonce_store
  WHERE timestamp < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 24 HOUR));
END$$
DELIMITER ;

-- Insert sample LTI configuration (disabled by default)
INSERT INTO `lti_configurations` (
  `lms_platform`,
  `platform_name`,
  `client_id`,
  `issuer`,
  `auth_login_url`,
  `auth_token_url`,
  `key_set_url`,
  `deployment_id`,
  `is_active`
) VALUES (
  'moodle',
  'Sample Moodle Instance',
  'your-client-id-here',
  'https://your-moodle-site.com',
  'https://your-moodle-site.com/mod/lti/auth.php',
  'https://your-moodle-site.com/mod/lti/token.php',
  'https://your-moodle-site.com/mod/lti/certs.php',
  'deployment-id-here',
  0
) ON DUPLICATE KEY UPDATE `platform_name` = VALUES(`platform_name`);

-- Verification queries
SELECT 'LTI tables created successfully' AS status;
SELECT COUNT(*) AS lti_config_count FROM lti_configurations;
