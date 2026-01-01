<?php
/**
 * Hostel Warden Portal - Residence Hub
 * Complete dashboard with occupancy stats, leave requests, and mess menu
 */

require_once dirname(__DIR__) . '/includes/config.php';
$wardenName = $_SESSION['full_name'] ?? 'Warden';
$greeting = date('H') < 12 ? 'Good Morning' : (date('H') < 17 ? 'Good Afternoon' : 'Good Evening');
$pageTitle = "Hostel Management";
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
            background: linear-gradient(90deg, var(--purple), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ===== OCCUPANCY CARDS ===== */
        .occupancy-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .occupancy-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
        }

        .occupancy-card h3 {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .occupancy-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .occupancy-card.total .occupancy-value { color: var(--primary); }
        .occupancy-card.occupied .occupancy-value { color: var(--success); }
        .occupancy-card.vacant .occupancy-value { color: var(--warning); }
        .occupancy-card.leave .occupancy-value { color: var(--purple); }

        .occupancy-bar {
            height: 6px;
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
            overflow: hidden;
        }

        .occupancy-bar .fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--success));
            border-radius: 3px;
        }

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

        .card-badge {
            background: var(--warning);
            color: #000;
            font-size: 0.7rem;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            font-weight: 600;
        }

        .card-body { padding: 1.5rem; }

        /* ===== LEAVE REQUESTS ===== */
        .leave-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }

        .leave-item:last-child { margin-bottom: 0; }

        .leave-info h4 { font-size: 0.95rem; font-weight: 500; margin-bottom: 0.15rem; }
        .leave-info p { font-size: 0.8rem; color: var(--text-muted); }

        .leave-actions {
            display: flex;
            gap: 0.5rem;
        }

        .leave-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .leave-btn.approve { background: var(--success); color: #000; }
        .leave-btn.reject { background: transparent; border: 1px solid var(--danger); color: var(--danger); }

        /* ===== MESS MENU ===== */
        .mess-section {
            margin-top: 2rem;
        }

        .mess-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .mess-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.25rem;
            text-align: center;
        }

        .mess-time {
            font-size: 0.8rem;
            color: var(--primary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .mess-meal {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .mess-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* ===== ROOM GRID ===== */
        .room-grid-section {
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

        .section-header h2 i { color: var(--primary); }

        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 0.5rem;
        }

        .room-cell {
            aspect-ratio: 1;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .room-cell:hover { transform: scale(1.1); }

        .room-cell.full { background: var(--success); color: #000; }
        .room-cell.partial { background: var(--warning); color: #000; }
        .room-cell.empty { background: rgba(255,255,255,0.1); color: var(--text-muted); }

        @media (max-width: 1024px) {
            .occupancy-grid { grid-template-columns: repeat(2, 1fr); }
            .main-grid { grid-template-columns: 1fr; }
            .mess-grid { grid-template-columns: 1fr; }
            .rooms-grid { grid-template-columns: repeat(4, 1fr); }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1><?= $greeting ?>, <span><?= htmlspecialchars(explode(' ', $wardenName)[0]) ?></span>!</h1>
                <p style="color: var(--text-muted);">Hostel Management • <?= date('l, F j, Y') ?></p>
            </div>
        </header>

        <!-- OCCUPANCY CARDS -->
        <div class="occupancy-grid">
            <div class="occupancy-card total">
                <h3>Total Beds</h3>
                <div class="occupancy-value">120</div>
                <div class="occupancy-bar"><div class="fill" style="width: 100%;"></div></div>
            </div>
            <div class="occupancy-card occupied">
                <h3>Occupied</h3>
                <div class="occupancy-value">102</div>
                <div class="occupancy-bar"><div class="fill" style="width: 85%;"></div></div>
            </div>
            <div class="occupancy-card vacant">
                <h3>Vacant</h3>
                <div class="occupancy-value">18</div>
                <div class="occupancy-bar"><div class="fill" style="width: 15%; background: var(--warning);"></div></div>
            </div>
            <div class="occupancy-card leave">
                <h3>On Leave</h3>
                <div class="occupancy-value">8</div>
                <div class="occupancy-bar"><div class="fill" style="width: 7%; background: var(--purple);"></div></div>
            </div>
        </div>

        <!-- MAIN GRID -->
        <div class="main-grid">
            <!-- LEAVE REQUESTS -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-times"></i> Leave Requests</h3>
                    <span class="card-badge">3 pending</span>
                </div>
                <div class="card-body">
                    <div class="leave-item">
                        <div class="leave-info">
                            <h4>Chinedu Okoro - Room 12</h4>
                            <p>Jan 5-8 (3 nights) • Family event</p>
                        </div>
                        <div class="leave-actions">
                            <button class="leave-btn approve"><i class="fas fa-check"></i></button>
                            <button class="leave-btn reject"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div class="leave-item">
                        <div class="leave-info">
                            <h4>Emeka Nwosu - Room 8</h4>
                            <p>Jan 10-12 (2 nights) • Medical</p>
                        </div>
                        <div class="leave-actions">
                            <button class="leave-btn approve"><i class="fas fa-check"></i></button>
                            <button class="leave-btn reject"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div class="leave-item">
                        <div class="leave-info">
                            <h4>Kemi Adebayo - Room 22</h4>
                            <p>Jan 6-7 (1 night) • Exeat</p>
                        </div>
                        <div class="leave-actions">
                            <button class="leave-btn approve"><i class="fas fa-check"></i></button>
                            <button class="leave-btn reject"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DAILY UPDATES -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-clipboard-list"></i> Today's Updates</h3>
                </div>
                <div class="card-body">
                    <div class="leave-item">
                        <div class="leave-info">
                            <h4>Room 15 - Maintenance</h4>
                            <p>Plumbing issue reported • In progress</p>
                        </div>
                        <span style="color: var(--warning);"><i class="fas fa-tools"></i></span>
                    </div>
                    <div class="leave-item">
                        <div class="leave-info">
                            <h4>Lights Out Compliance</h4>
                            <p>98% last night • All blocks</p>
                        </div>
                        <span style="color: var(--success);"><i class="fas fa-check-circle"></i></span>
                    </div>
                    <div class="leave-item">
                        <div class="leave-info">
                            <h4>New Admissions</h4>
                            <p>2 students pending room assignment</p>
                        </div>
                        <span style="color: var(--primary);"><i class="fas fa-user-plus"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- MESS MENU -->
        <div class="mess-section">
            <div class="section-header">
                <h2><i class="fas fa-utensils"></i> Today's Mess Menu</h2>
            </div>
            <div class="mess-grid">
                <div class="mess-card">
                    <div class="mess-time">Breakfast • 7:00 AM</div>
                    <div class="mess-meal">Akara & Pap</div>
                    <div class="mess-desc">With bread and tea</div>
                </div>
                <div class="mess-card">
                    <div class="mess-time">Lunch • 1:00 PM</div>
                    <div class="mess-meal">Jollof Rice</div>
                    <div class="mess-desc">With fried chicken & salad</div>
                </div>
                <div class="mess-card">
                    <div class="mess-time">Dinner • 6:30 PM</div>
                    <div class="mess-meal">Eba & Egusi</div>
                    <div class="mess-desc">With assorted meat</div>
                </div>
            </div>
        </div>

        <!-- ROOM GRID -->
        <div class="room-grid-section">
            <div class="section-header">
                <h2><i class="fas fa-bed"></i> Room Status - Block A</h2>
            </div>
            <div class="rooms-grid">
                <?php for($i = 1; $i <= 24; $i++): ?>
                    <?php $status = $i % 5 === 0 ? 'empty' : ($i % 3 === 0 ? 'partial' : 'full'); ?>
                    <div class="room-cell <?= $status ?>" title="Room <?= $i ?>">
                        <span><?= $i ?></span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</body>
</html>
