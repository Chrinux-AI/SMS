<?php
/**
 * Parent Portal - Complete Homepage
 * Family Overview with child cards, updates, communication, and bus tracker
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$parentName = $_SESSION['full_name'] ?? 'Parent';
$greeting = date('H') < 12 ? 'Good Morning' : (date('H') < 17 ? 'Good Afternoon' : 'Good Evening');

// Sample children data
$children = [
    ['id' => 1, 'name' => 'Chinedu Okoro', 'class' => 'JSS 2A', 'avatar' => 'C', 'attendance' => 94, 'grade' => 78, 'fees_paid' => 85],
    ['id' => 2, 'name' => 'Adaeze Okoro', 'class' => 'Primary 5', 'avatar' => 'A', 'attendance' => 98, 'grade' => 82, 'fees_paid' => 100]
];

$pageTitle = "Parent Dashboard";
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00D4FF;
            --success: #00FF87;
            --warning: #FFB800;
            --danger: #FF4757;
            --purple: #A855F7;
            --pink: #EC4899;
            --bg-dark: #0B0F19;
            --bg-card: #111827;
            --border: rgba(255,255,255,0.08);
            --text: #E5E7EB;
            --text-muted: #9CA3AF;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            color: var(--text);
            min-height: 100vh;
            padding: 1.5rem;
        }

        .container { max-width: 1400px; margin: 0 auto; }

        /* ===== HEADER ===== */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .header h1 span {
            background: linear-gradient(90deg, var(--primary), var(--success));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header p { color: var(--text-muted); font-size: 0.9rem; }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .header-btn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
        }

        .header-btn:hover { border-color: var(--primary); color: var(--primary); }

        .notification-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            background: var(--danger);
            border-radius: 50%;
        }

        /* ===== CHILDREN TABS ===== */
        .children-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .child-tab {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.3s;
            flex: 1;
        }

        .child-tab:hover { border-color: rgba(255,255,255,0.2); }

        .child-tab.active {
            border-color: var(--primary);
            background: rgba(0, 212, 255, 0.1);
        }

        .child-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
        }

        .child-info h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.15rem;
        }

        .child-info p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* ===== ALERTS BANNER ===== */
        .alerts-banner {
            background: linear-gradient(90deg, rgba(255, 184, 0, 0.15), rgba(255, 71, 87, 0.15));
            border: 1px solid rgba(255, 184, 0, 0.4);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alerts-banner i {
            font-size: 1.5rem;
            color: var(--warning);
        }

        .alerts-banner-content {
            flex: 1;
        }

        .alerts-banner-content h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .alerts-banner-content p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .alerts-banner-action {
            padding: 0.6rem 1.25rem;
            background: var(--warning);
            color: #000;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
        }

        /* ===== STATS GRID ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .stat-card.attendance::before { background: var(--primary); }
        .stat-card.grades::before { background: var(--success); }
        .stat-card.fees::before { background: var(--purple); }
        .stat-card.messages::before { background: var(--pink); }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .stat-card.attendance .stat-icon { background: rgba(0,212,255,0.15); color: var(--primary); }
        .stat-card.grades .stat-icon { background: rgba(0,255,135,0.15); color: var(--success); }
        .stat-card.fees .stat-icon { background: rgba(168,85,247,0.15); color: var(--purple); }
        .stat-card.messages .stat-icon { background: rgba(236,72,153,0.15); color: var(--pink); }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.15rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ===== MAIN GRID ===== */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card:last-child { margin-bottom: 0; }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-header h3 i { color: var(--primary); }

        .card-action {
            font-size: 0.8rem;
            color: var(--primary);
            text-decoration: none;
        }

        .card-body { padding: 1.5rem; }

        /* ===== UPDATES LIST ===== */
        .update-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }

        .update-item:last-child { margin-bottom: 0; }

        .update-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .update-icon.grade { background: rgba(0,255,135,0.15); color: var(--success); }
        .update-icon.attendance { background: rgba(255,184,0,0.15); color: var(--warning); }
        .update-icon.fee { background: rgba(168,85,247,0.15); color: var(--purple); }
        .update-icon.message { background: rgba(0,212,255,0.15); color: var(--primary); }

        .update-content h4 {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.15rem;
        }

        .update-content p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ===== BUS TRACKER ===== */
        .bus-tracker {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 255, 135, 0.1));
            border: 1px solid var(--primary);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .bus-tracker h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .bus-tracker h3 i { color: var(--success); }

        .bus-status {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(0,0,0,0.3);
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .bus-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--success);
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(0, 255, 135, 0.5); }
            50% { box-shadow: 0 0 0 15px rgba(0, 255, 135, 0); }
        }

        .bus-info h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.15rem;
        }

        .bus-info p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .bus-eta {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            margin-bottom: 0.25rem;
        }

        .bus-eta-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-align: center;
        }

        /* ===== MESSAGE BOX ===== */
        .message-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
        }

        .message-box h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .message-box h3 i { color: var(--primary); }

        .message-textarea {
            width: 100%;
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1rem;
            color: var(--text);
            font-family: inherit;
            font-size: 0.9rem;
            min-height: 100px;
            resize: vertical;
            margin-bottom: 1rem;
        }

        .message-textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .message-send {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, var(--primary), var(--purple));
            border: none;
            border-radius: 10px;
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .main-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .children-tabs { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header class="header">
            <div>
                <h1><?= $greeting ?>, <span><?= htmlspecialchars(explode(' ', $parentName)[0]) ?></span>!</h1>
                <p>Family Dashboard • <?= date('l, F j, Y') ?></p>
            </div>
            <div class="header-actions">
                <button class="header-btn" title="Messages">
                    <i class="fas fa-envelope"></i>
                    <span class="notification-dot"></span>
                </button>
                <button class="header-btn" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-dot"></span>
                </button>
            </div>
        </header>

        <!-- CHILDREN TABS -->
        <div class="children-tabs">
            <?php foreach ($children as $index => $child): ?>
            <div class="child-tab <?= $index === 0 ? 'active' : '' ?>" data-child="<?= $child['id'] ?>">
                <div class="child-avatar"><?= $child['avatar'] ?></div>
                <div class="child-info">
                    <h3><?= htmlspecialchars($child['name']) ?></h3>
                    <p><?= $child['class'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- ALERTS BANNER -->
        <div class="alerts-banner">
            <i class="fas fa-exclamation-triangle"></i>
            <div class="alerts-banner-content">
                <h4>Fee Payment Reminder</h4>
                <p>Second term fees for Chinedu are due by January 15th. ₦25,000 outstanding.</p>
            </div>
            <button class="alerts-banner-action"><i class="fas fa-credit-card"></i> Pay Now</button>
        </div>

        <!-- STATS GRID -->
        <div class="stats-grid">
            <div class="stat-card attendance">
                <div class="stat-card-header">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                </div>
                <div class="stat-value">94%</div>
                <div class="stat-label">Attendance Rate</div>
            </div>
            <div class="stat-card grades">
                <div class="stat-card-header">
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                </div>
                <div class="stat-value">78%</div>
                <div class="stat-label">Average Grade</div>
            </div>
            <div class="stat-card fees">
                <div class="stat-card-header">
                    <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                </div>
                <div class="stat-value">85%</div>
                <div class="stat-label">Fees Paid</div>
            </div>
            <div class="stat-card messages">
                <div class="stat-card-header">
                    <div class="stat-icon"><i class="fas fa-envelope"></i></div>
                </div>
                <div class="stat-value">2</div>
                <div class="stat-label">Unread Messages</div>
            </div>
        </div>

        <!-- MAIN GRID -->
        <div class="main-grid">
            <div>
                <!-- RECENT UPDATES -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-bell"></i> Recent Updates</h3>
                        <a href="updates.php" class="card-action">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="update-item">
                            <div class="update-icon grade"><i class="fas fa-star"></i></div>
                            <div class="update-content">
                                <h4>New Grade Posted: Mathematics CA 2</h4>
                                <p>Chinedu scored 85% (A) • 2 hours ago</p>
                            </div>
                        </div>
                        <div class="update-item">
                            <div class="update-icon attendance"><i class="fas fa-calendar-times"></i></div>
                            <div class="update-content">
                                <h4>Attendance Alert</h4>
                                <p>Chinedu was marked late for morning assembly • Yesterday</p>
                            </div>
                        </div>
                        <div class="update-item">
                            <div class="update-icon message"><i class="fas fa-comment"></i></div>
                            <div class="update-content">
                                <h4>Message from Class Teacher</h4>
                                <p>Mrs. Johnson sent a message about homework • 2 days ago</p>
                            </div>
                        </div>
                        <div class="update-item">
                            <div class="update-icon fee"><i class="fas fa-receipt"></i></div>
                            <div class="update-content">
                                <h4>Fee Payment Confirmed</h4>
                                <p>₦45,000 received for Adaeze • 5 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- UPCOMING EVENTS -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar"></i> School Calendar</h3>
                        <a href="calendar.php" class="card-action">Full Calendar</a>
                    </div>
                    <div class="card-body">
                        <div class="update-item">
                            <div class="update-icon" style="background: rgba(168,85,247,0.15); color: var(--purple);">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="update-content">
                                <h4>PTA Meeting</h4>
                                <p>January 10, 2025 at 2:00 PM • Main Hall</p>
                            </div>
                        </div>
                        <div class="update-item">
                            <div class="update-icon" style="background: rgba(0,212,255,0.15); color: var(--primary);">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="update-content">
                                <h4>Mid-Term Exams Begin</h4>
                                <p>February 5, 2025</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <!-- BUS TRACKER -->
                <div class="bus-tracker">
                    <h3><i class="fas fa-bus"></i> School Bus Tracker</h3>
                    <div class="bus-status">
                        <div class="bus-icon"><i class="fas fa-bus"></i></div>
                        <div class="bus-info">
                            <h4>Bus #2 - Route B</h4>
                            <p>Currently at: Ikeja Junction</p>
                        </div>
                    </div>
                    <div class="bus-eta">12 min</div>
                    <div class="bus-eta-label">Estimated arrival at school</div>
                </div>

                <!-- MESSAGE TEACHER -->
                <div class="message-box">
                    <h3><i class="fas fa-comment-dots"></i> Message Class Teacher</h3>
                    <textarea class="message-textarea" placeholder="Type your message to Mrs. Johnson..."></textarea>
                    <button class="message-send">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Child tab switching
        document.querySelectorAll('.child-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.child-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
