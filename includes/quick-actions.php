<?php

/**
 * Quick Actions Floating Action Button (FAB)
 * Role-aware quick actions with expandable menu
 * Verdant SMS v3.0
 */

// Get quick actions based on user role
function get_quick_actions($role)
{
    $actions = [
        // Common actions for all roles
        'common' => [
            ['icon' => 'search', 'label' => 'Search', 'action' => 'openGlobalSearch()', 'color' => '#6366f1'],
            ['icon' => 'comment-alt', 'label' => 'Messages', 'link' => '/messages.php', 'color' => '#10b981'],
        ],

        // Admin actions
        'admin' => [
            ['icon' => 'user-plus', 'label' => 'Add Student', 'link' => '/admin/student-add.php', 'color' => '#3b82f6'],
            ['icon' => 'chalkboard-teacher', 'label' => 'Add Teacher', 'link' => '/admin/teachers.php?action=add', 'color' => '#8b5cf6'],
            ['icon' => 'bullhorn', 'label' => 'Announcement', 'link' => '/admin/announcements.php', 'color' => '#f59e0b'],
            ['icon' => 'chart-bar', 'label' => 'Reports', 'link' => '/admin/reports.php', 'color' => '#ec4899'],
            ['icon' => 'cog', 'label' => 'Settings', 'link' => '/admin/settings.php', 'color' => '#6b7280'],
        ],

        // Teacher actions
        'teacher' => [
            ['icon' => 'clipboard-check', 'label' => 'Take Attendance', 'link' => '/teacher/attendance.php', 'color' => '#3b82f6'],
            ['icon' => 'edit', 'label' => 'Enter Grades', 'link' => '/teacher/grades.php', 'color' => '#10b981'],
            ['icon' => 'file-alt', 'label' => 'Add Assignment', 'link' => '/teacher/assignments.php?action=add', 'color' => '#f59e0b'],
            ['icon' => 'users', 'label' => 'My Classes', 'link' => '/teacher/classes.php', 'color' => '#8b5cf6'],
        ],

        // Student actions
        'student' => [
            ['icon' => 'book-open', 'label' => 'My Courses', 'link' => '/student/courses.php', 'color' => '#3b82f6'],
            ['icon' => 'tasks', 'label' => 'Assignments', 'link' => '/student/assignments.php', 'color' => '#f59e0b'],
            ['icon' => 'chart-line', 'label' => 'My Grades', 'link' => '/student/grades.php', 'color' => '#10b981'],
            ['icon' => 'calendar-alt', 'label' => 'Timetable', 'link' => '/student/timetable.php', 'color' => '#8b5cf6'],
        ],

        // Parent actions
        'parent' => [
            ['icon' => 'user-graduate', 'label' => 'Child Progress', 'link' => '/parent/progress.php', 'color' => '#3b82f6'],
            ['icon' => 'clipboard-list', 'label' => 'Attendance', 'link' => '/parent/attendance.php', 'color' => '#10b981'],
            ['icon' => 'money-bill', 'label' => 'Fee Status', 'link' => '/parent/fees.php', 'color' => '#f59e0b'],
            ['icon' => 'envelope', 'label' => 'Contact Teacher', 'link' => '/parent/messages.php', 'color' => '#8b5cf6'],
        ],

        // Principal actions
        'principal' => [
            ['icon' => 'chart-pie', 'label' => 'Analytics', 'link' => '/principal/analytics.php', 'color' => '#3b82f6'],
            ['icon' => 'users', 'label' => 'Staff Overview', 'link' => '/principal/staff.php', 'color' => '#10b981'],
            ['icon' => 'bullhorn', 'label' => 'Announcement', 'link' => '/principal/announcements.php', 'color' => '#f59e0b'],
            ['icon' => 'calendar-check', 'label' => 'Events', 'link' => '/principal/events.php', 'color' => '#8b5cf6'],
        ],

        // Librarian actions
        'librarian' => [
            ['icon' => 'book', 'label' => 'Issue Book', 'link' => '/librarian/issue.php', 'color' => '#3b82f6'],
            ['icon' => 'undo', 'label' => 'Return Book', 'link' => '/librarian/return.php', 'color' => '#10b981'],
            ['icon' => 'search', 'label' => 'Search Catalog', 'link' => '/librarian/search.php', 'color' => '#f59e0b'],
            ['icon' => 'plus', 'label' => 'Add Book', 'link' => '/librarian/add-book.php', 'color' => '#8b5cf6'],
        ],

        // Accountant actions
        'accountant' => [
            ['icon' => 'receipt', 'label' => 'Collect Fee', 'link' => '/accountant/collect-fee.php', 'color' => '#3b82f6'],
            ['icon' => 'file-invoice-dollar', 'label' => 'Generate Invoice', 'link' => '/accountant/invoice.php', 'color' => '#10b981'],
            ['icon' => 'chart-bar', 'label' => 'Reports', 'link' => '/accountant/reports.php', 'color' => '#f59e0b'],
            ['icon' => 'history', 'label' => 'Transactions', 'link' => '/accountant/transactions.php', 'color' => '#8b5cf6'],
        ],

        // Transport actions
        'transport' => [
            ['icon' => 'bus', 'label' => 'Routes', 'link' => '/transport/routes.php', 'color' => '#3b82f6'],
            ['icon' => 'map-marked-alt', 'label' => 'Track Vehicles', 'link' => '/transport/tracking.php', 'color' => '#10b981'],
            ['icon' => 'user-plus', 'label' => 'Assign Students', 'link' => '/transport/assign.php', 'color' => '#f59e0b'],
        ],

        // Hostel actions
        'hostel' => [
            ['icon' => 'bed', 'label' => 'Room Allocation', 'link' => '/hostel/rooms.php', 'color' => '#3b82f6'],
            ['icon' => 'clipboard-check', 'label' => 'Attendance', 'link' => '/hostel/attendance.php', 'color' => '#10b981'],
            ['icon' => 'utensils', 'label' => 'Mess Menu', 'link' => '/hostel/mess.php', 'color' => '#f59e0b'],
        ],

        // Nurse actions
        'nurse' => [
            ['icon' => 'notes-medical', 'label' => 'Health Record', 'link' => '/nurse/records.php', 'color' => '#3b82f6'],
            ['icon' => 'first-aid', 'label' => 'New Visit', 'link' => '/nurse/visit.php', 'color' => '#10b981'],
            ['icon' => 'pills', 'label' => 'Medication', 'link' => '/nurse/medication.php', 'color' => '#f59e0b'],
        ],

        // Counselor actions
        'counselor' => [
            ['icon' => 'calendar-plus', 'label' => 'Schedule Session', 'link' => '/counselor/schedule.php', 'color' => '#3b82f6'],
            ['icon' => 'user-friends', 'label' => 'My Cases', 'link' => '/counselor/cases.php', 'color' => '#10b981'],
            ['icon' => 'file-medical-alt', 'label' => 'Add Notes', 'link' => '/counselor/notes.php', 'color' => '#f59e0b'],
        ],

        // Superadmin actions
        'superadmin' => [
            ['icon' => 'school', 'label' => 'Manage Schools', 'link' => '/superadmin/schools.php', 'color' => '#3b82f6'],
            ['icon' => 'user-shield', 'label' => 'Manage Admins', 'link' => '/superadmin/admins.php', 'color' => '#10b981'],
            ['icon' => 'cogs', 'label' => 'System Config', 'link' => '/superadmin/config.php', 'color' => '#f59e0b'],
            ['icon' => 'database', 'label' => 'Backups', 'link' => '/superadmin/backups.php', 'color' => '#8b5cf6'],
        ],
    ];

    // Merge common actions with role-specific
    $role_actions = $actions[$role] ?? $actions['common'];
    return array_merge($actions['common'], $role_actions);
}

