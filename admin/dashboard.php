<?php

/**
 * Cyberpunk Admin Dashboard
 * Advanced Futuristic UI with Real-time Data
 */

session_start();

require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';
require_once '../includes/theme-loader.php';

// Require admin access
require_role('admin');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Get real statistics from database
$total_students = db()->count('students');
$total_classes = db()->count('classes');
$total_teachers = db()->count('users', 'role = :role', ['role' => 'teacher']);

// Today's attendance
$today = date('Y-m-d');
$today_present = db()->count(
    'attendance_records',
    'attendance_date = :date AND status IN ("present", "late")',
    ['date' => $today]
);
$today_total = db()->count(
    'attendance_records',
    'attendance_date = :date',
    ['date' => $today]
);
$today_rate = $today_total > 0 ? round(($today_present / $today_total) * 100, 1) : 0;

// Risk students (>10% absence in last 30 days)
$risk_students = db()->fetchAll("
    SELECT s.id, s.student_id, s.first_name, s.last_name,
           COUNT(ar.id) as total_days,
           SUM(CASE WHEN ar.status = 'absent' THEN 1 ELSE 0 END) as absent_days
    FROM students s
    LEFT JOIN attendance_records ar ON s.id = ar.student_id
    WHERE ar.attendance_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY s.id
    HAVING (absent_days / total_days) > 0.1
    ORDER BY absent_days DESC
    LIMIT 5
");

// Recent activity
$recent_records = db()->fetchAll("
    SELECT ar.*, s.first_name, s.last_name, c.name as class_name,
           CONCAT(s.first_name, ' ', s.last_name) as student_name
    FROM attendance_records ar
    LEFT JOIN students s ON ar.student_id = s.id
    LEFT JOIN classes c ON ar.class_id = c.id
    ORDER BY ar.created_at DESC
    LIMIT 10
");

$page_title = 'Attendance Dashboard';
$page_icon = 'chart-line';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/attendance/manifest.json">
    <meta name="theme-color" content="#00BFFF">
    <link rel="apple-touch-icon" href="/attendance/assets/images/icons/icon-192x192.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo APP_NAME; ?></title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&family=Inter:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Cyberpunk UI Framework -->
    <link href="../assets/css/cyberpunk-ui.css" rel="stylesheet">

    <!-- Dynamic Theme CSS -->
    <?php output_theme_css(); ?>

</head>

<body class="cyber-bg <?php echo get_theme_body_class(); ?>">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>

        <!-- Main Content -->
        <main class="cyber-main">
            <!-- Header -->
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb">
                        <i class="fas fa-<?php echo $page_icon; ?>"></i>
                    </div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>

                <div class="header-actions">
                    <!-- Biometric Quick Scan -->
                    <div class="biometric-orb" title="Quick Scan">
                        <i class="fas fa-fingerprint"></i>
                    </div>

                    <!-- User Info -->
                    <div class="user-card" style="padding: 8px 15px; margin: 0;">
                        <div class="user-avatar" style="width: 35px; height: 35px; font-size: 0.9rem;">
                            <?php echo strtoupper(substr($full_name, 0, 2)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name" style="font-size: 0.85rem;"><?php echo htmlspecialchars($full_name); ?></div>
                            <div class="user-role">Administrator</div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="cyber-content slide-in">
                <?php include '../includes/notice-board.php'; ?>

                <!-- Statistics Orbs -->
                <section class="orb-grid">
                    <!-- Total Students -->
                    <div class="stat-orb">
                        <div class="stat-icon cyan">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($total_students); ?></div>
                            <div class="stat-label">Total Students</div>
                            <div class="stat-trend up">
                                <i class="fas fa-arrow-up"></i>
                                <span>Active Enrollment</span>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Attendance -->
                    <div class="stat-orb">
                        <div class="stat-icon green">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $today_rate; ?>%</div>
                            <div class="stat-label">Today's Attendance</div>
                            <div class="stat-trend <?php echo $today_rate >= 90 ? 'up' : 'down'; ?>">
                                <i class="fas fa-<?php echo $today_rate >= 90 ? 'check' : 'exclamation'; ?>-circle"></i>
                                <span><?php echo $today_present; ?> of <?php echo $today_total; ?> Present</span>
                            </div>
                        </div>
                    </div>

                    <!-- Risk Students -->
                    <div class="stat-orb">
                        <div class="stat-icon red">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo count($risk_students); ?></div>
                            <div class="stat-label">Risk Students</div>
                            <div class="stat-trend down">
                                <i class="fas fa-heartbeat"></i>
                                <span>Need Attention</span>
                            </div>
                        </div>
                    </div>

                    <!-- Active Classes -->
                    <div class="stat-orb">
                        <div class="stat-icon purple">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($total_classes); ?></div>
                            <div class="stat-label">Active Classes</div>
                            <div class="stat-trend up">
                                <i class="fas fa-check-circle"></i>
                                <span>All Operational</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- AI Analytics Section -->
                <section class="holo-card" style="margin-bottom: 30px;">
                    <?php
                    // Load AI Analytics from database
                    $ai_models = db()->query("SELECT * FROM ai_analytics ORDER BY id");
                    $has_active = false;
                    foreach ($ai_models as $model) {
                        if ($model['status'] === 'active') {
                            $has_active = true;
                            break;
                        }
                    }
                    ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <h2 style="margin: 0; display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-chart-pie" style="color: var(--hologram-purple);"></i>
                            <span>System Analytics & Reports</span>
                        </h2>
                        <span class="cyber-badge" style="background:rgba(<?php echo $has_active ? '0,255,127' : '100,100,100'; ?>,0.3);">
                            System <?php echo $has_active ? 'Active' : 'Inactive'; ?>
                        </span>
                    </div>

                    <div class="orb-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                        <?php
                        $colors = ['var(--cyber-cyan)', 'var(--golden-pulse)', 'var(--neon-green)', 'var(--cyber-red)'];
                        $i = 0;
                        foreach ($ai_models as $model):
                            $color = $colors[$i % 4];
                            $badge_color = $model['status'] === 'active' ? 'rgba(0,255,127,0.3)' : ($model['status'] === 'training' ? 'rgba(255,165,0,0.3)' : 'rgba(100,100,100,0.3)');
                            $i++;
                        ?>
                            <div class="holo-card" style="padding: 20px;">
                                <h4 style="color: <?php echo $color; ?>; margin-bottom: 12px;"><?php echo htmlspecialchars($model['model_name']); ?></h4>
                                <div style="font-size: 2rem; color: var(--text-primary); margin-bottom: 8px;"><?php echo number_format($model['accuracy_rate'], 1); ?>%</div>
                                <div style="color: var(--text-muted); font-size: 0.85rem;">Accuracy Rate</div>
                                <span class="cyber-badge" style="margin-top: 10px;background:<?php echo $badge_color; ?>;"><?php echo ucfirst($model['status']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Activity & Alerts Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; margin-bottom: 30px;">

                    <!-- Recent Activity -->
                    <div class="holo-card">
                        <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-clock" style="color: var(--cyber-cyan);"></i>
                            <span>Recent Activity</span>
                        </h3>

                        <?php if (!empty($recent_records)): ?>
                            <div style="max-height: 400px; overflow-y: auto;">
                                <?php foreach (array_slice($recent_records, 0, 5) as $record): ?>
                                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 10px; margin-bottom: 10px; background: rgba(0, 191, 255, 0.05); border: 1px solid var(--glass-border); transition: all 0.3s;"
                                        onmouseover="this.style.background='rgba(0, 191, 255, 0.1)'; this.style.borderColor='var(--cyber-cyan)'"
                                        onmouseout="this.style.background='rgba(0, 191, 255, 0.05)'; this.style.borderColor='var(--glass-border)'">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--cyber-cyan), var(--hologram-purple)); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--text-primary);">
                                            <?php echo strtoupper(substr($record['first_name'] ?? 'U', 0, 1) . substr($record['last_name'] ?? 'N', 0, 1)); ?>
                                        </div>
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 3px;">
                                                <?php echo htmlspecialchars($record['student_name'] ?? 'Unknown'); ?>
                                            </div>
                                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                                <?php echo ucfirst($record['status'] ?? 'unknown'); ?> in <?php echo htmlspecialchars($record['class_name'] ?? 'N/A'); ?>
                                            </div>
                                        </div>
                                        <span class="cyber-badge <?php
                                                                    echo ($record['status'] ?? '') === 'present' ? 'success' : (($record['status'] ?? '') === 'late' ? 'warning' : 'danger');
                                                                    ?>">
                                            <?php echo ucfirst($record['status'] ?? 'N/A'); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                                <p>No recent activity</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Risk Students Alert -->
                    <div class="holo-card">
                        <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-exclamation-triangle" style="color: var(--cyber-red);"></i>
                            <span>Students Requiring Attention</span>
                        </h3>

                        <?php if (!empty($risk_students)): ?>
                            <div style="max-height: 400px; overflow-y: auto;">
                                <?php foreach ($risk_students as $student): ?>
                                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 10px; margin-bottom: 10px; background: rgba(255, 69, 0, 0.05); border: 1px solid rgba(255, 69, 0, 0.3); transition: all 0.3s;"
                                        onmouseover="this.style.background='rgba(255, 69, 0, 0.1)'; this.style.boxShadow='0 0 20px rgba(255, 69, 0, 0.3)'"
                                        onmouseout="this.style.background='rgba(255, 69, 0, 0.05)'; this.style.boxShadow='none'">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--cyber-red), var(--hologram-purple)); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--text-primary);">
                                            <?php echo strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)); ?>
                                        </div>
                                        <div style="flex: 1;">
                                            <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 3px;">
                                                <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                                            </div>
                                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                                ID: <?php echo htmlspecialchars($student['student_id']); ?> -
                                                <?php echo $student['absent_days']; ?>/<?php echo $student['total_days']; ?> days absent
                                            </div>
                                        </div>
                                        <span class="cyber-badge danger">
                                            <?php echo round(($student['absent_days'] / $student['total_days']) * 100, 1); ?>%
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 15px; color: var(--neon-green);"></i>
                                <p style="color: var(--neon-green);">No students at risk</p>
                                <p style="font-size: 0.85rem;">All students maintaining good attendance</p>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Quick Actions -->
                <section class="holo-card">
                    <h3 style="margin-bottom: 20px;">Quick Actions</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <a href="students.php" class="cyber-btn cyber-btn-primary" style="justify-content: center;">
                            <i class="fas fa-user-graduate"></i>
                            <span>Manage Students</span>
                        </a>
                        <a href="attendance.php" class="cyber-btn cyber-btn-success" style="justify-content: center;">
                            <i class="fas fa-check-circle"></i>
                            <span>Mark Attendance</span>
                        </a>
                        <a href="reports.php" class="cyber-btn cyber-btn-outline" style="justify-content: center;">
                            <i class="fas fa-chart-line"></i>
                            <span>View Reports</span>
                        </a>
                        <a href="analytics.php" class="cyber-btn cyber-btn-outline" style="justify-content: center;">
                            <i class="fas fa-chart-pie"></i>
                            <span>Analytics</span>
                        </a>
                    </div>
                </section>

                <!-- Enhanced Dashboard Components -->
                <div class="enhanced-components">
                    <!-- Analytics Dashboard -->
                    <?php include '../includes/analytics-dashboard.php'; ?>

                    <!-- Poll System -->
                    <?php include '../includes/poll-system.php'; ?>

                    <!-- Study Groups -->
                    <?php include '../includes/study-groups.php'; ?>

                    <!-- Grade Calculator -->
                    <?php include '../includes/grade-calculator.php'; ?>
                </div>

            </div>
        </main>
    </div>

    <!-- Visual Polish & Toast System -->
    <?php include '../includes/visual-polish.php'; ?>

    <!-- Quick Actions FAB -->
    <?php include '../includes/quick-actions.php'; ?>

    <!-- Online Users Widget -->
    <?php include '../includes/online-users.php'; ?>

    <!-- AI Copilot -->
    <?php include '../includes/ai-copilot.php'; ?>

    <!-- Mobile Bottom Nav -->
    <?php include '../includes/mobile-bottom-nav.php'; ?>

    <!-- JavaScript -->
    <script>
        // Animate stat orbs on load
        document.addEventListener('DOMContentLoaded', function() {
            const orbs = document.querySelectorAll('.stat-orb');
            orbs.forEach((orb, index) => {
                orb.style.opacity = '0';
                orb.style.transform = 'translateY(20px) scale(0.9)';
                setTimeout(() => {
                    orb.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                    orb.style.opacity = '1';
                    orb.style.transform = 'translateY(0) scale(1)';
                }, index * 100);
            });

            // Biometric orb click - redirect to biometric scan page
            document.querySelector('.biometric-orb').addEventListener('click', function() {
                window.location.href = 'biometric-scan.php';
            });
        });

        // Real-time clock update
        setInterval(() => {
            const now = new Date();
            const timeStr = now.toLocaleTimeString();
            document.title = `${timeStr} - Attendance Dashboard`;
        }, 1000);
    </script>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body>

</html>