<?php
/**
 * Track Bus - Student Portal
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_role('student');

$page_title = "Track My Bus";
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
            <h1><i class="fas fa-map-marked-alt"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Track Bus</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-bus"></i></div>
                <div class="stat-details">
                    <div class="stat-value">Route 5</div>
                    <div class="stat-label">Your Route</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-details">
                    <div class="stat-value">7:30 AM</div>
                    <div class="stat-label">Pickup Time</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="stat-details">
                    <div class="stat-value">5 min</div>
                    <div class="stat-label">ETA</div>
                </div>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-map"></i> Live Bus Location</h3>
            </div>
            <div class="card-body">
                <div id="busMap" style="height: 400px; background: rgba(0,191,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <div style="text-align: center;">
                        <i class="fas fa-map-marked-alt" style="font-size: 4rem; color: var(--cyber-cyan); opacity: 0.5;"></i>
                        <h3 style="margin-top: 20px;">Live Map Coming Soon</h3>
                        <p style="color: var(--text-muted);">GPS tracking integration with Google Maps will be available here.</p>
                        <button class="btn btn-primary" style="margin-top: 15px;" onclick="refreshLocation()">
                            <i class="fas fa-sync-alt"></i> Refresh Location
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-info-circle"></i> Route Information</h3>
            </div>
            <div class="card-body">
                <table class="cyber-table">
                    <tr>
                        <th>Route Name</th>
                        <td>Route 5 - Eastern District</td>
                    </tr>
                    <tr>
                        <th>Bus Number</th>
                        <td>SMS-BUS-05</td>
                    </tr>
                    <tr>
                        <th>Driver</th>
                        <td>Mr. Emmanuel</td>
                    </tr>
                    <tr>
                        <th>Driver Contact</th>
                        <td>+234 800 000 0000</td>
                    </tr>
                    <tr>
                        <th>Your Stop</th>
                        <td>Main Street Junction</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <?php include '../includes/chatbot-unified.php'; ?>

    <script>
        function refreshLocation() {
            alert('Fetching latest bus location...');
        }
    </script>
</body>
</html>
