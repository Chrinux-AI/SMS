<?php

/**
 * Examination Management - Admin Panel
 * Schedule and manage examinations
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Examination Management";
$current_page = "academics/exams.php";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $stmt = db()->prepare("INSERT INTO examinations (exam_name, exam_type, grade_level, academic_year, exam_date, start_time, end_time, total_marks, passing_marks, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            sanitize_input($_POST['exam_name']),
            $_POST['exam_type'],
            sanitize_input($_POST['grade_level']),
            sanitize_input($_POST['academic_year']),
            sanitize_input($_POST['exam_date']),
            sanitize_input($_POST['start_time']),
            sanitize_input($_POST['end_time']),
            intval($_POST['total_marks']),
            intval($_POST['passing_marks']),
            sanitize_input($_POST['description'])
        ]);

        $_SESSION['success_message'] = "Examination scheduled successfully!";
        header("Location: exams.php");
        exit;
    }
}

// Fetch examinations
$exams = db()->fetchAll("SELECT * FROM examinations ORDER BY exam_date DESC");

$upcoming = count(array_filter($exams, fn($e) => strtotime($e['exam_date']) >= strtotime(date('Y-m-d'))));
$completed = count(array_filter($exams, fn($e) => strtotime($e['exam_date']) < strtotime(date('Y-m-d'))));

include '../../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <link rel="stylesheet" href="../../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-clipboard-check"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Academics</span>
                <span>/</span>
                <span>Examinations</span>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $upcoming; ?></div>
                    <div class="stat-label">Upcoming Exams</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $completed; ?></div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count($exams); ?></div>
                    <div class="stat-label">Total Exams</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Schedule Exam
            </button>
            <button class="btn btn-success">
                <i class="fas fa-calendar-alt"></i> Exam Calendar
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Examinations</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Exam Name</th>
                                <th>Type</th>
                                <th>Grade</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Total Marks</th>
                                <th>Passing Marks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exams as $exam):
                                $is_upcoming = strtotime($exam['exam_date']) >= strtotime(date('Y-m-d'));
                            ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($exam['exam_name']); ?></strong></td>
                                    <td><span class="badge badge-info"><?php echo ucfirst(str_replace('_', ' ', $exam['exam_type'])); ?></span></td>
                                    <td><?php echo htmlspecialchars($exam['grade_level']); ?></td>
                                    <td>
                                        <?php if ($is_upcoming): ?>
                                            <span class="text-success"><i class="fas fa-calendar-day"></i> <?php echo date('M d, Y', strtotime($exam['exam_date'])); ?></span>
                                        <?php else: ?>
                                            <?php echo date('M d, Y', strtotime($exam['exam_date'])); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('g:i A', strtotime($exam['start_time'])) . ' - ' . date('g:i A', strtotime($exam['end_time'])); ?></td>
                                    <td><?php echo $exam['total_marks']; ?></td>
                                    <td><?php echo $exam['passing_marks']; ?></td>
                                    <td>
                                        <button class="btn-icon btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-success" title="Enter Results">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Exam Modal -->
    <div id="examModal" class="modal">
        <div class="modal-content large-modal">
            <div class="modal-header">
                <h3><i class="fas fa-clipboard-check"></i> Schedule Examination</h3>
                <span class="modal-close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Exam Name <span class="required">*</span></label>
                        <input type="text" name="exam_name" required>
                    </div>
                    <div class="form-group">
                        <label>Exam Type <span class="required">*</span></label>
                        <select name="exam_type" required>
                            <option value="unit_test">Unit Test</option>
                            <option value="mid_term">Mid Term</option>
                            <option value="final">Final Exam</option>
                            <option value="practical">Practical</option>
                            <option value="oral">Oral Exam</option>
                            <option value="project">Project</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Grade Level <span class="required">*</span></label>
                        <input type="text" name="grade_level" required placeholder="e.g., Grade 10">
                    </div>
                    <div class="form-group">
                        <label>Academic Year <span class="required">*</span></label>
                        <input type="text" name="academic_year" value="2024-2025" required>
                    </div>
                    <div class="form-group">
                        <label>Exam Date <span class="required">*</span></label>
                        <input type="date" name="exam_date" required>
                    </div>
                    <div class="form-group">
                        <label>Start Time <span class="required">*</span></label>
                        <input type="time" name="start_time" required>
                    </div>
                    <div class="form-group">
                        <label>End Time <span class="required">*</span></label>
                        <input type="time" name="end_time" required>
                    </div>
                    <div class="form-group">
                        <label>Total Marks <span class="required">*</span></label>
                        <input type="number" name="total_marks" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Passing Marks <span class="required">*</span></label>
                        <input type="number" name="passing_marks" min="1" required>
                    </div>
                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Schedule Exam
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('examModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('examModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('examModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>
