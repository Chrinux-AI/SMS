<?php

/**
 * Polls API Endpoint
 * Create polls, vote, and get results
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
$role = $_SESSION['role'] ?? 'student';

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check for JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input && isset($input['action']) && $input['action'] === 'vote') {
        // Handle vote
        handleVote($input, $user_id);
    } else {
        // Handle poll creation
        handleCreatePoll($_POST, $user_id, $role);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get polls
    handleGetPolls($user_id);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

/**
 * Handle voting on a poll
 */
function handleVote($data, $user_id)
{
    $poll_id = intval($data['poll_id'] ?? 0);
    $option_id = intval($data['option_id'] ?? 0);

    if (!$poll_id || !$option_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid poll or option']);
        return;
    }

    try {
        // Check if poll exists and is active
        $poll = db()->fetchOne(
            "SELECT * FROM polls WHERE id = ? AND status = 'active'",
            [$poll_id]
        );

        if (!$poll) {
            echo json_encode(['success' => false, 'message' => 'Poll not found or inactive']);
            return;
        }

        // Check if already voted
        $existing = db()->fetchOne(
            "SELECT id FROM poll_votes WHERE poll_id = ? AND user_id = ?",
            [$poll_id, $user_id]
        );

        if ($existing) {
            echo json_encode(['success' => false, 'message' => 'You have already voted']);
            return;
        }

        // Record vote
        db()->insert('poll_votes', [
            'poll_id' => $poll_id,
            'option_id' => $option_id,
            'user_id' => $user_id,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Get updated results
        $results = db()->fetchAll(
            "SELECT po.id, po.option_text,
                    (SELECT COUNT(*) FROM poll_votes pv WHERE pv.option_id = po.id) as votes
             FROM poll_options po
             WHERE po.poll_id = ?",
            [$poll_id]
        );

        $total = array_sum(array_column($results, 'votes'));

        foreach ($results as &$result) {
            $result['percentage'] = $total > 0 ? round(($result['votes'] / $total) * 100) : 0;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Vote recorded',
            'results' => $results
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to record vote']);
    }
}

/**
 * Handle poll creation
 */
function handleCreatePoll($data, $user_id, $role)
{
    // Check permission
    if (!in_array($role, ['admin', 'teacher', 'principal', 'superadmin'])) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    $question = trim($data['question'] ?? '');
    $options = $data['options'] ?? [];
    $expires = intval($data['expires'] ?? 0);

    if (empty($question)) {
        echo json_encode(['success' => false, 'message' => 'Question is required']);
        return;
    }

    // Filter empty options
    $options = array_filter(array_map('trim', $options));

    if (count($options) < 2) {
        echo json_encode(['success' => false, 'message' => 'At least 2 options are required']);
        return;
    }

    try {
        // Calculate expiry
        $expires_at = null;
        if ($expires > 0) {
            $expires_at = date('Y-m-d H:i:s', strtotime("+{$expires} hours"));
        }

        // Create poll
        $poll_id = db()->insert('polls', [
            'question' => $question,
            'created_by' => $user_id,
            'status' => 'active',
            'expires_at' => $expires_at,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Create options
        foreach ($options as $option_text) {
            db()->insert('poll_options', [
                'poll_id' => $poll_id,
                'option_text' => $option_text
            ]);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Poll created successfully',
            'poll_id' => $poll_id
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to create poll: ' . $e->getMessage()]);
    }
}

/**
 * Get active polls
 */
function handleGetPolls($user_id)
{
    try {
        $polls = db()->fetchAll(
            "SELECT p.*,
                    u.first_name, u.last_name,
                    (SELECT COUNT(*) FROM poll_votes WHERE poll_id = p.id) as total_votes,
                    (SELECT option_id FROM poll_votes WHERE poll_id = p.id AND user_id = ?) as user_vote
             FROM polls p
             LEFT JOIN users u ON p.created_by = u.id
             WHERE p.status = 'active'
             AND (p.expires_at IS NULL OR p.expires_at > NOW())
             ORDER BY p.created_at DESC
             LIMIT 10",
            [$user_id]
        );

        foreach ($polls as &$poll) {
            $poll['options'] = db()->fetchAll(
                "SELECT po.*,
                        (SELECT COUNT(*) FROM poll_votes pv WHERE pv.option_id = po.id) as vote_count
                 FROM poll_options po
                 WHERE po.poll_id = ?",
                [$poll['id']]
            );

            $poll['created_by_name'] = trim($poll['first_name'] . ' ' . $poll['last_name']) ?: 'Admin';
        }

        echo json_encode([
            'success' => true,
            'polls' => $polls
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch polls']);
    }
}
