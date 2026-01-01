<?php
/**
 * Complaints - Student Portal
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_role('student');

$page_title = "Complaints & Feedback";
$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complaint'])) {
    $_SESSION['success_message'] = "Your complaint has been submitted successfully. We'll respond within 24 hours.";
    header('Location: complaints.php');
    exit;
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
            <h1><i class="fas fa-exclamation-circle"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Complaints</span>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-plus-circle"></i> Submit New Complaint</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <option value="hostel">Hostel/Room Issue</option>
                            <option value="mess">Mess/Food Quality</option>
                            <option value="academic">Academic Issue</option>
                            <option value="transport">Transport Issue</option>
                            <option value="fees">Fee/Payment Issue</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="Brief subject of your complaint" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="complaint" class="form-control" rows="5" placeholder="Describe your complaint in detail..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Priority</label>
                        <select name="priority" class="form-control">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Submit Complaint
                    </button>
                </form>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> My Previous Complaints</h3>
            </div>
            <div class="card-body">
                <div class="empty-state" style="text-align: center; padding: 40px;">
                    <i class="fas fa-smile" style="font-size: 4rem; opacity: 0.3; color: var(--cyber-cyan);"></i>
                    <h3>No Complaints Yet</h3>
                    <p>You haven't submitted any complaints. If you have an issue, use the form above.</p>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/chatbot-unified.php'; ?>
</body>
</html>
