<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for TEST_MODE support
require_role('transport', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Fleet Overview
$total_vehicles = db()->fetchOne("SELECT COUNT(*) as count FROM transport_vehicles")['count'] ?? 0;
$active_vehicles = db()->fetchOne("SELECT COUNT(*) as count FROM transport_vehicles WHERE status = 'active'")['count'] ?? 0;
$under_maintenance = db()->fetchOne("SELECT COUNT(*) as count FROM transport_vehicles WHERE status = 'maintenance'")['count'] ?? 0;

// Route Stats
$total_routes = db()->fetchOne("SELECT COUNT(*) as count FROM transport_routes")['count'] ?? 0;
$active_students = db()->fetchOne("SELECT COUNT(*) as count FROM transport_assignments WHERE is_active = 1")['count'] ?? 0;

// Today's Trips
$today_trips = db()->fetchAll("
    SELECT tv.vehicle_number, tv.vehicle_type, tr.route_name,
           CONCAT(u.first_name, ' ', u.last_name) as driver_name,
           COUNT(ta.id) as student_count
    FROM transport_vehicles tv
    LEFT JOIN transport_routes tr ON tv.assigned_route_id = tr.id
    LEFT JOIN transport_drivers td ON tv.driver_id = td.id
    LEFT JOIN users u ON td.user_id = u.id
    LEFT JOIN transport_assignments ta ON tr.id = ta.route_id AND ta.is_active = 1
    WHERE tv.status = 'active'
    GROUP BY tv.id
");

// Maintenance Schedule
$upcoming_maintenance = db()->fetchAll("
    SELECT tv.vehicle_number, tv.vehicle_type, tm.maintenance_type, tm.scheduled_date
    FROM transport_maintenance tm
    JOIN transport_vehicles tv ON tm.vehicle_id = tv.id
    WHERE tm.scheduled_date >= CURDATE() AND tm.status = 'scheduled'
    ORDER BY tm.scheduled_date
    LIMIT 5
");

// Monthly Fuel Cost
$fuel_cost = db()->fetchOne("
    SELECT IFNULL(SUM(amount), 0) as total
    FROM transport_expenses
    WHERE expense_type = 'fuel' AND MONTH(expense_date) = MONTH(CURDATE())
")['total'] ?? 0;

$page_title = 'Transport Management';
$page_icon = 'bus';
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
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb orange"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <button class="cyber-btn cyan" onclick="window.location.href='../admin/transport/add-vehicle.php'">
                        <i class="fas fa-plus"></i> Add Vehicle
                    </button>
                    <div class="user-card" style="padding:8px 15px;">
                        <div class="user-avatar orange" style="width:35px;height:35px;">
                            <?php echo strtoupper(substr($full_name, 0, 2)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($full_name); ?></div>
                            <div class="user-role">Transport Manager</div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="cyber-content slide-in">

                <section class="orb-grid">
                    <div class="stat-orb">
                        <div class="stat-icon cyan"><i class="fas fa-bus"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $total_vehicles; ?></div>
                            <div class="stat-label">Total Vehicles</div>
                            <div class="stat-trend"><i class="fas fa-warehouse"></i> Fleet</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $active_vehicles; ?></div>
                            <div class="stat-label">Active</div>
                            <div class="stat-trend up"><i class="fas fa-road"></i> Running</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon orange"><i class="fas fa-tools"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $under_maintenance; ?></div>
                            <div class="stat-label">Maintenance</div>
                            <div class="stat-trend"><i class="fas fa-wrench"></i> Servicing</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon purple"><i class="fas fa-route"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $total_routes; ?></div>
                            <div class="stat-label">Active Routes</div>
                            <div class="stat-trend"><i class="fas fa-map"></i> Coverage</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon cyan"><i class="fas fa-users"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $active_students; ?></div>
                            <div class="stat-label">Students Using</div>
                            <div class="stat-trend"><i class="fas fa-user-graduate"></i> Active</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon red"><i class="fas fa-gas-pump"></i></div>
                        <div class="stat-content">
                            <div class="stat-value">₹<?php echo number_format($fuel_cost / 1000, 1); ?>K</div>
                            <div class="stat-label">Fuel Cost</div>
                            <div class="stat-trend down"><i class="fas fa-rupee-sign"></i> This Month</div>
                        </div>
                    </div>
                </section>

                <!-- Quick Actions -->
                <section class="holo-card" style="margin-top:30px;">
                    <h3 style="margin-bottom:20px;"><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
                        <a href="../admin/transport/vehicles.php" class="action-card">
                            <div class="action-icon cyan"><i class="fas fa-bus-alt"></i></div>
                            <h4>Manage Fleet</h4>
                            <p>Vehicles & drivers</p>
                        </a>

                        <a href="../admin/transport/routes.php" class="action-card">
                            <div class="action-icon green"><i class="fas fa-map-marked-alt"></i></div>
                            <h4>Routes</h4>
                            <p>Plan & optimize</p>
                        </a>

                        <a href="../admin/transport/gps-tracking.php" class="action-card">
                            <div class="action-icon purple"><i class="fas fa-satellite-dish"></i></div>
                            <h4>Live GPS</h4>
                            <p>Track in real-time</p>
                        </a>

                        <a href="../admin/transport/maintenance.php" class="action-card">
                            <div class="action-icon orange"><i class="fas fa-wrench"></i></div>
                            <h4>Maintenance</h4>
                            <p>Schedule & history</p>
                        </a>
                    </div>
                </section>

                <!-- Today's Active Trips -->
                <?php if (!empty($today_trips)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-route"></i> Active Trips Today</h3>
                        <div class="cyber-table-container">
                            <table class="cyber-table">
                                <thead>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Type</th>
                                        <th>Route</th>
                                        <th>Driver</th>
                                        <th>Students</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($today_trips as $trip): ?>
                                        <tr>
                                            <td><span class="cyber-badge cyan"><?php echo htmlspecialchars($trip['vehicle_number']); ?></span></td>
                                            <td><?php echo htmlspecialchars($trip['vehicle_type']); ?></td>
                                            <td><?php echo htmlspecialchars($trip['route_name'] ?? 'Unassigned'); ?></td>
                                            <td><?php echo htmlspecialchars($trip['driver_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo $trip['student_count']; ?></td>
                                            <td><span class="cyber-badge green">Active</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Upcoming Maintenance -->
                <?php if (!empty($upcoming_maintenance)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-calendar-check"></i> Upcoming Maintenance</h3>
                        <div style="display:grid;gap:12px;">
                            <?php foreach ($upcoming_maintenance as $maint): ?>
                                <div style="background:rgba(255,152,0,0.1);border-left:3px solid var(--orange-glow);padding:15px;border-radius:8px;display:flex;justify-content:space-between;align-items:center;">
                                    <div>
                                        <h4 style="color:var(--orange-glow);margin:0 0 5px 0;"><?php echo htmlspecialchars($maint['vehicle_number']); ?></h4>
                                        <div style="color:var(--text-muted);font-size:0.9rem;">
                                            <?php echo ucfirst($maint['maintenance_type']); ?> - <?php echo date('M d, Y', strtotime($maint['scheduled_date'])); ?>
                                        </div>
                                    </div>
                                    <span class="cyber-badge orange">Scheduled</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>
