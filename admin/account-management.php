<?php

/**
 * Verdant SMS - Admin Account Management
 * ADMIN ONLY: Create accounts, approve registrations, manage users
 * Google Form + AI Bulk Registration
 * @version 3.0-evergreen
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// ADMIN ONLY ACCESS
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$success_message = '';
$error_message = '';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // CREATE NEW ACCOUNT
    if ($action === 'create_account') {
        $role = $_POST['role'] ?? '';
        $email = trim($_POST['email'] ?? '');
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');

        // Validate
        if (empty($role) || empty($email) || empty($first_name) || empty($last_name) || empty($password)) {
            $error_message = 'All required fields must be filled';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Invalid email address';
        } elseif ($role === 'admin') {
            $error_message = 'Cannot create another Admin account';
        } else {
            // Check if email exists
            $existing = db()->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
            if ($existing) {
                $error_message = 'Email already exists';
            } else {
                try {
                    $user_id = db()->insert('users', [
                        'email' => $email,
                        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'full_name' => $first_name . ' ' . $last_name,
                        'phone' => $phone,
                        'role' => $role,
                        'status' => 'active',
                        'email_verified' => 1,
                        'approved' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    if ($user_id) {
                        // Send welcome email
                        send_email(
                            $email,
                            'Your Verdant SMS Account',
                            "Hello {$first_name},\n\nYour account has been created.\n\n" .
                                "Email: {$email}\nPassword: {$password}\nRole: " . ucfirst($role) . "\n\n" .
                                "Login at: " . (getenv('APP_URL') ?: 'http://localhost/attendance') . "/login.php"
                        );
                        $success_message = "Account created for {$email} ({$role})";
                    }
                } catch (Exception $e) {
                    $error_message = 'Failed to create account: ' . $e->getMessage();
                }
            }
        }
    }

    // APPROVE REGISTRATION
    elseif ($action === 'approve_user') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        if ($user_id > 0) {
            $result = db()->update('users', [
                'status' => 'active',
                'approved' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$user_id]);

            if ($result) {
                // Get user info and send notification
                $user = db()->fetchOne("SELECT email, first_name FROM users WHERE id = ?", [$user_id]);
                if ($user) {
                    send_email(
                        $user['email'],
                        'Account Approved - Verdant SMS',
                        "Hello {$user['first_name']},\n\nGreat news! Your account has been approved.\n\n" .
                            "You can now log in at: " . (getenv('APP_URL') ?: 'http://localhost/attendance') . "/login.php"
                    );
                }
                $success_message = 'User approved and notified';
            }
        }
    }

    // DECLINE REGISTRATION
    elseif ($action === 'decline_user') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        if ($user_id > 0) {
            $user = db()->fetchOne("SELECT email, first_name, role FROM users WHERE id = ?", [$user_id]);
            if ($user && $user['role'] !== 'admin') {
                db()->delete('students', 'user_id = ?', [$user_id]);
                db()->delete('users', 'id = ?', [$user_id]);
                $success_message = 'User registration declined and removed';
            }
        }
    }

    // DELETE USER
    elseif ($action === 'delete_user') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        if ($user_id > 0 && $user_id != $_SESSION['user_id']) {
            $user = db()->fetchOne("SELECT role FROM users WHERE id = ?", [$user_id]);
            if ($user && $user['role'] !== 'admin') {
                db()->delete('students', 'user_id = ?', [$user_id]);
                db()->delete('teachers', 'user_id = ?', [$user_id]);
                db()->delete('users', 'id = ?', [$user_id]);
                $success_message = 'User deleted successfully';
            } else {
                $error_message = 'Cannot delete Admin account';
            }
        }
    }

    // SAVE BULK REGISTRATION SETTINGS
    elseif ($action === 'save_bulk_settings') {
        $google_form_url = trim($_POST['google_form_url'] ?? '');
        $google_sheet_id = trim($_POST['google_sheet_id'] ?? '');
        $duration_days = (int)($_POST['duration_days'] ?? 5);
        $start_date = $_POST['start_date'] ?? date('Y-m-d');

        // Save to settings or file
        $settings = [
            'google_form_url' => $google_form_url,
            'google_sheet_id' => $google_sheet_id,
            'duration_days' => $duration_days,
            'start_date' => $start_date,
            'end_date' => date('Y-m-d', strtotime($start_date . ' + ' . $duration_days . ' days')),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        file_put_contents(__DIR__ . '/../config/bulk-registration-settings.json', json_encode($settings, JSON_PRETTY_PRINT));
        $success_message = 'Bulk registration settings saved. Form open until ' . $settings['end_date'];
    }
}

// Get pending registrations
$pending_users = db()->fetchAll("SELECT * FROM users WHERE status = 'pending' OR approved = 0 ORDER BY created_at DESC");

// Get all users (except pending)
$all_users = db()->fetchAll("SELECT * FROM users WHERE status = 'active' ORDER BY role, first_name");

// Get bulk registration settings
$bulk_settings = [];
$bulk_settings_file = __DIR__ . '/../config/bulk-registration-settings.json';
if (file_exists($bulk_settings_file)) {
    $bulk_settings = json_decode(file_get_contents($bulk_settings_file), true) ?: [];
}

// Available roles for creation (all except admin)
$available_roles = [
    'principal' => 'Principal',
    'vice-principal' => 'Vice Principal',
    'teacher' => 'Teacher',
    'class-teacher' => 'Class Teacher',
    'subject-coordinator' => 'Subject Coordinator',
    'parent' => 'Parent',
    'student' => 'Student',
    'accountant' => 'Accountant',
    'librarian' => 'Librarian',
    'counselor' => 'Counselor',
    'nurse' => 'Nurse',
    'transport' => 'Transport Manager',
    'hostel' => 'Hostel Warden',
    'canteen' => 'Canteen Manager',
    'admin-officer' => 'Admin Officer',
    'alumni' => 'Alumni',
    'general' => 'General Staff'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Management | Admin | Verdant SMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <style>
        .account-mgmt-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            background: linear-gradient(135deg, var(--cyber-cyan), var(--neon-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 0.75rem 1.5rem;
            background: rgba(0, 0, 0, 0.5);
            border: 2px solid var(--glass-border);
            border-radius: 10px;
            color: var(--text-secondary);
            font-family: 'Rajdhani', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .tab-btn:hover {
            border-color: var(--cyber-cyan);
            color: var(--cyber-cyan);
        }

        .tab-btn.active {
            background: rgba(0, 245, 255, 0.1);
            border-color: var(--cyber-cyan);
            color: var(--cyber-cyan);
            box-shadow: 0 0 20px rgba(0, 245, 255, 0.2);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: rgba(20, 20, 30, 0.8);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--glass-border);
        }

        .card-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.2rem;
            color: var(--neon-green);
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-pending {
            background: rgba(255, 165, 0, 0.2);
            color: #ffa500;
            border: 1px solid #ffa500;
        }

        .badge-count {
            background: rgba(0, 245, 255, 0.2);
            color: var(--cyber-cyan);
            border: 1px solid var(--cyber-cyan);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(0, 0, 0, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'Rajdhani', sans-serif;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--cyber-cyan);
            box-shadow: 0 0 15px rgba(0, 245, 255, 0.2);
        }

        .form-select option {
            background: #1a1a2e;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-family: 'Rajdhani', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--neon-green), var(--cyber-cyan));
            color: #000;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 255, 136, 0.3);
        }

        .btn-success {
            background: rgba(0, 255, 136, 0.2);
            border: 1px solid var(--neon-green);
            color: var(--neon-green);
        }

        .btn-danger {
            background: rgba(255, 71, 87, 0.2);
            border: 1px solid #ff4757;
            color: #ff4757;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }

        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }

        .data-table th {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.75rem;
            color: var(--cyber-cyan);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .data-table tr:hover {
            background: rgba(0, 245, 255, 0.05);
        }

        .role-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .role-admin {
            background: rgba(255, 0, 110, 0.2);
            color: #ff006e;
        }

        .role-principal {
            background: rgba(191, 0, 255, 0.2);
            color: #bf00ff;
        }

        .role-teacher {
            background: rgba(0, 136, 255, 0.2);
            color: #0088ff;
        }

        .role-student {
            background: rgba(0, 255, 136, 0.2);
            color: #00ff88;
        }

        .role-parent {
            background: rgba(255, 149, 0, 0.2);
            color: #ff9500;
        }

        .role-default {
            background: rgba(136, 136, 170, 0.2);
            color: #8888aa;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid var(--neon-green);
            color: var(--neon-green);
        }

        .alert-error {
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid #ff4757;
            color: #ff4757;
        }

        .bulk-info {
            background: rgba(0, 245, 255, 0.1);
            border: 1px solid var(--cyber-cyan);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .bulk-info h4 {
            color: var(--cyber-cyan);
            margin-bottom: 1rem;
            font-family: 'Orbitron', sans-serif;
        }

        .bulk-info ol {
            color: var(--text-secondary);
            padding-left: 1.5rem;
            line-height: 1.8;
        }

        .status-active {
            color: var(--neon-green);
        }

        .status-pending {
            color: #ffa500;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
    </style>
</head>

<body class="cyber-bg">
    <?php include '../includes/cyber-nav.php'; ?>

    <main class="cyber-main">
        <div class="account-mgmt-container">
            <!-- Header -->
            <div class="page-header">
                <h1 class="page-title"><i class="fas fa-users-cog"></i> Account Management</h1>
                <a href="dashboard.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <!-- Alerts -->
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('create')">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
                <button class="tab-btn" onclick="showTab('pending')">
                    <i class="fas fa-clock"></i> Pending Approvals
                    <?php if (count($pending_users) > 0): ?>
                        <span class="badge badge-pending"><?= count($pending_users) ?></span>
                    <?php endif; ?>
                </button>
                <button class="tab-btn" onclick="showTab('users')">
                    <i class="fas fa-users"></i> All Users
                    <span class="badge badge-count"><?= count($all_users) ?></span>
                </button>
                <button class="tab-btn" onclick="showTab('bulk')">
                    <i class="fas fa-robot"></i> AI Bulk Registration
                </button>
            </div>

            <!-- Tab: Create Account -->
            <div id="tab-create" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-plus"></i> Create New Account</h3>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="create_account">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Role *</label>
                                <select name="role" class="form-select" required>
                                    <option value="">Select Role</option>
                                    <?php foreach ($available_roles as $value => $label): ?>
                                        <option value="<?= $value ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-input" placeholder="user@school.edu" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-input" placeholder="John" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-input" placeholder="Doe" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" class="form-input" placeholder="Min 8 characters" required minlength="8">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-input" placeholder="+1 234 567 890">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Account
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tab: Pending Approvals -->
            <div id="tab-pending" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clock"></i> Pending Registrations</h3>
                        <span class="badge badge-pending"><?= count($pending_users) ?> pending</span>
                    </div>

                    <?php if (empty($pending_users)): ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No pending registrations</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_users as $user): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td>
                                                <span class="role-badge role-<?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span>
                                            </td>
                                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="action" value="approve_user">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                    </form>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Decline this registration?');">
                                                        <input type="hidden" name="action" value="decline_user">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Decline">
                                                            <i class="fas fa-times"></i> Decline
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tab: All Users -->
            <div id="tab-users" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-users"></i> All Active Users</h3>
                        <span class="badge badge-count"><?= count($all_users) ?> users</span>
                    </div>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_users as $user): ?>
                                    <tr>
                                        <td>#<?= $user['id'] ?></td>
                                        <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td>
                                            <span class="role-badge role-<?= in_array($user['role'], ['admin', 'principal', 'teacher', 'student', 'parent']) ? $user['role'] : 'default' ?>">
                                                <?= ucfirst(str_replace('-', ' ', $user['role'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-<?= $user['status'] ?>">
                                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                                <?= ucfirst($user['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                        <td>
                                            <?php if ($user['role'] !== 'admin'): ?>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user permanently?');">
                                                    <input type="hidden" name="action" value="delete_user">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge badge-pending">Protected</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab: AI Bulk Registration -->
            <div id="tab-bulk" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-robot"></i> AI Bulk Registration</h3>
                    </div>

                    <div class="bulk-info">
                        <h4><i class="fas fa-info-circle"></i> How This Works</h4>
                        <ol>
                            <li><strong>Create a Google Form</strong> with fields: Name, Email, Phone, Role (Parent/Teacher), Child's Name (for Parents)</li>
                            <li><strong>Link the form to a Google Sheet</strong> (Form → Responses → Spreadsheet icon)</li>
                            <li><strong>Paste the Sheet ID</strong> below and set the form duration</li>
                            <li><strong>Share the form link</strong> with parents/teachers during the open period</li>
                            <li><strong>After the duration ends</strong>, click "Process Submissions" to auto-create accounts</li>
                            <li><strong>AI will validate</strong> data, create accounts for Parents/Teachers, and send welcome emails</li>
                            <li><strong>Principals and Staff</strong> are flagged for manual review (not auto-created)</li>
                        </ol>
                    </div>

                    <form method="POST">
                        <input type="hidden" name="action" value="save_bulk_settings">
                        <div class="form-grid">
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label class="form-label">Google Form URL (for sharing)</label>
                                <input type="url" name="google_form_url" class="form-input"
                                    placeholder="https://docs.google.com/forms/d/e/..."
                                    value="<?= htmlspecialchars($bulk_settings['google_form_url'] ?? '') ?>">
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label class="form-label">Google Sheet ID (from linked spreadsheet URL)</label>
                                <input type="text" name="google_sheet_id" class="form-input"
                                    placeholder="1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms"
                                    value="<?= htmlspecialchars($bulk_settings['google_sheet_id'] ?? '') ?>">
                                <small style="color: var(--text-secondary);">
                                    Find in Sheet URL: docs.google.com/spreadsheets/d/<strong>SHEET_ID</strong>/edit
                                </small>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-input"
                                    value="<?= htmlspecialchars($bulk_settings['start_date'] ?? date('Y-m-d')) ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Duration (Days)</label>
                                <select name="duration_days" class="form-select">
                                    <?php for ($i = 1; $i <= 14; $i++): ?>
                                        <option value="<?= $i ?>" <?= ($bulk_settings['duration_days'] ?? 5) == $i ? 'selected' : '' ?>>
                                            <?= $i ?> day<?= $i > 1 ? 's' : '' ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <?php if (!empty($bulk_settings['end_date'])): ?>
                            <div class="alert alert-success" style="margin-top: 1rem;">
                                <i class="fas fa-calendar-check"></i>
                                Form open until: <strong><?= date('M j, Y', strtotime($bulk_settings['end_date'])) ?></strong>
                            </div>
                        <?php endif; ?>

                        <div style="display: flex; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                            <a href="ai-bulk-process.php" class="btn btn-success">
                                <i class="fas fa-robot"></i> Process Submissions
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById('tab-' + tabName).classList.add('active');
            event.target.closest('.tab-btn').classList.add('active');
        }
    </script>
</body>

</html>