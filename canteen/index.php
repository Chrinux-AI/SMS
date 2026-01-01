<?php
/**
 * Canteen Portal - Food Services Hub
 */
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = "Canteen Management";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00D4FF;
            --success: #00FF87;
            --warning: #FFB800;
            --danger: #FF4757;
            --bg-dark: #0B0F19;
            --bg-card: #111827;
            --border: rgba(255,255,255,0.08);
            --text: #E5E7EB;
            --text-muted: #9CA3AF;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-dark); color: var(--text); padding: 1.5rem; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header h1 { font-size: 1.5rem; margin-bottom: 0.25rem; }
        .header h1 span { background: linear-gradient(90deg, var(--warning), var(--danger)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .header p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem; }
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 1.25rem; text-align: center; }
        .stat-value { font-size: 1.75rem; font-weight: 700; color: var(--success); }
        .stat-label { font-size: 0.8rem; color: var(--text-muted); }
        .pos-btn { display: block; background: linear-gradient(135deg, var(--success), var(--primary)); border: none; border-radius: 14px; padding: 1.5rem; text-align: center; color: #000; font-size: 1.25rem; font-weight: 700; text-decoration: none; margin-bottom: 2rem; transition: transform 0.2s; }
        .pos-btn:hover { transform: translateY(-3px); }
        .pos-btn i { margin-right: 0.5rem; }
        .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; }
        .card-header { padding: 1.25rem; border-bottom: 1px solid var(--border); }
        .card-header h3 { font-size: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .card-header h3 i { color: var(--warning); }
        .card-body { padding: 1.25rem; }
        .stock-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: rgba(0,0,0,0.2); border-radius: 8px; margin-bottom: 0.5rem; }
        .stock-item:last-child { margin-bottom: 0; }
        .stock-status { padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
        .stock-status.ok { background: rgba(0,255,135,0.15); color: var(--success); }
        .stock-status.low { background: rgba(255,184,0,0.15); color: var(--warning); }
        .stock-status.out { background: rgba(255,71,87,0.15); color: var(--danger); }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1><i class="fas fa-utensils"></i> <span>Canteen Management</span></h1>
            <p>Food Services Hub • <?= date('l, F j, Y') ?></p>
        </header>

        <a href="pos.php" class="pos-btn"><i class="fas fa-cash-register"></i> Open Point of Sale</a>

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-value">₦45K</div>
                <div class="stat-label">Today's Sales</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">312</div>
                <div class="stat-label">Transactions</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">24</div>
                <div class="stat-label">Menu Items</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: var(--warning);">3</div>
                <div class="stat-label">Low Stock</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-boxes"></i> Stock Alerts</h3>
            </div>
            <div class="card-body">
                <div class="stock-item">
                    <span>Soft Drinks</span>
                    <span class="stock-status low">Low - 12 left</span>
                </div>
                <div class="stock-item">
                    <span>Meat Pies</span>
                    <span class="stock-status out">Out of Stock</span>
                </div>
                <div class="stock-item">
                    <span>Rice (50kg bags)</span>
                    <span class="stock-status low">Low - 2 bags</span>
                </div>
                <div class="stock-item">
                    <span>Bread</span>
                    <span class="stock-status ok">OK - 45 loaves</span>
                </div>
            </div>
        </div>
    </div>
    <?php include dirname(__DIR__) . "/includes/ai-assistant.php"; ?>
</body>
</html>
