<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for TEST_MODE support
require_role('principal', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// School Overview Stats
$total_students = db()->fetchOne("SELECT COUNT(*) as count FROM students WHERE is_active = 1")['count'] ?? 0;
$total_teachers = db()->fetchOne("SELECT COUNT(*) as count FROM teachers WHERE is_active = 1")['count'] ?? 0;
$total_classes = db()->fetchOne("SELECT COUNT(*) as count FROM classes WHERE academic_year = '2024-2025'")['count'] ?? 0;

// Today's Attendance
$today_attendance = db()->fetchOne("
    SELECT
        COUNT(*) as total_marked,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
        ROUND((SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as attendance_rate
    FROM attendance
    WHERE date = CURDATE()
")  ?? ['total_marked' => 0, 'present_count' => 0, 'attendance_rate' => 0];

// Upcoming Events
$upcoming_events = db()->fetchAll("
    SELECT * FROM events
    WHERE event_date >= CURDATE() AND status = 'active'
    ORDER BY event_date
    LIMIT 5
");

// Pending Approvals
$pending_leaves = db()->fetchOne("SELECT COUNT(*) as count FROM leave_requests WHERE status = 'pending'")['count'] ?? 0;
$pending_admissions = db()->fetchOne("SELECT COUNT(*) as count FROM admission_applications WHERE status = 'pending'")['count'] ?? 0;

// Fee Collection Status
$fee_collection = db()->fetchOne("
    SELECT
        IFNULL(SUM(fii.amount), 0) as total_billed,
        IFNULL(SUM(fp.amount_paid), 0) as total_collected
    FROM fee_invoices fi
    LEFT JOIN fee_invoice_items fii ON fi.id = fii.invoice_id
    LEFT JOIN fee_payments fp ON fi.id = fp.invoice_id
    WHERE fi.academic_year = '2024-2025'
") ?? ['total_billed' => 0, 'total_collected' => 0];

$collection_rate = $fee_collection['total_billed'] > 0
    ? round(($fee_collection['total_collected'] / $fee_collection['total_billed']) * 100, 1)
    : 0;

// Recent Activities
$recent_activities = db()->fetchAll("
    SELECT 'admission' as type, CONCAT(first_name, ' ', last_name) as description, created_at
    FROM admission_applications
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    UNION ALL
    SELECT 'payment' as type, CONCAT('Fee payment: ₹', amount_paid) as description, payment_date as created_at
    FROM fee_payments
    WHERE payment_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY created_at DESC
    LIMIT 10
");

$page_title = 'Principal Dashboard';
$page_icon = 'user-tie';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
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
                    <button class="cyber-btn cyan" onclick="window.location.href='../admin/events/create.php'">
                        <i class="fas fa-plus"></i> New Event
                    </button>
                    <div class="user-card" style="padding:8px 15px;">
                        <div class="user-avatar purple" style="width:35px;height:35px;">
                            <?php echo strtoupper(substr($full_name, 0, 2)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($full_name); ?></div>
                            <div class="user-role">Principal</div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="cyber-content slide-in">

                <!-- Key Metrics -->
                <section class="orb-grid">
                    <div class="stat-orb">
                        <div class="stat-icon cyan"><i class="fas fa-user-graduate"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($total_students); ?></div>
                            <div class="stat-label">Total Students</div>
                            <div class="stat-trend up"><i class="fas fa-users"></i> Active</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon green"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $total_teachers; ?></div>
                            <div class="stat-label">Teaching Staff</div>
                            <div class="stat-trend up"><i class="fas fa-check"></i> Active</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon orange"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $today_attendance['attendance_rate']; ?>%</div>
                            <div class="stat-label">Today's Attendance</div>
                            <div class="stat-trend <?php echo $today_attendance['attendance_rate'] >= 85 ? 'up' : 'down'; ?>">
                                <i class="fas fa-calendar-check"></i> <?php echo $today_attendance['present_count']; ?> present
                            </div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon purple"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $collection_rate; ?>%</div>
                            <div class="stat-label">Fee Collection</div>
                            <div class="stat-trend <?php echo $collection_rate >= 75 ? 'up' : 'down'; ?>">
                                <i class="fas fa-rupee-sign"></i> ₹<?php echo number_format($fee_collection['total_collected'] / 100000, 1); ?>L
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Pending Actions -->
                <?php if ($pending_leaves > 0 || $pending_admissions > 0): ?>
                    <section class="holo-card" style="margin-top:30px;background:linear-gradient(135deg,rgba(255,193,7,0.1),rgba(255,152,0,0.1));border-left:4px solid var(--golden-pulse);">
                        <h3 style="color:var(--golden-pulse);margin-bottom:15px;"><i class="fas fa-exclamation-triangle"></i> Pending Approvals</h3>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;">
                            <?php if ($pending_leaves > 0): ?>
                                <a href="../admin/hr/leave-requests.php" style="text-decoration:none;padding:15px;background:rgba(0,0,0,0.2);border-radius:8px;display:flex;align-items:center;gap:12px;">
                                    <i class="fas fa-calendar-times" style="font-size:1.5rem;color:var(--orange-glow);"></i>
                                    <div>
                                        <div style="font-size:1.5rem;color:var(--text-primary);"><?php echo $pending_leaves; ?></div>
                                        <div style="color:var(--text-muted);font-size:0.9rem;">Leave Requests</div>
                                    </div>
                                </a>
                            <?php endif; ?>

                            <?php if ($pending_admissions > 0): ?>
                                <a href="../admin/admissions/pending.php" style="text-decoration:none;padding:15px;background:rgba(0,0,0,0.2);border-radius:8px;display:flex;align-items:center;gap:12px;">
                                    <i class="fas fa-user-plus" style="font-size:1.5rem;color:var(--cyber-cyan);"></i>
                                    <div>
                                        <div style="font-size:1.5rem;color:var(--text-primary);"><?php echo $pending_admissions; ?></div>
                                        <div style="color:var(--text-muted);font-size:0.9rem;">Admission Applications</div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Quick Management Tools -->
                <section class="holo-card" style="margin-top:30px;">
                    <h3 style="margin-bottom:20px;"><i class="fas fa-rocket"></i> Quick Management</h3>
                    <div class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
                        <a href="../admin/academics/timetable.php" class="action-card">
                            <div class="action-icon cyan"><i class="fas fa-calendar-alt"></i></div>
                            <h4>Timetable</h4>
                            <p>Manage class schedules</p>
                        </a>

                        <a href="../admin/academics/exams.php" class="action-card">
                            <div class="action-icon green"><i class="fas fa-file-alt"></i></div>
                            <h4>Examinations</h4>
                            <p>Schedule & results</p>
                        </a>

                        <a href="../admin/events/calendar.php" class="action-card">
                            <div class="action-icon purple"><i class="fas fa-calendar-star"></i></div>
                            <h4>Events</h4>
                            <p>School activities</p>
                        </a>

                        <a href="../admin/discipline/incidents.php" class="action-card">
                            <div class="action-icon orange"><i class="fas fa-gavel"></i></div>
                            <h4>Discipline</h4>
                            <p>Behavior tracking</p>
                        </a>

                        <a href="../admin/reports/principal-overview.php" class="action-card">
                            <div class="action-icon golden"><i class="fas fa-chart-bar"></i></div>
                            <h4>Reports</h4>
                            <p>Analytics & insights</p>
                        </a>

                        <a href="../admin/certificates/generator.php" class="action-card">
                            <div class="action-icon red"><i class="fas fa-certificate"></i></div>
                            <h4>Certificates</h4>
                            <p>Generate documents</p>
                        </a>
                    </div>
                </section>

                <!-- Upcoming Events -->
                <?php if (!empty($upcoming_events)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-calendar-star"></i> Upcoming Events</h3>
                        <div style="display:grid;gap:12px;">
                            <?php foreach ($upcoming_events as $event): ?>
                                <div style="background:rgba(0,191,255,0.05);border-left:3px solid var(--cyber-cyan);padding:15px;border-radius:8px;display:flex;justify-content:space-between;align-items:center;">
                                    <div>
                                        <h4 style="color:var(--cyber-cyan);margin:0 0 5px 0;"><?php echo htmlspecialchars($event['event_name']); ?></h4>
                                        <div style="color:var(--text-muted);font-size:0.9rem;">
                                            <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                            <?php if (!empty($event['location'])): ?>
                                                <span style="margin-left:15px;"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <span class="cyber-badge cyan"><?php echo ucfirst($event['event_type'] ?? 'Event'); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>
