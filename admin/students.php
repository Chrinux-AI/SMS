<?php

/**
 * Students Management - Nature Neural Interface
 */

session_start();

require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Require admin access
require_role('admin');

$page_title = 'Student Neural Database';
$page_icon = 'user-graduate';

// Get all students
$students = db()->fetchAll("SELECT * FROM students ORDER BY created_at DESC");

// Calculate statistics
$total_students = count($students);
$active_students = count(array_filter($students, fn($s) => $s['status'] === 'active'));

// Get average attendance per student
$attendance_stats = db()->fetchAll("
    SELECT
        s.id,
        s.first_name,
        s.last_name,
        COUNT(ar.id) as total_records,
        SUM(CASE WHEN ar.status IN ('present', 'late') THEN 1 ELSE 0 END) as present_count
    FROM students s
    LEFT JOIN attendance_records ar ON s.id = ar.student_id
    GROUP BY s.id
");

// Create attendance map
$attendance_map = [];
foreach ($attendance_stats as $stat) {
    $attendance_rate = $stat['total_records'] > 0
        ? round(($stat['present_count'] / $stat['total_records']) * 100, 1)
        : 0;
    $attendance_map[$stat['id']] = $attendance_rate;
}

$avg_attendance = $total_students > 0 ? round(array_sum($attendance_map) / $total_students, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/attendance/manifest.json">
    <meta name="theme-color" content="#00BFFF">
    <link rel="apple-touch-icon" href="/attendance/assets/images/icons/icon-192x192.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo APP_NAME; ?></title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Cyberpunk UI Framework -->
    <link href="../assets/css/cyberpunk-ui.css" rel="stylesheet">

</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>
    <div class="cyber-bg">
        <div class="starfield"></div>
    </div>
    <div class="cyber-grid"></div>

    </div>


    <!-- Main Layout -->
    <div class="cyber-layout">

        <?php include '../includes/cyber-nav.php'; ?>

        <!-- Main Content -->
        <main class="cyber-main">
            <!-- Header -->
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb">
                        <i class="fas fa-<?php echo $page_icon; ?>"></i>
                    </div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>

                <div class="header-actions">
                    <!-- Add Student Button -->
                    <a href="student-add.php" class="cyber-btn cyber-btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Add Student</span>
                    </a>

                    <!-- Search -->
                    <input type="search"
                        class="cyber-input"
                        placeholder="Search students..."
                        id="searchInput"
                        style="width: 250px; margin: 0;"
                        onkeyup="searchStudents()">
                </div>
            </header>

            <!-- Content -->
            <div class="cyber-content slide-in">

                <!-- Statistics Orbs -->
                <section class="orb-grid">
                    <div class="stat-orb">
                        <div class="stat-icon cyan">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($total_students); ?></div>
                            <div class="stat-label">Total Students</div>
                            <div class="stat-trend up">
                                <i class="fas fa-database"></i>
                                <span>In Database</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon green">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($active_students); ?></div>
                            <div class="stat-label">Active Students</div>
                            <div class="stat-trend up">
                                <i class="fas fa-check-circle"></i>
                                <span>Currently Enrolled</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon purple">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $avg_attendance; ?>%</div>
                            <div class="stat-label">Average Attendance</div>
                            <div class="stat-trend <?php echo $avg_attendance >= 90 ? 'up' : 'down'; ?>">
                                <i class="fas fa-chart-line"></i>
                                <span>Overall Rate</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-orb">
                        <div class="stat-icon red">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">
                                <?php
                                $grades = array_unique(array_column($students, 'grade_level'));
                                echo count($grades);
                                ?>
                            </div>
                            <div class="stat-label">Grade Levels</div>
                            <div class="stat-trend up">
                                <i class="fas fa-layer-group"></i>
                                <span>Active Grades</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Students Table -->
                <section class="holo-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <h2 style="margin: 0; display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-database" style="color: var(--cyber-cyan);"></i>
                            <span>Student Records</span>
                        </h2>
                        <span class="cyber-badge primary"><?php echo $total_students; ?> Records</span>
                    </div>

                    <?php if (!empty($students)): ?>
                        <div style="overflow-x: auto;">
                            <table class="holo-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Grade</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Attendance</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTable">
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td>
                                                <span style="font-family: 'Orbitron', monospace; color: var(--cyber-cyan); font-weight: 700;">
                                                    <?php echo htmlspecialchars($student['student_id'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 12px;">
                                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--cyber-cyan), var(--hologram-purple)); display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                                        <?php echo strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <div style="font-weight: 600; color: var(--text-primary);">
                                                            <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                                                        </div>
                                                        <div style="font-size: 0.8rem; color: var(--text-muted);">
                                                            <?php echo date('M d, Y', strtotime($student['date_of_birth'] ?? 'now')); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="cyber-badge primary">
                                                    Grade <?php echo $student['grade_level'] ?? '0'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span style="color: var(--text-muted);">
                                                    <?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span style="color: var(--text-muted);">
                                                    <?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $rate = $attendance_map[$student['id']] ?? 0;
                                                $badge_class = $rate >= 90 ? 'success' : ($rate >= 75 ? 'warning' : 'danger');
                                                ?>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <div style="flex: 1; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden;">
                                                        <div style="width: <?php echo $rate; ?>%; height: 100%; background: var(--<?php
                                                                                                                                    echo $rate >= 90 ? 'neon-green' : ($rate >= 75 ? 'golden-pulse' : 'cyber-red');
                                                                                                                                    ?>); transition: width 1s ease;"></div>
                                                    </div>
                                                    <span class="cyber-badge <?php echo $badge_class; ?>" style="min-width: 60px; text-align: center;">
                                                        <?php echo $rate; ?>%
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="cyber-badge <?php echo $student['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($student['status'] ?? 'inactive'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 8px;">
                                                    <button class="cyber-btn cyber-btn-primary"
                                                        style="padding: 6px 12px; font-size: 0.85rem;"
                                                        onclick="viewStudent(<?php echo $student['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="cyber-btn cyber-btn-outline"
                                                        style="padding: 6px 12px; font-size: 0.85rem;"
                                                        onclick="editStudent(<?php echo $student['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 60px 20px;">
                            <div style="width: 120px; height: 120px; margin: 0 auto 25px; border-radius: 50%; background: linear-gradient(135deg, var(--cyber-cyan), var(--hologram-purple)); display: flex; align-items: center; justify-content: center; box-shadow: 0 0 40px rgba(0, 191, 255, 0.5);">
                                <i class="fas fa-user-plus" style="font-size: 3rem; color: var(--text-primary);"></i>
                            </div>
                            <h3 style="color: var(--text-primary); margin-bottom: 15px;">No Students Found</h3>
                            <p style="color: var(--text-muted); margin-bottom: 25px;">Start by adding your first student to the database</p>
                            <a href="student-add.php" class="cyber-btn cyber-btn-primary">
                                <i class="fas fa-plus"></i>
                                <span>Add First Student</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </section>

            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script>
        // Search functionality
        function searchStudents() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('studentsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const text = row.textContent || row.innerText;
                if (text.toUpperCase().indexOf(filter) > -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }

        // View student
        function viewStudent(id) {
            window.location.href = 'student-view.php?id=' + id;
        }

        // Edit student
        function editStudent(id) {
            window.location.href = 'student-edit.php?id=' + id;
        }

        // Animate table rows on load
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.holo-table tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                    row.style.opacity = '1';
                    row.style.transform = 'translateX(0)';
                }, index * 50);
            });

            // Animate progress bars
            setTimeout(() => {
                const progressBars = document.querySelectorAll('[style*="width:"][style*="%"]');
                progressBars.forEach(bar => {
                    const width = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = width;
                    }, 100);
                });
            }, 500);
        });
    </script>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body>

</html>
