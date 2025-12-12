<?php

/**
 * Hostel Allocation Management - Admin Panel
 * Manage hostel room allocations
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Hostel Allocations";
$current_page = "hostel/allocations.php";

// Fetch allocations
$allocations = db()->fetchAll("
    SELECT ha.*, h.hostel_name, hr.room_number,
           CONCAT(u.first_name, ' ', u.last_name) as student_name
    FROM hostel_allocations ha
    JOIN hostels h ON ha.hostel_id = h.id
    JOIN hostel_rooms hr ON ha.room_id = hr.id
    JOIN students s ON ha.student_id = s.id
    JOIN users u ON s.user_id = u.id
    WHERE ha.is_active = 1
    ORDER BY h.hostel_name, hr.room_number
");

include '../../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <link rel="stylesheet" href="../../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user-plus"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Hostel</span>
                <span>/</span>
                <span>Allocations</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count($allocations); ?></div>
                    <div class="stat-label">Total Allocations</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary">
                <i class="fas fa-plus"></i> Allocate Room
            </button>
            <button class="btn btn-info">
                <i class="fas fa-exchange-alt"></i> Transfer Student
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Current Allocations</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Hostel</th>
                                <th>Room Number</th>
                                <th>Allocation Date</th>
                                <th>Academic Year</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allocations as $allocation): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($allocation['student_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($allocation['hostel_name']); ?></td>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($allocation['room_number']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($allocation['allocation_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($allocation['academic_year']); ?></td>
                                    <td>
                                        <button class="btn-icon btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon btn-warning" title="Transfer">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                        <button class="btn-icon btn-danger" title="Deallocate">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
