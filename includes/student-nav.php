<?php

/**
 * Student Panel Navigation Component
 * Comprehensive navigation aligned with Student Panel Overview
 * Features: Enhanced dashboard, messaging, notifications, assignments, grades, collaboration
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$user_name = $_SESSION['full_name'] ?? 'Student';
$user_role = $_SESSION['role'] ?? 'student';
$user_id = $_SESSION['user_id'] ?? 0;
$user_initials = strtoupper(substr($user_name, 0, 2));

// Get unread messages count
$unread_messages = 0;
if ($user_id > 0) {
    try {
        $result = db()->fetchOne("
            SELECT COUNT(*) as count FROM student_messages
            WHERE to_student_id = ? AND is_read = 0
        ", [$user_id]);
        $unread_messages = $result['count'] ?? 0;
    } catch (Exception $e) {
        $unread_messages = 0;
    }
}

// Get unread notifications count
$unread_notifications = 0;
if ($user_id > 0) {
    try {
        $result = db()->fetchOne("
            SELECT COUNT(*) as count FROM notifications
            WHERE user_id = ? AND is_read = 0
        ", [$user_id]);
        $unread_notifications = $result['count'] ?? 0;
    } catch (Exception $e) {
        $unread_notifications = 0;
    }
}

// Get pending assignments count
$pending_assignments = 0;
if ($user_id > 0) {
    try {
        $result = db()->fetchOne("
            SELECT COUNT(*) as count FROM assignments a
            JOIN class_enrollments ce ON a.class_id = ce.class_id
            WHERE ce.student_id = ? AND a.due_date >= CURDATE()
            AND a.id NOT IN (SELECT assignment_id FROM assignment_submissions WHERE student_id = ?)
        ", [$user_id, $user_id]);
        $pending_assignments = $result['count'] ?? 0;
    } catch (Exception $e) {
        $pending_assignments = 0;
    }
}

// Get current attendance streak
$current_streak = 0;
if ($user_id > 0) {
    try {
        $result = db()->fetchOne("
            SELECT current_streak FROM attendance_streaks WHERE student_id = ?
        ", [$user_id]);
        $current_streak = $result['current_streak'] ?? 0;
    } catch (Exception $e) {
        $current_streak = 0;
    }
}

// Student navigation structure - Comprehensive as per Student Panel Overview
$nav_sections = [
    'Core' => [
        'dashboard-enhanced.php' => ['icon' => 'brain', 'label' => 'Dashboard', 'badge' => null, 'description' => 'Enhanced dashboard with widgets'],
        'checkin.php' => ['icon' => 'fingerprint', 'label' => 'Check In', 'badge' => null, 'description' => 'QR/Geolocation check-in'],
        'attendance.php' => ['icon' => 'clipboard-check', 'label' => 'My Attendance', 'badge' => null, 'description' => 'View attendance history'],
        'schedule.php' => ['icon' => 'calendar-alt', 'label' => 'Schedule', 'badge' => null, 'description' => 'Class timetable with filters'],
    ],
    'Academic' => [
        'assignments.php' => ['icon' => 'tasks', 'label' => 'Assignments', 'badge' => $pending_assignments > 0 ? $pending_assignments : null, 'description' => 'View and submit assignments'],
        'grades.php' => ['icon' => 'chart-line', 'label' => 'My Grades', 'badge' => null, 'description' => 'Grade analytics with charts'],
        'class-registration.php' => ['icon' => 'user-plus', 'label' => 'Class Registration', 'badge' => null, 'description' => 'Enroll in classes'],
        'events.php' => ['icon' => 'calendar-check', 'label' => 'Events', 'badge' => null, 'description' => 'School events calendar'],
        'lms-portal.php' => ['icon' => 'graduation-cap', 'label' => 'LMS Portal', 'badge' => 'LMS', 'description' => 'Learning Management System'],
    ],
    'Communication' => [
        'messages.php' => ['icon' => 'comment-dots', 'label' => 'Messages', 'badge' => $unread_messages > 0 ? $unread_messages : null, 'description' => 'WhatsApp-style messaging'],
        'notifications.php' => ['icon' => 'bell', 'label' => 'Notifications', 'badge' => $unread_notifications > 0 ? $unread_notifications : null, 'description' => 'Notification center'],
        'communication.php' => ['icon' => 'comments', 'label' => 'Peer Chat', 'badge' => null, 'description' => 'Student-to-student messaging'],
        '../messages.php' => ['icon' => 'envelope', 'label' => 'Inbox', 'badge' => null, 'description' => 'Universal inbox'],
        '../notices.php' => ['icon' => 'bullhorn', 'label' => 'Notice Board', 'badge' => null, 'description' => 'School announcements'],
        '../forum/index.php' => ['icon' => 'users', 'label' => 'The Quad Forum', 'badge' => null, 'description' => 'Community discussions'],
        'study-groups.php' => ['icon' => 'user-friends', 'label' => 'Study Groups', 'badge' => null, 'description' => 'Collaborative learning'],
    ],
    'Analytics' => [
        'analytics.php' => ['icon' => 'chart-bar', 'label' => 'Performance', 'badge' => null, 'description' => 'Performance analytics'],
        'reports.php' => ['icon' => 'file-alt', 'label' => 'Reports', 'badge' => null, 'description' => 'Generate custom reports'],
    ],
    'Tools' => [
        'profile.php' => ['icon' => 'user', 'label' => 'Profile', 'badge' => null, 'description' => 'Manage profile'],
        'id-card.php' => ['icon' => 'id-card', 'label' => 'Digital ID Card', 'badge' => null, 'description' => 'Student ID card'],
        'settings.php' => ['icon' => 'cog', 'label' => 'Settings', 'badge' => null, 'description' => 'Account settings'],
        'emergency-alerts.php' => ['icon' => 'exclamation-triangle', 'label' => 'Emergency Alerts', 'badge' => null, 'description' => 'Emergency notifications'],
    ],
];
?>

<!-- Cyberpunk Student Sidebar -->
<aside class="cyber-sidebar slide-in">
    <!-- Brand Section -->
    <div class="sidebar-brand">
        <div class="brand-orb">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <h2 class="brand-title">School Management System</h2>
        <p class="brand-subtitle">STUDENT PANEL</p>
    </div>

    <!-- User Profile Card -->
    <div class="user-profile-card">
        <div class="user-avatar-large"><?php echo $user_initials; ?></div>
        <div class="user-details">
            <h3 class="user-name"><?php echo htmlspecialchars($user_name); ?></h3>
            <p class="user-role-badge">
                <i class="fas fa-user-graduate"></i> Student
            </p>
        </div>
        <?php if ($current_streak > 0): ?>
            <div class="streak-indicator">
                <i class="fas fa-fire"></i>
                <span><?php echo $current_streak; ?> day streak!</span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Stats Bar -->
    <div class="quick-stats-bar">
        <div class="stat-item" title="Pending Assignments">
            <i class="fas fa-tasks"></i>
            <span><?php echo $pending_assignments; ?></span>
        </div>
        <div class="stat-item" title="Unread Messages">
            <i class="fas fa-envelope"></i>
            <span><?php echo $unread_messages; ?></span>
        </div>
        <div class="stat-item" title="Notifications">
            <i class="fas fa-bell"></i>
            <span><?php echo $unread_notifications; ?></span>
        </div>
    </div>

    <!-- Navigation Sections -->
    <nav class="sidebar-nav">
        <?php foreach ($nav_sections as $section_name => $section_items): ?>
            <div class="nav-section">
                <div class="section-title">
                    <i class="fas fa-chevron-right"></i>
                    <?php echo strtoupper($section_name); ?>
                </div>
                <?php foreach ($section_items as $page => $item): ?>
                    <?php
                    $is_active = ($current_page === basename($page)) ? 'active' : '';
                    $has_badge = !empty($item['badge']);
                    $badge_class = is_numeric($item['badge']) ? 'badge-count' : 'badge-label';
                    ?>
                    <a href="<?php echo htmlspecialchars($page); ?>"
                        class="nav-item <?php echo $is_active; ?>"
                        title="<?php echo htmlspecialchars($item['description'] ?? ''); ?>">
                        <i class="fas fa-<?php echo $item['icon']; ?>"></i>
                        <span class="nav-label"><?php echo htmlspecialchars($item['label']); ?></span>
                        <?php if ($has_badge): ?>
                            <span class="nav-badge <?php echo $badge_class; ?>">
                                <?php echo htmlspecialchars($item['badge']); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </nav>

    <!-- AI Chatbot Button -->
    <div class="chatbot-launcher">
        <button class="cyber-btn cyan" id="openChatbot" style="width:100%;">
            <i class="fas fa-robot"></i> School Management System Bot
        </button>
    </div>

    <!-- Logout Section -->
    <div class="sidebar-footer">
        <a href="../logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
        <div class="version-info">v2.1.0 Enhanced</div>
    </div>
</aside>

<!-- Chatbot Integration Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Open School Management System Bot chatbot
        const chatbotBtn = document.getElementById('openChatbot');
        if (chatbotBtn) {
            chatbotBtn.addEventListener('click', function() {
                // Trigger chatbot modal (assuming sams-bot.php is included in pages)
                const chatbotModal = document.getElementById('samsBotModal');
                if (chatbotModal) {
                    chatbotModal.style.display = 'flex';
                }
            });
        }

        // Active page highlighting
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-item').forEach(item => {
            if (item.getAttribute('href') && currentPath.includes(item.getAttribute('href'))) {
                item.classList.add('active');
            }
        });

        // Notification polling (every 30 seconds)
        setInterval(function() {
            fetch('../api/notifications.php?action=get_unread_count')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.count > 0) {
                        updateNotificationBadge(data.count);
                    }
                })
                .catch(err => console.error('Notification poll error:', err));
        }, 30000);

        // Message polling
        setInterval(function() {
            fetch('../api/messaging-enhanced.php?action=get_messaging_stats')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.stats.unread > 0) {
                        updateMessageBadge(data.stats.unread);
                    }
                })
                .catch(err => console.error('Message poll error:', err));
        }, 30000);
    });

    function updateNotificationBadge(count) {
        const notifNavItem = document.querySelector('a[href="notifications.php"] .nav-badge');
        if (notifNavItem) {
            notifNavItem.textContent = count;
            notifNavItem.classList.add('badge-pulse');
        }

        const statBadge = document.querySelector('.quick-stats-bar .stat-item:nth-child(3) span');
        if (statBadge) {
            statBadge.textContent = count;
        }
    }

    function updateMessageBadge(count) {
        const msgNavItem = document.querySelector('a[href="messages.php"] .nav-badge');
        if (msgNavItem) {
            msgNavItem.textContent = count;
            msgNavItem.classList.add('badge-pulse');
        }

        const statBadge = document.querySelector('.quick-stats-bar .stat-item:nth-child(2) span');
        if (statBadge) {
            statBadge.textContent = count;
        }
    }
</script>

<style>
    /* Enhanced Student Navigation Styles */
    .user-profile-card {
        background: linear-gradient(135deg, rgba(0, 191, 255, 0.1), rgba(138, 43, 226, 0.1));
        border: 1px solid rgba(0, 191, 255, 0.3);
        border-radius: 12px;
        padding: 20px;
        margin: 20px 15px;
        text-align: center;
    }

    .user-avatar-large {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--cyber-cyan), var(--hologram-purple));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        font-weight: 900;
        color: #fff;
        margin: 0 auto 15px;
        box-shadow: 0 0 20px rgba(0, 191, 255, 0.5);
    }

    .user-details {
        margin-bottom: 10px;
    }

    .user-name {
        color: var(--text-primary);
        font-size: 1.1rem;
        margin: 0 0 5px 0;
        font-weight: 600;
    }

    .user-role-badge {
        display: inline-block;
        padding: 4px 12px;
        background: rgba(0, 191, 255, 0.2);
        border-radius: 20px;
        color: var(--cyber-cyan);
        font-size: 0.85rem;
        margin: 0;
    }

    .streak-indicator {
        margin-top: 12px;
        padding: 8px;
        background: linear-gradient(135deg, rgba(255, 100, 0, 0.2), rgba(255, 200, 0, 0.2));
        border-radius: 8px;
        color: var(--golden-pulse);
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        animation: pulse-glow 2s infinite;
    }

    .quick-stats-bar {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        padding: 0 15px 20px;
    }

    .quick-stats-bar .stat-item {
        background: rgba(0, 191, 255, 0.1);
        border: 1px solid rgba(0, 191, 255, 0.3);
        border-radius: 8px;
        padding: 10px;
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 5px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .quick-stats-bar .stat-item:hover {
        background: rgba(0, 191, 255, 0.2);
        border-color: var(--cyber-cyan);
        transform: translateY(-2px);
    }

    .quick-stats-bar .stat-item i {
        color: var(--cyber-cyan);
        font-size: 1.2rem;
    }

    .quick-stats-bar .stat-item span {
        color: var(--text-primary);
        font-weight: 700;
        font-size: 1.1rem;
        font-family: 'Orbitron', sans-serif;
    }

    .nav-item {
        position: relative;
    }

    .nav-badge {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        font-family: 'Orbitron', sans-serif;
    }

    .badge-count {
        background: var(--cyber-cyan);
        color: #0a0e27;
    }

    .badge-label {
        background: linear-gradient(135deg, var(--golden-pulse), var(--neon-green));
        color: #0a0e27;
    }

    .badge-pulse {
        animation: badge-pulse 1s ease-in-out;
    }

    @keyframes badge-pulse {

        0%,
        100% {
            transform: translateY(-50%) scale(1);
        }

        50% {
            transform: translateY(-50%) scale(1.2);
        }
    }

    .chatbot-launcher {
        padding: 15px;
        border-top: 1px solid rgba(0, 191, 255, 0.2);
    }

    .version-info {
        text-align: center;
        padding: 10px;
        color: var(--text-muted);
        font-size: 0.75rem;
        font-family: 'Orbitron', monospace;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .user-profile-card {
            padding: 15px;
            margin: 15px 10px;
        }

        .user-avatar-large {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }

        .quick-stats-bar {
            gap: 5px;
            padding: 0 10px 15px;
        }

        .quick-stats-bar .stat-item {
            padding: 8px 5px;
        }
    }
</style>