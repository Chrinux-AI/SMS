<?php

/**
 * AI Copilot API Endpoint
 * Processes natural language queries and returns intelligent responses
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

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$query = trim($input['query'] ?? '');
$context = $input['context'] ?? [];

if (empty($query)) {
    echo json_encode(['success' => false, 'message' => 'No query provided']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'student';

// Process the query
$response = processAIQuery($query, $role, $context);

echo json_encode($response);

/**
 * Process AI Query with pattern matching and intent detection
 */
function processAIQuery($query, $role, $context)
{
    $query_lower = strtolower($query);

    // Navigation intents
    $navigation_patterns = [
        'go to|navigate to|open|show me' => 'navigation',
        'search|find|look for|lookup' => 'search',
        'create|add|new|make' => 'create',
        'help|how do i|what is|explain' => 'help',
        'report|statistics|analytics|summary' => 'report'
    ];

    $intent = 'general';
    foreach ($navigation_patterns as $pattern => $detected_intent) {
        if (preg_match('/\b(' . $pattern . ')\b/i', $query_lower)) {
            $intent = $detected_intent;
            break;
        }
    }

    // Process based on intent
    switch ($intent) {
        case 'navigation':
            return handleNavigationIntent($query_lower, $role);
        case 'search':
            return handleSearchIntent($query_lower, $role);
        case 'create':
            return handleCreateIntent($query_lower, $role);
        case 'help':
            return handleHelpIntent($query_lower, $role);
        case 'report':
            return handleReportIntent($query_lower, $role);
        default:
            return handleGeneralIntent($query_lower, $role);
    }
}

/**
 * Handle navigation requests
 */
function handleNavigationIntent($query, $role)
{
    $pages = [
        'dashboard' => ['url' => "/{$role}/dashboard.php", 'name' => 'Dashboard'],
        'attendance' => ['url' => "/{$role}/attendance.php", 'name' => 'Attendance'],
        'students' => ['url' => '/admin/students.php', 'name' => 'Students'],
        'teachers' => ['url' => '/admin/teachers.php', 'name' => 'Teachers'],
        'classes' => ['url' => '/admin/classes.php', 'name' => 'Classes'],
        'reports' => ['url' => "/{$role}/reports.php", 'name' => 'Reports'],
        'settings' => ['url' => "/{$role}/settings.php", 'name' => 'Settings'],
        'messages' => ['url' => '/messages.php', 'name' => 'Messages'],
        'announcements' => ['url' => '/admin/announcements.php', 'name' => 'Announcements'],
        'grades' => ['url' => "/{$role}/grades.php", 'name' => 'Grades'],
        'timetable' => ['url' => "/{$role}/timetable.php", 'name' => 'Timetable'],
        'calendar' => ['url' => "/{$role}/calendar.php", 'name' => 'Calendar'],
        'profile' => ['url' => "/{$role}/profile.php", 'name' => 'Profile'],
        'fees' => ['url' => '/admin/fee-management.php', 'name' => 'Fee Management'],
        'library' => ['url' => '/librarian/dashboard.php', 'name' => 'Library'],
        'transport' => ['url' => '/transport/dashboard.php', 'name' => 'Transport'],
    ];

    foreach ($pages as $keyword => $page) {
        if (strpos($query, $keyword) !== false) {
            return [
                'success' => true,
                'response' => "Taking you to {$page['name']}...",
                'action' => [
                    'type' => 'navigate',
                    'url' => APP_URL . $page['url']
                ]
            ];
        }
    }

    return [
        'success' => true,
        'response' => "I couldn't find that page. You can navigate to: Dashboard, Attendance, Messages, Reports, Settings, or try being more specific."
    ];
}

/**
 * Handle search requests
 */
function handleSearchIntent($query, $role)
{
    // Extract search term
    $search_term = preg_replace('/^(search|find|look for|lookup)\s*(for)?\s*/i', '', $query);
    $search_term = trim($search_term);

    if (empty($search_term)) {
        return [
            'success' => true,
            'response' => "What would you like me to search for? You can search for students, teachers, classes, or any other records."
        ];
    }

    // Determine search type
    if (preg_match('/student/i', $query)) {
        return performStudentSearch($search_term, $role);
    } elseif (preg_match('/teacher/i', $query)) {
        return performTeacherSearch($search_term, $role);
    } elseif (preg_match('/class/i', $query)) {
        return performClassSearch($search_term, $role);
    }

    // Generic search
    return [
        'success' => true,
        'response' => "Searching for \"{$search_term}\"... Use the global search (Ctrl+K) for comprehensive results, or specify: search student/teacher/class + name."
    ];
}

