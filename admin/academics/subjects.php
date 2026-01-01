<?php

/**
 * Subject Management - Admin Panel
 * Manage all school subjects
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Check if user is admin
require_role('admin');

$page_title = "Subject Management";
$current_page = "academics/subjects.php";

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $subject_code = sanitize_input($_POST['subject_code']);
        $subject_name = sanitize_input($_POST['subject_name']);
        $subject_type = $_POST['subject_type'];
        $grade_level = sanitize_input($_POST['grade_level']);
        $credit_hours = floatval($_POST['credit_hours']);
        $description = sanitize_input($_POST['description']);

        $stmt = db()->prepare("INSERT INTO subjects (subject_code, subject_name, subject_type, grade_level, credit_hours, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$subject_code, $subject_name, $subject_type, $grade_level, $credit_hours, $description]);

        $_SESSION['success_message'] = "Subject added successfully!";
        header("Location: subjects.php");
        exit;
    }

    if ($action === 'edit') {
        $id = intval($_POST['id']);
        $subject_name = sanitize_input($_POST['subject_name']);
        $subject_type = $_POST['subject_type'];
        $grade_level = sanitize_input($_POST['grade_level']);
        $credit_hours = floatval($_POST['credit_hours']);
        $description = sanitize_input($_POST['description']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        $stmt = db()->prepare("UPDATE subjects SET subject_name = ?, subject_type = ?, grade_level = ?, credit_hours = ?, description = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$subject_name, $subject_type, $grade_level, $credit_hours, $description, $is_active, $id]);

        $_SESSION['success_message'] = "Subject updated successfully!";
        header("Location: subjects.php");
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = db()->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['success_message'] = "Subject deleted successfully!";
        header("Location: subjects.php");
        exit;
    }
}

// Fetch all subjects
$subjects = db()->fetchAll("SELECT * FROM subjects ORDER BY grade_level, subject_name");

// Get statistics
$total_subjects = count($subjects);
$active_subjects = count(array_filter($subjects, fn($s) => $s['is_active'] == 1));

include '../../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - School Management System</title>
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
        <!-- Header -->
        <div class="page-header">
            <h1><i class="fas fa-book"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Academics</span>
                <span>/</span>
                <span>Subjects</span>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_subjects; ?></div>
                    <div class="stat-label">Total Subjects</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $active_subjects; ?></div>
                    <div class="stat-label">Active Subjects</div>
                </div>
            </div>
        </div>

        <!-- Add Subject Button -->
        <div class="page-actions">
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add New Subject
            </button>
        </div>

        <!-- Subjects Table -->
        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Subjects</h3>
                <div class="card-actions">
                    <input type="text" id="searchInput" placeholder="Search subjects..." class="search-input">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="cyber-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Subject Name</th>
                                <th>Type</th>
                                <th>Grade Level</th>
                                <th>Credit Hours</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="subjectsTable">
                            <?php foreach ($subjects as $subject): ?>
                                <tr>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars($subject['subject_code']); ?></span></td>
                                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                    <td><span class="badge badge-<?php echo $subject['subject_type'] === 'core' ? 'success' : 'info'; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $subject['subject_type'])); ?>
                                        </span></td>
                                    <td><?php echo htmlspecialchars($subject['grade_level']); ?></td>
                                    <td><?php echo $subject['credit_hours']; ?></td>
                                    <td>
                                        <?php if ($subject['is_active']): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><i class="fas fa-times"></i> Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn-icon btn-edit" onclick='editSubject(<?php echo json_encode($subject); ?>)' title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-delete" onclick="deleteSubject(<?php echo $subject['id']; ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
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

    <!-- Add/Edit Subject Modal -->
    <div id="subjectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle"><i class="fas fa-book"></i> Add New Subject</h3>
                <span class="modal-close" onclick="closeModal()">&times;</span>
            </div>
            <form id="subjectForm" method="POST" action="">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="subjectId">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Subject Code <span class="required">*</span></label>
                        <input type="text" name="subject_code" id="subject_code" required>
                    </div>
                    <div class="form-group">
                        <label>Subject Name <span class="required">*</span></label>
                        <input type="text" name="subject_name" id="subject_name" required>
                    </div>
                    <div class="form-group">
                        <label>Subject Type <span class="required">*</span></label>
                        <select name="subject_type" id="subject_type" required>
                            <option value="core">Core</option>
                            <option value="elective">Elective</option>
                            <option value="optional">Optional</option>
                            <option value="extra_curricular">Extra Curricular</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Grade Level</label>
                        <input type="text" name="grade_level" id="grade_level" placeholder="e.g., Grade 1-5, High School">
                    </div>
                    <div class="form-group">
                        <label>Credit Hours</label>
                        <input type="number" name="credit_hours" id="credit_hours" step="0.5" min="0" value="0">
                    </div>
                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description" id="description" rows="3"></textarea>
                    </div>
                    <div class="form-group full-width" id="statusGroup" style="display:none;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" id="is_active" checked>
                            <span>Active</span>
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Subject
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Open add modal
        function openAddModal() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-book"></i> Add New Subject';
            document.getElementById('formAction').value = 'add';
            document.getElementById('subjectForm').reset();
            document.getElementById('subject_code').readOnly = false;
            document.getElementById('statusGroup').style.display = 'none';
            document.getElementById('subjectModal').style.display = 'block';
        }

        // Edit subject
        function editSubject(subject) {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Subject';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('subjectId').value = subject.id;
            document.getElementById('subject_code').value = subject.subject_code;
            document.getElementById('subject_code').readOnly = true;
            document.getElementById('subject_name').value = subject.subject_name;
            document.getElementById('subject_type').value = subject.subject_type;
            document.getElementById('grade_level').value = subject.grade_level || '';
            document.getElementById('credit_hours').value = subject.credit_hours;
            document.getElementById('description').value = subject.description || '';
            document.getElementById('is_active').checked = subject.is_active == 1;
            document.getElementById('statusGroup').style.display = 'block';
            document.getElementById('subjectModal').style.display = 'block';
        }

        // Delete subject
        function deleteSubject(id) {
            if (confirm('Are you sure you want to delete this subject? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modal
        function closeModal() {
            document.getElementById('subjectModal').style.display = 'none';
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#subjectsTable tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('subjectModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>
