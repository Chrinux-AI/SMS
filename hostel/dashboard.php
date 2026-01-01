<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for TEST_MODE support
require_role('hostel', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Hostel Stats
$total_rooms = db()->fetchOne("SELECT COUNT(*) as count FROM hostel_rooms")['count'] ?? 0;
$occupied_rooms = db()->fetchOne("SELECT COUNT(DISTINCT room_id) as count FROM hostel_allocations WHERE is_active = 1")['count'] ?? 0;
$total_students = db()->fetchOne("SELECT COUNT(*) as count FROM hostel_allocations WHERE is_active = 1")['count'] ?? 0;
$available_beds = db()->fetchOne("
    SELECT SUM(hr.capacity) - COUNT(ha.id) as available
    FROM hostel_rooms hr
    LEFT JOIN hostel_allocations ha ON hr.id = ha.room_id AND ha.is_active = 1
")['available'] ?? 0;

// Today's Mess Attendance
$mess_attendance = db()->fetchOne("
    SELECT COUNT(*) as count
    FROM mess_attendance
    WHERE DATE(attendance_time) = CURDATE()
")['count'] ?? 0;

// Pending Requests
$pending_requests = db()->fetchOne("
    SELECT COUNT(*) as count
    FROM hostel_leave_requests
    WHERE status = 'pending'
")['count'] ?? 0;

// Recent Allocations
$recent_allocations = db()->fetchAll("
    SELECT ha.*,
           CONCAT(u.first_name, ' ', u.last_name) as student_name,
           hr.room_number, hr.block_name
    FROM hostel_allocations ha
    JOIN students st ON ha.student_id = st.id
    JOIN users u ON st.user_id = u.id
    JOIN hostel_rooms hr ON ha.room_id = hr.id
    WHERE ha.is_active = 1
    ORDER BY ha.allocated_on DESC
    LIMIT 10
");

// Monthly Expenses
$monthly_expenses = db()->fetchOne("
    SELECT IFNULL(SUM(amount), 0) as total
    FROM hostel_expenses
    WHERE MONTH(expense_date) = MONTH(CURDATE())
")['total'] ?? 0;

$page_title = 'Hostel Management';
$page_icon = 'bed';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/cyberpunk-ui.css" rel="stylesheet">
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb purple"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <button class="cyber-btn cyan" onclick="window.location.href='../admin/hostel/allocate.php'">
                        <i class="fas fa-plus"></i> Allocate Room
                    </button>
                    <div class="user-card" style="padding:8px 15px;">
                        <div class="user-avatar purple" style="width:35px;height:35px;">
                            <?php echo strtoupper(substr($full_name, 0, 2)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($full_name); ?></div>
                            <div class="user-role">Hostel Warden</div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="cyber-content slide-in">

                <section class="orb-grid">
                    <div class="stat-orb">
                        <div class="stat-icon cyan"><i class="fas fa-door-open"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $total_rooms; ?></div>
                            <div class="stat-label">Total Rooms</div>
                            <div class="stat-trend"><i class="fas fa-building"></i> Capacity</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon green"><i class="fas fa-bed"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $occupied_rooms; ?></div>
                            <div class="stat-label">Occupied</div>
                            <div class="stat-trend up"><i class="fas fa-check"></i> Active</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon orange"><i class="fas fa-users"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $total_students; ?></div>
                            <div class="stat-label">Hostel Students</div>
                            <div class="stat-trend"><i class="fas fa-user-graduate"></i> Residents</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon purple"><i class="fas fa-door-closed"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $available_beds; ?></div>
                            <div class="stat-label">Available Beds</div>
                            <div class="stat-trend"><i class="fas fa-plus-circle"></i> Open</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon golden"><i class="fas fa-utensils"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $mess_attendance; ?></div>
                            <div class="stat-label">Mess Today</div>
                            <div class="stat-trend"><i class="fas fa-drumstick-bite"></i> Meals</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon red"><i class="fas fa-exclamation-circle"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $pending_requests; ?></div>
                            <div class="stat-label">Pending Requests</div>
                            <div class="stat-trend"><i class="fas fa-clock"></i> Review</div>
                        </div>
                    </div>
                </section>

                <!-- Quick Actions -->
                <section class="holo-card" style="margin-top:30px;">
                    <h3 style="margin-bottom:20px;"><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
                        <a href="../admin/hostel/allocations.php" class="action-card">
                            <div class="action-icon cyan"><i class="fas fa-bed"></i></div>
                            <h4>Room Allocations</h4>
                            <p>Manage student rooms</p>
                        </a>

                        <a href="../admin/hostel/mess-attendance.php" class="action-card">
                            <div class="action-icon green"><i class="fas fa-clipboard-check"></i></div>
                            <h4>Mess Attendance</h4>
                            <p>Biometric meal tracking</p>
                        </a>

                        <a href="../admin/hostel/leave-requests.php" class="action-card">
                            <div class="action-icon orange"><i class="fas fa-calendar-minus"></i></div>
                            <h4>Leave Requests</h4>
                            <p>Approve/deny outings</p>
                        </a>

                        <a href="../admin/hostel/complaints.php" class="action-card">
                            <div class="action-icon purple"><i class="fas fa-comment-dots"></i></div>
                            <h4>Complaints</h4>
                            <p>Facility issues</p>
                        </a>
                    </div>
                </section>

                <!-- Recent Room Allocations -->
                <?php if (!empty($recent_allocations)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-bed"></i> Recent Room Allocations</h3>
                        <div class="cyber-table-container">
                            <table class="cyber-table">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Room Number</th>
                                        <th>Block</th>
                                        <th>Allocated On</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_allocations as $allocation): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($allocation['student_name']); ?></td>
                                            <td><span class="cyber-badge cyan"><?php echo htmlspecialchars($allocation['room_number']); ?></span></td>
                                            <td><?php echo htmlspecialchars($allocation['block_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($allocation['allocated_on'])); ?></td>
                                            <td><span class="cyber-badge green">Active</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>
