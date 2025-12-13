<?php

/**
 * Cyberpunk Login Page - Futuristic Authentication
 * Security Hardened: Rate limiting, account lockout, generic error messages
 */

session_start();

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

// Security constants
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION_MINUTES', 15);

/**
 * Check if account is locked
 */
function is_account_locked($user) {
    if (empty($user['locked_until'])) {
        return false;
    }
    return strtotime($user['locked_until']) > time();
}

/**
 * Get remaining lockout time in minutes
 */
function get_lockout_remaining($user) {
    if (empty($user['locked_until'])) {
        return 0;
    }
    $remaining = strtotime($user['locked_until']) - time();
    return max(0, ceil($remaining / 60));
}

/**
 * Record failed login attempt
 */
function record_failed_attempt($user_id) {
    $attempts = db()->fetchColumn(
        "SELECT failed_login_attempts FROM users WHERE id = ?",
        [$user_id]
    );
    $attempts = (int)$attempts + 1;
    
    $update_data = ['failed_login_attempts' => $attempts];
    
    // Lock account if max attempts reached
    if ($attempts >= MAX_LOGIN_ATTEMPTS) {
        $update_data['locked_until'] = date('Y-m-d H:i:s', strtotime('+' . LOCKOUT_DURATION_MINUTES . ' minutes'));
    }
    
    db()->update('users', $update_data, 'id = ?', [$user_id]);
}

/**
 * Reset failed login attempts on successful login
 */
