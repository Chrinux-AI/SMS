<?php

/**
 * Student Self Check-in Page - Nature Edition
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

$message = '';
$message_type = '';
$student = null;
$classes = [];
$today_attendance = [];

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $student_id = strtoupper(sanitize($_POST['student_id']));

    // Validate student ID format (accepts both STU20250001 and 20250001)
    if (!preg_match('/^(STU)?\d{8}$/', $student_id)) {
        $message = 'Invalid Student ID format! Use format: STU20250001 or 20250001';
        $message_type = 'error';
    } else {
        // Strip STU prefix if present to match database format
        $numeric_id = str_replace('STU', '', $student_id);

        // Query students table joined with users table
        $student = db()->fetchOne("
            SELECT s.*, u.first_name, u.last_name, u.email, u.id as user_id
            FROM students s
            JOIN users u ON s.user_id = u.id
            WHERE s.student_id = ? AND s.status = 'active' AND u.status = 'active'
        ", [$numeric_id]);

        if ($student) {
            $_SESSION['student_data'] = $student;
            $message = 'Welcome, ' . htmlspecialchars($student['first_name']) . '!';
            $message_type = 'success';
        } else {
            $message = 'Student ID not found or account is inactive. Please check your ID or contact administration.';
            $message_type = 'error';
        }
    }
}

// Handle check-in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkin'])) {
    $class_id = (int)$_POST['class_id'];
    $student = $_SESSION['student_data'];

    // Check if already checked in today
    $existing = db()->fetchOne("
        SELECT id FROM attendance_records
        WHERE student_id = ? AND class_id = ? AND DATE(check_in_time) = CURDATE()
    ", [$student['user_id'], $class_id]);

    if (!$existing) {
        db()->insert('attendance_records', [
            'student_id' => $student['user_id'],
            'class_id' => $class_id,
            'check_in_time' => date('Y-m-d H:i:s'),
            'status' => 'present',
            'marked_by' => $student['user_id']
        ]);

        log_activity($student['user_id'], 'self_checkin', 'attendance_records', $class_id, "Self check-in for class ID: $class_id");
        $message = 'Attendance recorded successfully!';
        $message_type = 'success';

        // Refresh attendance data
        $today_attendance = db()->fetchAll("
            SELECT ar.*, c.class_name, c.class_code
            FROM attendance_records ar
            JOIN classes c ON ar.class_id = c.id
            WHERE ar.student_id = ? AND DATE(ar.check_in_time) = CURDATE()
            ORDER BY ar.check_in_time DESC
        ", [$student['user_id']]);
    } else {
        $message = 'You have already checked in for this class today!';
        $message_type = 'warning';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['student_data']);
    $student = null;
    $message = 'Logged out successfully!';
    $message_type = 'success';
}

// Get student data from session
if (isset($_SESSION['student_data'])) {
    $student = $_SESSION['student_data'];

    // Get enrolled classes
    $classes = db()->fetchAll("
        SELECT c.*, COUNT(ar.id) as attendance_count
        FROM classes c
        JOIN class_enrollments ce ON c.id = ce.class_id
        LEFT JOIN attendance_records ar ON c.id = ar.class_id AND ar.student_id = ?
        WHERE ce.student_id = ?
        GROUP BY c.id
        ORDER BY c.class_name
    ", [$student['user_id'], $student['user_id']]);

    // Get today's attendance
    $today_attendance = db()->fetchAll("
        SELECT ar.*, c.class_name, c.class_code
        FROM attendance_records ar
        JOIN classes c ON ar.class_id = c.id
        WHERE ar.student_id = ? AND DATE(ar.check_in_time) = CURDATE()
        ORDER BY ar.check_in_time DESC
    ", [$student['user_id']]);
}
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
    <title>Student Check-in - <?php echo APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/cyberpunk-ui.css" rel="stylesheet">
    
    <style>
        .checkin-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .checkin-box {
            max-width: 500px;
            width: 100%;
        }

        .cyber-clock {
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.1), rgba(138, 43, 226, 0.1));
            border: 2px solid rgba(0, 191, 255, 0.3);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
        }

        .clock-time {
            font-family: 'Orbitron', monospace;
            font-size: 48px;
            font-weight: 700;
            color: #00BFFF;
            margin-bottom: 10px;
            text-shadow: 0 0 20px rgba(0, 191, 255, 0.5);
        }

        .clock-date {
            font-size: 18px;
            color: #E0E0E0;
        }

        .class-grid {
            display: grid;
            gap: 15px;
            margin-top: 20px;
        }

        .class-card-checkin {
            background: rgba(0, 191, 255, 0.05);
            border: 2px solid rgba(0, 191, 255, 0.2);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
        }

        .class-card-checkin:hover {
            border-color: #00BFFF;
            background: rgba(0, 191, 255, 0.1);
            transform: translateY(-2px);
        }

        .class-card-checkin.checked-in {
            border-color: #00FF7F;
            background: rgba(0, 255, 127, 0.05);
        }

        .class-info {
            margin-bottom: 15px;
        }

        .class-name {
            font-size: 18px;
            font-weight: 700;
            color: #00BFFF;
            margin-bottom: 5px;
        }

        .class-code {
            font-size: 14px;
            color: #888;
        }

        .checkin-btn-class {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #00FF7F, #00BFFF);
            border: none;
            border-radius: 8px;
            color: #000;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .checkin-btn-class:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 255, 127, 0.4);
        }

        .checkin-btn-class:disabled {
            background: rgba(255, 255, 255, 0.1);
            color: #666;
            cursor: not-allowed;
        }

        .student-id-input {
            font-family: 'Orbitron', monospace;
            font-size: 24px;
            text-align: center;
            letter-spacing: 2px;
        }
    </style>
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/student-nav.php'; ?>

        <main class="cyber-main">

    <div class="starfield"></div>
    <div class="cyber-grid"></div>
</div>
    

    <div class="checkin-container">
        <div class="checkin-box">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>" style="margin-bottom: 20px;">
                    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : ($message_type === 'warning' ? 'exclamation-triangle' : 'times-circle'); ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if (!$student): ?>
                <!-- Login Form -->
                <div class="holo-card">
                    <div class="card-header" style="text-align: center;">
                        <div style="margin: 0 auto 20px;">
                            <div class="page-icon-orb" style="margin: 0 auto;">
                                <i class="fas fa-fingerprint"></i>
                            </div>
                        </div>
                        <h1 style="font-size: 32px; margin-bottom: 10px;">Student Check-in</h1>
                        <p class="page-subtitle">Enter your Student ID to access the system</p>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-id-badge"></i> Student ID
                                </label>
                                <input type="text" name="student_id" class="cyber-input student-id-input"
                                    placeholder="STU20250001"
                                    maxlength="11"
                                    pattern="STU\d{8}"
                                    required autofocus
                                    style="text-transform: uppercase;">
                                <small style="color: #888; font-size: 13px; display: block; margin-top: 8px;">
                                    <i class="fas fa-info-circle"></i> Format: STU followed by 8 digits (e.g., STU20250001)
                                </small>
                            </div>

                            <button type="submit" name="login" class="cyber-btn" style="width: 100%; padding: 15px; font-size: 18px; margin-bottom: 15px;">
                                <i class="fas fa-sign-in-alt"></i> Check In
                            </button>

                            <a href="dashboard.php" class="cyber-btn btn-secondary" style="width: 100%; padding: 12px; text-align: center; display: block;">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </form>

                        <div style="margin-top: 25px; padding: 15px; background: rgba(0,191,255,0.05); border-left: 4px solid #00BFFF; border-radius: 8px;">
                            <h4 style="margin: 0 0 10px 0; color: #00BFFF;">
                                <i class="fas fa-question-circle"></i> Don't have a Student ID?
                            </h4>
                            <p style="margin: 0; font-size: 14px; color: #888;">
                                Contact administration or <a href="../register.php" style="color: #00FF7F; text-decoration: none; font-weight: 600;">register a new account</a>.
                            </p>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- Student Dashboard -->
                <div class="cyber-clock">
                    <div class="clock-time" id="current-time"><?php echo date('H:i:s'); ?></div>
                    <div class="clock-date"><?php echo date('l, F j, Y'); ?></div>
                </div>

                <div class="holo-card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-user-circle"></i>
                            <span>Welcome, <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></span>
                        </div>
                        <a href="?logout=1" class="cyber-btn btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                    <div class="card-body">
                        <p style="margin: 0; color: #00BFFF;">
                            <strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?>
                        </p>
                    </div>
                </div>

                <!-- Today's Check-ins -->
                <?php if (!empty($today_attendance)): ?>
                    <div class="holo-card" style="margin-top: 20px;">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-check-circle"></i>
                                <span>Today's Check-ins</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php foreach ($today_attendance as $record): ?>
                                <div style="padding: 10px; background: rgba(0,255,127,0.05); border-left: 3px solid #00FF7F; margin-bottom: 10px; border-radius: 5px;">
                                    <strong><?php echo htmlspecialchars($record['class_name']); ?></strong>
                                    <span style="float: right; color: #00FF7F;">
                                        <i class="fas fa-check"></i> <?php echo date('h:i A', strtotime($record['check_in_time'])); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Available Classes -->
                <div class="holo-card" style="margin-top: 20px;">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-door-open"></i>
                            <span>Available Classes</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($classes)): ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <p>No classes enrolled</p>
                            </div>
                        <?php else: ?>
                            <div class="class-grid">
                                <?php foreach ($classes as $class):
                                    $is_checked_in = false;
                                    foreach ($today_attendance as $att) {
                                        if ($att['class_id'] == $class['id']) {
                                            $is_checked_in = true;
                                            break;
                                        }
                                    }
                                ?>
                                    <div class="class-card-checkin <?php echo $is_checked_in ? 'checked-in' : ''; ?>">
                                        <div class="class-info">
                                            <div class="class-name"><?php echo htmlspecialchars($class['class_name']); ?></div>
                                            <div class="class-code">
                                                <?php echo htmlspecialchars($class['class_code']); ?>
                                                <?php if ($class['room_number']): ?>
                                                    • Room <?php echo htmlspecialchars($class['room_number']); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <form method="POST" style="margin: 0;">
                                            <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                                            <button type="submit" name="checkin" class="checkin-btn-class"
                                                <?php echo $is_checked_in ? 'disabled' : ''; ?>>
                                                <?php if ($is_checked_in): ?>
                                                    <i class="fas fa-check-double"></i> Already Checked In
                                                <?php else: ?>
                                                    <i class="fas fa-fingerprint"></i> Check In Now
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="margin-top: 20px; text-align: center;">
                    <a href="dashboard.php" class="cyber-btn btn-secondary">
                        <i class="fas fa-home"></i> Go to Dashboard
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Update clock every second
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour12: false
            });
            const clockElement = document.getElementById('current-time');
            if (clockElement) {
                clockElement.textContent = timeString;
            }
        }
        setInterval(updateClock, 1000);

        // Auto-format student ID input
        const studentIdInput = document.querySelector('input[name="student_id"]');
        if (studentIdInput) {
            studentIdInput.addEventListener('input', function(e) {
                let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                if (value.length > 0 && !value.startsWith('STU')) {
                    value = 'STU' + value;
                }
                e.target.value = value.substring(0, 11);
            });
        }
    </script>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body>

</html>