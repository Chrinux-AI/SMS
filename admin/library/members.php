<?php
/**
 * Library Members Management - Admin Panel
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Library Members";
$current_page = "library/members.php";

// Fetch library members
try {
    $members = db()->fetchAll("SELECT u.id, u.full_name, u.role, u.email, u.created_at
        FROM users u WHERE u.is_active = 1 ORDER BY u.full_name LIMIT 100") ?? [];
} catch (Exception $e) {
    $members = [];
}

$total_members = count($members);

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
            <h1><i class="fas fa-id-card"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Library</span>
                <span>/</span>
                <span>Members</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_members; ?></div>
                    <div class="stat-label">Total Members</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count(array_filter($members, fn($m) => $m['role'] == 'student')); ?></div>
                    <div class="stat-label">Students</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count(array_filter($members, fn($m) => $m['role'] == 'teacher')); ?></div>
                    <div class="stat-label">Teachers</div>
                </div>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Library Members</h3>
                <div class="card-actions">
                    <input type="text" id="searchMembers" placeholder="Search members..." class="search-input">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Books Borrowed</th>
                                <th>Fine Due</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['full_name']); ?></td>
                                    <td><span class="badge badge-info"><?php echo ucfirst($member['role']); ?></span></td>
                                    <td><?php echo htmlspecialchars($member['email']); ?></td>
                                    <td>0</td>
                                    <td>₦0</td>
                                    <td><span class="badge badge-success">Active</span></td>
                                    <td>
                                        <button class="btn-icon btn-view" title="View History"><i class="fas fa-eye"></i></button>
                                        <button class="btn-icon btn-edit" title="Issue Book"><i class="fas fa-book"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/chatbot-unified.php'; ?>
</body>
</html>
