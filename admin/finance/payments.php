<?php

/**
 * Payment Management - Admin Panel
 * Track and manage fee payments
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Payment Management";
$current_page = "finance/payments.php";

// Fetch recent payments
$payments = db()->fetchAll("
    SELECT fp.*, fi.invoice_number,
           CONCAT(u.first_name, ' ', u.last_name) as student_name
    FROM fee_payments fp
    JOIN fee_invoices fi ON fp.invoice_id = fi.id
    JOIN students s ON fi.student_id = s.id
    JOIN users u ON s.user_id = u.id
    ORDER BY fp.payment_date DESC
    LIMIT 100
");

$today_total = array_sum(array_column(
    array_filter($payments, fn($p) => date('Y-m-d', strtotime($p['payment_date'])) == date('Y-m-d')),
    'amount_paid'
));

$total_collected = array_sum(array_column($payments, 'amount_paid'));

$payment_methods = array_count_values(array_column($payments, 'payment_method'));

include '../../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/apple-touch-icon.png">
    <link rel="manifest" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>manifest.json">
    <meta name="msapplication-TileColor" content="#00BFFF">
    <meta name="msapplication-TileImage" content="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/mstile-150x150.png">
    <meta name="theme-color" content="#0a0a0f">
    <link rel="stylesheet" href="../../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-credit-card"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Finance</span>
                <span>/</span>
                <span>Payments</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-details">
                    <div class="stat-value">₹<?php echo number_format($today_total, 2); ?></div>
                    <div class="stat-label">Today's Collection</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-details">
                    <div class="stat-value">₹<?php echo number_format($total_collected, 2); ?></div>
                    <div class="stat-label">Total Collected</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-receipt"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count($payments); ?></div>
                    <div class="stat-label">Total Transactions</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary">
                <i class="fas fa-plus"></i> Record Payment
            </button>
            <button class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Report
            </button>
            <button class="btn btn-info">
                <i class="fas fa-print"></i> Print Receipt
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Recent Payments</h3>
                <div class="card-actions">
                    <input type="date" id="filterDate" class="filter-select" value="<?php echo date('Y-m-d'); ?>">
                    <select id="filterMethod" class="filter-select">
                        <option value="">All Methods</option>
                        <option value="cash">Cash</option>
                        <option value="online">Online</option>
                        <option value="cheque">Cheque</option>
                        <option value="card">Card</option>
                        <option value="upi">UPI</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Receipt No.</th>
                                <th>Student Name</th>
                                <th>Invoice No.</th>
                                <th>Payment Date</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Transaction ID</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><span class="badge badge-success">REC-<?php echo str_pad($payment['id'], 6, '0', STR_PAD_LEFT); ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($payment['student_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($payment['invoice_number']); ?></td>
                                    <td><?php echo date('M d, Y g:i A', strtotime($payment['payment_date'])); ?></td>
                                    <td><strong class="text-success">₹<?php echo number_format($payment['amount_paid'], 2); ?></strong></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php
                                            $icons = [
                                                'cash' => 'money-bill',
                                                'online' => 'globe',
                                                'cheque' => 'money-check',
                                                'card' => 'credit-card',
                                                'upi' => 'mobile-alt'
                                            ];
                                            ?>
                                            <i class="fas fa-<?php echo $icons[$payment['payment_method']] ?? 'dollar-sign'; ?>"></i>
                                            <?php echo ucfirst($payment['payment_method']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($payment['transaction_id'] ?? 'N/A'); ?></td>
                                    <td>
                                        <button class="btn-icon btn-info" title="Print Receipt">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <button class="btn-icon btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
