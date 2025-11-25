<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.php');
    exit;
}

$student_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Get student's classes
$classes = db()->fetchAll("
    SELECT c.*, t.first_name as teacher_first, t.last_name as teacher_last
    FROM classes c
    LEFT JOIN teachers t ON c.teacher_id = t.id
    JOIN class_enrollments ce ON c.id = ce.class_id
    WHERE ce.student_id = ?
    ORDER BY c.day_of_week, c.start_time
", [$student_id]);

// Organize by day of week
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$schedule = array_fill_keys($days, []);

foreach ($classes as $class) {
    $day_index = ($class['day_of_week'] ?? 1) - 1;
    if ($day_index >= 0 && $day_index < 7) {
        $schedule[$days[$day_index]][] = $class;
    }
}

$page_title = 'My Schedule';
$page_icon = 'calendar-alt';
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

        <div class="starfield"></div>
    <div class="cyber-grid"></div>

        <div class="starfield"></div>
    <div class="cyber-grid"></div>

        <div class="cyber-bg">
        <div class="starfield"></div>
    </div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/student-nav.php'; ?>

        <!-- Main Content -->
        <aside class="cyber-sidebar">
            <div class="sidebar-header">
                <div class="logo-wrapper">
                    <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
                    <div class="logo-text">
                        <div class="app-name">School Management System</div>
                        <div class="app-tagline">STUDENT PANEL</div>
                    </div>
                </div>
            </div>
            <div class="user-card">
                <div class="user-avatar"><?php echo strtoupper(substr($full_name, 0, 2)); ?></div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($full_name); ?></div>
                    <div class="user-role">Student</div>
                </div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="section-title">CORE</div>
                    <a href="dashboard.php" class="menu-item"><i class="fas fa-brain"></i><span>Dashboard</span></a>
                    <a href="checkin.php" class="menu-item"><i class="fas fa-fingerprint"></i><span>Check In</span></a>
                    <a href="attendance.php" class="menu-item"><i class="fas fa-clipboard-check"></i><span>My Attendance</span></a>
                    <a href="schedule.php" class="menu-item active"><i class="fas fa-calendar-alt"></i><span>Schedule</span></a>
                </div>
                <div class="nav-section">
                    <div class="section-title">MANAGEMENT</div>
                    <a href="profile.php" class="menu-item"><i class="fas fa-user-circle"></i><span>Profile</span></a>
                    <a href="settings.php" class="menu-item"><i class="fas fa-cog"></i><span>Settings</span></a>
                </div>
            </nav>
            <div class="sidebar-footer">
                <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </aside>
        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <div class="stat-badge" style="background:rgba(0,191,255,0.1);border:1px solid var(--cyber-cyan);padding:8px 15px;border-radius:8px;">
                        <i class="fas fa-book"></i> <?php echo count($classes); ?> Classes
                    </div>
                </div>
            </header>
            <div class="cyber-content slide-in">

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(350px,1fr));gap:20px;">
                    <?php foreach ($schedule as $day => $day_classes): ?>
                        <div class="holo-card">
                            <div class="card-header">
                                <div class="card-title">
                                    <i class="fas fa-calendar-day" style="color:var(--cyber-cyan);"></i>
                                    <span><?php echo $day; ?></span>
                                </div>
                                <span class="cyber-badge <?php echo count($day_classes) > 0 ? 'success' : ''; ?>">
                                    <?php echo count($day_classes); ?> <?php echo count($day_classes) == 1 ? 'Class' : 'Classes'; ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <?php if (count($day_classes) > 0): ?>
                                    <?php foreach ($day_classes as $class): ?>
                                        <div style="background:rgba(0,191,255,0.05);border-left:3px solid var(--cyber-cyan);padding:15px;border-radius:8px;margin-bottom:15px;">
                                            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:10px;">
                                                <h4 style="color:var(--cyber-cyan);margin:0;font-size:1.1rem;">
                                                    <?php echo htmlspecialchars($class['name'] ?? 'Unnamed Class'); ?>
                                                </h4>
                                                <span class="cyber-badge" style="background:rgba(102,126,234,0.2);">
                                                    <?php echo htmlspecialchars($class['code'] ?? 'N/A'); ?>
                                                </span>
                                            </div>
                                            <div style="color:var(--text-muted);font-size:0.9rem;margin-bottom:8px;">
                                                <i class="fas fa-clock"></i>
                                                <?php echo htmlspecialchars($class['start_time'] ?? 'TBA'); ?> -
                                                <?php echo htmlspecialchars($class['end_time'] ?? 'TBA'); ?>
                                            </div>
                                            <?php if (!empty($class['teacher_first']) || !empty($class['teacher_last'])): ?>
                                                <div style="color:var(--text-muted);font-size:0.9rem;margin-bottom:8px;">
                                                    <i class="fas fa-chalkboard-teacher"></i>
                                                    <?php echo htmlspecialchars($class['teacher_first'] . ' ' . $class['teacher_last']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($class['room'])): ?>
                                                <div style="color:var(--text-muted);font-size:0.9rem;">
                                                    <i class="fas fa-door-open"></i>
                                                    Room <?php echo htmlspecialchars($class['room']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div style="text-align:center;padding:40px;color:var(--text-muted);">
                                        <i class="fas fa-calendar-times" style="font-size:3rem;opacity:0.3;margin-bottom:15px;"></i>
                                        <p>No classes scheduled</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($classes) == 0): ?>
                    <div class="holo-card" style="margin-top:30px;">
                        <div class="card-body" style="text-align:center;padding:60px 20px;">
                            <div style="font-size:5rem;color:var(--cyber-cyan);opacity:0.3;margin-bottom:20px;">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <h3 style="color:var(--text-primary);margin-bottom:15px;">No Classes Scheduled</h3>
                            <p style="color:var(--text-muted);max-width:500px;margin:0 auto;">
                                You are not enrolled in any classes yet. Please contact your administrator for assistance.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body>

</html>