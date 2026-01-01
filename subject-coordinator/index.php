<?php
/**
 * Subject Coordinator Dashboard
 */
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = "Subject Coordinator";
?>
<!DOCTYPE html>
<html lang="en" data-theme="cyberpunk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --cyber-cyan: #00FFFF; --cyber-green: #00FF7F; --dark-bg: #0A0A0F; --card-bg: #12121A; }
        body { background: var(--dark-bg); color: #fff; font-family: 'Segoe UI', sans-serif; min-height: 100vh; padding: 2rem; }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 span { background: linear-gradient(90deg, var(--cyber-cyan), var(--cyber-green)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .subject-header { background: linear-gradient(135deg, rgba(139,0,255,0.2), rgba(0,255,255,0.1)); border: 2px solid var(--cyber-purple); border-radius: 16px; padding: 2rem; margin: 2rem 0; text-align: center; }
        .subject-header h2 { color: var(--cyber-purple); font-size: 2rem; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem; }
        .section-card { background: var(--card-bg); border: 1px solid rgba(0,255,255,0.2); border-radius: 12px; padding: 1.5rem; }
        .section-card h3 { color: var(--cyber-cyan); margin-bottom: 1rem; }
        .teacher-item { display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .teacher-item:last-child { border-bottom: none; }
        .performance { text-align: center; padding: 2rem; }
        .performance .score { font-size: 4rem; font-weight: 700; color: var(--cyber-green); }
        @media (max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-book"></i> <span>Mathematics Department</span></h1>

        <div class="subject-header">
            <h2>Subject Coordinator</h2>
            <p style="color: rgba(255,255,255,0.7);">Oversee curriculum, exams, and teacher performance</p>
        </div>

        <div class="grid-2">
            <div class="section-card">
                <h3><i class="fas fa-users"></i> Math Teachers</h3>
                <div class="teacher-item"><span>Mr. Adebayo</span><span style="color: var(--cyber-green);">JSS 1, JSS 2</span></div>
                <div class="teacher-item"><span>Mrs. Okonkwo</span><span style="color: var(--cyber-green);">JSS 3</span></div>
                <div class="teacher-item"><span>Mr. Chukwu</span><span style="color: var(--cyber-green);">SSS 1, SSS 2</span></div>
                <div class="teacher-item"><span>Dr. Eze</span><span style="color: var(--cyber-green);">SSS 3</span></div>
            </div>

            <div class="section-card">
                <h3><i class="fas fa-chart-line"></i> Subject Performance</h3>
                <div class="performance">
                    <div class="score">74%</div>
                    <p style="color: rgba(255,255,255,0.6);">Average across all classes</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
