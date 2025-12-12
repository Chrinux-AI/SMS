<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Use require_role for TEST_MODE support
require_role('nurse', '../login.php');

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Health Stats
$total_students = db()->fetchOne("SELECT COUNT(*) as count FROM students WHERE is_active = 1")['count'] ?? 0;
$pending_checkups = db()->fetchOne("SELECT COUNT(*) as count FROM health_checkups WHERE status = 'scheduled' AND checkup_date >= CURDATE()")['count'] ?? 0;
$active_medications = db()->fetchOne("SELECT COUNT(*) as count FROM health_medications WHERE status = 'active'")['count'] ?? 0;

// Today's Visits
$today_visits = db()->fetchOne("SELECT COUNT(*) as count FROM health_visits WHERE DATE(visit_date) = CURDATE()")['count'] ?? 0;

// Vaccination Status
$vaccination_due = db()->fetchOne("
    SELECT COUNT(*) as count
    FROM health_vaccinations
    WHERE due_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status != 'completed'
")['count'] ?? 0;

// Recent Visits
$recent_visits = db()->fetchAll("
    SELECT hv.*,
           CONCAT(u.first_name, ' ', u.last_name) as student_name,
           c.class_name
    FROM health_visits hv
    JOIN students st ON hv.student_id = st.id
    JOIN users u ON st.user_id = u.id
    LEFT JOIN class_enrollments ce ON st.id = ce.student_id AND ce.is_active = 1
    LEFT JOIN classes c ON ce.class_id = c.id
    ORDER BY hv.visit_date DESC
    LIMIT 10
");

// Common Ailments (This Month)
$common_ailments = db()->fetchAll("
    SELECT ailment, COUNT(*) as count
    FROM health_visits
    WHERE MONTH(visit_date) = MONTH(CURDATE())
    GROUP BY ailment
    ORDER BY count DESC
    LIMIT 5
");

$page_title = 'Health Management';
$page_icon = 'user-md';
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
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb red"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <button class="cyber-btn cyan" onclick="window.location.href='../admin/health/new-visit.php'">
                        <i class="fas fa-plus"></i> New Visit
                    </button>
                    <div class="user-card" style="padding:8px 15px;">
                        <div class="user-avatar red" style="width:35px;height:35px;">
                            <?php echo strtoupper(substr($full_name, 0, 2)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($full_name); ?></div>
                            <div class="user-role">School Nurse</div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="cyber-content slide-in">

                <section class="orb-grid">
                    <div class="stat-orb">
                        <div class="stat-icon cyan"><i class="fas fa-users"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($total_students); ?></div>
                            <div class="stat-label">Total Students</div>
                            <div class="stat-trend"><i class="fas fa-user-graduate"></i> Active</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon green"><i class="fas fa-stethoscope"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $today_visits; ?></div>
                            <div class="stat-label">Visits Today</div>
                            <div class="stat-trend"><i class="fas fa-calendar-day"></i> Today</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon orange"><i class="fas fa-calendar-check"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $pending_checkups; ?></div>
                            <div class="stat-label">Pending Checkups</div>
                            <div class="stat-trend"><i class="fas fa-clipboard-list"></i> Scheduled</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon purple"><i class="fas fa-pills"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $active_medications; ?></div>
                            <div class="stat-label">Active Medications</div>
                            <div class="stat-trend"><i class="fas fa-prescription-bottle"></i> Ongoing</div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon red"><i class="fas fa-syringe"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $vaccination_due; ?></div>
                            <div class="stat-label">Vaccinations Due</div>
                            <div class="stat-trend down"><i class="fas fa-exclamation-triangle"></i> This Week</div>
                        </div>
                    </div>
                </section>

                <!-- Quick Actions -->
                <section class="holo-card" style="margin-top:30px;">
                    <h3 style="margin-bottom:20px;"><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
                        <a href="../admin/health/visits.php" class="action-card">
                            <div class="action-icon cyan"><i class="fas fa-notes-medical"></i></div>
                            <h4>Health Visits</h4>
                            <p>Log & track visits</p>
                        </a>

                        <a href="../admin/health/medical-records.php" class="action-card">
                            <div class="action-icon green"><i class="fas fa-file-medical"></i></div>
                            <h4>Medical Records</h4>
                            <p>Student health files</p>
                        </a>

                        <a href="../admin/health/vaccinations.php" class="action-card">
                            <div class="action-icon orange"><i class="fas fa-syringe"></i></div>
                            <h4>Vaccinations</h4>
                            <p>Track immunizations</p>
                        </a>

                        <a href="../admin/health/growth-charts.php" class="action-card">
                            <div class="action-icon purple"><i class="fas fa-chart-line"></i></div>
                            <h4>Growth Charts</h4>
                            <p>Height & weight tracking</p>
                        </a>
                    </div>
                </section>

                <!-- Common Ailments -->
                <?php if (!empty($common_ailments)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-virus"></i> Common Ailments (This Month)</h3>
                        <div style="display:grid;gap:12px;">
                            <?php foreach ($common_ailments as $ailment): ?>
                                <div style="background:rgba(255,87,34,0.1);border-left:3px solid var(--red-alert);padding:15px;border-radius:8px;display:flex;justify-content:space-between;align-items:center;">
                                    <div>
                                        <h4 style="color:var(--text-primary);margin:0;"><?php echo htmlspecialchars($ailment['ailment']); ?></h4>
                                    </div>
                                    <span class="cyber-badge red"><?php echo $ailment['count']; ?> cases</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Recent Visits -->
                <?php if (!empty($recent_visits)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-history"></i> Recent Health Visits</h3>
                        <div class="cyber-table-container">
                            <table class="cyber-table">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Ailment</th>
                                        <th>Visit Date</th>
                                        <th>Action Taken</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_visits as $visit): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($visit['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($visit['class_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($visit['ailment']); ?></td>
                                            <td><?php echo date('M d, Y h:i A', strtotime($visit['visit_date'])); ?></td>
                                            <td><span class="cyber-badge cyan"><?php echo htmlspecialchars($visit['action_taken'] ?? 'Observed'); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>
