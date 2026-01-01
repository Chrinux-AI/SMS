<?php

/**
 * Assets Management - Admin Panel
 * Track and manage school assets
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Assets Management";
$current_page = "inventory/assets.php";

// Fetch categories and assets
$categories = db()->fetchAll("SELECT * FROM asset_categories WHERE is_active = 1");
$assets = db()->fetchAll("SELECT a.*, ac.category_name FROM assets a JOIN asset_categories ac ON a.category_id = ac.id ORDER BY ac.category_name, a.asset_name");

$total_assets = count($assets);
$in_use = count(array_filter($assets, fn($a) => $a['status'] == 'in_use'));
$available = count(array_filter($assets, fn($a) => $a['status'] == 'available'));

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
            <h1><i class="fas fa-boxes"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Inventory</span>
                <span>/</span>
                <span>Assets</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-box"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_assets; ?></div>
                    <div class="stat-label">Total Assets</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $in_use; ?></div>
                    <div class="stat-label">In Use</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-warehouse"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $available; ?></div>
                    <div class="stat-label">Available</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add New Asset
            </button>
            <button class="btn btn-success">
                <i class="fas fa-qrcode"></i> Generate QR Codes
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Assets</h3>
                <div class="card-actions">
                    <select id="filterCategory" class="filter-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Asset Code</th>
                                <th>Asset Name</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Purchase Date</th>
                                <th>Value (₹)</th>
                                <th>Condition</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assets as $asset): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($asset['asset_code']); ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($asset['asset_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($asset['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars($asset['location']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($asset['purchase_date'])); ?></td>
                                    <td>₹<?php echo number_format($asset['purchase_cost'], 2); ?></td>
                                    <td><span class="badge badge-<?php
                                                                    echo $asset['condition'] == 'excellent' ? 'success' : ($asset['condition'] == 'good' ? 'info' : 'warning');
                                                                    ?>"><?php echo ucfirst($asset['condition']); ?></span></td>
                                    <td>
                                        <?php
                                        $status_colors = [
                                            'in_use' => 'success',
                                            'available' => 'info',
                                            'maintenance' => 'warning',
                                            'retired' => 'secondary',
                                            'lost' => 'danger'
                                        ];
                                        ?>
                                        <span class="badge badge-<?php echo $status_colors[$asset['status']]; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $asset['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-icon btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-info" title="QR Code">
                                            <i class="fas fa-qrcode"></i>
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

    <script>
        function openAddModal() {
            alert('Add Asset Modal - Implementation pending');
        }
    </script>
</body>

</html>
