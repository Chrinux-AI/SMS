<?php
/**
 * System Health Monitor & Diagnostics
 * Comprehensive system health check and troubleshooting tool
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_admin();

$page_title = "System Health Monitor";
$current_page = "system-health-monitor.php";

// System checks
$health_checks = [];

// 1. Database Connection
try {
    $db = Database::getInstance();
    $health_checks['database'] = [
        'status' => 'healthy',
        'message' => 'Database connection successful',
        'details' => 'Connected to ' . DB_NAME
    ];
} catch (Exception $e) {
    $health_checks['database'] = [
        'status' => 'critical',
        'message' => 'Database connection failed',
        'details' => $e->getMessage()
    ];
}

// 2. PHP Version
$php_version = phpversion();
$health_checks['php_version'] = [
    'status' => version_compare($php_version, '8.0.0', '>=') ? 'healthy' : 'warning',
    'message' => 'PHP ' . $php_version,
    'details' => version_compare($php_version, '8.0.0', '>=') ? 'PHP version is compatible' : 'Consider upgrading to PHP 8.0+'
];

// 3. Required PHP Extensions
$required_extensions = ['pdo', 'pdo_mysql', 'mysqli', 'json', 'mbstring', 'openssl', 'curl'];
$missing_extensions = [];
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

$health_checks['php_extensions'] = [
    'status' => empty($missing_extensions) ? 'healthy' : 'critical',
    'message' => 'PHP Extensions',
    'details' => empty($missing_extensions) ? 'All required extensions loaded' : 'Missing: ' . implode(', ', $missing_extensions)
];

// 4. File Permissions
$writable_dirs = ['uploads', 'cache', 'logs'];
$permission_issues = [];
foreach ($writable_dirs as $dir) {
    $path = BASE_PATH . '/' . $dir;
    if (!is_dir($path)) {
        @mkdir($path, 0755, true);
    }
    if (!is_writable($path)) {
        $permission_issues[] = $dir;
    }
}

$health_checks['file_permissions'] = [
    'status' => empty($permission_issues) ? 'healthy' : 'warning',
    'message' => 'Directory Permissions',
    'details' => empty($permission_issues) ? 'All directories writable' : 'Not writable: ' . implode(', ', $permission_issues)
];

// 5. Disk Space
$total_space = disk_total_space('.');
$free_space = disk_free_space('.');
$used_percentage = (($total_space - $free_space) / $total_space) * 100;

$health_checks['disk_space'] = [
    'status' => $used_percentage < 90 ? 'healthy' : 'warning',
    'message' => 'Disk Space',
    'details' => number_format($free_space / 1024 / 1024 / 1024, 2) . ' GB free (' . number_format(100 - $used_percentage, 1) . '% available)'
];

// 6. Memory Limit
$memory_limit = ini_get('memory_limit');
$memory_bytes = return_bytes($memory_limit);

$health_checks['memory_limit'] = [
    'status' => $memory_bytes >= 128 * 1024 * 1024 ? 'healthy' : 'warning',
    'message' => 'PHP Memory Limit',
    'details' => $memory_limit . ($memory_bytes < 128 * 1024 * 1024 ? ' (Recommended: 128M+)' : '')
];

// 7. Upload Max File Size
$upload_max = ini_get('upload_max_filesize');
$health_checks['upload_limit'] = [
    'status' => 'healthy',
    'message' => 'Upload Max Filesize',
    'details' => $upload_max
];

// 8. Session Configuration
$session_save_path = session_save_path();
$health_checks['sessions'] = [
    'status' => !empty($session_save_path) && is_writable($session_save_path) ? 'healthy' : 'warning',
    'message' => 'Session Storage',
    'details' => $session_save_path
];

// 9. Email Configuration
$smtp_configured = getenv('SMTP_USERNAME') && getenv('SMTP_USERNAME') !== 'your-email@gmail.com';
$health_checks['email'] = [
    'status' => $smtp_configured ? 'healthy' : 'warning',
    'message' => 'Email Configuration',
    'details' => $smtp_configured ? 'SMTP configured' : 'SMTP not configured (using default settings)'
];

// 10. Security Checks
$security_issues = [];
if (ini_get('display_errors') == '1') {
    $security_issues[] = 'display_errors is enabled';
}
if (!ini_get('session.cookie_httponly')) {
    $security_issues[] = 'session.cookie_httponly not set';
}
if (!ini_get('session.cookie_secure') && isset($_SERVER['HTTPS'])) {
    $security_issues[] = 'session.cookie_secure not set';
}

$health_checks['security'] = [
    'status' => empty($security_issues) ? 'healthy' : 'warning',
    'message' => 'Security Settings',
    'details' => empty($security_issues) ? 'Security settings configured correctly' : implode(', ', $security_issues)
];

// 11. Database Table Count
try {
    $table_count = count(db()->query("SHOW TABLES")->fetchAll());
    $health_checks['database_tables'] = [
        'status' => $table_count >= 20 ? 'healthy' : 'warning',
        'message' => 'Database Tables',
        'details' => $table_count . ' tables found' . ($table_count < 20 ? ' (May need schema migration)' : '')
    ];
} catch (Exception $e) {
    $health_checks['database_tables'] = [
        'status' => 'critical',
        'message' => 'Database Tables',
        'details' => 'Error: ' . $e->getMessage()
    ];
}

// 12. LTI Configuration
$lti_configured = file_exists(BASE_PATH . '/includes/lti.php');
$health_checks['lti'] = [
    'status' => $lti_configured ? 'healthy' : 'info',
    'message' => 'LTI Integration',
    'details' => $lti_configured ? 'LTI files present' : 'LTI not configured'
];

// 13. PWA Configuration
$pwa_configured = file_exists(BASE_PATH . '/manifest.json') && file_exists(BASE_PATH . '/sw.js');
$health_checks['pwa'] = [
    'status' => $pwa_configured ? 'healthy' : 'info',
    'message' => 'PWA Configuration',
    'details' => $pwa_configured ? 'PWA files present' : 'PWA not configured'
];

// Overall System Health Score
$total_checks = count($health_checks);
$healthy_count = count(array_filter($health_checks, fn($check) => $check['status'] === 'healthy'));
$health_score = ($healthy_count / $total_checks) * 100;

// Helper function
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo APP_NAME; ?></title>
    <link rel="manifest" href="/attendance/manifest.json">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>

        <main class="cyber-main">
            <div class="cyber-container">
                <div class="page-header">
                    <h1 class="glitch-text" data-text="<?php echo htmlspecialchars($page_title); ?>">
                        <i class="fas fa-heartbeat"></i> <?php echo htmlspecialchars($page_title); ?>
                    </h1>
                    <p class="page-subtitle">Real-time system diagnostics and health checks</p>
                </div>

                <!-- Overall Health Score -->
                <div class="holo-card mb-4 text-center">
                    <h2 class="mb-3">Overall System Health</h2>
                    <div class="health-score-circle" style="--score: <?php echo $health_score; ?>">
                        <div class="score-value"><?php echo round($health_score); ?>%</div>
                        <div class="score-label">
                            <?php if ($health_score >= 90): ?>
                                <span class="text-success">Excellent</span>
                            <?php elseif ($health_score >= 75): ?>
                                <span class="text-info">Good</span>
                            <?php elseif ($health_score >= 60): ?>
                                <span class="text-warning">Fair</span>
                            <?php else: ?>
                                <span class="text-danger">Needs Attention</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="mt-3">
                        <?php echo $healthy_count; ?> of <?php echo $total_checks; ?> checks passed
                    </p>
                </div>

                <!-- Health Checks Grid -->
                <div class="row">
                    <?php foreach ($health_checks as $key => $check): ?>
                    <div class="col-md-6 mb-3">
                        <div class="holo-card health-check-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h4 class="mb-2">
                                        <?php
                                        $icon = '';
                                        switch ($check['status']) {
                                            case 'healthy':
                                                $icon = '<i class="fas fa-check-circle text-success"></i>';
                                                break;
                                            case 'warning':
                                                $icon = '<i class="fas fa-exclamation-triangle text-warning"></i>';
                                                break;
                                            case 'critical':
                                                $icon = '<i class="fas fa-times-circle text-danger"></i>';
                                                break;
                                            case 'info':
                                                $icon = '<i class="fas fa-info-circle text-info"></i>';
                                                break;
                                        }
                                        echo $icon;
                                        ?>
                                        <?php echo htmlspecialchars($check['message']); ?>
                                    </h4>
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($check['details']); ?></p>
                                </div>
                                <span class="cyber-badge badge-<?php 
                                    echo $check['status'] === 'healthy' ? 'success' : 
                                         ($check['status'] === 'warning' ? 'warning' : 
                                          ($check['status'] === 'critical' ? 'danger' : 'info')); 
                                ?>">
                                    <?php echo strtoupper($check['status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Server Information -->
                <div class="holo-card">
                    <h3 class="card-title">Server Information</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="stat-list">
                                <li><i class="fas fa-server"></i> <strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></li>
                                <li><i class="fas fa-code"></i> <strong>PHP Version:</strong> <?php echo phpversion(); ?></li>
                                <li><i class="fas fa-database"></i> <strong>Database:</strong> MySQL</li>
                                <li><i class="fas fa-globe"></i> <strong>Server IP:</strong> <?php echo $_SERVER['SERVER_ADDR'] ?? 'Unknown'; ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="stat-list">
                                <li><i class="fas fa-clock"></i> <strong>Server Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></li>
                                <li><i class="fas fa-map-marker-alt"></i> <strong>Timezone:</strong> <?php echo date_default_timezone_get(); ?></li>
                                <li><i class="fas fa-folder"></i> <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></li>
                                <li><i class="fas fa-memory"></i> <strong>Memory Usage:</strong> <?php echo round(memory_get_usage() / 1024 / 1024, 2); ?> MB</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include '../includes/sams-bot.php'; ?>
    <script src="../assets/js/main.js"></script>
    
    <style>
    .health-score-circle {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: conic-gradient(
            #00d9ff 0% calc(var(--score) * 1%),
            rgba(0, 217, 255, 0.1) calc(var(--score) * 1%) 100%
        );
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        position: relative;
    }
    .health-score-circle::before {
        content: '';
        position: absolute;
        width: 170px;
        height: 170px;
        border-radius: 50%;
        background: #0a0e27;
    }
    .score-value {
        font-size: 3rem;
        font-weight: bold;
        color: #00d9ff;
        z-index: 1;
    }
    .score-label {
        font-size: 1rem;
        z-index: 1;
    }
    .health-check-card {
        height: 100%;
        transition: transform 0.3s ease;
    }
    .health-check-card:hover {
        transform: translateY(-5px);
    }
    </style>
</body>
</html>
