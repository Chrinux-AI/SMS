<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';

$message = '';
$message_type = '';

// Check if registration is enabled
$setting = db()->fetch("SELECT setting_value FROM system_settings WHERE setting_key = 'registration_enabled'");
$registration_enabled = $setting ? (bool)$setting['setting_value'] : true;

if (!$registration_enabled) {
    $message = 'Registration is currently disabled. Please contact the administrator.';
    $message_type = 'error';
}

// Handle registration submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register']) && $registration_enabled) {
    $errors = [];

    try {
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $first_name = sanitize($_POST['first_name'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $role = sanitize($_POST['role'] ?? '');

        // Block admin role registration
        if ($role === 'admin') {
            $errors[] = 'Admin registration is not allowed. Contact system administrator.';
        }
        $phone = sanitize($_POST['phone'] ?? '');

        if (empty($username)) $errors[] = 'Username is required';
        if (empty($email)) $errors[] = 'Email is required';
        if (empty($password)) $errors[] = 'Password is required';
        if (empty($first_name)) $errors[] = 'First name is required';
        if (empty($last_name)) $errors[] = 'Last name is required';
        if (empty($role)) $errors[] = 'Role is required';

        if (strlen($username) < 3) $errors[] = 'Username must be at least 3 characters';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters';
        if ($password !== $confirm_password) $errors[] = 'Passwords do not match';
        if (!in_array($role, ['student', 'parent', 'teacher'])) $errors[] = 'Invalid role selected';

        if ($role === 'student') {
            if (empty($_POST['date_of_birth'])) $errors[] = 'Date of birth is required for students';
            if (empty($_POST['grade_level'])) $errors[] = 'Grade level is required for students';
        }

        $existing = db()->fetch("SELECT id FROM users WHERE username = ?", [$username]);
        if ($existing) $errors[] = 'Username already exists';

        $existing = db()->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) $errors[] = 'Email already registered';

        if (empty($errors)) {
            // Generate verification token with 10-minute expiration
            $verification_token = bin2hex(random_bytes(32));
            $token_expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            $user_data = [
                'username' => $username,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'first_name' => $first_name,
                'last_name' => $last_name,
                'role' => $role,
                'phone' => $phone,
                'status' => 'pending',
                'email_verified' => 0,
                'email_verification_token' => $verification_token,
                'token_expires_at' => $token_expires_at,
                'approved' => 0
            ];

            $user_id = db()->insert('users', $user_data);

            if ($user_id) {
                // Generate student ID with YEAR+sequential format
                $assigned_id = null; // Initialize assigned ID variable

                if ($role === 'student') {
                    $year = date('Y');
                    $count = db()->count('students') + 1;
                    $student_id = $year . str_pad($count, 4, '0', STR_PAD_LEFT);
                    $assigned_id = 'STU' . $student_id; // Format for display

                    $student_data = [
                        'user_id' => $user_id,
                        'student_id' => $student_id,
                        'assigned_student_id' => $student_id,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'email' => $email,
                        'phone' => $phone,
                        'date_of_birth' => $_POST['date_of_birth'],
                        'grade_level' => (int)$_POST['grade_level'],
                        'status' => 'pending'
                    ];
                    db()->insert('students', $student_data);
                } elseif ($role === 'teacher') {
                    // Generate teacher employee ID
                    $year = date('Y');
                    $count = db()->count('teachers') + 1;
                    $teacher_id = $year . str_pad($count, 4, '0', STR_PAD_LEFT);
                    $assigned_id = 'EMP' . $teacher_id; // Format for display

                    // Teachers table insert would happen here (if exists)
                }

                // Send verification email
                $verification_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/verify-email.php?token=" . $verification_token;

                $to = $email;
                $subject = "Verify Your Email - School Management System";
                $email_message = "
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <title>Email Verification</title>
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="assets/images/icons/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/images/icons/favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/icons/apple-touch-icon.png">
    <link rel="manifest" href="manifest.json">
    <meta name="msapplication-TileColor" content="#00BFFF">
    <meta name="msapplication-TileImage" content="assets/images/icons/mstile-150x150.png">
    <meta name="theme-color" content="#0a0a0f">
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f4f4f4; }
                        .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; }
                        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                        .content { padding: 30px; }
                        .button { display: inline-block; background: #00BFFF; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                        .footer { background: #f9f9f9; padding: 20px; text-align: center; color: #666; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>📧 Email Verification</h1>
                        </div>
                        <div class='content'>
                            <p>Hello <strong>{$first_name} {$last_name}</strong>,</p>
                            <p>Thank you for registering with the Attendance Management System!</p>
                            <p>Please verify your email address by clicking the button below:</p>
                            <p style='text-align: center;'>
                                <a href='{$verification_link}' class='button'>Verify My Email</a>
                            </p>
                            <p>Or copy and paste this link into your browser:</p>
                            <p style='background: #f9f9f9; padding: 10px; border-radius: 5px; word-break: break-all; font-size: 12px;'>{$verification_link}</p>
                            <p><strong>⏱️ This link expires in 10 minutes.</strong> Please verify soon!</p>
                            <p><strong>⚠️ Important:</strong> After email verification, your account must be approved by an administrator before you can login.</p>
                        </div>
                        <div class='footer'>
                            <p>If you didn't register for this account, please ignore this email.</p>
                            <p>&copy; " . date('Y') . " School Management System. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>
                ";

                // Send verification email using proper function
                $email_sent = send_verification_email($email, $first_name . ' ' . $last_name, $verification_token, $assigned_id, $role);

                if ($email_sent) {
                    $message = 'Registration successful! Please check your email (' . $email . ') to verify your account. ⏱️ Verification link expires in 10 minutes.';
                    $message_type = 'success';
                } else {
                    $message = 'Registration successful but verification email failed to send. Please contact administrator.';
                    $message_type = 'warning';
                }
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    } catch (Exception $e) {
        $errors[] = 'An error occurred: ' . $e->getMessage();
    }

    if (!empty($errors)) {
        $message = implode('<br>', $errors);
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/css/cyberpunk-ui.css" rel="stylesheet">
    <style>
        .register-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            max-width: 700px;
            width: 100%;
            background: rgba(10, 10, 10, 0.9);
            border: 1px solid rgba(0, 191, 255, 0.3);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 0 50px rgba(0, 191, 255, 0.2);
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            color: var(--cyber-cyan);
            font-family: Orbitron;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .register-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .role-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .role-option {
            position: relative;
        }

        .role-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .role-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            padding: 20px;
            background: rgba(10, 10, 10, 0.6);
            border: 2px solid rgba(100, 100, 100, 0.3);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .role-label:hover {
            border-color: var(--cyber-cyan);
            box-shadow: 0 0 20px rgba(0, 191, 255, 0.3);
        }

        .role-option input:checked+.role-label {
            border-color: var(--neon-green);
            background: rgba(0, 255, 127, 0.1);
            box-shadow: 0 0 30px rgba(0, 255, 127, 0.4);
        }

        .role-label i {
            font-size: 2.5rem;
            color: var(--cyber-cyan);
        }

        .role-label span {
            color: var(--text-primary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            color: var(--cyber-cyan);
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            background: rgba(0, 191, 255, 0.05);
            border: 1px solid var(--cyber-cyan);
            border-radius: 8px;
            color: var(--cyber-cyan);
            font-family: Rajdhani;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--neon-green);
            box-shadow: 0 0 20px rgba(0, 255, 127, 0.3);
            background: rgba(0, 255, 127, 0.05);
        }

        .conditional-fields {
            display: none;
            grid-column: span 2;
        }

        .conditional-fields.active {
            display: block;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--cyber-cyan), var(--hologram-purple));
            border: none;
            border-radius: 10px;
            color: white;
            font-family: Orbitron;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 30px rgba(0, 191, 255, 0.5);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: var(--text-muted);
        }

        .login-link a {
            color: var(--cyber-cyan);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            color: var(--neon-green);
        }

        .cyber-alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-family: Rajdhani;
            font-weight: 600;
        }

        .cyber-alert.success {
            background: rgba(0, 255, 127, 0.1);
            border: 1px solid var(--neon-green);
            color: var(--neon-green);
        }

        .cyber-alert.error {
            background: rgba(255, 69, 0, 0.1);
            border: 1px solid var(--cyber-red);
            color: var(--cyber-red);
        }
    </style>
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>
    <div class="cyber-bg">
        <div class="starfield"></div>
    </div>
    <div class="cyber-grid"></div>
    <div class="register-wrapper">
        <div class="register-card">
            <div class="register-header">
                <h1><i class="fas fa-user-plus"></i> Create Account</h1>
                <p>Register for School Management System Access</p>
            </div>
            <?php if ($message): ?>
                <div class="cyber-alert <?php echo $message_type === 'success' ? 'success' : 'error'; ?>">
                    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                    <span><?php echo $message; ?></span>
                </div>
            <?php endif; ?>
            <?php if ($registration_enabled && (!$message || $message_type !== 'success')): ?>
                <form method="POST">
                    <div class="role-selector">
                        <div class="role-option">
                            <input type="radio" id="student" name="role" value="student" required>
                            <label for="student" class="role-label">
                                <i class="fas fa-user-graduate"></i>
                                <span>Student</span>
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="parent" name="role" value="parent">
                            <label for="parent" class="role-label">
                                <i class="fas fa-user-friends"></i>
                                <span>Parent</span>
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="teacher" name="role" value="teacher">
                            <label for="teacher" class="role-label">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span>Teacher</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name"><i class="fas fa-user"></i> First Name</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name"><i class="fas fa-user"></i> Last Name</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                        <div class="form-group full-width">
                            <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="username"><i class="fas fa-at"></i> Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="phone"><i class="fas fa-phone"></i> Phone</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> Password</label>
                            <div class="pw-wrapper">
                                <input type="password" id="password" name="password" class="pw-input" required>
                                <button type="button" class="pw-toggle" aria-label="Toggle password visibility" onclick="togglePassword('password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                            <div class="pw-wrapper">
                                <input type="password" id="confirm_password" name="confirm_password" class="pw-input" required>
                                <button type="button" class="pw-toggle" aria-label="Toggle password visibility" onclick="togglePassword('confirm_password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div id="student_fields" class="conditional-fields">
                            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:20px;">
                                <div class="form-group">
                                    <label for="date_of_birth"><i class="fas fa-calendar"></i> Date of Birth</label>
                                    <input type="date" id="date_of_birth" name="date_of_birth">
                                </div>
                                <div class="form-group">
                                    <label for="grade_level"><i class="fas fa-graduation-cap"></i> Level</label>
                                    <select id="grade_level" name="grade_level">
                                        <option value="">Select Level</option>
                                        <option value="100">100 Level</option>
                                        <option value="200">200 Level</option>
                                        <option value="300">300 Level</option>
                                        <option value="400">400 Level</option>
                                        <option value="500">500 Level</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <button type="submit" name="register" class="btn-submit">
                                <i class="fas fa-rocket"></i> Create Account
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
            <div class="login-link">
                Already have an account? <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login here</a>
            </div>
        </div>
    </div>
    <script>
        const roleInputs = document.querySelectorAll('input[name="role"]');
        const studentFields = document.getElementById('student_fields');
        roleInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value === 'student') {
                    studentFields.classList.add('active');
                    document.getElementById('date_of_birth').required = true;
                    document.getElementById('grade_level').required = true;
                } else {
                    studentFields.classList.remove('active');
                    document.getElementById('date_of_birth').required = false;
                    document.getElementById('grade_level').required = false;
                }
            });
        });
    </script>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body>

</html>
<script>
    function togglePassword(fieldId, btn) {
        var input = document.getElementById(fieldId);
        var icon = btn.querySelector('i');
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }
</script>