function reset_failed_attempts($user_id) {
    db()->update('users', [
        'failed_login_attempts' => 0,
        'locked_until' => null
    ], 'id = ?', [$user_id]);
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Honeypot check - bot detection
    if (!empty($_POST['website'])) {
        // Silently reject spam bots
        sleep(2);
        $error = 'Invalid email or password. Please try again.';
    }
    // Verify CSRF token
    elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please refresh and try again.';
    } else {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Please enter both email and password.';
        } else {
            $user = db()->fetchOne(
                "SELECT * FROM users WHERE email = ?",
                [$email]
            );

            // Check if account is locked (even for non-existent users, use generic message)
            if ($user && is_account_locked($user)) {
                $remaining = get_lockout_remaining($user);
                $error = "Account temporarily locked due to too many failed attempts. Please try again in {$remaining} minutes.";
            }
            // Validate credentials
            elseif ($user && password_verify($password, $user['password_hash'])) {
                // Reset failed attempts on successful password verification
                reset_failed_attempts($user['id']);
                
                // Check user status
                if ($user['email_verified'] == 0) {
                    // Generate OTP for verification
                    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                    db()->query("UPDATE users SET otp_code = ?, otp_expires_at = ? WHERE id = ?", [$otp, $expires, $user['id']]);

                    // Send OTP email
                    send_email(
                        $user['email'],
                        'Your Verdant SMS Verification Code',
                        "Hello " . ($user['first_name'] ?? 'User') . ",\n\n" .
                            "Your verification code is: $otp\n\n" .
                            "This code expires in 15 minutes.\n\n" .
                            "Or click this link to verify: " . (getenv('APP_URL') ?: 'http://localhost/attendance') . "/verify.php?token=" . ($user['verification_token'] ?? '')
                    );

                    $error = 'Please verify your email address. A new verification code has been sent to your inbox. <a href="verify.php?email=' . urlencode($email) . '" style="color: #00ff88;">Click here to verify</a>';
                } elseif ($user['approved'] == 0) {
                    $error = 'Your account is pending admin approval. You will receive an email once approved.';
                } elseif ($user['status'] !== 'active') {
                    $error = 'Your account is not active. Please contact the administrator.';
                } else {
                    // Update last login time
                    db()->update('users', [
                        'last_login' => date('Y-m-d H:i:s')
                    ], 'id = ?', [$user['id']]);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['user_role'] = $user['role'];  // For compatibility with has_role() function
                    $_SESSION['assigned_id'] = $user['assigned_id'];
                    $_SESSION['last_login'] = $user['last_login'];  // Store previous login time

                    // Log the login activity
                    log_activity($user['id'], 'login', 'users', $user['id']);

                    // Redirect based on role
                    $role_redirects = [
                        // Leadership
                        'principal' => 'principal/dashboard.php',
                        'vice-principal' => 'vice-principal/dashboard.php',
                        // Administration
                        'admin' => 'admin/dashboard.php',
                        'admin-officer' => 'admin-officer/dashboard.php',
                        'accountant' => 'accountant/dashboard/index.php',
                        // Academic
                        'teacher' => 'teacher/dashboard.php',
                        'class-teacher' => 'class-teacher/dashboard.php',
                        'subject-coordinator' => 'subject-coordinator/dashboard.php',
                        // Support Services
                        'librarian' => 'librarian/dashboard.php',
                        'counselor' => 'counselor/dashboard.php',
                        'nurse' => 'nurse/dashboard.php',
                        // Facilities
                        'transport' => 'transport/dashboard.php',
                        'hostel' => 'hostel/dashboard.php',
                        'canteen' => 'canteen/dashboard.php',
                        'general' => 'general/dashboard.php',
                        // Users
                        'student' => 'student/dashboard.php',
                        'parent' => 'parent/dashboard.php',
                        'alumni' => 'alumni/dashboard.php',
                    ];

                    $redirect_url = $role_redirects[$user['role']] ?? 'student/dashboard.php';
                    header('Location: ' . $redirect_url);
                    exit;
                }
            } else {
                // Record failed attempt if user exists
                if ($user) {
                    record_failed_attempt($user['id']);
                    $attempts_left = MAX_LOGIN_ATTEMPTS - (int)($user['failed_login_attempts'] ?? 0) - 1;
                    if ($attempts_left > 0 && $attempts_left <= 3) {
                        $error = "Invalid email or password. {$attempts_left} attempts remaining before account lockout.";
                    } else {
                        $error = 'Invalid email or password. Please try again.';
                    }
                } else {
                    // Generic error for non-existent users (no information leakage)
                    $error = 'Invalid email or password. Please try again.';
                }
            }
        }
    }
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
    <title>Attendance Login - <?php echo APP_NAME; ?></title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Cyberpunk UI Framework -->
    <link href="assets/css/cyberpunk-ui.css" rel="stylesheet">

    <!-- Biometric Authentication -->
    <script src="assets/js/biometric-auth.js"></script>

    <style>
        /* Ensure background elements don't block inputs */
        .starfield,
        .cyber-grid,
        .cyber-bg::before,
        .particle {
            pointer-events: none !important;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .login-orb-wrapper {
            position: relative;
            width: 100%;
            max-width: 450px;
            z-index: 10;
        }

        /* Floating Login Hologram */
        .login-hologram {
            background: rgba(30, 30, 30, 0.7);
            backdrop-filter: blur(30px);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            padding: 50px 40px;
            box-shadow:
                0 0 60px rgba(0, 191, 255, 0.3),
                0 20px 60px rgba(0, 0, 0, 0.5);
            animation: floatHologram 6s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .login-hologram::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg,
                    transparent 0deg,
                    rgba(0, 191, 255, 0.1) 60deg,
                    transparent 120deg);
            animation: rotateBorder 8s linear infinite;
        }

        @keyframes floatHologram {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-15px) scale(1.02);
            }
        }

        @keyframes rotateBorder {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .login-content {
            position: relative;
            z-index: 1;
        }

        /* Brand Orb */
        .login-brand-orb {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            background: radial-gradient(circle, var(--cyber-cyan) 0%, var(--hologram-purple) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                0 0 40px var(--cyber-cyan),
                0 0 80px rgba(0, 191, 255, 0.5);
            animation: orbPulse 3s ease-in-out infinite;
        }

        .login-brand-orb i {
            font-size: 3rem;
            color: var(--text-primary);
        }

        .login-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--cyber-cyan), var(--hologram-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-subtitle {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 40px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Biometric Scan Button */
        .biometric-scan-btn {
            width: 100%;
            padding: 15px;
            background: radial-gradient(circle, var(--golden-pulse), var(--hologram-purple));
            border: none;
            border-radius: 15px;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.5);
            transition: all var(--transition-smooth);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .biometric-scan-btn:hover {
            box-shadow: 0 0 50px rgba(255, 215, 0, 0.8);
            transform: translateY(-3px);
        }

        .biometric-icon {
            font-size: 1.5rem;
            animation: scanPulse 2s ease-in-out infinite;
        }

        /* Divider */
        .login-divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--glass-border), transparent);
        }

        .login-divider span {
            padding: 0 15px;
        }

        /* Form Styling */
        .login-form {
            margin-bottom: 25px;
        }

        .alert-hologram {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: fadeIn 0.5s;
        }

        .alert-hologram.error {
            background: rgba(255, 69, 0, 0.15);
            border: 1px solid var(--cyber-red);
            color: var(--cyber-red);
            box-shadow: 0 0 20px rgba(255, 69, 0, 0.3);
        }

        .alert-hologram.success {
            background: rgba(0, 255, 127, 0.15);
            border: 1px solid var(--neon-green);
            color: var(--neon-green);
            box-shadow: 0 0 20px rgba(0, 255, 127, 0.3);
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: var(--cyber-cyan);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all var(--transition-smooth);
        }

        .forgot-password a:hover {
            color: var(--hologram-purple);
            text-shadow: 0 0 10px rgba(0, 191, 255, 0.5);
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid var(--glass-border);
            color: var(--text-muted);
        }

        .register-link a {
            color: var(--neon-green);
            text-decoration: none;
            font-weight: 600;
            transition: all var(--transition-smooth);
        }

        .register-link a:hover {
            color: var(--cyber-cyan);
            text-shadow: 0 0 10px rgba(0, 255, 127, 0.5);
        }

        /* Particles Effect */
        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: var(--cyber-cyan);
            border-radius: 50%;
            opacity: 0.6;
            animation: particleFloat 10s linear infinite;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0);
                opacity: 0;
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.6;
            }

            100% {
                transform: translateY(-100vh) translateX(100px);
                opacity: 0;
            }
        }
    </style>
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <!-- Floating Particles -->
    <?php for ($i = 0; $i < 20; $i++): ?>
        <div class="particle" style="
            left: <?php echo rand(0, 100); ?>%;
            animation-delay: <?php echo rand(0, 10); ?>s;
            animation-duration: <?php echo rand(8, 15); ?>s;
        "></div>
    <?php endfor; ?>

    <!-- Login Container -->
    <div class="login-container fade-in">
        <div class="login-orb-wrapper">
            <div class="login-hologram">
                <div class="login-content">
                    <!-- Brand Orb -->
                    <div class="login-brand-orb">
                        <i class="fas fa-fingerprint"></i>
                    </div>

                    <!-- Title -->
                    <h1 class="login-title">System Login</h1>
                    <p class="login-subtitle">Biometric Authentication</p>

                    <!-- Alerts -->
                    <?php if ($error): ?>
                        <div class="alert-hologram error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><?php echo htmlspecialchars($error); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert-hologram success">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo htmlspecialchars($success); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Biometric Scan Button -->
                    <button type="button" class="biometric-scan-btn" onclick="performBiometricLogin()">
                        <i class="fas fa-fingerprint biometric-icon"></i>
                        <span>Scan to Authenticate</span>
                    </button>

                    <!-- Divider -->
                    <div class="login-divider">
                        <span>OR USE CREDENTIALS</span>
                    </div>

                    <!-- Login Form -->
                    <form method="POST" action="" class="login-form">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        
                        <!-- Honeypot field for bot detection -->
                        <div style="position: absolute; left: -9999px;">
                            <input type="text" name="website" tabindex="-1" autocomplete="off">
                        </div>
                        
                        <div class="cyber-input-group">
                            <label class="cyber-label" for="email">
                                <i class="fas fa-envelope"></i> Email Address
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="cyber-input"
                                placeholder="Enter your email"
                                required
                                autocomplete="email">
                        </div>

                        <div class="cyber-input-group">
                            <label class="cyber-label" for="password">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <div class="pw-wrapper">
                                <input type="password" id="password" name="password" class="cyber-input pw-input" placeholder="Enter your password" required>
                                <button type="button" class="pw-toggle" aria-label="Toggle password visibility" onclick="togglePassword('password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" name="login" class="cyber-btn cyber-btn-primary" style="width: 100%;">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>LOGIN</span>
                        </button>
                    </form>

                    <!-- Forgot Password -->
                    <div class="forgot-password">
                        <a href="forgot-password.php">
                            <i class="fas fa-key"></i> Forgot Password?
                        </a>
                    </div>

                    <!-- Register Link -->
                    <div class="register-link">
                        Need an account? <a href="register.php">Register Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        /**
         * Perform real biometric login
         */
        async function performBiometricLogin() {
            const btn = event.target.closest('.biometric-scan-btn');
            const originalHTML = btn.innerHTML;

            // Check if biometric is supported
            if (!window.biometricAuth || !window.biometricAuth.supported) {
                alert('Biometric authentication is not supported on this browser/device.\\n\\nPlease use Chrome, Edge, or Safari on a device with biometric capabilities.');
                return;
            }

            try {
                // Update button to scanning state
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>SCANNING...</span>';
                btn.style.background = 'radial-gradient(circle, var(--cyber-cyan), var(--hologram-purple))';
                btn.disabled = true;

                // Perform biometric login
                const result = await window.biometricAuth.login();

                if (result.success) {
                    // Success
                    btn.innerHTML = '<i class="fas fa-check-circle"></i> <span>AUTHENTICATED</span>';
                    btn.style.background = 'radial-gradient(circle, var(--neon-green), var(--hologram-purple))';

                    // Redirect to dashboard
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    throw new Error(result.error || 'Authentication failed');
                }

            } catch (error) {
                console.error('Biometric login error:', error);

                // Show error
                btn.innerHTML = '<i class="fas fa-times-circle"></i> <span>AUTH FAILED</span>';
                btn.style.background = 'radial-gradient(circle, var(--cyber-red), var(--hologram-purple))';

                // Show error message
                setTimeout(() => {
                    if (error.message && error.message.includes('not supported')) {
                        alert('Biometric authentication is not supported on this device.');
                    } else if (error.message && error.message.includes('credential')) {
                        alert('No biometric credentials found.\\n\\nPlease login with email/password first, then register your biometric in Settings.');
                    } else {
                        alert('Biometric authentication failed.\\n\\n' + (error.message || 'Unknown error'));
                    }

                    // Reset button
                    btn.innerHTML = originalHTML;
                    btn.style.background = 'radial-gradient(circle, var(--golden-pulse), var(--hologram-purple))';
                    btn.disabled = false;
                }, 2000);
            }
        }

        // Auto-focus on email field
        document.getElementById('email').focus();

        // Add enter key support
        document.querySelectorAll('.cyber-input').forEach(input => {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    const form = e.target.closest('form');
                    if (form) form.submit();
                }
            });
        });

        // Password toggle shared helper
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

    <?php include 'includes/theme-toggle.php'; ?>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/pwa-manager.js"></script>
    <script src="assets/js/pwa-analytics.js"></script>
</body>

</html>