<?php
/**
 * Vice-Principal Portal - Operations & Discipline Hub
 * Complete dashboard with discipline issues, substitutions, and event management
 */

require_once dirname(__DIR__) . '/includes/config.php';
$vpName = $_SESSION['full_name'] ?? 'Vice Principal';
$greeting = date('H') < 12 ? 'Good Morning' : (date('H') < 17 ? 'Good Afternoon' : 'Good Evening');
$pageTitle = "Operations Dashboard";
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
            background: linear-gradient(90deg, var(--warning), var(--danger));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ===== STATS ROW ===== */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-card.discipline .stat-icon { background: rgba(255,71,87,0.15); color: var(--danger); }
        .stat-card.substitution .stat-icon { background: rgba(255,184,0,0.15); color: var(--warning); }
        .stat-card.events .stat-icon { background: rgba(168,85,247,0.15); color: var(--purple); }
        .stat-card.resolved .stat-icon { background: rgba(0,255,135,0.15); color: var(--success); }

        .stat-info h3 { font-size: 1.5rem; font-weight: 700; }
        .stat-info p { font-size: 0.8rem; color: var(--text-muted); }

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

        .card-header h3 i.danger { color: var(--danger); }
        .card-header h3 i.warning { color: var(--warning); }
        .card-header h3 i.purple { color: var(--purple); }

        .card-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            font-weight: 600;
        }

        .card-badge.danger { background: var(--danger); color: #fff; }
        .card-badge.warning { background: var(--warning); color: #000; }

        .card-body { padding: 1.5rem; }

        /* ===== DISCIPLINE ITEMS ===== */
        .discipline-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255,71,87,0.1);
            border: 1px solid rgba(255,71,87,0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }

        .discipline-item:last-child { margin-bottom: 0; }

        .severity {
            width: 4px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        .severity.high { background: var(--danger); }
        .severity.medium { background: var(--warning); }
        .severity.low { background: var(--primary); }

        .discipline-info { flex: 1; }
        .discipline-info h4 { font-size: 0.95rem; font-weight: 500; margin-bottom: 0.15rem; }
        .discipline-info p { font-size: 0.8rem; color: var(--text-muted); }

        .discipline-action {
            padding: 0.5rem 1rem;
            background: var(--danger);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            align-self: center;
        }

        /* ===== SUBSTITUTION ITEMS ===== */
        .sub-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }

        .sub-item:last-child { margin-bottom: 0; }

        .sub-info h4 { font-size: 0.95rem; font-weight: 500; margin-bottom: 0.15rem; }
        .sub-info p { font-size: 0.8rem; color: var(--text-muted); }

        .sub-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .sub-status.assigned { background: rgba(0,255,135,0.15); color: var(--success); }
        .sub-status.pending { background: rgba(255,184,0,0.15); color: var(--warning); }

        /* ===== EVENTS ===== */
        .event-item {
            display: flex;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
        }

        .event-item:last-child { border-bottom: none; }

        .event-date {
            text-align: center;
            background: var(--bg-dark);
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            min-width: 60px;
        }

        .event-date .day { font-size: 1.25rem; font-weight: 700; color: var(--primary); }
        .event-date .month { font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; }

        .event-info h4 { font-size: 0.95rem; font-weight: 500; margin-bottom: 0.15rem; }
        .event-info p { font-size: 0.8rem; color: var(--text-muted); }

        @media (max-width: 1024px) {
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .main-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1><?= $greeting ?>, <span>VP <?= htmlspecialchars(explode(' ', $vpName)[0]) ?></span>!</h1>
                <p style="color: var(--text-muted);">Operations & Discipline • <?= date('l, F j, Y') ?></p>
            </div>
        </header>

        <!-- STATS ROW -->
        <div class="stats-row">
            <div class="stat-card discipline">
                <div class="stat-icon"><i class="fas fa-gavel"></i></div>
                <div class="stat-info">
                    <h3>5</h3>
                    <p>Open Cases</p>
                </div>
            </div>
            <div class="stat-card substitution">
                <div class="stat-icon"><i class="fas fa-exchange-alt"></i></div>
                <div class="stat-info">
                    <h3>3</h3>
                    <p>Substitutions Today</p>
                </div>
            </div>
            <div class="stat-card events">
                <div class="stat-icon"><i class="fas fa-calendar"></i></div>
                <div class="stat-info">
                    <h3>8</h3>
                    <p>Upcoming Events</p>
                </div>
            </div>
            <div class="stat-card resolved">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3>24</h3>
                    <p>Resolved This Week</p>
                </div>
            </div>
        </div>

        <!-- MAIN GRID -->
        <div class="main-grid">
            <!-- DISCIPLINE ISSUES -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-exclamation-circle danger"></i> Discipline Cases</h3>
                    <span class="card-badge danger">5 open</span>
                </div>
                <div class="card-body">
                    <div class="discipline-item">
                        <div class="severity high"></div>
                        <div class="discipline-info">
                            <h4>Fighting in Classroom</h4>
                            <p>Emeka Nwosu & Tunde Ajayi • JSS 3A • Today</p>
                        </div>
                        <button class="discipline-action">Review</button>
                    </div>
                    <div class="discipline-item">
                        <div class="severity medium"></div>
                        <div class="discipline-info">
                            <h4>Truancy</h4>
                            <p>Chioma Obi • SSS 2B • 3rd occurrence</p>
                        </div>
                        <button class="discipline-action">Review</button>
                    </div>
                    <div class="discipline-item">
                        <div class="severity low"></div>
                        <div class="discipline-info">
                            <h4>Late to School</h4>
                            <p>5 students • Various classes • Today</p>
                        </div>
                        <button class="discipline-action">Review</button>
                    </div>
                </div>
            </div>

            <!-- SUBSTITUTIONS -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-exchange-alt warning"></i> Teacher Substitutions</h3>
                    <span class="card-badge warning">3 today</span>
                </div>
                <div class="card-body">
                    <div class="sub-item">
                        <div class="sub-info">
                            <h4>Mrs. Eze → Mr. Adebayo</h4>
                            <p>Social Studies • JSS 2A • 10:00 AM</p>
                        </div>
                        <span class="sub-status assigned">Assigned</span>
                    </div>
                    <div class="sub-item">
                        <div class="sub-info">
                            <h4>Mr. Okonkwo → TBD</h4>
                            <p>Basic Science • JSS 3B • 11:00 AM</p>
                        </div>
                        <span class="sub-status pending">Pending</span>
                    </div>
                    <div class="sub-item">
                        <div class="sub-info">
                            <h4>Mrs. Johnson → Mrs. Chukwu</h4>
                            <p>English • SSS 1A • 1:00 PM</p>
                        </div>
                        <span class="sub-status assigned">Assigned</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- UPCOMING EVENTS -->
        <div class="card" style="margin-top: 1.5rem;">
            <div class="card-header">
                <h3><i class="fas fa-calendar-alt purple"></i> Upcoming Events</h3>
            </div>
            <div class="card-body" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div class="event-item">
                    <div class="event-date">
                        <div class="day">5</div>
                        <div class="month">Jan</div>
                    </div>
                    <div class="event-info">
                        <h4>Inter-House Sports</h4>
                        <p>9:00 AM • Sports Complex</p>
                    </div>
                </div>
                <div class="event-item">
                    <div class="event-date">
                        <div class="day">10</div>
                        <div class="month">Jan</div>
                    </div>
                    <div class="event-info">
                        <h4>PTA Meeting</h4>
                        <p>2:00 PM • Main Hall</p>
                    </div>
                </div>
                <div class="event-item">
                    <div class="event-date">
                        <div class="day">15</div>
                        <div class="month">Jan</div>
                    </div>
                    <div class="event-info">
                        <h4>Staff Training</h4>
                        <p>All Day • Admin Block</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include dirname(__DIR__) . "/includes/ai-assistant.php"; ?>
</body>
</html>
