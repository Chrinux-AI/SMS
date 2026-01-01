<?php
/**
 * Principal Portal - Executive Overview
 * Complete dashboard with KPIs, staff monitor, approvals, and academic pulse
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$principalName = $_SESSION['full_name'] ?? 'Principal';
$greeting = date('H') < 12 ? 'Good Morning' : (date('H') < 17 ? 'Good Afternoon' : 'Good Evening');

$pageTitle = "Principal Dashboard";
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
            background: linear-gradient(90deg, var(--primary), var(--purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header p { color: var(--text-muted); }

        /* ===== KPI CARDS ===== */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .kpi-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            text-align: center;
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            border-radius: 16px 16px 0 0;
        }

        .kpi-card.attendance::before { background: var(--primary); }
        .kpi-card.fees::before { background: var(--success); }
        .kpi-card.grades::before { background: var(--purple); }
        .kpi-card.approvals::before { background: var(--warning); }

        .kpi-value {
            font-size: 3rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .kpi-card.attendance .kpi-value { color: var(--primary); }
        .kpi-card.fees .kpi-value { color: var(--success); }
        .kpi-card.grades .kpi-value { color: var(--purple); }
        .kpi-card.approvals .kpi-value { color: var(--warning); }

        .kpi-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
        }

        .kpi-change {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.8rem;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
        }

        .kpi-change.up { background: rgba(0,255,135,0.15); color: var(--success); }
        .kpi-change.down { background: rgba(255,71,87,0.15); color: var(--danger); }
        .kpi-change.neutral { background: rgba(255,184,0,0.15); color: var(--warning); }

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
        }

        .card-body { padding: 1.5rem; }

        /* ===== STAFF LIST ===== */
        .staff-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
        }

        .staff-item:last-child { border-bottom: none; }

        .staff-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .staff-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .staff-status.present { background: var(--success); }
        .staff-status.absent { background: var(--danger); }
        .staff-status.late { background: var(--warning); }

        .staff-name { font-size: 0.9rem; }

        .staff-time {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .staff-time.present { background: rgba(0,255,135,0.15); color: var(--success); }
        .staff-time.absent { background: rgba(255,71,87,0.15); color: var(--danger); }
        .staff-time.late { background: rgba(255,184,0,0.15); color: var(--warning); }

        /* ===== APPROVALS ===== */
        .approval-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
            margin-bottom: 0.75rem;
        }

        .approval-item:last-child { margin-bottom: 0; }

        .approval-info h4 {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.15rem;
        }

        .approval-info p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .approval-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-approve, .btn-reject {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-approve {
            background: var(--success);
            color: #000;
        }

        .btn-reject {
            background: transparent;
            border: 1px solid var(--danger);
            color: var(--danger);
        }

        .btn-approve:hover, .btn-reject:hover { transform: scale(1.1); }

        /* ===== ACADEMIC PULSE ===== */
        .class-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border);
        }

        .class-row:last-child { border-bottom: none; }

        .class-name { font-size: 0.9rem; }

        .class-students {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .class-avg {
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
        }

        .class-avg.high { background: rgba(0,255,135,0.15); color: var(--success); }
        .class-avg.medium { background: rgba(255,184,0,0.15); color: var(--warning); }
        .class-avg.low { background: rgba(255,71,87,0.15); color: var(--danger); }

        /* ===== ANNOUNCEMENT ===== */
        .announce-section {
            margin-top: 2rem;
        }

        .announce-card {
            background: linear-gradient(135deg, rgba(168,85,247,0.1), rgba(0,212,255,0.1));
            border: 1px solid var(--purple);
            border-radius: 16px;
            padding: 1.5rem;
        }

        .announce-card h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .announce-card h3 i { color: var(--purple); }

        .announce-textarea {
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

        .announce-textarea:focus { outline: none; border-color: var(--purple); }

        .announce-actions {
            display: flex;
            gap: 1rem;
        }

        .announce-btn {
            flex: 1;
            padding: 0.75rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .announce-btn.primary {
            background: linear-gradient(135deg, var(--purple), var(--primary));
            border: none;
            color: #fff;
        }

        .announce-btn.secondary {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .announce-btn:hover { transform: translateY(-2px); }

        @media (max-width: 1200px) {
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
            .main-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header class="header">
            <div>
                <h1><?= $greeting ?>, <span><?= htmlspecialchars(explode(' ', $principalName)[0]) ?></span>!</h1>
                <p>Executive Overview • <?= date('l, F j, Y') ?></p>
            </div>
        </header>

        <!-- KPI CARDS -->
        <div class="kpi-grid">
            <div class="kpi-card attendance">
                <div class="kpi-value">94%</div>
                <div class="kpi-label">School Attendance</div>
                <span class="kpi-change up"><i class="fas fa-arrow-up"></i> 2% from last week</span>
            </div>
            <div class="kpi-card fees">
                <div class="kpi-value">₦2.4M</div>
                <div class="kpi-label">Term Collections</div>
                <span class="kpi-change up"><i class="fas fa-arrow-up"></i> 15% ahead of target</span>
            </div>
            <div class="kpi-card grades">
                <div class="kpi-value">76%</div>
                <div class="kpi-label">Average Grade</div>
                <span class="kpi-change down"><i class="fas fa-arrow-down"></i> 3% from last term</span>
            </div>
            <div class="kpi-card approvals">
                <div class="kpi-value">5</div>
                <div class="kpi-label">Pending Approvals</div>
                <span class="kpi-change neutral"><i class="fas fa-clock"></i> Needs attention</span>
            </div>
        </div>

        <!-- MAIN GRID -->
        <div class="main-grid">
            <!-- STAFF ATTENDANCE -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-users"></i> Staff Attendance</h3>
                    <span class="card-badge">Today</span>
                </div>
                <div class="card-body">
                    <div class="staff-item">
                        <div class="staff-info">
                            <span class="staff-status present"></span>
                            <span class="staff-name">Mr. Adebayo (Mathematics)</span>
                        </div>
                        <span class="staff-time present">7:45 AM</span>
                    </div>
                    <div class="staff-item">
                        <div class="staff-info">
                            <span class="staff-status present"></span>
                            <span class="staff-name">Mrs. Johnson (English)</span>
                        </div>
                        <span class="staff-time present">7:52 AM</span>
                    </div>
                    <div class="staff-item">
                        <div class="staff-info">
                            <span class="staff-status late"></span>
                            <span class="staff-name">Mr. Okonkwo (Science)</span>
                        </div>
                        <span class="staff-time late">9:15 AM</span>
                    </div>
                    <div class="staff-item">
                        <div class="staff-info">
                            <span class="staff-status absent"></span>
                            <span class="staff-name">Mrs. Eze (Social Studies)</span>
                        </div>
                        <span class="staff-time absent">Sick Leave</span>
                    </div>
                    <div class="staff-item">
                        <div class="staff-info">
                            <span class="staff-status present"></span>
                            <span class="staff-name">Mr. Chukwu (Computer)</span>
                        </div>
                        <span class="staff-time present">7:30 AM</span>
                    </div>
                </div>
            </div>

            <!-- PENDING APPROVALS -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-clipboard-check"></i> Approvals</h3>
                    <span class="card-badge">5</span>
                </div>
                <div class="card-body">
                    <div class="approval-item">
                        <div class="approval-info">
                            <h4>Leave Request</h4>
                            <p>Mr. Okonkwo • 3 days</p>
                        </div>
                        <div class="approval-actions">
                            <button class="btn-approve"><i class="fas fa-check"></i></button>
                            <button class="btn-reject"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div class="approval-item">
                        <div class="approval-info">
                            <h4>Purchase Order</h4>
                            <p>Lab Equipment • ₦85,000</p>
                        </div>
                        <div class="approval-actions">
                            <button class="btn-approve"><i class="fas fa-check"></i></button>
                            <button class="btn-reject"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div class="approval-item">
                        <div class="approval-info">
                            <h4>Student Transfer</h4>
                            <p>Chinedu Okoro • SSS 1A</p>
                        </div>
                        <div class="approval-actions">
                            <button class="btn-approve"><i class="fas fa-check"></i></button>
                            <button class="btn-reject"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACADEMIC PULSE -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line"></i> Academic Pulse</h3>
                </div>
                <div class="card-body">
                    <div class="class-row">
                        <div>
                            <div class="class-name">JSS 1</div>
                            <div class="class-students">120 students</div>
                        </div>
                        <span class="class-avg high">72%</span>
                    </div>
                    <div class="class-row">
                        <div>
                            <div class="class-name">JSS 2</div>
                            <div class="class-students">115 students</div>
                        </div>
                        <span class="class-avg high">78%</span>
                    </div>
                    <div class="class-row">
                        <div>
                            <div class="class-name">JSS 3</div>
                            <div class="class-students">108 students</div>
                        </div>
                        <span class="class-avg medium">65%</span>
                    </div>
                    <div class="class-row">
                        <div>
                            <div class="class-name">SSS 1</div>
                            <div class="class-students">95 students</div>
                        </div>
                        <span class="class-avg high">74%</span>
                    </div>
                    <div class="class-row">
                        <div>
                            <div class="class-name">SSS 2</div>
                            <div class="class-students">88 students</div>
                        </div>
                        <span class="class-avg high">71%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- QUICK ANNOUNCE -->
        <div class="announce-section">
            <div class="announce-card">
                <h3><i class="fas fa-bullhorn"></i> Quick Announcement</h3>
                <textarea class="announce-textarea" placeholder="Type your announcement to all staff and students..."></textarea>
                <div class="announce-actions">
                    <button class="announce-btn secondary"><i class="fas fa-users"></i> Staff Only</button>
                    <button class="announce-btn primary"><i class="fas fa-paper-plane"></i> Send to All</button>
                </div>
            </div>
        </div>
    </div>
    <?php include dirname(__DIR__) . "/includes/ai-assistant.php"; ?>
</body>
</html>
