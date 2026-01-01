<?php
/**
 * Class Teacher Portal - Class Overview
 */
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = "Class Teacher Portal";
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
            --pink: #EC4899;
            --bg-dark: #0B0F19;
            --bg-card: #111827;
            --border: rgba(255,255,255,0.08);
            --text: #E5E7EB;
            --text-muted: #9CA3AF;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-dark); color: var(--text); padding: 1.5rem; }
        .container { max-width: 1100px; margin: 0 auto; }
        .header h1 { font-size: 1.5rem; margin-bottom: 0.25rem; }
        .header h1 span { background: linear-gradient(90deg, var(--pink), var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .header p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem; }
        .class-banner { background: linear-gradient(135deg, rgba(236,72,153,0.15), rgba(0,212,255,0.1)); border: 2px solid var(--pink); border-radius: 20px; padding: 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; }
        .class-info h2 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .class-info p { color: var(--text-muted); }
        .class-stats { display: flex; gap: 2rem; }
        .class-stat { text-align: center; }
        .class-stat-value { font-size: 2rem; font-weight: 700; color: var(--primary); }
        .class-stat-label { font-size: 0.8rem; color: var(--text-muted); }
        .quick-actions { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; }
        .quick-action { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1.25rem; background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; cursor: pointer; transition: all 0.2s; text-decoration: none; color: var(--text); }
        .quick-action:hover { border-color: var(--pink); transform: translateY(-2px); }
        .quick-action i { font-size: 1.5rem; color: var(--pink); }
        .quick-action span { font-size: 0.85rem; }
        .main-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; }
        .card-header { padding: 1.25rem; border-bottom: 1px solid var(--border); }
        .card-header h3 { font-size: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .card-header h3 i { color: var(--primary); }
        .card-body { padding: 1.25rem; }
        .alert-item { display: flex; align-items: center; gap: 1rem; padding: 1rem; border-radius: 10px; margin-bottom: 0.75rem; }
        .alert-item:last-child { margin-bottom: 0; }
        .alert-item.warning { background: rgba(255,184,0,0.1); border: 1px solid rgba(255,184,0,0.3); }
        .alert-item.danger { background: rgba(255,71,87,0.1); border: 1px solid rgba(255,71,87,0.3); }
        .alert-item.warning i { color: var(--warning); }
        .alert-item.danger i { color: var(--danger); }
        .alert-item .content h4 { font-size: 0.9rem; margin-bottom: 0.1rem; }
        .alert-item .content p { font-size: 0.8rem; color: var(--text-muted); }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1><i class="fas fa-users-class"></i> <span>Class Teacher Portal</span></h1>
            <p>Class Overview • <?= date('l, F j, Y') ?></p>
        </header>

        <div class="class-banner">
            <div class="class-info">
                <h2>JSS 2A</h2>
                <p><i class="fas fa-door-open"></i> Room 12 • Morning Session</p>
            </div>
            <div class="class-stats">
                <div class="class-stat">
                    <div class="class-stat-value">35</div>
                    <div class="class-stat-label">Students</div>
                </div>
                <div class="class-stat">
                    <div class="class-stat-value">94%</div>
                    <div class="class-stat-label">Attendance</div>
                </div>
                <div class="class-stat">
                    <div class="class-stat-value">76%</div>
                    <div class="class-stat-label">Avg Grade</div>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <a href="attendance.php" class="quick-action">
                <i class="fas fa-clipboard-check"></i>
                <span>Mark Attendance</span>
            </a>
            <a href="report-cards.php" class="quick-action">
                <i class="fas fa-scroll"></i>
                <span>Report Cards</span>
            </a>
            <a href="students.php" class="quick-action">
                <i class="fas fa-users"></i>
                <span>View Students</span>
            </a>
            <a href="messages.php" class="quick-action">
                <i class="fas fa-envelope"></i>
                <span>Message Parents</span>
            </a>
        </div>

        <div class="main-grid">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-exclamation-triangle"></i> Attention Needed</h3>
                </div>
                <div class="card-body">
                    <div class="alert-item danger">
                        <i class="fas fa-user-times"></i>
                        <div class="content">
                            <h4>Emeka Nwosu</h4>
                            <p>Absent 3 days this week</p>
                        </div>
                    </div>
                    <div class="alert-item warning">
                        <i class="fas fa-chart-line"></i>
                        <div class="content">
                            <h4>Chioma Obi</h4>
                            <p>Grades dropped by 15%</p>
                        </div>
                    </div>
                    <div class="alert-item warning">
                        <i class="fas fa-money-bill"></i>
                        <div class="content">
                            <h4>Tunde Ajayi</h4>
                            <p>Outstanding fees: ₦45,000</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-star"></i> Top Performers</h3>
                </div>
                <div class="card-body">
                    <div class="alert-item" style="background: rgba(0,255,135,0.1); border: 1px solid rgba(0,255,135,0.3);">
                        <i class="fas fa-trophy" style="color: gold;"></i>
                        <div class="content">
                            <h4>Adaeze Eze</h4>
                            <p>1st Position • 92% Average</p>
                        </div>
                    </div>
                    <div class="alert-item" style="background: rgba(0,212,255,0.1); border: 1px solid rgba(0,212,255,0.3);">
                        <i class="fas fa-medal" style="color: silver;"></i>
                        <div class="content">
                            <h4>Chinedu Okoro</h4>
                            <p>2nd Position • 88% Average</p>
                        </div>
                    </div>
                    <div class="alert-item" style="background: rgba(168,85,247,0.1); border: 1px solid rgba(168,85,247,0.3);">
                        <i class="fas fa-award" style="color: #cd7f32;"></i>
                        <div class="content">
                            <h4>Kemi Adebayo</h4>
                            <p>3rd Position • 85% Average</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
