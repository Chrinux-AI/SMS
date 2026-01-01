<?php
/**
 * Payroll Management - Admin Panel
 * Manage staff salary structures and payments
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Payroll Management";
$current_page = "finance/payroll.php";

// Fetch payroll data
try {
    $payroll_records = db()->fetchAll("SELECT u.id, u.full_name, u.role, u.email
        FROM users u WHERE u.role IN ('teacher', 'admin', 'staff') ORDER BY u.full_name") ?? [];
} catch (Exception $e) {
    $payroll_records = [];
}

$total_staff = count($payroll_records);

include '../../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <?php include '../../includes/head-meta.php'; ?>
    <link rel="stylesheet" href="../../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-hand-holding-usd"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Finance</span>
                <span>/</span>
                <span>Payroll</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_staff; ?></div>
                    <div class="stat-label">Total Staff</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo date('F Y'); ?></div>
                    <div class="stat-label">Current Period</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value">Pending</div>
                    <div class="stat-label">Processing Status</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="processPayroll()">
                <i class="fas fa-play"></i> Process Payroll
            </button>
            <button class="btn btn-secondary" onclick="generateSlips()">
                <i class="fas fa-file-alt"></i> Generate Salary Slips
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Staff Payroll</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Basic Salary</th>
                                <th>Allowances</th>
                                <th>Deductions</th>
                                <th>Net Salary</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($payroll_records)): ?>
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="empty-state">
                                            <i class="fas fa-users"></i>
                                            <p>No staff records found</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($payroll_records as $staff): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($staff['full_name']); ?></td>
                                        <td><span class="badge badge-info"><?php echo ucfirst($staff['role']); ?></span></td>
                                        <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                        <td>₦50,000</td>
                                        <td>₦5,000</td>
                                        <td>₦3,000</td>
                                        <td><strong>₦52,000</strong></td>
                                        <td><span class="badge badge-warning">Pending</span></td>
                                        <td>
                                            <button class="btn-icon btn-edit" title="Edit"><i class="fas fa-edit"></i></button>
                                            <button class="btn-icon btn-view" title="View"><i class="fas fa-eye"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/chatbot-unified.php'; ?>

    <script>
        function processPayroll() {
            if (confirm('Process payroll for all staff for ' + '<?php echo date("F Y"); ?>' + '?')) {
                alert('Payroll processing initiated. This feature is under development.');
            }
        }

        function generateSlips() {
            alert('Salary slips generation feature is under development.');
        }
    </script>
</body>
</html>
