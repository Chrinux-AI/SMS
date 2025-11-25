<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';

$message = '';
$message_type = '';

if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    // Find user with this token
    $user = db()->fetchOne("SELECT * FROM users WHERE email_verification_token = ?", [$token]);

    if ($user) {
        // Check if token has expired (10 minutes)
        if ($user['token_expires_at'] && strtotime($user['token_expires_at']) < time()) {
            $message = 'Verification link has expired. Please request a new verification email.';
            $message_type = 'error';
        } elseif ($user['email_verified'] == 1) {
            $message = 'Your email has already been verified. Please wait for admin approval.';
            $message_type = 'info';
        } else {
            // Verify the email
            $updated = db()->update(
                'users',
                ['email_verified' => 1, 'email_verification_token' => null, 'token_expires_at' => null],
                'id = ?',
                [$user['id']]
            );

            if ($updated) {
                $message = 'Email verified successfully! Your account is now pending admin approval. You will receive an email once approved.';
                $message_type = 'success';
            } else {
                $message = 'Failed to verify email. Please try again or contact support.';
                $message_type = 'error';
            }
        }
    } else {
        $message = 'Invalid or expired verification token.';
        $message_type = 'error';
    }
} else {
    $message = 'No verification token provided.';
    $message_type = 'error';
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
    <title>Email Verification - <?php echo APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/css/cyberpunk-ui.css" rel="stylesheet">
    <style>
        .verify-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .verify-card {
            max-width: 600px;
            width: 100%;
            background: rgba(10, 10, 10, 0.9);
            border: 1px solid rgba(0, 191, 255, 0.3);
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 0 50px rgba(0, 191, 255, 0.2);
            text-align: center;
        }

        .verify-icon {
            font-size: 5rem;
            margin-bottom: 30px;
        }

        .verify-icon.success {
            color: var(--neon-green);
        }

        .verify-icon.error {
            color: var(--cyber-red);
        }

        .verify-icon.info {
            color: var(--cyber-cyan);
        }

        h1 {
            color: var(--cyber-cyan);
            font-family: Orbitron;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        p {
            color: var(--text-primary);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .cyber-btn {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, var(--cyber-cyan), var(--hologram-purple));
            border: none;
            border-radius: 10px;
            color: white;
            font-family: Orbitron;
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
        }

        .cyber-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 30px rgba(0, 191, 255, 0.5);
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
    <div class="verify-wrapper">
        <div class="verify-card">
            <?php if ($message_type === 'success'): ?>
                <div class="verify-icon success"><i class="fas fa-check-circle"></i></div>
            <?php elseif ($message_type === 'error'): ?>
                <div class="verify-icon error"><i class="fas fa-times-circle"></i></div>
            <?php else: ?>
                <div class="verify-icon info"><i class="fas fa-info-circle"></i></div>
            <?php endif; ?>

            <h1><?php echo $message_type === 'success' ? 'Verification Complete' : ($message_type === 'error' ? 'Verification Failed' : 'Email Status'); ?></h1>
            <p><?php echo $message; ?></p>
            <a href="login.php" class="cyber-btn"><i class="fas fa-sign-in-alt"></i> Go to Login</a>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body>

</html>