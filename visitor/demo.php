<?php
/**
 * Verdant SMS - Demo Page
 */
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = "Demo - Verdant SMS";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #00D4FF; --success: #00FF87; --purple: #A855F7; --bg-dark: #0A0E17; --bg-card: #111827; --border: rgba(255,255,255,0.08); --text: #F3F4F6; --text-muted: #9CA3AF; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-dark); color: var(--text); min-height: 100vh; }
        .navbar { padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
        .navbar-brand { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
        .navbar-logo { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, var(--success), var(--primary)); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #000; }
        .navbar-title { font-size: 1.1rem; font-weight: 700; background: linear-gradient(90deg, var(--success), var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .container { max-width: 1000px; margin: 0 auto; padding: 4rem 2rem; text-align: center; }
        h1 { font-size: 2.5rem; margin-bottom: 1rem; }
        .subtitle { color: var(--text-muted); font-size: 1.1rem; margin-bottom: 3rem; }
        .demo-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
        .demo-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 2rem; text-align: center; transition: all 0.3s; }
        .demo-card:hover { border-color: var(--primary); transform: translateY(-5px); }
        .demo-icon { width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, rgba(0,212,255,0.15), rgba(168,85,247,0.15)); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.75rem; color: var(--primary); }
        .demo-card h3 { font-size: 1.25rem; margin-bottom: 0.5rem; }
        .demo-card p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; }
        .demo-creds { background: var(--bg-dark); border-radius: 10px; padding: 1rem; margin-bottom: 1.5rem; text-align: left; }
        .demo-creds p { margin-bottom: 0.25rem; font-size: 0.85rem; }
        .demo-creds code { color: var(--success); }
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.85rem 1.5rem; border-radius: 10px; font-size: 0.9rem; font-weight: 600; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, var(--success), var(--primary)); color: #000; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="navbar-brand">
            <div class="navbar-logo"><i class="fas fa-leaf"></i></div>
            <span class="navbar-title">Verdant SMS</span>
        </a>
    </nav>

    <div class="container">
        <h1>Try Verdant SMS</h1>
        <p class="subtitle">Explore the system with our demo accounts. No signup required.</p>

        <div class="demo-options">
            <div class="demo-card">
                <div class="demo-icon"><i class="fas fa-user-shield"></i></div>
                <h3>Admin Demo</h3>
                <p>Full access to school management features.</p>
                <div class="demo-creds">
                    <p>Email: <code>admin@demo.verdant</code></p>
                    <p>Password: <code>demo2025</code></p>
                </div>
                <a href="../login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login as Admin
                </a>
            </div>

            <div class="demo-card">
                <div class="demo-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <h3>Teacher Demo</h3>
                <p>Attendance, grades, AI lesson planner.</p>
                <div class="demo-creds">
                    <p>Email: <code>teacher@demo.verdant</code></p>
                    <p>Password: <code>demo2025</code></p>
                </div>
                <a href="../login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login as Teacher
                </a>
            </div>

            <div class="demo-card">
                <div class="demo-icon"><i class="fas fa-user-graduate"></i></div>
                <h3>Student Demo</h3>
                <p>View grades, schedule, assignments.</p>
                <div class="demo-creds">
                    <p>Email: <code>student@demo.verdant</code></p>
                    <p>Password: <code>demo2025</code></p>
                </div>
                <a href="../login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login as Student
                </a>
            </div>
        </div>
    </div>
    <?php include __DIR__ . "/includes/ai-assistant.php"; ?>
</body>
</html>
