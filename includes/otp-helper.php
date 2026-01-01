<?php
/**
 * OTP (One-Time Password) Management Functions
 * Handles generation, storage, email sending, and verification of OTPs
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/functions.php';

/**
 * Generate a 6-digit OTP
 * @return string 6-digit OTP code
 */
function generate_otp(): string {
    return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Generate a secure verification token
 * @return string 64-character hex token
 */
function generate_verification_token(): string {
    return bin2hex(random_bytes(32));
}

/**
 * Create and store OTP for a user
 * @param int $userId User ID
 * @param string $email User email
 * @return array ['otp' => string, 'token' => string, 'expires_at' => string]
 */
function create_otp(int $userId, string $email): array {
    $db = Database::getInstance();

    // Invalidate any existing OTPs for this user
    $db->execute(
        "UPDATE user_otps SET used = 1 WHERE user_id = ? AND used = 0",
        [$userId]
    );

    // Generate new OTP and token
    $otp = generate_otp();
    $token = generate_verification_token();
    $expiryMinutes = defined('OTP_EXPIRY_MINUTES') ? OTP_EXPIRY_MINUTES : 10;
    $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryMinutes} minutes"));

    // Store in database
    $db->insert('user_otps', [
        'user_id' => $userId,
        'email' => $email,
        'otp_code' => password_hash($otp, PASSWORD_DEFAULT), // Hash for security
        'token' => $token,
        'expires_at' => $expiresAt,
        'created_at' => date('Y-m-d H:i:s'),
        'used' => 0
    ]);

    return [
        'otp' => $otp,
        'token' => $token,
        'expires_at' => $expiresAt
    ];
}

/**
 * Send OTP verification email
 * @param string $email Recipient email
 * @param string $name User's name
 * @param string $otp The OTP code
 * @param string $token Verification token for link
 * @return bool Success status
 */
function send_otp_email(string $email, string $name, string $otp, string $token): bool {
    $appUrl = defined('APP_URL') ? APP_URL : 'http://localhost/attendance';
    $verifyLink = $appUrl . '/verify.php?token=' . urlencode($token);
    $expiryMinutes = defined('OTP_EXPIRY_MINUTES') ? OTP_EXPIRY_MINUTES : 10;

    $subject = "🔐 Your Verdant SMS Verification Code";

    $message = "
        <div style='text-align: center; margin-bottom: 30px;'>
            <h2 style='color: #00FF87;'>🌿 Verdant SMS</h2>
            <p style='color: #9CA3AF;'>Email Verification</p>
        </div>

        <h3>Hello {$name}!</h3>

        <p>Your one-time verification code is:</p>

        <div style='background: linear-gradient(135deg, #00D4FF 0%, #00FF87 100%); padding: 25px; border-radius: 12px; text-align: center; margin: 20px 0;'>
            <span style='font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #0B0F19;'>{$otp}</span>
        </div>

        <p style='color: #FF4757;'><strong>⏰ This code expires in {$expiryMinutes} minutes.</strong></p>

        <p>Or click the button below to verify directly:</p>

        <div style='text-align: center; margin: 25px 0;'>
            <a href='{$verifyLink}' style='display: inline-block; padding: 15px 40px; background: #00D4FF; color: #0B0F19; text-decoration: none; border-radius: 8px; font-weight: bold;'>
                ✓ Verify My Account
            </a>
        </div>

        <p style='color: #9CA3AF; font-size: 14px;'>If you didn't request this code, please ignore this email.</p>

        <hr style='border: 1px solid #374151; margin: 30px 0;'>

        <p style='color: #9CA3AF; font-size: 12px; text-align: center;'>
            Verdant SMS — Nigeria's AI-Powered School Management System<br>
            © " . date('Y') . " Chrinux-AI. All rights reserved.
        </p>
    ";

    return send_email($email, $subject, $message, 'Verdant SMS');
}

/**
 * Verify OTP code
 * @param string $code The OTP code entered by user
 * @param int $userId User ID (optional, uses token if not provided)
 * @param string $email User email (optional, uses token if not provided)
 * @return array ['success' => bool, 'message' => string, 'user_id' => int|null]
 */
