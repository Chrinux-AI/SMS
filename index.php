<?php

/**
 * Main Entry Point - Redirects to appropriate interface
 */

session_start();

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('modern-index.php');
}

// Get user role and redirect appropriately
$role = $_SESSION['user_role'] ?? 'student';

switch ($role) {
    case 'admin':
        redirect('admin/dashboard.php');
        break;
    case 'teacher':
        redirect('admin/dashboard.php'); // Teachers also use admin panel
        break;
    case 'student':
        redirect('student/dashboard.php');
        break;
    default:
        redirect('modern-index.php');
        break;
}

// This should never be reached due to redirects above
exit;
?>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
    }

    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f1f5f9;
    }

    .welcome {
        color: #1e293b;
        font-size: 2.5rem;
        margin-bottom: 10px;
    }

    .role-badge {
        display: inline-block;
        padding: 8px 20px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-radius: 25px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }

    .dashboard-card {
        background: #f8fafc;
        padding: 25px;
        border-radius: 12px;
        border-left: 4px solid #3b82f6;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .card-title {
        font-size: 1.5rem;
        color: #1e293b;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-description {
        color: #64748b;
        margin-bottom: 20px;
    }

    .btn {
        display: inline-block;
        padding: 12px 25px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
    }

    .logout-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #ef4444;
        padding: 10px 20px;
        border-radius: 8px;
        color: white;
        text-decoration: none;
    }
</style>
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>
<a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>

    <div class="dashboard-container">
        <div class="header">
            <h1 class="welcome">Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
            <div class="role-badge">
                <i class="fas fa-<?php echo $role === 'admin' ? 'crown' : ($role === 'teacher' ? 'chalkboard-teacher' : 'user-graduate'); ?>"></i>
                <?php echo ucfirst($role); ?>
            </div>
        </div>

        <div class="dashboard-grid">
            <?php if ($role === 'admin'): ?>
                <div class="dashboard-card" onclick="window.location.href='admin/dashboard.php'">
                    <h3 class="card-title"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h3>
                    <p class="card-description">Manage system settings, users, and view analytics</p>
                    <a href="admin/dashboard.php" class="btn">Access Admin Panel</a>
                </div>

                <div class="dashboard-card" onclick="window.location.href='admin/users.php'">
                    <h3 class="card-title"><i class="fas fa-users-cog"></i> User Management</h3>
                    <p class="card-description">Manage user accounts, roles, and permissions</p>
                    <a href="admin/users.php" class="btn">Manage Users</a>
                </div>

                <div class="dashboard-card" onclick="window.location.href='admin/registrations.php'">
                    <h3 class="card-title"><i class="fas fa-user-plus"></i> Registration Requests</h3>
                    <p class="card-description">Review and approve new user registrations</p>
                    <a href="admin/registrations.php" class="btn">Review Requests</a>
                </div>
            <?php elseif ($role === 'teacher'): ?>
                <div class="dashboard-card" onclick="window.location.href='admin/dashboard.php'">
                    <h3 class="card-title"><i class="fas fa-chalkboard-teacher"></i> Teacher Dashboard</h3>
                    <p class="card-description">Manage your classes and student attendance</p>
                    <a href="admin/dashboard.php" class="btn">Access Dashboard</a>
                </div>

                <div class="dashboard-card" onclick="window.location.href='admin/attendance.php'">
                    <h3 class="card-title"><i class="fas fa-clipboard-check"></i> Attendance Management</h3>
                    <p class="card-description">Mark attendance and view reports</p>
                    <a href="admin/attendance.php" class="btn">Manage Attendance</a>
                </div>
            <?php else: ?>
                <div class="dashboard-card" onclick="window.location.href='student/checkin.php'">
                    <h3 class="card-title"><i class="fas fa-clock"></i> Check In</h3>
                    <p class="card-description">Mark your attendance for today's classes</p>
                    <a href="student/checkin.php" class="btn">Check In Now</a>
                </div>

                <div class="dashboard-card" onclick="window.location.href='student/reports.php'">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> My Attendance</h3>
                    <p class="card-description">View your attendance history and reports</p>
                    <a href="student/reports.php" class="btn">View Reports</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body>

</html>