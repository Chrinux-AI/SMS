<?php
/**
 * Make Payment - Student Portal
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_role('student');

$page_title = "Make Payment";
$user_id = $_SESSION['user_id'];

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
            <h1><i class="fas fa-credit-card"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Make Payment</span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-details">
                    <div class="stat-value">₦0</div>
                    <div class="stat-label">Outstanding Balance</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-details">
                    <div class="stat-value">₦0</div>
                    <div class="stat-label">Paid This Term</div>
                </div>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-file-invoice"></i> Outstanding Invoices</h3>
            </div>
            <div class="card-body">
                <div class="empty-state" style="text-align: center; padding: 40px;">
                    <i class="fas fa-smile" style="font-size: 4rem; opacity: 0.3; margin-bottom: 20px; color: var(--cyber-cyan);"></i>
                    <h3>All Caught Up!</h3>
                    <p>You have no outstanding payments at this time.</p>
                </div>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-money-check"></i> Payment Methods</h3>
            </div>
            <div class="card-body">
                <div class="payment-methods" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                    <button class="btn btn-lg btn-primary" onclick="payWithCard()" style="padding: 30px;">
                        <i class="fas fa-credit-card" style="font-size: 2rem;"></i>
                        <br>Pay with Card
                    </button>
                    <button class="btn btn-lg btn-secondary" onclick="payWithBank()" style="padding: 30px;">
                        <i class="fas fa-university" style="font-size: 2rem;"></i>
                        <br>Bank Transfer
                    </button>
                    <button class="btn btn-lg btn-secondary" onclick="payWithUSSD()" style="padding: 30px;">
                        <i class="fas fa-mobile-alt" style="font-size: 2rem;"></i>
                        <br>USSD
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/chatbot-unified.php'; ?>

    <script>
        function payWithCard() { alert('Card payment integration coming soon! (Paystack/Flutterwave)'); }
        function payWithBank() { alert('Bank transfer details will be displayed here.'); }
        function payWithUSSD() { alert('USSD payment coming soon!'); }
    </script>
</body>
</html>
