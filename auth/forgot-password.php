<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

$message = '';
$message_type = '';
$step = 'email'; // email, otp, reset

// Handle email submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_otp'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address';
        $message_type = 'error';
    } else {
        $user = db()->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);

        if ($user) {
            // Generate 6-digit OTP
            $otp = sprintf("%06d", mt_rand(1, 999999));
            $otp_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // Store OTP in database
            db()->query(
                "UPDATE users SET reset_otp = ?, reset_otp_expiry = ? WHERE email = ?",
                [$otp, $otp_expiry, $email]
            );

            // Send OTP via email
            $subject = "Password Reset OTP - School Management System";
            $user_first_name = htmlspecialchars($user['first_name']);
            $current_year = date('Y');
            $email_body = <<<HTML
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; background: #f4f4f4; }
                    .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; }
                    .header { background: linear-gradient(135deg, #00BFFF, #8A2BE2); color: white; padding: 30px; text-align: center; }
                    .content { padding: 30px; }
                    .otp-box { background: #f0f0f0; border: 2px dashed #00BFFF; border-radius: 10px; padding: 20px; text-align: center; margin: 20px 0; }
                    .otp-code { font-size: 32px; font-weight: bold; color: #00BFFF; letter-spacing: 8px; font-family: 'Courier New', monospace; }
                    .footer { background: #f9f9f9; padding: 20px; text-align: center; color: #666; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🔐 Password Reset Request</h1>
                    </div>
                    <div class='content'>
                        <p>Hello <strong>{$user_first_name}</strong>,</p>
                        <p>You have requested to reset your password. Please use the OTP code below to verify your identity:</p>

                        <div class='otp-box'>
                            <div style='color: #666; font-size: 14px; margin-bottom: 10px;'>Your OTP Code</div>
                            <div class='otp-code'>{$otp}</div>
                            <div style='color: #999; font-size: 12px; margin-top: 10px;'>Valid for 15 minutes</div>
                        </div>

                        <p><strong>⚠️ Security Notice:</strong></p>
                        <ul>
                            <li>Do not share this code with anyone</li>
                            <li>This code expires in 15 minutes</li>
                            <li>If you didn't request this, please ignore this email</li>
                        </ul>
                    </div>
                    <div class='footer'>
                        <p>This is an automated message from the Attendance Management System.</p>
                        <p>&copy; {$current_year} School Management System. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
HTML;

            if (quick_send_email($email, $subject, $email_body)) {
                $_SESSION['reset_email'] = $email;
                $message = 'OTP sent successfully! Check your email.';
                $message_type = 'success';
                $step = 'otp';
            } else {
                $message = 'Failed to send OTP. Please try again.';
                $message_type = 'error';
            }
        } else {
            // Don't reveal if email exists for security
            $_SESSION['reset_email'] = $email;
            $message = 'If this email exists, an OTP has been sent.';
            $message_type = 'success';
            $step = 'otp';
        }
    }
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $email = $_SESSION['reset_email'] ?? '';
    $otp = $_POST['otp'] ?? '';

    $user = db()->fetchOne(
        "SELECT * FROM users WHERE email = ? AND reset_otp = ? AND reset_otp_expiry > NOW()",
        [$email, $otp]
    );

    if ($user) {
        $_SESSION['verified_email'] = $email;
        $step = 'reset';
        $message = 'OTP verified! Please set your new password.';
        $message_type = 'success';
    } else {
        $message = 'Invalid or expired OTP. Please try again.';
        $message_type = 'error';
        $step = 'otp';
    }
}

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $email = $_SESSION['verified_email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters';
        $message_type = 'error';
        $step = 'reset';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match';
        $message_type = 'error';
        $step = 'reset';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        db()->query(
            "UPDATE users SET password_hash = ?, reset_otp = NULL, reset_otp_expiry = NULL WHERE email = ?",
            [$password_hash, $email]
        );

        $message = 'Password reset successfully! You can now login.';
        $message_type = 'success';

        // Clear session
        unset($_SESSION['reset_email']);
        unset($_SESSION['verified_email']);

        // Redirect to login after 3 seconds
        header("refresh:3;url=login.php");
    }
}

// Determine current step from session
if (isset($_SESSION['verified_email'])) {
    $step = 'reset';
} elseif (isset($_SESSION['reset_email'])) {
    $step = 'otp';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?php echo APP_NAME; ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/css/cyberpunk-ui.css" rel="stylesheet">

    <style>
        .forgot-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .forgot-card {
            max-width: 500px;
            width: 100%;
            background: rgba(30, 30, 30, 0.7);
            backdrop-filter: blur(30px);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 0 60px rgba(0, 191, 255, 0.3);
        }

        .forgot-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .forgot-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--cyber-cyan), var(--hologram-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            box-shadow: 0 0 30px rgba(0, 191, 255, 0.5);
        }

        .forgot-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            color: var(--cyber-cyan);
            margin-bottom: 10px;
        }

        .otp-input-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }

        .otp-digit {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            background: rgba(0, 191, 255, 0.05);
            border: 2px solid var(--cyber-cyan);
            border-radius: 10px;
            color: var(--cyber-cyan);
            font-family: 'Courier New', monospace;
        }

        .otp-digit:focus {
            outline: none;
            border-color: var(--neon-green);
            box-shadow: 0 0 20px rgba(0, 255, 127, 0.3);
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

    <div class="forgot-container fade-in">
        <div class="forgot-card">
            <div class="forgot-header">
                <div class="forgot-icon">
                    <i class="fas fa-<?php echo $step === 'reset' ? 'lock-open' : ($step === 'otp' ? 'shield-alt' : 'key'); ?>"></i>
                </div>
                <h1 class="forgot-title">
                    <?php
                    echo $step === 'reset' ? 'Reset Password' : ($step === 'otp' ? 'Verify OTP' : 'Forgot Password');
                    ?>
                </h1>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">
                    <?php
                    echo $step === 'reset' ? 'Enter your new password' : ($step === 'otp' ? 'Enter the 6-digit code sent to your email' : 'Enter your email to receive OTP');
                    ?>
                </p>
            </div>

            <?php if ($message): ?>
                <div class="cyber-alert cyber-alert-<?php echo $message_type === 'success' ? 'success' : 'error'; ?>" style="margin-bottom: 20px;">
                    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($step === 'email'): ?>
                <form method="POST">
                    <div class="cyber-form-group">
                        <label class="cyber-label" for="email">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" id="email" name="email" class="cyber-input" required placeholder="your@email.com">
                    </div>

                    <button type="submit" name="send_otp" class="cyber-btn cyber-btn-primary" style="width: 100%; margin-top: 20px;">
                        <i class="fas fa-paper-plane"></i>
                        <span>Send OTP</span>
                    </button>
                </form>

            <?php elseif ($step === 'otp'): ?>
                <form method="POST" id="otpForm">
                    <div class="otp-input-group">
                        <input type="text" maxlength="1" class="otp-digit" id="otp1" required>
                        <input type="text" maxlength="1" class="otp-digit" id="otp2" required>
                        <input type="text" maxlength="1" class="otp-digit" id="otp3" required>
                        <input type="text" maxlength="1" class="otp-digit" id="otp4" required>
                        <input type="text" maxlength="1" class="otp-digit" id="otp5" required>
                        <input type="text" maxlength="1" class="otp-digit" id="otp6" required>
                    </div>
                    <input type="hidden" name="otp" id="otpValue">

                    <button type="submit" name="verify_otp" class="cyber-btn cyber-btn-primary" style="width: 100%; margin-top: 20px;">
                        <i class="fas fa-check-circle"></i>
                        <span>Verify OTP</span>
                    </button>

                    <button type="button" onclick="window.location.href='forgot-password.php'; sessionStorage.clear();" class="cyber-btn cyber-btn-outline" style="width: 100%; margin-top: 10px;">
                        <i class="fas fa-redo"></i>
                        <span>Resend OTP</span>
                    </button>
                </form>

            <?php elseif ($step === 'reset'): ?>
                <form method="POST">
                    <div class="cyber-form-group">
                        <label class="cyber-label" for="password">
                            <i class="fas fa-lock"></i> New Password
                        </label>
                        <div class="pw-wrapper">
                            <input type="password" id="password" name="password" class="cyber-input pw-input" required placeholder="At least 6 characters">
                            <button type="button" class="pw-toggle" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="cyber-form-group">
                        <label class="cyber-label" for="confirm_password">
                            <i class="fas fa-lock"></i> Confirm Password
                        </label>
                        <div class="pw-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" class="cyber-input pw-input" required placeholder="Re-enter password">
                            <button type="button" class="pw-toggle" onclick="togglePassword('confirm_password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="reset_password" class="cyber-btn cyber-btn-primary" style="width: 100%; margin-top: 20px;">
                        <i class="fas fa-save"></i>
                        <span>Reset Password</span>
                    </button>
                </form>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 20px;">
                <a href="login.php" style="color: var(--cyber-cyan); text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

    <script>
        // OTP input handling
        <?php if ($step === 'otp'): ?>
            const otpInputs = document.querySelectorAll('.otp-digit');

            otpInputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    if (e.target.value) {
                        if (index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }
                    }
                    updateOTPValue();
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasteData = e.clipboardData.getData('text').slice(0, 6);
                    pasteData.split('').forEach((char, i) => {
                        if (otpInputs[i]) {
                            otpInputs[i].value = char;
                        }
                    });
                    updateOTPValue();
                    if (pasteData.length === 6) {
                        otpInputs[5].focus();
                    }
                });
            });

            function updateOTPValue() {
                const otp = Array.from(otpInputs).map(input => input.value).join('');
                document.getElementById('otpValue').value = otp;
            }

            document.getElementById('otpForm').addEventListener('submit', function(e) {
                updateOTPValue();
                if (document.getElementById('otpValue').value.length !== 6) {
                    e.preventDefault();
                    alert('Please enter the complete 6-digit OTP');
                }
            });

            // Auto-focus first input
            otpInputs[0].focus();
        <?php endif; ?>

        // Password toggle function
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
    </script>

    <?php include '../includes/theme-toggle.php'; ?>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body>

</html>
