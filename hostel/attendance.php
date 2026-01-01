<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';
require_role('hostel');
$page_title = "Attendance ";
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
        <div class="page-header"><h1><i class="fas fa-bed"></i> <?php echo $page_title; ?></h1></div>
        <div class="cyber-card">
            <div class="card-body" style="text-align: center; padding: 40px;">
                <i class="fas fa-bed" style="font-size: 4rem; opacity: 0.3;"></i>
                <h3>Attendance </h3><p>Hostel module - This feature is under development.</p>
            </div>
        </div>
    </div>
    <?php include '../includes/chatbot-unified.php'; ?>
</body>
</html>