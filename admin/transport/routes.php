<?php

/**
 * Transport Routes Management - Admin Panel
 * Manage school transport routes
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Transport Routes Management";
$current_page = "transport/routes.php";

// Fetch routes
$routes = db()->fetchAll("SELECT * FROM transport_routes ORDER BY route_number");

$total_routes = count($routes);
$active_routes = count(array_filter($routes, fn($r) => $r['is_active'] == 1));

include '../../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <link rel="stylesheet" href="../../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-route"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Transport</span>
                <span>/</span>
                <span>Routes</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-route"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_routes; ?></div>
                    <div class="stat-label">Total Routes</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $active_routes; ?></div>
                    <div class="stat-label">Active Routes</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add New Route
            </button>
            <button class="btn btn-success">
                <i class="fas fa-map"></i> View Map
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Routes</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Route No.</th>
                                <th>Route Name</th>
                                <th>Start Point</th>
                                <th>End Point</th>
                                <th>Distance (km)</th>
                                <th>Duration</th>
                                <th>Fare (₹)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($routes as $route): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($route['route_number']); ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($route['route_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($route['starting_point']); ?></td>
                                    <td><?php echo htmlspecialchars($route['ending_point']); ?></td>
                                    <td><?php echo number_format($route['total_distance_km'], 2); ?></td>
                                    <td><?php echo $route['estimated_time_minutes']; ?> min</td>
                                    <td>₹<?php echo number_format($route['fare_amount'], 2); ?></td>
                                    <td>
                                        <?php if ($route['is_active']): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn-icon btn-view" title="View Route">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </button>
                                        <button class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
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
            alert('Add Route Modal - Implementation pending');
        }
    </script>
</body>

</html>
