<?php
/**
 * Teacher Portal - Complete Homepage
 * Classroom Cockpit with next class countdown, tasks, and class management
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$teacherName = $_SESSION['full_name'] ?? 'Teacher';
$greeting = date('H') < 12 ? 'Good Morning' : (date('H') < 17 ? 'Good Afternoon' : 'Good Evening');

$pageTitle = "Teacher Dashboard";
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

        .header-left h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .header-left h1 span {
            background: linear-gradient(90deg, var(--primary), var(--success));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-left p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .header-right {
            display: flex;
            align-items: center;
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
            transition: all 0.2s;
        }

        .header-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--purple), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #fff;
            font-size: 1.1rem;
        }

        /* ===== NEXT CLASS CARD ===== */
        .next-class-card {
            background: linear-gradient(135deg, rgba(0, 255, 135, 0.15), rgba(0, 212, 255, 0.1));
            border: 2px solid var(--success);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 2rem;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .next-class-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -5%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(0, 255, 135, 0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .next-class-label {
            font-size: 0.85rem;
            color: var(--success);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .next-class-label i {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .next-class-info h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .next-class-details {
            display: flex;
            gap: 2rem;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .next-class-details span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .next-class-details i {
            color: var(--primary);
        }

        .countdown-box {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .countdown-timer {
            font-size: 4rem;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            background: linear-gradient(90deg, var(--primary), var(--success));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
        }

        .countdown-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        /* ===== QUICK ACTIONS ===== */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-action {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            text-decoration: none;
            color: var(--text);
            transition: all 0.3s;
        }

        .quick-action:hover {
            background: rgba(0, 212, 255, 0.1);
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        .quick-action-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            background: rgba(0, 255, 135, 0.15);
            color: var(--success);
        }

        .quick-action-text h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.15rem;
        }

        .quick-action-text p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ===== MAIN GRID ===== */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.5rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
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

        .card-header h3 i { color: var(--primary); }

        .card-badge {
            background: var(--danger);
            color: #fff;
            font-size: 0.7rem;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            font-weight: 600;
        }

        .card-body { padding: 1.5rem; }

        /* ===== TASK LIST ===== */
        .task-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 0.75rem;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }

        .task-item:last-child { margin-bottom: 0; }

        .task-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .task-icon.grading { background: rgba(255,184,0,0.15); color: var(--warning); }
        .task-icon.attendance { background: rgba(0,212,255,0.15); color: var(--primary); }
        .task-icon.message { background: rgba(168,85,247,0.15); color: var(--purple); }

        .task-content h4 {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.15rem;
        }

        .task-content p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ===== STUDENT SPOTLIGHT ===== */
        .spotlight-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
        }

        .spotlight-item:last-child { border-bottom: none; }

        .spotlight-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .spotlight-info h4 {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.1rem;
        }

        .spotlight-info p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ===== MY CLASSES ===== */
        .my-classes-section {
            margin-top: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .section-header h2 {
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-header h2 i { color: var(--primary); }

        .classes-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        .class-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.25rem;
            text-decoration: none;
            color: var(--text);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .class-card:hover {
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        .class-card-subject {
            font-size: 1rem;
            font-weight: 600;
        }

        .class-card-class {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .class-card-students {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            padding: 0.4rem 0.75rem;
            background: rgba(0, 255, 135, 0.1);
            border-radius: 20px;
            color: var(--success);
            width: fit-content;
        }

        @media (max-width: 1200px) {
            .main-grid { grid-template-columns: 1fr 1fr; }
            .classes-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .next-class-card { grid-template-columns: 1fr; text-align: center; }
            .countdown-box { order: -1; }
            .quick-actions { grid-template-columns: repeat(2, 1fr); }
            .main-grid { grid-template-columns: 1fr; }
            .classes-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header class="header">
            <div class="header-left">
                <h1><?= $greeting ?>, <span><?= htmlspecialchars(explode(' ', $teacherName)[0]) ?></span>!</h1>
                <p><?= date('l, F j, Y') ?></p>
            </div>
            <div class="header-right">
                <button class="header-btn" title="Messages"><i class="fas fa-envelope"></i></button>
                <button class="header-btn" title="Notifications"><i class="fas fa-bell"></i></button>
                <div class="user-avatar"><?= strtoupper(substr($teacherName, 0, 1)) ?></div>
            </div>
        </header>

        <!-- NEXT CLASS CARD -->
        <div class="next-class-card">
            <div class="next-class-info">
                <div class="next-class-label">
                    <i class="fas fa-circle"></i> Next Class Starting Soon
                </div>
                <h2>Mathematics - JSS 2A</h2>
                <div class="next-class-details">
                    <span><i class="fas fa-clock"></i> 9:00 AM</span>
                    <span><i class="fas fa-door-open"></i> Room 12</span>
                    <span><i class="fas fa-users"></i> 35 Students</span>
                    <span><i class="fas fa-book"></i> Chapter 5: Algebra</span>
                </div>
            </div>
            <div class="countdown-box">
                <div class="countdown-timer" id="countdown">15:42</div>
                <div class="countdown-label">Minutes Until Class</div>
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="quick-actions">
            <a href="ai-lesson-planner.php" class="quick-action" style="background: linear-gradient(135deg, rgba(0,212,255,0.1), rgba(168,85,247,0.1)); border-color: var(--primary);">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, var(--primary), var(--purple)); color: #fff;"><i class="fas fa-brain"></i></div>
                <div class="quick-action-text">
                    <h4>AI Lesson Planner</h4>
                    <p>Generate NERDC plans</p>
                </div>
            </a>
            <a href="attendance.php" class="quick-action">
                <div class="quick-action-icon"><i class="fas fa-clipboard-check"></i></div>
                <div class="quick-action-text">
                    <h4>Mark Attendance</h4>
                    <p>Take class attendance</p>
                </div>
            </a>
            <a href="grades/entry.php" class="quick-action">
                <div class="quick-action-icon"><i class="fas fa-pen"></i></div>
                <div class="quick-action-text">
                    <h4>Enter Grades</h4>
                    <p>Record student scores</p>
                </div>
            </a>
            <a href="assignments/create.php" class="quick-action">
                <div class="quick-action-icon"><i class="fas fa-file-upload"></i></div>
                <div class="quick-action-text">
                    <h4>Create Assignment</h4>
                    <p>Post new homework</p>
                </div>
            </a>
        </div>

        <!-- MAIN GRID -->
        <div class="main-grid">
            <!-- PENDING TASKS -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-tasks"></i> Pending Tasks</h3>
                    <span class="card-badge">5</span>
                </div>
                <div class="card-body">
                    <div class="task-item">
                        <div class="task-icon grading"><i class="fas fa-pen"></i></div>
                        <div class="task-content">
                            <h4>Grade JSS 2A Math Test</h4>
                            <p>35 papers • Due today</p>
                        </div>
                    </div>
                    <div class="task-item">
                        <div class="task-icon attendance"><i class="fas fa-clipboard-list"></i></div>
                        <div class="task-content">
                            <h4>Submit Lesson Plan</h4>
                            <p>Week 12 • Due Jan 5</p>
                        </div>
                    </div>
                    <div class="task-item">
                        <div class="task-icon grading"><i class="fas fa-pen"></i></div>
                        <div class="task-content">
                            <h4>Mark SSS 1 Assignments</h4>
                            <p>28 submissions</p>
                        </div>
                    </div>
                    <div class="task-item">
                        <div class="task-icon message"><i class="fas fa-comment"></i></div>
                        <div class="task-content">
                            <h4>Reply to Parent Message</h4>
                            <p>Mrs. Okonkwo</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MESSAGES -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-envelope"></i> Messages</h3>
                    <span class="card-badge">3</span>
                </div>
                <div class="card-body">
                    <div class="task-item">
                        <div class="task-icon message"><i class="fas fa-user"></i></div>
                        <div class="task-content">
                            <h4>Mrs. Okonkwo (Parent)</h4>
                            <p>Regarding Chinedu's progress...</p>
                        </div>
                    </div>
                    <div class="task-item">
                        <div class="task-icon attendance"><i class="fas fa-building"></i></div>
                        <div class="task-content">
                            <h4>Principal's Office</h4>
                            <p>Staff meeting reminder</p>
                        </div>
                    </div>
                    <div class="task-item">
                        <div class="task-icon grading"><i class="fas fa-book"></i></div>
                        <div class="task-content">
                            <h4>Math Department</h4>
                            <p>New curriculum update</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STUDENT SPOTLIGHT -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-star"></i> Student Spotlight</h3>
                </div>
                <div class="card-body">
                    <h4 style="font-size: 0.85rem; color: var(--warning); margin-bottom: 1rem;">
                        <i class="fas fa-birthday-cake"></i> Birthdays Today
                    </h4>
                    <div class="spotlight-item">
                        <div class="spotlight-avatar">C</div>
                        <div class="spotlight-info">
                            <h4>Chinedu Okoro</h4>
                            <p>JSS 2A • Turns 13</p>
                        </div>
                    </div>
                    <div class="spotlight-item">
                        <div class="spotlight-avatar">A</div>
                        <div class="spotlight-info">
                            <h4>Amaka Eze</h4>
                            <p>SSS 1B • Turns 16</p>
                        </div>
                    </div>
                    <h4 style="font-size: 0.85rem; color: var(--danger); margin: 1rem 0;">
                        <i class="fas fa-exclamation-triangle"></i> Needs Attention
                    </h4>
                    <div class="spotlight-item">
                        <div class="spotlight-avatar">E</div>
                        <div class="spotlight-info">
                            <h4>Emeka Nwosu</h4>
                            <p>JSS 2A • 3 absences this week</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MY CLASSES -->
        <div class="my-classes-section">
            <div class="section-header">
                <h2><i class="fas fa-chalkboard-teacher"></i> My Classes</h2>
                <a href="classes/" style="color: var(--primary); text-decoration: none; font-size: 0.9rem;">View All</a>
            </div>
            <div class="classes-grid">
                <a href="class-view.php?id=1" class="class-card">
                    <div class="class-card-subject">Mathematics</div>
                    <div class="class-card-class">JSS 2A</div>
                    <div class="class-card-students"><i class="fas fa-users"></i> 35 students</div>
                </a>
                <a href="class-view.php?id=2" class="class-card">
                    <div class="class-card-subject">Mathematics</div>
                    <div class="class-card-class">JSS 2B</div>
                    <div class="class-card-students"><i class="fas fa-users"></i> 32 students</div>
                </a>
                <a href="class-view.php?id=3" class="class-card">
                    <div class="class-card-subject">Further Mathematics</div>
                    <div class="class-card-class">SSS 1A</div>
                    <div class="class-card-students"><i class="fas fa-users"></i> 28 students</div>
                </a>
                <a href="class-view.php?id=4" class="class-card">
                    <div class="class-card-subject">Further Mathematics</div>
                    <div class="class-card-class">SSS 1B</div>
                    <div class="class-card-students"><i class="fas fa-users"></i> 30 students</div>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Countdown Timer
        let totalSeconds = 15 * 60 + 42;
        function updateCountdown() {
            if (totalSeconds > 0) {
                totalSeconds--;
                const mins = Math.floor(totalSeconds / 60);
                const secs = totalSeconds % 60;
                document.getElementById('countdown').textContent =
                    String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
            }
        }
        setInterval(updateCountdown, 1000);
    </script>
</body>
</html>
