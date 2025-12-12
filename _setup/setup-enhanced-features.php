<?php

/**
 * Enhanced Features Database Setup
 * Run this script via browser or CLI to set up the new tables
 * URL: http://localhost/attendance/_setup/setup-enhanced-features.php
 */

// CLI or web output
$is_cli = php_sapi_name() === 'cli';
function output($msg, $type = 'info')
{
    global $is_cli;
    if ($is_cli) {
        $prefix = $type === 'success' ? '✓' : ($type === 'error' ? '✗' : '→');
        echo "$prefix $msg\n";
    } else {
        $color = $type === 'success' ? '#00ff88' : ($type === 'error' ? '#ff6b6b' : '#00d4ff');
        echo "<div style='color: $color; margin: 5px 0;'>$msg</div>";
    }
}

if (!$is_cli) {
    echo "<!DOCTYPE html><html><head><title>Setup Enhanced Features</title>
    <style>body{background:#0a0a0a;color:#fff;font-family:monospace;padding:40px;}</style></head><body>";
    echo "<h1 style='color:#00ff88;'>🚀 Enhanced Features Setup</h1>";
}

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';

$pdo = db()->getConnection();

$tables = [
    // Polls
    'polls' => "CREATE TABLE IF NOT EXISTS `polls` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `question` VARCHAR(500) NOT NULL,
        `created_by` INT NOT NULL,
        `status` ENUM('active', 'closed', 'draft') DEFAULT 'active',
        `expires_at` DATETIME NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_polls_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    'poll_options' => "CREATE TABLE IF NOT EXISTS `poll_options` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `poll_id` INT NOT NULL,
        `option_text` VARCHAR(255) NOT NULL,
        `display_order` INT DEFAULT 0,
        INDEX `idx_poll_options_poll` (`poll_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    'poll_votes' => "CREATE TABLE IF NOT EXISTS `poll_votes` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `poll_id` INT NOT NULL,
        `option_id` INT NOT NULL,
        `user_id` INT NOT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_vote` (`poll_id`, `user_id`),
        INDEX `idx_poll_votes_user` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // Study Groups
    'study_groups' => "CREATE TABLE IF NOT EXISTS `study_groups` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `subject` VARCHAR(100) NULL,
        `description` TEXT NULL,
        `emoji` VARCHAR(10) DEFAULT '📚',
        `is_public` TINYINT(1) DEFAULT 1,
        `created_by` INT NOT NULL,
        `status` ENUM('active', 'archived') DEFAULT 'active',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_study_groups_public` (`is_public`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    'study_group_members' => "CREATE TABLE IF NOT EXISTS `study_group_members` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `group_id` INT NOT NULL,
        `user_id` INT NOT NULL,
        `role` ENUM('admin', 'moderator', 'member') DEFAULT 'member',
        `joined_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_membership` (`group_id`, `user_id`),
        INDEX `idx_study_group_members_user` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // Widgets
    'user_widgets' => "CREATE TABLE IF NOT EXISTS `user_widgets` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `widget_id` VARCHAR(50) NOT NULL,
        `position` INT DEFAULT 0,
        `is_visible` TINYINT(1) DEFAULT 1,
        `settings` TEXT NULL,
        UNIQUE KEY `unique_user_widget` (`user_id`, `widget_id`),
        INDEX `idx_user_widgets_user` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // Push Notifications
    'push_subscriptions' => "CREATE TABLE IF NOT EXISTS `push_subscriptions` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `endpoint` VARCHAR(500) NOT NULL,
        `p256dh_key` VARCHAR(200) NOT NULL,
        `auth_key` VARCHAR(100) NOT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_push_subscriptions_user` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // User Sessions (online tracking)
    'user_sessions' => "CREATE TABLE IF NOT EXISTS `user_sessions` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // Onboarding
    'user_onboarding' => "CREATE TABLE IF NOT EXISTS `user_onboarding` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL UNIQUE,
        `completed_tours` TEXT NULL,
        `dismissed_tips` TEXT NULL,
        `onboarding_complete` TINYINT(1) DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_user_onboarding_user` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // AI Command History
    'ai_command_history' => "CREATE TABLE IF NOT EXISTS `ai_command_history` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `command` VARCHAR(500) NOT NULL,
        `intent` VARCHAR(50) NULL,
        `was_successful` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_ai_commands_user` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

output("Starting table creation...", "info");

$success = 0;
$errors = [];

foreach ($tables as $name => $sql) {
    try {
        $pdo->exec($sql);
        output("Created table: $name", "success");
        $success++;
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            output("Table exists: $name", "info");
            $success++;
        } else {
            output("Error creating $name: " . $e->getMessage(), "error");
            $errors[] = $name;
        }
    }
}

// Insert sample data
output("\nInserting sample data...", "info");

try {
    // Check if sample poll exists
    $existing = $pdo->query("SELECT id FROM polls WHERE question LIKE '%study session%' LIMIT 1")->fetch();

    if (!$existing) {
        $pdo->exec("INSERT INTO polls (question, created_by, status, expires_at) VALUES
            ('What subject should we focus on for the upcoming study session?', 1, 'active', DATE_ADD(NOW(), INTERVAL 7 DAY))");
        $poll_id = $pdo->lastInsertId();

        $pdo->exec("INSERT INTO poll_options (poll_id, option_text, display_order) VALUES
            ($poll_id, 'Mathematics', 1),
            ($poll_id, 'Science', 2),
            ($poll_id, 'History', 3),
            ($poll_id, 'English', 4)");

        output("Created sample poll with 4 options", "success");
    } else {
        output("Sample poll already exists", "info");
    }

    // Check if sample study groups exist
    $existing = $pdo->query("SELECT id FROM study_groups WHERE name = 'Math Masters' LIMIT 1")->fetch();

    if (!$existing) {
        $pdo->exec("INSERT INTO study_groups (name, subject, description, emoji, is_public, created_by) VALUES
            ('Math Masters', 'Mathematics', 'Study group for advanced mathematics', '🧮', 1, 1),
            ('Science Squad', 'Science', 'Explore physics, chemistry, and biology', '🔬', 1, 1),
            ('History Buffs', 'History', 'Discuss historical events together', '📜', 1, 1)");

        output("Created 3 sample study groups", "success");
    } else {
        output("Sample study groups already exist", "info");
    }
} catch (PDOException $e) {
    output("Error inserting sample data: " . $e->getMessage(), "error");
}

// Summary
echo $is_cli ? "\n" : "<hr style='border-color:#333;margin:20px 0;'>";
output("Setup Complete!", "success");
output("Tables created/verified: $success/" . count($tables), "info");

if (!empty($errors)) {
    output("Failed tables: " . implode(", ", $errors), "error");
}

if (!$is_cli) {
    echo "<br><a href='../admin/dashboard.php' style='color:#00ff88;'>→ Go to Dashboard</a>";
    echo "</body></html>";
}
