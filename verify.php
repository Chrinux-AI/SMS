<?php
/**
 * Email Verification Page
 * Handles OTP code entry and token-based verification
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/otp-helper.php';

$message = '';
$messageType = 'info';
$verified = false;
$showForm = true;

// Handle token-based verification (from email link)
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $result = verify_by_token($_GET['token']);
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
    $verified = $result['success'];
    $showForm = !$result['success'];
}

// Handle OTP form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {

        // Verify OTP code
        if ($_POST['action'] === 'verify' && isset($_POST['otp']) && isset($_POST['email'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $otp = preg_replace('/\D/', '', $_POST['otp']); // Numbers only

            if (strlen($otp) !== 6) {
                $message = 'Please enter a valid 6-digit code.';
                $messageType = 'error';
            } else {
                $result = verify_otp($otp, null, $email);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                $verified = $result['success'];
                $showForm = !$result['success'];
            }
        }

        // Resend OTP
        if ($_POST['action'] === 'resend' && isset($_POST['email'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $result = resend_otp($email);
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'error';
        }
    }
}

$pageTitle = "Verify Your Email";
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= APP_NAME ?></title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00D4FF;
            --success: #00FF87;
            --warning: #FFB800;
            --danger: #FF4757;
            --bg-dark: #0B0F19;
            --bg-card: #111827;
            --border: rgba(255,255,255,0.1);
            --text: #E5E7EB;
            --text-muted: #9CA3AF;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image:
                radial-gradient(circle at 20% 80%, rgba(0, 212, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 255, 135, 0.1) 0%, transparent 50%);
        }

        .verify-container {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 40px;
            max-width: 440px;
            width: 100%;
            text-align: center;
        }

        .logo {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(90deg, var(--primary), var(--success));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: left;
        }

        .alert.success {
            background: rgba(0, 255, 135, 0.15);
            border: 1px solid rgba(0, 255, 135, 0.3);
            color: var(--success);
        }

        .alert.error {
            background: rgba(255, 71, 87, 0.15);
            border: 1px solid rgba(255, 71, 87, 0.3);
            color: var(--danger);
        }

        .alert.info {
            background: rgba(0, 212, 255, 0.15);
            border: 1px solid rgba(0, 212, 255, 0.3);
            color: var(--primary);
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text);
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.2);
        }

        .otp-input {
            text-align: center;
            font-size: 2rem !important;
            letter-spacing: 12px;
            font-weight: 700;
        }

        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--success));
            color: #0B0F19;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
        }

        .btn-secondary {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
            margin-top: 15px;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.05);
        }

        .success-icon {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 20px;
        }

        .timer {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 20px;
        }

        .timer span {
            color: var(--warning);
            font-weight: 600;
        }

        .footer-links {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .footer-links a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <?php if ($verified): ?>
            <!-- Success State -->
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Email Verified!</h1>
            <p class="subtitle">Your account has been successfully verified.</p>

            <a href="login.php" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i>
                Continue to Login
            </a>

        <?php else: ?>
            <!-- Verification Form -->
            <div class="logo">🔐</div>
            <h1>Verify Your Email</h1>
            <p class="subtitle">Enter the 6-digit code sent to your email</p>

            <?php if ($message): ?>
                <div class="alert <?= $messageType ?>">
                    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : ($messageType === 'error' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($showForm): ?>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="verify">

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" id="email" placeholder="your@email.com" required
                               value="<?= htmlspecialchars($_POST['email'] ?? $_GET['email'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="otp"><i class="fas fa-key"></i> Verification Code</label>
                        <input type="text" name="otp" id="otp" class="otp-input"
                               placeholder="000000" maxlength="6" pattern="\d{6}" required
                               autocomplete="one-time-code" inputmode="numeric">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i>
                        Verify Code
                    </button>
                </form>

                <form method="POST" action="" style="display: inline;">
                    <input type="hidden" name="action" value="resend">
                    <input type="hidden" name="email" id="resend-email" value="">
                    <button type="submit" class="btn btn-secondary" onclick="document.getElementById('resend-email').value = document.getElementById('email').value;">
                        <i class="fas fa-redo"></i>
                        Resend Code
                    </button>
                </form>

                <p class="timer">
                    Code expires in <span id="countdown"><?= defined('OTP_EXPIRY_MINUTES') ? OTP_EXPIRY_MINUTES : 10 ?>:00</span>
                </p>
            <?php endif; ?>
        <?php endif; ?>

        <div class="footer-links">
            <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>

    <script>
        // Auto-focus OTP input
        document.getElementById('otp')?.focus();

        // Format OTP input (numbers only)
        document.getElementById('otp')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').substring(0, 6);
        });

        // Countdown timer
        let timeLeft = <?= (defined('OTP_EXPIRY_MINUTES') ? OTP_EXPIRY_MINUTES : 10) * 60 ?>;
        const countdown = document.getElementById('countdown');

        if (countdown) {
            const timer = setInterval(() => {
                timeLeft--;
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    countdown.textContent = 'Expired';
                    countdown.style.color = '#FF4757';
                } else {
                    const mins = Math.floor(timeLeft / 60);
                    const secs = timeLeft % 60;
                    countdown.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
                }
            }, 1000);
        }
    </script>
</body>
</html>
