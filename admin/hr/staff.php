<?php

/**
 * Staff Management - Admin Panel
 * Manage non-teaching staff
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Staff Management";
$current_page = "hr/staff.php";

// Fetch departments and staff
$departments = db()->fetchAll("SELECT * FROM departments WHERE is_active = 1");
$staff = db()->fetchAll("SELECT s.*, d.department_name, u.first_name, u.last_name, u.email FROM staff s JOIN departments d ON s.department_id = d.id JOIN users u ON s.user_id = u.id WHERE s.is_active = 1 ORDER BY d.department_name, u.last_name");

$total_staff = count($staff);
$permanent_staff = count(array_filter($staff, fn($s) => $s['employment_type'] == 'permanent'));

include '../../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
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
    <link rel="stylesheet" href="../../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-users"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>HR & Payroll</span>
                <span>/</span>
                <span>Staff</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_staff; ?></div>
                    <div class="stat-label">Total Staff</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $permanent_staff; ?></div>
                    <div class="stat-label">Permanent Staff</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-building"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count($departments); ?></div>
                    <div class="stat-label">Departments</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add New Staff
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Staff Members</h3>
                <div class="card-actions">
                    <select id="filterDepartment" class="filter-select">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['department_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Employment Type</th>
                                <th>Joining Date</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff as $member): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($member['employee_id']); ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($member['department_name']); ?></td>
                                    <td><?php echo htmlspecialchars($member['designation']); ?></td>
                                    <td><span class="badge badge-<?php echo $member['employment_type'] == 'permanent' ? 'success' : 'info'; ?>">
                                            <?php echo ucfirst($member['employment_type']); ?>
                                        </span></td>
                                    <td><?php echo date('M d, Y', strtotime($member['date_of_joining'])); ?></td>
                                    <td><?php echo htmlspecialchars($member['contact_number']); ?></td>
                                    <td>
                                        <button class="btn-icon btn-view" title="View Profile">
                                            <i class="fas fa-user"></i>
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
            alert('Add Staff Modal - Implementation pending');
        }
    </script>
</body>

</html>
