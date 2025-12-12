<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_role('general', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

$page_title = 'General Dashboard';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?> - <?php echo APP_NAME; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
</head>

<body class="cyber-bg">
  <?php include '../includes/cyber-nav.php'; ?>

  <div class="cyber-layout">
    <main class="cyber-main">
      <div class="page-header">
        <h1><i class="fas fa-user"></i> <?php echo $page_title; ?></h1>
        <p>Welcome back, <?php echo htmlspecialchars($full_name); ?>!</p>
      </div>

      <div class="holo-card">
        <h2><i class="fas fa-info-circle"></i> Welcome</h2>
        <p>Welcome to the School Management System. As a general user, you have access to:</p>
        <ul style="margin: 20px 0; padding-left: 20px;">
          <li>View announcements and notices</li>
          <li>Access help documentation</li>
          <li>Contact support</li>
          <li>Update your profile</li>
        </ul>
      </div>

      <div class="holo-card">
        <h2><i class="fas fa-link"></i> Quick Links</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
          <a href="help.php" class="cyber-btn">
            <i class="fas fa-question-circle"></i> Help Center
          </a>
          <a href="faq.php" class="cyber-btn secondary">
            <i class="fas fa-list"></i> FAQ
          </a>
          <a href="settings.php" class="cyber-btn secondary">
            <i class="fas fa-cog"></i> Settings
          </a>
        </div>
      </div>
    </main>
  </div>

  <?php include '../includes/theme-toggle.php'; ?>
</body>

</html>
