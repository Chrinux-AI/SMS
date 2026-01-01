<?php

/**
 * Alumni Events Page
 * View and register for alumni events
 * Verdant SMS v3.0
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Alumni only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'alumni') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user = db()->fetch("SELECT * FROM users WHERE id = ?", [$user_id]);

$page_title = 'Alumni Events';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/apple-touch-icon.png">
    <link rel="manifest" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>manifest.json">
    <meta name="msapplication-TileColor" content="#00BFFF">
    <meta name="msapplication-TileImage" content="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/mstile-150x150.png">
    <meta name="theme-color" content="#0a0a0f">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <style>
        .events-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            color: var(--cyber-cyan);
            font-size: 2rem;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .event-card {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid var(--cyber-cyan);
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 255, 255, 0.3);
        }

        .event-image {
            height: 200px;
            background: linear-gradient(135deg, var(--cyber-cyan), var(--cyber-pink));
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .event-image i {
            font-size: 4rem;
            color: #000;
        }

        .event-content {
            padding: 1.5rem;
        }

        .event-date {
            color: var(--cyber-pink);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .event-title {
            color: var(--cyber-cyan);
            font-size: 1.3rem;
            margin-bottom: 0.75rem;
        }

        .event-description {
            color: #aaa;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .event-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid rgba(0, 255, 255, 0.2);
        }

        .event-location {
            color: #888;
            font-size: 0.9rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--cyber-cyan);
            color: #000;
        }

        .btn-primary:hover {
            box-shadow: 0 0 15px var(--cyber-cyan);
        }

        .coming-soon {
            text-align: center;
            padding: 4rem 2rem;
            color: #888;
        }

        .coming-soon i {
            font-size: 4rem;
            color: var(--cyber-cyan);
            margin-bottom: 1rem;
        }
    </style>
</head>

<body class="cyber-bg">
    <?php include '../includes/cyber-nav.php'; ?>

    <main class="cyber-main">
        <div class="events-container">
            <div class="page-header">
                <h1><i class="fas fa-calendar-alt"></i> Alumni Events</h1>
                <p style="color: #888;">Stay connected with your alma mater through events and reunions</p>
            </div>

            <div class="coming-soon">
                <i class="fas fa-calendar-check"></i>
                <h2 style="color: var(--cyber-cyan);">Events Coming Soon!</h2>
                <p>We're planning exciting alumni events. Check back soon for:</p>
                <ul style="list-style: none; padding: 0; margin-top: 1rem;">
                    <li style="padding: 0.5rem 0;"><i class="fas fa-users" style="color: var(--cyber-cyan);"></i> Annual Alumni Reunion</li>
                    <li style="padding: 0.5rem 0;"><i class="fas fa-briefcase" style="color: var(--cyber-cyan);"></i> Career Networking Events</li>
                    <li style="padding: 0.5rem 0;"><i class="fas fa-trophy" style="color: var(--cyber-cyan);"></i> Sports Day & Games</li>
                    <li style="padding: 0.5rem 0;"><i class="fas fa-graduation-cap" style="color: var(--cyber-cyan);"></i> Mentorship Programs</li>
                </ul>
            </div>
        </div>
    </main>
</body>

</html>