<?php

/**
 * Invoice Management - Admin Panel
 * Generate and manage fee invoices
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Invoice Management";
$current_page = "finance/invoices.php";

// Fetch invoices with student details
$invoices = db()->fetchAll("
    SELECT fi.*,
           CONCAT(u.first_name, ' ', u.last_name) as student_name,
           u.email,
           (SELECT SUM(amount) FROM fee_invoice_items WHERE invoice_id = fi.id) as total_amount,
           (SELECT IFNULL(SUM(amount_paid), 0) FROM fee_payments WHERE invoice_id = fi.id) as paid_amount
    FROM fee_invoices fi
    JOIN students s ON fi.student_id = s.id
    JOIN users u ON s.user_id = u.id
    ORDER BY fi.invoice_date DESC
    LIMIT 100
");

$total_invoiced = array_sum(array_column($invoices, 'total_amount'));
$total_collected = array_sum(array_column($invoices, 'paid_amount'));
$pending_count = count(array_filter($invoices, fn($i) => $i['status'] == 'pending'));

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
            <h1><i class="fas fa-file-invoice-dollar"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Finance</span>
                <span>/</span>
                <span>Invoices</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="stat-details">
                    <div class="stat-value">₹<?php echo number_format($total_invoiced, 2); ?></div>
                    <div class="stat-label">Total Invoiced</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value">₹<?php echo number_format($total_collected, 2); ?></div>
                    <div class="stat-label">Collected</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $pending_count; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-details">
                    <div class="stat-value">₹<?php echo number_format($total_invoiced - $total_collected, 2); ?></div>
                    <div class="stat-label">Outstanding</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary">
                <i class="fas fa-plus"></i> Generate Invoice
            </button>
            <button class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export to Excel
            </button>
            <button class="btn btn-info">
                <i class="fas fa-envelope"></i> Send Reminders
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Invoices</h3>
                <div class="card-actions">
                    <input type="text" id="searchInput" placeholder="Search by student or invoice..." class="search-input">
                    <select id="filterStatus" class="filter-select">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="partial">Partial</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Invoice No.</th>
                                <th>Student Name</th>
                                <th>Invoice Date</th>
                                <th>Due Date</th>
                                <th>Total Amount</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $invoice):
                                $balance = $invoice['total_amount'] - $invoice['paid_amount'];
                                $is_overdue = strtotime($invoice['due_date']) < strtotime(date('Y-m-d')) && $balance > 0;
                            ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($invoice['invoice_number']); ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($invoice['student_name']); ?></strong></td>
                                    <td><?php echo date('M d, Y', strtotime($invoice['invoice_date'])); ?></td>
                                    <td>
                                        <?php if ($is_overdue): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo date('M d, Y', strtotime($invoice['due_date'])); ?></span>
                                        <?php else: ?>
                                            <?php echo date('M d, Y', strtotime($invoice['due_date'])); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong>₹<?php echo number_format($invoice['total_amount'], 2); ?></strong></td>
                                    <td>₹<?php echo number_format($invoice['paid_amount'], 2); ?></td>
                                    <td>
                                        <?php if ($balance > 0): ?>
                                            <strong class="text-warning">₹<?php echo number_format($balance, 2); ?></strong>
                                        <?php else: ?>
                                            <span class="text-success">₹0.00</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'paid' => 'success',
                                            'partial' => 'warning',
                                            'pending' => 'danger',
                                            'overdue' => 'dark'
                                        ];
                                        $display_status = $is_overdue ? 'overdue' : $invoice['status'];
                                        ?>
                                        <span class="badge badge-<?php echo $status_class[$display_status]; ?>">
                                            <?php echo ucfirst($display_status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-icon btn-view" title="View Invoice">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon btn-info" title="Print">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <?php if ($balance > 0): ?>
                                            <button class="btn-icon btn-success" title="Record Payment">
                                                <i class="fas fa-money-bill"></i>
                                            </button>
                                        <?php endif; ?>
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
