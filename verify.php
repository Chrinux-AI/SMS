<?php

/**
 * VERDANT SMS v3.0 — EMAIL + OTP VERIFICATION
 * Verifies user email via token or 6-digit OTP
 */

session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$success = '';
$error = '';
$showOtpForm = false;
$email = '';

// Handle token verification (from email link)
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    $user = db()->fetchOne("SELECT id, email, email_verified FROM users WHERE verification_token = ?", [$token]);

    if ($user) {
        if ($user['email_verified']) {
            $success = 'Your email is already verified. You can now login.';
        } else {
            db()->query("UPDATE users SET email_verified = 1, email_verified_at = NOW(), verification_token = NULL WHERE id = ?", [$user['id']]);
            $success = 'Email verified successfully! You can now login.';
        }
    } else {
        $error = 'Invalid or expired verification link.';
    }
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'verify_otp') {
        $email = trim($_POST['email'] ?? '');
        $otp = trim($_POST['otp'] ?? '');

        if (empty($email) || empty($otp)) {
            $error = 'Email and OTP are required.';
            $showOtpForm = true;
        } else {
            $user = db()->fetchOne("
                SELECT id, otp_code, otp_expires_at, email_verified
                FROM users
                WHERE email = ? AND otp_code = ?
            ", [$email, $otp]);

            if (!$user) {
                $error = 'Invalid OTP code.';
                $showOtpForm = true;
            } elseif ($user['email_verified']) {
                $success = 'Your email is already verified.';
            } elseif (strtotime($user['otp_expires_at']) < time()) {
                $error = 'OTP has expired. Please request a new one.';
                $showOtpForm = true;
            } else {
                db()->query("UPDATE users SET email_verified = 1, email_verified_at = NOW(), otp_code = NULL, otp_expires_at = NULL WHERE id = ?", [$user['id']]);
                $success = 'Email verified successfully! You can now login.';
            }
        }
    } elseif ($action === 'resend_otp') {
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $error = 'Email is required.';
        } else {
            $user = db()->fetchOne("SELECT id, first_name, email_verified FROM users WHERE email = ?", [$email]);

            if (!$user) {
                $error = 'No account found with this email.';
            } elseif ($user['email_verified']) {
                $success = 'Your email is already verified.';
            } else {
                // Generate new OTP
                $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                db()->query("UPDATE users SET otp_code = ?, otp_expires_at = ? WHERE id = ?", [$otp, $expires, $user['id']]);

                // Send OTP email
                send_email(
                    $email,
                    'Your Verdant SMS Verification Code',
                    "Hello " . ($user['first_name'] ?? 'User') . ",\n\n" .
                        "Your verification code is: $otp\n\n" .
                        "This code expires in 15 minutes.\n\n" .
                        "If you didn't request this, please ignore this email."
                );

                $success = 'A new OTP has been sent to your email.';
                $showOtpForm = true;
            }
        }
    } elseif ($action === 'show_otp') {
        $email = trim($_POST['email'] ?? '');
        $showOtpForm = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — Verdant SMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <style>
        :root {
            --neon-green: #00ff88;
            --neon-blue: #00d4ff;
            --dark-bg: #0a0a0f;
            --card-bg: rgba(15, 25, 35, 0.95);
            --text-primary: #e0e6ed;
            --text-muted: #8892a0;
            --success: #00ff88;
            --error: #ff4757;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--dark-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
        }

        .cyber-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                linear-gradient(90deg, rgba(0, 255, 136, 0.03) 1px, transparent 1px),
                linear-gradient(rgba(0, 255, 136, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: -1;
        }

        .container {
            max-width: 450px;
            width: 90%;
            padding: 20px;
        }

        .verify-card {
            background: var(--card-bg);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 0 40px rgba(0, 255, 136, 0.1);
        }

        .verify-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .verify-icon.success {
            color: var(--success);
        }

        .verify-icon.error {
            color: var(--error);
        }

        .verify-icon.pending {
            color: var(--neon-blue);
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            background: linear-gradient(135deg, var(--neon-green), var(--neon-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            color: var(--text-muted);
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: rgba(0, 255, 136, 0.15);
            border: 1px solid var(--success);
            color: var(--success);
        }

        .alert-error {
            background: rgba(255, 71, 87, 0.15);
            border: 1px solid var(--error);
            color: var(--error);
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(0, 255, 136, 0.2);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--neon-green);
        }

        .otp-input {
            text-align: center;
            font-size: 2rem;
            letter-spacing: 10px;
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            padding: 14px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--neon-green), #00cc6a);
            color: #000;
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 255, 136, 0.4);
        }

        .btn-secondary {
            background: transparent;
            border: 1px solid var(--neon-blue);
            color: var(--neon-blue);
            margin-top: 15px;
        }

        .links {
            margin-top: 25px;
        }

        .links a {
            color: var(--neon-green);
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="cyber-grid"></div>

    <div class="container">
        <div class="verify-card">
            <?php if ($success): ?>
                <div class="verify-icon success"><i class="fas fa-check-circle"></i></div>
                <h1>Verified!</h1>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <a href="login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login Now
                </a>
            <?php elseif ($error && !$showOtpForm): ?>
                <div class="verify-icon error"><i class="fas fa-times-circle"></i></div>
                <h1>Verification Failed</h1>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <form method="POST">
                    <input type="hidden" name="action" value="show_otp">
                    <div class="form-group">
                        <label>Enter your email to verify with OTP</label>
                        <input type="email" name="email" placeholder="your@email.com" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Verify with OTP
                    </button>
                </form>
            <?php elseif ($showOtpForm): ?>
                <div class="verify-icon pending"><i class="fas fa-envelope-open-text"></i></div>
                <h1>Enter OTP</h1>
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <p>Enter the 6-digit code sent to your email</p>
                <form method="POST">
                    <input type="hidden" name="action" value="verify_otp">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                    <div class="form-group">
                        <input type="text" name="otp" class="otp-input" maxlength="6" placeholder="000000" pattern="[0-9]{6}" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Verify OTP
                    </button>
                </form>
                <form method="POST" style="margin-top: 15px;">
                    <input type="hidden" name="action" value="resend_otp">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Resend OTP
                    </button>
                </form>
            <?php else: ?>
                <div class="verify-icon pending"><i class="fas fa-envelope"></i></div>
                <h1>Verify Your Email</h1>
                <p>Enter your email address to receive a verification code.</p>
                <form method="POST">
                    <input type="hidden" name="action" value="resend_otp">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="your@email.com" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Verification Code
                    </button>
                </form>
            <?php endif; ?>

            <div class="links">
                <a href="login.php">← Back to Login</a>
            </div>
        </div>
    </div>
</body>

</html>