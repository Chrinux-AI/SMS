<?php
/**
 * Hostels Management - Admin Panel
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Hostels Management";
$current_page = "hostel/hostels.php";

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
            <h1><i class="fas fa-building"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Hostel</span>
                <span>/</span>
                <span>Hostels</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-building"></i></div>
                <div class="stat-details">
                    <div class="stat-value">2</div>
                    <div class="stat-label">Total Hostels</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-door-open"></i></div>
                <div class="stat-details">
                    <div class="stat-value">50</div>
                    <div class="stat-label">Total Rooms</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-bed"></i></div>
                <div class="stat-details">
                    <div class="stat-value">200</div>
                    <div class="stat-label">Total Beds</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="openAddHostelModal()">
                <i class="fas fa-plus"></i> Add Hostel
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Hostels</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Hostel Name</th>
                                <th>Type</th>
                                <th>Capacity</th>
                                <th>Occupied</th>
                                <th>Warden</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Boys Hostel A</td>
                                <td><span class="badge badge-info">Boys</span></td>
                                <td>100</td>
                                <td>85</td>
                                <td>Mr. Johnson</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="btn-icon btn-view" title="View"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit" title="Edit"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>Girls Hostel A</td>
                                <td><span class="badge badge-pink">Girls</span></td>
                                <td>100</td>
                                <td>90</td>
                                <td>Mrs. Williams</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="btn-icon btn-view" title="View"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit" title="Edit"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/chatbot-unified.php'; ?>

    <script>
        function openAddHostelModal() {
            alert('Add hostel functionality coming soon!');
        }
    </script>
</body>
</html>
