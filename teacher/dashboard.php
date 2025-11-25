<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../login.php');
    exit;
}

$teacher_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Get teacher details
$teacher = db()->fetchOne("SELECT * FROM teachers WHERE user_id = ?", [$teacher_id]);

// Get teacher's classes
$my_classes = db()->fetchAll("
    SELECT c.id, c.class_name, c.class_code, c.teacher_id, c.description, c.schedule, c.room, c.created_at,
           COUNT(DISTINCT ce.student_id) as student_count
    FROM classes c
    LEFT JOIN class_enrollments ce ON c.id = ce.class_id
    WHERE c.teacher_id = ?
    GROUP BY c.id, c.class_name, c.class_code, c.teacher_id, c.description, c.schedule, c.room, c.created_at
", [$teacher_id]);

// Get total unique students across all classes
$total_students_result = db()->fetchOne("
    SELECT COUNT(DISTINCT ce.student_id) as total
    FROM class_enrollments ce
    JOIN classes c ON ce.class_id = c.id
    WHERE c.teacher_id = ?
", [$teacher_id]);
$total_students = $total_students_result['total'] ?? 0;

// Today's attendance statistics
$today = date('Y-m-d');
$today_attendance = db()->fetchAll("
    SELECT ar.*, c.class_name
    FROM attendance_records ar
    JOIN classes c ON ar.class_id = c.id
    WHERE c.teacher_id = ? AND DATE(ar.check_in_time) = ?
    ORDER BY ar.check_in_time DESC
", [$teacher_id, $today]);

$today_present = count(array_filter($today_attendance, fn($r) => $r['status'] === 'present'));
$today_late = count(array_filter($today_attendance, fn($r) => $r['status'] === 'late'));
$today_absent = count(array_filter($today_attendance, fn($r) => $r['status'] === 'absent'));
$today_total = count($today_attendance);
$today_rate = $today_total > 0 ? round((($today_present + $today_late) / $today_total) * 100, 1) : 0;

// This week's attendance trend
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d', strtotime('sunday this week'));
$week_attendance = db()->fetchAll("
    SELECT DATE(ar.check_in_time) as date, ar.status, COUNT(*) as count
    FROM attendance_records ar
    JOIN classes c ON ar.class_id = c.id
    WHERE c.teacher_id = ? AND DATE(ar.check_in_time) BETWEEN ? AND ?
    GROUP BY DATE(ar.check_in_time), ar.status
", [$teacher_id, $week_start, $week_end]);

// Unread messages count
$unread_count = db()->fetchOne("
    SELECT COUNT(*) as count FROM message_recipients mr
    WHERE mr.recipient_id = ? AND mr.is_read = 0 AND mr.deleted_at IS NULL
", [$teacher_id])['count'] ?? 0;

$page_title = 'Teacher Dashboard';
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

    <style>
        .attendance-trend-chart {
            background: rgba(0, 191, 255, 0.05);
            border: 1px solid rgba(0, 191, 255, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin-top: 15px;
        }

        .day-bar {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            gap: 10px;
        }

        .day-label {
            width: 80px;
            font-weight: 600;
            color: #00BFFF;
        }

        .bar-container {
            flex: 1;
            height: 25px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }

        .bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #00BFFF, #00FF7F);
            transition: width 0.3s;
        }

        .bar-value {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-weight: 600;
            font-size: 12px;
        }
    </style>
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
                    <div class="page-icon-orb">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <h1 class="page-title">Teacher Command Center</h1>
                        <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars($full_name); ?></p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="../messages.php" class="cyber-btn btn-icon" title="Messages">
                        <i class="fas fa-envelope"></i>
                        <?php if ($unread_count > 0): ?>
                            <span class="badge"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="datetime-display">
                        <div class="date-text"><?php echo date('l, F j, Y'); ?></div>
                        <div class="time-text"><?php echo date('h:i A'); ?></div>
                    </div>
                </div>
            </header>

            <div class="cyber-content slide-in">
                <?php include '../includes/notice-board.php'; ?>

                <!-- Stats Overview -->
                <div class="stats-grid">
                    <div class="stat-orb">
                        <div class="stat-icon cyber">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="stat-label">My Classes</div>
                        <div class="stat-value"><?php echo count($my_classes); ?></div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon green">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-label">Total Students</div>
                        <div class="stat-value"><?php echo $total_students; ?></div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon purple">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="stat-label">Today's Attendance</div>
                        <div class="stat-value"><?php echo $today_rate; ?>%</div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon yellow">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-label">Today Absent</div>
                        <div class="stat-value"><?php echo $today_absent; ?></div>
                    </div>
                </div>

                <div class="grid-2">
                    <!-- My Classes -->
                    <div class="holo-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-door-open"></i>
                                <span>My Classes</span>
                            </div>
                            <a href="my-classes.php" class="cyber-btn btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($my_classes)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-door-closed"></i>
                                    <p>No classes assigned yet</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach (array_slice($my_classes, 0, 5) as $class): ?>
                                        <div class="list-item">
                                            <div class="item-icon">
                                                <i class="fas fa-book"></i>
                                            </div>
                                            <div class="item-content">
                                                <div class="item-title"><?php echo htmlspecialchars($class['class_name']); ?></div>
                                                <div class="item-subtitle">
                                                    <?php echo htmlspecialchars($class['class_code']); ?> •
                                                    <?php echo $class['student_count']; ?> students
                                                </div>
                                            </div>
                                            <a href="attendance.php?class=<?php echo $class['id']; ?>" class="cyber-btn btn-sm">
                                                Mark Attendance
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Today's Attendance -->
                    <div class="holo-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-calendar-day"></i>
                                <span>Today's Attendance Log</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($today_attendance)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-clipboard"></i>
                                    <p>No attendance records for today</p>
                                </div>
                            <?php else: ?>
                                <table class="holo-table compact">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Class</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($today_attendance, 0, 8) as $record): ?>
                                            <tr>
                                                <td><?php echo date('h:i A', strtotime($record['check_in_time'])); ?></td>
                                                <td><?php echo htmlspecialchars($record['class_name']); ?></td>
                                                <td>
                                                    <span class="status-badge <?php echo $record['status']; ?>">
                                                        <?php echo ucfirst($record['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Weekly Attendance Trend -->
                <div class="holo-card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-chart-line"></i>
                            <span>This Week's Attendance Trend</span>
                        </div>
                        <a href="reports.php" class="cyber-btn btn-sm">Full Report</a>
                    </div>
                    <div class="card-body">
                        <div class="attendance-trend-chart">
                            <?php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                            foreach ($days as $day):
                                $day_date = date('Y-m-d', strtotime($day . ' this week'));
                                $day_records = array_filter($week_attendance, fn($r) => $r['date'] === $day_date);
                                $day_total = array_sum(array_column($day_records, 'count'));
                                $day_present = array_sum(array_column(array_filter($day_records, fn($r) => $r['status'] === 'present'), 'count'));
                                $day_percentage = $day_total > 0 ? round(($day_present / $day_total) * 100) : 0;
                            ?>
                                <div class="day-bar">
                                    <div class="day-label"><?php echo $day; ?></div>
                                    <div class="bar-container">
                                        <div class="bar-fill" style="width: <?php echo $day_percentage; ?>%"></div>
                                        <div class="bar-value"><?php echo $day_percentage; ?>%</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Update time every second
        setInterval(() => {
            const now = new Date();
            const timeText = document.querySelector('.time-text');
            if (timeText) {
                timeText.textContent = now.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }, 1000);

        // Check for new messages every 30 seconds
        setInterval(async () => {
            try {
                const response = await fetch('../api/messaging.php?action=unread_count');
                const data = await response.json();
                if (data.success) {
                    const badge = document.querySelector('.header-actions .badge');
                    if (data.count > 0) {
                        if (badge) {
                            badge.textContent = data.count;
                        } else {
                            const btn = document.querySelector('.header-actions .cyber-btn');
                            const newBadge = document.createElement('span');
                            newBadge.className = 'badge';
                            newBadge.textContent = data.count;
                            btn.appendChild(newBadge);
                        }
                    } else if (badge) {
                        badge.remove();
                    }
                }
            } catch (error) {
                console.error('Failed to fetch unread count:', error);
            }
        }, 30000);
    </script>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body>

</html>