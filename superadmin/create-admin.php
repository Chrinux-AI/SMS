<?php
/**
 * Superadmin - Create School Admin
 * Create the single admin account for a registered school
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/school-context.php';

// Ensure superadmin access
if (!defined('TEST_MODE') || !TEST_MODE) {
    SchoolContext::requireSuperadmin();
}

$db = Database::getInstance();
$message = '';
$messageType = '';
$generatedPassword = '';

// Get school ID from query string
$schoolId = isset($_GET['school_id']) ? (int)$_GET['school_id'] : 0;
$isNewSchool = isset($_GET['new']);

// Get all schools without admin
$schoolsWithoutAdmin = $db->fetchAll(
    "SELECT s.id, s.school_code, s.school_name
     FROM schools s
     WHERE s.admin_user_id IS NULL AND s.is_active = 1
     ORDER BY s.school_name"
);

// Get selected school details
$selectedSchool = null;
if ($schoolId > 0) {
    $selectedSchool = $db->fetchOne(
        "SELECT * FROM schools WHERE id = ? AND admin_user_id IS NULL",
        [$schoolId]
    );
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schoolId = (int)($_POST['school_id'] ?? 0);
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $useGeneratedPassword = isset($_POST['generate_password']);
    $password = $useGeneratedPassword ? bin2hex(random_bytes(8)) : ($_POST['password'] ?? '');

    // Validation
    if (empty($schoolId) || empty($fullName) || empty($email) || empty($password)) {
        $message = 'All required fields must be filled.';
        $messageType = 'error';
    } else {
        // Check if email already exists
        $existingUser = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existingUser) {
            $message = 'Email already exists. Please use a different email.';
            $messageType = 'error';
        } else {
            // Check if school already has an admin
            $school = $db->fetchOne("SELECT * FROM schools WHERE id = ?", [$schoolId]);
            if (!$school) {
                $message = 'Invalid school selected.';
                $messageType = 'error';
            } elseif ($school['admin_user_id']) {
                $message = 'This school already has an admin assigned.';
                $messageType = 'error';
            } else {
                // Create admin user
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $userId = $db->insert('users', [
                    'school_id' => $schoolId,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'full_name' => $fullName,
                    'phone' => $phone,
                    'role' => 'admin',
                    'is_active' => 1,
                    'email_verified' => 1
                ]);

                if ($userId) {
                    // Link admin to school
                    $db->update('schools', ['admin_user_id' => $userId], 'id = ?', [$schoolId]);

                    $generatedPassword = $useGeneratedPassword ? $password : '';
                    $message = "Admin account created successfully for {$school['school_name']}!";
                    $messageType = 'success';

                    // Refresh schools list
                    $schoolsWithoutAdmin = $db->fetchAll(
                        "SELECT s.id, s.school_code, s.school_name
                         FROM schools s
                         WHERE s.admin_user_id IS NULL AND s.is_active = 1
                         ORDER BY s.school_name"
                    );
                } else {
                    $message = 'Failed to create admin account. Please try again.';
                    $messageType = 'error';
                }
            }
        }
    }
}

$pageTitle = "Create School Admin";
?>
<!DOCTYPE html>
<html lang="en" data-theme="cyberpunk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --cyber-cyan: #00FFFF;
            --cyber-green: #00FF7F;
            --cyber-purple: #8B00FF;
            --dark-bg: #0A0A0F;
            --card-bg: #12121A;
        }

        body {
            background: var(--dark-bg);
            color: #fff;
            font-family: 'Segoe UI', system-ui, sans-serif;
            margin: 0;
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
        }

        .back-link {
            color: var(--cyber-cyan);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        h1 {
            background: linear-gradient(90deg, var(--cyber-cyan), var(--cyber-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
        }

        .form-card {
            background: var(--card-bg);
            border: 1px solid rgba(0, 255, 255, 0.2);
            border-radius: 12px;
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            color: var(--cyber-cyan);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        input, select {
            width: 100%;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            color: #fff;
            font-size: 1rem;
            box-sizing: border-box;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--cyber-cyan);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.3);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--cyber-cyan), var(--cyber-purple));
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 255, 255, 0.4);
        }

        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .message.error {
            background: rgba(255, 0, 0, 0.2);
            border: 1px solid #ff4444;
            color: #ff4444;
        }

        .message.success {
            background: rgba(0, 255, 127, 0.2);
            border: 1px solid var(--cyber-green);
            color: var(--cyber-green);
        }

        .credentials-box {
            background: rgba(0, 255, 127, 0.1);
            border: 2px dashed var(--cyber-green);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            text-align: center;
        }

        .credentials-box h3 {
            color: var(--cyber-green);
            margin-top: 0;
        }

        .credentials-box code {
            display: block;
            background: rgba(0, 0, 0, 0.5);
            padding: 0.75rem;
            border-radius: 6px;
            margin: 0.5rem 0;
            font-size: 1.1rem;
            color: var(--cyber-cyan);
        }

        .warning-text {
            color: orange;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .no-schools {
            text-align: center;
            padding: 3rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .no-schools i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: rgba(0, 255, 255, 0.3);
        }

        .no-schools a {
            color: var(--cyber-cyan);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <h1><i class="fas fa-user-shield"></i> Create School Admin</h1>

        <?php if ($isNewSchool && $selectedSchool): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i>
                School "<strong><?= htmlspecialchars($selectedSchool['school_name']) ?></strong>" registered!
                Now create the admin account below.
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>

            <?php if ($generatedPassword): ?>
                <div class="credentials-box">
                    <h3><i class="fas fa-key"></i> Admin Credentials</h3>
                    <p>Send these credentials to the school admin:</p>
                    <code><strong>Email:</strong> <?= htmlspecialchars($_POST['email'] ?? '') ?></code>
                    <code><strong>Password:</strong> <?= htmlspecialchars($generatedPassword) ?></code>
                    <p class="warning-text">
                        <i class="fas fa-exclamation-triangle"></i>
                        This password will NOT be shown again. Copy it now!
                    </p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (empty($schoolsWithoutAdmin)): ?>
            <div class="form-card no-schools">
                <i class="fas fa-check-double"></i>
                <h3>All Schools Have Admins</h3>
                <p>Every registered school already has an admin assigned.</p>
                <p><a href="add-school.php">Register a new school</a> to create another admin.</p>
            </div>
        <?php else: ?>
            <div class="form-card">
                <form method="POST">
                    <div class="form-group">
                        <label for="school_id">Select School *</label>
                        <select id="school_id" name="school_id" required>
                            <option value="">-- Select School --</option>
                            <?php foreach ($schoolsWithoutAdmin as $school): ?>
                                <option value="<?= $school['id'] ?>"
                                    <?= $schoolId == $school['id'] ? 'selected' : '' ?>>
                                    [<?= htmlspecialchars($school['school_code']) ?>]
                                    <?= htmlspecialchars($school['school_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="full_name">Admin Full Name *</label>
                        <input type="text" id="full_name" name="full_name" required
                               placeholder="e.g. Mr. John Adebayo">
                    </div>

                    <div class="form-group">
                        <label for="email">Admin Email *</label>
                        <input type="email" id="email" name="email" required
                               placeholder="admin@school.com">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="+234...">
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="generate_password" name="generate_password" checked>
                            <label for="generate_password" style="margin-bottom: 0;">
                                Generate secure password automatically
                            </label>
                        </div>
                    </div>

                    <div class="form-group" id="password-field" style="display: none;">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password"
                               placeholder="Min 8 characters" minlength="8">
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-user-plus"></i> Create Admin Account
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Toggle password field visibility
        document.getElementById('generate_password').addEventListener('change', function() {
            const passwordField = document.getElementById('password-field');
            const passwordInput = document.getElementById('password');

            if (this.checked) {
                passwordField.style.display = 'none';
                passwordInput.required = false;
            } else {
                passwordField.style.display = 'block';
                passwordInput.required = true;
            }
        });
    </script>
</body>
</html>
