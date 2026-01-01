<?php
/**
 * Admin Officer Dashboard
 */
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = "Admin Officer";
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
        .container { max-width: 900px; margin: 0 auto; }
        h1 span { background: linear-gradient(90deg, var(--cyber-cyan), var(--cyber-green)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .tasks { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0; }
        .task-btn { display: flex; flex-direction: column; align-items: center; gap: 0.75rem; background: var(--card-bg); border: 1px solid rgba(0,255,255,0.2); border-radius: 12px; padding: 1.5rem; color: #fff; text-decoration: none; transition: all 0.3s ease; }
        .task-btn:hover { border-color: var(--cyber-cyan); transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,255,255,0.2); }
        .task-btn i { font-size: 2rem; color: var(--cyber-green); }
        .section-card { background: var(--card-bg); border: 1px solid rgba(0,255,255,0.2); border-radius: 12px; padding: 1.5rem; }
        .section-card h3 { color: var(--cyber-cyan); margin-bottom: 1rem; }
        .pending-item { display: flex; justify-content: space-between; padding: 1rem; background: rgba(255,165,0,0.1); border: 1px solid orange; border-radius: 8px; margin-bottom: 0.75rem; }
        .pending-item:last-child { margin-bottom: 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-clipboard-list"></i> <span>Admin Office</span></h1>

        <div class="tasks">
            <a href="admissions.php" class="task-btn"><i class="fas fa-user-plus"></i><span>New Admission</span></a>
            <a href="certificates.php" class="task-btn"><i class="fas fa-certificate"></i><span>Certificates</span></a>
            <a href="records.php" class="task-btn"><i class="fas fa-folder-open"></i><span>Student Records</span></a>
            <a href="letters.php" class="task-btn"><i class="fas fa-envelope"></i><span>Letters</span></a>
            <a href="visitors.php" class="task-btn"><i class="fas fa-id-card"></i><span>Visitor Log</span></a>
            <a href="supplies.php" class="task-btn"><i class="fas fa-box"></i><span>Supplies</span></a>
        </div>

        <div class="section-card">
            <h3><i class="fas fa-bell"></i> Pending Requests</h3>
            <div class="pending-item"><span>Transfer Certificate - Chinedu O.</span><span style="color: orange;">2 days</span></div>
            <div class="pending-item"><span>Character Reference - Adaeze E.</span><span style="color: orange;">1 day</span></div>
            <div class="pending-item"><span>Admission Letter - New Student</span><span style="color: var(--cyber-green);">Today</span></div>
        </div>
    </div>
</body>
</html>
