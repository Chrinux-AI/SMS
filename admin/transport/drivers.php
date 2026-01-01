<?php
/**
 * Drivers Management - Admin Panel
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Drivers Management";
$current_page = "transport/drivers.php";

// Sample drivers data
$drivers = [];

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
            <h1><i class="fas fa-id-badge"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Transport</span>
                <span>/</span>
                <span>Drivers</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-details">
                    <div class="stat-value">0</div>
                    <div class="stat-label">Total Drivers</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value">0</div>
                    <div class="stat-label">On Duty</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-bus"></i></div>
                <div class="stat-details">
                    <div class="stat-value">0</div>
                    <div class="stat-label">Assigned</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="openAddDriverModal()">
                <i class="fas fa-plus"></i> Add Driver
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Drivers</h3>
            </div>
            <div class="card-body">
                <div class="empty-state" style="text-align: center; padding: 40px;">
                    <i class="fas fa-user-plus" style="font-size: 4rem; opacity: 0.3; margin-bottom: 20px;"></i>
                    <h3>No Drivers Yet</h3>
                    <p>Add drivers to manage your transport fleet</p>
                    <button class="btn btn-primary" onclick="openAddDriverModal()">
                        <i class="fas fa-plus"></i> Add First Driver
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/chatbot-unified.php'; ?>

    <script>
        function openAddDriverModal() {
            alert('Add driver functionality coming soon!');
        }
    </script>
</body>
</html>
