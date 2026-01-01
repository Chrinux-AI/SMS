<?php
/**
 * Student Portal - Complete Homepage
 * Personal Learning Hub with schedule, assignments, grades, gamification
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$studentName = $_SESSION['full_name'] ?? 'Student';
$studentClass = 'JSS 2A';
$greeting = date('H') < 12 ? 'Good Morning' : (date('H') < 17 ? 'Good Afternoon' : 'Good Evening');

// Motivational quotes
$quotes = [
    "Education is the passport to the future, for tomorrow belongs to those who prepare for it today.",
    "The expert in anything was once a beginner.",
    "Success is the sum of small efforts repeated day in and day out.",
    "Learning never exhausts the mind. – Leonardo da Vinci",
    "Dream big. Work hard. Stay focused. Surround yourself with good people."
];
$quote = $quotes[array_rand($quotes)];

$pageTitle = "Student Dashboard";
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

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ===== WELCOME BANNER ===== */
        .welcome-banner {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.15), rgba(168, 85, 247, 0.15));
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .welcome-content h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .welcome-content h1 span {
            background: linear-gradient(90deg, var(--primary), var(--success));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-content .quote {
            font-size: 0.95rem;
            color: var(--text-muted);
            font-style: italic;
            max-width: 500px;
            margin-bottom: 1rem;
        }

        .class-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(0, 255, 135, 0.15);
            border: 1px solid var(--success);
            color: var(--success);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .welcome-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid var(--primary);
            box-shadow: 0 0 30px rgba(0, 212, 255, 0.3);
            background: linear-gradient(135deg, var(--primary), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            color: #fff;
            position: relative;
            z-index: 1;
        }

        /* ===== QUICK STATS ===== */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-stat {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s;
        }

        .quick-stat:hover {
            border-color: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }

        .quick-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .quick-stat.attendance .quick-stat-icon { background: rgba(0,212,255,0.15); color: var(--primary); }
        .quick-stat.grade .quick-stat-icon { background: rgba(0,255,135,0.15); color: var(--success); }
        .quick-stat.assignments .quick-stat-icon { background: rgba(255,184,0,0.15); color: var(--warning); }
        .quick-stat.position .quick-stat-icon { background: rgba(168,85,247,0.15); color: var(--purple); }

        .quick-stat-info h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.15rem;
        }

        .quick-stat-info p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ===== QUICK LINKS ===== */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            padding: 1.25rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            text-decoration: none;
            color: var(--text);
            transition: all 0.3s;
        }

        .quick-link:hover {
            background: rgba(0, 212, 255, 0.1);
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        .quick-link i {
            font-size: 1.75rem;
            color: var(--success);
        }

        .quick-link span {
            font-size: 0.85rem;
            font-weight: 500;
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

        .card:last-child {
            margin-bottom: 0;
        }

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

        .card-header h3 i {
            color: var(--primary);
        }

        .card-action {
            font-size: 0.8rem;
            color: var(--primary);
            text-decoration: none;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* ===== TODAY'S SCHEDULE ===== */
        .schedule-timeline {
            position: relative;
            padding-left: 20px;
        }

        .schedule-timeline::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 10px;
            bottom: 10px;
            width: 2px;
            background: linear-gradient(to bottom, var(--primary), var(--purple));
            border-radius: 2px;
        }

        .schedule-item {
            position: relative;
            padding: 0.75rem 0 0.75rem 1.5rem;
        }

        .schedule-item::before {
            content: '';
            position: absolute;
            left: -1px;
            top: 1.25rem;
            width: 12px;
            height: 12px;
            background: var(--bg-card);
            border: 3px solid var(--primary);
            border-radius: 50%;
        }

        .schedule-item.current::before {
            background: var(--primary);
            box-shadow: 0 0 10px var(--primary);
        }

        .schedule-time {
            font-size: 0.8rem;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .schedule-subject {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.15rem;
        }

        .schedule-teacher {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ===== ASSIGNMENTS ===== */
        .assignment-card {
            background: rgba(0,0,0,0.3);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .assignment-card:last-child {
            margin-bottom: 0;
        }

        .assignment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .assignment-header h4 {
            font-size: 0.95rem;
            font-weight: 600;
        }

        .assignment-due {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .assignment-due.urgent {
            background: rgba(255,71,87,0.15);
            color: var(--danger);
        }

        .assignment-due.soon {
            background: rgba(255,184,0,0.15);
            color: var(--warning);
        }

        .assignment-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
        }

        .assignment-progress {
            height: 6px;
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
            overflow: hidden;
        }

        .assignment-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--success));
            border-radius: 3px;
            transition: width 0.3s;
        }

        /* ===== GRADES ===== */
        .grade-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
        }

        .grade-item:last-child {
            border-bottom: none;
        }

        .grade-subject {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .grade-value {
            padding: 0.25rem 0.75rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .grade-value.a { background: rgba(0,255,135,0.15); color: var(--success); }
        .grade-value.b { background: rgba(0,212,255,0.15); color: var(--primary); }
        .grade-value.c { background: rgba(255,184,0,0.15); color: var(--warning); }

        /* ===== GAMIFICATION ===== */
        .gamification-section {
            background: linear-gradient(135deg, rgba(236,72,153,0.1), rgba(168,85,247,0.1));
            border: 1px solid rgba(236,72,153,0.3);
            border-radius: 16px;
            padding: 1.5rem;
        }

        .gamification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .gamification-header h3 {
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .house-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(236,72,153,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            color: var(--pink);
            font-weight: 600;
        }

        .badges-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
        }

        .badge-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            background: rgba(0,0,0,0.3);
            border-radius: 12px;
            text-align: center;
        }

        .badge-item i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .badge-item span {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .badge-item.gold i { color: gold; }
        .badge-item.silver i { color: silver; }
        .badge-item.bronze i { color: #cd7f32; }
        .badge-item.special i { color: var(--pink); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .quick-stats { grid-template-columns: repeat(2, 1fr); }
            .quick-links { grid-template-columns: repeat(3, 1fr); }
            .main-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .welcome-banner { flex-direction: column; text-align: center; gap: 1.5rem; }
            .welcome-avatar { order: -1; }
            .quick-links { grid-template-columns: repeat(2, 1fr); }
            .badges-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- WELCOME BANNER -->
        <div class="welcome-banner">
            <div class="welcome-content">
                <h1><?= $greeting ?>, <span><?= htmlspecialchars(explode(' ', $studentName)[0]) ?></span>!</h1>
                <p class="quote">"<?= htmlspecialchars($quote) ?>"</p>
                <div class="class-badge">
                    <i class="fas fa-graduation-cap"></i>
                    <span><?= $studentClass ?></span>
                </div>
            </div>
            <div class="welcome-avatar"><?= strtoupper(substr($studentName, 0, 1)) ?></div>
        </div>

        <!-- QUICK STATS -->
        <div class="quick-stats">
            <div class="quick-stat attendance">
                <div class="quick-stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="quick-stat-info">
                    <h3>94%</h3>
                    <p>Attendance Rate</p>
                </div>
            </div>
            <div class="quick-stat grade">
                <div class="quick-stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="quick-stat-info">
                    <h3>78%</h3>
                    <p>Average Grade</p>
                </div>
            </div>
            <div class="quick-stat assignments">
                <div class="quick-stat-icon"><i class="fas fa-tasks"></i></div>
                <div class="quick-stat-info">
                    <h3>3</h3>
                    <p>Pending Tasks</p>
                </div>
            </div>
            <div class="quick-stat position">
                <div class="quick-stat-icon"><i class="fas fa-trophy"></i></div>
                <div class="quick-stat-info">
                    <h3>5<sup>th</sup></h3>
                    <p>Class Position</p>
                </div>
            </div>
        </div>

        <!-- QUICK LINKS -->
        <div class="quick-links">
            <a href="timetable.php" class="quick-link">
                <i class="fas fa-calendar-alt"></i>
                <span>Timetable</span>
            </a>
            <a href="grades.php" class="quick-link">
                <i class="fas fa-chart-bar"></i>
                <span>My Grades</span>
            </a>
            <a href="attendance.php" class="quick-link">
                <i class="fas fa-clipboard-check"></i>
                <span>Attendance</span>
            </a>
            <a href="assignments/" class="quick-link">
                <i class="fas fa-file-alt"></i>
                <span>Assignments</span>
            </a>
            <a href="fees/status.php" class="quick-link">
                <i class="fas fa-credit-card"></i>
                <span>Fee Status</span>
            </a>
            <a href="messages.php" class="quick-link">
                <i class="fas fa-envelope"></i>
                <span>Messages</span>
            </a>
        </div>

        <!-- MAIN GRID -->
        <div class="main-grid">
            <div>
                <!-- TODAY'S SCHEDULE -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-clock"></i> Today's Classes</h3>
                        <a href="timetable.php" class="card-action">Full Timetable</a>
                    </div>
                    <div class="card-body">
                        <div class="schedule-timeline">
                            <div class="schedule-item">
                                <div class="schedule-time">8:00 - 8:45 AM</div>
                                <div class="schedule-subject">Mathematics</div>
                                <div class="schedule-teacher">Mr. Adebayo • Room 12</div>
                            </div>
                            <div class="schedule-item current">
                                <div class="schedule-time">9:00 - 9:45 AM</div>
                                <div class="schedule-subject">English Language</div>
                                <div class="schedule-teacher">Mrs. Johnson • Room 8</div>
                            </div>
                            <div class="schedule-item">
                                <div class="schedule-time">10:00 - 10:45 AM</div>
                                <div class="schedule-subject">Break</div>
                                <div class="schedule-teacher">Canteen</div>
                            </div>
                            <div class="schedule-item">
                                <div class="schedule-time">11:00 - 11:45 AM</div>
                                <div class="schedule-subject">Basic Science</div>
                                <div class="schedule-teacher">Mr. Okonkwo • Lab 2</div>
                            </div>
                            <div class="schedule-item">
                                <div class="schedule-time">12:00 - 12:45 PM</div>
                                <div class="schedule-subject">Social Studies</div>
                                <div class="schedule-teacher">Mrs. Eze • Room 5</div>
                            </div>
                            <div class="schedule-item">
                                <div class="schedule-time">1:00 - 1:45 PM</div>
                                <div class="schedule-subject">Computer Science</div>
                                <div class="schedule-teacher">Mr. Chukwu • ICT Room</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ASSIGNMENTS -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-tasks"></i> Assignments Due Soon</h3>
                        <a href="assignments/" class="card-action">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="assignment-card">
                            <div class="assignment-header">
                                <h4>Mathematics - Algebra Exercises</h4>
                                <span class="assignment-due urgent">Due Tomorrow</span>
                            </div>
                            <p class="assignment-desc">Complete exercises 1-20 from Chapter 5</p>
                            <div class="assignment-progress">
                                <div class="assignment-progress-fill" style="width: 60%;"></div>
                            </div>
                        </div>
                        <div class="assignment-card">
                            <div class="assignment-header">
                                <h4>English - Essay Writing</h4>
                                <span class="assignment-due soon">Due in 3 days</span>
                            </div>
                            <p class="assignment-desc">Write a 500-word essay on "My Role in Environmental Protection"</p>
                            <div class="assignment-progress">
                                <div class="assignment-progress-fill" style="width: 20%;"></div>
                            </div>
                        </div>
                        <div class="assignment-card">
                            <div class="assignment-header">
                                <h4>Basic Science - Project</h4>
                                <span class="assignment-due soon">Due in 5 days</span>
                            </div>
                            <p class="assignment-desc">Build a simple circuit demonstrating series and parallel connections</p>
                            <div class="assignment-progress">
                                <div class="assignment-progress-fill" style="width: 10%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <!-- RECENT GRADES -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> Recent Grades</h3>
                        <a href="grades.php" class="card-action">All Grades</a>
                    </div>
                    <div class="card-body">
                        <div class="grade-item">
                            <span class="grade-subject">Mathematics CA 2</span>
                            <span class="grade-value a">A (85%)</span>
                        </div>
                        <div class="grade-item">
                            <span class="grade-subject">English Test</span>
                            <span class="grade-value b">B+ (78%)</span>
                        </div>
                        <div class="grade-item">
                            <span class="grade-subject">Basic Science</span>
                            <span class="grade-value a">A- (82%)</span>
                        </div>
                        <div class="grade-item">
                            <span class="grade-subject">Social Studies</span>
                            <span class="grade-value b">B (72%)</span>
                        </div>
                        <div class="grade-item">
                            <span class="grade-subject">Computer Science</span>
                            <span class="grade-value a">A (90%)</span>
                        </div>
                    </div>
                </div>

                <!-- GAMIFICATION -->
                <div class="gamification-section">
                    <div class="gamification-header">
                        <h3><i class="fas fa-star"></i> Achievements</h3>
                        <div class="house-badge">
                            <i class="fas fa-shield-alt"></i>
                            Red House
                        </div>
                    </div>
                    <div class="badges-grid">
                        <div class="badge-item gold">
                            <i class="fas fa-medal"></i>
                            <span>Perfect<br>Attendance</span>
                        </div>
                        <div class="badge-item silver">
                            <i class="fas fa-book-reader"></i>
                            <span>Top Reader</span>
                        </div>
                        <div class="badge-item bronze">
                            <i class="fas fa-calculator"></i>
                            <span>Math Whiz</span>
                        </div>
                        <div class="badge-item special">
                            <i class="fas fa-heart"></i>
                            <span>Helper</span>
                        </div>
                    </div>
                    <p style="text-align: center; margin-top: 1rem; font-size: 0.85rem; color: var(--text-muted);">
                        <strong style="color: var(--pink);">1,250 XP</strong> • Level 8 Scholar
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php include dirname(__DIR__) . '/includes/ai-assistant.php'; ?>
</body>
</html>
