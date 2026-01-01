<?php
/**
 * AI Chat API Endpoint
 * Handles AJAX requests from the AI Learning Assistant
 */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

session_start();

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/ai-core.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Parse JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Message required']);
    exit;
}

$message = trim($input['message']);
$role = $input['role'] ?? $_SESSION['role'] ?? 'visitor';
$class = $input['class'] ?? $_SESSION['class'] ?? '';
$subject = $input['subject'] ?? '';

// Rate limiting
$_SESSION['ai_requests'] = ($_SESSION['ai_requests'] ?? 0) + 1;
if ($_SESSION['ai_requests'] > 50) {
    echo json_encode([
        'response' => "You've reached the request limit. Please wait a moment.",
        'limit_reached' => true
    ]);
    exit;
}

// Get AI instance
$ai = VerdantAI::getInstance();

// Process message
$lowerMessage = strtolower($message);

if (strpos($lowerMessage, 'lesson plan') !== false) {
    $response = $ai->generateLessonPlan($class ?: 'JSS 2', $subject ?: 'Mathematics', $message);
} elseif (strpos($lowerMessage, 'quiz') !== false) {
    $response = $ai->generateQuiz($class ?: 'JSS 2', $subject ?: 'General', 'General topic', 5);
} elseif (strpos($lowerMessage, 'homework') !== false || strpos($lowerMessage, 'solve') !== false || strpos($lowerMessage, 'explain') !== false) {
    $response = $ai->solveHomework($message, $subject ?: 'General', $class);
} elseif (strpos($lowerMessage, 'progress') !== false || strpos($lowerMessage, 'how is my child') !== false) {
    $studentData = ['name' => 'Your child', 'class' => $class ?: 'JSS 2', 'attendance' => 92, 'avg_grade' => 78, 'weak_subjects' => ['Mathematics']];
    $response = $ai->getProgressSummary($studentData);
} else {
    $response = $ai->generate($message, ['role' => $role, 'class' => $class, 'subject' => $subject]);
}

echo json_encode([
    'response' => $response['response'] ?? 'Sorry, I couldn\'t process your request.',
    'source' => $response['source'] ?? 'unknown',
    'success' => $response['success'] ?? false
]);
