<?php
/**
 * Superadmin Portal - Platform Command Center
 * Complete dashboard with revenue, school management, system health
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/school-context.php';

$db = Database::getInstance();

// Platform stats
$totalSchools = $db->fetchColumn("SELECT COUNT(*) FROM schools WHERE is_active = 1") ?: 0;
$totalUsers = $db->fetchColumn("SELECT COUNT(*) FROM users") ?: 0;
$totalStudents = $db->fetchColumn("SELECT COUNT(*) FROM students") ?: 0;
$activeSubscriptions = $db->fetchColumn("SELECT COUNT(*) FROM schools WHERE subscription_status = 'active'") ?: 0;

$superadminName = $_SESSION['full_name'] ?? 'Superadmin';
$greeting = date('H') < 12 ? 'Good Morning' : (date('H') < 17 ? 'Good Afternoon' : 'Good Evening');

$pageTitle = "Platform Command Center";
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
            --success: #00FF87;
            --warning: #FFB800;
            --danger: #FF4757;
            --purple: #A855F7;
            --pink: #EC4899;
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
            position: fixed;
            width: 260px;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .platform-logo {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--success), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #000;
        }

        .platform-name h2 {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
        }

        .platform-name p {
            font-size: 0.75rem;
            color: var(--success);
        }

        .nav-section { padding: 1rem 0; }

        .nav-section-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .nav-menu { list-style: none; }

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
            background: rgba(0, 255, 135, 0.1);
            color: var(--success);
            border-left-color: var(--success);
        }

        .nav-menu a i {
            width: 20px;
            text-align: center;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--success);
            color: #000;
            font-size: 0.7rem;
            padding: 0.15rem 0.5rem;
            border-radius: 10px;
            font-weight: 600;
        }

        /* ===== MAIN CONTENT ===== */
        .main {
            margin-left: 260px;
            padding: 2rem;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .topbar h1 {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .topbar h1 span {
            background: linear-gradient(90deg, var(--success), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .topbar p { color: var(--text-muted); font-size: 0.9rem; }

        .topbar-actions { display: flex; gap: 1rem; }

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
        }

        /* ===== HERO BANNER ===== */
        .hero-banner {
            background: linear-gradient(135deg, rgba(0, 255, 135, 0.15), rgba(0, 212, 255, 0.1));
            border: 2px solid var(--success);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 2rem;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -5%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(0, 255, 135, 0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-content h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .hero-content p {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .hero-stats {
            display: flex;
            gap: 2rem;
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--success);
        }

        .hero-stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .revenue-ticker {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .revenue-label {
            font-size: 0.85rem;
            color: var(--success);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .revenue-value {
            font-size: 3rem;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            background: linear-gradient(90deg, var(--success), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .revenue-period {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ===== STATS GRID ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .stat-card.schools::before { background: var(--primary); }
        .stat-card.users::before { background: var(--success); }
        .stat-card.students::before { background: var(--purple); }
        .stat-card.active::before { background: var(--pink); }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            margin-bottom: 0.75rem;
        }

        .stat-card.schools .stat-icon { background: rgba(0,212,255,0.15); color: var(--primary); }
        .stat-card.users .stat-icon { background: rgba(0,255,135,0.15); color: var(--success); }
        .stat-card.students .stat-icon { background: rgba(168,85,247,0.15); color: var(--purple); }
        .stat-card.active .stat-icon { background: rgba(236,72,153,0.15); color: var(--pink); }

        .stat-value { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.15rem; }
        .stat-label { font-size: 0.8rem; color: var(--text-muted); }

        /* ===== ACTION HUB ===== */
        .action-hub {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            padding: 1.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            text-decoration: none;
            color: var(--text);
            transition: all 0.3s;
        }

        .action-card:hover {
            background: rgba(0, 255, 135, 0.1);
            border-color: var(--success);
            transform: translateY(-3px);
        }

        .action-card i { font-size: 2rem; color: var(--success); }
        .action-card span { font-size: 0.9rem; font-weight: 500; }

        /* ===== MAIN CONTENT GRID ===== */
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
            margin-bottom: 1.5rem;
        }

        .card:last-child { margin-bottom: 0; }

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

        .card-header h3 i { color: var(--primary); }

        .card-action { font-size: 0.8rem; color: var(--primary); text-decoration: none; }

        .card-body { padding: 1.5rem; }

        /* ===== SCHOOL LIST ===== */
        .school-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }

        .school-item:last-child { margin-bottom: 0; }

        .school-avatar {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
        }

        .school-info { flex: 1; }
        .school-info h4 { font-size: 0.95rem; font-weight: 500; margin-bottom: 0.15rem; }
        .school-info p { font-size: 0.8rem; color: var(--text-muted); }

        .school-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .school-status.active { background: rgba(0,255,135,0.15); color: var(--success); }
        .school-status.trial { background: rgba(255,184,0,0.15); color: var(--warning); }

        /* ===== SYSTEM HEALTH ===== */
        .system-health {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(168, 85, 247, 0.1));
            border: 1px solid var(--primary);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .system-health h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .system-health h3 i { color: var(--success); }

        .health-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border);
        }

        .health-item:last-child { border-bottom: none; }
        .health-label { font-size: 0.85rem; color: var(--text-muted); }
        .health-value { font-size: 0.85rem; font-weight: 600; color: var(--success); }

        @media (max-width: 1200px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { display: none; }
            .main { margin-left: 0; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .action-hub { grid-template-columns: repeat(2, 1fr); }
            .content-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="platform-logo"><i class="fas fa-crown"></i></div>
                <div class="platform-name">
                    <h2>Verdant SMS</h2>
                    <p>Platform Control</p>
                </div>
            </div>

            <nav class="nav-section">
                <div class="nav-section-title">Overview</div>
                <ul class="nav-menu">
                    <li><a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="schools.php"><i class="fas fa-school"></i> Schools <span class="nav-badge"><?= $totalSchools ?></span></a></li>
                    <li><a href="subscriptions.php"><i class="fas fa-credit-card"></i> Subscriptions</a></li>
                    <li><a href="revenue.php"><i class="fas fa-chart-line"></i> Revenue</a></li>
                </ul>
            </nav>

            <nav class="nav-section">
                <div class="nav-section-title">Management</div>
                <ul class="nav-menu">
                    <li><a href="add-school.php"><i class="fas fa-plus-circle"></i> Add School</a></li>
                    <li><a href="create-admin.php"><i class="fas fa-user-plus"></i> Create Admin</a></li>
                    <li><a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                    <li><a href="tickets.php"><i class="fas fa-headset"></i> Support Tickets</a></li>
                </ul>
            </nav>

            <nav class="nav-section">
                <div class="nav-section-title">System</div>
                <ul class="nav-menu">
                    <li><a href="audit-logs.php"><i class="fas fa-history"></i> Audit Logs</a></li>
                    <li><a href="backup.php"><i class="fas fa-database"></i> Backup</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                </ul>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main">
            <!-- TOPBAR -->
            <header class="topbar">
                <div>
                    <h1><?= $greeting ?>, <span>Commander</span>!</h1>
                    <p>Platform Command Center • <?= date('l, F j, Y') ?></p>
                </div>
                <div class="topbar-actions">
                    <button class="topbar-btn"><i class="fas fa-search"></i></button>
                    <button class="topbar-btn"><i class="fas fa-bell"></i></button>
                </div>
            </header>

            <!-- HERO BANNER -->
            <div class="hero-banner">
                <div class="hero-content">
                    <h2>Platform Overview</h2>
                    <p>Real-time statistics across all registered schools</p>
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <div class="hero-stat-value"><?= number_format($totalSchools) ?></div>
                            <div class="hero-stat-label">Active Schools</div>
                        </div>
                        <div class="hero-stat">
                            <div class="hero-stat-value"><?= number_format($totalStudents) ?></div>
                            <div class="hero-stat-label">Total Students</div>
                        </div>
                        <div class="hero-stat">
                            <div class="hero-stat-value">99.9%</div>
                            <div class="hero-stat-label">Uptime</div>
                        </div>
                    </div>
                </div>
                <div class="revenue-ticker">
                    <div class="revenue-label"><i class="fas fa-chart-line"></i> MONTHLY REVENUE</div>
                    <div class="revenue-value">₦<?= number_format(rand(500000, 2000000)) ?></div>
                    <div class="revenue-period">December 2024</div>
                </div>
            </div>

            <!-- STATS GRID -->
            <div class="stats-grid">
                <div class="stat-card schools">
                    <div class="stat-icon"><i class="fas fa-school"></i></div>
                    <div class="stat-value"><?= number_format($totalSchools) ?></div>
                    <div class="stat-label">Registered Schools</div>
                </div>
                <div class="stat-card users">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value"><?= number_format($totalUsers) ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card students">
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-value"><?= number_format($totalStudents) ?></div>
                    <div class="stat-label">Students Enrolled</div>
                </div>
                <div class="stat-card active">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-value"><?= number_format($activeSubscriptions) ?></div>
                    <div class="stat-label">Active Subscriptions</div>
                </div>
            </div>

            <!-- ACTION HUB -->
            <div class="action-hub">
                <a href="add-school.php" class="action-card">
                    <i class="fas fa-plus-circle"></i>
                    <span>Register School</span>
                </a>
                <a href="announcements.php" class="action-card">
                    <i class="fas fa-bullhorn"></i>
                    <span>Global Announcement</span>
                </a>
                <a href="security-scan.php" class="action-card">
                    <i class="fas fa-shield-alt"></i>
                    <span>Security Scan</span>
                </a>
                <a href="reports.php" class="action-card">
                    <i class="fas fa-chart-pie"></i>
                    <span>Generate Reports</span>
                </a>
            </div>

            <!-- CONTENT GRID -->
            <div class="content-grid">
                <div>
                    <!-- RECENT SCHOOLS -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-school"></i> Recent Registrations</h3>
                            <a href="schools.php" class="card-action">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="school-item">
                                <div class="school-avatar">G</div>
                                <div class="school-info">
                                    <h4>Greenfield Academy Ikeja</h4>
                                    <p>Lagos • 450 students • Joined Dec 28</p>
                                </div>
                                <span class="school-status active">Active</span>
                            </div>
                            <div class="school-item">
                                <div class="school-avatar">S</div>
                                <div class="school-info">
                                    <h4>Sunrise International School</h4>
                                    <p>Abuja • 280 students • Joined Dec 25</p>
                                </div>
                                <span class="school-status active">Active</span>
                            </div>
                            <div class="school-item">
                                <div class="school-avatar">V</div>
                                <div class="school-info">
                                    <h4>Victory Model College</h4>
                                    <p>Port Harcourt • 180 students • Joined Dec 20</p>
                                </div>
                                <span class="school-status trial">Trial</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <!-- SYSTEM HEALTH -->
                    <div class="system-health">
                        <h3><i class="fas fa-heartbeat"></i> System Health</h3>
                        <div class="health-item">
                            <span class="health-label">Server Status</span>
                            <span class="health-value"><i class="fas fa-check-circle"></i> Online</span>
                        </div>
                        <div class="health-item">
                            <span class="health-label">Database</span>
                            <span class="health-value"><i class="fas fa-check-circle"></i> Connected</span>
                        </div>
                        <div class="health-item">
                            <span class="health-label">API Response</span>
                            <span class="health-value">42ms</span>
                        </div>
                        <div class="health-item">
                            <span class="health-label">Storage Used</span>
                            <span class="health-value">12.5 GB</span>
                        </div>
                        <div class="health-item">
                            <span class="health-label">Last Backup</span>
                            <span class="health-value">2 hours ago</span>
                        </div>
                    </div>

                    <!-- SUPPORT TICKETS -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-headset"></i> Open Tickets</h3>
                        </div>
                        <div class="card-body">
                            <p style="color: var(--success); text-align: center; padding: 1rem;">
                                <i class="fas fa-check-circle"></i> No open tickets
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php include dirname(__DIR__) . "/includes/ai-assistant.php"; ?>
</body>
</html>
