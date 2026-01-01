<?php

/**
 * Advanced Analytics Dashboard - Admin Panel
 * Real-time system analytics with charts and insights
 */

session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Advanced Analytics";
$current_page = "analytics-advanced.php";

// Get date range from query params or default to last 30 days
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Calculate system-wide statistics
$stats = [];

// 1. User Statistics
$stats['total_users'] = db()->count('users');
$stats['active_users'] = db()->count('users', 'status = ?', ['active']);
$stats['pending_users'] = db()->count('users', 'status = ?', ['pending']);
$stats['total_students'] = db()->count('students');
$stats['total_teachers'] = db()->count('teachers');
$stats['total_parents'] = db()->count('guardians');

// 2. Attendance Statistics
$total_attendance = db()->count('attendance', 'attendance_date >= ? AND attendance_date <= ?', [$start_date, $end_date]);
$present_count = db()->count('attendance', 'status = ? AND attendance_date >= ? AND attendance_date <= ?', ['present', $start_date, $end_date]);
$absent_count = db()->count('attendance', 'status = ? AND attendance_date >= ? AND attendance_date <= ?', ['absent', $start_date, $end_date]);
$late_count = db()->count('attendance', 'status = ? AND attendance_date >= ? AND attendance_date <= ?', ['late', $start_date, $end_date]);

$stats['attendance_rate'] = $total_attendance > 0 ? round(($present_count / $total_attendance) * 100, 1) : 0;
$stats['absent_rate'] = $total_attendance > 0 ? round(($absent_count / $total_attendance) * 100, 1) : 0;
$stats['late_rate'] = $total_attendance > 0 ? round(($late_count / $total_attendance) * 100, 1) : 0;

// 3. Academic Statistics
$stats['total_classes'] = db()->count('classes');
$stats['total_subjects'] = db()->count('subjects');
$stats['total_assignments'] = db()->count('assignments');
$stats['pending_assignments'] = db()->count('assignment_submissions', 'status = ?', ['pending']);

// 4. Financial Statistics
$total_fees = db()->query("SELECT COALESCE(SUM(amount), 0) as total FROM fee_invoices")->fetch()['total'] ?? 0;
$paid_fees = db()->query("SELECT COALESCE(SUM(amount), 0) as total FROM fee_invoices WHERE status = 'paid'")->fetch()['total'] ?? 0;
$pending_fees = $total_fees - $paid_fees;

$stats['total_fees'] = number_format($total_fees, 2);
$stats['paid_fees'] = number_format($paid_fees, 2);
$stats['pending_fees'] = number_format($pending_fees, 2);
$stats['collection_rate'] = $total_fees > 0 ? round(($paid_fees / $total_fees) * 100, 1) : 0;

// 5. Communication Statistics
$stats['total_messages'] = db()->count('messages', 'created_at >= ? AND created_at <= ?', [$start_date, $end_date]);
$stats['total_notices'] = db()->count('notices', 'created_at >= ? AND created_at <= ?', [$start_date, $end_date]);
$stats['total_events'] = db()->count('events', 'event_date >= ? AND event_date <= ?', [$start_date, $end_date]);

// 6. Library Statistics (if table exists)
try {
    $stats['total_books'] = db()->count('library_books');
    $stats['books_issued'] = db()->count('library_transactions', 'return_date IS NULL');
    $stats['books_available'] = $stats['total_books'] - $stats['books_issued'];
} catch (Exception $e) {
    $stats['total_books'] = 0;
    $stats['books_issued'] = 0;
    $stats['books_available'] = 0;
}

