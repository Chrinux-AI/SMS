<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('accountant', '../../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Financial Stats
$total_revenue = db()->fetchOne("SELECT IFNULL(SUM(amount_paid), 0) as total FROM fee_payments WHERE MONTH(payment_date) = MONTH(CURDATE())")['total'] ?? 0;
$pending_fees = db()->fetchOne("SELECT IFNULL(SUM(amount_due - amount_paid), 0) as total FROM student_fees WHERE status = 'pending'")['total'] ?? 0;
$total_expenses = db()->fetchOne("SELECT IFNULL(SUM(amount), 0) as total FROM expenses WHERE MONTH(expense_date) = MONTH(CURDATE())")['total'] ?? 0;

$page_title = 'Accountant Dashboard';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?> - <?php echo APP_NAME; ?></title>
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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../assets/css/cyberpunk-ui.css">
</head>

<body class="cyber-bg">
  <?php include '../../includes/cyber-nav.php'; ?>

  <div class="cyber-layout">
    <main class="cyber-main">
      <div class="page-header">
        <h1><i class="fas fa-calculator"></i> <?php echo $page_title; ?></h1>
        <p>Welcome back, <?php echo htmlspecialchars($full_name); ?>!</p>
      </div>

      <div class="orb-grid">
        <div class="stat-orb">
          <div class="orb-icon-wrapper green">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <div class="orb-content">
            <span class="orb-value">$<?php echo number_format($total_revenue, 2); ?></span>
            <span class="orb-label">Monthly Revenue</span>
          </div>
        </div>

        <div class="stat-orb">
          <div class="orb-icon-wrapper gold">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="orb-content">
            <span class="orb-value">$<?php echo number_format($pending_fees, 2); ?></span>
            <span class="orb-label">Pending Fees</span>
          </div>
        </div>

        <div class="stat-orb">
          <div class="orb-icon-wrapper red">
            <i class="fas fa-receipt"></i>
          </div>
          <div class="orb-content">
            <span class="orb-value">$<?php echo number_format($total_expenses, 2); ?></span>
            <span class="orb-label">Monthly Expenses</span>
          </div>
        </div>

        <div class="stat-orb">
          <div class="orb-icon-wrapper cyan">
            <i class="fas fa-balance-scale"></i>
          </div>
          <div class="orb-content">
            <span class="orb-value">$<?php echo number_format($total_revenue - $total_expenses, 2); ?></span>
            <span class="orb-label">Net Balance</span>
          </div>
        </div>
      </div>

      <div class="holo-card">
        <h2><i class="fas fa-tasks"></i> Quick Actions</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
          <a href="../admin/fee-management.php" class="cyber-btn">
            <i class="fas fa-file-invoice-dollar"></i> Fee Management
          </a>
          <a href="../admin/reports.php" class="cyber-btn secondary">
            <i class="fas fa-chart-bar"></i> Financial Reports
          </a>
          <a href="settings/index.php" class="cyber-btn secondary">
            <i class="fas fa-cog"></i> Settings
          </a>
        </div>
      </div>
    </main>
  </div>

  <?php include '../../includes/theme-toggle.php'; ?>
</body>

</html>
