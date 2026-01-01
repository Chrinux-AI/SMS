<?php
/**
 * School Nurse Portal - Health Center Hub
 * Complete dashboard with quick visit log, patient counter, and health alerts
 */

require_once dirname(__DIR__) . '/includes/config.php';
$nurseName = $_SESSION['full_name'] ?? 'Nurse';
$greeting = date('H') < 12 ? 'Good Morning' : (date('H') < 17 ? 'Good Afternoon' : 'Good Evening');
$pageTitle = "Health Center";
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
            --pink: #EC4899;
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

        .container { max-width: 1200px; margin: 0 auto; }

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
            background: linear-gradient(90deg, var(--pink), var(--danger));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ===== PATIENT COUNTER ===== */
        .patient-counter {
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.15), rgba(255, 71, 87, 0.1));
            border: 2px solid var(--pink);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .patient-counter::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .patient-counter h2 {
            font-size: 1rem;
            color: var(--pink);
            margin-bottom: 1rem;
            position: relative;
        }

        .counter-value {
            font-size: 5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.5rem;
            position: relative;
        }

        .counter-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            position: relative;
        }

        /* ===== QUICK VISIT FORM ===== */
        .quick-visit {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .quick-visit h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quick-visit h3 i { color: var(--success); }

        .visit-form {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .form-group input, .form-group select {
            width: 100%;
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: var(--text);
            font-size: 0.9rem;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn-log {
            background: linear-gradient(135deg, var(--success), var(--primary));
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            color: #000;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            align-self: flex-end;
            transition: all 0.3s;
        }

        .btn-log:hover { transform: translateY(-2px); }

        /* ===== MAIN GRID ===== */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

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

        .card-body { padding: 1.5rem; }

        /* ===== VISIT LIST ===== */
        .visit-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }

        .visit-item:last-child { margin-bottom: 0; }

        .visit-time {
            font-size: 0.8rem;
            color: var(--primary);
            font-weight: 600;
            white-space: nowrap;
        }

        .visit-info h4 { font-size: 0.95rem; font-weight: 500; margin-bottom: 0.15rem; }
        .visit-info p { font-size: 0.8rem; color: var(--text-muted); }

        .visit-status {
            margin-left: auto;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .visit-status.treated { background: rgba(0,255,135,0.15); color: var(--success); }
        .visit-status.referred { background: rgba(255,184,0,0.15); color: var(--warning); }

        /* ===== HEALTH ALERTS ===== */
        .alert-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }

        .alert-item.warning {
            background: rgba(255,184,0,0.15);
            border: 1px solid rgba(255,184,0,0.3);
        }

        .alert-item.danger {
            background: rgba(255,71,87,0.15);
            border: 1px solid rgba(255,71,87,0.3);
        }

        .alert-item i {
            font-size: 1.25rem;
        }

        .alert-item.warning i { color: var(--warning); }
        .alert-item.danger i { color: var(--danger); }

        .alert-content h4 { font-size: 0.95rem; font-weight: 500; margin-bottom: 0.15rem; }
        .alert-content p { font-size: 0.8rem; color: var(--text-muted); }

        /* ===== INVENTORY ===== */
        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .inventory-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: rgba(0,0,0,0.2);
            border-radius: 8px;
        }

        .inventory-name { font-size: 0.85rem; }

        .inventory-count {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
        }

        .inventory-count.ok { background: rgba(0,255,135,0.15); color: var(--success); }
        .inventory-count.low { background: rgba(255,184,0,0.15); color: var(--warning); }
        .inventory-count.critical { background: rgba(255,71,87,0.15); color: var(--danger); }

        @media (max-width: 1024px) {
            .visit-form { grid-template-columns: 1fr; }
            .main-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1><?= $greeting ?>, <span>Nurse <?= htmlspecialchars(explode(' ', $nurseName)[0]) ?></span>!</h1>
                <p style="color: var(--text-muted);">Health Center • <?= date('l, F j, Y') ?></p>
            </div>
        </header>

        <!-- PATIENT COUNTER -->
        <div class="patient-counter">
            <h2><i class="fas fa-heartbeat"></i> Patients Today</h2>
            <div class="counter-value">12</div>
            <div class="counter-label">Students visited the health center</div>
        </div>

        <!-- QUICK VISIT FORM -->
        <div class="quick-visit">
            <h3><i class="fas fa-plus-circle"></i> Quick Visit Log</h3>
            <form class="visit-form">
                <div class="form-group">
                    <label>Student ID / Name</label>
                    <input type="text" placeholder="Enter ID or Name...">
                </div>
                <div class="form-group">
                    <label>Complaint</label>
                    <select>
                        <option>Headache</option>
                        <option>Stomach Ache</option>
                        <option>Fever</option>
                        <option>Injury</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Treatment</label>
                    <select>
                        <option>First Aid</option>
                        <option>Medication</option>
                        <option>Rest</option>
                        <option>Referred</option>
                    </select>
                </div>
                <button type="submit" class="btn-log"><i class="fas fa-save"></i> Log Visit</button>
            </form>
        </div>

        <!-- MAIN GRID -->
        <div class="main-grid">
            <!-- TODAY'S VISITS -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-clipboard-list"></i> Today's Visits</h3>
                </div>
                <div class="card-body">
                    <div class="visit-item">
                        <div class="visit-time">10:30 AM</div>
                        <div class="visit-info">
                            <h4>Chinedu Okoro - JSS 2A</h4>
                            <p>Headache • Given paracetamol</p>
                        </div>
                        <span class="visit-status treated">Treated</span>
                    </div>
                    <div class="visit-item">
                        <div class="visit-time">9:45 AM</div>
                        <div class="visit-info">
                            <h4>Adaeze Eze - SSS 1B</h4>
                            <p>Stomach ache • Rest recommended</p>
                        </div>
                        <span class="visit-status treated">Treated</span>
                    </div>
                    <div class="visit-item">
                        <div class="visit-time">9:15 AM</div>
                        <div class="visit-info">
                            <h4>Emeka Nwosu - JSS 3A</h4>
                            <p>Ankle sprain • Sent to hospital</p>
                        </div>
                        <span class="visit-status referred">Referred</span>
                    </div>
                </div>
            </div>

            <!-- HEALTH ALERTS -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-exclamation-triangle"></i> Health Alerts</h3>
                </div>
                <div class="card-body">
                    <div class="alert-item warning">
                        <i class="fas fa-pills"></i>
                        <div class="alert-content">
                            <h4>Low Stock: Paracetamol</h4>
                            <p>Only 15 tablets remaining</p>
                        </div>
                    </div>
                    <div class="alert-item danger">
                        <i class="fas fa-virus"></i>
                        <div class="alert-content">
                            <h4>Flu Season Alert</h4>
                            <p>5 cases reported this week</p>
                        </div>
                    </div>
                    <div class="alert-item warning">
                        <i class="fas fa-syringe"></i>
                        <div class="alert-content">
                            <h4>Vaccination Due</h4>
                            <p>12 students due for booster shots</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- INVENTORY -->
        <div class="card" style="margin-top: 1.5rem;">
            <div class="card-header">
                <h3><i class="fas fa-first-aid"></i> Medical Inventory</h3>
            </div>
            <div class="card-body">
                <div class="inventory-grid">
                    <div class="inventory-item">
                        <span class="inventory-name">Paracetamol</span>
                        <span class="inventory-count low">15 tablets</span>
                    </div>
                    <div class="inventory-item">
                        <span class="inventory-name">Bandages</span>
                        <span class="inventory-count ok">48 rolls</span>
                    </div>
                    <div class="inventory-item">
                        <span class="inventory-name">Antiseptic</span>
                        <span class="inventory-count ok">5 bottles</span>
                    </div>
                    <div class="inventory-item">
                        <span class="inventory-name">Cotton Wool</span>
                        <span class="inventory-count ok">12 packs</span>
                    </div>
                    <div class="inventory-item">
                        <span class="inventory-name">Thermometer</span>
                        <span class="inventory-count ok">3 units</span>
                    </div>
                    <div class="inventory-item">
                        <span class="inventory-name">First Aid Kits</span>
                        <span class="inventory-count critical">1 kit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include dirname(__DIR__) . "/includes/ai-assistant.php"; ?>
</body>
</html>
