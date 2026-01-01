<?php

/**
 * Security Policies Management
 * Configure biometric/passkey requirements per role
 *
 * @package VerdantSMS
 * @since 3.0.0
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_admin('../login.php');

$message = '';
$message_type = '';

// All available roles
$roles = [
    'admin' => 'Administrator',
    'teacher' => 'Teacher',
    'student' => 'Student',
    'parent' => 'Parent',
    'principal' => 'Principal',
    'vice-principal' => 'Vice Principal',
    'librarian' => 'Librarian',
    'accountant' => 'Accountant',
    'transport' => 'Transport Manager',
    'hostel' => 'Hostel Warden',
    'canteen' => 'Canteen Manager',
    'nurse' => 'School Nurse',
    'counselor' => 'Counselor',
    'class-teacher' => 'Class Teacher',
    'subject-coordinator' => 'Subject Coordinator',
    'admin-officer' => 'Admin Officer',
    'alumni' => 'Alumni',
    'general' => 'General User'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_policies'])) {
        foreach ($roles as $role => $label) {
            $require_biometric = isset($_POST["require_biometric_$role"]) ? 1 : 0;
            $require_passkey = isset($_POST["require_passkey_$role"]) ? 1 : 0;
            $allow_password_fallback = isset($_POST["allow_password_$role"]) ? 1 : 0;

            // Check if policy exists
            $existing = db()->fetch(
                "SELECT id FROM biometric_policies WHERE role = ?",
                [$role]
            );

            if ($existing) {
                db()->update('biometric_policies', [
                    'require_biometric' => $require_biometric,
                    'require_passkey' => $require_passkey,
                    'allow_password_fallback' => $allow_password_fallback
                ], 'role = ?', [$role]);
            } else {
                db()->insert('biometric_policies', [
                    'role' => $role,
                    'require_biometric' => $require_biometric,
                    'require_passkey' => $require_passkey,
                    'allow_password_fallback' => $allow_password_fallback
                ]);
            }
        }

        $message = 'Security policies updated successfully!';
        $message_type = 'success';
    }
}

// Get current policies
$policies = [];
$policyData = db()->fetchAll("SELECT * FROM biometric_policies");
foreach ($policyData as $p) {
    $policies[$p['role']] = $p;
}

// Get user counts and credential stats per role
$userStats = db()->fetchAll("
    SELECT
        u.role,
        COUNT(DISTINCT u.id) as total_users,
        COUNT(DISTINCT wc.user_id) as users_with_credentials,
        SUM(CASE WHEN wc.credential_type = 'biometric' THEN 1 ELSE 0 END) as biometric_count,
        SUM(CASE WHEN wc.credential_type = 'passkey' THEN 1 ELSE 0 END) as passkey_count
    FROM users u
    LEFT JOIN webauthn_credentials wc ON u.id = wc.user_id
    WHERE u.status = 'active'
    GROUP BY u.role
");

$stats = [];
foreach ($userStats as $s) {
    $stats[$s['role']] = $s;
}

$page_title = "Security Policies";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/cyberpunk-ui.css" rel="stylesheet">
    <style>
        .policy-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--primary-color);
        }

        .policy-grid {
            display: grid;
            gap: 1.5rem;
        }

        .policy-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            display: grid;
            grid-template-columns: 1fr 200px 200px 150px;
            gap: 1rem;
            align-items: center;
            transition: all 0.3s ease;
        }

        .policy-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 0 20px rgba(0, 240, 255, 0.1);
        }

        .role-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .role-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #000;
        }

        .role-name {
            font-weight: 600;
            color: var(--text-primary);
        }

        .role-stats {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .policy-toggle {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .toggle-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.1);
            transition: .3s;
            border-radius: 26px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: #888;
            transition: .3s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background: linear-gradient(135deg, #00f0ff, #00d4aa);
        }

        input:checked+.toggle-slider:before {
            transform: translateX(24px);
            background-color: #000;
        }

        .compliance-badge {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            text-align: center;
        }

        .compliance-good {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .compliance-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .compliance-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00f0ff, #00d4aa);
            color: #000;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 240, 255, 0.3);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
        }

        .summary-card h3 {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .summary-card p {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        @media (max-width: 1024px) {
            .policy-card {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>
    <?php include '../includes/cyber-nav.php'; ?>

    <div class="policy-container">
        <div class="page-header">
            <h1><i class="fas fa-shield-alt"></i> Security Policies</h1>
            <a href="settings.php" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary);">
                <i class="fas fa-arrow-left"></i> Back to Settings
            </a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <i class="fas fa-check-circle"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Summary Stats -->
        <div class="summary-cards">
            <?php
            $totalUsers = array_sum(array_column($userStats, 'total_users'));
            $usersWithCreds = array_sum(array_column($userStats, 'users_with_credentials'));
            $biometricTotal = array_sum(array_column($userStats, 'biometric_count'));
            $passkeyTotal = array_sum(array_column($userStats, 'passkey_count'));
            ?>
            <div class="summary-card">
                <h3><?php echo $totalUsers; ?></h3>
                <p>Total Active Users</p>
            </div>
            <div class="summary-card">
                <h3><?php echo $usersWithCreds; ?></h3>
                <p>Users with Passwordless</p>
            </div>
            <div class="summary-card">
                <h3><?php echo $biometricTotal; ?></h3>
                <p>Biometric Credentials</p>
            </div>
            <div class="summary-card">
                <h3><?php echo $passkeyTotal; ?></h3>
                <p>Passkey Credentials</p>
            </div>
        </div>

        <form method="POST">
            <div class="policy-grid">
                <?php foreach ($roles as $role => $label):
                    $policy = $policies[$role] ?? ['require_biometric' => 0, 'require_passkey' => 0, 'allow_password_fallback' => 1];
                    $stat = $stats[$role] ?? ['total_users' => 0, 'users_with_credentials' => 0, 'biometric_count' => 0, 'passkey_count' => 0];

                    // Calculate compliance
                    $compliance = 100;
                    if ($policy['require_biometric'] && $stat['total_users'] > 0) {
                        $compliance = round(($stat['biometric_count'] / $stat['total_users']) * 100);
                    }

                    $complianceClass = $compliance >= 80 ? 'good' : ($compliance >= 50 ? 'warning' : 'danger');
                ?>
                    <div class="policy-card">
                        <div class="role-info">
                            <div class="role-icon">
                                <i class="fas fa-<?php
                                                    echo match ($role) {
                                                        'admin' => 'user-shield',
                                                        'teacher' => 'chalkboard-teacher',
                                                        'student' => 'user-graduate',
                                                        'parent' => 'users',
                                                        'principal' => 'crown',
                                                        'librarian' => 'book',
                                                        'accountant' => 'calculator',
                                                        'nurse' => 'heartbeat',
                                                        'transport' => 'bus',
                                                        'hostel' => 'building',
                                                        'canteen' => 'utensils',
                                                        default => 'user'
                                                    };
                                                    ?>"></i>
                            </div>
                            <div>
                                <div class="role-name"><?php echo $label; ?></div>
                                <div class="role-stats">
                                    <?php echo $stat['total_users']; ?> users •
                                    <?php echo $stat['users_with_credentials']; ?> with credentials
                                </div>
                            </div>
                        </div>

                        <div class="policy-toggle">
                            <div class="toggle-row">
                                <span class="toggle-label">Require Biometric</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="require_biometric_<?php echo $role; ?>"
                                        <?php echo $policy['require_biometric'] ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="toggle-row">
                                <span class="toggle-label">Require Passkey</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="require_passkey_<?php echo $role; ?>"
                                        <?php echo $policy['require_passkey'] ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="policy-toggle">
                            <div class="toggle-row">
                                <span class="toggle-label">Allow Password</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" name="allow_password_<?php echo $role; ?>"
                                        <?php echo $policy['allow_password_fallback'] ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="compliance-badge compliance-<?php echo $complianceClass; ?>">
                            <?php if ($policy['require_biometric']): ?>
                                <?php echo $compliance; ?>% Compliant
                            <?php else: ?>
                                Optional
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 2rem; text-align: center;">
                <button type="submit" name="update_policies" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save All Policies
                </button>
            </div>
        </form>
    </div>

    <script src="../assets/js/main.js"></script>
</body>

</html>