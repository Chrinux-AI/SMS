<?php

/**
 * Cyberpunk Sidebar Navigation Component
 * Advanced UI with Holographic Effects
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
}
?>

<!-- Hamburger Menu Button -->
<button class="hamburger-btn" id="sidebarToggle" aria-label="Toggle Sidebar">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Cyberpunk Sidebar -->
<aside class="cyber-sidebar slide-in" id="cyberSidebar">
    <!-- Brand Section -->
    <div class="sidebar-brand">
        <div class="brand-orb">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <h2 class="brand-title">School Management System</h2>
        <p class="brand-subtitle">Management System</p>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-menu">
        <?php foreach ($nav_sections as $section_name => $items): ?>
            <div class="menu-section-title"><?php echo $section_name; ?></div>
            <?php foreach ($items as $page => $item): ?>
                <a href="<?php echo $page; ?>" class="menu-item <?php echo $current_page === $page ? 'active' : ''; ?>">
                    <span class="menu-icon">
                        <i class="fas fa-<?php echo $item['icon']; ?>"></i>
                    </span>
                    <span class="menu-label"><?php echo $item['label']; ?></span>
                    <?php if ($item['badge']): ?>
                        <span class="menu-badge"><?php echo $item['badge']; ?></span>
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

<!-- Sidebar Toggle Script -->
<script>
(function() {
    const sidebar = document.getElementById('cyberSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle && sidebar && sidebarOverlay) {
        // Toggle sidebar
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        });
        
        // Close sidebar when clicking overlay
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
        
        // Close sidebar on mobile when clicking a link
        const menuItems = sidebar.querySelectorAll('.menu-item');
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth <= 1024) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                }
            });
        });
    }
})();
</script>

<?php
// Include School Management System Bot widget on all pages
include __DIR__ . '/sams-bot.php';
?>