$role = $_SESSION['role'] ?? 'student';
$quick_actions = get_quick_actions($role);
?>

<!-- Quick Actions FAB -->
<div class="quick-actions-fab" id="quickActionsFAB">
    <button class="fab-main" id="fabMain" onclick="toggleFAB()" aria-label="Quick Actions" aria-expanded="false">
        <i class="fas fa-bolt"></i>
    </button>

    <div class="fab-menu" id="fabMenu">
        <?php foreach (array_reverse($quick_actions) as $index => $action): ?>
            <?php if (isset($action['link'])): ?>
                <a href="<?php echo APP_URL . $action['link']; ?>"
                    class="fab-action"
                    style="--fab-color: <?php echo $action['color']; ?>; --fab-delay: <?php echo $index * 0.05; ?>s;"
                    title="<?php echo htmlspecialchars($action['label']); ?>">
                    <i class="fas fa-<?php echo $action['icon']; ?>"></i>
                    <span class="fab-label"><?php echo htmlspecialchars($action['label']); ?></span>
                </a>
            <?php else: ?>
                <button class="fab-action"
                    onclick="<?php echo $action['action']; ?>"
                    style="--fab-color: <?php echo $action['color']; ?>; --fab-delay: <?php echo $index * 0.05; ?>s;"
                    title="<?php echo htmlspecialchars($action['label']); ?>">
                    <i class="fas fa-<?php echo $action['icon']; ?>"></i>
                    <span class="fab-label"><?php echo htmlspecialchars($action['label']); ?></span>
                </button>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="fab-backdrop" id="fabBackdrop" onclick="closeFAB()"></div>
