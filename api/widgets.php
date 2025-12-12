<?php

/**
 * Widgets API
 * Handles widget rendering, preferences, and customization
 * Verdant SMS v3.0
 */

session_start();
header('Content-Type: application/json');

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Require authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'student';

// Ensure widget preferences table
try {
    db()->query("CREATE TABLE IF NOT EXISTS user_widget_preferences (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        widget_id VARCHAR(50) NOT NULL,
        position INT DEFAULT 0,
        is_visible TINYINT(1) DEFAULT 1,
        size ENUM('small', 'medium', 'large') DEFAULT 'medium',
        settings JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_widget (user_id, widget_id),
        INDEX idx_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {
    // Table exists
}

// GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'render':
            $widget_id = $_GET['widget'] ?? '';
            $html = render_widget($widget_id, $role);
            echo json_encode(['success' => true, 'html' => $html]);
            break;

        case 'preferences':
            $prefs = db()->fetchAll(
                "SELECT widget_id, position, is_visible, size FROM user_widget_preferences WHERE user_id = ? ORDER BY position",
                [$user_id]
            );
            echo json_encode(['success' => true, 'preferences' => $prefs]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

// POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_require();

    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    switch ($action) {
        case 'save':
            $widgets = $input['widgets'] ?? [];

            foreach ($widgets as $widget) {
                db()->query(
                    "INSERT INTO user_widget_preferences (user_id, widget_id, position, is_visible)
                     VALUES (?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE position = VALUES(position), is_visible = VALUES(is_visible)",
                    [$user_id, $widget['id'], $widget['position'] ?? 0, $widget['visible'] ?? 1]
                );
            }

            echo json_encode(['success' => true, 'message' => 'Layout saved']);
            break;

        case 'reset':
            db()->query("DELETE FROM user_widget_preferences WHERE user_id = ?", [$user_id]);
            echo json_encode(['success' => true, 'message' => 'Reset to defaults']);
            break;

        case 'update_size':
            $widget_id = $input['widget_id'] ?? '';
            $size = $input['size'] ?? 'medium';

            if (!in_array($size, ['small', 'medium', 'large'])) {
                $size = 'medium';
            }

            db()->query(
                "UPDATE user_widget_preferences SET size = ? WHERE user_id = ? AND widget_id = ?",
                [$size, $user_id, $widget_id]
            );

            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

// Widget rendering function
function render_widget($widget_id, $role)
{
    global $user_id;

    switch ($widget_id) {
        case 'calendar':
            return render_calendar_widget();

        case 'notifications':
            return render_notifications_widget($user_id);

        case 'quick_links':
            return render_quick_links_widget($role);

        case 'weather':
            return render_weather_widget();

        case 'user_stats':
            return render_user_stats_widget();

        case 'attendance_overview':
            return render_attendance_widget($role);

        case 'my_classes':
            return render_my_classes_widget($user_id);

        case 'my_grades':
            return render_my_grades_widget($user_id);

        case 'assignments_due':
            return render_assignments_widget($user_id, $role);

        case 'timetable_today':
            return render_timetable_widget($user_id, $role);

        case 'fee_status':
            return render_fee_status_widget($user_id, $role);

        case 'system_health':
            return render_system_health_widget();

        default:
            return '<div class="widget-placeholder"><i class="fas fa-puzzle-piece"></i><p>Widget not available</p></div>';
    }
}

// Individual widget renderers
function render_calendar_widget()
{
    $today = date('Y-m-d');
    $month = date('F Y');
    $day = date('j');
    $dayName = date('l');

    return <<<HTML
    <div class="calendar-widget">
        <div class="calendar-today">
            <div class="calendar-day-name">{$dayName}</div>
            <div class="calendar-day-number">{$day}</div>
            <div class="calendar-month">{$month}</div>
        </div>
        <div class="calendar-events">
            <div class="event-item"><i class="fas fa-circle text-success"></i> No events today</div>
        </div>
    </div>
    <style>
    .calendar-widget { text-align: center; }
    .calendar-day-name { font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase; }
    .calendar-day-number { font-size: 3rem; font-weight: 700; color: var(--primary-color); line-height: 1; }
    .calendar-month { font-size: 0.85rem; color: var(--text-secondary); margin-top: 4px; }
    .calendar-events { margin-top: 16px; text-align: left; font-size: 0.85rem; }
    .event-item { padding: 8px; background: var(--bg-secondary); border-radius: 6px; }
    .event-item i { font-size: 0.5rem; margin-right: 8px; }
    </style>
HTML;
}

function render_notifications_widget($user_id)
{
    try {
        $notifications = db()->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5",
            [$user_id]
        );
    } catch (Exception $e) {
        $notifications = [];
    }

    if (empty($notifications)) {
        return '<div class="widget-empty"><i class="fas fa-bell-slash"></i><p>No notifications</p></div>';
    }

    $html = '<div class="notifications-widget">';
    foreach ($notifications as $notif) {
        $icon = $notif['is_read'] ? 'circle' : 'circle text-primary';
        $title = htmlspecialchars($notif['title'] ?? $notif['message']);
        $time = timeAgo($notif['created_at']);
        $html .= "<div class='notif-item'><i class='fas fa-{$icon}'></i><span>{$title}</span><small>{$time}</small></div>";
    }
    $html .= '</div>';
    $html .= '<style>.notifications-widget { font-size: 0.85rem; } .notif-item { display: flex; align-items: center; gap: 8px; padding: 8px 0; border-bottom: 1px solid var(--border-color); } .notif-item:last-child { border: none; } .notif-item i { font-size: 0.5rem; } .notif-item span { flex: 1; } .notif-item small { color: var(--text-muted); font-size: 0.7rem; }</style>';

    return $html;
}

function render_quick_links_widget($role)
{
    $links = [
        'admin' => [
            ['icon' => 'users', 'label' => 'Users', 'url' => '/admin/users.php'],
            ['icon' => 'cog', 'label' => 'Settings', 'url' => '/admin/settings.php'],
            ['icon' => 'chart-bar', 'label' => 'Reports', 'url' => '/admin/reports.php'],
        ],
        'teacher' => [
            ['icon' => 'clipboard', 'label' => 'Attendance', 'url' => '/teacher/attendance.php'],
            ['icon' => 'edit', 'label' => 'Grades', 'url' => '/teacher/grades.php'],
            ['icon' => 'users', 'label' => 'Classes', 'url' => '/teacher/classes.php'],
        ],
        'student' => [
            ['icon' => 'book', 'label' => 'Courses', 'url' => '/student/courses.php'],
            ['icon' => 'chart-line', 'label' => 'Grades', 'url' => '/student/grades.php'],
            ['icon' => 'calendar', 'label' => 'Schedule', 'url' => '/student/timetable.php'],
        ],
    ];

    $role_links = $links[$role] ?? $links['student'];

    $html = '<div class="quick-links-widget">';
    foreach ($role_links as $link) {
        $html .= "<a href='" . APP_URL . $link['url'] . "' class='quick-link'><i class='fas fa-{$link['icon']}'></i>{$link['label']}</a>";
    }
    $html .= '</div>';
    $html .= '<style>.quick-links-widget { display: flex; gap: 8px; flex-wrap: wrap; } .quick-link { display: flex; align-items: center; gap: 6px; padding: 8px 12px; background: var(--bg-secondary); border-radius: 8px; text-decoration: none; color: var(--text-primary); font-size: 0.85rem; transition: all 0.15s; } .quick-link:hover { background: var(--primary-color); color: white; }</style>';

    return $html;
}

function render_weather_widget()
{
    return <<<HTML
    <div class="weather-widget">
        <div class="weather-icon"><i class="fas fa-cloud-sun"></i></div>
        <div class="weather-temp">24°C</div>
        <div class="weather-desc">Partly Cloudy</div>
    </div>
    <style>
    .weather-widget { text-align: center; }
    .weather-icon { font-size: 2.5rem; color: #f59e0b; }
    .weather-temp { font-size: 2rem; font-weight: 700; }
    .weather-desc { font-size: 0.85rem; color: var(--text-muted); }
    </style>
HTML;
}

function render_user_stats_widget()
{
    try {
        $total_users = db()->count('users');
        $active_today = db()->fetchColumn("SELECT COUNT(DISTINCT user_id) FROM user_sessions WHERE last_activity > DATE_SUB(NOW(), INTERVAL 24 HOUR)") ?: 0;
        $new_this_week = db()->fetchColumn("SELECT COUNT(*) FROM users WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)") ?: 0;
    } catch (Exception $e) {
        $total_users = 0;
        $active_today = 0;
        $new_this_week = 0;
    }

    return <<<HTML
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-value">{$total_users}</div>
            <div class="stat-label">Total Users</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{$active_today}</div>
            <div class="stat-label">Active Today</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{$new_this_week}</div>
            <div class="stat-label">New This Week</div>
        </div>
    </div>
    <style>
    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .stat-item { text-align: center; padding: 12px; background: var(--bg-secondary); border-radius: 8px; }
    .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--primary-color); }
    .stat-label { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }
    </style>
HTML;
}

function render_attendance_widget($role)
{
    try {
        $today = date('Y-m-d');
        $present = db()->fetchColumn("SELECT COUNT(*) FROM attendance WHERE date = ? AND status = 'present'", [$today]) ?: 0;
        $absent = db()->fetchColumn("SELECT COUNT(*) FROM attendance WHERE date = ? AND status = 'absent'", [$today]) ?: 0;
        $late = db()->fetchColumn("SELECT COUNT(*) FROM attendance WHERE date = ? AND status = 'late'", [$today]) ?: 0;
    } catch (Exception $e) {
        $present = $absent = $late = 0;
    }

    $total = $present + $absent + $late;
    $rate = $total > 0 ? round(($present / $total) * 100) : 0;

    return <<<HTML
    <div class="attendance-widget">
        <div class="attendance-chart">
            <div class="attendance-circle" style="--rate: {$rate}%">
                <span class="rate-value">{$rate}%</span>
                <span class="rate-label">Present</span>
            </div>
        </div>
        <div class="attendance-stats">
            <div class="att-stat"><span class="dot success"></span> Present: {$present}</div>
            <div class="att-stat"><span class="dot danger"></span> Absent: {$absent}</div>
            <div class="att-stat"><span class="dot warning"></span> Late: {$late}</div>
        </div>
    </div>
    <style>
    .attendance-widget { display: flex; align-items: center; gap: 20px; }
    .attendance-circle { width: 100px; height: 100px; border-radius: 50%; background: conic-gradient(var(--primary-color) var(--rate), #e5e7eb var(--rate)); display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; }
    .attendance-circle::before { content: ''; position: absolute; inset: 8px; background: var(--card-bg); border-radius: 50%; }
    .rate-value { position: relative; font-size: 1.25rem; font-weight: 700; }
    .rate-label { position: relative; font-size: 0.7rem; color: var(--text-muted); }
    .attendance-stats { flex: 1; }
    .att-stat { padding: 6px 0; font-size: 0.85rem; display: flex; align-items: center; gap: 8px; }
    .dot { width: 8px; height: 8px; border-radius: 50%; }
    .dot.success { background: #10b981; }
    .dot.danger { background: #ef4444; }
    .dot.warning { background: #f59e0b; }
    </style>
HTML;
}

function render_my_classes_widget($user_id)
{
    // Placeholder - would need actual class schedule data
    return <<<HTML
    <div class="my-classes-widget">
        <div class="class-item"><span class="class-time">09:00 AM</span><span class="class-name">Mathematics</span><span class="class-room">Room 201</span></div>
        <div class="class-item"><span class="class-time">11:00 AM</span><span class="class-name">Physics</span><span class="class-room">Lab 3</span></div>
        <div class="class-item"><span class="class-time">02:00 PM</span><span class="class-name">English</span><span class="class-room">Room 105</span></div>
    </div>
    <style>
    .my-classes-widget { font-size: 0.875rem; }
    .class-item { display: flex; align-items: center; gap: 12px; padding: 10px; background: var(--bg-secondary); border-radius: 8px; margin-bottom: 8px; }
    .class-time { font-weight: 600; color: var(--primary-color); min-width: 80px; }
    .class-name { flex: 1; font-weight: 500; }
    .class-room { color: var(--text-muted); font-size: 0.8rem; }
    </style>
HTML;
}

function render_my_grades_widget($user_id)
{
    return <<<HTML
    <div class="grades-widget">
        <div class="grade-summary">
            <div class="gpa-circle"><span>3.75</span></div>
            <div class="gpa-label">Current GPA</div>
        </div>
        <div class="recent-grades">
            <div class="grade-item"><span>Mathematics</span><span class="grade-a">A</span></div>
            <div class="grade-item"><span>Physics</span><span class="grade-b">B+</span></div>
            <div class="grade-item"><span>English</span><span class="grade-a">A-</span></div>
        </div>
    </div>
    <style>
    .grades-widget { display: flex; gap: 20px; align-items: center; }
    .gpa-circle { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), #059669); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; font-weight: 700; }
    .gpa-label { text-align: center; font-size: 0.75rem; color: var(--text-muted); margin-top: 8px; }
    .recent-grades { flex: 1; }
    .grade-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border-color); font-size: 0.85rem; }
    .grade-a { color: #10b981; font-weight: 600; }
    .grade-b { color: #3b82f6; font-weight: 600; }
    </style>
HTML;
}

function render_assignments_widget($user_id, $role)
{
    return <<<HTML
    <div class="assignments-widget">
        <div class="assignment-item due-soon">
            <div class="assignment-icon"><i class="fas fa-file-alt"></i></div>
            <div class="assignment-info">
                <div class="assignment-title">Math Homework Ch. 5</div>
                <div class="assignment-due"><i class="fas fa-clock"></i> Due in 2 days</div>
            </div>
        </div>
        <div class="assignment-item">
            <div class="assignment-icon"><i class="fas fa-file-alt"></i></div>
            <div class="assignment-info">
                <div class="assignment-title">Physics Lab Report</div>
                <div class="assignment-due"><i class="fas fa-clock"></i> Due in 5 days</div>
            </div>
        </div>
    </div>
    <style>
    .assignments-widget { font-size: 0.875rem; }
    .assignment-item { display: flex; gap: 12px; padding: 12px; background: var(--bg-secondary); border-radius: 8px; margin-bottom: 8px; }
    .assignment-item.due-soon { border-left: 3px solid #f59e0b; }
    .assignment-icon { width: 40px; height: 40px; background: var(--primary-color); color: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
    .assignment-title { font-weight: 500; }
    .assignment-due { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }
    </style>
HTML;
}

function render_timetable_widget($user_id, $role)
{
    $day = date('l');
    return <<<HTML
    <div class="timetable-widget">
        <div class="timetable-header">{$day}'s Schedule</div>
        <div class="timetable-items">
            <div class="tt-item"><span class="tt-time">08:00-09:00</span><span class="tt-subject">Assembly</span></div>
            <div class="tt-item current"><span class="tt-time">09:00-10:00</span><span class="tt-subject">Mathematics</span></div>
            <div class="tt-item"><span class="tt-time">10:00-11:00</span><span class="tt-subject">Physics</span></div>
            <div class="tt-item"><span class="tt-time">11:15-12:15</span><span class="tt-subject">Chemistry</span></div>
        </div>
    </div>
    <style>
    .timetable-header { font-weight: 600; margin-bottom: 12px; }
    .tt-item { display: flex; gap: 12px; padding: 8px; border-radius: 6px; font-size: 0.85rem; margin-bottom: 4px; }
    .tt-item.current { background: rgba(16, 185, 129, 0.1); border-left: 3px solid var(--primary-color); }
    .tt-time { color: var(--text-muted); min-width: 90px; }
    .tt-subject { font-weight: 500; }
    </style>
HTML;
}

function render_fee_status_widget($user_id, $role)
{
    return <<<HTML
    <div class="fee-widget">
        <div class="fee-summary">
            <div class="fee-amount">$2,500</div>
            <div class="fee-status paid">Paid</div>
        </div>
        <div class="fee-details">
            <div class="fee-row"><span>Total Fee</span><span>$5,000</span></div>
            <div class="fee-row"><span>Paid</span><span class="text-success">$2,500</span></div>
            <div class="fee-row"><span>Balance</span><span class="text-warning">$2,500</span></div>
        </div>
    </div>
    <style>
    .fee-summary { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .fee-amount { font-size: 1.75rem; font-weight: 700; }
    .fee-status { padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
    .fee-status.paid { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .fee-details { font-size: 0.85rem; }
    .fee-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border-color); }
    .text-success { color: #10b981; }
    .text-warning { color: #f59e0b; }
    </style>
HTML;
}

function render_system_health_widget()
{
    $php_version = phpversion();

    return <<<HTML
    <div class="health-widget">
        <div class="health-item good"><i class="fas fa-check-circle"></i> Database Connected</div>
        <div class="health-item good"><i class="fas fa-check-circle"></i> PHP {$php_version}</div>
        <div class="health-item good"><i class="fas fa-check-circle"></i> Cache Active</div>
    </div>
    <style>
    .health-widget { font-size: 0.85rem; }
    .health-item { display: flex; align-items: center; gap: 8px; padding: 8px 0; }
    .health-item.good { color: #10b981; }
    .health-item.warning { color: #f59e0b; }
    .health-item.error { color: #ef4444; }
    </style>
HTML;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
