<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';


require_role('admin'); // TEST_MODE will allow access for testing

// Fetch All Incidents
$incidents = db()->fetchAll("
    SELECT di.*,
           CONCAT(u.first_name, ' ', u.last_name) as student_name,
           c.class_name
    FROM discipline_incidents di
    JOIN students st ON di.student_id = st.id
    JOIN users u ON st.user_id = u.id
    LEFT JOIN class_enrollments ce ON st.id = ce.student_id AND ce.is_active = 1
    LEFT JOIN classes c ON ce.class_id = c.id
    ORDER BY di.incident_date DESC
");

// Stats
$total_incidents = count($incidents);
$pending_review = count(array_filter($incidents, fn($i) => $i['status'] == 'pending'));
$resolved_this_month = db()->fetchOne("
    SELECT COUNT(*) as count FROM discipline_incidents
    WHERE status = 'resolved' AND MONTH(resolved_date) = MONTH(CURDATE())
")['count'] ?? 0;

$page_title = 'Discipline & Behavior Tracking';
$page_icon = 'gavel';
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
                    <div class="page-icon-orb red"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <button class="cyber-btn green" onclick="window.location.href='log-incident.php'">
                        <i class="fas fa-plus"></i> Log Incident
                    </button>
                </div>
            </header>

            <div class="cyber-content slide-in">

                <section class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
                    <div class="stat-orb">
                        <div class="stat-icon red"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $total_incidents; ?></div>
                            <div class="stat-label">Total Incidents</div>
                        </div>
                    </div>
                    <div class="stat-orb">
                        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $pending_review; ?></div>
                            <div class="stat-label">Pending Review</div>
                        </div>
                    </div>
                    <div class="stat-orb">
                        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $resolved_this_month; ?></div>
                            <div class="stat-label">Resolved This Month</div>
                        </div>
                    </div>
                </section>

                <section class="holo-card" style="margin-top:30px;">
                    <h3 style="margin-bottom:20px;"><i class="fas fa-list"></i> Discipline Incidents</h3>
                    <div class="cyber-table-container">
                        <table class="cyber-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Class</th>
                                    <th>Incident Type</th>
                                    <th>Severity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($incidents)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align:center;padding:30px;">
                                            <i class="fas fa-smile" style="font-size:3rem;color:var(--neon-green);margin-bottom:15px;"></i>
                                            <p style="color:var(--text-muted);">No discipline incidents recorded. Keep up the great work!</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($incidents as $incident): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($incident['incident_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($incident['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($incident['class_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($incident['incident_type']); ?></td>
                                            <td>
                                                <span class="cyber-badge <?php
                                                                            echo $incident['severity'] == 'high' ? 'red' : ($incident['severity'] == 'medium' ? 'orange' : 'green');
                                                                            ?>">
                                                    <?php echo ucfirst($incident['severity']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="cyber-badge <?php echo $incident['status'] == 'resolved' ? 'green' : 'orange'; ?>">
                                                    <?php echo ucfirst($incident['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="cyber-btn small cyan" onclick="window.location.href='view-incident.php?id=<?php echo $incident['id']; ?>'">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>

</html>
