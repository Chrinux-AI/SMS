<?php
/**
 * School Owner Portal - Business Overview
 */
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = "Business Overview";
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
            --purple: #A855F7;
            --bg-dark: #0B0F19;
            --bg-card: #111827;
            --border: rgba(255,255,255,0.08);
            --text: #E5E7EB;
            --text-muted: #9CA3AF;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-dark); color: var(--text); padding: 1.5rem; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header h1 { font-size: 1.5rem; margin-bottom: 0.25rem; }
        .header h1 span { background: linear-gradient(90deg, var(--purple), var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .header p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem; }
        .revenue-banner { background: linear-gradient(135deg, rgba(168,85,247,0.15), rgba(0,255,135,0.1)); border: 2px solid var(--purple); border-radius: 20px; padding: 2rem; text-align: center; margin-bottom: 2rem; }
        .revenue-label { font-size: 0.9rem; color: var(--purple); margin-bottom: 0.5rem; }
        .revenue-value { font-size: 3.5rem; font-weight: 700; background: linear-gradient(90deg, var(--success), var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .revenue-period { font-size: 0.85rem; color: var(--text-muted); }
        .branches-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; }
        .branch-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem; position: relative; }
        .branch-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--success); border-radius: 16px 16px 0 0; }
        .branch-name { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem; }
        .branch-location { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem; }
        .branch-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        .branch-stat { background: rgba(0,0,0,0.2); border-radius: 8px; padding: 0.75rem; text-align: center; }
        .branch-stat-value { font-size: 1.25rem; font-weight: 700; color: var(--primary); }
        .branch-stat-label { font-size: 0.75rem; color: var(--text-muted); }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1><i class="fas fa-building"></i> <span>Business Overview</span></h1>
            <p>School Owner Portal • <?= date('l, F j, Y') ?></p>
        </header>

        <div class="revenue-banner">
            <div class="revenue-label"><i class="fas fa-chart-line"></i> Total Monthly Revenue</div>
            <div class="revenue-value">₦12.5M</div>
            <div class="revenue-period">December 2024 • All Branches</div>
        </div>

        <div class="branches-grid">
            <div class="branch-card">
                <div class="branch-name">Verdant Academy - Main Campus</div>
                <div class="branch-location"><i class="fas fa-map-marker-alt"></i> Ikeja, Lagos</div>
                <div class="branch-stats">
                    <div class="branch-stat">
                        <div class="branch-stat-value">450</div>
                        <div class="branch-stat-label">Students</div>
                    </div>
                    <div class="branch-stat">
                        <div class="branch-stat-value">₦5.2M</div>
                        <div class="branch-stat-label">Revenue</div>
                    </div>
                    <div class="branch-stat">
                        <div class="branch-stat-value">32</div>
                        <div class="branch-stat-label">Staff</div>
                    </div>
                    <div class="branch-stat">
                        <div class="branch-stat-value">96%</div>
                        <div class="branch-stat-label">Collection</div>
                    </div>
                </div>
            </div>

            <div class="branch-card">
                <div class="branch-name">Verdant Academy - Lekki</div>
                <div class="branch-location"><i class="fas fa-map-marker-alt"></i> Lekki, Lagos</div>
                <div class="branch-stats">
                    <div class="branch-stat">
                        <div class="branch-stat-value">320</div>
                        <div class="branch-stat-label">Students</div>
                    </div>
                    <div class="branch-stat">
                        <div class="branch-stat-value">₦4.1M</div>
                        <div class="branch-stat-label">Revenue</div>
                    </div>
                    <div class="branch-stat">
                        <div class="branch-stat-value">24</div>
                        <div class="branch-stat-label">Staff</div>
                    </div>
                    <div class="branch-stat">
                        <div class="branch-stat-value">92%</div>
                        <div class="branch-stat-label">Collection</div>
                    </div>
                </div>
            </div>

            <div class="branch-card">
                <div class="branch-name">Verdant Academy - Abuja</div>
                <div class="branch-location"><i class="fas fa-map-marker-alt"></i> Wuse, Abuja</div>
                <div class="branch-stats">
                    <div class="branch-stat">
                        <div class="branch-stat-value">280</div>
                        <div class="branch-stat-label">Students</div>
                    </div>
                    <div class="branch-stat">
                        <div class="branch-stat-value">₦3.2M</div>
                        <div class="branch-stat-label">Revenue</div>
                    </div>
                    <div class="branch-stat">
                        <div class="branch-stat-value">20</div>
                        <div class="branch-stat-label">Staff</div>
                    </div>
                    <div class="branch-stat">
                        <div class="branch-stat-value">88%</div>
                        <div class="branch-stat-label">Collection</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include dirname(__DIR__) . "/includes/ai-assistant.php"; ?>
</body>
</html>
