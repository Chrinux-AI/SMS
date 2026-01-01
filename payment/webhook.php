<?php
/**
 * Flutterwave Webhook Handler
 * Receives and processes payment notifications
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';

$paymentConfig = require dirname(__DIR__) . '/config/payment.php';

// Log webhook for debugging
$payload = file_get_contents('php://input');
$webhookLog = dirname(__DIR__) . '/logs/webhook_' . date('Y-m-d') . '.log';
file_put_contents($webhookLog, date('Y-m-d H:i:s') . " - " . $payload . "\n", FILE_APPEND);

// Verify webhook signature if secret is set
$signature = $_SERVER['HTTP_VERIF_HASH'] ?? '';
$secretHash = $paymentConfig['flutterwave']['webhook_secret'];

if ($secretHash && $signature !== $secretHash) {
    http_response_code(401);
    exit('Invalid signature');
}

// Parse the webhook payload
$data = json_decode($payload, true);

if (!$data || !isset($data['event'])) {
    http_response_code(400);
    exit('Invalid payload');
}

// Handle different event types
switch ($data['event']) {
    case 'charge.completed':
        handleSuccessfulPayment($data['data']);
        break;

    case 'charge.failed':
        handleFailedPayment($data['data']);
        break;

    default:
        // Log unknown event
        file_put_contents($webhookLog, "Unknown event: " . $data['event'] . "\n", FILE_APPEND);
}

http_response_code(200);
echo 'OK';

/**
 * Handle successful payment
 */
function handleSuccessfulPayment($transaction)
{
    $db = Database::getInstance();

    $txRef = $transaction['tx_ref'] ?? '';
    $amount = $transaction['amount'] ?? 0;
    $status = $transaction['status'] ?? '';
    $meta = $transaction['meta'] ?? [];

    if ($status !== 'successful') {
        return;
    }

    // Extract plan and school info from meta
    $plan = $meta['plan'] ?? 'basic';
    $schoolId = $meta['school_id'] ?? null;

    // Save payment record
    $db->insert('payments', [
        'tx_ref' => $txRef,
        'amount' => $amount,
        'currency' => 'NGN',
        'status' => 'completed',
        'plan' => $plan,
        'school_id' => $schoolId,
        'payment_method' => 'flutterwave',
        'created_at' => date('Y-m-d H:i:s'),
    ]);

    // Upgrade school plan if school_id exists
    if ($schoolId) {
        $db->update('schools', [
            'subscription_plan' => $plan,
            'subscription_status' => 'active',
            'subscription_expires' => date('Y-m-d H:i:s', strtotime('+1 year')),
        ], 'id = ?', [$schoolId]);
    }

    // Send confirmation email (placeholder)
    // sendPaymentConfirmation($transaction);
}

/**
 * Handle failed payment
 */
function handleFailedPayment($transaction)
{
    $db = Database::getInstance();

    $txRef = $transaction['tx_ref'] ?? '';
    $amount = $transaction['amount'] ?? 0;

    // Save failed payment record
    $db->insert('payments', [
        'tx_ref' => $txRef,
        'amount' => $amount,
        'currency' => 'NGN',
        'status' => 'failed',
        'payment_method' => 'flutterwave',
        'created_at' => date('Y-m-d H:i:s'),
    ]);
}
