<?php

/**
 * Bulk Registration Management
 * Admin-only page for AI-powered bulk user registration via Google Forms/CSV
 * Verdant SMS v3.0
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create_config':
            $name = trim($_POST['name'] ?? '');
            $google_sheet_url = trim($_POST['google_sheet_url'] ?? '');
            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? '';
            $target_roles = $_POST['target_roles'] ?? [];

            if (empty($name) || empty($start_date) || empty($end_date) || empty($target_roles)) {
                $error_message = 'Please fill in all required fields.';
            } else {
                try {
                    db()->insert('bulk_registration_config', [
                        'name' => $name,
                        'google_sheet_url' => $google_sheet_url ?: null,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'target_roles' => json_encode($target_roles),
                        'created_by' => $_SESSION['user_id']
                    ]);
                    $success_message = 'Bulk registration window created successfully!';
                } catch (Exception $e) {
                    $error_message = 'Error creating config: ' . $e->getMessage();
                }
            }
            break;

        case 'upload_csv':
            $config_id = intval($_POST['config_id'] ?? 0);

            if (!empty($_FILES['csv_file']['tmp_name'])) {
                $file = $_FILES['csv_file']['tmp_name'];
                $handle = fopen($file, 'r');

                if ($handle) {
                    $header = fgetcsv($handle); // Skip header row
                    $records_added = 0;

                    while (($row = fgetcsv($handle)) !== false) {
                        if (count($row) >= 3) {
                            try {
                                db()->insert('bulk_registration_records', [
                                    'config_id' => $config_id,
                                    'full_name' => $row[0] ?? '',
                                    'email' => $row[1] ?? '',
                                    'phone' => $row[2] ?? '',
                                    'role' => $row[3] ?? 'teacher',
                                    'department' => $row[4] ?? null,
                                    'qualifications' => $row[5] ?? null,
                                    'child1_name' => $row[6] ?? null,
                                    'child1_class' => $row[7] ?? null,
                                    'child2_name' => $row[8] ?? null,
                                    'child2_class' => $row[9] ?? null,
                                    'relationship' => $row[10] ?? null
                                ]);
                                $records_added++;
                            } catch (Exception $e) {
                                // Skip duplicates
                            }
                        }
                    }
                    fclose($handle);

                    // Update total records count
                    db()->query("UPDATE bulk_registration_config SET total_records = total_records + ? WHERE id = ?", [$records_added, $config_id]);

                    $success_message = "Successfully imported $records_added records!";
                }
            } else {
                $error_message = 'Please select a CSV file to upload.';
            }
            break;

        case 'process_now':
            $config_id = intval($_POST['config_id'] ?? 0);
            $result = process_bulk_registrations($config_id);
            if ($result['success']) {
                $success_message = $result['message'];
            } else {
                $error_message = $result['message'];
            }
            break;

        case 'delete_config':
            $config_id = intval($_POST['config_id'] ?? 0);
            db()->query("DELETE FROM bulk_registration_config WHERE id = ?", [$config_id]);
            $success_message = 'Registration window deleted.';
            break;
    }
}

/**
 * Process bulk registrations for a config
 */
function process_bulk_registrations($config_id)
{
    $config = db()->fetch("SELECT * FROM bulk_registration_config WHERE id = ?", [$config_id]);

    if (!$config) {
        return ['success' => false, 'message' => 'Configuration not found.'];
    }

    // Update status to processing
    db()->query("UPDATE bulk_registration_config SET status = 'processing' WHERE id = ?", [$config_id]);

    // Get pending records
    $records = db()->fetchAll("SELECT * FROM bulk_registration_records WHERE config_id = ? AND status = 'pending'", [$config_id]);

    $processed = 0;
    $failed = 0;

    foreach ($records as $record) {
        // Check for duplicate email
        $existing = db()->fetch("SELECT id FROM users WHERE email = ?", [$record['email']]);

        if ($existing) {
            db()->query("UPDATE bulk_registration_records SET status = 'duplicate', error_message = 'Email already exists', processed_at = NOW() WHERE id = ?", [$record['id']]);
            $failed++;
            continue;
        }

        // Generate secure password
        $password = generate_secure_password();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Create user
        try {
            $user_id = db()->insert('users', [
                'name' => $record['full_name'],
                'email' => $record['email'],
                'phone' => $record['phone'],
                'password' => $hashed_password,
                'role' => $record['role'],
                'status' => 'active',
                'email_verified_at' => null // Will need to verify
            ]);

            // Send welcome email with credentials
            $email_sent = send_welcome_email($record['email'], $record['full_name'], $password, $record['role']);

            // Update record status
            db()->query("UPDATE bulk_registration_records SET status = 'processed', user_id = ?, processed_at = NOW() WHERE id = ?", [$user_id, $record['id']]);

            // If parent, link children
            if ($record['role'] === 'parent' && !empty($record['child1_name'])) {
                link_parent_children($user_id, $record);
            }

            $processed++;
        } catch (Exception $e) {
            db()->query("UPDATE bulk_registration_records SET status = 'failed', error_message = ?, processed_at = NOW() WHERE id = ?", [$e->getMessage(), $record['id']]);
            $failed++;
        }
    }

    // Update config status
    db()->query("UPDATE bulk_registration_config SET status = 'completed', processed_records = ?, failed_records = ?, processed_at = NOW() WHERE id = ?", [$processed, $failed, $config_id]);

    return [
        'success' => true,
        'message' => "Processing complete! $processed accounts created, $failed failed."
    ];
}

