<?php

/**
 * Vice Principal Dashboard - Discipline & Operations
 * Verdant SMS v3.0
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for authentication
require_role('vice-principal', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Vice Principal';

// Today's Attendance Overview
$today_attendance = db()->fetchOne("
    SELECT
        COUNT(*) as total_marked,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
        ROUND((SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / NULLIF(COUNT(*), 0)) * 100, 1) as attendance_rate
    FROM attendance
    WHERE date = CURDATE()
") ?? ['total_marked' => 0, 'present_count' => 0, 'absent_count' => 0, 'attendance_rate' => 0];

// Pending Discipline Cases
$pending_discipline = db()->fetchOne("SELECT COUNT(*) as count FROM discipline_records WHERE status = 'pending'")['count'] ?? 0;

// Teacher Substitutions Today
$substitutions_today = db()->fetchOne("SELECT COUNT(*) as count FROM teacher_substitutions WHERE date = CURDATE()")['count'] ?? 0;

// Pending Leave Requests
$pending_leaves = db()->fetchOne("SELECT COUNT(*) as count FROM leave_requests WHERE status = 'pending'")['count'] ?? 0;

// Recent Incident Reports
$recent_incidents = db()->fetchAll("
    SELECT dr.*, CONCAT(s.first_name, ' ', s.last_name) as student_name
    FROM discipline_records dr
    LEFT JOIN students s ON dr.student_id = s.id
    ORDER BY dr.created_at DESC
    LIMIT 10
");

// Staff on Leave Today
$staff_on_leave = db()->fetchAll("
    SELECT lr.*, CONCAT(u.first_name, ' ', u.last_name) as staff_name
    FROM leave_requests lr
    LEFT JOIN users u ON lr.user_id = u.id
    WHERE lr.status = 'approved' AND CURDATE() BETWEEN lr.start_date AND lr.end_date
    LIMIT 10
");

$page_title = 'Vice Principal Dashboard';
$page_icon = 'user-shield';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/cyberpunk-ui.css" rel="stylesheet">
    <style>
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg, rgba(20, 20, 30, 0.9));
            border: 1px solid var(--cyber-cyan, #00BFFF);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 191, 255, 0.3);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }

        .stat-icon.cyan {
            color: #00BFFF;
        }

        .stat-icon.green {
            color: #00FF7F;
        }

        .stat-icon.orange {
            color: #FF9F43;
        }

        .stat-icon.red {
            color: #FF4757;
        }

        .stat-icon.purple {
            color: #8A2BE2;
        }

        .stat-value {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .dashboard-section {
            background: var(--card-bg, rgba(20, 20, 30, 0.9));
            border: 1px solid var(--border, rgba(0, 191, 255, 0.2));
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
            color: var(--cyber-cyan, #00BFFF);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .incident-list {
            list-style: none;
        }

        .incident-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .incident-item:last-child {
            border-bottom: none;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-pending {
            background: rgba(255, 159, 67, 0.2);
            color: #FF9F43;
        }

        .badge-resolved {
            background: rgba(0, 255, 127, 0.2);
            color: #00FF7F;
        }

        .badge-severe {
            background: rgba(255, 71, 87, 0.2);
            color: #FF4757;
        }

        .two-col {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
        }
    </style>
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb purple"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <span class="welcome-text">Welcome, <?php echo htmlspecialchars($full_name); ?></span>
                </div>
            </header>

            <!-- Overview Stats -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-value"><?php echo $today_attendance['attendance_rate'] ?? 0; ?>%</div>
                    <div class="stat-label">Today's Attendance</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon cyan"><i class="fas fa-user-check"></i></div>
                    <div class="stat-value"><?php echo $today_attendance['present_count'] ?? 0; ?></div>
                    <div class="stat-label">Students Present</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-user-times"></i></div>
                    <div class="stat-value"><?php echo $today_attendance['absent_count'] ?? 0; ?></div>
                    <div class="stat-label">Students Absent</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-gavel"></i></div>
                    <div class="stat-value"><?php echo $pending_discipline; ?></div>
                    <div class="stat-label">Pending Cases</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-exchange-alt"></i></div>
                    <div class="stat-value"><?php echo $substitutions_today; ?></div>
                    <div class="stat-label">Substitutions Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon cyan"><i class="fas fa-calendar-times"></i></div>
                    <div class="stat-value"><?php echo $pending_leaves; ?></div>
                    <div class="stat-label">Pending Leaves</div>
                </div>
            </div>

            <div class="two-col">
                <!-- Recent Incidents -->
                <div class="dashboard-section">
                    <h2 class="section-title"><i class="fas fa-exclamation-triangle"></i> Recent Discipline Cases</h2>
                    <ul class="incident-list">
                        <?php if (empty($recent_incidents)): ?>
                            <li class="incident-item">
                                <span style="color: rgba(255,255,255,0.5);">No recent incidents</span>
                            </li>
                        <?php else: ?>
                            <?php foreach ($recent_incidents as $incident): ?>
                                <li class="incident-item">
                                    <div>
                                        <strong><?php echo htmlspecialchars($incident['student_name'] ?? 'Unknown'); ?></strong>
                                        <br><small><?php echo htmlspecialchars($incident['incident_type'] ?? 'N/A'); ?></small>
                                    </div>
                                    <span class="badge badge-<?php echo strtolower($incident['status'] ?? 'pending'); ?>">
                                        <?php echo ucfirst($incident['status'] ?? 'Pending'); ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Staff on Leave -->
                <div class="dashboard-section">
                    <h2 class="section-title"><i class="fas fa-user-clock"></i> Staff on Leave Today</h2>
                    <ul class="incident-list">
                        <?php if (empty($staff_on_leave)): ?>
                            <li class="incident-item">
                                <span style="color: rgba(255,255,255,0.5);">No staff on leave today</span>
                            </li>
                        <?php else: ?>
                            <?php foreach ($staff_on_leave as $leave): ?>
                                <li class="incident-item">
                                    <div>
                                        <strong><?php echo htmlspecialchars($leave['staff_name'] ?? 'Unknown'); ?></strong>
                                        <br><small><?php echo htmlspecialchars($leave['leave_type'] ?? 'Leave'); ?></small>
                                    </div>
                                    <span class="badge badge-pending">On Leave</span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-section">
                <h2 class="section-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="../admin/attendance.php" class="cyber-btn cyan"><i class="fas fa-clipboard-check"></i> View Attendance</a>
                    <a href="../admin/approve-users.php" class="cyber-btn green"><i class="fas fa-user-check"></i> Approve Users</a>
                    <a href="../admin/reports.php" class="cyber-btn purple"><i class="fas fa-chart-bar"></i> Reports</a>
                    <a href="../admin/events.php" class="cyber-btn orange"><i class="fas fa-calendar"></i> Events</a>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>