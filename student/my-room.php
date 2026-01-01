<?php
/**
 * My Room - Student Portal (Hostel)
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_role('student');

$page_title = "My Room";
$user_id = $_SESSION['user_id'];

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
            <h1><i class="fas fa-bed"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>My Room</span>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-info-circle"></i> Room Information</h3>
            </div>
            <div class="card-body">
                <div class="empty-state" style="text-align: center; padding: 40px;">
                    <i class="fas fa-door-closed" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h3>No Room Assigned</h3>
                    <p>You haven't been allocated a hostel room yet. Contact the hostel warden for room allocation.</p>
                    <a href="complaints.php" class="btn btn-primary" style="margin-top: 15px;">
                        <i class="fas fa-envelope"></i> Request Room Allocation
                    </a>
                </div>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-users"></i> Hostel Quick Links</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                    <a href="mess-menu.php" class="btn btn-secondary">
                        <i class="fas fa-utensils"></i> Mess Menu
                    </a>
                    <a href="complaints.php" class="btn btn-secondary">
                        <i class="fas fa-exclamation-circle"></i> Complaints
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/chatbot-unified.php'; ?>
</body>
</html>
