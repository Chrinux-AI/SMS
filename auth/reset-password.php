<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

$message = '';
$message_type = '';
$token = $_GET['token'] ?? '';

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = sanitize($_POST['token']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = 'Passwords do not match';
        $message_type = 'error';
    } elseif (strlen($new_password) < 8) {
        $message = 'Password must be at least 8 characters';
        $message_type = 'error';
    } else {
        // Verify token
        $user = db()->fetchOne("
            SELECT id, email FROM users
            WHERE verification_token = ? AND token_expiry > NOW()
        ", [$token]);

        if ($user) {
            // Update password and clear token
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            db()->update('users', [
                'password' => $hashed_password,
                'verification_token' => null,
                'token_expiry' => null
            ], 'id = ?', [$user['id']]);

            $message = 'Password reset successful! You can now login.';
            $message_type = 'success';

            // Redirect to login after 2 seconds
            header("refresh:2;url=login.php");
        } else {
            $message = 'Invalid or expired reset token';
            $message_type = 'error';
        }
    }
}

// Verify token exists for GET request
if (!empty($token) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $user = db()->fetchOne("
        SELECT id FROM users
        WHERE verification_token = ? AND token_expiry > NOW()
    ", [$token]);

    if (!$user) {
        $message = 'Invalid or expired reset link';
        $message_type = 'error';
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
    <title>Reset Password - <?php echo APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/css/cyberpunk-ui.css" rel="stylesheet">
</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>
<div class="cyber-bg">
        <div class="starfield"></div>
    </div>
    <div class="cyber-grid"></div>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="brand-orb"><i class="fas fa-key"></i></div>
                <h1 class="brand-title">Reset Password</h1>
                <p class="brand-subtitle">Enter your new password</p>
            </div>

            <?php if ($message): ?>
                <div class="cyber-alert <?php echo $message_type === 'success' ? 'success' : 'error'; ?>">
                    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                    <span><?php echo $message; ?></span>
                </div>
            <?php endif; ?>

            <?php if (empty($message) || $message_type !== 'success'): ?>
                <form method="POST" action="" class="auth-form">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> New Password
                        </label>
                        <input type="password" name="new_password" required
                            minlength="8" placeholder="Enter new password"
                            class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> Confirm Password
                        </label>
                        <input type="password" name="confirm_password" required
                            minlength="8" placeholder="Confirm new password"
                            class="form-input">
                    </div>

                    <button type="submit" class="cyber-btn primary full-width">
                        <i class="fas fa-check"></i> Reset Password
                    </button>

                    <div class="auth-links">
                        <a href="login.php">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body>

</html>