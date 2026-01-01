<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';


require_role('admin'); // TEST_MODE will allow access for testing

// Gamification Stats
$total_students = db()->fetchOne("SELECT COUNT(*) as count FROM students WHERE is_active = 1")['count'] ?? 0;
$active_houses = db()->fetchOne("SELECT COUNT(*) as count FROM gamification_houses WHERE is_active = 1")['count'] ?? 0;

// Top Students (Leaderboard)
$leaderboard = db()->fetchAll("
    SELECT CONCAT(u.first_name, ' ', u.last_name) as student_name,
           c.class_name,
           gp.total_points,
           gp.badges_earned,
           gp.current_level,
           h.house_name
    FROM gamification_points gp
    JOIN students st ON gp.student_id = st.id
    JOIN users u ON st.user_id = u.id
    LEFT JOIN class_enrollments ce ON st.id = ce.student_id AND ce.is_active = 1
    LEFT JOIN classes c ON ce.class_id = c.id
    LEFT JOIN gamification_houses h ON gp.house_id = h.id
    ORDER BY gp.total_points DESC
    LIMIT 10
");

// House Cup Standings
$house_standings = db()->fetchAll("
    SELECT h.house_name, h.house_color,
           SUM(gp.total_points) as house_points,
           COUNT(DISTINCT gp.student_id) as member_count
    FROM gamification_houses h
    LEFT JOIN gamification_points gp ON h.id = gp.house_id
    WHERE h.is_active = 1
    GROUP BY h.id
    ORDER BY house_points DESC
");

$page_title = 'Gamification & House System';
$page_icon = 'trophy';
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
                    <button class="cyber-btn green" onclick="window.location.href='award-points.php'">
                        <i class="fas fa-plus"></i> Award Points
                    </button>
                </div>
            </header>

            <div class="cyber-content slide-in">

                <section class="orb-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
                    <div class="stat-orb">
                        <div class="stat-icon cyan"><i class="fas fa-users"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($total_students); ?></div>
                            <div class="stat-label">Total Students</div>
                        </div>
                    </div>
                    <div class="stat-orb">
                        <div class="stat-icon golden"><i class="fas fa-house-user"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $active_houses; ?></div>
                            <div class="stat-label">Active Houses</div>
                        </div>
                    </div>
                </section>

                <!-- House Cup Standings -->
                <?php if (!empty($house_standings)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-trophy"></i> House Cup Standings</h3>
                        <div style="display:grid;gap:15px;">
                            <?php foreach ($house_standings as $index => $house): ?>
                                <div style="background:linear-gradient(135deg,rgba(255,193,7,0.15),rgba(255,152,0,0.1));border-left:4px solid <?php echo $house['house_color'] ?? '#FFD700'; ?>;padding:20px;border-radius:12px;display:flex;justify-content:space-between;align-items:center;">
                                    <div style="display:flex;align-items:center;gap:20px;">
                                        <div style="font-size:2rem;color:var(--golden-pulse);font-weight:bold;">
                                            <?php if ($index == 0): ?>🥇<?php elseif ($index == 1): ?>🥈<?php elseif ($index == 2): ?>🥉<?php else: ?>#<?php echo $index + 1; ?><?php endif; ?>
                                        </div>
                                        <div>
                                            <h3 style="margin:0;color:<?php echo $house['house_color'] ?? '#FFD700'; ?>;"><?php echo htmlspecialchars($house['house_name']); ?></h3>
                                            <p style="margin:5px 0 0;color:var(--text-muted);"><?php echo $house['member_count']; ?> members</p>
                                        </div>
                                    </div>
                                    <div style="text-align:right;">
                                        <div style="font-size:2rem;color:var(--golden-pulse);font-weight:bold;">
                                            <?php echo number_format($house['house_points'] ?? 0); ?>
                                        </div>
                                        <div style="color:var(--text-muted);font-size:0.9rem;">Points</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Student Leaderboard -->
                <?php if (!empty($leaderboard)): ?>
                    <section class="holo-card" style="margin-top:30px;">
                        <h3 style="margin-bottom:20px;"><i class="fas fa-medal"></i> Top Students Leaderboard</h3>
                        <div class="cyber-table-container">
                            <table class="cyber-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Student</th>
                                        <th>Class</th>
                                        <th>House</th>
                                        <th>Points</th>
                                        <th>Badges</th>
                                        <th>Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leaderboard as $index => $student): ?>
                                        <tr>
                                            <td>
                                                <?php if ($index < 3): ?>
                                                    <span class="cyber-badge golden"><?php
                                                                                        echo $index == 0 ? '🥇' : ($index == 1 ? '🥈' : '🥉');
                                                                                        ?></span>
                                                <?php else: ?>
                                                    #<?php echo $index + 1; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($student['class_name'] ?? 'N/A'); ?></td>
                                            <td><span class="cyber-badge purple"><?php echo htmlspecialchars($student['house_name'] ?? 'Unassigned'); ?></span></td>
                                            <td><span class="cyber-badge cyan"><?php echo number_format($student['total_points'] ?? 0); ?></span></td>
                                            <td><?php echo $student['badges_earned'] ?? 0; ?></td>
                                            <td><span class="cyber-badge green">Level <?php echo $student['current_level'] ?? 1; ?></span></td>
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

    <script src="../../assets/js/main.js"></script>
</body>

</html>
