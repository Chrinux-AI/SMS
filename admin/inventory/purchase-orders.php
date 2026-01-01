<?php
/**
 * Purchase Orders - Admin Panel
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Purchase Orders";
$current_page = "inventory/purchase-orders.php";

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
            <h1><i class="fas fa-shopping-cart"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Inventory</span>
                <span>/</span>
                <span>Purchase Orders</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="stat-details">
                    <div class="stat-value">25</div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-details">
                    <div class="stat-value">5</div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-truck"></i></div>
                <div class="stat-details">
                    <div class="stat-value">3</div>
                    <div class="stat-label">In Transit</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="createPO()">
                <i class="fas fa-plus"></i> New Purchase Order
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Purchase Orders</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Vendor</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>PO-2025-001</td>
                                <td>Office Supplies Co.</td>
                                <td>Jan 10, 2025</td>
                                <td>5</td>
                                <td>₦125,000</td>
                                <td><span class="badge badge-success">Delivered</span></td>
                                <td>
                                    <button class="btn-icon btn-view"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit"><i class="fas fa-print"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>PO-2025-002</td>
                                <td>Tech Equipment Ltd.</td>
                                <td>Jan 15, 2025</td>
                                <td>3</td>
                                <td>₦450,000</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td>
                                    <button class="btn-icon btn-view"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/chatbot-unified.php'; ?>

    <script>
        function createPO() { alert('Create purchase order coming soon!'); }
    </script>
</body>
</html>
