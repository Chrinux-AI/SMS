<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for TEST_MODE support
require_role('canteen', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Today's Sales
$today_sales = db()->fetchOne("
    SELECT IFNULL(SUM(total_amount), 0) as total,
           COUNT(*) as transaction_count
    FROM canteen_transactions
    WHERE DATE(transaction_date) = CURDATE()
") ?? ['total' => 0, 'transaction_count' => 0];

// Active Wallet Users
$active_wallets = db()->fetchOne("
    SELECT COUNT(*) as count
    FROM canteen_wallets
    WHERE balance > 0
")['count'] ?? 0;

// Low Stock Items
$low_stock = db()->fetchOne("
    SELECT COUNT(*) as count
    FROM canteen_inventory
    WHERE stock_quantity <= reorder_level
")['count'] ?? 0;

// Top Selling Items (Today)
$top_items = db()->fetchAll("
    SELECT ci.item_name,
           SUM(cti.quantity) as total_sold,
           SUM(cti.amount) as revenue
    FROM canteen_transaction_items cti
    JOIN canteen_items ci ON cti.item_id = ci.id
    JOIN canteen_transactions ct ON cti.transaction_id = ct.id
    WHERE DATE(ct.transaction_date) = CURDATE()
    GROUP BY ci.id
    ORDER BY total_sold DESC
    LIMIT 5
");

// Recent Transactions
$recent_transactions = db()->fetchAll("
    SELECT ct.*,
           CONCAT(u.first_name, ' ', u.last_name) as customer_name
    FROM canteen_transactions ct
    LEFT JOIN users u ON ct.user_id = u.id
    ORDER BY ct.transaction_date DESC
    LIMIT 10
");

// Monthly Revenue
$monthly_revenue = db()->fetchOne("
    SELECT IFNULL(SUM(total_amount), 0) as total
    FROM canteen_transactions
    WHERE MONTH(transaction_date) = MONTH(CURDATE())
")['total'] ?? 0;

$page_title = 'Canteen Management';
$page_icon = 'utensils';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/cyberpunk-ui.css" rel="stylesheet">
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb golden"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <button class="cyber-btn cyan" onclick="window.location.href='../admin/canteen/new-sale.php'">
                        <i class="fas fa-cash-register"></i> New Sale
                    </button>
                    <div class="user-card" style="padding:8px 15px;">
                        <div class="user-avatar golden" style="width:35px;height:35px;">
                            <?php echo strtoupper(substr($full_name, 0, 2)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($full_name); ?></div>
                            <div class="user-role">Canteen Manager</div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="cyber-content slide-in">

                <section class="orb-grid">
                    <div class="stat-orb">
                        <div class="stat-icon green"><i class="fas fa-rupee-sign"></i></div>
                        <div class="stat-content">
                            <div class="stat-value">₹<?php echo number_format($today_sales['total']); ?></div>
                            <div class="stat-label">Today's Sales</div>
                            <div class="stat-trend up"><i class="fas fa-chart-line"></i> <?php echo $today_sales['transaction_count']; ?> orders</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon cyan"><i class="fas fa-wallet"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $active_wallets; ?></div>
                            <div class="stat-label">Active Wallets</div>
                            <div class="stat-trend"><i class="fas fa-users"></i> Cashless</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon orange"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $low_stock; ?></div>
                            <div class="stat-label">Low Stock Items</div>
                            <div class="stat-trend down"><i class="fas fa-boxes"></i> Reorder</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon purple"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="stat-content">
                            <div class="stat-value">₹<?php echo number_format($monthly_revenue / 1000, 1); ?>K</div>
                            <div class="stat-label">Monthly Revenue</div>
                            <div class="stat-trend up"><i class="fas fa-calendar"></i> This Month</div>
                        </div>
                    </div>
                </section>

                <!-- Quick Actions -->
                <section class="holo-card" style="margin-top:30px;">
                    <h3 style="margin-bottom:20px;"><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
                        <a href="../admin/canteen/pos.php" class="action-card">
                            <div class="action-icon cyan"><i class="fas fa-cash-register"></i></div>
                            <h4>POS System</h4>
                            <p>Process sales</p>
                        </a>

                        <a href="../admin/canteen/inventory.php" class="action-card">
                            <div class="action-icon green"><i class="fas fa-boxes"></i></div>
                            <h4>Inventory</h4>
                            <p>Stock management</p>
                        </a>

                        <a href="../admin/canteen/wallets.php" class="action-card">
                            <div class="action-icon purple"><i class="fas fa-wallet"></i></div>
                            <h4>Wallet Topups</h4>
                            <p>Manage balances</p>
                        </a>

                        <a href="../admin/canteen/reports.php" class="action-card">
                            <div class="action-icon orange"><i class="fas fa-chart-pie"></i></div>
                            <h4>Sales Reports</h4>
                            <p>Analytics & trends</p>
                        </a>
                    </div>
                </section>

                <!-- Top Selling Items Today -->
                <?php if (!empty($top_items)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-fire"></i> Top Selling Items (Today)</h3>
                        <div class="cyber-table-container">
                            <table class="cyber-table">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Quantity Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                            <td><span class="cyber-badge cyan"><?php echo $item['total_sold']; ?> units</span></td>
                                            <td>₹<?php echo number_format($item['revenue'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Recent Transactions -->
                <?php if (!empty($recent_transactions)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-receipt"></i> Recent Transactions</h3>
                        <div class="cyber-table-container">
                            <table class="cyber-table">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_transactions as $txn): ?>
                                        <tr>
                                            <td><span class="cyber-badge purple">#<?php echo $txn['id']; ?></span></td>
                                            <td><?php echo htmlspecialchars($txn['customer_name'] ?? 'Guest'); ?></td>
                                            <td>₹<?php echo number_format($txn['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="cyber-badge <?php echo $txn['payment_method'] == 'wallet' ? 'cyan' : 'green'; ?>">
                                                    <?php echo ucfirst($txn['payment_method']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y h:i A', strtotime($txn['transaction_date'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>
