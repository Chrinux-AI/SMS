<?php
/**
 * Alumni Portal - Alumni Network
 */
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = "Alumni Network";
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
        .container { max-width: 1000px; margin: 0 auto; }
        .header h1 { font-size: 1.5rem; margin-bottom: 0.25rem; }
        .header h1 span { background: linear-gradient(90deg, var(--success), var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .header p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem; }
        .welcome-banner { background: linear-gradient(135deg, rgba(0,255,135,0.15), rgba(0,212,255,0.1)); border: 1px solid var(--success); border-radius: 16px; padding: 2rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1.5rem; }
        .welcome-avatar { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--success), var(--primary)); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #000; }
        .welcome-content h2 { font-size: 1.25rem; margin-bottom: 0.25rem; }
        .welcome-content p { font-size: 0.9rem; color: var(--text-muted); }
        .section-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; }
        .card-header { padding: 1.25rem; border-bottom: 1px solid var(--border); }
        .card-header h3 { font-size: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .card-header h3 i { color: var(--primary); }
        .card-body { padding: 1.25rem; }
        .event-item { display: flex; gap: 1rem; padding: 1rem; background: rgba(0,0,0,0.2); border-radius: 10px; margin-bottom: 0.75rem; }
        .event-item:last-child { margin-bottom: 0; }
        .event-date { text-align: center; background: var(--bg-dark); border-radius: 8px; padding: 0.5rem 0.75rem; }
        .event-date .day { font-size: 1.25rem; font-weight: 700; color: var(--primary); }
        .event-date .month { font-size: 0.7rem; color: var(--text-muted); }
        .event-info h4 { font-size: 0.95rem; margin-bottom: 0.15rem; }
        .event-info p { font-size: 0.8rem; color: var(--text-muted); }
        .job-item { padding: 1rem; background: rgba(0,0,0,0.2); border-radius: 10px; margin-bottom: 0.75rem; }
        .job-item:last-child { margin-bottom: 0; }
        .job-item h4 { font-size: 0.95rem; margin-bottom: 0.15rem; }
        .job-item p { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem; }
        .job-badge { display: inline-block; padding: 0.2rem 0.5rem; background: rgba(0,255,135,0.15); color: var(--success); border-radius: 4px; font-size: 0.75rem; }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1><i class="fas fa-graduation-cap"></i> <span>Alumni Network</span></h1>
            <p>Stay Connected • <?= date('l, F j, Y') ?></p>
        </header>

        <div class="welcome-banner">
            <div class="welcome-avatar"><i class="fas fa-user-graduate"></i></div>
            <div class="welcome-content">
                <h2>Welcome Back, Alumni!</h2>
                <p>Class of 2020 • Connect with fellow graduates and stay updated on school events</p>
            </div>
        </div>

        <div class="section-grid">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-alt"></i> Upcoming Reunions</h3>
                </div>
                <div class="card-body">
                    <div class="event-item">
                        <div class="event-date">
                            <div class="day">15</div>
                            <div class="month">FEB</div>
                        </div>
                        <div class="event-info">
                            <h4>Class of 2015 Reunion</h4>
                            <p>Main Hall • 4:00 PM</p>
                        </div>
                    </div>
                    <div class="event-item">
                        <div class="event-date">
                            <div class="day">28</div>
                            <div class="month">MAR</div>
                        </div>
                        <div class="event-info">
                            <h4>Annual Alumni Meet</h4>
                            <p>Sports Complex • 10:00 AM</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-briefcase"></i> Job Board</h3>
                </div>
                <div class="card-body">
                    <div class="job-item">
                        <h4>Software Developer</h4>
                        <p>TechCorp Nigeria • Lagos</p>
                        <span class="job-badge">Alumni Exclusive</span>
                    </div>
                    <div class="job-item">
                        <h4>Marketing Manager</h4>
                        <p>GlobalBrands Ltd • Abuja</p>
                        <span class="job-badge">Alumni Exclusive</span>
                    </div>
                    <div class="job-item">
                        <h4>Accountant</h4>
                        <p>FinanceHub • Port Harcourt</p>
                        <span class="job-badge">Alumni Exclusive</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
