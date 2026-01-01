<?php
/**
 * Accountant Portal - Complete Homepage
 * Financial Command Center with collections, payments, and payroll
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$accountantName = $_SESSION['full_name'] ?? 'Accountant';
$greeting = date('H') < 12 ? 'Good Morning' : (date('H') < 17 ? 'Good Afternoon' : 'Good Evening');

$pageTitle = "Accountant Dashboard";
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00D4FF;
            --success: #00FF87;
            --warning: #FFB800;
            --danger: #FF4757;
            --purple: #A855F7;
            --bg-dark: #0B0F19;
            --bg-card: #111827;
            --border: rgba(255,255,255,0.08);
            --text: #E5E7EB;
            --text-muted: #9CA3AF;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            color: var(--text);
            min-height: 100vh;
            padding: 1.5rem;
        }

        .container { max-width: 1400px; margin: 0 auto; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .header h1 span {
            background: linear-gradient(90deg, var(--success), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header p { color: var(--text-muted); font-size: 0.9rem; }

        /* ===== FINANCE CARDS ===== */
        .finance-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .finance-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .finance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .finance-card.collected::before { background: var(--success); }
        .finance-card.outstanding::before { background: var(--warning); }
        .finance-card.expenses::before { background: var(--danger); }
        .finance-card.profit::before { background: var(--purple); }

        .finance-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .finance-card.collected .finance-icon { background: rgba(0,255,135,0.15); color: var(--success); }
        .finance-card.outstanding .finance-icon { background: rgba(255,184,0,0.15); color: var(--warning); }
        .finance-card.expenses .finance-icon { background: rgba(255,71,87,0.15); color: var(--danger); }
        .finance-card.profit .finance-icon { background: rgba(168,85,247,0.15); color: var(--purple); }

        .finance-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .finance-label {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .finance-change {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
        }

        .finance-change.up { background: rgba(0,255,135,0.15); color: var(--success); }
        .finance-change.down { background: rgba(255,71,87,0.15); color: var(--danger); }

        /* ===== QUICK ACTIONS ===== */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            padding: 1.25rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            text-decoration: none;
            color: var(--text);
            transition: all 0.3s;
        }

        .quick-action:hover {
            background: rgba(0, 212, 255, 0.1);
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        .quick-action i {
            font-size: 1.5rem;
            color: var(--success);
        }

        .quick-action span {
            font-size: 0.85rem;
            font-weight: 500;
            text-align: center;
        }

        /* ===== MAIN GRID ===== */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card:last-child { margin-bottom: 0; }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-header h3 i { color: var(--primary); }

        .card-action {
            font-size: 0.8rem;
            color: var(--primary);
            text-decoration: none;
        }

        .card-body { padding: 1.5rem; }

        /* ===== PAYMENT LIST ===== */
        .payment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }

        .payment-item:last-child { margin-bottom: 0; }

        .payment-info h4 {
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 0.15rem;
        }

        .payment-info p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .payment-amount {
            font-size: 1rem;
            font-weight: 700;
            color: var(--success);
        }

        /* ===== PAYROLL WIDGET ===== */
        .payroll-widget {
            background: linear-gradient(135deg, rgba(168,85,247,0.15), rgba(0,212,255,0.1));
            border: 1px solid var(--purple);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .payroll-widget h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .payroll-widget h3 i { color: var(--purple); }

        .payroll-countdown {
            font-size: 4rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .payroll-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .payroll-amount {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--success);
        }

        /* ===== OUTSTANDING LIST ===== */
        .outstanding-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            background: rgba(255,184,0,0.1);
            border: 1px solid rgba(255,184,0,0.2);
            border-radius: 10px;
            margin-bottom: 0.5rem;
        }

        .outstanding-item:last-child { margin-bottom: 0; }

        .outstanding-item h4 {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .outstanding-item p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .outstanding-amount {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--warning);
        }

        @media (max-width: 1200px) {
            .finance-cards { grid-template-columns: repeat(2, 1fr); }
            .quick-actions { grid-template-columns: repeat(3, 1fr); }
            .main-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .quick-actions { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header class="header">
            <div>
                <h1><?= $greeting ?>, <span><?= htmlspecialchars(explode(' ', $accountantName)[0]) ?></span>!</h1>
                <p>Financial Command Center • <?= date('l, F j, Y') ?></p>
            </div>
        </header>

        <!-- FINANCE CARDS -->
        <div class="finance-cards">
            <div class="finance-card collected">
                <div class="finance-icon"><i class="fas fa-coins"></i></div>
                <div class="finance-value">₦2.45M</div>
                <div class="finance-label">Collected This Term</div>
                <span class="finance-change up"><i class="fas fa-arrow-up"></i> 15%</span>
            </div>
            <div class="finance-card outstanding">
                <div class="finance-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="finance-value">₦850K</div>
                <div class="finance-label">Outstanding Fees</div>
                <span class="finance-change down"><i class="fas fa-arrow-down"></i> 8%</span>
            </div>
            <div class="finance-card expenses">
                <div class="finance-icon"><i class="fas fa-receipt"></i></div>
                <div class="finance-value">₦1.2M</div>
                <div class="finance-label">Expenses This Month</div>
            </div>
            <div class="finance-card profit">
                <div class="finance-icon"><i class="fas fa-chart-pie"></i></div>
                <div class="finance-value">₦1.25M</div>
                <div class="finance-label">Net Surplus</div>
                <span class="finance-change up"><i class="fas fa-arrow-up"></i> 22%</span>
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="quick-actions">
            <a href="fee-collection.php" class="quick-action">
                <i class="fas fa-money-bill-wave"></i>
                <span>Accept Payment</span>
            </a>
            <a href="invoices/create.php" class="quick-action">
                <i class="fas fa-file-invoice"></i>
                <span>Generate Invoice</span>
            </a>
            <a href="expenses/add.php" class="quick-action">
                <i class="fas fa-wallet"></i>
                <span>Record Expense</span>
            </a>
            <a href="reports/" class="quick-action">
                <i class="fas fa-chart-bar"></i>
                <span>Financial Reports</span>
            </a>
            <a href="payroll/" class="quick-action">
                <i class="fas fa-users"></i>
                <span>Staff Payroll</span>
            </a>
        </div>

        <!-- MAIN GRID -->
        <div class="main-grid">
            <div>
                <!-- RECENT PAYMENTS -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-check-circle"></i> Today's Payments</h3>
                        <a href="payments/" class="card-action">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="payment-item">
                            <div class="payment-info">
                                <h4>Chinedu Okoro - JSS 2A</h4>
                                <p>School Fees • Today 10:30 AM</p>
                            </div>
                            <span class="payment-amount">₦45,000</span>
                        </div>
                        <div class="payment-item">
                            <div class="payment-info">
                                <h4>Adaeze Eze - SSS 1B</h4>
                                <p>School Fees • Today 9:15 AM</p>
                            </div>
                            <span class="payment-amount">₦55,000</span>
                        </div>
                        <div class="payment-item">
                            <div class="payment-info">
                                <h4>Emeka Nwosu - Primary 5</h4>
                                <p>Registration Fee • Today 8:45 AM</p>
                            </div>
                            <span class="payment-amount">₦15,000</span>
                        </div>
                        <div class="payment-item">
                            <div class="payment-info">
                                <h4>Kemi Adebayo - JSS 1A</h4>
                                <p>Transport Fee • Today 8:30 AM</p>
                            </div>
                            <span class="payment-amount">₦25,000</span>
                        </div>
                    </div>
                </div>

                <!-- EXPENSE SUMMARY -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-wallet"></i> Recent Expenses</h3>
                        <a href="expenses/" class="card-action">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="payment-item">
                            <div class="payment-info">
                                <h4>Staff Salaries - December</h4>
                                <p>Payroll • Dec 28, 2024</p>
                            </div>
                            <span class="payment-amount" style="color: var(--danger);">₦850,000</span>
                        </div>
                        <div class="payment-item">
                            <div class="payment-info">
                                <h4>Utility Bills</h4>
                                <p>Electricity & Water • Dec 25, 2024</p>
                            </div>
                            <span class="payment-amount" style="color: var(--danger);">₦120,000</span>
                        </div>
                        <div class="payment-item">
                            <div class="payment-info">
                                <h4>Laboratory Equipment</h4>
                                <p>Procurement • Dec 20, 2024</p>
                            </div>
                            <span class="payment-amount" style="color: var(--danger);">₦85,000</span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <!-- PAYROLL WIDGET -->
                <div class="payroll-widget">
                    <h3><i class="fas fa-calendar-check"></i> Next Payroll</h3>
                    <div class="payroll-countdown">27</div>
                    <div class="payroll-label">Days until staff salaries due</div>
                    <div class="payroll-amount">Total: ₦1,250,000</div>
                </div>

                <!-- TOP OUTSTANDING -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-exclamation-triangle"></i> Top Outstanding</h3>
                    </div>
                    <div class="card-body">
                        <div class="outstanding-item">
                            <div>
                                <h4>Oluwaseun Bakare</h4>
                                <p>SSS 2A • 3 terms overdue</p>
                            </div>
                            <span class="outstanding-amount">₦150,000</span>
                        </div>
                        <div class="outstanding-item">
                            <div>
                                <h4>Chioma Obi</h4>
                                <p>JSS 3B • 2 terms overdue</p>
                            </div>
                            <span class="outstanding-amount">₦95,000</span>
                        </div>
                        <div class="outstanding-item">
                            <div>
                                <h4>Tunde Ajayi</h4>
                                <p>Primary 6 • 1 term overdue</p>
                            </div>
                            <span class="outstanding-amount">₦65,000</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include dirname(__DIR__) . "/includes/ai-assistant.php"; ?>
</body>
</html>
