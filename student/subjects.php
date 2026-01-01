<?php
/**
 * My Subjects - Student Portal
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_role('student');

$page_title = "My Subjects";
$user_id = $_SESSION['user_id'];

// Fetch student's enrolled subjects
try {
    $subjects = db()->fetchAll("
        SELECT DISTINCT s.* FROM subjects s
        INNER JOIN class_subjects cs ON s.id = cs.subject_id
        INNER JOIN student_classes sc ON cs.class_id = sc.class_id
        WHERE sc.student_id = ?
        ORDER BY s.name
    ", [$user_id]) ?? [];
} catch (Exception $e) {
    $subjects = [];
}

include '../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <?php include '../includes/head-meta.php'; ?>
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book-open"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>My Subjects</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-details">
                    <div class="stat-value"><?php echo count($subjects); ?></div>
                    <div class="stat-label">Enrolled Subjects</div>
                </div>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> My Enrolled Subjects</h3>
            </div>
            <div class="card-body">
                <?php if (empty($subjects)): ?>
                    <div class="empty-state" style="text-align: center; padding: 40px;">
                        <i class="fas fa-book-open" style="font-size: 4rem; opacity: 0.3; margin-bottom: 20px;"></i>
                        <h3>No Subjects Yet</h3>
                        <p>You haven't been enrolled in any subjects yet. Contact your class teacher.</p>
                    </div>
                <?php else: ?>
                    <div class="subjects-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                        <?php foreach ($subjects as $subject): ?>
                            <div class="subject-card cyber-card" style="padding: 20px;">
                                <h4><i class="fas fa-book"></i> <?php echo htmlspecialchars($subject['name']); ?></h4>
                                <p style="color: var(--text-muted);"><?php echo htmlspecialchars($subject['code'] ?? 'N/A'); ?></p>
                                <div class="subject-meta" style="margin-top: 15px;">
                                    <span class="badge badge-info">Active</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/chatbot-unified.php'; ?>
</body>
</html>
