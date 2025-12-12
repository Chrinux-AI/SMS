<?php

/**
 * Hostel Rooms Management - Admin Panel
 * Manage hostel rooms and allocations
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Hostel Rooms Management";
$current_page = "hostel/rooms.php";

// Fetch hostels and rooms
$hostels = db()->fetchAll("SELECT * FROM hostels WHERE is_active = 1");
$rooms = db()->fetchAll("SELECT hr.*, h.hostel_name FROM hostel_rooms hr JOIN hostels h ON hr.hostel_id = h.id ORDER BY h.hostel_name, hr.room_number");

$total_rooms = count($rooms);
$occupied_rooms = count(array_filter($rooms, fn($r) => $r['status'] == 'occupied'));
$available_rooms = count(array_filter($rooms, fn($r) => $r['status'] == 'available'));

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
            <h1><i class="fas fa-bed"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Hostel</span>
                <span>/</span>
                <span>Rooms</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-door-open"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_rooms; ?></div>
                    <div class="stat-label">Total Rooms</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $available_rooms; ?></div>
                    <div class="stat-label">Available</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $occupied_rooms; ?></div>
                    <div class="stat-label">Occupied</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add New Room
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Rooms</h3>
                <div class="card-actions">
                    <select id="filterHostel" class="filter-select">
                        <option value="">All Hostels</option>
                        <?php foreach ($hostels as $hostel): ?>
                            <option value="<?php echo $hostel['id']; ?>"><?php echo htmlspecialchars($hostel['hostel_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Hostel</th>
                                <th>Room No.</th>
                                <th>Floor</th>
                                <th>Type</th>
                                <th>Capacity</th>
                                <th>Occupancy</th>
                                <th>Rent (₹)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($room['hostel_name']); ?></td>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($room['room_number']); ?></span></td>
                                    <td>Floor <?php echo $room['floor_number']; ?></td>
                                    <td><?php echo ucfirst($room['room_type']); ?></td>
                                    <td><?php echo $room['capacity']; ?></td>
                                    <td><?php echo $room['current_occupancy']; ?> / <?php echo $room['capacity']; ?></td>
                                    <td>₹<?php echo number_format($room['rent_amount'], 2); ?></td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'available' => 'success',
                                            'occupied' => 'warning',
                                            'maintenance' => 'danger',
                                            'reserved' => 'info'
                                        ];
                                        ?>
                                        <span class="badge badge-<?php echo $status_class[$room['status']]; ?>">
                                            <?php echo ucfirst($room['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-icon btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
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

    <script>
        function openAddModal() {
            alert('Add Room Modal - Implementation pending');
        }
    </script>
</body>

</html>
