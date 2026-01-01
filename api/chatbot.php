<?php

/**
 * Unified Chatbot API - Verdant SMS AI Assistant
 * Merges functionality from sams-bot.php and ai-copilot.php
 * Processes user queries with context-aware responses
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$user_name = $_SESSION['full_name'];

$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? $input['query'] ?? '');
$context = $input['context'] ?? [];

if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Message required']);
    exit;
}

// Process the query with unified response system
$response = processUnifiedQuery($message, $user_role, $user_id, $context);

echo json_encode($response);

/**
 * Unified query processor - merges both chatbot logics
 */
function processUnifiedQuery($message, $role, $user_id, $context = [])
{
    $message_lower = strtolower($message);
    
    // Detect intent
    $intent = detectIntent($message_lower);
    
    // Process based on intent
    switch ($intent) {
        case 'navigation':
            return handleNavigationIntent($message_lower, $role);
        case 'attendance':
            return handleAttendanceQuery($message_lower, $role, $user_id);
        case 'schedule':
            return handleScheduleQuery($message_lower, $role, $user_id);
        case 'grade':
            return handleGradeQuery($message_lower, $role, $user_id);
        case 'fee':
            return handleFeeQuery($message_lower, $role, $user_id);
        case 'search':
            return handleSearchIntent($message_lower, $role);
        case 'create':
            return handleCreateIntent($message_lower, $role);
        case 'help':
            return handleHelpIntent($message_lower, $role);
        case 'report':
            return handleReportIntent($message_lower, $role);
        case 'message_draft':
            return handleMessageDraft($message_lower, $role);
        case 'greeting':
            return handleGreeting($message_lower);
        default:
            return handleGeneralIntent($message_lower, $role);
    }
}

/**
 * Detect user intent from message
 */
