<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for TEST_MODE support
require_role('superadmin', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// MASTER STATS - Cross-School Analytics
$total_schools = db()->fetchOne("SELECT COUNT(*) as count FROM schools")['count'] ?? 1;
$total_students = db()->fetchOne("SELECT COUNT(*) as count FROM students WHERE is_active = 1")['count'] ?? 0;
$total_teachers = db()->fetchOne("SELECT COUNT(*) as count FROM teachers WHERE is_active = 1")['count'] ?? 0;
$total_staff = db()->fetchOne("SELECT COUNT(*) as count FROM users WHERE role IN ('admin', 'accountant', 'librarian', 'nurse', 'counselor') AND is_active = 1")['count'] ?? 0;

// Revenue & Financial Overview
$total_revenue = db()->fetchOne("
    SELECT IFNULL(SUM(amount_paid), 0) as total
    FROM fee_payments
    WHERE MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())
")['total'] ?? 0;

$pending_fees = db()->fetchOne("
    SELECT IFNULL(SUM(fii.amount), 0) - IFNULL(SUM(fp.amount_paid), 0) as pending
    FROM fee_invoices fi
    LEFT JOIN fee_invoice_items fii ON fi.id = fii.invoice_id
    LEFT JOIN fee_payments fp ON fi.id = fp.invoice_id
    WHERE fi.status != 'paid'
")['pending'] ?? 0;

// System Health Metrics
$active_sessions = db()->fetchOne("SELECT COUNT(*) as count FROM user_sessions WHERE last_activity > DATE_SUB(NOW(), INTERVAL 30 MINUTE)")['count'] ?? 0;
$daily_attendance = db()->fetchOne("SELECT COUNT(*) as count FROM attendance WHERE date = CURDATE()")['count'] ?? 0;

// Recent Activities (Last 24 hours)
$recent_admissions = db()->fetchOne("SELECT COUNT(*) as count FROM students WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)")['count'] ?? 0;
$recent_payments = db()->fetchOne("SELECT COUNT(*) as count FROM fee_payments WHERE payment_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)")['count'] ?? 0;

// School Performance Rankings
$school_rankings = db()->fetchAll("
    SELECT s.school_name,
           COUNT(DISTINCT st.id) as student_count,
           AVG(er.marks_obtained / er.total_marks * 100) as avg_performance
    FROM schools s
    LEFT JOIN students st ON s.id = st.school_id AND st.is_active = 1
    LEFT JOIN exam_results er ON st.id = er.student_id
    WHERE er.created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
    GROUP BY s.id
    ORDER BY avg_performance DESC
    LIMIT 10
");

// Critical Alerts
$critical_alerts = [];

// Low attendance schools
$low_attendance = db()->fetchAll("
    SELECT s.school_name,
           COUNT(a.id) as present_count,
           COUNT(DISTINCT a.student_id) as total_students,
           ROUND((COUNT(a.id) / COUNT(DISTINCT a.student_id)) * 100, 1) as attendance_rate
    FROM schools s
    LEFT JOIN students st ON s.id = st.school_id
    LEFT JOIN attendance a ON st.id = a.student_id AND a.date = CURDATE() AND a.status = 'present'
    GROUP BY s.id
    HAVING attendance_rate < 70
");

foreach ($low_attendance as $alert) {
    $critical_alerts[] = [
        'type' => 'warning',
        'icon' => 'exclamation-triangle',
        'message' => $alert['school_name'] . ' has low attendance: ' . $alert['attendance_rate'] . '%'
    ];
}

// Fee collection issues
if ($pending_fees > 100000) {
    $critical_alerts[] = [
        'type' => 'danger',
        'icon' => 'money-bill-wave',
        'message' => 'High pending fees: ₹' . number_format($pending_fees, 2)
    ];
}

$page_title = 'Super Admin Command Center';
$page_icon = 'crown';
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
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb golden"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                    <span class="cyber-badge golden" style="margin-left:15px;">AI-Powered</span>
                </div>
                <div class="header-actions">
                    <div class="biometric-orb" title="Voice Command"><i class="fas fa-microphone"></i></div>
                    <div class="user-card" style="padding:8px 15px;">
                        <div class="user-avatar golden" style="width:35px;height:35px;">
                            <?php echo strtoupper(substr($full_name, 0, 2)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($full_name); ?></div>
                            <div class="user-role">Super Administrator</div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="cyber-content slide-in">

                <?php if (!empty($critical_alerts)): ?>
                    <section class="holo-card alert-banner" style="background:linear-gradient(135deg,rgba(255,193,7,0.1),rgba(255,87,34,0.1));border-left:4px solid var(--golden-pulse);margin-bottom:25px;">
                        <h3 style="color:var(--golden-pulse);margin-bottom:15px;"><i class="fas fa-bell"></i> Critical Alerts</h3>
                        <?php foreach ($critical_alerts as $alert): ?>
                            <div class="alert-item" style="padding:10px;margin-bottom:10px;background:rgba(0,0,0,0.2);border-radius:8px;display:flex;align-items:center;gap:12px;">
                                <i class="fas fa-<?php echo $alert['icon']; ?>" style="color:var(--alert-<?php echo $alert['type']; ?>);font-size:1.2rem;"></i>
                                <span><?php echo htmlspecialchars($alert['message']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>

                <!-- Master Statistics Grid -->
                <section class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
                    <div class="stat-orb">
                        <div class="stat-icon golden"><i class="fas fa-school"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $total_schools; ?></div>
                            <div class="stat-label">Active Schools</div>
                            <div class="stat-trend up"><i class="fas fa-building"></i> Multi-Campus</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon cyan"><i class="fas fa-user-graduate"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($total_students); ?></div>
                            <div class="stat-label">Total Students</div>
                            <div class="stat-trend up"><i class="fas fa-chart-line"></i> Growing</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon green"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($total_teachers); ?></div>
                            <div class="stat-label">Teaching Staff</div>
                            <div class="stat-trend up"><i class="fas fa-users"></i> Active</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon purple"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="stat-content">
                            <div class="stat-value">₹<?php echo number_format($total_revenue / 100000, 1); ?>L</div>
                            <div class="stat-label">Monthly Revenue</div>
                            <div class="stat-trend up"><i class="fas fa-rupee-sign"></i> This Month</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $active_sessions; ?></div>
                            <div class="stat-label">Active Sessions</div>
                            <div class="stat-trend"><i class="fas fa-wifi"></i> Live Now</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon red"><i class="fas fa-exclamation-circle"></i></div>
                        <div class="stat-content">
                            <div class="stat-value">₹<?php echo number_format($pending_fees / 100000, 1); ?>L</div>
                            <div class="stat-label">Pending Fees</div>
                            <div class="stat-trend down"><i class="fas fa-arrow-down"></i> Collect</div>
                        </div>
                    </div>
                </section>

                <!-- Quick Actions Dashboard -->
                <section class="holo-card" style="margin-top:30px;">
                    <h3 style="margin-bottom:20px;"><i class="fas fa-bolt"></i> Quick Management</h3>
                    <div class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(250px,1fr));">
                        <a href="../admin/hr/employees.php" class="action-card" style="text-decoration:none;">
                            <div class="action-icon cyan"><i class="fas fa-users-cog"></i></div>
                            <h4>HR & Payroll</h4>
                            <p>Manage <?php echo $total_staff + $total_teachers; ?> employees</p>
                        </a>

                        <a href="../admin/finance/overview.php" class="action-card" style="text-decoration:none;">
                            <div class="action-icon green"><i class="fas fa-chart-pie"></i></div>
                            <h4>Financial Overview</h4>
                            <p>Revenue, expenses & budgets</p>
                        </a>

                        <a href="../admin/inventory/assets.php" class="action-card" style="text-decoration:none;">
                            <div class="action-icon purple"><i class="fas fa-boxes"></i></div>
                            <h4>Inventory & Assets</h4>
                            <p>Track school resources</p>
                        </a>

                        <a href="../admin/reports/custom-builder.php" class="action-card" style="text-decoration:none;">
                            <div class="action-icon orange"><i class="fas fa-file-invoice"></i></div>
                            <h4>Custom Reports</h4>
                            <p>Build & schedule reports</p>
                        </a>

                        <a href="../admin/integrations/api-manager.php" class="action-card" style="text-decoration:none;">
                            <div class="action-icon golden"><i class="fas fa-plug"></i></div>
                            <h4>API & Integrations</h4>
                            <p>Connect 50+ services</p>
                        </a>

                        <a href="../admin/white-label/branding.php" class="action-card" style="text-decoration:none;">
                            <div class="action-icon red"><i class="fas fa-palette"></i></div>
                            <h4>White Label Mode</h4>
                            <p>Reseller & branding tools</p>
                        </a>
                    </div>
                </section>

                <!-- School Performance Rankings -->
                <?php if (!empty($school_rankings)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-trophy"></i> Top Performing Schools</h3>
                        <div class="cyber-table-container">
                            <table class="cyber-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>School Name</th>
                                        <th>Students</th>
                                        <th>Avg Performance</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($school_rankings as $index => $school): ?>
                                        <tr>
                                            <td>
                                                <?php if ($index < 3): ?>
                                                    <span class="cyber-badge golden">#<?php echo $index + 1; ?></span>
                                                <?php else: ?>
                                                    #<?php echo $index + 1; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($school['school_name']); ?></td>
                                            <td><?php echo number_format($school['student_count']); ?></td>
                                            <td>
                                                <div class="progress-bar" style="width:100px;height:8px;background:rgba(255,255,255,0.1);border-radius:4px;overflow:hidden;">
                                                    <div style="width:<?php echo $school['avg_performance']; ?>%;height:100%;background:var(--neon-green);"></div>
                                                </div>
                                                <span style="margin-left:10px;"><?php echo round($school['avg_performance'], 1); ?>%</span>
                                            </td>
                                            <td><span class="cyber-badge green">Excellent</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>
