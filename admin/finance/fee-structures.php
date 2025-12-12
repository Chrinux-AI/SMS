<?php

/**
 * Fee Structure Management - Admin Panel
 * Configure fee structures for different grades and fee types
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Fee Structure Management";
$current_page = "finance/fee-structures.php";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $stmt = db()->prepare("INSERT INTO fee_structures (fee_name, fee_type, grade_level, amount, frequency, due_day, description, is_mandatory, academic_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            sanitize_input($_POST['fee_name']),
            $_POST['fee_type'],
            sanitize_input($_POST['grade_level']),
            floatval($_POST['amount']),
            $_POST['frequency'],
            intval($_POST['due_day']),
            sanitize_input($_POST['description']),
            isset($_POST['is_mandatory']) ? 1 : 0,
            sanitize_input($_POST['academic_year'])
        ]);

        $_SESSION['success_message'] = "Fee structure added successfully!";
        header("Location: fee-structures.php");
        exit;
    }
}

// Fetch fee structures
$fee_structures = db()->fetchAll("SELECT * FROM fee_structures ORDER BY academic_year DESC, grade_level, fee_type");

$total_structures = count($fee_structures);
$active_structures = count(array_filter($fee_structures, fn($f) => $f['is_active'] == 1));

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
            <h1><i class="fas fa-money-bill-wave"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Finance</span>
                <span>/</span>
                <span>Fee Structures</span>
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
                <div class="stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $total_structures; ?></div>
                    <div class="stat-label">Fee Structures</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo $active_structures; ?></div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
        </div>

        <div class="page-actions">
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add Fee Structure
            </button>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Fee Structures</h3>
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
                                <th>Fee Name</th>
                                <th>Type</th>
                                <th>Grade</th>
                                <th>Amount</th>
                                <th>Frequency</th>
                                <th>Academic Year</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fee_structures as $fee): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fee['fee_name']); ?></td>
                                    <td><span class="badge badge-info"><?php echo ucfirst($fee['fee_type']); ?></span></td>
                                    <td><?php echo htmlspecialchars($fee['grade_level']); ?></td>
                                    <td><strong>₹<?php echo number_format($fee['amount'], 2); ?></strong></td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $fee['frequency'])); ?></td>
                                    <td><?php echo htmlspecialchars($fee['academic_year']); ?></td>
                                    <td>
                                        <?php if ($fee['is_active']): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
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

    <!-- Add Fee Structure Modal -->
    <div id="feeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus"></i> Add Fee Structure</h3>
                <span class="modal-close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Fee Name <span class="required">*</span></label>
                        <input type="text" name="fee_name" required>
                    </div>
                    <div class="form-group">
                        <label>Fee Type <span class="required">*</span></label>
                        <select name="fee_type" required>
                            <option value="tuition">Tuition Fee</option>
                            <option value="admission">Admission Fee</option>
                            <option value="annual">Annual Fee</option>
                            <option value="exam">Examination Fee</option>
                            <option value="transport">Transport Fee</option>
                            <option value="hostel">Hostel Fee</option>
                            <option value="library">Library Fee</option>
                            <option value="sports">Sports Fee</option>
                            <option value="lab">Laboratory Fee</option>
                            <option value="misc">Miscellaneous</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Grade Level</label>
                        <input type="text" name="grade_level" placeholder="e.g., Grade 1-5, All">
                    </div>
                    <div class="form-group">
                        <label>Amount (₹) <span class="required">*</span></label>
                        <input type="number" name="amount" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Frequency <span class="required">*</span></label>
                        <select name="frequency" required>
                            <option value="one_time">One Time</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="half_yearly">Half Yearly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Due Day (of month)</label>
                        <input type="number" name="due_day" min="1" max="31" value="5">
                    </div>
                    <div class="form-group">
                        <label>Academic Year <span class="required">*</span></label>
                        <input type="text" name="academic_year" value="2024-2025" required>
                    </div>
                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_mandatory" checked>
                            <span>Mandatory Fee</span>
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Fee Structure
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('feeModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('feeModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('feeModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>
