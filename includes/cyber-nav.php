<?php

/**
 * Cyberpunk Sidebar Navigation Component
 * Advanced UI with Holographic Effects
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include theme loader
require_once __DIR__ . '/theme-loader.php';

$current_page = basename($_SERVER['PHP_SELF']);
$user_name = $_SESSION['full_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'user';
$user_id = $_SESSION['user_id'] ?? 0;
$user_initials = strtoupper(substr($user_name, 0, 2));

// Get unread messages count
$unread_count = 0;
if ($user_id > 0) {
    try {
        $result = db()->fetchOne("
            SELECT COUNT(*) as count FROM message_recipients
            WHERE recipient_id = ? AND is_read = 0 AND deleted_at IS NULL
        ", [$user_id]);
        $unread_count = $result['count'] ?? 0;
    } catch (Exception $e) {
        $unread_count = 0;
    }
}

// Role-specific navigation menu structure
$nav_sections = [];

if ($user_role === 'admin') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'tachometer-alt', 'label' => 'Dashboard', 'badge' => null],
            'overview.php' => ['icon' => 'chart-pie', 'label' => 'System Overview', 'badge' => null],
            'students.php' => ['icon' => 'user-graduate', 'label' => 'Students', 'badge' => null],
            'teachers.php' => ['icon' => 'chalkboard-teacher', 'label' => 'Teachers', 'badge' => null],
            'parents.php' => ['icon' => 'users', 'label' => 'Parents', 'badge' => null],
            'classes.php' => ['icon' => 'door-open', 'label' => 'Classes', 'badge' => null],
            'attendance.php' => ['icon' => 'check-circle', 'label' => 'Attendance', 'badge' => null],
        ],
        'Academics' => [
            'academics/subjects.php' => ['icon' => 'book-open', 'label' => 'Subjects', 'badge' => null],
            'academics/syllabus.php' => ['icon' => 'list-alt', 'label' => 'Syllabus', 'badge' => null],
            'academics/exams.php' => ['icon' => 'clipboard-check', 'label' => 'Examinations', 'badge' => null],
            'academics/timetable.php' => ['icon' => 'calendar-week', 'label' => 'Timetable', 'badge' => null],
        ],
        'Finance' => [
            'finance/fee-structures.php' => ['icon' => 'money-bill-wave', 'label' => 'Fee Structures', 'badge' => null],
            'finance/invoices.php' => ['icon' => 'file-invoice-dollar', 'label' => 'Invoices', 'badge' => null],
            'finance/payments.php' => ['icon' => 'credit-card', 'label' => 'Payments', 'badge' => null],
            'finance/payroll.php' => ['icon' => 'hand-holding-usd', 'label' => 'Payroll', 'badge' => null],
        ],
        'Library' => [
            'library/books.php' => ['icon' => 'book', 'label' => 'Books', 'badge' => null],
            'library/issue-return.php' => ['icon' => 'exchange-alt', 'label' => 'Issue/Return', 'badge' => null],
            'library/members.php' => ['icon' => 'id-card', 'label' => 'Members', 'badge' => null],
        ],
        'Transport' => [
            'transport/routes.php' => ['icon' => 'route', 'label' => 'Routes', 'badge' => null],
            'transport/vehicles.php' => ['icon' => 'bus', 'label' => 'Vehicles', 'badge' => null],
            'transport/drivers.php' => ['icon' => 'id-badge', 'label' => 'Drivers', 'badge' => null],
        ],
        'Hostel' => [
            'hostel/hostels.php' => ['icon' => 'building', 'label' => 'Hostels', 'badge' => null],
            'hostel/rooms.php' => ['icon' => 'bed', 'label' => 'Rooms', 'badge' => null],
            'hostel/allocations.php' => ['icon' => 'user-plus', 'label' => 'Allocations', 'badge' => null],
        ],
        'HR & Payroll' => [
            'hr/departments.php' => ['icon' => 'sitemap', 'label' => 'Departments', 'badge' => null],
            'hr/staff.php' => ['icon' => 'users', 'label' => 'Staff', 'badge' => null],
            'hr/attendance.php' => ['icon' => 'clock', 'label' => 'Staff Attendance', 'badge' => null],
            'hr/leave.php' => ['icon' => 'calendar-times', 'label' => 'Leave Management', 'badge' => null],
        ],
        'Inventory' => [
            'inventory/assets.php' => ['icon' => 'boxes', 'label' => 'Assets', 'badge' => null],
            'inventory/stock.php' => ['icon' => 'warehouse', 'label' => 'Stock', 'badge' => null],
            'inventory/purchase-orders.php' => ['icon' => 'shopping-cart', 'label' => 'Purchase Orders', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
            'notices.php' => ['icon' => 'cog', 'label' => 'Manage Notices', 'badge' => null],
            '../forum/index.php' => ['icon' => 'comments', 'label' => 'The Quad Forum', 'badge' => null],
            'emergency-alerts.php' => ['icon' => 'exclamation-triangle', 'label' => 'Emergency Alerts', 'badge' => null],
        ],
        'Analytics' => [
            'reports.php' => ['icon' => 'chart-line', 'label' => 'Reports', 'badge' => null],
            'analytics.php' => ['icon' => 'brain', 'label' => 'AI Analytics', 'badge' => 'AI'],
            'activity-monitor.php' => ['icon' => 'chart-bar', 'label' => 'Activity Monitor', 'badge' => null],
        ],
        'System' => [
            'system-health.php' => ['icon' => 'heartbeat', 'label' => 'System Health', 'badge' => null],
            'audit-logs.php' => ['icon' => 'clipboard-list', 'label' => 'Audit Logs', 'badge' => null],
            'backup-export.php' => ['icon' => 'database', 'label' => 'Backup & Export', 'badge' => null],
            'lms-settings.php' => ['icon' => 'graduation-cap', 'label' => 'LMS Integration', 'badge' => 'LTI'],
        ],
        'Management' => [
            'users.php' => ['icon' => 'users-cog', 'label' => 'Users', 'badge' => null],
            'registrations.php' => ['icon' => 'user-plus', 'label' => 'Registrations', 'badge' => null],
            'class-enrollment.php' => ['icon' => 'user-graduate', 'label' => 'Class Enrollment', 'badge' => null],
            'manage-ids.php' => ['icon' => 'id-card', 'label' => 'Manage IDs', 'badge' => null],
            'approve-users.php' => ['icon' => 'user-check', 'label' => 'Approve Users', 'badge' => null],
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'teacher') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'tachometer-alt', 'label' => 'Dashboard', 'badge' => null],
            'my-classes.php' => ['icon' => 'door-open', 'label' => 'My Classes', 'badge' => null],
            'students.php' => ['icon' => 'user-graduate', 'label' => 'My Students', 'badge' => null],
            'attendance.php' => ['icon' => 'clipboard-check', 'label' => 'Mark Attendance', 'badge' => null],
        ],
        'Academic' => [
            'materials.php' => ['icon' => 'file-upload', 'label' => 'Class Materials', 'badge' => null],
            'assignments.php' => ['icon' => 'tasks', 'label' => 'Assignments', 'badge' => null],
            'grades.php' => ['icon' => 'graduation-cap', 'label' => 'Grades', 'badge' => null],
            'class-enrollment.php' => ['icon' => 'user-graduate', 'label' => 'Enroll Students', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            'parent-comms.php' => ['icon' => 'users', 'label' => 'Parent Communication', 'badge' => null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
            '../forum/index.php' => ['icon' => 'comments', 'label' => 'The Quad Forum', 'badge' => null],
            'resources.php' => ['icon' => 'book', 'label' => 'My Resources', 'badge' => null],
            'resource-library.php' => ['icon' => 'globe', 'label' => 'Resource Library', 'badge' => null],
            'meeting-hours.php' => ['icon' => 'calendar-alt', 'label' => 'Meeting Hours', 'badge' => null],
            'behavior-logs.php' => ['icon' => 'clipboard-list', 'label' => 'Behavior Logs', 'badge' => null],
        ],
        'Analytics' => [
            'analytics.php' => ['icon' => 'chart-line', 'label' => 'Performance Analytics', 'badge' => null],
            'reports.php' => ['icon' => 'file-alt', 'label' => 'Report Generator', 'badge' => null],
            'lms-sync.php' => ['icon' => 'sync-alt', 'label' => 'LMS Sync', 'badge' => 'LMS'],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'student') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'tachometer-alt', 'label' => 'Dashboard', 'badge' => null],
            'schedule.php' => ['icon' => 'calendar-alt', 'label' => 'My Schedule', 'badge' => null],
            'attendance.php' => ['icon' => 'clipboard-list', 'label' => 'Attendance', 'badge' => null],
            'checkin.php' => ['icon' => 'fingerprint', 'label' => 'Check-in', 'badge' => null],
        ],
        'Academic' => [
            'class-registration.php' => ['icon' => 'user-plus', 'label' => 'Class Registration', 'badge' => null],
            'subjects.php' => ['icon' => 'book-open', 'label' => 'My Subjects', 'badge' => null],
            'assignments.php' => ['icon' => 'clipboard-list', 'label' => 'Assignments', 'badge' => null],
            'grades.php' => ['icon' => 'chart-line', 'label' => 'My Grades', 'badge' => null],
            'exams.php' => ['icon' => 'clipboard-check', 'label' => 'Examinations', 'badge' => null],
            'events.php' => ['icon' => 'calendar-check', 'label' => 'Events', 'badge' => null],
            'lms-portal.php' => ['icon' => 'graduation-cap', 'label' => 'LMS Portal', 'badge' => 'LMS'],
        ],
        'Finance' => [
            'fee-invoices.php' => ['icon' => 'file-invoice', 'label' => 'Fee Invoices', 'badge' => null],
            'payments.php' => ['icon' => 'credit-card', 'label' => 'Make Payment', 'badge' => null],
        ],
        'Library' => [
            'search-books.php' => ['icon' => 'search', 'label' => 'Search Books', 'badge' => null],
            'my-books.php' => ['icon' => 'book-reader', 'label' => 'My Books', 'badge' => null],
        ],
        'Transport' => [
            'my-route.php' => ['icon' => 'route', 'label' => 'My Route', 'badge' => null],
            'track-bus.php' => ['icon' => 'map-marked-alt', 'label' => 'Track Bus', 'badge' => null],
        ],
        'Hostel' => [
            'my-room.php' => ['icon' => 'bed', 'label' => 'My Room', 'badge' => null],
            'mess-menu.php' => ['icon' => 'utensils', 'label' => 'Mess Menu', 'badge' => null],
            'complaints.php' => ['icon' => 'exclamation-circle', 'label' => 'Complaints', 'badge' => null],
        ],
        'Communication' => [
            'communication.php' => ['icon' => 'comment-dots', 'label' => 'Student Chat', 'badge' => 'NEW'],
            '../messages.php' => ['icon' => 'envelope', 'label' => 'Inbox', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
            '../forum/index.php' => ['icon' => 'comments', 'label' => 'The Quad Forum', 'badge' => null],
            'study-groups.php' => ['icon' => 'users', 'label' => 'Study Groups', 'badge' => null],
        ],
        'Account' => [
            'profile.php' => ['icon' => 'user', 'label' => 'Profile', 'badge' => null],
            'id-card.php' => ['icon' => 'id-card', 'label' => 'Digital ID Card', 'badge' => 'NEW'],
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'parent') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'home', 'label' => 'Dashboard', 'badge' => null],
            'link-children.php' => ['icon' => 'link', 'label' => 'Link Children', 'badge' => null],
            'attendance.php' => ['icon' => 'clipboard-list', 'label' => 'Attendance', 'badge' => null],
        ],
        'Academic' => [
            'grades.php' => ['icon' => 'chart-bar', 'label' => "Children's Grades", 'badge' => null],
            'fees.php' => ['icon' => 'wallet', 'label' => 'Fees & Payments', 'badge' => null],
            'events.php' => ['icon' => 'calendar-alt', 'label' => 'Events & Calendar', 'badge' => null],
            'lms-overview.php' => ['icon' => 'graduation-cap', 'label' => 'LMS Overview', 'badge' => 'LMS'],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            'communication.php' => ['icon' => 'envelope', 'label' => 'Contact Teachers', 'badge' => null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
            '../forum/index.php' => ['icon' => 'comments', 'label' => 'The Quad Forum', 'badge' => null],
            'book-meeting.php' => ['icon' => 'calendar-plus', 'label' => 'Book Meeting', 'badge' => 'NEW'],
            'my-meetings.php' => ['icon' => 'calendar-check', 'label' => 'My Meetings', 'badge' => null],
        ],
        'Analytics' => [
            'analytics.php' => ['icon' => 'chart-line', 'label' => 'Family Analytics', 'badge' => 'AI'],
            'reports.php' => ['icon' => 'file-alt', 'label' => 'Reports', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'superadmin') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'crown', 'label' => 'Dashboard', 'badge' => null],
            'schools.php' => ['icon' => 'school', 'label' => 'Schools', 'badge' => null],
            'system-health.php' => ['icon' => 'heartbeat', 'label' => 'System Health', 'badge' => null],
        ],
        'Management' => [
            'users.php' => ['icon' => 'users-cog', 'label' => 'All Users', 'badge' => null],
            'roles.php' => ['icon' => 'user-shield', 'label' => 'Role Management', 'badge' => null],
            'permissions.php' => ['icon' => 'key', 'label' => 'Permissions', 'badge' => null],
        ],
        'System' => [
            'audit-logs.php' => ['icon' => 'clipboard-list', 'label' => 'Audit Logs', 'badge' => null],
            'backup-restore.php' => ['icon' => 'database', 'label' => 'Backup & Restore', 'badge' => null],
            'maintenance.php' => ['icon' => 'tools', 'label' => 'Maintenance', 'badge' => null],
            'settings.php' => ['icon' => 'cog', 'label' => 'System Settings', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'owner') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'building', 'label' => 'Dashboard', 'badge' => null],
            'revenue.php' => ['icon' => 'chart-line', 'label' => 'Revenue Analytics', 'badge' => null],
            'schools.php' => ['icon' => 'school', 'label' => 'My Schools', 'badge' => null],
        ],
        'Finance' => [
            'financial-overview.php' => ['icon' => 'wallet', 'label' => 'Financial Overview', 'badge' => null],
            'investments.php' => ['icon' => 'hand-holding-usd', 'label' => 'Investments', 'badge' => null],
            'reports.php' => ['icon' => 'file-invoice-dollar', 'label' => 'Financial Reports', 'badge' => null],
        ],
        'Management' => [
            'principals.php' => ['icon' => 'user-tie', 'label' => 'Principals', 'badge' => null],
            'performance.php' => ['icon' => 'chart-bar', 'label' => 'Performance', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'principal') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'landmark', 'label' => 'Dashboard', 'badge' => null],
            'overview.php' => ['icon' => 'chart-pie', 'label' => 'School Overview', 'badge' => null],
        ],
        'Academic' => [
            'teachers.php' => ['icon' => 'chalkboard-teacher', 'label' => 'Teachers', 'badge' => null],
            'students.php' => ['icon' => 'user-graduate', 'label' => 'Students', 'badge' => null],
            'classes.php' => ['icon' => 'door-open', 'label' => 'Classes', 'badge' => null],
            'attendance.php' => ['icon' => 'clipboard-check', 'label' => 'Attendance', 'badge' => null],
            'academics.php' => ['icon' => 'book-open', 'label' => 'Academics', 'badge' => null],
        ],
        'Management' => [
            'staff.php' => ['icon' => 'users', 'label' => 'Staff Management', 'badge' => null],
            'approvals.php' => ['icon' => 'user-check', 'label' => 'Approvals', 'badge' => null],
            'announcements.php' => ['icon' => 'bullhorn', 'label' => 'Announcements', 'badge' => null],
        ],
        'Analytics' => [
            'reports.php' => ['icon' => 'chart-line', 'label' => 'Reports', 'badge' => null],
            'analytics.php' => ['icon' => 'brain', 'label' => 'AI Analytics', 'badge' => 'AI'],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
            '../forum/index.php' => ['icon' => 'comments', 'label' => 'The Quad Forum', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'vice-principal') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'user-tie', 'label' => 'Dashboard', 'badge' => null],
            'overview.php' => ['icon' => 'chart-pie', 'label' => 'Overview', 'badge' => null],
        ],
        'Discipline' => [
            'discipline.php' => ['icon' => 'gavel', 'label' => 'Discipline Records', 'badge' => null],
            'incidents.php' => ['icon' => 'exclamation-triangle', 'label' => 'Incidents', 'badge' => null],
            'behavior.php' => ['icon' => 'user-shield', 'label' => 'Behavior Tracking', 'badge' => null],
        ],
        'Academic' => [
            'attendance.php' => ['icon' => 'clipboard-check', 'label' => 'Attendance', 'badge' => null],
            'substitutions.php' => ['icon' => 'exchange-alt', 'label' => 'Substitutions', 'badge' => null],
            'timetable.php' => ['icon' => 'calendar-week', 'label' => 'Timetable', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'accountant') {
    $nav_sections = [
        'Core' => [
            'dashboard/' => ['icon' => 'calculator', 'label' => 'Dashboard', 'badge' => null],
        ],
        'Finance' => [
            'fee-collection.php' => ['icon' => 'money-bill-wave', 'label' => 'Fee Collection', 'badge' => null],
            'invoices.php' => ['icon' => 'file-invoice-dollar', 'label' => 'Invoices', 'badge' => null],
            'payments.php' => ['icon' => 'credit-card', 'label' => 'Payments', 'badge' => null],
            'expenses.php' => ['icon' => 'receipt', 'label' => 'Expenses', 'badge' => null],
        ],
        'Payroll' => [
            'payroll.php' => ['icon' => 'hand-holding-usd', 'label' => 'Payroll', 'badge' => null],
            'salary-slips.php' => ['icon' => 'file-alt', 'label' => 'Salary Slips', 'badge' => null],
        ],
        'Reports' => [
            'reports.php' => ['icon' => 'chart-line', 'label' => 'Financial Reports', 'badge' => null],
            'ledger.php' => ['icon' => 'book', 'label' => 'Ledger', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'librarian') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'book-reader', 'label' => 'Dashboard', 'badge' => null],
        ],
        'Catalog' => [
            'books.php' => ['icon' => 'book', 'label' => 'Book Catalog', 'badge' => null],
            'add-book.php' => ['icon' => 'plus-circle', 'label' => 'Add Book', 'badge' => null],
            'categories.php' => ['icon' => 'tags', 'label' => 'Categories', 'badge' => null],
        ],
        'Circulation' => [
            'issue-book.php' => ['icon' => 'hand-holding', 'label' => 'Issue Book', 'badge' => null],
            'return-book.php' => ['icon' => 'undo', 'label' => 'Return Book', 'badge' => null],
            'overdue.php' => ['icon' => 'exclamation-circle', 'label' => 'Overdue Books', 'badge' => null],
        ],
        'Members' => [
            'members.php' => ['icon' => 'users', 'label' => 'Members', 'badge' => null],
            'fines.php' => ['icon' => 'money-bill', 'label' => 'Fines', 'badge' => null],
        ],
        'Reports' => [
            'reports.php' => ['icon' => 'chart-bar', 'label' => 'Reports', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'transport') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'bus', 'label' => 'Dashboard', 'badge' => null],
        ],
        'Fleet' => [
            'vehicles.php' => ['icon' => 'car', 'label' => 'Vehicles', 'badge' => null],
            'drivers.php' => ['icon' => 'id-badge', 'label' => 'Drivers', 'badge' => null],
            'maintenance.php' => ['icon' => 'tools', 'label' => 'Maintenance', 'badge' => null],
        ],
        'Routes' => [
            'routes.php' => ['icon' => 'route', 'label' => 'Routes', 'badge' => null],
            'stops.php' => ['icon' => 'map-marker-alt', 'label' => 'Stops', 'badge' => null],
            'tracking.php' => ['icon' => 'map-marked-alt', 'label' => 'Live Tracking', 'badge' => null],
        ],
        'Students' => [
            'allocations.php' => ['icon' => 'user-plus', 'label' => 'Allocations', 'badge' => null],
            'attendance.php' => ['icon' => 'clipboard-check', 'label' => 'Attendance', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'hostel') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'bed', 'label' => 'Dashboard', 'badge' => null],
        ],
        'Accommodation' => [
            'hostels.php' => ['icon' => 'building', 'label' => 'Hostels', 'badge' => null],
            'rooms.php' => ['icon' => 'door-open', 'label' => 'Rooms', 'badge' => null],
            'allocations.php' => ['icon' => 'user-plus', 'label' => 'Allocations', 'badge' => null],
        ],
        'Management' => [
            'attendance.php' => ['icon' => 'clipboard-check', 'label' => 'Attendance', 'badge' => null],
            'mess.php' => ['icon' => 'utensils', 'label' => 'Mess Management', 'badge' => null],
            'complaints.php' => ['icon' => 'exclamation-circle', 'label' => 'Complaints', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'canteen') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'utensils', 'label' => 'Dashboard', 'badge' => null],
        ],
        'Menu' => [
            'menu.php' => ['icon' => 'clipboard-list', 'label' => 'Menu Management', 'badge' => null],
            'items.php' => ['icon' => 'hamburger', 'label' => 'Food Items', 'badge' => null],
            'categories.php' => ['icon' => 'tags', 'label' => 'Categories', 'badge' => null],
        ],
        'Orders' => [
            'orders.php' => ['icon' => 'shopping-cart', 'label' => 'Orders', 'badge' => null],
            'pos.php' => ['icon' => 'cash-register', 'label' => 'POS', 'badge' => null],
        ],
        'Inventory' => [
            'stock.php' => ['icon' => 'boxes', 'label' => 'Stock', 'badge' => null],
            'suppliers.php' => ['icon' => 'truck', 'label' => 'Suppliers', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'nurse') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'heartbeat', 'label' => 'Dashboard', 'badge' => null],
        ],
        'Health' => [
            'patients.php' => ['icon' => 'procedures', 'label' => 'Patient Records', 'badge' => null],
            'checkups.php' => ['icon' => 'stethoscope', 'label' => 'Health Checkups', 'badge' => null],
            'incidents.php' => ['icon' => 'ambulance', 'label' => 'Incidents', 'badge' => null],
        ],
        'Medication' => [
            'medications.php' => ['icon' => 'pills', 'label' => 'Medications', 'badge' => null],
            'dispensary.php' => ['icon' => 'prescription-bottle', 'label' => 'Dispensary', 'badge' => null],
        ],
        'Reports' => [
            'reports.php' => ['icon' => 'chart-line', 'label' => 'Health Reports', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'counselor') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'hands-helping', 'label' => 'Dashboard', 'badge' => null],
        ],
        'Counseling' => [
            'appointments.php' => ['icon' => 'calendar-check', 'label' => 'Appointments', 'badge' => null],
            'sessions.php' => ['icon' => 'comments', 'label' => 'Sessions', 'badge' => null],
            'cases.php' => ['icon' => 'folder-open', 'label' => 'Cases', 'badge' => null],
        ],
        'Students' => [
            'students.php' => ['icon' => 'user-graduate', 'label' => 'Student Profiles', 'badge' => null],
            'referrals.php' => ['icon' => 'share', 'label' => 'Referrals', 'badge' => null],
        ],
        'Reports' => [
            'reports.php' => ['icon' => 'chart-line', 'label' => 'Reports', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'admin-officer') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'user-tie', 'label' => 'Dashboard', 'badge' => null],
        ],
        'Front Desk' => [
            'visitors.php' => ['icon' => 'users', 'label' => 'Visitors', 'badge' => null],
            'enquiries.php' => ['icon' => 'question-circle', 'label' => 'Enquiries', 'badge' => null],
            'admissions.php' => ['icon' => 'user-plus', 'label' => 'Admissions', 'badge' => null],
        ],
        'Documents' => [
            'certificates.php' => ['icon' => 'certificate', 'label' => 'Certificates', 'badge' => null],
            'id-cards.php' => ['icon' => 'id-card', 'label' => 'ID Cards', 'badge' => null],
            'letters.php' => ['icon' => 'envelope', 'label' => 'Letters', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'class-teacher') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'chalkboard-teacher', 'label' => 'Dashboard', 'badge' => null],
            'my-class.php' => ['icon' => 'door-open', 'label' => 'My Class', 'badge' => null],
        ],
        'Students' => [
            'students.php' => ['icon' => 'user-graduate', 'label' => 'Students', 'badge' => null],
            'attendance.php' => ['icon' => 'clipboard-check', 'label' => 'Attendance', 'badge' => null],
            'behavior.php' => ['icon' => 'user-shield', 'label' => 'Behavior', 'badge' => null],
        ],
        'Academic' => [
            'grades.php' => ['icon' => 'chart-line', 'label' => 'Grades', 'badge' => null],
            'report-cards.php' => ['icon' => 'file-alt', 'label' => 'Report Cards', 'badge' => null],
        ],
        'Parents' => [
            'parent-comms.php' => ['icon' => 'users', 'label' => 'Parent Communication', 'badge' => null],
            'meetings.php' => ['icon' => 'calendar-alt', 'label' => 'Parent Meetings', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'subject-coordinator') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'sitemap', 'label' => 'Dashboard', 'badge' => null],
        ],
        'Curriculum' => [
            'subjects.php' => ['icon' => 'book-open', 'label' => 'Subjects', 'badge' => null],
            'syllabus.php' => ['icon' => 'list-alt', 'label' => 'Syllabus', 'badge' => null],
            'lesson-plans.php' => ['icon' => 'clipboard', 'label' => 'Lesson Plans', 'badge' => null],
        ],
        'Resources' => [
            'question-bank.php' => ['icon' => 'database', 'label' => 'Question Bank', 'badge' => null],
            'resources.php' => ['icon' => 'folder-open', 'label' => 'Resources', 'badge' => null],
        ],
        'Teachers' => [
            'teachers.php' => ['icon' => 'chalkboard-teacher', 'label' => 'Teachers', 'badge' => null],
            'performance.php' => ['icon' => 'chart-bar', 'label' => 'Performance', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'alumni') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'user-graduate', 'label' => 'Dashboard', 'badge' => null],
        ],
        'Network' => [
            'directory.php' => ['icon' => 'address-book', 'label' => 'Alumni Directory', 'badge' => null],
            'events.php' => ['icon' => 'calendar-alt', 'label' => 'Events', 'badge' => null],
            'reunions.php' => ['icon' => 'users', 'label' => 'Reunions', 'badge' => null],
        ],
        'Engagement' => [
            'mentorship.php' => ['icon' => 'hands-helping', 'label' => 'Mentorship', 'badge' => null],
            'jobs.php' => ['icon' => 'briefcase', 'label' => 'Job Board', 'badge' => null],
            'donate.php' => ['icon' => 'heart', 'label' => 'Donate', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
            '../forum/index.php' => ['icon' => 'comments', 'label' => 'The Quad Forum', 'badge' => null],
        ],
        'Account' => [
            'profile.php' => ['icon' => 'user', 'label' => 'Profile', 'badge' => null],
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} elseif ($user_role === 'general') {
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'home', 'label' => 'Dashboard', 'badge' => null],
        ],
        'Services' => [
            'services.php' => ['icon' => 'concierge-bell', 'label' => 'Services', 'badge' => null],
            'requests.php' => ['icon' => 'paper-plane', 'label' => 'My Requests', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'profile.php' => ['icon' => 'user', 'label' => 'Profile', 'badge' => null],
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
} else {
    // Default navigation for any undefined role
    $nav_sections = [
        'Core' => [
            'dashboard.php' => ['icon' => 'home', 'label' => 'Dashboard', 'badge' => null],
        ],
        'Communication' => [
            '../messages.php' => ['icon' => 'comments', 'label' => 'Messages', 'badge' => $unread_count > 0 ? $unread_count : null],
            '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null],
        ],
        'Account' => [
            'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null],
        ],
    ];
}
?>

<!-- Hamburger Menu Button -->
<button class="hamburger-btn" id="sidebarToggle" aria-label="Toggle Sidebar" aria-expanded="false" aria-controls="cyberSidebar">
    <i class="fas fa-bars" aria-hidden="true"></i>
</button>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" role="presentation"></div>

<!-- Cyberpunk Sidebar -->
<aside class="cyber-sidebar slide-in" id="cyberSidebar" role="navigation" aria-label="Main Navigation">
    <!-- Brand Section -->
    <div class="sidebar-brand">
        <div class="brand-orb" aria-hidden="true">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <h2 class="brand-title">SMS</h2>
        <p class="brand-subtitle">School Management System</p>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-menu" aria-label="Dashboard Navigation">
        <?php foreach ($nav_sections as $section_name => $items): ?>
            <div class="menu-section-title" role="heading" aria-level="3"><?php echo $section_name; ?></div>
            <?php foreach ($items as $page => $item): ?>
                <a href="<?php echo $page; ?>" class="menu-item <?php echo $current_page === $page ? 'active' : ''; ?>" <?php echo $current_page === $page ? 'aria-current="page"' : ''; ?>>
                    <span class="menu-icon" aria-hidden="true">
                        <i class="fas fa-<?php echo $item['icon']; ?>"></i>
                    </span>
                    <span class="menu-label"><?php echo $item['label']; ?></span>
                    <?php if ($item['badge']): ?>
                        <span class="menu-badge" aria-label="<?php echo $item['badge']; ?> notifications"><?php echo $item['badge']; ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        <?php endforeach; ?>

        <!-- Logout -->
        <a href="../logout.php" class="menu-item" style="margin-top: 20px; border-top: 1px solid var(--glass-border); padding-top: 20px;">
            <span class="menu-icon">
                <i class="fas fa-sign-out-alt"></i>
            </span>
            <span class="menu-label">Logout</span>
        </a>

        <!-- Theme Selector Button -->
        <a href="javascript:void(0)" class="menu-item theme-toggle-btn" onclick="openThemeModal()" title="Change Theme">
            <span class="menu-icon">
                <i class="fas fa-palette"></i>
            </span>
            <span class="menu-label">Theme</span>
            <span class="menu-badge theme-indicator"><?php echo ucfirst(str_replace('-', ' ', substr(get_user_theme(), 0, 6))); ?></span>
        </a>
    </nav>

    <!-- User Profile Card -->
    <div class="sidebar-user">
        <div class="user-card">
            <div class="user-avatar">
                <?php echo $user_initials; ?>
            </div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                <div class="user-role"><?php echo htmlspecialchars($user_role); ?></div>
            </div>
        </div>
    </div>
</aside>

<?php
// Include Theme Selector Modal (once only)
include __DIR__ . '/theme-selector.php';
?>

<!-- Sidebar Toggle Script -->
<script>
    (function() {
            const sidebar = document.getElementById('cyberSidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (sidebarToggle && sidebar && sidebarOverlay) {
                // Toggle sidebar function
                function toggleSidebar() {
                    const isMobile = window.innerWidth <= 1024;
                    const isExpanded = sidebarToggle.getAttribute('aria-expanded') === 'true';

                    if (isMobile) {
                        sidebar.classList.toggle('active');
                        sidebarOverlay.classList.toggle('active');
                    } else {
                        sidebar.classList.toggle('hidden');
                    }

                    // Update ARIA state
                    sidebarToggle.setAttribute('aria-expanded', !isExpanded);
                }

                // Close sidebar function
                function closeSidebar() {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    sidebarToggle.setAttribute('aria-expanded', 'false');
                }

                // Click handler
                sidebarToggle.addEventListener('click', toggleSidebar);

                // Keyboard handler for toggle button
                sidebarToggle.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        toggleSidebar();
                    }
                });

                // Close sidebar when clicking overlay (mobile only)
                sidebarOverlay.addEventListener('click', closeSidebar);

                // Escape key to close sidebar
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                        closeSidebar();
                        sidebarToggle.focus();
                    }
                });

                // Close sidebar on mobile when clicking a link
                const menuItems = sidebar.querySelectorAll('.menu-item');
                menuItems.forEach(item => {
                    item.addEventListener('click', function() {
                        if (window.innerWidth <= 1024) {
                            closeSidebar();
                        }
                    });
                });

                // Trap focus in sidebar when open on mobile
                sidebar.addEventListener('keydown', function(e) {
                        if (e.key === 'Tab' && sidebar.classList.contains('active')) {
                            const focusableElements = sidebar.querySelectorAll('a, button');
                            const firstElement = focusableElements[0];
                            const lastElement = focusableElements[focusableElements.length - 1];

                            if (e.shiftKey && document.activeElement === firstElement) {
                                e.preventDefault();
                                lastElement.focus();
                            } else if (!e.shiftKey && document.activeElement === lastElement) {
                                e.preventDefault();
                                firstElement.focus();
                            }
                        }
                    }
                }
            })();
</script>

<?php
// Include School Management System Bot widget on all pages
include __DIR__ . '/sams-bot.php';
?>