<?php
/**
 * Stock Management - Admin Panel
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Stock Management";
$current_page = "inventory/stock.php";

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
            <h1><i class="fas fa-warehouse"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Inventory</span>
                <span>/</span>
                <span>Stock</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-cubes"></i></div>
                <div class="stat-details">
                    <div class="stat-value">500</div>
                    <div class="stat-label">Total Items</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-details">
                    <div class="stat-value">15</div>
                    <div class="stat-label">Low Stock</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value">3</div>
                    <div class="stat-label">Out of Stock</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="addStock()">
                <i class="fas fa-plus"></i> Add Stock
            </button>
            <button class="btn btn-secondary" onclick="adjustStock()">
                <i class="fas fa-balance-scale"></i> Adjust Stock
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Stock Items</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Min Stock</th>
                                <th>Unit Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>STK-001</td>
                                <td>A4 Paper (Ream)</td>
                                <td>Stationery</td>
                                <td>150</td>
                                <td>20</td>
                                <td>₦2,500</td>
                                <td><span class="badge badge-success">In Stock</span></td>
                                <td>
                                    <button class="btn-icon btn-view"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>STK-002</td>
                                <td>Whiteboard Markers</td>
                                <td>Stationery</td>
                                <td>8</td>
                                <td>10</td>
                                <td>₦500</td>
                                <td><span class="badge badge-warning">Low Stock</span></td>
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
        function addStock() { alert('Add stock coming soon!'); }
        function adjustStock() { alert('Adjust stock coming soon!'); }
    </script>
</body>
</html>
