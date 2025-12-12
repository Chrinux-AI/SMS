<?php

/**
 * Student Fee Invoices - Student Panel
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_role('student');

$page_title = "My Fee Invoices";
$current_page = "fee-invoices.php";

$student_id = $_SESSION['user_id'];

// Fetch student invoices with total amounts
$invoices = db()->fetchAll("
    SELECT fi.*,
           (SELECT SUM(amount) FROM fee_invoice_items WHERE invoice_id = fi.id) as total_amount,
           (SELECT IFNULL(SUM(amount_paid), 0) FROM fee_payments WHERE invoice_id = fi.id) as paid_amount
    FROM fee_invoices fi
    WHERE fi.student_id = ?
    ORDER BY fi.due_date DESC
", [$student_id]);

$total_pending = 0;
$total_paid = 0;
foreach ($invoices as $invoice) {
    $balance = $invoice['total_amount'] - $invoice['paid_amount'];
    if ($balance > 0) {
        $total_pending += $balance;
    }
    $total_paid += $invoice['paid_amount'];
}

include '../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <link rel="stylesheet" href="../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-file-invoice"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Finance</span>
                <span>/</span>
                <span>Fee Invoices</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-details">
                    <div class="stat-value">₹<?php echo number_format($total_pending, 2); ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value">₹<?php echo number_format($total_paid, 2); ?></div>
                    <div class="stat-label">Paid</div>
                </div>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Invoices</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Invoice No.</th>
                                <th>Academic Year</th>
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
                            ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($invoice['invoice_number']); ?></span></td>
                                    <td><?php echo htmlspecialchars($invoice['academic_year']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($invoice['due_date'])); ?></td>
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
                                        $status_colors = [
                                            'paid' => 'success',
                                            'partial' => 'warning',
                                            'pending' => 'danger',
                                            'overdue' => 'dark'
                                        ];
                                        ?>
                                        <span class="badge badge-<?php echo $status_colors[$invoice['status']]; ?>">
                                            <?php echo ucfirst($invoice['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-icon btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($balance > 0): ?>
                                            <a href="payments.php?invoice=<?php echo $invoice['id']; ?>" class="btn-icon btn-success" title="Pay Now">
                                                <i class="fas fa-credit-card"></i>
                                            </a>
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
