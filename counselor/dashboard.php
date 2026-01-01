<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for TEST_MODE support
require_role('counselor', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Counseling Stats
$total_students = db()->fetchOne("SELECT COUNT(*) as count FROM students WHERE is_active = 1")['count'] ?? 0;
$active_sessions = db()->fetchOne("SELECT COUNT(*) as count FROM counseling_sessions WHERE status = 'scheduled' AND session_date >= CURDATE()")['count'] ?? 0;
$completed_sessions = db()->fetchOne("SELECT COUNT(*) as count FROM counseling_sessions WHERE status = 'completed' AND MONTH(session_date) = MONTH(CURDATE()")['count'] ?? 0;

// Today's Appointments
$today_appointments = db()->fetchAll("
    SELECT cs.*,
           CONCAT(u.first_name, ' ', u.last_name) as student_name
    FROM counseling_sessions cs
    JOIN students st ON cs.student_id = st.id
    JOIN users u ON st.user_id = u.id
    WHERE DATE(cs.session_date) = CURDATE()
    ORDER BY cs.session_time
");

// Career Assessments Pending
$pending_assessments = db()->fetchOne("SELECT COUNT(*) as count FROM career_assessments WHERE status = 'pending'")['count'] ?? 0;

// Recent Cases
$recent_cases = db()->fetchAll("
    SELECT cs.*,
           CONCAT(u.first_name, ' ', u.last_name) as student_name,
           c.class_name
    FROM counseling_sessions cs
    JOIN students st ON cs.student_id = st.id
    JOIN users u ON st.user_id = u.id
    LEFT JOIN class_enrollments ce ON st.id = ce.student_id AND ce.is_active = 1
    LEFT JOIN classes c ON ce.class_id = c.id
    WHERE cs.status = 'completed'
    ORDER BY cs.session_date DESC
    LIMIT 10
");

// Common Issues (This Month)
$common_issues = db()->fetchAll("
    SELECT concern_type, COUNT(*) as count
    FROM counseling_sessions
    WHERE MONTH(session_date) = MONTH(CURDATE())
    GROUP BY concern_type
    ORDER BY count DESC
    LIMIT 5
");

$page_title = 'Counseling & Career Guidance';
$page_icon = 'user-friends';
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
                    <button class="cyber-btn cyan" onclick="window.location.href='../admin/career/new-session.php'">
                        <i class="fas fa-plus"></i> New Session
                    </button>
                    <div class="user-card" style="padding:8px 15px;">
                        <div class="user-avatar purple" style="width:35px;height:35px;">
                            <?php echo strtoupper(substr($full_name, 0, 2)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($full_name); ?></div>
                            <div class="user-role">Counselor</div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="cyber-content slide-in">

                <section class="orb-grid">
                    <div class="stat-orb">
                        <div class="stat-icon cyan"><i class="fas fa-users"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($total_students); ?></div>
                            <div class="stat-label">Total Students</div>
                            <div class="stat-trend"><i class="fas fa-user-graduate"></i> Active</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $active_sessions; ?></div>
                            <div class="stat-label">Scheduled Sessions</div>
                            <div class="stat-trend"><i class="fas fa-clock"></i> Upcoming</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon orange"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $completed_sessions; ?></div>
                            <div class="stat-label">Sessions This Month</div>
                            <div class="stat-trend up"><i class="fas fa-chart-line"></i> Completed</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon purple"><i class="fas fa-clipboard-list"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $pending_assessments; ?></div>
                            <div class="stat-label">Career Assessments</div>
                            <div class="stat-trend"><i class="fas fa-graduation-cap"></i> Pending</div>
                        </div>
                    </div>
                </section>

                <!-- Today's Appointments -->
                <?php if (!empty($today_appointments)): ?>
                    <section class="holo-card" style="margin-top:30px;background:linear-gradient(135deg,rgba(0,191,255,0.1),rgba(138,43,226,0.1));border-left:4px solid var(--cyber-cyan);">
                        <h3 style="color:var(--cyber-cyan);margin-bottom:15px;"><i class="fas fa-calendar-day"></i> Today's Appointments</h3>
                        <div style="display:grid;gap:12px;">
                            <?php foreach ($today_appointments as $apt): ?>
                                <div style="padding:15px;background:rgba(0,0,0,0.2);border-radius:8px;display:flex;justify-content:space-between;align-items:center;">
                                    <div>
                                        <h4 style="color:var(--text-primary);margin:0 0 5px 0;"><?php echo htmlspecialchars($apt['student_name']); ?></h4>
                                        <div style="color:var(--text-muted);font-size:0.9rem;">
                                            <i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($apt['session_time'])); ?>
                                            <span style="margin-left:15px;"><i class="fas fa-tag"></i> <?php echo ucfirst($apt['concern_type'] ?? 'General'); ?></span>
                                        </div>
                                    </div>
                                    <button class="cyber-btn cyan small" onclick="window.location.href='../admin/career/session-details.php?id=<?php echo $apt['id']; ?>'">View</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Quick Actions -->
                <section class="holo-card" style="margin-top:30px;">
                    <h3 style="margin-bottom:20px;"><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
                        <a href="../admin/career/sessions.php" class="action-card">
                            <div class="action-icon cyan"><i class="fas fa-comments"></i></div>
                            <h4>Counseling Sessions</h4>
                            <p>Manage appointments</p>
                        </a>

                        <a href="../admin/career/assessments.php" class="action-card">
                            <div class="action-icon green"><i class="fas fa-clipboard-check"></i></div>
                            <h4>Career Assessments</h4>
                            <p>Aptitude tests</p>
                        </a>

                        <a href="../admin/career/university-matcher.php" class="action-card">
                            <div class="action-icon purple"><i class="fas fa-university"></i></div>
                            <h4>University Matcher</h4>
                            <p>AI recommendations</p>
                        </a>

                        <a href="../admin/career/reports.php" class="action-card">
                            <div class="action-icon orange"><i class="fas fa-chart-bar"></i></div>
                            <h4>Reports</h4>
                            <p>Analytics & insights</p>
                        </a>
                    </div>
                </section>

                <!-- Common Concerns -->
                <?php if (!empty($common_issues)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-tag"></i> Common Concerns (This Month)</h3>
                        <div style="display:grid;gap:12px;">
                            <?php foreach ($common_issues as $issue): ?>
                                <div style="background:rgba(138,43,226,0.1);border-left:3px solid var(--hologram-purple);padding:15px;border-radius:8px;display:flex;justify-content:space-between;align-items:center;">
                                    <div>
                                        <h4 style="color:var(--text-primary);margin:0;"><?php echo htmlspecialchars(ucfirst($issue['concern_type'])); ?></h4>
                                    </div>
                                    <span class="cyber-badge purple"><?php echo $issue['count']; ?> sessions</span>
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
