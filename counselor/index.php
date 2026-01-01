<?php
/**
 * Counselor Portal - Student Wellness Hub
 */
require_once dirname(__DIR__) . '/includes/config.php';
$counselorName = $_SESSION['full_name'] ?? 'Counselor';
$pageTitle = "Student Wellness";
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
            --purple: #A855F7;
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
        .header h1 span { background: linear-gradient(90deg, var(--primary), var(--purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .header p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem; }
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 1.25rem; text-align: center; }
        .stat-value { font-size: 2rem; font-weight: 700; color: var(--primary); }
        .stat-label { font-size: 0.85rem; color: var(--text-muted); }
        .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; margin-bottom: 1.5rem; }
        .card-header { padding: 1.25rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .card-header h3 { font-size: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .card-header h3 i { color: var(--primary); }
        .card-body { padding: 1.25rem; }
        .session-item { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: rgba(0,0,0,0.2); border-radius: 10px; margin-bottom: 0.75rem; }
        .session-item:last-child { margin-bottom: 0; }
        .session-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--purple)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600; }
        .session-info h4 { font-size: 0.95rem; margin-bottom: 0.15rem; }
        .session-info p { font-size: 0.8rem; color: var(--text-muted); }
        .wellbeing-meter { background: linear-gradient(135deg, rgba(0,212,255,0.1), rgba(0,255,135,0.1)); border: 1px solid var(--success); border-radius: 16px; padding: 1.5rem; text-align: center; }
        .wellbeing-meter h3 { font-size: 1rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .wellbeing-meter h3 i { color: var(--success); }
        .meter-value { font-size: 3rem; font-weight: 700; color: var(--success); }
        .meter-label { font-size: 0.85rem; color: var(--text-muted); }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1><i class="fas fa-comments"></i> <span>Student Wellness</span></h1>
            <p>Counseling Portal • <?= date('l, F j, Y') ?></p>
        </header>

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-value">8</div>
                <div class="stat-label">Sessions Today</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">24</div>
                <div class="stat-label">This Week</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">156</div>
                <div class="stat-label">This Term</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-check"></i> Upcoming Sessions</h3>
                </div>
                <div class="card-body">
                    <div class="session-item">
                        <div class="session-avatar">A</div>
                        <div class="session-info">
                            <h4>Anonymous Session #127</h4>
                            <p>10:30 AM • Academic Stress</p>
                        </div>
                    </div>
                    <div class="session-item">
                        <div class="session-avatar">C</div>
                        <div class="session-info">
                            <h4>Chinedu Okoro - JSS 2A</h4>
                            <p>11:30 AM • Follow-up Session</p>
                        </div>
                    </div>
                    <div class="session-item">
                        <div class="session-avatar">A</div>
                        <div class="session-info">
                            <h4>Anonymous Session #128</h4>
                            <p>2:00 PM • Peer Relationships</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wellbeing-meter">
                <h3><i class="fas fa-heart"></i> School Wellbeing Index</h3>
                <div class="meter-value">78%</div>
                <div class="meter-label">Based on weekly surveys</div>
            </div>
        </div>
    </div>
</body>
</html>
