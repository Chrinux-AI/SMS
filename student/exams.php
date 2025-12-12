<?php

/**
 * Student My Exams - Student Panel
 * View exam schedule and results
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';
require_role('student');

$page_title = "My Examinations";
$current_page = "exams.php";

$student_id = $_SESSION['user_id'];

// Get student's grade level
$student = db()->fetch("
    SELECT s.*, c.class_name, c.grade_level
    FROM students s
    JOIN class_enrollments ce ON s.id = ce.student_id
    JOIN classes c ON ce.class_id = c.id
    WHERE s.user_id = ? AND ce.is_active = 1
    LIMIT 1
", [$student_id]);

$grade_level = $student['grade_level'] ?? '';

// Fetch upcoming exams
$upcoming_exams = db()->fetchAll("
    SELECT * FROM examinations
    WHERE grade_level = ? AND exam_date >= CURDATE()
    ORDER BY exam_date, start_time
", [$grade_level]);

// Fetch recent results
$results = db()->fetchAll("
    SELECT er.*, e.exam_name, s.subject_name
    FROM exam_results er
    JOIN examinations e ON er.exam_id = e.id
    JOIN subjects s ON er.subject_id = s.id
    JOIN students st ON er.student_id = st.id
    WHERE st.user_id = ?
    ORDER BY e.exam_date DESC
    LIMIT 10
", [$student_id]);

include '../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <link rel="stylesheet" href="../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-clipboard-check"></i> <?php echo $page_title; ?></h1>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count($upcoming_exams); ?></div>
                    <div class="stat-label">Upcoming Exams</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count($results); ?></div>
                    <div class="stat-label">Results Available</div>
                </div>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-calendar-alt"></i> Upcoming Examinations</h3>
            </div>
            <div class="card-body">
                <?php if (empty($upcoming_exams)): ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar fa-3x"></i>
                        <p>No upcoming exams scheduled</p>
                    </div>
                <?php else: ?>
                    <div class="exam-list">
                        <?php foreach ($upcoming_exams as $exam): ?>
                            <div class="exam-card">
                                <div class="exam-header">
                                    <h4><?php echo htmlspecialchars($exam['exam_name']); ?></h4>
                                    <span class="badge badge-info"><?php echo ucfirst(str_replace('_', ' ', $exam['exam_type'])); ?></span>
                                </div>
                                <div class="exam-details">
                                    <div class="exam-detail-item">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('l, F d, Y', strtotime($exam['exam_date'])); ?>
                                    </div>
                                    <div class="exam-detail-item">
                                        <i class="fas fa-clock"></i>
                                        <?php echo date('g:i A', strtotime($exam['start_time'])) . ' - ' . date('g:i A', strtotime($exam['end_time'])); ?>
                                    </div>
                                    <div class="exam-detail-item">
                                        <i class="fas fa-award"></i>
                                        Total Marks: <?php echo $exam['total_marks']; ?> | Passing: <?php echo $exam['passing_marks']; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-chart-bar"></i> Recent Exam Results</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Exam Name</th>
                                <th>Subject</th>
                                <th>Marks Obtained</th>
                                <th>Total Marks</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $result): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($result['exam_name']); ?></td>
                                    <td><?php echo htmlspecialchars($result['subject_name']); ?></td>
                                    <td><strong><?php echo $result['marks_obtained']; ?></strong></td>
                                    <td><?php echo $result['total_marks']; ?></td>
                                    <td><?php echo number_format($result['percentage'], 2); ?>%</td>
                                    <td>
                                        <span class="badge badge-<?php
                                                                    echo $result['percentage'] >= 75 ? 'success' : ($result['percentage'] >= 60 ? 'info' : ($result['percentage'] >= 40 ? 'warning' : 'danger'));
                                                                    ?>">
                                            <?php echo $result['grade']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .exam-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .exam-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 20px;
        }

        .exam-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .exam-header h4 {
            margin: 0;
            color: var(--neon-cyan);
        }

        .exam-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .exam-detail-item {
            color: var(--text-secondary);
            font-size: 0.95em;
        }

        .exam-detail-item i {
            margin-right: 10px;
            color: var(--neon-cyan);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state i {
            color: var(--neon-cyan);
            margin-bottom: 20px;
        }
    </style>
</body>

</html>
