<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';


require_role('admin'); // TEST_MODE will allow access for testing

// Fetch All Events
$events = db()->fetchAll("
    SELECT * FROM events
    WHERE event_date >= CURDATE()
    ORDER BY event_date ASC
");

// Stats
$upcoming_events = count($events);
$events_this_month = db()->fetchOne("SELECT COUNT(*) as count FROM events WHERE MONTH(event_date) = MONTH(CURDATE())")['count'] ?? 0;

$page_title = 'Events & Activities Management';
$page_icon = 'calendar-star';
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../../assets/css/cyberpunk-ui.css" rel="stylesheet">
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb golden"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <button class="cyber-btn green" onclick="window.location.href='create.php'">
                        <i class="fas fa-plus"></i> Create Event
                    </button>
                </div>
            </header>

            <div class="cyber-content slide-in">

                <section class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
                    <div class="stat-orb">
                        <div class="stat-icon cyan"><i class="fas fa-calendar-alt"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $upcoming_events; ?></div>
                            <div class="stat-label">Upcoming Events</div>
                        </div>
                    </div>
                    <div class="stat-orb">
                        <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $events_this_month; ?></div>
                            <div class="stat-label">This Month</div>
                        </div>
                    </div>
                </section>

                <section class="holo-card" style="margin-top:30px;">
                    <h3 style="margin-bottom:20px;"><i class="fas fa-calendar-star"></i> Event Calendar</h3>
                    <div style="display:grid;gap:15px;">
                        <?php if (empty($events)): ?>
                            <div style="text-align:center;padding:50px;">
                                <i class="fas fa-calendar-times" style="font-size:4rem;color:var(--text-muted);margin-bottom:15px;"></i>
                                <p style="color:var(--text-muted);">No upcoming events scheduled.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($events as $event): ?>
                                <div style="background:linear-gradient(135deg,rgba(255,193,7,0.1),rgba(255,152,0,0.1));border-left:4px solid var(--golden-pulse);padding:20px;border-radius:12px;">
                                    <div style="display:flex;justify-content:space-between;align-items:start;">
                                        <div>
                                            <h3 style="color:var(--golden-pulse);margin:0 0 10px 0;"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                                            <p style="color:var(--text-muted);margin:0 0 10px 0;"><?php echo htmlspecialchars($event['description'] ?? ''); ?></p>
                                            <div style="display:flex;gap:20px;color:var(--text-muted);font-size:0.9rem;">
                                                <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                                                <?php if (!empty($event['location'])): ?>
                                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></span>
                                                <?php endif; ?>
                                                <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($event['event_type'] ?? 'General'); ?></span>
                                            </div>
                                        </div>
                                        <span class="cyber-badge golden"><?php echo ucfirst($event['status'] ?? 'Active'); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>

</html>
