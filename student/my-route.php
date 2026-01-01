<?php

/**
 * Student My Route - Student Panel
 * View assigned transport route
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_role('student');

$page_title = "My Transport Route";
$current_page = "my-route.php";

$student_id = $_SESSION['user_id'];

// Fetch student's transport details
$transport = db()->fetch("
    SELECT st.*, tr.route_name, tr.route_number, tr.starting_point, tr.ending_point,
           tv.vehicle_number, tv.vehicle_type, tv.model,
           CONCAT(td.driver_name) as driver_name, td.contact_number as driver_contact
    FROM student_transport st
    JOIN transport_routes tr ON st.route_id = tr.id
    LEFT JOIN transport_assignments ta ON tr.id = ta.route_id
    LEFT JOIN transport_vehicles tv ON ta.vehicle_id = tv.id
    LEFT JOIN transport_drivers td ON ta.driver_id = td.id
    JOIN students s ON st.student_id = s.id
    WHERE s.user_id = ? AND st.is_active = 1
", [$student_id]);

include '../includes/cyber-nav.php';
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
    <link rel="stylesheet" href="../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-route"></i> <?php echo $page_title; ?></h1>
        </div>

        <?php if ($transport): ?>
            <div class="cyber-card">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle"></i> Route Information</h3>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Route Number</div>
                            <div class="info-value"><span class="badge badge-primary"><?php echo htmlspecialchars($transport['route_number']); ?></span></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Route Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($transport['route_name']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Starting Point</div>
                            <div class="info-value"><?php echo htmlspecialchars($transport['starting_point']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Ending Point</div>
                            <div class="info-value"><?php echo htmlspecialchars($transport['ending_point']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">My Pickup Point</div>
                            <div class="info-value"><strong><?php echo htmlspecialchars($transport['pickup_point']); ?></strong></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Monthly Fee</div>
                            <div class="info-value"><strong>₹<?php echo number_format($transport['monthly_fee'], 2); ?></strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($transport['vehicle_number']): ?>
                <div class="cyber-card">
                    <div class="card-header">
                        <h3><i class="fas fa-bus"></i> Vehicle & Driver Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Vehicle Number</div>
                                <div class="info-value"><span class="badge badge-success"><?php echo htmlspecialchars($transport['vehicle_number']); ?></span></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Vehicle Type</div>
                                <div class="info-value"><?php echo ucfirst($transport['vehicle_type']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Model</div>
                                <div class="info-value"><?php echo htmlspecialchars($transport['model']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Driver Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($transport['driver_name']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Driver Contact</div>
                                <div class="info-value"><a href="tel:<?php echo htmlspecialchars($transport['driver_contact']); ?>"><?php echo htmlspecialchars($transport['driver_contact']); ?></a></div>
                            </div>
                        </div>

                        <div class="page-actions">
                            <a href="track-bus.php?vehicle=<?php echo $transport['vehicle_number']; ?>" class="btn btn-primary">
                                <i class="fas fa-map-marked-alt"></i> Track My Bus Live
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="cyber-card">
                <div class="card-body">
                    <div class="empty-state">
                        <i class="fas fa-bus fa-3x"></i>
                        <p>No transport route assigned</p>
                        <p class="text-secondary">Contact administration to request transport facility</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            background: var(--glass-bg);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--glass-border);
        }

        .info-label {
            font-size: 0.85em;
            color: var(--text-secondary);
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1.1em;
            color: var(--text-primary);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state i {
            color: var(--neon-cyan);
            margin-bottom: 20px;
        }
    </style>
</body>

</html>
