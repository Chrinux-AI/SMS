<?php

/**
 * Timetable Management - Admin Panel
 * Create and manage class timetables
 */
session_start();
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

require_role('admin');

$page_title = "Timetable Management";
$current_page = "academics/timetable.php";

// Fetch classes
$classes = db()->fetchAll("SELECT * FROM classes ORDER BY class_name");
$selected_class = $_GET['class'] ?? 0;

// Fetch timetable for selected class
$timetable = [];
if ($selected_class) {
    $timetable = db()->fetchAll("
        SELECT t.*, s.subject_name, CONCAT(u.first_name, ' ', u.last_name) as teacher_name
        FROM timetable t
        JOIN subjects s ON t.subject_id = s.id
        LEFT JOIN teachers te ON t.teacher_id = te.id
        LEFT JOIN users u ON te.user_id = u.id
        WHERE t.class_id = ?
        ORDER BY t.day_of_week, t.period_number
    ", [$selected_class]);
}

include '../../includes/cyber-nav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SMS</title>
    <link rel="stylesheet" href="../../assets/css/cyber-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .timetable-grid {
            display: grid;
            grid-template-columns: 100px repeat(8, 1fr);
            gap: 5px;
            margin-top: 20px;
        }

        .timetable-cell {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            padding: 10px;
            text-align: center;
            border-radius: 8px;
            min-height: 80px;
        }

        .timetable-header {
            background: var(--neon-cyan);
            color: var(--bg-primary);
            font-weight: bold;
        }

        .timetable-day {
            background: var(--glass-bg-hover);
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .period-info {
            font-size: 0.85em;
        }

        .period-subject {
            font-weight: bold;
            color: var(--neon-cyan);
        }

        .period-teacher {
            font-size: 0.75em;
            color: var(--text-secondary);
        }
    </style>
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="cyber-bg cyber-bg">
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-calendar-week"></i> <?php echo $page_title; ?></h1>
            <div class="breadcrumbs">
                <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Academics</span>
                <span>/</span>
                <span>Timetable</span>
            </div>
        </div>

        <div class="cyber-card">
            <div class="card-header">
                <h3><i class="fas fa-filter"></i> Select Class</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <select name="class" onchange="this.form.submit()">
                                <option value="">Select Class</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo $class['id']; ?>" <?php echo $selected_class == $class['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($class['class_name'] . ' - ' . ($class['section'] ?? 'All')); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($selected_class): ?>
            <div class="page-actions">
                <button class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Period
                </button>
                <button class="btn btn-success">
                    <i class="fas fa-print"></i> Print Timetable
                </button>
                <button class="btn btn-info">
                    <i class="fas fa-copy"></i> Copy from Another Class
                </button>
            </div>

            <div class="cyber-card">
                <div class="card-header">
                    <h3><i class="fas fa-table"></i> Weekly Timetable</h3>
                </div>
                <div class="card-body">
                    <div class="timetable-grid">
                        <!-- Header Row -->
                        <div class="timetable-cell timetable-header">Day/Period</div>
                        <?php for ($period = 1; $period <= 8; $period++): ?>
                            <div class="timetable-cell timetable-header">Period <?php echo $period; ?></div>
                        <?php endfor; ?>

                        <!-- Days -->
                        <?php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                        foreach ($days as $day_index => $day):
                        ?>
                            <div class="timetable-cell timetable-day"><?php echo $day; ?></div>
                            <?php for ($period = 1; $period <= 8; $period++):
                                $cell_data = array_filter(
                                    $timetable,
                                    fn($t) =>
                                    $t['day_of_week'] == $day_index && $t['period_number'] == $period
                                );
                                $cell_data = reset($cell_data);
                            ?>
                                <div class="timetable-cell">
                                    <?php if ($cell_data): ?>
                                        <div class="period-info">
                                            <div class="period-subject"><?php echo htmlspecialchars($cell_data['subject_name']); ?></div>
                                            <div class="period-teacher"><?php echo htmlspecialchars($cell_data['teacher_name'] ?? 'No Teacher'); ?></div>
                                            <div class="period-teacher"><?php echo $cell_data['room_number'] ?? ''; ?></div>
                                        </div>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-primary" style="font-size: 0.7em;">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endfor; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
