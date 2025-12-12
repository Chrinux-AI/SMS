<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('alumni', '../../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

$page_title = 'Alumni Dashboard';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?> - <?php echo APP_NAME; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/cyberpunk-ui.css">
</head>

<body class="cyber-bg">
  <?php include '../../includes/cyber-nav.php'; ?>

  <div class="cyber-layout">
    <main class="cyber-main">
      <div class="page-header">
        <h1><i class="fas fa-user-graduate"></i> <?php echo $page_title; ?></h1>
        <p>Welcome back, <?php echo htmlspecialchars($full_name); ?>!</p>
      </div>

      <div class="orb-grid">
        <div class="stat-orb">
          <div class="orb-icon-wrapper cyan">
            <i class="fas fa-calendar-alt"></i>
          </div>
          <div class="orb-content">
            <span class="orb-value">0</span>
            <span class="orb-label">Upcoming Events</span>
          </div>
        </div>

        <div class="stat-orb">
          <div class="orb-icon-wrapper green">
            <i class="fas fa-users"></i>
          </div>
          <div class="orb-content">
            <span class="orb-value">0</span>
            <span class="orb-label">Network Connections</span>
          </div>
        </div>

        <div class="stat-orb">
          <div class="orb-icon-wrapper purple">
            <i class="fas fa-briefcase"></i>
          </div>
          <div class="orb-content">
            <span class="orb-value">0</span>
            <span class="orb-label">Job Opportunities</span>
          </div>
        </div>

        <div class="stat-orb">
          <div class="orb-icon-wrapper gold">
            <i class="fas fa-heart"></i>
          </div>
          <div class="orb-content">
            <span class="orb-value">0</span>
            <span class="orb-label">Donations Made</span>
          </div>
        </div>
      </div>

      <div class="holo-card">
        <h2><i class="fas fa-info-circle"></i> Alumni Portal</h2>
        <p>Welcome to the Alumni Network! Stay connected with your alma mater.</p>
        <ul style="margin: 20px 0; padding-left: 20px;">
          <li>View upcoming reunions and events</li>
          <li>Connect with fellow alumni</li>
          <li>Access job boards and career resources</li>
          <li>Support current students through mentorship</li>
        </ul>
      </div>
    </main>
  </div>

  <?php include '../../includes/theme-toggle.php'; ?>
</body>

</html>