function verify_otp(string $code, ?int $userId = null, ?string $email = null): array {
    $db = Database::getInstance();

    // Build query based on what we have
    if ($userId) {
        $otp = $db->fetchOne(
            "SELECT * FROM user_otps WHERE user_id = ? AND used = 0 AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1",
            [$userId]
        );
    } elseif ($email) {
        $otp = $db->fetchOne(
            "SELECT * FROM user_otps WHERE email = ? AND used = 0 AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1",
            [$email]
        );
    } else {
        return ['success' => false, 'message' => 'User identification required', 'user_id' => null];
    }

    if (!$otp) {
        return ['success' => false, 'message' => 'OTP expired or not found. Please request a new code.', 'user_id' => null];
    }

    // Verify the OTP code (hashed in database)
    if (!password_verify($code, $otp['otp_code'])) {
        return ['success' => false, 'message' => 'Invalid OTP code. Please try again.', 'user_id' => null];
    }

    // Mark OTP as used
    $db->execute("UPDATE user_otps SET used = 1 WHERE id = ?", [$otp['id']]);

    // Mark user as verified
    $db->execute("UPDATE users SET email_verified = 1, email_verified_at = NOW() WHERE id = ?", [$otp['user_id']]);

    return ['success' => true, 'message' => 'Email verified successfully!', 'user_id' => (int)$otp['user_id']];
}

/**
 * Verify using token link
 * @param string $token Verification token from URL
 * @return array ['success' => bool, 'message' => string, 'user_id' => int|null]
 */
function verify_by_token(string $token): array {
    $db = Database::getInstance();

    $otp = $db->fetchOne(
        "SELECT * FROM user_otps WHERE token = ? AND used = 0 AND expires_at > NOW() LIMIT 1",
        [$token]
    );

    if (!$otp) {
        return ['success' => false, 'message' => 'Verification link expired or invalid. Please request a new code.', 'user_id' => null];
    }

    // Mark OTP as used
    $db->execute("UPDATE user_otps SET used = 1 WHERE id = ?", [$otp['id']]);

    // Mark user as verified
    $db->execute("UPDATE users SET email_verified = 1, email_verified_at = NOW() WHERE id = ?", [$otp['user_id']]);

    return ['success' => true, 'message' => 'Email verified successfully! You can now log in.', 'user_id' => (int)$otp['user_id']];
}

/**
 * Resend OTP to user
 * @param string $email User email
 * @return array ['success' => bool, 'message' => string]
 */
function resend_otp(string $email): array {
    $db = Database::getInstance();

    // Find user by email
    $user = $db->fetchOne("SELECT id, full_name, email_verified FROM users WHERE email = ?", [$email]);

    if (!$user) {
        return ['success' => false, 'message' => 'Email not found in our system.'];
    }

    if ($user['email_verified']) {
        return ['success' => false, 'message' => 'Email is already verified. You can log in.'];
    }

    // Check rate limit (max 3 OTPs per hour)
    $recentCount = $db->fetchColumn(
        "SELECT COUNT(*) FROM user_otps WHERE user_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
        [$user['id']]
    );

    if ($recentCount >= 3) {
        return ['success' => false, 'message' => 'Too many OTP requests. Please wait an hour before requesting again.'];
    }

    // Create and send new OTP
    $otpData = create_otp($user['id'], $email);
    $sent = send_otp_email($email, $user['full_name'], $otpData['otp'], $otpData['token']);

    if ($sent) {
        return ['success' => true, 'message' => 'A new verification code has been sent to your email.'];
    }

    return ['success' => false, 'message' => 'Failed to send email. Please try again later.'];
}

/**
 * Clean up expired OTPs (can be called periodically)
 * @return int Number of expired OTPs deleted
 */
function cleanup_expired_otps(): int {
    $db = Database::getInstance();
    $result = $db->execute("DELETE FROM user_otps WHERE expires_at < NOW() OR used = 1");
    return $result ? $result->rowCount() : 0;
}
