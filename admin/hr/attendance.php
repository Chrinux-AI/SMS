<?php
/**
 * Staff Attendance - Admin Panel
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Staff Attendance";
$current_page = "hr/attendance.php";

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
            <h1><i class="fas fa-clock"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>HR & Payroll</span>
                <span>/</span>
                <span>Staff Attendance</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value">42</div>
                    <div class="stat-label">Present Today</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value">3</div>
                    <div class="stat-label">Absent</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-times"></i></div>
                <div class="stat-details">
                    <div class="stat-value">5</div>
                    <div class="stat-label">On Leave</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="markAttendance()">
                <i class="fas fa-check"></i> Mark Attendance
            </button>
            <input type="date" id="attendanceDate" class="form-control" value="<?php echo date('Y-m-d'); ?>" style="width: auto; display: inline-block;">
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Today's Attendance - <?php echo date('F d, Y'); ?></h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Staff Name</th>
                                <th>Role</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Working Hours</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Mr. Johnson</td>
                                <td>Teacher</td>
                                <td>8:00 AM</td>
                                <td>4:00 PM</td>
                                <td>8 hrs</td>
                                <td><span class="badge badge-success">Present</span></td>
                            </tr>
                            <tr>
                                <td>Mrs. Williams</td>
                                <td>Teacher</td>
                                <td>8:15 AM</td>
                                <td>-</td>
                                <td>-</td>
                                <td><span class="badge badge-warning">Late</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/chatbot-unified.php'; ?>

    <script>
        function markAttendance() { alert('Mark attendance coming soon!'); }
    </script>
</body>
</html>
