<?php
/**
 * School Admin Portal - Complete Homepage
 * Full-featured dashboard with sidebar navigation
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/school-context.php';

$db = Database::getInstance();
$schoolId = SchoolContext::getSchoolId() ?? 1;
$school = $db->fetchOne("SELECT * FROM schools WHERE id = ?", [$schoolId]);

// Stats
$totalStudents = $db->fetchColumn("SELECT COUNT(*) FROM students WHERE school_id = ?", [$schoolId]) ?: 0;
$totalTeachers = $db->fetchColumn("SELECT COUNT(*) FROM users WHERE school_id = ? AND role = 'teacher'", [$schoolId]) ?: 0;
$totalClasses = $db->fetchColumn("SELECT COUNT(*) FROM classes WHERE school_id = ?", [$schoolId]) ?: 0;
$totalParents = $db->fetchColumn("SELECT COUNT(*) FROM users WHERE school_id = ? AND role = 'parent'", [$schoolId]) ?: 0;

$userName = $_SESSION['full_name'] ?? 'Administrator';
$greeting = date('H') < 12 ? 'Good Morning' : (date('H') < 17 ? 'Good Afternoon' : 'Good Evening');
$today = date('l, F j, Y');

$pageTitle = "Admin Dashboard";
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00D4FF;
            --primary-glow: rgba(0, 212, 255, 0.3);
            --success: #00FF87;
            --warning: #FFB800;
            --danger: #FF4757;
            --purple: #A855F7;
            --bg-dark: #0B0F19;
            --bg-card: #111827;
            --bg-sidebar: #0D1117;
            --border: rgba(255,255,255,0.08);
            --text: #E5E7EB;
            --text-muted: #9CA3AF;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            color: var(--text);
            min-height: 100vh;
        }

        .layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            padding: 0;
            position: fixed;
            width: 260px;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .school-logo {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
        }

        .school-name {
            flex: 1;
        }

        .school-name h2 {
            font-size: 0.95rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.3;
        }

        .school-name p {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .nav-section {
            padding: 1rem 0;
        }

        .nav-section-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-menu a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .nav-menu a:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text);
        }

        .nav-menu a.active {
            background: rgba(0, 212, 255, 0.1);
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .nav-menu a i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: #fff;
            font-size: 0.7rem;
            padding: 0.15rem 0.5rem;
            border-radius: 10px;
            font-weight: 600;
        }

        /* ===== MAIN CONTENT ===== */
        .main {
            margin-left: 260px;
            padding: 2rem;
            background: var(--bg-dark);
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .topbar-left h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .topbar-left h1 span {
            background: linear-gradient(90deg, var(--primary), var(--success));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .topbar-left p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar-btn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
        }

        .topbar-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .notification-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background: var(--danger);
            border-radius: 50%;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--purple), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #fff;
        }

        /* ===== STATS GRID ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }

        .stat-card:hover {
            border-color: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .stat-card.students::before { background: var(--primary); }
        .stat-card.teachers::before { background: var(--success); }
        .stat-card.classes::before { background: var(--purple); }
        .stat-card.parents::before { background: var(--warning); }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .stat-card.students .stat-icon { background: rgba(0,212,255,0.15); color: var(--primary); }
        .stat-card.teachers .stat-icon { background: rgba(0,255,135,0.15); color: var(--success); }
        .stat-card.classes .stat-icon { background: rgba(168,85,247,0.15); color: var(--purple); }
        .stat-card.parents .stat-icon { background: rgba(255,184,0,0.15); color: var(--warning); }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .stat-change {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .stat-change.up { background: rgba(0,255,135,0.15); color: var(--success); }
        .stat-change.down { background: rgba(255,71,87,0.15); color: var(--danger); }

        /* ===== CONTENT GRID ===== */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-header h3 i {
            color: var(--primary);
        }

        .card-action {
            font-size: 0.8rem;
            color: var(--primary);
            text-decoration: none;
        }

        .card-action:hover {
            text-decoration: underline;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* ===== QUICK ACTIONS ===== */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .quick-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            padding: 1.5rem 1rem;
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--border);
            border-radius: 12px;
            text-decoration: none;
            color: var(--text);
            transition: all 0.3s;
        }

        .quick-action:hover {
            background: rgba(0,212,255,0.1);
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        .quick-action i {
            font-size: 1.75rem;
            color: var(--success);
        }

        .quick-action span {
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* ===== ACTIVITY LIST ===== */
        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .activity-icon.payment { background: rgba(0,255,135,0.15); color: var(--success); }
        .activity-icon.student { background: rgba(0,212,255,0.15); color: var(--primary); }
        .activity-icon.alert { background: rgba(255,71,87,0.15); color: var(--danger); }
        .activity-icon.event { background: rgba(168,85,247,0.15); color: var(--purple); }

        .activity-content h4 {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .activity-content p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ===== ALERTS PANEL ===== */
        .alert-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255,184,0,0.1);
            border: 1px solid rgba(255,184,0,0.3);
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .alert-card:last-child {
            margin-bottom: 0;
        }

        .alert-card.danger {
            background: rgba(255,71,87,0.1);
            border-color: rgba(255,71,87,0.3);
        }

        .alert-card i {
            font-size: 1.25rem;
            color: var(--warning);
        }

        .alert-card.danger i {
            color: var(--danger);
        }

        .alert-card p {
            flex: 1;
            font-size: 0.9rem;
        }

        /* ===== TODAY'S SCHEDULE ===== */
        .schedule-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
        }

        .schedule-item:last-child {
            border-bottom: none;
        }

        .schedule-time {
            width: 70px;
            font-size: 0.85rem;
            color: var(--primary);
            font-weight: 500;
        }

        .schedule-event {
            flex: 1;
        }

        .schedule-event h4 {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.15rem;
        }

        .schedule-event p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .content-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { display: none; }
            .main { margin-left: 0; padding: 1rem; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .quick-actions { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="layout">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="school-logo"><?= strtoupper(substr($school['school_name'] ?? 'S', 0, 1)) ?></div>
                <div class="school-name">
                    <h2><?= htmlspecialchars($school['school_name'] ?? 'Your School') ?></h2>
                    <p>Admin Portal</p>
                </div>
            </div>

            <nav class="nav-section">
                <div class="nav-section-title">Main Menu</div>
                <ul class="nav-menu">
                    <li><a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="students/"><i class="fas fa-user-graduate"></i> Students <span class="nav-badge"><?= $totalStudents ?></span></a></li>
                    <li><a href="teachers/"><i class="fas fa-chalkboard-teacher"></i> Teachers</a></li>
                    <li><a href="parents/"><i class="fas fa-users"></i> Parents</a></li>
                    <li><a href="classes/"><i class="fas fa-door-open"></i> Classes</a></li>
                    <li><a href="subjects/"><i class="fas fa-book"></i> Subjects</a></li>
                </ul>
            </nav>

            <nav class="nav-section">
                <div class="nav-section-title">Academics</div>
                <ul class="nav-menu">
                    <li><a href="attendance/"><i class="fas fa-clipboard-check"></i> Attendance</a></li>
                    <li><a href="exams/"><i class="fas fa-file-alt"></i> Exams</a></li>
                    <li><a href="grades/"><i class="fas fa-chart-line"></i> Grades</a></li>
                    <li><a href="timetable/"><i class="fas fa-calendar-alt"></i> Timetable</a></li>
                    <li><a href="report-cards/"><i class="fas fa-scroll"></i> Report Cards</a></li>
                </ul>
            </nav>

            <nav class="nav-section">
                <div class="nav-section-title">Finance</div>
                <ul class="nav-menu">
                    <li><a href="finance/fees.php"><i class="fas fa-money-bill-wave"></i> Fee Management</a></li>
                    <li><a href="finance/payments.php"><i class="fas fa-credit-card"></i> Payments</a></li>
                    <li><a href="finance/invoices.php"><i class="fas fa-file-invoice"></i> Invoices</a></li>
                </ul>
            </nav>

            <nav class="nav-section">
                <div class="nav-section-title">Communication</div>
                <ul class="nav-menu">
                    <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                    <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages <span class="nav-badge">3</span></a></li>
                    <li><a href="calendar.php"><i class="fas fa-calendar"></i> Calendar</a></li>
                </ul>
            </nav>

            <nav class="nav-section">
                <div class="nav-section-title">Settings</div>
                <ul class="nav-menu">
                    <li><a href="settings/"><i class="fas fa-cog"></i> School Settings</a></li>
                    <li><a href="settings/staff.php"><i class="fas fa-user-cog"></i> Staff Management</a></li>
                    <li><a href="reports/"><i class="fas fa-chart-bar"></i> Reports</a></li>
                </ul>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main">
            <!-- TOP BAR -->
            <header class="topbar">
                <div class="topbar-left">
                    <h1><?= $greeting ?>, <span><?= htmlspecialchars(explode(' ', $userName)[0]) ?></span>!</h1>
                    <p><?= $today ?> — Here's your school overview</p>
                </div>
                <div class="topbar-right">
                    <button class="topbar-btn" title="Search">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="topbar-btn" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notification-dot"></span>
                    </button>
                    <button class="topbar-btn" title="Settings">
                        <i class="fas fa-cog"></i>
                    </button>
                    <div class="user-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                </div>
            </header>

            <!-- STATS GRID -->
            <div class="stats-grid">
                <div class="stat-card students">
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-value"><?= number_format($totalStudents) ?></div>
                    <div class="stat-label">Total Students</div>
                    <div class="stat-change up"><i class="fas fa-arrow-up"></i> 12%</div>
                </div>
                <div class="stat-card teachers">
                    <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="stat-value"><?= number_format($totalTeachers) ?></div>
                    <div class="stat-label">Total Teachers</div>
                    <div class="stat-change up"><i class="fas fa-arrow-up"></i> 3%</div>
                </div>
                <div class="stat-card classes">
                    <div class="stat-icon"><i class="fas fa-door-open"></i></div>
                    <div class="stat-value"><?= number_format($totalClasses) ?></div>
                    <div class="stat-label">Active Classes</div>
                </div>
                <div class="stat-card parents">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value"><?= number_format($totalParents) ?></div>
                    <div class="stat-label">Registered Parents</div>
                </div>
            </div>

            <!-- CONTENT GRID -->
            <div class="content-grid">
                <div>
                    <!-- QUICK ACTIONS -->
                    <div class="card" style="margin-bottom: 1.5rem;">
                        <div class="card-header">
                            <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <a href="students/add.php" class="quick-action">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Admit Student</span>
                                </a>
                                <a href="teachers/add.php" class="quick-action">
                                    <i class="fas fa-user-tie"></i>
                                    <span>Add Teacher</span>
                                </a>
                                <a href="finance/collect.php" class="quick-action">
                                    <i class="fas fa-coins"></i>
                                    <span>Collect Fee</span>
                                </a>
                                <a href="attendance/mark.php" class="quick-action">
                                    <i class="fas fa-clipboard-check"></i>
                                    <span>Mark Attendance</span>
                                </a>
                                <a href="announcements/create.php" class="quick-action">
                                    <i class="fas fa-bullhorn"></i>
                                    <span>Announcement</span>
                                </a>
                                <a href="reports/" class="quick-action">
                                    <i class="fas fa-chart-pie"></i>
                                    <span>View Reports</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- RECENT ACTIVITY -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-history"></i> Recent Activity</h3>
                            <a href="activity-log.php" class="card-action">View All</a>
                        </div>
                        <div class="card-body">
                            <ul class="activity-list">
                                <li class="activity-item">
                                    <div class="activity-icon payment"><i class="fas fa-check"></i></div>
                                    <div class="activity-content">
                                        <h4>Fee Payment Received</h4>
                                        <p>Chinedu Okoro paid ₦45,000 • 10 mins ago</p>
                                    </div>
                                </li>
                                <li class="activity-item">
                                    <div class="activity-icon student"><i class="fas fa-user-plus"></i></div>
                                    <div class="activity-content">
                                        <h4>New Student Admitted</h4>
                                        <p>Adaeze Eze enrolled in JSS 1A • 1 hour ago</p>
                                    </div>
                                </li>
                                <li class="activity-item">
                                    <div class="activity-icon event"><i class="fas fa-calendar-check"></i></div>
                                    <div class="activity-content">
                                        <h4>Event Scheduled</h4>
                                        <p>PTA Meeting added for Jan 10 • 2 hours ago</p>
                                    </div>
                                </li>
                                <li class="activity-item">
                                    <div class="activity-icon alert"><i class="fas fa-exclamation"></i></div>
                                    <div class="activity-content">
                                        <h4>Low Attendance Alert</h4>
                                        <p>JSS 2A has 65% attendance today • 3 hours ago</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div>
                    <!-- ALERTS -->
                    <div class="card" style="margin-bottom: 1.5rem;">
                        <div class="card-header">
                            <h3><i class="fas fa-exclamation-triangle"></i> Alerts</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert-card danger">
                                <i class="fas fa-money-bill-wave"></i>
                                <p><strong>₦850,000</strong> in unpaid fees this term</p>
                            </div>
                            <div class="alert-card">
                                <i class="fas fa-user-clock"></i>
                                <p><strong>5 students</strong> with chronic absenteeism</p>
                            </div>
                            <div class="alert-card">
                                <i class="fas fa-calendar-times"></i>
                                <p><strong>2 teachers</strong> on leave this week</p>
                            </div>
                        </div>
                    </div>

                    <!-- TODAY'S SCHEDULE -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-calendar-day"></i> Today's Events</h3>
                            <a href="calendar.php" class="card-action">Full Calendar</a>
                        </div>
                        <div class="card-body">
                            <div class="schedule-item">
                                <div class="schedule-time">8:00 AM</div>
                                <div class="schedule-event">
                                    <h4>Morning Assembly</h4>
                                    <p>Main Hall</p>
                                </div>
                            </div>
                            <div class="schedule-item">
                                <div class="schedule-time">10:30 AM</div>
                                <div class="schedule-event">
                                    <h4>Staff Briefing</h4>
                                    <p>Admin Block</p>
                                </div>
                            </div>
                            <div class="schedule-item">
                                <div class="schedule-time">2:00 PM</div>
                                <div class="schedule-event">
                                    <h4>Parent Meeting</h4>
                                    <p>JSS 2 Class Teachers</p>
                                </div>
                            </div>
                            <div class="schedule-item">
                                <div class="schedule-time">4:00 PM</div>
                                <div class="schedule-event">
                                    <h4>Sports Practice</h4>
                                    <p>Football Field</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Add subtle hover effects
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 10px 40px rgba(0, 212, 255, 0.15)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = 'none';
            });
        });
    </script>
</body>
</html>
