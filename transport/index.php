<?php
/**
 * Transport Manager Portal - Fleet Monitor
 * Complete dashboard with live bus tracking, route status, and maintenance alerts
 */

require_once dirname(__DIR__) . '/includes/config.php';
$transportName = $_SESSION['full_name'] ?? 'Transport Manager';
$greeting = date('H') < 12 ? 'Good Morning' : (date('H') < 17 ? 'Good Afternoon' : 'Good Evening');
$pageTitle = "Fleet Monitor";
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

        .header p { color: var(--text-muted); }

        .live-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(0,255,135,0.15);
            border: 1px solid var(--success);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            color: var(--success);
        }

        .live-badge::before {
            content: '';
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* ===== FLEET GRID ===== */
        .fleet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .bus-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .bus-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .bus-card.moving::before { background: var(--success); }
        .bus-card.stopped::before { background: var(--warning); }
        .bus-card.delayed::before { background: var(--danger); }

        .bus-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .bus-header h3 {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.1rem;
        }

        .bus-header h3 i {
            font-size: 1.25rem;
        }

        .bus-card.moving .bus-header h3 i { color: var(--success); }
        .bus-card.stopped .bus-header h3 i { color: var(--warning); }
        .bus-card.delayed .bus-header h3 i { color: var(--danger); }

        .bus-status {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .bus-status.moving { background: rgba(0,255,135,0.15); color: var(--success); }
        .bus-status.stopped { background: rgba(255,184,0,0.15); color: var(--warning); }
        .bus-status.delayed { background: rgba(255,71,87,0.15); color: var(--danger); }

        .bus-info {
            display: grid;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .bus-info-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .bus-info-row i {
            width: 20px;
            text-align: center;
            color: var(--primary);
        }

        .progress-bar {
            height: 8px;
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar .fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--success));
            border-radius: 4px;
            transition: width 0.5s;
        }

        .bus-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
            font-size: 0.85rem;
        }

        .bus-footer .eta {
            color: var(--success);
            font-weight: 600;
        }

        /* ===== MAINTENANCE SECTION ===== */
        .maintenance-section {
            margin-top: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .section-header h2 {
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-header h2 i { color: var(--warning); }

        .maintenance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
        }

        .maintenance-card {
            background: var(--bg-card);
            border: 1px solid rgba(255,184,0,0.3);
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .maintenance-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(255,184,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: var(--warning);
        }

        .maintenance-info h4 { font-size: 0.95rem; margin-bottom: 0.15rem; }
        .maintenance-info p { font-size: 0.8rem; color: var(--text-muted); }

        @media (max-width: 768px) {
            .header { flex-direction: column; gap: 1rem; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1><i class="fas fa-bus"></i> <span>Fleet Monitor</span></h1>
                <p><?= $greeting ?>, <?= htmlspecialchars(explode(' ', $transportName)[0]) ?> • <?= date('l, F j, Y') ?></p>
            </div>
            <div class="live-badge">Live Tracking</div>
        </header>

        <!-- FLEET GRID -->
        <div class="fleet-grid">
            <div class="bus-card moving">
                <div class="bus-header">
                    <h3><i class="fas fa-bus"></i> Bus #1</h3>
                    <span class="bus-status moving">En Route</span>
                </div>
                <div class="bus-info">
                    <div class="bus-info-row"><i class="fas fa-road"></i> Route A - Ikeja → School</div>
                    <div class="bus-info-row"><i class="fas fa-user"></i> Driver: Mr. Bello</div>
                    <div class="bus-info-row"><i class="fas fa-users"></i> 28 students onboard</div>
                    <div class="bus-info-row"><i class="fas fa-map-marker-alt"></i> Currently at: Allen Junction</div>
                </div>
                <div class="progress-bar"><div class="fill" style="width: 70%;"></div></div>
                <div class="bus-footer">
                    <span>70% to destination</span>
                    <span class="eta">ETA: 12 mins</span>
                </div>
            </div>

            <div class="bus-card moving">
                <div class="bus-header">
                    <h3><i class="fas fa-bus"></i> Bus #2</h3>
                    <span class="bus-status moving">En Route</span>
                </div>
                <div class="bus-info">
                    <div class="bus-info-row"><i class="fas fa-road"></i> Route B - Lekki → School</div>
                    <div class="bus-info-row"><i class="fas fa-user"></i> Driver: Mr. Chukwu</div>
                    <div class="bus-info-row"><i class="fas fa-users"></i> 32 students onboard</div>
                    <div class="bus-info-row"><i class="fas fa-map-marker-alt"></i> Currently at: Ajah Roundabout</div>
                </div>
                <div class="progress-bar"><div class="fill" style="width: 45%;"></div></div>
                <div class="bus-footer">
                    <span>45% to destination</span>
                    <span class="eta">ETA: 25 mins</span>
                </div>
            </div>

            <div class="bus-card stopped">
                <div class="bus-header">
                    <h3><i class="fas fa-bus"></i> Bus #3</h3>
                    <span class="bus-status stopped">At School</span>
                </div>
                <div class="bus-info">
                    <div class="bus-info-row"><i class="fas fa-road"></i> Route C - Mainland</div>
                    <div class="bus-info-row"><i class="fas fa-user"></i> Driver: Mr. Ogundimu</div>
                    <div class="bus-info-row"><i class="fas fa-parking"></i> Parked at school compound</div>
                    <div class="bus-info-row"><i class="fas fa-gas-pump"></i> Fuel Level: 65%</div>
                </div>
                <div class="progress-bar"><div class="fill" style="width: 100%; background: var(--warning);"></div></div>
                <div class="bus-footer">
                    <span>Awaiting dispatch</span>
                    <span class="eta" style="color: var(--warning);">Standby</span>
                </div>
            </div>

            <div class="bus-card delayed">
                <div class="bus-header">
                    <h3><i class="fas fa-bus"></i> Bus #4</h3>
                    <span class="bus-status delayed">Delayed</span>
                </div>
                <div class="bus-info">
                    <div class="bus-info-row"><i class="fas fa-road"></i> Route D - Victoria Island</div>
                    <div class="bus-info-row"><i class="fas fa-user"></i> Driver: Mr. Adeyemi</div>
                    <div class="bus-info-row"><i class="fas fa-users"></i> 25 students onboard</div>
                    <div class="bus-info-row"><i class="fas fa-exclamation-triangle"></i> Traffic at Falomo Bridge</div>
                </div>
                <div class="progress-bar"><div class="fill" style="width: 35%; background: var(--danger);"></div></div>
                <div class="bus-footer">
                    <span>35% to destination</span>
                    <span class="eta" style="color: var(--danger);">ETA: 40 mins</span>
                </div>
            </div>
        </div>

        <!-- MAINTENANCE ALERTS -->
        <div class="maintenance-section">
            <div class="section-header">
                <h2><i class="fas fa-tools"></i> Maintenance Reminders</h2>
            </div>
            <div class="maintenance-grid">
                <div class="maintenance-card">
                    <div class="maintenance-icon"><i class="fas fa-oil-can"></i></div>
                    <div class="maintenance-info">
                        <h4>Bus #1 - Oil Change</h4>
                        <p>Due in 500 km • Scheduled Jan 5</p>
                    </div>
                </div>
                <div class="maintenance-card">
                    <div class="maintenance-icon"><i class="fas fa-car-battery"></i></div>
                    <div class="maintenance-info">
                        <h4>Bus #3 - Battery Check</h4>
                        <p>Due in 2 weeks • Last: Dec 15</p>
                    </div>
                </div>
                <div class="maintenance-card">
                    <div class="maintenance-icon"><i class="fas fa-tire"></i></div>
                    <div class="maintenance-info">
                        <h4>Bus #4 - Tire Rotation</h4>
                        <p>Overdue • Please schedule ASAP</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include dirname(__DIR__) . "/includes/ai-assistant.php"; ?>
</body>
</html>
