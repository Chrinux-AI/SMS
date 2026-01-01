<?php
/**
 * Dashboard - Owner Portal
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_role('owner');

$page_title = "Owner Dashboard";
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'] ?? 'User';

include '../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <?php include '../includes/head-meta.php'; ?>
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-tachometer-alt"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <span>Dashboard</span>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-hand-wave"></i> Welcome, <?php echo htmlspecialchars($user_name); ?>!</h3>
            </div>
            <div class="card-body">
                <p>Welcome to your Owner dashboard. This portal is being enhanced with new features.</p>
                <div style="margin-top: 20px;">
                    <a href="settings.php" class="btn btn-primary">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/chatbot-unified.php'; ?>
</body>
</html>