/**
 * Generate a secure random password
 */
function generate_secure_password($length = 12)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

/**
 * Send welcome email with credentials
 */
function send_welcome_email($email, $name, $password, $role)
{
    $subject = "Welcome to Verdant SMS - Your Account Details";
    $message = "
    <h2>Welcome to Verdant School Management System!</h2>
    <p>Dear $name,</p>
    <p>Your account has been created with the following details:</p>
    <table style='border-collapse: collapse;'>
        <tr><td><strong>Email:</strong></td><td>$email</td></tr>
        <tr><td><strong>Password:</strong></td><td>$password</td></tr>
        <tr><td><strong>Role:</strong></td><td>" . ucfirst($role) . "</td></tr>
    </table>
    <p><strong>Important:</strong> Please change your password after your first login.</p>
    <p>Login at: " . APP_URL . "/login.php</p>
    <p>Best regards,<br>Verdant SMS Team</p>
    ";

    return send_email($email, $subject, $message);
}

/**
 * Link parent to children
 */
function link_parent_children($parent_id, $record)
{
    // Implementation depends on your parent-student linking table
    // This is a placeholder
    if (!empty($record['child1_name'])) {
        // Find or create child link
    }
}

// Get all configs
$configs = db()->fetchAll("SELECT * FROM bulk_registration_config ORDER BY created_at DESC");

// Get available roles for bulk registration
$bulk_roles = [
    'teacher' => 'Teacher',
    'parent' => 'Parent',
    'librarian' => 'Librarian',
    'transport' => 'Transport Officer',
    'hostel' => 'Hostel Warden',
    'canteen' => 'Canteen Manager',
    'nurse' => 'School Nurse',
    'counselor' => 'Counselor',
    'accountant' => 'Accountant',
    'admin-officer' => 'Admin Officer'
];

