<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';


require_role('admin'); // TEST_MODE will allow access for testing

// Fetch All Employees
$employees = db()->fetchAll("
    SELECT u.*,
           CASE
               WHEN u.role = 'teacher' THEN 'Teaching Staff'
               WHEN u.role IN ('admin', 'superadmin', 'principal') THEN 'Administrative'
               ELSE 'Support Staff'
           END as department,
           CASE WHEN u.is_active = 1 THEN 'Active' ELSE 'Inactive' END as employment_status
    FROM users u
    WHERE u.role IN ('teacher', 'admin', 'librarian', 'accountant', 'nurse', 'counselor', 'transport_manager', 'hostel_warden', 'canteen_manager')
    ORDER BY u.created_at DESC
");

// Stats
$total_employees = count($employees);
$active_employees = count(array_filter($employees, fn($e) => $e['is_active'] == 1));
$on_leave_today = db()->fetchOne("
    SELECT COUNT(*) as count
    FROM leave_requests
    WHERE status = 'approved' AND CURDATE() BETWEEN start_date AND end_date
")['count'] ?? 0;

$page_title = 'HR & Employee Management';
$page_icon = 'users-cog';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../../assets/css/cyberpunk-ui.css" rel="stylesheet">
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb cyan"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <button class="cyber-btn green" onclick="window.location.href='add-employee.php'">
                        <i class="fas fa-user-plus"></i> Add Employee
                    </button>
                </div>
            </header>

            <div class="cyber-content slide-in">

                <section class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
                    <div class="stat-orb">
                        <div class="stat-icon cyan"><i class="fas fa-users"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $total_employees; ?></div>
                            <div class="stat-label">Total Employees</div>
                        </div>
                    </div>
                    <div class="stat-orb">
                        <div class="stat-icon green"><i class="fas fa-user-check"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $active_employees; ?></div>
                            <div class="stat-label">Active</div>
                        </div>
                    </div>
                    <div class="stat-orb">
                        <div class="stat-icon orange"><i class="fas fa-calendar-times"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $on_leave_today; ?></div>
                            <div class="stat-label">On Leave Today</div>
                        </div>
                    </div>
                </section>

                <section class="holo-card" style="margin-top:30px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <h3><i class="fas fa-list"></i> All Employees</h3>
                        <div style="display:flex;gap:10px;">
                            <a href="payroll.php" class="cyber-btn purple"><i class="fas fa-money-bill-wave"></i> Payroll</a>
                            <a href="leave-requests.php" class="cyber-btn orange"><i class="fas fa-calendar-alt"></i> Leave Requests</a>
                            <a href="attendance-report.php" class="cyber-btn cyan"><i class="fas fa-clipboard-check"></i> Attendance</a>
                        </div>
                    </div>

                    <div class="cyber-table-container">
                        <table class="cyber-table">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employees as $emp): ?>
                                    <tr>
                                        <td><span class="cyber-badge purple">#<?php echo $emp['id']; ?></span></td>
                                        <td><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($emp['email']); ?></td>
                                        <td><span class="cyber-badge cyan"><?php echo ucfirst(str_replace('_', ' ', $emp['role'])); ?></span></td>
                                        <td><?php echo htmlspecialchars($emp['department']); ?></td>
                                        <td>
                                            <span class="cyber-badge <?php echo $emp['is_active'] ? 'green' : 'red'; ?>">
                                                <?php echo $emp['employment_status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="cyber-btn small cyan" onclick="window.location.href='view-employee.php?id=<?php echo $emp['id']; ?>'">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="cyber-btn small orange" onclick="window.location.href='edit-employee.php?id=<?php echo $emp['id']; ?>'">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>

</html>
