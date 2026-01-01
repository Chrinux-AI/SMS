<?php

/**
 * Vehicle Management - Admin Panel
 * Manage school transport vehicles
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Vehicle Management";
$current_page = "transport/vehicles.php";

// Fetch vehicles
$vehicles = db()->fetchAll("SELECT * FROM transport_vehicles ORDER BY vehicle_number");

$active_count = count(array_filter($vehicles, fn($v) => $v['status'] == 'active'));
$maintenance_count = count(array_filter($vehicles, fn($v) => $v['status'] == 'maintenance'));

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
            <h1><i class="fas fa-bus"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Transport</span>
                <span>/</span>
                <span>Vehicles</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-bus-alt"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count($vehicles); ?></div>
                    <div class="stat-label">Total Vehicles</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $active_count; ?></div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-tools"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $maintenance_count; ?></div>
                    <div class="stat-label">In Maintenance</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Vehicle
            </button>
            <button class="btn btn-warning">
                <i class="fas fa-wrench"></i> Schedule Maintenance
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Vehicles</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Vehicle No.</th>
                                <th>Type</th>
                                <th>Model</th>
                                <th>Capacity</th>
                                <th>Registration</th>
                                <th>Insurance Expiry</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vehicles as $vehicle): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($vehicle['vehicle_number']); ?></span></td>
                                    <td><?php echo ucfirst($vehicle['vehicle_type']); ?></td>
                                    <td><?php echo htmlspecialchars($vehicle['model']); ?></td>
                                    <td><?php echo $vehicle['seating_capacity']; ?> seats</td>
                                    <td><?php echo htmlspecialchars($vehicle['registration_number']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($vehicle['insurance_expiry'])); ?></td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'active' => 'success',
                                            'inactive' => 'secondary',
                                            'maintenance' => 'warning',
                                            'retired' => 'danger'
                                        ];
                                        ?>
                                        <span class="badge badge-<?php echo $status_class[$vehicle['status']]; ?>">
                                            <?php echo ucfirst($vehicle['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-icon btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-info" title="Track">
                                            <i class="fas fa-map-marked-alt"></i>
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