$page_title = 'Bulk Registration';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <style>
        .bulk-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            color: var(--cyber-cyan);
            font-size: 2rem;
        }

        .card {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid var(--cyber-cyan);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0, 255, 255, 0.2);
        }

        .card-header h2 {
            color: var(--cyber-cyan);
            font-size: 1.3rem;
            margin: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            color: var(--cyber-cyan);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid var(--cyber-cyan);
            border-radius: 5px;
            color: #fff;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            box-shadow: 0 0 10px var(--cyber-cyan);
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-item input[type="checkbox"] {
            width: auto;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--cyber-cyan);
            color: #000;
        }

        .btn-primary:hover {
            box-shadow: 0 0 20px var(--cyber-cyan);
        }

        .btn-danger {
            background: var(--cyber-pink);
            color: #fff;
        }

        .btn-success {
            background: #00ff00;
            color: #000;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: rgba(0, 255, 0, 0.2);
            border: 1px solid #00ff00;
            color: #00ff00;
        }

        .alert-error {
            background: rgba(255, 0, 100, 0.2);
            border: 1px solid var(--cyber-pink);
            color: var(--cyber-pink);
        }

        .config-table {
            width: 100%;
            border-collapse: collapse;
        }

        .config-table th,
        .config-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(0, 255, 255, 0.2);
        }

        .config-table th {
            color: var(--cyber-cyan);
            font-weight: 600;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.3);
            color: #ffc107;
        }

        .status-processing {
            background: rgba(0, 255, 255, 0.3);
            color: var(--cyber-cyan);
        }

        .status-completed {
            background: rgba(0, 255, 0, 0.3);
            color: #00ff00;
        }

        .status-failed {
            background: rgba(255, 0, 100, 0.3);
            color: var(--cyber-pink);
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .csv-template {
            background: rgba(0, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
        }

        .csv-template h4 {
            color: var(--cyber-cyan);
            margin-bottom: 0.5rem;
        }

        .csv-template code {
            display: block;
            background: rgba(0, 0, 0, 0.5);
            padding: 0.5rem;
            border-radius: 3px;
            font-size: 0.85rem;
            overflow-x: auto;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: #0a0a0a;
            border: 1px solid var(--cyber-cyan);
            border-radius: 10px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--cyber-pink);
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>

<body class="cyber-bg">
    <?php include '../includes/cyber-nav.php'; ?>

    <main class="cyber-main">
        <div class="bulk-container">
            <div class="page-header">
                <h1><i class="fas fa-users-cog"></i> AI Bulk Registration</h1>
                <button class="btn btn-primary" onclick="document.getElementById('createModal').classList.add('active')">
                    <i class="fas fa-plus"></i> New Registration Window
                </button>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Registration Windows -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-list"></i> Registration Windows</h2>
                </div>

                <?php if (empty($configs)): ?>
                    <p style="color: var(--cyber-cyan); text-align: center; padding: 2rem;">
                        No registration windows created yet. Click "New Registration Window" to start.
                    </p>
                <?php else: ?>
                    <table class="config-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Target Roles</th>
                                <th>Period</th>
                                <th>Records</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($configs as $config): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($config['name']); ?></td>
                                    <td>
                                        <?php
                                        $roles = json_decode($config['target_roles'], true);
                                        echo implode(', ', array_map('ucfirst', $roles));
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($config['start_date'])); ?> -
                                        <?php echo date('d/m/Y', strtotime($config['end_date'])); ?>
                                    </td>
                                    <td>
                                        <?php echo $config['processed_records']; ?>/<?php echo $config['total_records']; ?>
                                        <?php if ($config['failed_records'] > 0): ?>
                                            <span style="color: var(--cyber-pink);">(<?php echo $config['failed_records']; ?> failed)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $config['status']; ?>">
                                            <?php echo ucfirst($config['status']); ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <?php if ($config['status'] === 'pending'): ?>
                                            <button class="btn btn-primary btn-sm" onclick="openUploadModal(<?php echo $config['id']; ?>)">
                                                <i class="fas fa-upload"></i> Upload CSV
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="process_now">
                                                <input type="hidden" name="config_id" value="<?php echo $config['id']; ?>">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-play"></i> Process
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this registration window?');">
                                            <input type="hidden" name="action" value="delete_config">
                                            <input type="hidden" name="config_id" value="<?php echo $config['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- CSV Template Info -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-file-csv"></i> CSV Template</h2>
                </div>
                <div class="csv-template">
                    <h4>Staff CSV Format:</h4>
                    <code>Full Name,Email,Phone,Role,Department,Qualifications</code>
                    <p style="margin-top: 0.5rem; font-size: 0.9rem; color: #888;">
                        Example: John Doe,john@email.com,+234801234567,teacher,Mathematics,B.Ed Mathematics
                    </p>
                </div>
                <div class="csv-template" style="margin-top: 1rem;">
                    <h4>Parent CSV Format:</h4>
                    <code>Full Name,Email,Phone,Role,Department,Qualifications,Child1 Name,Child1 Class,Child2 Name,Child2 Class,Relationship</code>
                    <p style="margin-top: 0.5rem; font-size: 0.9rem; color: #888;">
                        Example: Jane Doe,jane@email.com,+234801234567,parent,,,John Junior,JSS 1,Mary Junior,P5,Mother
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Create Modal -->
    <div class="modal" id="createModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="color: var(--cyber-cyan);">Create Registration Window</h2>
                <button class="modal-close" onclick="document.getElementById('createModal').classList.remove('active')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create_config">

                <div class="form-group">
                    <label>Window Name *</label>
                    <input type="text" name="name" placeholder="e.g., Parent Registration Dec 2025" required>
                </div>

                <div class="form-group">
                    <label>Google Sheet URL (optional)</label>
                    <input type="url" name="google_sheet_url" placeholder="https://docs.google.com/spreadsheets/...">
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Start Date *</label>
                        <input type="datetime-local" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>End Date *</label>
                        <input type="datetime-local" name="end_date" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Target Roles *</label>
                    <div class="checkbox-group">
                        <?php foreach ($bulk_roles as $role_key => $role_name): ?>
                            <div class="checkbox-item">
                                <input type="checkbox" name="target_roles[]" value="<?php echo $role_key; ?>" id="role_<?php echo $role_key; ?>">
                                <label for="role_<?php echo $role_key; ?>"><?php echo $role_name; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    <i class="fas fa-plus"></i> Create Window
                </button>
            </form>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal" id="uploadModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="color: var(--cyber-cyan);">Upload CSV File</h2>
                <button class="modal-close" onclick="document.getElementById('uploadModal').classList.remove('active')">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_csv">
                <input type="hidden" name="config_id" id="upload_config_id">

                <div class="form-group">
                    <label>Select CSV File *</label>
                    <input type="file" name="csv_file" accept=".csv" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    <i class="fas fa-upload"></i> Upload & Import
                </button>
            </form>
        </div>
    </div>

    <script>
        function openUploadModal(configId) {
            document.getElementById('upload_config_id').value = configId;
            document.getElementById('uploadModal').classList.add('active');
        }

        // Close modal on outside click
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
</body>

</html>