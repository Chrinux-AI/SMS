<?php

/**
 * Alumni Dashboard - Graduate Network Portal
 * Verdant SMS v3.0
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for authentication
require_role('alumni', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Alumni';

// Get alumni profile
$alumni_profile = db()->fetchOne("
    SELECT a.*, u.email, u.phone
    FROM alumni a
    JOIN users u ON a.user_id = u.id
    WHERE a.user_id = ?
", [$user_id]);

$graduation_year = $alumni_profile['graduation_year'] ?? date('Y');

// Total Alumni Network
$total_alumni = db()->fetchOne("SELECT COUNT(*) as count FROM alumni")['count'] ?? 0;

// Same Batch Alumni
$batch_alumni = db()->fetchOne("
    SELECT COUNT(*) as count FROM alumni WHERE graduation_year = ?
", [$graduation_year])['count'] ?? 0;

// Upcoming Alumni Events
$upcoming_events = db()->fetchAll("
    SELECT * FROM alumni_events
    WHERE event_date >= CURDATE() AND status = 'active'
    ORDER BY event_date
    LIMIT 5
");

// Recent News
$recent_news = db()->fetchAll("
    SELECT * FROM alumni_news
    WHERE status = 'published'
    ORDER BY published_at DESC
    LIMIT 5
");

// Donation Stats
$total_donations = db()->fetchOne("
    SELECT IFNULL(SUM(amount), 0) as total FROM alumni_donations WHERE alumni_id = ?
", [$alumni_profile['id'] ?? 0])['total'] ?? 0;

// Mentorship Opportunities
$mentorship_count = db()->fetchOne("
    SELECT COUNT(*) as count FROM mentorship_programs WHERE status = 'active'
")['count'] ?? 0;

$page_title = 'Alumni Portal';
$page_icon = 'users-rays';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/cyberpunk-ui.css" rel="stylesheet">
    <style>
        .alumni-banner {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.15), rgba(255, 165, 0, 0.1));
            border: 1px solid #FFD700;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alumni-welcome {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            color: #FFD700;
            margin-bottom: 0.5rem;
        }

        .graduation-badge {
            background: rgba(255, 215, 0, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            color: #FFD700;
            font-weight: 600;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg, rgba(20, 20, 30, 0.9));
            border: 1px solid var(--border, rgba(0, 191, 255, 0.2));
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }

        .stat-icon.gold {
            color: #FFD700;
        }

        .stat-icon.cyan {
            color: #00BFFF;
        }

        .stat-icon.green {
            color: #00FF7F;
        }

        .stat-icon.purple {
            color: #8A2BE2;
        }

        .stat-value {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .dashboard-section {
            background: var(--card-bg, rgba(20, 20, 30, 0.9));
            border: 1px solid var(--border, rgba(0, 191, 255, 0.2));
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
            color: #FFD700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .event-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .event-card:last-child {
            border-bottom: none;
        }

        .event-date {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #FFD700, #FF9F43);
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #000;
            font-weight: 700;
        }

        .event-date .month {
            font-size: 0.7rem;
            text-transform: uppercase;
        }

        .event-date .day {
            font-size: 1.5rem;
            line-height: 1;
        }

        .event-info h4 {
            margin-bottom: 0.25rem;
        }

        .event-info p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .two-col {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
        }

        .news-item {
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .news-item:last-child {
            border-bottom: none;
        }

        .news-item h4 {
            color: #00BFFF;
            margin-bottom: 0.25rem;
        }

        .news-item small {
            color: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb gold" style="background: linear-gradient(135deg, #FFD700, #FF9F43);"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <span class="welcome-text">Welcome back, <?php echo htmlspecialchars($full_name); ?></span>
                </div>
            </header>

            <!-- Alumni Banner -->
            <div class="alumni-banner">
                <div>
                    <div class="alumni-welcome">Welcome to the Alumni Network</div>
                    <p style="color: rgba(255,255,255,0.7);">Stay connected with your alma mater and fellow graduates</p>
                </div>
                <div class="graduation-badge">
                    <i class="fas fa-graduation-cap"></i> Class of <?php echo $graduation_year; ?>
                </div>
            </div>

            <!-- Overview Stats -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon gold"><i class="fas fa-users"></i></div>
                    <div class="stat-value"><?php echo number_format($total_alumni); ?></div>
                    <div class="stat-label">Alumni Network</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon cyan"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-value"><?php echo $batch_alumni; ?></div>
                    <div class="stat-label">Your Batch</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-hand-holding-heart"></i></div>
                    <div class="stat-value">₹<?php echo number_format($total_donations); ?></div>
                    <div class="stat-label">Your Contributions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fas fa-handshake"></i></div>
                    <div class="stat-value"><?php echo $mentorship_count; ?></div>
                    <div class="stat-label">Mentorship Programs</div>
                </div>
            </div>

            <div class="two-col">
                <!-- Upcoming Events -->
                <div class="dashboard-section">
                    <h2 class="section-title"><i class="fas fa-calendar-star"></i> Upcoming Alumni Events</h2>
                    <?php if (empty($upcoming_events)): ?>
                        <p style="color: rgba(255,255,255,0.5); padding: 1rem 0;">No upcoming events scheduled</p>
                    <?php else: ?>
                        <?php foreach ($upcoming_events as $event): ?>
                            <div class="event-card">
                                <div class="event-date">
                                    <span class="month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                                    <span class="day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                                </div>
                                <div class="event-info">
                                    <h4><?php echo htmlspecialchars($event['title'] ?? 'Event'); ?></h4>
                                    <p><?php echo htmlspecialchars($event['location'] ?? 'TBA'); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Alumni News -->
                <div class="dashboard-section">
                    <h2 class="section-title"><i class="fas fa-newspaper"></i> Latest News</h2>
                    <?php if (empty($recent_news)): ?>
                        <p style="color: rgba(255,255,255,0.5); padding: 1rem 0;">No news available</p>
                    <?php else: ?>
                        <?php foreach ($recent_news as $news): ?>
                            <div class="news-item">
                                <h4><?php echo htmlspecialchars($news['title'] ?? 'News'); ?></h4>
                                <small><?php echo date('M d, Y', strtotime($news['published_at'] ?? 'now')); ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-section">
                <h2 class="section-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="directory.php" class="cyber-btn gold" style="background: linear-gradient(135deg, #FFD700, #FF9F43);"><i class="fas fa-address-book"></i> Alumni Directory</a>
                    <a href="events.php" class="cyber-btn cyan"><i class="fas fa-calendar-alt"></i> Events</a>
                    <a href="donate.php" class="cyber-btn green"><i class="fas fa-donate"></i> Make a Donation</a>
                    <a href="mentorship.php" class="cyber-btn purple"><i class="fas fa-handshake"></i> Become a Mentor</a>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>