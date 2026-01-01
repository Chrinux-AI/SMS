<?php
/**
 * Test Email Sending
 * Quick test to verify Gmail SMTP is working
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$testEmail = 'christolabiyi35@gmail.com';

echo "<h2>Testing Verdant SMS Email System</h2>";
echo "<p>Sending test email to: {$testEmail}</p>";

$subject = "🧪 Verdant SMS - Email Test";
$message = "
    <h2>Email Test Successful! 🎉</h2>
    <p>This confirms that your Verdant SMS email system is working correctly.</p>
    <div style='background: #00FF87; color: #0B0F19; padding: 20px; border-radius: 10px; text-align: center;'>
        <strong>Gmail SMTP: ACTIVE</strong><br>
        Time: " . date('Y-m-d H:i:s') . "
    </div>
    <p>You can now receive OTP verification codes and notifications.</p>
";

$result = send_email($testEmail, $subject, $message, 'Verdant SMS Test');

if ($result) {
    echo "<div style='background: #00FF87; color: #0B0F19; padding: 20px; border-radius: 10px; margin-top: 20px;'>";
    echo "<h3>✅ SUCCESS!</h3>";
    echo "<p>Email sent successfully. Check your inbox (and spam folder).</p>";
    echo "</div>";
} else {
    echo "<div style='background: #FF4757; color: white; padding: 20px; border-radius: 10px; margin-top: 20px;'>";
    echo "<h3>❌ FAILED</h3>";
    echo "<p>Email sending failed. Check PHP error logs for details.</p>";
    echo "</div>";
}

echo "<p><a href='index.php'>← Back to Homepage</a></p>";