/**
 * Perform student search
 */
function performStudentSearch($term, $role)
{
    if (!in_array($role, ['admin', 'teacher', 'principal'])) {
        return [
            'success' => true,
            'response' => "You don't have permission to search student records."
        ];
    }

    try {
        $students = db()->fetchAll(
            "SELECT id, student_id, first_name, last_name, email
             FROM students
             WHERE first_name LIKE ? OR last_name LIKE ? OR student_id LIKE ?
             LIMIT 5",
            ["%{$term}%", "%{$term}%", "%{$term}%"]
        );

        if (empty($students)) {
            return [
                'success' => true,
                'response' => "No students found matching \"{$term}\". Try a different name or ID."
            ];
        }

        $results = array_map(function ($s) {
            return "• {$s['first_name']} {$s['last_name']} ({$s['student_id']})";
        }, $students);

        return [
            'success' => true,
            'response' => "Found " . count($students) . " student(s):\n" . implode("\n", $results)
        ];
    } catch (Exception $e) {
        return [
            'success' => true,
            'response' => "Sorry, I encountered an error searching. Please try again."
        ];
    }
}

/**
 * Perform teacher search
 */
function performTeacherSearch($term, $role)
{
    if (!in_array($role, ['admin', 'principal'])) {
        return [
            'success' => true,
            'response' => "You don't have permission to search teacher records."
        ];
    }

    try {
        $teachers = db()->fetchAll(
            "SELECT id, first_name, last_name, email
             FROM users
             WHERE role = 'teacher' AND (first_name LIKE ? OR last_name LIKE ?)
             LIMIT 5",
            ["%{$term}%", "%{$term}%"]
        );

        if (empty($teachers)) {
            return [
                'success' => true,
                'response' => "No teachers found matching \"{$term}\"."
            ];
        }

        $results = array_map(function ($t) {
            return "• {$t['first_name']} {$t['last_name']}";
        }, $teachers);

        return [
            'success' => true,
            'response' => "Found " . count($teachers) . " teacher(s):\n" . implode("\n", $results)
        ];
    } catch (Exception $e) {
        return [
            'success' => true,
            'response' => "Sorry, I encountered an error searching. Please try again."
        ];
    }
}

/**
 * Perform class search
 */
function performClassSearch($term, $role)
{
    try {
        $classes = db()->fetchAll(
            "SELECT id, name, grade_level, section
             FROM classes
             WHERE name LIKE ? OR grade_level LIKE ?
             LIMIT 5",
            ["%{$term}%", "%{$term}%"]
        );

        if (empty($classes)) {
            return [
                'success' => true,
                'response' => "No classes found matching \"{$term}\"."
            ];
        }

        $results = array_map(function ($c) {
            return "• {$c['name']} (Grade {$c['grade_level']})";
        }, $classes);

        return [
            'success' => true,
            'response' => "Found " . count($classes) . " class(es):\n" . implode("\n", $results)
        ];
    } catch (Exception $e) {
        return [
            'success' => true,
            'response' => "Sorry, I encountered an error searching. Please try again."
        ];
    }
}

/**
 * Handle create/add requests
 */
function handleCreateIntent($query, $role)
{
    $create_actions = [
        'student' => ['url' => '/admin/student-add.php', 'permission' => ['admin']],
        'announcement' => ['url' => '/admin/announcements.php', 'permission' => ['admin', 'teacher']],
        'class' => ['url' => '/admin/classes.php', 'permission' => ['admin']],
        'assignment' => ['url' => '/teacher/assignments.php', 'permission' => ['teacher']],
        'event' => ['url' => '/admin/events.php', 'permission' => ['admin']],
        'message' => ['url' => '/messages.php', 'permission' => ['admin', 'teacher', 'student', 'parent']],
    ];

    foreach ($create_actions as $item => $config) {
        if (strpos($query, $item) !== false) {
            if (!in_array($role, $config['permission'])) {
                return [
                    'success' => true,
                    'response' => "You don't have permission to create {$item}s."
                ];
            }

            return [
                'success' => true,
                'response' => "Opening form to create new {$item}...",
                'action' => [
                    'type' => 'navigate',
                    'url' => APP_URL . $config['url']
                ]
            ];
        }
    }

    return [
        'success' => true,
        'response' => "What would you like to create? I can help with: announcements, messages, assignments, events."
    ];
}

