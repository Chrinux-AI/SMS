<?php

/**
 * Owner / Director Dashboard - Strategic Oversight Portal
 * Verdant SMS v3.0
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for authentication
require_role('owner', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Director';

// Financial Overview
$total_revenue = db()->fetchOne("
    SELECT IFNULL(SUM(amount_paid), 0) as total
    FROM fee_payments
    WHERE YEAR(payment_date) = YEAR(CURDATE())
")['total'] ?? 0;

$monthly_revenue = db()->fetchOne("
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

// Institution Stats
$total_students = db()->fetchOne("SELECT COUNT(*) as count FROM students WHERE is_active = 1")['count'] ?? 0;
$total_teachers = db()->fetchOne("SELECT COUNT(*) as count FROM teachers WHERE is_active = 1")['count'] ?? 0;
$total_staff = db()->fetchOne("SELECT COUNT(*) as count FROM users WHERE role NOT IN ('student', 'parent', 'alumni') AND status = 'active'")['count'] ?? 0;

// Attendance Rate
$attendance_rate = db()->fetchOne("
    SELECT ROUND((SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as rate
    FROM attendance
    WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
")['rate'] ?? 0;

// Recent Major Transactions
$recent_transactions = db()->fetchAll("
    SELECT fp.*, CONCAT(s.first_name, ' ', s.last_name) as student_name
    FROM fee_payments fp
    LEFT JOIN students s ON fp.student_id = s.id
    ORDER BY fp.payment_date DESC
    LIMIT 10
");

$page_title = 'Owner Command Center';
$page_icon = 'building';
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--cyber-cyan, #00BFFF);
        }

        .stat-value {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--neon-green, #00FF7F);
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
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
            font-size: 1.2rem;
            color: var(--cyber-cyan, #00BFFF);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .transaction-list {
            list-style: none;
        }

        .transaction-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .transaction-item:last-child {
            border-bottom: none;
        }

        .amount-positive {
            color: var(--neon-green, #00FF7F);
            font-weight: 600;
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
                    <div class="page-icon-orb gold"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <span class="welcome-text">Welcome, <?php echo htmlspecialchars($full_name); ?></span>
                </div>
            </header>

            <!-- Financial Overview -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="stat-value">₹<?php echo number_format($total_revenue); ?></div>
                    <div class="stat-label">Total Revenue (YTD)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-value">₹<?php echo number_format($monthly_revenue); ?></div>
                    <div class="stat-label">This Month</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-value">₹<?php echo number_format($pending_fees); ?></div>
                    <div class="stat-label">Pending Fees</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-percentage"></i></div>
                    <div class="stat-value"><?php echo $attendance_rate; ?>%</div>
                    <div class="stat-label">Avg Attendance (30d)</div>
                </div>
            </div>

            <!-- Institution Stats -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-value"><?php echo number_format($total_students); ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="stat-value"><?php echo number_format($total_teachers); ?></div>
                    <div class="stat-label">Teaching Staff</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value"><?php echo number_format($total_staff); ?></div>
                    <div class="stat-label">Total Staff</div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="dashboard-section">
                <h2 class="section-title"><i class="fas fa-exchange-alt"></i> Recent Transactions</h2>
                <ul class="transaction-list">
                    <?php if (empty($recent_transactions)): ?>
                        <li class="transaction-item">
                            <span>No recent transactions</span>
                        </li>
                    <?php else: ?>
                        <?php foreach ($recent_transactions as $trans): ?>
                            <li class="transaction-item">
                                <div>
                                    <strong><?php echo htmlspecialchars($trans['student_name'] ?? 'Unknown'); ?></strong>
                                    <br><small><?php echo date('M d, Y', strtotime($trans['payment_date'])); ?></small>
                                </div>
                                <span class="amount-positive">+₹<?php echo number_format($trans['amount_paid'], 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-section">
                <h2 class="section-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="../admin/reports.php" class="cyber-btn cyan"><i class="fas fa-chart-bar"></i> View Reports</a>
                    <a href="../admin/analytics.php" class="cyber-btn purple"><i class="fas fa-brain"></i> Analytics</a>
                    <a href="../admin/fee-management.php" class="cyber-btn green"><i class="fas fa-dollar-sign"></i> Fee Management</a>
                    <a href="../admin/students.php" class="cyber-btn orange"><i class="fas fa-user-graduate"></i> Students</a>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>