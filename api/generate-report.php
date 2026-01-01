<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

header('Content-Type: application/json');

// Authentication check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Only allow certain roles to generate reports
$allowed_roles = ['admin', 'teacher'];
if (!in_array($user_role, $allowed_roles)) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// Get request data
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? $_GET['action'] ?? null;

try {
    switch ($action) {
        case 'pdf':
            generatePDF($data);
            break;

        case 'excel':
            generateExcel($data);
            break;

        case 'share':
            generateShareLink($data);
            break;

        case 'get_shared':
            getSharedReport($data);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

/**
 * Generate PDF Report
 */
function generatePDF($data)
{
    global $user_id, $user_role;

    $report_type = $data['type'] ?? 'attendance';
    $class_id = $data['class_id'] ?? null;
    $start_date = $data['start_date'] ?? date('Y-m-01');
    $end_date = $data['end_date'] ?? date('Y-m-d');

    // Build query based on role
    $where_conditions = [];
    $params = [];

    if ($user_role === 'teacher') {
        $where_conditions[] = "c.teacher_id = ?";
        $params[] = $user_id;
    }

    if ($class_id) {
        $where_conditions[] = "ar.class_id = ?";
        $params[] = $class_id;
    }

    $where_conditions[] = "DATE(ar.check_in_time) BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;

    $where_clause = implode(' AND ', $where_conditions);

    // Get attendance data
    $attendance_data = db()->fetchAll("
        SELECT
            s.id,
            s.student_id,
            CONCAT(u.first_name, ' ', u.last_name) as student_name,
            c.class_name,
            ar.status,
            ar.check_in_time,
            ar.check_out_time
        FROM attendance_records ar
        JOIN students s ON ar.student_id = s.id
        JOIN users u ON s.user_id = u.id
        JOIN classes c ON ar.class_id = c.id
        WHERE $where_clause
        ORDER BY ar.check_in_time DESC
    ", $params);

    // Get statistics
    $stats = db()->fetchAll("
        SELECT ar.status, COUNT(*) as count
        FROM attendance_records ar
        JOIN classes c ON ar.class_id = c.id
        WHERE $where_clause
        GROUP BY ar.status
    ", $params);

    // Calculate stats
    $total_records = array_sum(array_column($stats, 'count'));
    $present_count = 0;
    $late_count = 0;
    $absent_count = 0;

    foreach ($stats as $stat) {
        if ($stat['status'] === 'present') $present_count = $stat['count'];
        if ($stat['status'] === 'late') $late_count = $stat['count'];
        if ($stat['status'] === 'absent') $absent_count = $stat['count'];
    }

    $attendance_rate = $total_records > 0 ? round((($present_count + $late_count) / $total_records) * 100, 1) : 0;

    // Generate HTML for PDF
    $html = generateReportHTML([
        'title' => 'Attendance Report',
        'period' => date('M d, Y', strtotime($start_date)) . ' to ' . date('M d, Y', strtotime($end_date)),
        'stats' => [
            'total' => $total_records,
            'present' => $present_count,
            'late' => $late_count,
            'absent' => $absent_count,
            'rate' => $attendance_rate
        ],
        'data' => $attendance_data
    ]);

    // For now, return HTML (PDF library integration can be added later)
    echo json_encode([
        'success' => true,
        'html' => $html,
        'message' => 'Report generated successfully. PDF library (TCPDF/DOMPDF) integration recommended for production.'
    ]);
}

/**
 * Generate Excel/CSV Report
 */
function generateExcel($data)
{
    global $user_id, $user_role;

    $class_id = $data['class_id'] ?? null;
    $start_date = $data['start_date'] ?? date('Y-m-01');
    $end_date = $data['end_date'] ?? date('Y-m-d');

    // Build query based on role
    $where_conditions = [];
    $params = [];

    if ($user_role === 'teacher') {
        $where_conditions[] = "c.teacher_id = ?";
        $params[] = $user_id;
    }

    if ($class_id) {
        $where_conditions[] = "ar.class_id = ?";
        $params[] = $class_id;
    }

    $where_conditions[] = "DATE(ar.check_in_time) BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;

    $where_clause = implode(' AND ', $where_conditions);

    // Get attendance data
    $attendance_data = db()->fetchAll("
        SELECT
            s.student_id as 'Student ID',
            CONCAT(u.first_name, ' ', u.last_name) as 'Student Name',
            c.class_name as 'Class',
            ar.status as 'Status',
            DATE_FORMAT(ar.check_in_time, '%Y-%m-%d %H:%i') as 'Check In',
            DATE_FORMAT(ar.check_out_time, '%Y-%m-%d %H:%i') as 'Check Out'
        FROM attendance_records ar
        JOIN students s ON ar.student_id = s.id
        JOIN users u ON s.user_id = u.id
        JOIN classes c ON ar.class_id = c.id
        WHERE $where_clause
        ORDER BY ar.check_in_time DESC
    ", $params);

    // Generate CSV
    $csv_data = [];

    // Add headers
    if (!empty($attendance_data)) {
        $csv_data[] = array_keys($attendance_data[0]);

        // Add data rows
        foreach ($attendance_data as $row) {
            $csv_data[] = array_values($row);
        }
    }

    // Convert to CSV format
    $csv_string = '';
    foreach ($csv_data as $row) {
        $csv_string .= '"' . implode('","', $row) . "\"\n";
    }

    echo json_encode([
        'success' => true,
        'csv' => $csv_string,
        'filename' => 'attendance_report_' . date('Y-m-d') . '.csv',
        'message' => 'Excel/CSV report generated. For advanced Excel features, integrate PhpSpreadsheet library.'
    ]);
}

/**
 * Generate Shareable Link
 */
function generateShareLink($data)
{
    global $user_id;

    $report_type = $data['type'] ?? 'attendance';
    $class_id = $data['class_id'] ?? null;
    $start_date = $data['start_date'] ?? date('Y-m-01');
    $end_date = $data['end_date'] ?? date('Y-m-d');
    $expires_in = $data['expires_in'] ?? 7; // days

    // Generate unique token
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime("+$expires_in days"));

    // Store in database
    db()->query("
        INSERT INTO report_shares (
            user_id,
            report_type,
            class_id,
            start_date,
            end_date,
            token,
            expires_at,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ", [
        $user_id,
        $report_type,
        $class_id,
        $start_date,
        $end_date,
        $token,
        $expires_at
    ]);

    $share_url = APP_URL . "/api/generate-report.php?action=get_shared&token=" . $token;

    echo json_encode([
        'success' => true,
        'share_url' => $share_url,
        'token' => $token,
        'expires_at' => $expires_at,
        'message' => 'Share link generated successfully'
    ]);
}

/**
 * Get Shared Report (Public Access with Token)
 */
function getSharedReport($data)
{
    $token = $data['token'] ?? $_GET['token'] ?? null;

    if (!$token) {
        http_response_code(400);
        echo json_encode(['error' => 'Token required']);
        return;
    }

    // Get report share data
    $share = db()->fetchOne("
        SELECT * FROM report_shares
        WHERE token = ? AND expires_at > NOW() AND deleted_at IS NULL
    ", [$token]);

    if (!$share) {
        http_response_code(404);
        echo json_encode(['error' => 'Report not found or expired']);
        return;
    }

    // Get attendance data
    $where_conditions = [];
    $params = [];

    if ($share['class_id']) {
        $where_conditions[] = "ar.class_id = ?";
        $params[] = $share['class_id'];
    }

    $where_conditions[] = "DATE(ar.check_in_time) BETWEEN ? AND ?";
    $params[] = $share['start_date'];
    $params[] = $share['end_date'];

    $where_clause = implode(' AND ', $where_conditions);

    $attendance_data = db()->fetchAll("
        SELECT
            s.student_id,
            CONCAT(u.first_name, ' ', u.last_name) as student_name,
            c.class_name,
            ar.status,
            ar.check_in_time
        FROM attendance_records ar
        JOIN students s ON ar.student_id = s.id
        JOIN users u ON s.user_id = u.id
        JOIN classes c ON ar.class_id = c.id
        WHERE $where_clause
        ORDER BY ar.check_in_time DESC
    ", $params);

    // Get statistics
    $stats = db()->fetchAll("
        SELECT ar.status, COUNT(*) as count
        FROM attendance_records ar
        WHERE $where_clause
        GROUP BY ar.status
    ", $params);

    $total_records = array_sum(array_column($stats, 'count'));
    $present_count = 0;
    $late_count = 0;
    $absent_count = 0;

    foreach ($stats as $stat) {
        if ($stat['status'] === 'present') $present_count = $stat['count'];
        if ($stat['status'] === 'late') $late_count = $stat['count'];
        if ($stat['status'] === 'absent') $absent_count = $stat['count'];
    }

    $attendance_rate = $total_records > 0 ? round((($present_count + $late_count) / $total_records) * 100, 1) : 0;

    echo json_encode([
        'success' => true,
        'report' => [
            'type' => $share['report_type'],
            'period' => date('M d, Y', strtotime($share['start_date'])) . ' to ' . date('M d, Y', strtotime($share['end_date'])),
            'expires_at' => $share['expires_at'],
            'stats' => [
                'total' => $total_records,
                'present' => $present_count,
                'late' => $late_count,
                'absent' => $absent_count,
                'rate' => $attendance_rate
            ],
            'data' => $attendance_data
        ]
    ]);
}

/**
 * Generate Report HTML
 */
function generateReportHTML($data)
{
    $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($data['title']) . '</title>
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #00BFFF;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .stat-box {
            text-align: center;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #00BFFF;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #00BFFF;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>' . htmlspecialchars($data['title']) . '</h1>
        <p>Period: ' . htmlspecialchars($data['period']) . '</p>
        <p>Generated: ' . date('F d, Y h:i A') . '</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">' . $data['stats']['total'] . '</div>
            <div class="stat-label">Total Records</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">' . $data['stats']['present'] . '</div>
            <div class="stat-label">Present</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">' . $data['stats']['late'] . '</div>
            <div class="stat-label">Late</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">' . $data['stats']['absent'] . '</div>
            <div class="stat-label">Absent</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">' . $data['stats']['rate'] . '%</div>
            <div class="stat-label">Attendance Rate</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Class</th>
                <th>Status</th>
                <th>Check In</th>
                <th>Check Out</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($data['data'] as $row) {
        $html .= '<tr>
            <td>' . htmlspecialchars($row['student_id']) . '</td>
            <td>' . htmlspecialchars($row['student_name']) . '</td>
            <td>' . htmlspecialchars($row['class_name']) . '</td>
            <td>' . ucfirst(htmlspecialchars($row['status'])) . '</td>
            <td>' . date('M d, Y h:i A', strtotime($row['check_in_time'])) . '</td>
            <td>' . ($row['check_out_time'] ? date('M d, Y h:i A', strtotime($row['check_out_time'])) : '-') . '</td>
        </tr>';
    }

    $html .= '</tbody>
    </table>

    <div class="footer">
        <p>© ' . date('Y') . ' ' . APP_NAME . ' - All Rights Reserved</p>
    </div>
</body>
</html>';

    return $html;
}
