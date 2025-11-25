<?php

/**
 * School Management System Bot API - AI Assistant Backend
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
$message = trim($input['message'] ?? '');

if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Message required']);
    exit;
}

// Context-aware response system
$response = generateResponse($message, $user_role, $user_id);

echo json_encode([
    'success' => true,
    'response' => $response,
    'timestamp' => date('Y-m-d H:i:s')
]);

function generateResponse($message, $role, $user_id)
{
    $message_lower = strtolower($message);

    // Attendance queries
    if (preg_match('/attendance|present|absent/i', $message)) {
        return handleAttendanceQuery($message_lower, $role, $user_id);
    }

    // Schedule queries
    if (preg_match('/schedule|class|timetable/i', $message)) {
        return handleScheduleQuery($message_lower, $role, $user_id);
    }

    // Grade queries
    if (preg_match('/grade|score|marks/i', $message)) {
        return handleGradeQuery($message_lower, $role, $user_id);
    }

    // Message drafting
    if (preg_match('/draft|write|compose|message/i', $message)) {
        return handleMessageDraft($message_lower, $role);
    }

    // System help
    if (preg_match('/how to|help|guide|explain/i', $message)) {
        return handleSystemHelp($message_lower, $role);
    }

    // Fee queries
    if (preg_match('/fee|payment|pay|due/i', $message)) {
        return handleFeeQuery($message_lower, $role, $user_id);
    }

    // Default fallback
    return generateDefaultResponse($role);
}

function handleAttendanceQuery($message, $role, $user_id)
{
    if ($role === 'student') {
        $records = db()->fetchAll("SELECT * FROM attendance_records WHERE student_id = ?", [$user_id]);
        $present = count(array_filter($records, fn($r) => $r['status'] === 'present'));
        $total = count($records);
        $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        return "📊 Your Attendance Summary:\n\n" .
            "✅ Days Present: $present\n" .
            "📅 Total Days: $total\n" .
            "📈 Attendance Rate: {$percentage}%\n\n" .
            ($percentage >= 75 ? "Great job! Keep it up! 🎉" : "Try to improve your attendance to maintain good standing. 💪");
    }

    if ($role === 'teacher') {
        return "To view attendance statistics:\n\n" .
            "1. Go to Dashboard for overview\n" .
            "2. Visit 'Mark Attendance' to record today's attendance\n" .
            "3. Check 'Reports' for detailed analytics\n\n" .
            "Would you like me to help with something specific?";
    }

    if ($role === 'parent') {
        $children = db()->fetchAll("
            SELECT u.first_name, u.last_name, u.id
            FROM parent_student_links psl
            JOIN users u ON psl.student_id = u.id
            WHERE psl.parent_id = ? AND psl.verified_at IS NOT NULL
        ", [$user_id]);

        if (empty($children)) {
            return "You haven't linked any children yet. Visit 'Link Children' to get started!";
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

        return $response;
    }

    return "I can help you with attendance tracking! What specific information do you need?";
}

function handleScheduleQuery($message, $role, $user_id)
{
    if ($role === 'student') {
        $classes = db()->fetchAll("
            SELECT c.class_name, c.class_code, c.schedule
            FROM classes c
            JOIN class_enrollments ce ON c.id = ce.class_id
            WHERE ce.student_id = ?
        ", [$user_id]);

        if (empty($classes)) {
            return "You're not enrolled in any classes yet. Visit 'Class Registration' to enroll!";
        }

        $response = "📚 Your Class Schedule:\n\n";
        foreach ($classes as $class) {
            $response .= "• {$class['class_name']} ({$class['class_code']})\n";
            $response .= "  Schedule: " . ($class['schedule'] ?? 'Not set') . "\n\n";
        }

        return $response . "For detailed schedules, visit the 'My Schedule' page!";
    }

    return "To view your schedule, navigate to the 'Schedule' or 'My Classes' section in the menu.";
}

function handleGradeQuery($message, $role, $user_id)
{
    if ($role === 'student') {
        return "📝 To view your grades:\n\n" .
            "1. Go to 'My Grades' in the Academic section\n" .
            "2. Filter by subject or date range\n" .
            "3. Export reports if needed\n\n" .
            "Your grades are updated regularly by your teachers!";
    }

    if ($role === 'parent') {
        return "To view your children's grades:\n\n" .
            "1. Visit 'Children's Grades' in the Academic section\n" .
            "2. Select a child to view detailed reports\n" .
            "3. Download PDF reports for your records\n\n" .
            "Grades are synced with the LMS system!";
    }

    return "For grade management, visit the Grades section in your dashboard.";
}

function handleMessageDraft($message, $role)
{
    if ($role === 'teacher' && preg_match('/field trip|excursion/i', $message)) {
        return "📝 Here's a draft message about a field trip:\n\n" .
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
            "You can copy and customize this draft!";
    }

    return "I can help you draft messages! Please specify:\n" .
        "• Who you're writing to (parents/students/teachers)\n" .
        "• The topic or purpose\n" .
        "• Any specific details to include";
}

function handleSystemHelp($message, $role)
{
    if (preg_match('/backup|database/i', $message) && $role === 'admin') {
        return "💾 Database Backup Guide:\n\n" .
            "1. Navigate to 'Backup & Export' in System section\n" .
            "2. Choose backup type:\n" .
            "   • Full Backup (recommended weekly)\n" .
            "   • Incremental Backup (daily)\n" .
            "3. Click 'Create Backup'\n" .
            "4. Download or store in cloud\n\n" .
            "Automated backups run at 2:00 AM daily.\n\n" .
            "For manual MySQL backup:\n" .
            "```\nmysqldump -u root attendance_system > backup.sql\n```";
    }

    return "I'm here to help! You can ask me about:\n\n" .
        "• System features and navigation\n" .
        "• Attendance tracking\n" .
        "• Class schedules\n" .
        "• Grades and reports\n" .
        "• Messages and communication\n\n" .
        "Just type your question!";
}

function handleFeeQuery($message, $role, $user_id)
{
    if ($role === 'parent') {
        return "💰 Fee Payment Information:\n\n" .
            "To view and pay fees:\n" .
            "1. Go to 'Fees & Payments' in Academic section\n" .
            "2. View outstanding balances\n" .
            "3. Click 'Pay Now' for online payment\n" .
            "4. Download receipts after payment\n\n" .
            "Payment methods: Credit Card, Debit Card, Bank Transfer\n\n" .
            "Need help with a specific fee? Let me know!";
    }

    return "For fee-related queries, please visit the Fee Management section or contact administration.";
}

function generateDefaultResponse($role)
{
    $responses = [
        'student' => "I can help you with:\n• Checking attendance\n• Viewing schedules\n• Assignment info\n• System navigation\n\nWhat would you like to know?",
        'teacher' => "How can I assist you today?\n• Draft messages\n• Attendance summaries\n• Student insights\n• System features\n\nJust ask!",
        'parent' => "I'm here to help with:\n• Children's status\n• Attendance reports\n• Fee payments\n• Teacher communication\n\nWhat do you need?",
        'admin' => "Available assistance:\n• System analytics\n• User management\n• Technical support\n• Database operations\n\nHow can I help?"
    ];

    return $responses[$role] ?? "How can I assist you today?";
}
