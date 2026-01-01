<?php
/**
 * Department Management - Admin Panel
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Department Management";
$current_page = "hr/departments.php";

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
            <h1><i class="fas fa-sitemap"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>HR & Payroll</span>
                <span>/</span>
                <span>Departments</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-sitemap"></i></div>
                <div class="stat-details">
                    <div class="stat-value">8</div>
                    <div class="stat-label">Total Departments</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-details">
                    <div class="stat-value">45</div>
                    <div class="stat-label">Total Staff</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="addDepartment()">
                <i class="fas fa-plus"></i> Add Department
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Departments</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Head</th>
                                <th>Staff Count</th>
                                <th>Budget</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Administration</td>
                                <td>Dr. Smith</td>
                                <td>5</td>
                                <td>₦500,000</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="btn-icon btn-view"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>Academics</td>
                                <td>Prof. Johnson</td>
                                <td>20</td>
                                <td>₦1,500,000</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="btn-icon btn-view"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>Finance</td>
                                <td>Mr. Williams</td>
                                <td>4</td>
                                <td>₦300,000</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="btn-icon btn-view"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>IT Support</td>
                                <td>Mr. Brown</td>
                                <td>3</td>
                                <td>₦400,000</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <button class="btn-icon btn-view"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-edit"><i class="fas fa-edit"></i></button>
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
        function addDepartment() {
            alert('Add department functionality coming soon!');
        }
    </script>
</body>
</html>