/**
 * Handle help requests
 */
function handleHelpIntent($query, $role)
{
    $help_topics = [
        'attendance' => "To mark attendance:\n1. Go to Attendance page\n2. Select the class and date\n3. Mark each student as present, absent, or late\n4. Click Save",
        'grades' => "To enter grades:\n1. Go to Grades page\n2. Select the class and subject\n3. Enter marks for each student\n4. Click Submit",
        'report' => "To generate reports:\n1. Go to Reports page\n2. Select report type\n3. Choose date range\n4. Click Generate\n5. Export as PDF or Excel",
        'message' => "To send a message:\n1. Go to Messages\n2. Click 'New Message'\n3. Select recipient(s)\n4. Type your message\n5. Click Send",
        'password' => "To change your password:\n1. Go to Settings\n2. Click 'Security'\n3. Enter current password\n4. Enter new password twice\n5. Click Update",
    ];

    foreach ($help_topics as $topic => $help_text) {
        if (strpos($query, $topic) !== false) {
            return [
                'success' => true,
                'response' => $help_text
            ];
        }
    }

    return [
        'success' => true,
        'response' => "I can help you with:\n• Taking attendance\n• Entering grades\n• Generating reports\n• Sending messages\n• Changing password\n\nJust ask about any of these topics!"
    ];
}

/**
 * Handle report/analytics requests
 */
function handleReportIntent($query, $role)
{
    if (strpos($query, 'attendance') !== false) {
        try {
            $today = date('Y-m-d');
            $stats = db()->fetchOne(
                "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent,
                    SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late
                 FROM attendance_records
                 WHERE attendance_date = ?",
                [$today]
            );

            if ($stats && $stats['total'] > 0) {
                $rate = round(($stats['present'] / $stats['total']) * 100, 1);
                return [
                    'success' => true,
                    'response' => "📊 Today's Attendance Summary:\n• Total: {$stats['total']}\n• Present: {$stats['present']}\n• Absent: {$stats['absent']}\n• Late: {$stats['late']}\n• Rate: {$rate}%"
                ];
            }
        } catch (Exception $e) {
            // Fall through
        }
    }

    if (strpos($query, 'student') !== false || strpos($query, 'enrollment') !== false) {
        try {
            $total = db()->count('students');
            $active = db()->count('students', 'status = ?', ['active']);
            return [
                'success' => true,
                'response' => "📊 Student Statistics:\n• Total Enrolled: {$total}\n• Active: {$active}"
            ];
        } catch (Exception $e) {
            // Fall through
        }
    }

    return [
        'success' => true,
        'response' => "What report would you like to see? I can show:\n• Attendance report/statistics\n• Student enrollment summary\n\nFor detailed reports, visit the Reports page.",
        'action' => null
    ];
}

/**
 * Handle general/unknown intents
 */
function handleGeneralIntent($query, $role)
{
    // Greeting patterns
    if (preg_match('/^(hi|hello|hey|good morning|good afternoon|good evening)/i', $query)) {
        $greeting = getTimeBasedGreeting();
        return [
            'success' => true,
            'response' => "{$greeting}! How can I assist you today? You can ask me to:\n• Navigate to pages\n• Search for students/teachers\n• Show reports\n• Help with tasks"
        ];
    }

    // Thank you
    if (preg_match('/thank|thanks/i', $query)) {
        return [
            'success' => true,
            'response' => "You're welcome! Let me know if you need anything else. 😊"
        ];
    }

    // Time/date queries
    if (preg_match('/what time|what date|what day/i', $query)) {
        $now = new DateTime();
        return [
            'success' => true,
            'response' => "It's " . $now->format('l, F j, Y') . " at " . $now->format('g:i A')
        ];
    }

    // Default response
    return [
        'success' => true,
        'response' => "I'm not sure I understood that. Try asking me to:\n• \"Go to attendance\"\n• \"Search for student John\"\n• \"Show attendance report\"\n• \"Help with grades\"\n\nOr press Ctrl+K for quick search!"
    ];
}

/**
 * Get time-based greeting
 */
function getTimeBasedGreeting()
{
    $hour = (int)date('H');
    if ($hour < 12) return "Good morning";
    if ($hour < 17) return "Good afternoon";
    return "Good evening";
}
