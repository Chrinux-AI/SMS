<?php

/**
 * Subject Coordinator Dashboard - Department Management
 * Verdant SMS v3.0
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for authentication
require_role('subject-coordinator', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Subject Coordinator';

// Get assigned subject/department
$assigned_subject = db()->fetchOne("
    SELECT s.* FROM subjects s
    JOIN subject_coordinators sc ON s.id = sc.subject_id
    WHERE sc.user_id = ?
", [$user_id]);

$subject_id = $assigned_subject['id'] ?? 0;
$subject_name = $assigned_subject['name'] ?? 'Unassigned';

// Teachers in Department
$department_teachers = db()->fetchOne("
    SELECT COUNT(DISTINCT teacher_id) as count FROM teacher_subjects WHERE subject_id = ?
", [$subject_id])['count'] ?? 0;

// Classes Covering Subject
$classes_count = db()->fetchOne("
    SELECT COUNT(DISTINCT class_id) as count FROM class_subjects WHERE subject_id = ?
", [$subject_id])['count'] ?? 0;

// Question Bank Items
$question_bank_count = db()->fetchOne("
    SELECT COUNT(*) as count FROM question_bank WHERE subject_id = ?
", [$subject_id])['count'] ?? 0;

// Upcoming Exams
$upcoming_exams = db()->fetchAll("
    SELECT e.*, c.name as class_name
    FROM exams e
    JOIN classes c ON e.class_id = c.id
    WHERE e.subject_id = ? AND e.exam_date >= CURDATE()
    ORDER BY e.exam_date
    LIMIT 5
", [$subject_id]);

// Syllabus Coverage
$syllabus_topics = db()->fetchAll("
    SELECT * FROM syllabus_topics
    WHERE subject_id = ?
    ORDER BY sequence_order
    LIMIT 15
", [$subject_id]);

// Department Teachers List
$teachers_list = db()->fetchAll("
    SELECT CONCAT(u.first_name, ' ', u.last_name) as name, t.id
    FROM teachers t
    JOIN users u ON t.user_id = u.id
    JOIN teacher_subjects ts ON t.id = ts.teacher_id
    WHERE ts.subject_id = ?
    LIMIT 10
", [$subject_id]);

$page_title = 'Subject Coordinator Dashboard';
$page_icon = 'book-open-reader';
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
    <style>
        .subject-banner {
            background: linear-gradient(135deg, rgba(138, 43, 226, 0.2), rgba(0, 191, 255, 0.2));
            border: 1px solid var(--secondary, #8A2BE2);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .subject-name {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            color: #8A2BE2;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg, rgba(20, 20, 30, 0.9));
            border: 1px solid var(--secondary, #8A2BE2);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(138, 43, 226, 0.3);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }

        .stat-icon.purple {
            color: #8A2BE2;
        }

        .stat-icon.cyan {
            color: #00BFFF;
        }

        .stat-icon.green {
            color: #00FF7F;
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
            color: var(--secondary, #8A2BE2);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .data-table th {
            color: var(--secondary, #8A2BE2);
            font-weight: 600;
        }

        .progress-bar {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #8A2BE2, #00BFFF);
            border-radius: 4px;
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
                    <div class="page-icon-orb purple"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <span class="welcome-text">Welcome, <?php echo htmlspecialchars($full_name); ?></span>
                </div>
            </header>

            <!-- Subject Banner -->
            <div class="subject-banner">
                <div>
                    <span style="color: rgba(255,255,255,0.6);">Coordinating Subject</span>
                    <div class="subject-name"><?php echo htmlspecialchars($subject_name); ?></div>
                </div>
                <div>
                    <i class="fas fa-book" style="font-size: 2.5rem; color: #8A2BE2;"></i>
                </div>
            </div>

            <!-- Overview Stats -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="stat-value"><?php echo $department_teachers; ?></div>
                    <div class="stat-label">Teachers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon cyan"><i class="fas fa-door-open"></i></div>
                    <div class="stat-value"><?php echo $classes_count; ?></div>
                    <div class="stat-label">Classes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-question-circle"></i></div>
                    <div class="stat-value"><?php echo $question_bank_count; ?></div>
                    <div class="stat-label">Question Bank</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-value"><?php echo count($upcoming_exams); ?></div>
                    <div class="stat-label">Upcoming Exams</div>
                </div>
            </div>

            <div class="two-col">
                <!-- Upcoming Exams -->
                <div class="dashboard-section">
                    <h2 class="section-title"><i class="fas fa-file-alt"></i> Upcoming Exams</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Exam</th>
                                <th>Class</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($upcoming_exams)): ?>
                                <tr>
                                    <td colspan="3" style="color: rgba(255,255,255,0.5);">No upcoming exams</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($upcoming_exams as $exam): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($exam['name'] ?? 'Exam'); ?></td>
                                        <td><?php echo htmlspecialchars($exam['class_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('M d', strtotime($exam['exam_date'] ?? 'now')); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Department Teachers -->
                <div class="dashboard-section">
                    <h2 class="section-title"><i class="fas fa-users"></i> Department Teachers</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($teachers_list)): ?>
                                <tr>
                                    <td colspan="2" style="color: rgba(255,255,255,0.5);">No teachers assigned</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($teachers_list as $teacher): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                                        <td><a href="#" class="cyber-btn-sm cyan">View</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-section">
                <h2 class="section-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="../teacher/question-bank.php" class="cyber-btn purple"><i class="fas fa-database"></i> Question Bank</a>
                    <a href="../teacher/syllabus.php" class="cyber-btn cyan"><i class="fas fa-book"></i> Manage Syllabus</a>
                    <a href="../teacher/reports.php" class="cyber-btn green"><i class="fas fa-chart-bar"></i> Department Reports</a>
                    <a href="../teacher/resources.php" class="cyber-btn orange"><i class="fas fa-folder-open"></i> Resources</a>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>