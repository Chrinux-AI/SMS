<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('owner', '../../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Master Stats
$total_schools = db()->fetchOne("SELECT COUNT(*) as count FROM schools")['count'] ?? 1;
$total_students = db()->fetchOne("SELECT COUNT(*) as count FROM students WHERE is_active = 1")['count'] ?? 0;
$total_staff = db()->fetchOne("SELECT COUNT(*) as count FROM users WHERE role != 'student' AND is_active = 1")['count'] ?? 0;

$page_title = 'Owner Dashboard';
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
        <h1><i class="fas fa-crown"></i> <?php echo $page_title; ?></h1>
        <p>Welcome back, <?php echo htmlspecialchars($full_name); ?>!</p>
      </div>

      <div class="orb-grid">
        <div class="stat-orb">
          <div class="orb-icon-wrapper gold">
            <i class="fas fa-school"></i>
          </div>
          <div class="orb-content">
            <span class="orb-value"><?php echo number_format($total_schools); ?></span>
            <span class="orb-label">Schools Managed</span>
          </div>
        </div>

        <div class="stat-orb">
          <div class="orb-icon-wrapper cyan">
            <i class="fas fa-user-graduate"></i>
          </div>
          <div class="orb-content">
            <span class="orb-value"><?php echo number_format($total_students); ?></span>
            <span class="orb-label">Total Students</span>
          </div>
        </div>

        <div class="stat-orb">
          <div class="orb-icon-wrapper green">
            <i class="fas fa-users-cog"></i>
          </div>
          <div class="orb-content">
            <span class="orb-value"><?php echo number_format($total_staff); ?></span>
            <span class="orb-label">Total Staff</span>
          </div>
        </div>

        <div class="stat-orb">
          <div class="orb-icon-wrapper purple">
            <i class="fas fa-chart-line"></i>
          </div>
          <div class="orb-content">
            <span class="orb-value">$0</span>
            <span class="orb-label">Monthly Revenue</span>
          </div>
        </div>
      </div>

      <div class="holo-card">
        <h2><i class="fas fa-cog"></i> System Management</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
          <a href="../superadmin/dashboard.php" class="cyber-btn">
            <i class="fas fa-tachometer-alt"></i> Superadmin Panel
          </a>
          <a href="../admin/settings.php" class="cyber-btn secondary">
            <i class="fas fa-sliders-h"></i> System Settings
          </a>
          <a href="../admin/reports.php" class="cyber-btn secondary">
            <i class="fas fa-file-alt"></i> Financial Reports
          </a>
        </div>
      </div>
    </main>
  </div>

  <?php include '../../includes/theme-toggle.php'; ?>
</body>

</html>
