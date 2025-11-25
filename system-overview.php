<?php

/**
 * SAMS System Test & Dashboard Links
 * Quick access page for all role dashboards
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';

// Test database connection
$db_status = 'Connected';
$db_color = '#00ff7f';
try {
    $test = db()->fetchOne("SELECT COUNT(*) as count FROM users");
    $user_count = $test['count'] ?? 0;
} catch (Exception $e) {
    $db_status = 'Error: ' . $e->getMessage();
    $db_color = '#ff4500';
    $user_count = 0;
}

// Get system statistics
$stats = [
    'admins' => db()->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'admin'")['count'] ?? 0,
    'teachers' => db()->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'teacher'")['count'] ?? 0,
    'students' => db()->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'student'")['count'] ?? 0,
    'parents' => db()->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'parent'")['count'] ?? 0,
    'classes' => db()->fetchOne("SELECT COUNT(*) as count FROM classes")['count'] ?? 0,
    'messages' => db()->fetchOne("SELECT COUNT(*) as count FROM messages")['count'] ?? 0,
    'forum_threads' => db()->fetchOne("SELECT COUNT(*) as count FROM forum_threads")['count'] ?? 0,
    'study_groups' => db()->fetchOne("SELECT COUNT(*) as count FROM study_groups")['count'] ?? 0,
];

// Get server info
$server_ip = $_SERVER['SERVER_ADDR'] ?? 'localhost';
$server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';
$php_version = phpversion();
$base_url = 'http://' . $server_name . '/attendance';

// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);
$current_role = $_SESSION['role'] ?? 'guest';
$current_user = $_SESSION['full_name'] ?? 'Not logged in';

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
    <title>SAMS - System Overview & Dashboard Links</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a0e27 0%, #1a1a2e 100%);
            color: #ffffff;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
        }

        .header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 3rem;
            background: linear-gradient(135deg, #00bfff, #8a2be2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .header p {
            color: #00bfff;
            font-size: 1.2rem;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .status-card {
            background: rgba(0, 191, 255, 0.1);
            border: 1px solid rgba(0, 191, 255, 0.3);
            border-radius: 12px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }

        .status-card h3 {
            color: #00bfff;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .status-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: #00ff7f;
        }

        .dashboard-section {
            margin-bottom: 40px;
        }

        .dashboard-section h2 {
            color: #00bfff;
            font-size: 1.8rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .dashboard-card {
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.15), rgba(138, 43, 226, 0.15));
            border: 2px solid;
            border-radius: 15px;
            padding: 30px;
            text-decoration: none;
            color: white;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.2), rgba(138, 43, 226, 0.2));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .dashboard-card:hover::before {
            opacity: 1;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 191, 255, 0.3);
        }

        .dashboard-card.admin {
            border-color: #ff4500;
        }

        .dashboard-card.teacher {
            border-color: #00bfff;
        }

        .dashboard-card.student {
            border-color: #00ff7f;
        }

        .dashboard-card.parent {
            border-color: #ffd700;
        }

        .dashboard-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .dashboard-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .dashboard-desc {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .dashboard-url {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            color: #00bfff;
            background: rgba(0, 0, 0, 0.3);
            padding: 8px 12px;
            border-radius: 5px;
            word-break: break-all;
            position: relative;
            z-index: 1;
        }

        .features-list {
            background: rgba(0, 191, 255, 0.05);
            border: 1px solid rgba(0, 191, 255, 0.2);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 40px;
        }

        .features-list h3 {
            color: #00bfff;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: rgba(0, 191, 255, 0.1);
            border-radius: 8px;
        }

        .feature-item i {
            color: #00ff7f;
            font-size: 1.2rem;
        }

        .login-prompt {
            background: linear-gradient(135deg, rgba(255, 69, 0, 0.2), rgba(255, 215, 0, 0.2));
            border: 2px solid #ffd700;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 40px;
        }

        .login-prompt h3 {
            color: #ffd700;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #00bfff, #8a2be2);
            border: none;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            margin: 10px;
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(0, 191, 255, 0.4);
        }

        .copy-btn {
            display: inline-block;
            padding: 5px 10px;
            background: rgba(0, 191, 255, 0.2);
            border: 1px solid #00bfff;
            border-radius: 5px;
            color: #00bfff;
            font-size: 0.8rem;
            cursor: pointer;
            margin-left: 10px;
            transition: all 0.3s;
        }

        .copy-btn:hover {
            background: rgba(0, 191, 255, 0.4);
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>
<div class="container">
        <div class="header">
            <h1>🚀 SAMS System Overview</h1>
            <p>Student Attendance Management System - Dashboard Links</p>
        </div>

        <!-- Current Session Info -->
        <?php if ($logged_in): ?>
            <div style="background: rgba(0, 255, 127, 0.1); border: 1px solid #00ff7f; border-radius: 12px; padding: 20px; margin-bottom: 40px; text-align: center;">
                <h3 style="color: #00ff7f; margin-bottom: 10px;">
                    <i class="fas fa-user-check"></i> Currently Logged In
                </h3>
                <p style="font-size: 1.2rem;"><?php echo htmlspecialchars($current_user); ?> - <strong><?php echo ucfirst($current_role); ?></strong></p>
                <a href="<?php echo $current_role; ?>/dashboard.php" class="btn">
                    <i class="fas fa-tachometer-alt"></i> Go to My Dashboard
                </a>
                <a href="logout.php" class="btn" style="background: linear-gradient(135deg, #ff4500, #ff6347);">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        <?php else: ?>
            <div class="login-prompt">
                <h3><i class="fas fa-info-circle"></i> Not Logged In</h3>
                <p style="margin-bottom: 20px;">Login to access role-specific features and dashboards</p>
                <a href="login.php" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Login Now
                </a>
                <a href="register.php" class="btn" style="background: linear-gradient(135deg, #00ff7f, #32cd32);">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            </div>
        <?php endif; ?>

        <!-- System Status -->
        <div class="status-grid">
            <div class="status-card">
                <h3><i class="fas fa-database"></i> Database</h3>
                <div class="value" style="color: <?php echo $db_color; ?>"><?php echo $db_status; ?></div>
            </div>
            <div class="status-card">
                <h3><i class="fas fa-users"></i> Total Users</h3>
                <div class="value"><?php echo number_format($user_count); ?></div>
            </div>
            <div class="status-card">
                <h3><i class="fas fa-door-open"></i> Classes</h3>
                <div class="value"><?php echo number_format($stats['classes']); ?></div>
            </div>
            <div class="status-card">
                <h3><i class="fas fa-php"></i> PHP Version</h3>
                <div class="value" style="font-size: 1.5rem;"><?php echo $php_version; ?></div>
            </div>
        </div>

        <!-- Role Statistics -->
        <div class="status-grid">
            <div class="status-card" style="border-color: #ff4500;">
                <h3><i class="fas fa-user-shield"></i> Admins</h3>
                <div class="value"><?php echo $stats['admins']; ?></div>
            </div>
            <div class="status-card" style="border-color: #00bfff;">
                <h3><i class="fas fa-chalkboard-teacher"></i> Teachers</h3>
                <div class="value"><?php echo $stats['teachers']; ?></div>
            </div>
            <div class="status-card" style="border-color: #00ff7f;">
                <h3><i class="fas fa-user-graduate"></i> Students</h3>
                <div class="value"><?php echo $stats['students']; ?></div>
            </div>
            <div class="status-card" style="border-color: #ffd700;">
                <h3><i class="fas fa-users"></i> Parents</h3>
                <div class="value"><?php echo $stats['parents']; ?></div>
            </div>
        </div>

        <!-- Dashboard Links -->
        <div class="dashboard-section">
            <h2><i class="fas fa-link"></i> Dashboard Links by Role</h2>
            <div class="dashboard-grid">
                <!-- Admin Dashboard -->
                <a href="admin/dashboard.php" class="dashboard-card admin">
                    <div class="dashboard-icon" style="color: #ff4500;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="dashboard-title">Admin Dashboard</div>
                    <div class="dashboard-desc">
                        Full system control, user management, reports, analytics, and system configuration
                    </div>
                    <div class="dashboard-url">
                        <?php echo $base_url; ?>/admin/dashboard.php
                        <button onclick="copyToClipboard('<?php echo $base_url; ?>/admin/dashboard.php')" class="copy-btn">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </a>

                <!-- Teacher Dashboard -->
                <a href="teacher/dashboard.php" class="dashboard-card teacher">
                    <div class="dashboard-icon" style="color: #00bfff;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="dashboard-title">Teacher Dashboard</div>
                    <div class="dashboard-desc">
                        Mark attendance, manage classes, assignments, grades, and communicate with students/parents
                    </div>
                    <div class="dashboard-url">
                        <?php echo $base_url; ?>/teacher/dashboard.php
                        <button onclick="copyToClipboard('<?php echo $base_url; ?>/teacher/dashboard.php')" class="copy-btn">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </a>

                <!-- Student Dashboard -->
                <a href="student/dashboard.php" class="dashboard-card student">
                    <div class="dashboard-icon" style="color: #00ff7f;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="dashboard-title">Student Dashboard</div>
                    <div class="dashboard-desc">
                        View attendance, schedules, assignments, grades, and chat with classmates
                    </div>
                    <div class="dashboard-url">
                        <?php echo $base_url; ?>/student/dashboard.php
                        <button onclick="copyToClipboard('<?php echo $base_url; ?>/student/dashboard.php')" class="copy-btn">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </a>

                <!-- Parent Dashboard -->
                <a href="parent/dashboard.php" class="dashboard-card parent">
                    <div class="dashboard-icon" style="color: #ffd700;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="dashboard-title">Parent Dashboard</div>
                    <div class="dashboard-desc">
                        Monitor children's attendance, grades, fees, and schedule teacher meetings
                    </div>
                    <div class="dashboard-url">
                        <?php echo $base_url; ?>/parent/dashboard.php
                        <button onclick="copyToClipboard('<?php echo $base_url; ?>/parent/dashboard.php')" class="copy-btn">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </a>
            </div>
        </div>

        <!-- New Features -->
        <div class="features-list">
            <h3><i class="fas fa-star"></i> Latest Features Implemented</h3>
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-comments"></i>
                    <span>Student Communication Platform</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-robot"></i>
                    <span>Enhanced SAMS Bot Assistant</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-users"></i>
                    <span>Study Groups (<?php echo $stats['study_groups']; ?> active)</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-forum"></i>
                    <span>The Quad Forum (<?php echo $stats['forum_threads']; ?> threads)</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-book"></i>
                    <span>Teacher Resource Library</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-calendar-check"></i>
                    <span>Parent-Teacher Meetings</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Behavior Logging System</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Emergency Alert System</span>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="features-list">
            <h3><i class="fas fa-bolt"></i> Quick Access Links</h3>
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-home"></i>
                    <a href="index.php" style="color: #00bfff; text-decoration: none;">Main Portal</a>
                </div>
                <div class="feature-item">
                    <i class="fas fa-sign-in-alt"></i>
                    <a href="login.php" style="color: #00bfff; text-decoration: none;">Login Page</a>
                </div>
                <div class="feature-item">
                    <i class="fas fa-envelope"></i>
                    <a href="messages.php" style="color: #00bfff; text-decoration: none;">Messages</a>
                </div>
                <div class="feature-item">
                    <i class="fas fa-bullhorn"></i>
                    <a href="notices.php" style="color: #00bfff; text-decoration: none;">Notice Board</a>
                </div>
                <div class="feature-item">
                    <i class="fas fa-comments"></i>
                    <a href="forum/index.php" style="color: #00bfff; text-decoration: none;">Forum</a>
                </div>
                <div class="feature-item">
                    <i class="fas fa-info-circle"></i>
                    <a href="IMPLEMENTATION_SUMMARY_NOV23.md" style="color: #00bfff; text-decoration: none;">Documentation</a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 50px; padding-top: 30px; border-top: 1px solid rgba(0, 191, 255, 0.2);">
            <p style="color: rgba(255, 255, 255, 0.5); margin-bottom: 10px;">
                SAMS - Student Attendance Management System v2.0
            </p>
            <p style="color: rgba(255, 255, 255, 0.5);">
                Server: <?php echo $server_name; ?> (<?php echo $server_ip; ?>)
            </p>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Link copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy:', err);
            });
        }
    </script>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body>

</html>