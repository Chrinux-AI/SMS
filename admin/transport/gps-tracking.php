<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_login('../login.php');
require_role('admin');

$page_title = 'GPS Tracking';
$current_page = basename(__FILE__);
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
    <link rel="icon" href="../assets/images/icons/favicon-32x32.png">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="cyber-bg">
    <?php include '../includes/cyber-nav.php'; ?>

    <div class="cyber-main">
        <div class="page-header">
            <h1><i class="fas fa-map-marked-alt"></i> <?php echo $page_title; ?></h1>
            <p class="subtitle">Manage and view GPS Tracking</p>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3>GPS Tracking Dashboard</h3>
            </div>
            <div class="card-body">
                <p>This page is under construction. Features coming soon...</p>

                <!-- Add your content here -->
                <div class="empty-state">
                    <i class="fas fa-map-marked-alt" style="font-size: 4rem; color: var(--cyber-primary); margin-bottom: 1rem;"></i>
                    <h3>Getting Started</h3>
                    <p>Configure your GPS Tracking settings and start managing data.</p>
                    <button class="cyber-btn" onclick="window.location.href='dashboard.php'">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>