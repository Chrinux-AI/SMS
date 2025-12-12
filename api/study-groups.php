<?php

/**
 * Study Groups API Endpoint
 * Create, join, and manage study groups
 * Verdant SMS v3.0
 */

session_start();
header('Content-Type: application/json');

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check for JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input && isset($input['action'])) {
        switch ($input['action']) {
            case 'join':
                handleJoinGroup($input, $user_id);
                break;
            case 'leave':
                handleLeaveGroup($input, $user_id);
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } else {
        // Handle group creation
        handleCreateGroup($_POST, $user_id);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    handleGetGroups($user_id);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

/**
 * Handle group creation
 */
function handleCreateGroup($data, $user_id)
{
    $name = trim($data['name'] ?? '');
    $subject = trim($data['subject'] ?? '');
    $description = trim($data['description'] ?? '');
    $emoji = trim($data['emoji'] ?? '📚');
    $is_public = intval($data['is_public'] ?? 1);

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Group name is required']);
        return;
    }

    try {
        // Create group
        $group_id = db()->insert('study_groups', [
            'name' => $name,
            'subject' => $subject,
            'description' => $description,
            'emoji' => $emoji,
            'is_public' => $is_public,
            'created_by' => $user_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Add creator as admin member
        db()->insert('study_group_members', [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'role' => 'admin',
            'joined_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Group created successfully',
            'group_id' => $group_id
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to create group: ' . $e->getMessage()]);
    }
}

/**
 * Handle joining a group
 */
function handleJoinGroup($data, $user_id)
{
    $group_id = intval($data['group_id'] ?? 0);

    if (!$group_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid group']);
        return;
    }

    try {
        // Check if group exists and is public
        $group = db()->fetchOne(
            "SELECT * FROM study_groups WHERE id = ?",
            [$group_id]
        );

        if (!$group) {
            echo json_encode(['success' => false, 'message' => 'Group not found']);
            return;
        }

        if (!$group['is_public']) {
            echo json_encode(['success' => false, 'message' => 'This is a private group']);
            return;
        }

        // Check if already member
        $existing = db()->fetchOne(
            "SELECT id FROM study_group_members WHERE group_id = ? AND user_id = ?",
            [$group_id, $user_id]
        );

        if ($existing) {
            echo json_encode(['success' => false, 'message' => 'You are already a member']);
            return;
        }

        // Join group
        db()->insert('study_group_members', [
            'group_id' => $group_id,
            'user_id' => $user_id,
            'role' => 'member',
            'joined_at' => date('Y-m-d H:i:s')
        ]);

        // Update group timestamp
        db()->update(
            'study_groups',
            ['updated_at' => date('Y-m-d H:i:s')],
            'id = ?',
            [$group_id]
        );

        echo json_encode([
            'success' => true,
            'message' => 'Joined group successfully'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to join group']);
    }
}

/**
 * Handle leaving a group
 */
function handleLeaveGroup($data, $user_id)
{
    $group_id = intval($data['group_id'] ?? 0);

    if (!$group_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid group']);
        return;
    }

    try {
        db()->query(
            "DELETE FROM study_group_members WHERE group_id = ? AND user_id = ?",
            [$group_id, $user_id]
        );

        echo json_encode([
            'success' => true,
            'message' => 'Left group successfully'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to leave group']);
    }
}

/**
 * Get groups for user
 */
function handleGetGroups($user_id)
{
    try {
        // My groups
        $my_groups = db()->fetchAll(
            "SELECT sg.*,
                    (SELECT COUNT(*) FROM study_group_members WHERE group_id = sg.id) as member_count,
                    sgm.role as member_role
             FROM study_groups sg
             JOIN study_group_members sgm ON sg.id = sgm.group_id
             WHERE sgm.user_id = ?
             ORDER BY sg.updated_at DESC",
            [$user_id]
        );

        // Available public groups
        $available = db()->fetchAll(
            "SELECT sg.*,
                    (SELECT COUNT(*) FROM study_group_members WHERE group_id = sg.id) as member_count,
                    u.first_name as creator_name
             FROM study_groups sg
             LEFT JOIN users u ON sg.created_by = u.id
             WHERE sg.is_public = 1
             AND sg.id NOT IN (SELECT group_id FROM study_group_members WHERE user_id = ?)
             ORDER BY member_count DESC
             LIMIT 20",
            [$user_id]
        );

        echo json_encode([
            'success' => true,
            'my_groups' => $my_groups,
            'available' => $available
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch groups']);
    }
}
