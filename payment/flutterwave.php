<?php
/**
 * Flutterwave Payment Handler
 * Processes payments for Verdant SMS subscriptions
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';

$paymentConfig = require dirname(__DIR__) . '/config/payment.php';

class FlutterwavePayment
{
    private $publicKey;
    private $secretKey;
    private $baseUrl;

    public function __construct($config)
    {
        $this->publicKey = $config['flutterwave']['public_key'];
        $this->secretKey = $config['flutterwave']['secret_key'];
        $this->baseUrl = $config['flutterwave']['environment'] === 'live'
            ? 'https://api.flutterwave.com/v3'
            : 'https://api.flutterwave.com/v3'; // Same URL, different keys
    }

    /**
     * Initiate a payment
     */
    public function initiatePayment($data)
    {
        $payload = [
            'tx_ref' => $data['tx_ref'],
            'amount' => $data['amount'],
            'currency' => 'NGN',
            'redirect_url' => $data['redirect_url'],
            'payment_options' => 'card, banktransfer, ussd',
            'customer' => [
                'email' => $data['email'],
                'name' => $data['name'],
                'phonenumber' => $data['phone'] ?? '',
            ],
            'customizations' => [
                'title' => 'Verdant SMS',
                'description' => $data['description'] ?? 'Subscription Payment',
                'logo' => 'https://verdantsms.com/assets/logo.png',
            ],
            'meta' => [
                'school_id' => $data['school_id'] ?? null,
                'plan' => $data['plan'] ?? null,
            ],
        ];

        $response = $this->makeRequest('/payments', 'POST', $payload);

        return $response;
    }

    /**
     * Verify a transaction
     */
    public function verifyTransaction($txRef)
    {
        $response = $this->makeRequest("/transactions/verify_by_reference?tx_ref={$txRef}", 'GET');

        return $response;
    }

    /**
     * Make API request to Flutterwave
     */
    private function makeRequest($endpoint, $method = 'GET', $data = null)
    {
        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return json_decode($response, true);
    }
}

// Handle form submission to initiate payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan = $_POST['plan'] ?? 'basic';
    $email = $_POST['email'] ?? '';
    $schoolName = $_POST['school_name'] ?? '';

    $plans = $paymentConfig['plans'];

    if (!isset($plans[$plan]) || $plans[$plan]['price'] === null || $plans[$plan]['price'] === 0) {
        header('Location: ../visitor/pricing.php?error=invalid_plan');
        exit;
    }

    $amount = $plans[$plan]['price'];
    $txRef = 'verdant_' . time() . '_' . bin2hex(random_bytes(4));

    $payment = new FlutterwavePayment($paymentConfig);

    $result = $payment->initiatePayment([
        'tx_ref' => $txRef,
        'amount' => $amount,
        'email' => $email,
        'name' => $schoolName,
        'description' => $plans[$plan]['name'] . ' Plan Subscription',
        'redirect_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/attendance/payment/verify.php',
        'plan' => $plan,
    ]);

    if ($result && isset($result['data']['link'])) {
        // Save transaction reference to session or database
        $_SESSION['pending_payment'] = [
            'tx_ref' => $txRef,
            'plan' => $plan,
            'amount' => $amount,
        ];

        // Redirect to Flutterwave checkout
        header('Location: ' . $result['data']['link']);
        exit;
    } else {
        header('Location: ../visitor/pricing.php?error=payment_failed');
        exit;
    }
}

// If GET request, show payment form
$pageTitle = "Complete Payment - Verdant SMS";
$plan = $_GET['plan'] ?? 'basic';
$plans = $paymentConfig['plans'];
$selectedPlan = $plans[$plan] ?? $plans['basic'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #00D4FF; --success: #00FF87; --bg-dark: #0A0E17; --bg-card: #111827; --border: rgba(255,255,255,0.08); --text: #F3F4F6; --text-muted: #9CA3AF; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-dark); color: var(--text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .payment-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 2.5rem; max-width: 450px; width: 100%; }
        .payment-card h1 { font-size: 1.5rem; margin-bottom: 0.5rem; text-align: center; }
        .payment-card .subtitle { color: var(--text-muted); text-align: center; margin-bottom: 2rem; }
        .plan-summary { background: var(--bg-dark); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; text-align: center; }
        .plan-name { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem; }
        .plan-price { font-size: 2.5rem; font-weight: 700; color: var(--success); }
        .plan-price span { font-size: 1rem; font-weight: 400; color: var(--text-muted); }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-muted); }
        .form-group input { width: 100%; background: var(--bg-dark); border: 1px solid var(--border); border-radius: 10px; padding: 0.85rem 1rem; color: var(--text); font-size: 0.95rem; }
        .form-group input:focus { outline: none; border-color: var(--primary); }
        .btn-pay { display: block; width: 100%; padding: 1rem; background: linear-gradient(135deg, var(--success), var(--primary)); border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; color: #000; cursor: pointer; }
        .secure-badge { display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-top: 1.5rem; color: var(--text-muted); font-size: 0.85rem; }
        .secure-badge i { color: var(--success); }
    </style>
</head>
<body>
    <div class="payment-card">
        <h1>Complete Your Payment</h1>
        <p class="subtitle">Powered by Flutterwave</p>

        <div class="plan-summary">
            <div class="plan-name"><?= htmlspecialchars($selectedPlan['name']) ?></div>
            <div class="plan-price">₦<?= number_format($selectedPlan['price']) ?> <span>/year</span></div>
        </div>

        <form method="POST">
            <input type="hidden" name="plan" value="<?= htmlspecialchars($plan) ?>">

            <div class="form-group">
                <label>School Name</label>
                <input type="text" name="school_name" required placeholder="Your School Name">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="admin@yourschool.com">
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" placeholder="+234 800 000 0000">
            </div>

            <button type="submit" class="btn-pay">
                <i class="fas fa-lock"></i> Pay ₦<?= number_format($selectedPlan['price']) ?>
            </button>
        </form>

        <div class="secure-badge">
            <i class="fas fa-shield-alt"></i> Secure payment via Flutterwave
        </div>
    </div>
</body>
</html>