function detectIntent($message)
{
    // Navigation
    if (preg_match('/\b(go to|navigate to|open|show me|take me to)\b/i', $message)) {
        return 'navigation';
    }
    
    // Attendance
    if (preg_match('/\b(attendance|present|absent|late)\b/i', $message)) {
        return 'attendance';
    }
    
    // Schedule
    if (preg_match('/\b(schedule|class|timetable|time table)\b/i', $message)) {
        return 'schedule';
    }
    
    // Grades
    if (preg_match('/\b(grade|score|marks|result|gpa)\b/i', $message)) {
        return 'grade';
    }
    
    // Fees
    if (preg_match('/\b(fee|payment|pay|due|outstanding|balance)\b/i', $message)) {
        return 'fee';
    }
    
    // Search
    if (preg_match('/\b(search|find|look for|lookup)\b/i', $message)) {
        return 'search';
    }
    
    // Create
    if (preg_match('/\b(create|add|new|make|generate)\b/i', $message)) {
        return 'create';
    }
    
    // Help
    if (preg_match('/\b(how to|help|guide|explain|what is)\b/i', $message)) {
        return 'help';
    }
    
    // Report
    if (preg_match('/\b(report|statistics|analytics|summary|overview)\b/i', $message)) {
        return 'report';
    }
    
    // Message draft
    if (preg_match('/\b(draft|write|compose|message)\b/i', $message)) {
        return 'message_draft';
    }
    
    // Greeting
    if (preg_match('/^(hi|hello|hey|good morning|good afternoon|good evening)/i', $message)) {
        return 'greeting';
    }
    
    return 'general';
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
        'chat' => ['url' => '/chat.php', 'name' => 'Chat'],
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
 * Handle attendance queries
 */
function handleAttendanceQuery($message, $role, $user_id)
{
    if ($role === 'student') {
        try {
            $records = db()->fetchAll("SELECT * FROM attendance_records WHERE student_id = ?", [$user_id]);
            $present = count(array_filter($records, fn($r) => $r['status'] === 'present'));
            $total = count($records);
            $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            return [
                'success' => true,
                'response' => "📊 Your Attendance Summary:\n\n" .
                    "✅ Days Present: $present\n" .
                    "📅 Total Days: $total\n" .
                    "📈 Attendance Rate: {$percentage}%\n\n" .
                    ($percentage >= 75 ? "Great job! Keep it up! 🎉" : "Try to improve your attendance to maintain good standing. 💪")
            ];
        } catch (Exception $e) {
            return [
                'success' => true,
                'response' => "To view attendance statistics:\n\n" .
                    "1. Go to Dashboard for overview\n" .
                    "2. Visit 'Attendance' page for detailed records\n" .
                    "3. Check 'Reports' for analytics\n\n" .
                    "Would you like me to help with something specific?"
            ];
        }
    }

    if ($role === 'teacher') {
        return [
            'success' => true,
            'response' => "To view attendance statistics:\n\n" .
                "1. Go to Dashboard for overview\n" .
                "2. Visit 'Mark Attendance' to record today's attendance\n" .
                "3. Check 'Reports' for detailed analytics\n\n" .
                "Would you like me to help with something specific?"
        ];
    }

    if ($role === 'parent') {
        try {
            $children = db()->fetchAll("
                SELECT u.first_name, u.last_name, u.id
                FROM parent_student_links psl
                JOIN users u ON psl.student_id = u.id
                WHERE psl.parent_id = ? AND psl.verified_at IS NOT NULL
            ", [$user_id]);

            if (empty($children)) {
                return [
                    'success' => true,
                    'response' => "You haven't linked any children yet. Visit 'Link Children' to get started!"
                ];
            }

            $response = "👨‍👩‍👧‍👦 Your Children's Attendance:\n\n";
            foreach ($children as $child) {
                $records = db()->fetchAll("SELECT * FROM attendance_records WHERE student_id = ?", [$child['id']]);
                $present = count(array_filter($records, fn($r) => $r['status'] === 'present'));
                $total = count($records);
                $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

                $response .= "• {$child['first_name']} {$child['last_name']}: {$percentage}%";
                $response .= $percentage >= 75 ? " ✅\n" : " ⚠️\n";
            }

            return ['success' => true, 'response' => $response];
        } catch (Exception $e) {
            return [
                'success' => true,
                'response' => "I can help you with attendance tracking! What specific information do you need?"
            ];
        }
    }

    return [
        'success' => true,
        'response' => "I can help you with attendance tracking! What specific information do you need?"
    ];
}

/**
 * Handle schedule queries
 */
function handleScheduleQuery($message, $role, $user_id)
{
    if ($role === 'student') {
        try {
            $classes = db()->fetchAll("
                SELECT c.class_name, c.class_code, c.schedule
                FROM classes c
                JOIN class_enrollments ce ON c.id = ce.class_id
                WHERE ce.student_id = ?
            ", [$user_id]);

            if (empty($classes)) {
                return [
                    'success' => true,
                    'response' => "You're not enrolled in any classes yet. Visit 'Class Registration' to enroll!"
                ];
            }

            $response = "📚 Your Class Schedule:\n\n";
            foreach ($classes as $class) {
                $response .= "• {$class['class_name']} ({$class['class_code']})\n";
                $response .= "  Schedule: " . ($class['schedule'] ?? 'Not set') . "\n\n";
            }

            return [
                'success' => true,
                'response' => $response . "For detailed schedules, visit the 'My Schedule' page!"
            ];
        } catch (Exception $e) {
            return [
                'success' => true,
                'response' => "To view your schedule, navigate to the 'Schedule' or 'My Classes' section in the menu."
            ];
        }
    }

    return [
        'success' => true,
        'response' => "To view your schedule, navigate to the 'Schedule' or 'My Classes' section in the menu."
    ];
}

/**
 * Handle grade queries
 */
function handleGradeQuery($message, $role, $user_id)
{
    if ($role === 'student') {
        return [
            'success' => true,
            'response' => "📝 To view your grades:\n\n" .
                "1. Go to 'My Grades' in the Academic section\n" .
                "2. Filter by subject or date range\n" .
                "3. Export reports if needed\n\n" .
                "Your grades are updated regularly by your teachers!"
        ];
    }

    if ($role === 'parent') {
        return [
            'success' => true,
            'response' => "To view your children's grades:\n\n" .
                "1. Visit 'Children's Grades' in the Academic section\n" .
                "2. Select a child to view detailed reports\n" .
                "3. Download PDF reports for your records\n\n" .
                "Grades are synced with the LMS system!"
        ];
    }

    return [
        'success' => true,
        'response' => "For grade management, visit the Grades section in your dashboard."
    ];
}

/**
 * Handle fee queries
 */
function handleFeeQuery($message, $role, $user_id)
{
    if ($role === 'parent') {
        return [
            'success' => true,
            'response' => "💰 Fee Payment Information:\n\n" .
                "To view and pay fees:\n" .
                "1. Go to 'Fees & Payments' in Academic section\n" .
                "2. View outstanding balances\n" .
                "3. Click 'Pay Now' for online payment\n" .
                "4. Download receipts after payment\n\n" .
                "Payment methods: Credit Card, Debit Card, Bank Transfer\n\n" .
                "Need help with a specific fee? Let me know!"
        ];
    }

    return [
        'success' => true,
        'response' => "For fee-related queries, please visit the Fee Management section or contact administration."
    ];
}

/**
 * Handle search requests
 */
function handleSearchIntent($query, $role)
{
    $search_term = preg_replace('/^(search|find|look for|lookup)\s*(for)?\s*/i', '', $query);
    $search_term = trim($search_term);

    if (empty($search_term)) {
        return [
            'success' => true,
            'response' => "What would you like me to search for? You can search for students, teachers, classes, or any other records."
        ];
    }

    if (preg_match('/student/i', $query)) {
        return performStudentSearch($search_term, $role);
    } elseif (preg_match('/teacher/i', $query)) {
        return performTeacherSearch($search_term, $role);
    } elseif (preg_match('/class/i', $query)) {
        return performClassSearch($search_term, $role);
    }

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
        'backup' => "💾 Database Backup Guide:\n\n" .
            "1. Navigate to 'Backup & Export' in System section\n" .
            "2. Choose backup type:\n" .
            "   • Full Backup (recommended weekly)\n" .
            "   • Incremental Backup (daily)\n" .
            "3. Click 'Create Backup'\n" .
            "4. Download or store in cloud\n\n" .
            "Automated backups run at 2:00 AM daily.",
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
        'response' => "I can help you with:\n• Taking attendance\n• Entering grades\n• Generating reports\n• Sending messages\n• Changing password\n• Database backup\n\nJust ask about any of these topics!"
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
        'response' => "What report would you like to see? I can show:\n• Attendance report/statistics\n• Student enrollment summary\n\nFor detailed reports, visit the Reports page."
    ];
}

/**
 * Handle message drafting
 */
function handleMessageDraft($message, $role)
{
    if ($role === 'teacher' && preg_match('/field trip|excursion/i', $message)) {
        return [
            'success' => true,
            'response' => "📝 Here's a draft message about a field trip:\n\n" .
                "Subject: Upcoming Field Trip - [Date]\n\n" .
                "Dear Parents,\n\n" .
                "I hope this message finds you well. I am writing to inform you about an upcoming educational field trip for [Class Name].\n\n" .
                "Details:\n" .
                "• Date: [Insert Date]\n" .
                "• Destination: [Location]\n" .
                "• Departure Time: [Time]\n" .
                "• Return Time: [Time]\n" .
                "• Cost: [Amount]\n\n" .
                "Please sign and return the permission slip by [Deadline].\n\n" .
                "Best regards,\n" .
                "[Your Name]\n\n" .
                "You can copy and customize this draft!"
        ];
    }

    return [
        'success' => true,
        'response' => "I can help you draft messages! Please specify:\n" .
            "• Who you're writing to (parents/students/teachers)\n" .
            "• The topic or purpose\n" .
            "• Any specific details to include"
    ];
}

/**
 * Handle greetings
 */
function handleGreeting($message)
{
    $greeting = getTimeBasedGreeting();
    return [
        'success' => true,
        'response' => "{$greeting}! How can I assist you today? You can ask me to:\n• Navigate to pages\n• Search for students/teachers\n• Show reports\n• Help with tasks\n• Draft messages"
    ];
}

/**
 * Handle general/unknown intents
 */
function handleGeneralIntent($query, $role)
{
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
    $responses = [
        'student' => "I can help you with:\n• Checking attendance\n• Viewing schedules\n• Assignment info\n• System navigation\n\nWhat would you like to know?",
        'teacher' => "How can I assist you today?\n• Draft messages\n• Attendance summaries\n• Student insights\n• System features\n\nJust ask!",
        'parent' => "I'm here to help with:\n• Children's status\n• Attendance reports\n• Fee payments\n• Teacher communication\n\nWhat do you need?",
        'admin' => "Available assistance:\n• System analytics\n• User management\n• Technical support\n• Database operations\n\nHow can I help?"
    ];

    return [
        'success' => true,
        'response' => $responses[$role] ?? "I'm not sure I understood that. Try asking me to:\n• \"Go to attendance\"\n• \"Search for student John\"\n• \"Show attendance report\"\n• \"Help with grades\"\n\nOr press Ctrl+K for quick search!"
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


