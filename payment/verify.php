<?php
/**
 * Payment Verification Page
 * Verifies Flutterwave transaction after redirect
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';

$paymentConfig = require dirname(__DIR__) . '/config/payment.php';

$status = $_GET['status'] ?? '';
$txRef = $_GET['tx_ref'] ?? '';
$transactionId = $_GET['transaction_id'] ?? '';

$verified = false;
$message = '';

if ($status === 'successful' && $txRef) {
    // Verify with Flutterwave API
    $secretKey = $paymentConfig['flutterwave']['secret_key'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.flutterwave.com/v3/transactions/{$transactionId}/verify");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $secretKey,
        'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($result && $result['status'] === 'success' && $result['data']['status'] === 'successful') {
        $verified = true;
        $amount = $result['data']['amount'];
        $message = "Payment of ₦" . number_format($amount) . " received successfully!";

        // The webhook will handle the actual subscription upgrade
    } else {
        $message = "Payment verification failed. Please contact support.";
    }
} elseif ($status === 'cancelled') {
    $message = "Payment was cancelled.";
} else {
    $message = "Payment status unknown. Please contact support.";
}

$pageTitle = $verified ? "Payment Successful" : "Payment Status";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Verdant SMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #00D4FF; --success: #00FF87; --danger: #FF4757; --bg-dark: #0A0E17; --bg-card: #111827; --border: rgba(255,255,255,0.08); --text: #F3F4F6; --text-muted: #9CA3AF; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-dark); color: var(--text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .result-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 3rem; max-width: 450px; width: 100%; text-align: center; }
        .result-icon { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem; }
        .result-icon.success { background: rgba(0,255,135,0.15); color: var(--success); }
        .result-icon.failed { background: rgba(255,71,87,0.15); color: var(--danger); }
        h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .message { color: var(--text-muted); margin-bottom: 2rem; }
        .btn { display: inline-block; padding: 0.85rem 1.5rem; border-radius: 10px; text-decoration: none; font-weight: 600; }
        .btn-primary { background: linear-gradient(135deg, var(--success), var(--primary)); color: #000; }
        .btn-outline { border: 1px solid var(--border); color: var(--text); margin-left: 1rem; }
    </style>
</head>
<body>
    <div class="result-card">
        <div class="result-icon <?= $verified ? 'success' : 'failed' ?>">
            <i class="fas <?= $verified ? 'fa-check' : 'fa-times' ?>"></i>
        </div>
        <h1><?= $verified ? 'Payment Successful!' : 'Payment Issue' ?></h1>
        <p class="message"><?= htmlspecialchars($message) ?></p>

        <?php if ($verified): ?>
            <a href="../admin/index.php" class="btn btn-primary">Go to Dashboard</a>
        <?php else: ?>
            <a href="../visitor/pricing.php" class="btn btn-primary">Try Again</a>
            <a href="../visitor/contact.php" class="btn btn-outline">Contact Support</a>
        <?php endif; ?>
    </div>
</body>
</html>
