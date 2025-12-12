<?php

/**
 * Class Teacher Dashboard - Homeroom & Student Welfare
 * Verdant SMS v3.0
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for authentication
require_role('class-teacher', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Class Teacher';

// Get assigned class info
$assigned_class = db()->fetchOne("
    SELECT c.* FROM classes c
    JOIN class_teachers ct ON c.id = ct.class_id
    WHERE ct.teacher_id = ?
", [$user_id]);

$class_id = $assigned_class['id'] ?? 0;
$class_name = $assigned_class['name'] ?? 'Unassigned';

// Class Student Count
$student_count = db()->fetchOne("SELECT COUNT(*) as count FROM students WHERE class_id = ? AND is_active = 1", [$class_id])['count'] ?? 0;

// Today's Attendance for Class
$today_attendance = db()->fetchOne("
    SELECT
        COUNT(*) as total,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent
    FROM attendance a
    JOIN students s ON a.student_id = s.id
    WHERE s.class_id = ? AND a.date = CURDATE()
", [$class_id]) ?? ['total' => 0, 'present' => 0, 'absent' => 0];

// Pending Assignments
$pending_assignments = db()->fetchOne("
    SELECT COUNT(*) as count FROM assignments
    WHERE class_id = ? AND due_date >= CURDATE() AND status = 'active'
", [$class_id])['count'] ?? 0;

// Recent Behavior Notes
$behavior_notes = db()->fetchAll("
    SELECT bn.*, CONCAT(s.first_name, ' ', s.last_name) as student_name
    FROM behavior_notes bn
    JOIN students s ON bn.student_id = s.id
    WHERE s.class_id = ?
    ORDER BY bn.created_at DESC
    LIMIT 10
", [$class_id]);

// Class Students
$class_students = db()->fetchAll("
    SELECT s.*,
           (SELECT status FROM attendance WHERE student_id = s.id AND date = CURDATE() LIMIT 1) as today_status
    FROM students s
    WHERE s.class_id = ? AND s.is_active = 1
    ORDER BY s.first_name
    LIMIT 20
", [$class_id]);

$page_title = 'Class Teacher Dashboard';
$page_icon = 'people-group';
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
    <style>
        .class-banner {
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.2), rgba(138, 43, 226, 0.2));
            border: 1px solid var(--cyber-cyan, #00BFFF);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .class-name {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            color: #00BFFF;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg, rgba(20, 20, 30, 0.9));
            border: 1px solid var(--cyber-cyan, #00BFFF);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 191, 255, 0.3);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }

        .stat-icon.cyan {
            color: #00BFFF;
        }

        .stat-icon.green {
            color: #00FF7F;
        }

        .stat-icon.red {
            color: #FF4757;
        }

        .stat-icon.orange {
            color: #FF9F43;
        }

        .stat-value {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .dashboard-section {
            background: var(--card-bg, rgba(20, 20, 30, 0.9));
            border: 1px solid var(--border, rgba(0, 191, 255, 0.2));
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
            color: var(--cyber-cyan, #00BFFF);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .student-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .student-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
        }

        .student-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00BFFF, #8A2BE2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-size: 1.2rem;
            color: white;
        }

        .student-name {
            font-weight: 600;
            font-size: 0.85rem;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .status-present {
            background: #00FF7F;
        }

        .status-absent {
            background: #FF4757;
        }

        .status-unmarked {
            background: #6c757d;
        }

        .two-col {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
        }
    </style>
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb green"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <span class="welcome-text">Welcome, <?php echo htmlspecialchars($full_name); ?></span>
                </div>
            </header>

            <!-- Class Banner -->
            <div class="class-banner">
                <div>
                    <span style="color: rgba(255,255,255,0.6);">Assigned Class</span>
                    <div class="class-name"><?php echo htmlspecialchars($class_name); ?></div>
                </div>
                <div>
                    <span style="font-size: 2rem; font-family: 'Orbitron', sans-serif; color: #00FF7F;"><?php echo $student_count; ?></span>
                    <span style="color: rgba(255,255,255,0.6);"> Students</span>
                </div>
            </div>

            <!-- Overview Stats -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-value"><?php echo $today_attendance['present'] ?? 0; ?></div>
                    <div class="stat-label">Present Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
                    <div class="stat-value"><?php echo $today_attendance['absent'] ?? 0; ?></div>
                    <div class="stat-label">Absent Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-tasks"></i></div>
                    <div class="stat-value"><?php echo $pending_assignments; ?></div>
                    <div class="stat-label">Active Assignments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon cyan"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-value"><?php echo $student_count; ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
            </div>

            <!-- Students Grid -->
            <div class="dashboard-section">
                <h2 class="section-title"><i class="fas fa-users"></i> Class Students</h2>
                <div class="student-grid">
                    <?php if (empty($class_students)): ?>
                        <p style="color: rgba(255,255,255,0.5);">No students assigned to this class</p>
                    <?php else: ?>
                        <?php foreach ($class_students as $student): ?>
                            <div class="student-card">
                                <div class="student-avatar">
                                    <?php echo strtoupper(substr($student['first_name'] ?? 'S', 0, 1)); ?>
                                </div>
                                <div class="student-name"><?php echo htmlspecialchars(($student['first_name'] ?? '') . ' ' . substr($student['last_name'] ?? '', 0, 1) . '.'); ?></div>
                                <small>
                                    <span class="status-dot status-<?php echo $student['today_status'] ?? 'unmarked'; ?>"></span>
                                    <?php echo ucfirst($student['today_status'] ?? 'Not Marked'); ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-section">
                <h2 class="section-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="../teacher/attendance.php" class="cyber-btn cyan"><i class="fas fa-clipboard-check"></i> Take Attendance</a>
                    <a href="../teacher/assignments.php" class="cyber-btn green"><i class="fas fa-tasks"></i> Assignments</a>
                    <a href="../teacher/grades.php" class="cyber-btn purple"><i class="fas fa-star"></i> Enter Grades</a>
                    <a href="../teacher/messages.php" class="cyber-btn orange"><i class="fas fa-envelope"></i> Message Parents</a>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>