</div>

<style>
    /* Quick Actions FAB Styles */
    .quick-actions-fab {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1000;
    }

    .fab-main {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, var(--primary-color, #10b981), var(--primary-dark, #059669));
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(16, 185, 129, 0.4);
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 10;
    }

    .fab-main:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 30px rgba(16, 185, 129, 0.5);
    }

    .fab-main.active {
        transform: rotate(45deg);
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .fab-menu {
        position: absolute;
        bottom: 70px;
        right: 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }

    .fab-menu.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .fab-action {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 16px 10px 12px;
        border-radius: 28px;
        border: none;
        background: var(--fab-color, #6366f1);
        color: white;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
        transform: translateX(100px);
        opacity: 0;
        transition: all 0.3s ease;
        transition-delay: var(--fab-delay, 0s);
    }

    .fab-menu.active .fab-action {
        transform: translateX(0);
        opacity: 1;
    }

    .fab-action:hover {
        transform: translateX(-5px) scale(1.02);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .fab-action i {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        font-size: 0.9rem;
    }

    .fab-label {
        font-family: inherit;
    }

    .fab-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(2px);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: -1;
    }

    .fab-backdrop.active {
        opacity: 1;
        visibility: visible;
    }

    /* Dark theme adjustments */
    .dark-theme .fab-action {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.4);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .quick-actions-fab {
            bottom: 80px;
            /* Above mobile nav if present */
            right: 16px;
        }

        .fab-main {
            width: 52px;
            height: 52px;
            font-size: 1.3rem;
        }

        .fab-menu {
            bottom: 64px;
        }

        .fab-action {
            padding: 8px 14px 8px 10px;
            font-size: 0.8rem;
        }

        .fab-action i {
            width: 28px;
            height: 28px;
            font-size: 0.85rem;
        }
    }

    /* Reduced motion */
    @media (prefers-reduced-motion: reduce) {

        .fab-main,
        .fab-menu,
        .fab-action,
        .fab-backdrop {
            transition: none;
        }
    }
</style>

<script>
    // FAB Toggle Functions
    function toggleFAB() {
        const fab = document.getElementById('fabMain');
        const menu = document.getElementById('fabMenu');
        const backdrop = document.getElementById('fabBackdrop');

        const isActive = fab.classList.contains('active');

        if (isActive) {
            closeFAB();
        } else {
            fab.classList.add('active');
            fab.setAttribute('aria-expanded', 'true');
            menu.classList.add('active');
            backdrop.classList.add('active');
        }
    }

    function closeFAB() {
        const fab = document.getElementById('fabMain');
        const menu = document.getElementById('fabMenu');
        const backdrop = document.getElementById('fabBackdrop');

        fab.classList.remove('active');
        fab.setAttribute('aria-expanded', 'false');
        menu.classList.remove('active');
        backdrop.classList.remove('active');
    }

    // Global search function (can be overridden)
    function openGlobalSearch() {
        // Check if custom search modal exists
        if (typeof window.openSearchModal === 'function') {
            window.openSearchModal();
        } else {
            // Fallback: focus on any search input
            const searchInput = document.querySelector('input[type="search"], input.search-input, #globalSearch');
            if (searchInput) {
                searchInput.focus();
            } else {
                alert('Search functionality coming soon!');
            }
        }
        closeFAB();
    }

    // Keyboard shortcut for FAB (Alt + Q)
    document.addEventListener('keydown', function(e) {
        if (e.altKey && e.key === 'q') {
            e.preventDefault();
            toggleFAB();
        }

        // Escape to close
        if (e.key === 'Escape') {
            closeFAB();
        }
    });

    // Close on outside click
    document.addEventListener('click', function(e) {
        const fab = document.getElementById('quickActionsFAB');
        if (fab && !fab.contains(e.target)) {
            closeFAB();
        }
    });
</script>