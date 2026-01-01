<?php
/**
 * General Staff Dashboard
 */
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = "Staff Portal";
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
        .container { max-width: 800px; margin: 0 auto; }
        h1 span { background: linear-gradient(90deg, var(--cyber-cyan), var(--cyber-green)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .profile-card { background: var(--card-bg); border: 1px solid rgba(0,255,255,0.2); border-radius: 16px; padding: 2rem; margin: 2rem 0; display: flex; align-items: center; gap: 2rem; }
        .profile-avatar { width: 100px; height: 100px; border-radius: 50%; border: 3px solid var(--cyber-cyan); background: rgba(0,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--cyber-cyan); }
        .profile-info h2 { margin-bottom: 0.5rem; }
        .profile-info p { color: rgba(255,255,255,0.6); }
        .quick-links { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
        .link-btn { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; background: var(--card-bg); border: 1px solid rgba(0,255,255,0.2); border-radius: 10px; padding: 1.25rem; color: #fff; text-decoration: none; transition: all 0.3s ease; }
        .link-btn:hover { border-color: var(--cyber-cyan); transform: translateY(-3px); }
        .link-btn i { font-size: 1.5rem; color: var(--cyber-green); }
        .announcements { background: var(--card-bg); border: 1px solid rgba(0,255,255,0.2); border-radius: 12px; padding: 1.5rem; margin-top: 2rem; }
        .announcements h3 { color: var(--cyber-cyan); margin-bottom: 1rem; }
        .announcement { padding: 1rem; background: rgba(0,0,0,0.3); border-radius: 8px; margin-bottom: 0.75rem; }
        .announcement:last-child { margin-bottom: 0; }
        @media (max-width: 600px) { .quick-links { grid-template-columns: repeat(2, 1fr); } .profile-card { flex-direction: column; text-align: center; } }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-user"></i> <span>Staff Portal</span></h1>

        <div class="profile-card">
            <div class="profile-avatar"><i class="fas fa-user"></i></div>
            <div class="profile-info">
                <h2><?= htmlspecialchars($_SESSION['full_name'] ?? 'Staff Member') ?></h2>
                <p>General Staff • Since 2020</p>
            </div>
        </div>

        <div class="quick-links">
            <a href="profile.php" class="link-btn"><i class="fas fa-id-card"></i><span>My Profile</span></a>
            <a href="attendance.php" class="link-btn"><i class="fas fa-clock"></i><span>My Attendance</span></a>
            <a href="leave.php" class="link-btn"><i class="fas fa-calendar"></i><span>Apply Leave</span></a>
            <a href="payslips.php" class="link-btn"><i class="fas fa-receipt"></i><span>Payslips</span></a>
            <a href="requests.php" class="link-btn"><i class="fas fa-paper-plane"></i><span>Requests</span></a>
            <a href="directory.php" class="link-btn"><i class="fas fa-address-book"></i><span>Directory</span></a>
        </div>

        <div class="announcements">
            <h3><i class="fas fa-bullhorn"></i> Announcements</h3>
            <div class="announcement">
                <strong>Staff Meeting Tomorrow</strong>
                <p style="font-size: 0.85rem; color: rgba(255,255,255,0.6);">All staff to attend at 2:00 PM in the conference room.</p>
            </div>
            <div class="announcement">
                <strong>Happy New Year!</strong>
                <p style="font-size: 0.85rem; color: rgba(255,255,255,0.6);">Wishing everyone a prosperous 2025.</p>
            </div>
        </div>
    </div>
</body>
</html>
