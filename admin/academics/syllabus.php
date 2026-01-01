<?php

/**
 * Syllabus Management - Admin Panel
 * Manage syllabus for subjects across grades
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Syllabus Management";
$current_page = "academics/syllabus.php";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $stmt = db()->prepare("INSERT INTO syllabus (subject_id, grade_level, academic_year, topic_name, description, duration_weeks, learning_outcomes, resources, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'draft')");
        $stmt->execute([
            intval($_POST['subject_id']),
            sanitize_input($_POST['grade_level']),
            sanitize_input($_POST['academic_year']),
            sanitize_input($_POST['topic_name']),
            sanitize_input($_POST['description']),
            intval($_POST['duration_weeks']),
            sanitize_input($_POST['learning_outcomes']),
            sanitize_input($_POST['resources'])
        ]);

        $_SESSION['success_message'] = "Syllabus topic added successfully!";
        header("Location: syllabus.php");
        exit;
    }
}

// Fetch subjects and syllabus
$subjects = db()->fetchAll("SELECT * FROM subjects WHERE is_active = 1 ORDER BY subject_name");
$syllabus_items = db()->fetchAll("SELECT s.*, sub.subject_name FROM syllabus s JOIN subjects sub ON s.subject_id = sub.id ORDER BY s.academic_year DESC, s.grade_level, sub.subject_name");

include '../../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
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
    <link rel="stylesheet" href="../../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book-reader"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Academics</span>
                <span>/</span>
                <span>Syllabus</span>
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
                <div class="stat-icon"><i class="fas fa-list"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count($syllabus_items); ?></div>
                    <div class="stat-label">Total Topics</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count($subjects); ?></div>
                    <div class="stat-label">Subjects</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add Syllabus Topic
            </button>
            <button class="btn btn-success">
                <i class="fas fa-file-export"></i> Export Syllabus
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Syllabus Topics</h3>
                <div class="card-actions">
                    <select id="filterGrade" class="filter-select">
                        <option value="">All Grades</option>
                        <option value="Grade 1">Grade 1</option>
                        <option value="Grade 2">Grade 2</option>
                        <option value="High School">High School</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Grade</th>
                                <th>Topic</th>
                                <th>Duration</th>
                                <th>Academic Year</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($syllabus_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['subject_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['grade_level']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($item['topic_name']); ?></strong></td>
                                    <td><?php echo $item['duration_weeks']; ?> weeks</td>
                                    <td><?php echo htmlspecialchars($item['academic_year']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $item['status'] == 'approved' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($item['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-icon btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
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

    <!-- Add Syllabus Modal -->
    <div id="syllabusModal" class="modal">
        <div class="modal-content large-modal">
            <div class="modal-header">
                <h3><i class="fas fa-plus"></i> Add Syllabus Topic</h3>
                <span class="modal-close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Subject <span class="required">*</span></label>
                        <select name="subject_id" required>
                            <option value="">Select Subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo $subject['id']; ?>">
                                    <?php echo htmlspecialchars($subject['subject_name'] . ' (' . $subject['grade_level'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Grade Level <span class="required">*</span></label>
                        <input type="text" name="grade_level" required placeholder="e.g., Grade 1, Grade 10">
                    </div>
                    <div class="form-group">
                        <label>Academic Year <span class="required">*</span></label>
                        <input type="text" name="academic_year" value="2024-2025" required>
                    </div>
                    <div class="form-group">
                        <label>Duration (Weeks) <span class="required">*</span></label>
                        <input type="number" name="duration_weeks" min="1" max="52" required>
                    </div>
                    <div class="form-group full-width">
                        <label>Topic Name <span class="required">*</span></label>
                        <input type="text" name="topic_name" required>
                    </div>
                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Learning Outcomes</label>
                        <textarea name="learning_outcomes" rows="4" placeholder="List the key learning outcomes..."></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Resources</label>
                        <textarea name="resources" rows="3" placeholder="Textbooks, videos, websites..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Topic
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('syllabusModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('syllabusModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('syllabusModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>
