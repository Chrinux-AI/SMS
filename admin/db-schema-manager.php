<?php

/**
 * Database Migration and Schema Verification Tool
 * Ensures all required tables exist and are up-to-date
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_admin();

$page_title = "Database Schema Manager";
$current_page = "db-schema-manager.php";

// Get list of all schema files
$schema_files = glob('../database/*.sql');
$migration_results = [];
$verification_results = [];

// Handle schema application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_schema'])) {
    $schema_file = $_POST['schema_file'];
    if (file_exists($schema_file)) {
        try {
            $sql = file_get_contents($schema_file);

            // Split into individual statements
            $statements = array_filter(array_map('trim', explode(';', $sql)));

            $success_count = 0;
            $error_count = 0;

            foreach ($statements as $statement) {
                if (empty($statement)) continue;

                try {
                    db()->getConnection()->exec($statement);
                    $success_count++;
                } catch (Exception $e) {
                    $error_count++;
                    $migration_results[] = [
                        'status' => 'error',
                        'message' => $e->getMessage(),
                        'statement' => substr($statement, 0, 100) . '...'
                    ];
                }
            }

            $migration_results[] = [
                'status' => 'success',
                'message' => "Applied " . basename($schema_file) . ": $success_count statements succeeded, $error_count failed"
            ];
        } catch (Exception $e) {
            $migration_results[] = [
                'status' => 'error',
                'message' => "Failed to read schema file: " . $e->getMessage()
            ];
        }
    }
}

// Verify required tables exist
$required_tables = [
    'users',
    'students',
    'teachers',
    'guardians',
    'classes',
    'subjects',
    'attendance',
    'assignments',
    'assignment_submissions',
    'grades',
    'messages',
    'message_recipients',
    'notices',
    'events',
    'fee_invoices',
    'fee_payments',
    'class_enrollments',
    'activity_logs',
    'lti_configurations',
    'lti_sessions'
];

$existing_tables = db()->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

foreach ($required_tables as $table) {
    $exists = in_array($table, $existing_tables);
    $verification_results[] = [
        'table' => $table,
        'exists' => $exists,
        'status' => $exists ? 'success' : 'missing'
    ];
}

// Get table row counts for existing tables
$table_stats = [];
foreach ($existing_tables as $table) {
    try {
        $count = db()->query("SELECT COUNT(*) as count FROM `$table`")->fetch()['count'] ?? 0;
        $table_stats[$table] = $count;
    } catch (Exception $e) {
        $table_stats[$table] = 'Error';
    }
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
                        <i class="fas fa-database"></i> <?php echo htmlspecialchars($page_title); ?>
                    </h1>
                    <p class="page-subtitle">Manage database schema and migrations</p>
                </div>

                <!-- Migration Results -->
                <?php if (!empty($migration_results)): ?>
                    <div class="holo-card mb-4">
                        <h3 class="card-title">Migration Results</h3>
                        <?php foreach ($migration_results as $result): ?>
                            <div class="alert alert-<?php echo $result['status'] === 'success' ? 'success' : 'danger'; ?>">
                                <?php echo htmlspecialchars($result['message']); ?>
                                <?php if (isset($result['statement'])): ?>
                                    <pre class="mt-2"><?php echo htmlspecialchars($result['statement']); ?></pre>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Available Schema Files -->
                <div class="holo-card mb-4">
                    <h3 class="card-title">Available Schema Files</h3>
                    <div class="table-responsive">
                        <table class="cyber-table">
                            <thead>
                                <tr>
                                    <th>Schema File</th>
                                    <th>Size</th>
                                    <th>Last Modified</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schema_files as $file): ?>
                                    <tr>
                                        <td><?php echo basename($file); ?></td>
                                        <td><?php echo number_format(filesize($file) / 1024, 2); ?> KB</td>
                                        <td><?php echo date('Y-m-d H:i:s', filemtime($file)); ?></td>
                                        <td>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to apply this schema?');">
                                                <input type="hidden" name="schema_file" value="<?php echo htmlspecialchars($file); ?>">
                                                <button type="submit" name="apply_schema" class="cyber-btn btn-sm btn-primary">
                                                    <i class="fas fa-play"></i> Apply
                                                </button>
                                            </form>
                                            <a href="#" class="cyber-btn btn-sm btn-secondary" onclick="viewSchema('<?php echo basename($file); ?>')">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Table Verification -->
                <div class="holo-card mb-4">
                    <h3 class="card-title">Required Tables Verification</h3>
                    <div class="table-responsive">
                        <table class="cyber-table">
                            <thead>
                                <tr>
                                    <th>Table Name</th>
                                    <th>Status</th>
                                    <th>Records</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($verification_results as $result): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($result['table']); ?></td>
                                        <td>
                                            <?php if ($result['exists']): ?>
                                                <span class="cyber-badge badge-success">
                                                    <i class="fas fa-check"></i> Exists
                                                </span>
                                            <?php else: ?>
                                                <span class="cyber-badge badge-danger">
                                                    <i class="fas fa-times"></i> Missing
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($result['exists']) {
                                                echo number_format($table_stats[$result['table']] ?? 0);
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- All Tables Overview -->
                <div class="holo-card">
                    <h3 class="card-title">All Database Tables (<?php echo count($existing_tables); ?>)</h3>
                    <div class="row">
                        <?php
                        $chunks = array_chunk($existing_tables, ceil(count($existing_tables) / 3));
                        foreach ($chunks as $chunk):
                        ?>
                            <div class="col-md-4">
                                <ul class="stat-list">
                                    <?php foreach ($chunk as $table): ?>
                                        <li>
                                            <i class="fas fa-table"></i>
                                            <strong><?php echo htmlspecialchars($table); ?></strong>:
                                            <?php echo number_format($table_stats[$table] ?? 0); ?> rows
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include '../includes/sams-bot.php'; ?>
    <script src="../assets/js/main.js"></script>

    <script>
        function viewSchema(filename) {
            window.open('../database/' + filename, '_blank');
            return false;
        }
    </script>
</body>

</html>