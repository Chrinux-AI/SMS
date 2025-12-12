<?php

/**
 * Verdant SMS - AI Bulk Registration Processor
 * Processes Google Form submissions after duration expires
 * Auto-creates Parent/Teacher accounts, flags sensitive roles
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

$results = [
    'processed' => 0,
    'created' => 0,
    'flagged' => 0,
    'skipped' => 0,
    'errors' => [],
    'created_accounts' => [],
    'flagged_accounts' => []
];

$settings_file = __DIR__ . '/../config/bulk-registration-settings.json';
$settings = [];
$can_process = false;
$message = '';

// Load settings
if (file_exists($settings_file)) {
    $settings = json_decode(file_get_contents($settings_file), true) ?: [];
}

// Check if processing is allowed
if (!empty($settings['end_date'])) {
    $end_date = strtotime($settings['end_date']);
    $today = strtotime(date('Y-m-d'));

    if ($today >= $end_date) {
        $can_process = true;
    } else {
        $days_remaining = ceil(($end_date - $today) / 86400);
        $message = "Form is still open. {$days_remaining} day(s) remaining until " . date('M j, Y', $end_date);
    }
}

// Process submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process']) && $can_process) {
    // In production, this would use Google Sheets API
    // For demo, we'll simulate processing from a CSV file or manual input

    $manual_data = $_POST['manual_data'] ?? '';

    if (!empty($manual_data)) {
        $lines = explode("\n", trim($manual_data));

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Parse CSV: Name, Email, Phone, Role, Child's Name (optional)
            $parts = str_getcsv($line);
            if (count($parts) < 4) {
                $results['errors'][] = "Invalid line: {$line}";
                $results['skipped']++;
                continue;
            }

            $results['processed']++;

            $name = trim($parts[0]);
            $email = trim($parts[1]);
            $phone = trim($parts[2]);
            $role = strtolower(trim($parts[3]));
            $child_name = trim($parts[4] ?? '');

            // Split name
            $name_parts = explode(' ', $name, 2);
            $first_name = $name_parts[0];
            $last_name = $name_parts[1] ?? '';

            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $results['errors'][] = "Invalid email: {$email}";
                $results['skipped']++;
                continue;
            }

            // Check if exists
            $existing = db()->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
            if ($existing) {
                $results['errors'][] = "Duplicate email: {$email}";
                $results['skipped']++;
                continue;
            }

            // Determine if auto-create or flag
            $auto_create_roles = ['parent', 'teacher'];
            $flag_roles = ['principal', 'vice-principal', 'accountant', 'admin-officer', 'librarian'];

            if (in_array($role, $auto_create_roles)) {
                // Auto-create account
                $password = generate_random_password();

                try {
                    $user_id = db()->insert('users', [
                        'email' => $email,
                        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'full_name' => $name,
                        'phone' => $phone,
                        'role' => $role,
                        'status' => 'active',
                        'email_verified' => 1,
                        'approved' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    if ($user_id) {
                        // If parent, try to link to student
                        if ($role === 'parent' && !empty($child_name)) {
                            // Find student by name
                            $student = db()->fetchOne(
                                "SELECT u.id FROM users u WHERE CONCAT(u.first_name, ' ', u.last_name) LIKE ? AND u.role = 'student'",
                                ['%' . $child_name . '%']
                            );
                            if ($student) {
                                db()->insert('parent_student', [
                                    'parent_id' => $user_id,
                                    'student_id' => $student['id']
                                ]);
                            }
                        }

                        // Send welcome email
                        send_email(
                            $email,
                            'Welcome to Verdant SMS',
                            "Hello {$first_name},\n\n" .
                                "Your account has been created via bulk registration.\n\n" .
                                "Email: {$email}\n" .
                                "Password: {$password}\n" .
                                "Role: " . ucfirst($role) . "\n\n" .
                                "Login at: " . (getenv('APP_URL') ?: 'http://localhost/attendance') . "/login.php\n\n" .
                                "Please change your password after first login."
                        );

                        $results['created']++;
                        $results['created_accounts'][] = [
                            'name' => $name,
                            'email' => $email,
                            'role' => $role,
                            'password' => $password
                        ];
                    }
                } catch (Exception $e) {
                    $results['errors'][] = "Failed to create {$email}: " . $e->getMessage();
                }
            } elseif (in_array($role, $flag_roles)) {
                // Flag for manual review
                $results['flagged']++;
                $results['flagged_accounts'][] = [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'role' => $role,
                    'reason' => 'Sensitive role requires manual creation'
                ];
            } else {
                $results['errors'][] = "Unknown role: {$role} for {$email}";
                $results['skipped']++;
            }
        }
    }
}

// Generate random password
function generate_random_password($length = 10)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
    return substr(str_shuffle($chars), 0, $length);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Bulk Process | Admin | Verdant SMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <style>
        .container {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: rgba(20, 20, 30, 0.9);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .card-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            color: var(--neon-green);
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .form-textarea {
            width: 100%;
            min-height: 200px;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.5);
            border: 2px solid var(--glass-border);
            border-radius: 10px;
            color: var(--text-primary);
            font-family: monospace;
            font-size: 0.9rem;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--cyber-cyan);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--neon-green), var(--cyber-cyan));
            color: #000;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 255, 136, 0.3);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid var(--cyber-cyan);
            color: var(--cyber-cyan);
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .alert-warning {
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid #ffa500;
            color: #ffa500;
        }

        .alert-success {
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid var(--neon-green);
            color: var(--neon-green);
        }

        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .result-stat {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }

        .result-stat .number {
            font-size: 2.5rem;
            font-family: 'Orbitron', sans-serif;
        }

        .result-stat .label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .stat-processed .number {
            color: var(--cyber-cyan);
        }

        .stat-created .number {
            color: var(--neon-green);
        }

        .stat-flagged .number {
            color: #ffa500;
        }

        .stat-skipped .number {
            color: #ff4757;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }

        .data-table th {
            color: var(--cyber-cyan);
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(0, 255, 136, 0.2);
            color: var(--neon-green);
        }

        .badge-warning {
            background: rgba(255, 165, 0, 0.2);
            color: #ffa500;
        }

        .help-text {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
    </style>
</head>

<body class="cyber-bg">
    <?php include '../includes/cyber-nav.php'; ?>

    <main class="cyber-main">
        <div class="container">
            <h1 style="font-family: 'Orbitron', sans-serif; color: var(--cyber-cyan); margin-bottom: 2rem;">
                <i class="fas fa-robot"></i> AI Bulk Registration Processor
            </h1>

            <?php if (!$can_process && !empty($message)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-clock"></i> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($results['processed'] > 0): ?>
                <!-- Results -->
                <div class="card">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Processing Results</h3>

                    <div class="results-grid">
                        <div class="result-stat stat-processed">
                            <div class="number"><?= $results['processed'] ?></div>
                            <div class="label">Processed</div>
                        </div>
                        <div class="result-stat stat-created">
                            <div class="number"><?= $results['created'] ?></div>
                            <div class="label">Created</div>
                        </div>
                        <div class="result-stat stat-flagged">
                            <div class="number"><?= $results['flagged'] ?></div>
                            <div class="label">Flagged</div>
                        </div>
                        <div class="result-stat stat-skipped">
                            <div class="number"><?= $results['skipped'] ?></div>
                            <div class="label">Skipped</div>
                        </div>
                    </div>

                    <?php if (!empty($results['created_accounts'])): ?>
                        <h4 style="color: var(--neon-green); margin: 1.5rem 0 1rem;"><i class="fas fa-check-circle"></i> Accounts Created</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Password</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results['created_accounts'] as $acc): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($acc['name']) ?></td>
                                        <td><?= htmlspecialchars($acc['email']) ?></td>
                                        <td><span class="badge badge-success"><?= ucfirst($acc['role']) ?></span></td>
                                        <td><code><?= htmlspecialchars($acc['password']) ?></code></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if (!empty($results['flagged_accounts'])): ?>
                        <h4 style="color: #ffa500; margin: 1.5rem 0 1rem;"><i class="fas fa-flag"></i> Flagged for Manual Review</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results['flagged_accounts'] as $acc): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($acc['name']) ?></td>
                                        <td><?= htmlspecialchars($acc['email']) ?></td>
                                        <td><span class="badge badge-warning"><?= ucfirst($acc['role']) ?></span></td>
                                        <td><?= htmlspecialchars($acc['reason']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if (!empty($results['errors'])): ?>
                        <h4 style="color: #ff4757; margin: 1.5rem 0 1rem;"><i class="fas fa-exclamation-triangle"></i> Errors</h4>
                        <ul style="color: #ff4757;">
                            <?php foreach ($results['errors'] as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Manual Input Form -->
            <div class="card">
                <h3 class="card-title"><i class="fas fa-keyboard"></i> Manual Data Entry</h3>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                    Paste CSV data from your Google Sheet. One entry per line:<br>
                    <code style="color: var(--cyber-cyan);">Name, Email, Phone, Role, Child's Name (optional)</code>
                </p>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">CSV Data</label>
                        <textarea name="manual_data" class="form-textarea" placeholder="John Smith, john@email.com, +1234567890, parent, Jane Smith
Mary Johnson, mary@email.com, +0987654321, teacher,
Bob Principal, bob@school.edu, +1112223333, principal,"></textarea>
                        <p class="help-text">
                            <strong>Auto-create roles:</strong> parent, teacher<br>
                            <strong>Flagged roles:</strong> principal, vice-principal, accountant, admin-officer, librarian
                        </p>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" name="process" value="1" class="btn btn-primary" <?= !$can_process ? 'disabled' : '' ?>>
                            <i class="fas fa-robot"></i> Process Data
                        </button>
                        <a href="account-management.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </form>
            </div>

            <!-- Instructions -->
            <div class="card">
                <h3 class="card-title"><i class="fas fa-question-circle"></i> Google Sheets API Integration</h3>
                <p style="color: var(--text-secondary);">
                    For production use, integrate with Google Sheets API to automatically pull data:
                </p>
                <ol style="color: var(--text-secondary); padding-left: 1.5rem; line-height: 2;">
                    <li>Enable Google Sheets API in Google Cloud Console</li>
                    <li>Create a service account and download JSON key</li>
                    <li>Share your Google Sheet with the service account email</li>
                    <li>Place the JSON key in <code>/config/google-credentials.json</code></li>
                    <li>Update <code>GOOGLE_SHEET_ID</code> in settings</li>
                </ol>
            </div>
        </div>
    </main>
</body>

</html>