// 7. Get daily attendance trend for chart
$attendance_trend = db()->query("
    SELECT
        DATE(attendance_date) as date,
        COUNT(*) as total,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent,
        SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late
    FROM attendance
    WHERE attendance_date >= ? AND attendance_date <= ?
    GROUP BY DATE(attendance_date)
    ORDER BY date ASC
", [$start_date, $end_date])->fetchAll();

// 8. Get role distribution
$role_distribution = db()->query("
    SELECT role, COUNT(*) as count
    FROM users
    WHERE status = 'active'
    GROUP BY role
    ORDER BY count DESC
")->fetchAll();

// 9. Top performing students (by attendance)
$top_students = db()->query("
    SELECT
        s.id,
        u.first_name,
        u.last_name,
        COUNT(CASE WHEN a.status = 'present' THEN 1 END) as present_days,
        COUNT(*) as total_days,
        ROUND((COUNT(CASE WHEN a.status = 'present' THEN 1 END) / COUNT(*)) * 100, 1) as attendance_rate
    FROM students s
    JOIN users u ON s.user_id = u.id
    LEFT JOIN attendance a ON s.id = a.student_id AND a.attendance_date >= ? AND a.attendance_date <= ?
    GROUP BY s.id, u.first_name, u.last_name
    HAVING total_days > 0
    ORDER BY attendance_rate DESC
    LIMIT 10
", [$start_date, $end_date])->fetchAll();

// 10. Recent activity logs
$recent_activities = db()->query("
    SELECT
        al.*,
        u.first_name,
        u.last_name,
        u.role
    FROM activity_logs al
    JOIN users u ON al.user_id = u.id
    ORDER BY al.created_at DESC
    LIMIT 20
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo APP_NAME; ?></title>
    <link rel="manifest" href="/attendance/manifest.json">
    <meta name="theme-color" content="#00d9ff">
    <link rel="apple-touch-icon" href="/attendance/assets/images/icon-192.png">
    <link rel="stylesheet" href="../../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <div class="cyber-container">
                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="glitch-text" data-text="<?php echo htmlspecialchars($page_title); ?>">
                        <i class="fas fa-chart-line"></i> <?php echo htmlspecialchars($page_title); ?>
                    </h1>
                    <p class="page-subtitle">Comprehensive system insights and metrics</p>
                </div>

                <!-- Date Range Filter -->
                <div class="holo-card mb-4">
                    <form method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="cyber-input" value="<?php echo htmlspecialchars($start_date); ?>">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="cyber-input" value="<?php echo htmlspecialchars($end_date); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="cyber-btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Apply
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Overview Stats Grid -->
                <div class="stats-grid mb-4">
                    <div class="stat-orb">
                        <i class="fas fa-users stat-icon"></i>
                        <div class="stat-value"><?php echo number_format($stats['total_users']); ?></div>
                        <div class="stat-label">Total Users</div>
                        <div class="stat-sublabel">
                            <span class="text-success"><?php echo $stats['active_users']; ?> active</span>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <i class="fas fa-user-graduate stat-icon"></i>
                        <div class="stat-value"><?php echo number_format($stats['total_students']); ?></div>
                        <div class="stat-label">Students</div>
                    </div>

                    <div class="stat-orb">
                        <i class="fas fa-chalkboard-teacher stat-icon"></i>
                        <div class="stat-value"><?php echo number_format($stats['total_teachers']); ?></div>
                        <div class="stat-label">Teachers</div>
                    </div>

                    <div class="stat-orb">
                        <i class="fas fa-check-circle stat-icon"></i>
                        <div class="stat-value"><?php echo $stats['attendance_rate']; ?>%</div>
                        <div class="stat-label">Attendance Rate</div>
                        <div class="stat-sublabel">
                            <?php echo number_format($present_count); ?> / <?php echo number_format($total_attendance); ?>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <i class="fas fa-dollar-sign stat-icon"></i>
                        <div class="stat-value">$<?php echo $stats['paid_fees']; ?></div>
                        <div class="stat-label">Fees Collected</div>
                        <div class="stat-sublabel"><?php echo $stats['collection_rate']; ?>% of total</div>
                    </div>

                    <div class="stat-orb">
                        <i class="fas fa-book stat-icon"></i>
                        <div class="stat-value"><?php echo number_format($stats['total_books']); ?></div>
                        <div class="stat-label">Library Books</div>
                        <div class="stat-sublabel"><?php echo $stats['books_issued']; ?> issued</div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="holo-card">
                            <h3 class="card-title">Attendance Trend (Last 30 Days)</h3>
                            <canvas id="attendanceTrendChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="holo-card">
                            <h3 class="card-title">User Role Distribution</h3>
                            <canvas id="roleDistributionChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top Performers -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="holo-card">
                            <h3 class="card-title">Top 10 Students by Attendance</h3>
                            <div class="table-responsive">
                                <table class="cyber-table">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Student</th>
                                            <th>Present Days</th>
                                            <th>Attendance %</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($top_students as $index => $student): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                                <td><?php echo $student['present_days']; ?> / <?php echo $student['total_days']; ?></td>
                                                <td>
                                                    <span class="cyber-badge badge-<?php echo $student['attendance_rate'] >= 95 ? 'success' : ($student['attendance_rate'] >= 85 ? 'warning' : 'danger'); ?>">
                                                        <?php echo $student['attendance_rate']; ?>%
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($top_students)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No attendance data available</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="holo-card">
                            <h3 class="card-title">Recent Activity</h3>
                            <div class="activity-feed">
                                <?php foreach ($recent_activities as $activity): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-circle"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">
                                                <strong><?php echo htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']); ?></strong>
                                                <span class="cyber-badge badge-info"><?php echo htmlspecialchars($activity['role']); ?></span>
                                            </div>
                                            <div class="activity-description"><?php echo htmlspecialchars($activity['action']); ?></div>
                                            <div class="activity-time"><?php echo date('M d, Y h:i A', strtotime($activity['created_at'])); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Stats Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="holo-card">
                            <h4 class="card-title">Academic</h4>
                            <ul class="stat-list">
                                <li><i class="fas fa-graduation-cap"></i> Classes: <strong><?php echo $stats['total_classes']; ?></strong></li>
                                <li><i class="fas fa-book-open"></i> Subjects: <strong><?php echo $stats['total_subjects']; ?></strong></li>
                                <li><i class="fas fa-tasks"></i> Assignments: <strong><?php echo $stats['total_assignments']; ?></strong></li>
                                <li><i class="fas fa-clock"></i> Pending: <strong><?php echo $stats['pending_assignments']; ?></strong></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="holo-card">
                            <h4 class="card-title">Financial</h4>
                            <ul class="stat-list">
                                <li><i class="fas fa-dollar-sign"></i> Total Fees: <strong>$<?php echo $stats['total_fees']; ?></strong></li>
                                <li><i class="fas fa-check-circle"></i> Collected: <strong>$<?php echo $stats['paid_fees']; ?></strong></li>
                                <li><i class="fas fa-exclamation-circle"></i> Pending: <strong>$<?php echo $stats['pending_fees']; ?></strong></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="holo-card">
                            <h4 class="card-title">Communication</h4>
                            <ul class="stat-list">
                                <li><i class="fas fa-envelope"></i> Messages: <strong><?php echo number_format($stats['total_messages']); ?></strong></li>
                                <li><i class="fas fa-bullhorn"></i> Notices: <strong><?php echo $stats['total_notices']; ?></strong></li>
                                <li><i class="fas fa-calendar"></i> Events: <strong><?php echo $stats['total_events']; ?></strong></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="holo-card">
                            <h4 class="card-title">Library</h4>
                            <ul class="stat-list">
                                <li><i class="fas fa-book"></i> Total Books: <strong><?php echo number_format($stats['total_books']); ?></strong></li>
                                <li><i class="fas fa-hand-holding"></i> Issued: <strong><?php echo $stats['books_issued']; ?></strong></li>
                                <li><i class="fas fa-check"></i> Available: <strong><?php echo $stats['books_available']; ?></strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include '../../includes/chatbot-unified.php'; ?>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/pwa-manager.js"></script>

    <script>
        // Attendance Trend Chart
        const attendanceTrendCtx = document.getElementById('attendanceTrendChart');
        if (attendanceTrendCtx) {
            const trendData = <?php echo json_encode($attendance_trend); ?>;
            new Chart(attendanceTrendCtx, {
                type: 'line',
                data: {
                    labels: trendData.map(d => d.date),
                    datasets: [{
                            label: 'Present',
                            data: trendData.map(d => d.present),
                            borderColor: '#00d9ff',
                            backgroundColor: 'rgba(0, 217, 255, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Absent',
                            data: trendData.map(d => d.absent),
                            borderColor: '#ff2d75',
                            backgroundColor: 'rgba(255, 45, 117, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Late',
                            data: trendData.map(d => d.late),
                            borderColor: '#ffd700',
                            backgroundColor: 'rgba(255, 215, 0, 0.1)',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#00d9ff'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#00d9ff'
                            },
                            grid: {
                                color: 'rgba(0, 217, 255, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#00d9ff'
                            },
                            grid: {
                                color: 'rgba(0, 217, 255, 0.1)'
                            }
                        }
                    }
                }
            });
        }

        // Role Distribution Chart
        const roleDistCtx = document.getElementById('roleDistributionChart');
        if (roleDistCtx) {
            const roleData = <?php echo json_encode($role_distribution); ?>;
            new Chart(roleDistCtx, {
                type: 'doughnut',
                data: {
                    labels: roleData.map(r => r.role),
                    datasets: [{
                        data: roleData.map(r => r.count),
                        backgroundColor: [
                            '#00d9ff', '#ff2d75', '#ffd700', '#00ff88',
                            '#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#00d9ff',
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>

</html>
