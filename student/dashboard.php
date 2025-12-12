<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for TEST_MODE support
require_role('student');

$student_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Get student full info with class details
$student = db()->fetchOne("
    SELECT s.*, u.first_name, u.last_name, u.email, u.profile_picture,
           c.class_name, c.section, c.grade_level, c.id as class_id
    FROM students s
    JOIN users u ON s.user_id = u.id
    LEFT JOIN class_enrollments ce ON s.id = ce.student_id AND ce.is_active = 1
    LEFT JOIN classes c ON ce.class_id = c.id
    WHERE s.user_id = ?
", [$student_id]) ?? [];

// Attendance Statistics (Last 30 days)
$attendance_stats = db()->fetchOne("
    SELECT
        COUNT(*) as total_days,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days,
        SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_days
    FROM attendance
    JOIN students st ON attendance.student_id = st.id
    WHERE st.user_id = ? AND attendance.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
", [$student_id]) ?? ['total_days' => 0, 'present_days' => 0, 'absent_days' => 0, 'late_days' => 0];

$attendance_rate = $attendance_stats['total_days'] > 0
    ? round(($attendance_stats['present_days'] / $attendance_stats['total_days']) * 100, 1)
    : 100;

// Normalize attendance values for display
$present = isset($attendance_stats['present_days']) ? (int)$attendance_stats['present_days'] : 0;
$late = isset($attendance_stats['late_days']) ? (int)$attendance_stats['late_days'] : 0;
$absent = isset($attendance_stats['absent_days']) ? (int)$attendance_stats['absent_days'] : 0;

// Classes / enrollments for the student (used by "My Classes" card)
$classes = db()->fetchAll(
    "SELECT c.*, ce.room_number, ce.enrolled_on
     FROM class_enrollments ce
     JOIN classes c ON ce.class_id = c.id
     JOIN students st ON ce.student_id = st.id
     WHERE st.user_id = ? AND ce.is_active = 1",
    [$student_id]
) ?? [];
// Upcoming Assignments
$assignments = db()->fetchAll("
    SELECT a.*, s.subject_name,
           DATEDIFF(a.due_date, CURDATE()) as days_remaining
    FROM assignments a
    JOIN subjects s ON a.subject_id = s.id
    JOIN class_enrollments ce ON a.class_id = ce.class_id
    JOIN students st ON ce.student_id = st.id
    WHERE st.user_id = ? AND a.due_date >= CURDATE() AND a.status = 'active'
    ORDER BY a.due_date
    LIMIT 5
", [$student_id]);

// Upcoming Exams
$exams = db()->fetchAll("
    SELECT e.*, s.subject_name
    FROM examinations e
    LEFT JOIN subjects s ON e.subject_id = s.id
    WHERE e.grade_level = ? AND e.exam_date >= CURDATE()
    ORDER BY e.exam_date
    LIMIT 5
", [$student['grade_level'] ?? '']);

// Recent Grades
$grades = db()->fetchAll("
    SELECT er.*, e.exam_name, s.subject_name,
           ROUND((er.marks_obtained / er.total_marks) * 100, 1) as percentage
    FROM exam_results er
    JOIN examinations e ON er.exam_id = e.id
    JOIN subjects s ON er.subject_id = s.id
    JOIN students st ON er.student_id = st.id
    WHERE st.user_id = ?
    ORDER BY e.exam_date DESC
    LIMIT 5
", [$student_id]);

// Fee Status
$fee_status = db()->fetchOne("
    SELECT
        IFNULL(SUM(fii.amount), 0) as total_due,
        IFNULL((SELECT SUM(amount_paid) FROM fee_payments WHERE invoice_id IN (
            SELECT id FROM fee_invoices WHERE student_id = st.id
        )), 0) as total_paid
    FROM fee_invoices fi
    LEFT JOIN fee_invoice_items fii ON fi.id = fii.invoice_id
    JOIN students st ON fi.student_id = st.id
    WHERE st.user_id = ? AND fi.academic_year = '2024-2025'
", [$student_id]) ?? ['total_due' => 0, 'total_paid' => 0];

$fee_balance = $fee_status['total_due'] - $fee_status['total_paid'];

// Library Books
$library_books = db()->fetchAll("
    SELECT lir.*, lb.book_title, lb.author,
           DATEDIFF(lir.due_date, CURDATE()) as days_remaining
    FROM library_issue_return lir
    JOIN library_books lb ON lir.book_id = lb.id
    JOIN students st ON lir.member_id = st.id
    WHERE st.user_id = ? AND lir.status = 'issued'
    ORDER BY lir.due_date
", [$student_id]);

// Unread Messages
$unread_messages = db()->fetchOne("
    SELECT COUNT(*) as count
    FROM message_recipients mr
    WHERE mr.recipient_id = ? AND mr.is_read = 0
", [$student_id])['count'] ?? 0;

// Today's Schedule
$today_day = date('w') - 1; // 0 = Monday
if ($today_day < 0) $today_day = 6; // Sunday becomes 6

$today_schedule = db()->fetchAll("
    SELECT t.*, s.subject_name, CONCAT(u.first_name, ' ', u.last_name) as teacher_name
    FROM timetable t
    JOIN subjects s ON t.subject_id = s.id
    LEFT JOIN teachers te ON t.teacher_id = te.id
    LEFT JOIN users u ON te.user_id = u.id
    WHERE t.class_id = ? AND t.day_of_week = ?
    ORDER BY t.period_number
", [$student['class_id'] ?? 0, $today_day]);

$page_title = 'Student Dashboard';
$page_icon = 'user-graduate';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/attendance/manifest.json">
    <meta name="theme-color" content="#00BFFF">
    <link rel="apple-touch-icon" href="/attendance/assets/images/icons/icon-192x192.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&family=Inter:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/cyberpunk-ui.css" rel="stylesheet">

</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>
    <div class="cyber-bg">
        <div class="starfield"></div>
    </div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>
        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <div class="biometric-orb" title="Quick Scan"><i class="fas fa-fingerprint"></i></div>
                    <div class="user-card" style="padding:8px 15px;margin:0;">
                        <div class="user-avatar" style="width:35px;height:35px;font-size:0.9rem;">
                            <?php echo strtoupper(substr($full_name, 0, 2)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name" style="font-size:0.85rem;"><?php echo htmlspecialchars($full_name); ?></div>
                            <div class="user-role">Student</div>
                        </div>
                    </div>
                </div>
            </header>
            <div class="cyber-content slide-in">
                <?php include '../includes/notice-board.php'; ?>

                <section class="orb-grid">
                    <div class="stat-orb">
                        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $present; ?></div>
                            <div class="stat-label">Days Present</div>
                            <div class="stat-trend up"><i class="fas fa-check"></i><span>Excellent</span></div>
                        </div>
                    </div>
                    <div class="stat-orb">
                        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $late; ?></div>
                            <div class="stat-label">Times Late</div>
                            <div class="stat-trend <?php echo $late > 5 ? 'down' : 'up'; ?>"><i class="fas fa-exclamation-circle"></i><span><?php echo $late > 5 ? 'Improve' : 'Good'; ?></span></div>
                        </div>
                    </div>
                    <div class="stat-orb">
                        <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $absent; ?></div>
                            <div class="stat-label">Days Absent</div>
                            <div class="stat-trend <?php echo $absent > 3 ? 'down' : 'up'; ?>"><i class="fas fa-info-circle"></i><span><?php echo $absent > 3 ? 'Alert' : 'Great'; ?></span></div>
                        </div>
                    </div>
                    <div class="stat-orb">
                        <div class="stat-icon cyan"><i class="fas fa-percentage"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $attendance_rate; ?>%</div>
                            <div class="stat-label">Attendance Rate</div>
                            <div class="stat-trend <?php echo $attendance_rate >= 90 ? 'up' : 'down'; ?>"><i class="fas fa-star"></i><span><?php echo $attendance_rate >= 90 ? 'Excellent' : 'Improve'; ?></span></div>
                        </div>
                    </div>
                </section>

                <section class="holo-card" style="margin-bottom:30px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;">
                        <h2 style="margin:0;display:flex;align-items:center;gap:12px;">
                            <i class="fas fa-brain" style="color:var(--hologram-purple);"></i>
                            <span>AI Analytics & Machine Learning</span>
                        </h2>
                        <span class="cyber-badge" style="background:rgba(100,100,100,0.3);">Neural Network Inactive</span>
                    </div>
                    <div class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
                        <div class="holo-card" style="padding:20px;">
                            <h4 style="color:var(--cyber-cyan);margin-bottom:12px;">Performance Predictor</h4>
                            <div style="font-size:2rem;color:var(--text-primary);margin-bottom:8px;">0.0%</div>
                            <div style="color:var(--text-muted);font-size:0.85rem;">Accuracy Rate</div>
                            <span class="cyber-badge" style="margin-top:10px;background:rgba(100,100,100,0.3);">Inactive</span>
                        </div>
                        <div class="holo-card" style="padding:20px;">
                            <h4 style="color:var(--golden-pulse);margin-bottom:12px;">Behavior Analyzer</h4>
                            <div style="font-size:2rem;color:var(--text-primary);margin-bottom:8px;">0.0%</div>
                            <div style="color:var(--text-muted);font-size:0.85rem;">Accuracy Rate</div>
                            <span class="cyber-badge" style="margin-top:10px;background:rgba(100,100,100,0.3);">Inactive</span>
                        </div>
                    </div>
                </section>

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;margin-bottom:30px;">
                    <div class="holo-card">
                        <div style="display:flex;align-items:center;gap:15px;margin-bottom:15px;">
                            <div style="width:50px;height:50px;background:linear-gradient(135deg,var(--cyber-cyan),var(--hologram-purple));border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-fingerprint" style="font-size:1.5rem;color:#fff;"></i>
                            </div>
                            <div>
                                <h3 style="margin:0;color:var(--cyber-cyan);">Quick Check-In</h3>
                                <p style="margin:5px 0 0;color:var(--text-muted);font-size:0.9rem;">Mark your attendance</p>
                            </div>
                        </div>
                        <a href="checkin.php" class="cyber-btn cyan" style="width:100%;text-align:center;text-decoration:none;"><i class="fas fa-clock"></i> Check In Now</a>
                    </div>

                    <div class="holo-card">
                        <div style="display:flex;align-items:center;gap:15px;margin-bottom:15px;">
                            <div style="width:50px;height:50px;background:linear-gradient(135deg,var(--neon-green),var(--cyber-cyan));border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-chart-line" style="font-size:1.5rem;color:#fff;"></i>
                            </div>
                            <div>
                                <h3 style="margin:0;color:var(--neon-green);">Attendance History</h3>
                                <p style="margin:5px 0 0;color:var(--text-muted);font-size:0.9rem;">View detailed records</p>
                            </div>
                        </div>
                        <a href="attendance.php" class="cyber-btn green" style="width:100%;text-align:center;text-decoration:none;"><i class="fas fa-clipboard-check"></i> View Records</a>
                    </div>

                    <div class="holo-card">
                        <div style="display:flex;align-items:center;gap:15px;margin-bottom:15px;">
                            <div style="width:50px;height:50px;background:linear-gradient(135deg,var(--golden-pulse),var(--neon-green));border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-calendar-alt" style="font-size:1.5rem;color:#fff;"></i>
                            </div>
                            <div>
                                <h3 style="margin:0;color:var(--golden-pulse);">Class Schedule</h3>
                                <p style="margin:5px 0 0;color:var(--text-muted);font-size:0.9rem;">Check upcoming classes</p>
                            </div>
                        </div>
                        <a href="schedule.php" class="cyber-btn orange" style="width:100%;text-align:center;text-decoration:none;"><i class="fas fa-calendar"></i> View Schedule</a>
                    </div>
                </div>

                <div class="holo-card">
                    <h3 style="margin-bottom:20px;">My Classes</h3>
                    <div style="display:grid;gap:15px;">
                        <?php foreach ($classes as $class): ?>
                            <div style="background:linear-gradient(135deg,rgba(0,191,255,0.05),rgba(138,43,226,0.05));border:1px solid rgba(0,191,255,0.2);border-radius:12px;padding:15px;display:flex;justify-content:space-between;align-items:center;">
                                <div>
                                    <h4 style="color:var(--cyber-cyan);margin:0 0 5px 0;"><?php echo htmlspecialchars($class['name']); ?></h4>
                                    <div style="color:rgba(0,191,255,0.6);font-size:0.85rem;"><?php echo htmlspecialchars($class['class_code']); ?></div>
                                </div>
                                <div style="text-align:right;">
                                    <span class="cyber-badge cyan"><?php echo $class['grade_level']; ?> Level</span>
                                    <div style="color:var(--text-muted);font-size:0.85rem;margin-top:5px;">Room: <?php echo htmlspecialchars($class['room_number'] ?? 'TBA'); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body>

</html>
