<?php

/**
 * Admin Officer Dashboard - Front Desk & Certificates
 * Verdant SMS v3.0
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for authentication
require_role('admin-officer', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Admin Officer';

// Pending Admissions
$pending_admissions = db()->fetchOne("SELECT COUNT(*) as count FROM admission_applications WHERE status = 'pending'")['count'] ?? 0;

// Today's Visitors
$todays_visitors = db()->fetchOne("SELECT COUNT(*) as count FROM visitor_log WHERE DATE(check_in_time) = CURDATE()")['count'] ?? 0;

// Pending Certificate Requests
$pending_certificates = db()->fetchOne("SELECT COUNT(*) as count FROM certificate_requests WHERE status = 'pending'")['count'] ?? 0;

// Enquiries Today
$enquiries_today = db()->fetchOne("SELECT COUNT(*) as count FROM enquiries WHERE DATE(created_at) = CURDATE()")['count'] ?? 0;

// Recent Admissions
$recent_admissions = db()->fetchAll("
    SELECT * FROM admission_applications
    ORDER BY created_at DESC
    LIMIT 10
");

// Recent Visitor Log
$recent_visitors = db()->fetchAll("
    SELECT * FROM visitor_log
    ORDER BY check_in_time DESC
    LIMIT 10
");

$page_title = 'Admin Officer Dashboard';
$page_icon = 'id-card';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/cyberpunk-ui.css" rel="stylesheet">
    <style>
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg, rgba(20, 20, 30, 0.9));
            border: 1px solid var(--cyber-cyan, #00BFFF);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 191, 255, 0.3);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }

        .stat-icon.cyan {
            color: #00BFFF;
        }

        .stat-icon.green {
            color: #00FF7F;
        }

        .stat-icon.orange {
            color: #FF9F43;
        }

        .stat-icon.purple {
            color: #8A2BE2;
        }

        .stat-value {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .dashboard-section {
            background: var(--card-bg, rgba(20, 20, 30, 0.9));
            border: 1px solid var(--border, rgba(0, 191, 255, 0.2));
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
            color: var(--cyber-cyan, #00BFFF);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .data-table th {
            color: var(--cyber-cyan, #00BFFF);
            font-weight: 600;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-pending {
            background: rgba(255, 159, 67, 0.2);
            color: #FF9F43;
        }

        .badge-approved {
            background: rgba(0, 255, 127, 0.2);
            color: #00FF7F;
        }

        .badge-in {
            background: rgba(0, 191, 255, 0.2);
            color: #00BFFF;
        }

        .two-col {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
        }
    </style>
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb cyan"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <span class="welcome-text">Welcome, <?php echo htmlspecialchars($full_name); ?></span>
                </div>
            </header>

            <!-- Overview Stats -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-user-plus"></i></div>
                    <div class="stat-value"><?php echo $pending_admissions; ?></div>
                    <div class="stat-label">Pending Admissions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon cyan"><i class="fas fa-users"></i></div>
                    <div class="stat-value"><?php echo $todays_visitors; ?></div>
                    <div class="stat-label">Visitors Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-certificate"></i></div>
                    <div class="stat-value"><?php echo $pending_certificates; ?></div>
                    <div class="stat-label">Certificate Requests</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-question-circle"></i></div>
                    <div class="stat-value"><?php echo $enquiries_today; ?></div>
                    <div class="stat-label">Enquiries Today</div>
                </div>
            </div>

            <div class="two-col">
                <!-- Recent Admissions -->
                <div class="dashboard-section">
                    <h2 class="section-title"><i class="fas fa-user-graduate"></i> Recent Admission Applications</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_admissions)): ?>
                                <tr>
                                    <td colspan="3" style="color: rgba(255,255,255,0.5);">No recent applications</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_admissions as $app): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? '')); ?></td>
                                        <td><span class="badge badge-<?php echo strtolower($app['status'] ?? 'pending'); ?>"><?php echo ucfirst($app['status'] ?? 'Pending'); ?></span></td>
                                        <td><?php echo date('M d', strtotime($app['created_at'] ?? 'now')); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Visitor Log -->
                <div class="dashboard-section">
                    <h2 class="section-title"><i class="fas fa-door-open"></i> Today's Visitors</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Visitor</th>
                                <th>Purpose</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_visitors)): ?>
                                <tr>
                                    <td colspan="3" style="color: rgba(255,255,255,0.5);">No visitors today</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_visitors as $visitor): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($visitor['visitor_name'] ?? 'Guest'); ?></td>
                                        <td><?php echo htmlspecialchars($visitor['purpose'] ?? 'N/A'); ?></td>
                                        <td><span class="badge badge-in"><?php echo $visitor['check_out_time'] ? 'Left' : 'In'; ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-section">
                <h2 class="section-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="../admin/registrations.php" class="cyber-btn cyan"><i class="fas fa-user-plus"></i> New Admission</a>
                    <a href="../admin/manage-ids.php" class="cyber-btn green"><i class="fas fa-id-card"></i> Issue ID Card</a>
                    <a href="../admin/reports.php" class="cyber-btn purple"><i class="fas fa-file-alt"></i> Generate Certificate</a>
                    <a href="../admin/communication.php" class="cyber-btn orange"><i class="fas fa-envelope"></i> Send Notice</a>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>