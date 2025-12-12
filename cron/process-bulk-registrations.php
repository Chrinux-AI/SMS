<?php

/**
 * Cron Job: Process Bulk Registrations
 * Run daily to process expired registration windows
 *
 * Add to crontab:
 * 0 0 * * * /opt/lampp/bin/php /opt/lampp/htdocs/attendance/cron/process-bulk-registrations.php
 *
 * Verdant SMS v3.0
 */

// Prevent web access
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

echo "=== Bulk Registration Processor ===" . PHP_EOL;
echo "Started at: " . date('Y-m-d H:i:s') . PHP_EOL;

// Find configs where end_date has passed and status is still pending
$configs = db()->fetchAll("
    SELECT * FROM bulk_registration_config
    WHERE status = 'pending'
    AND end_date < NOW()
");

if (empty($configs)) {
    echo "No pending registration windows to process." . PHP_EOL;
    exit(0);
}

echo "Found " . count($configs) . " registration window(s) to process." . PHP_EOL;

foreach ($configs as $config) {
    echo PHP_EOL . "Processing: {$config['name']} (ID: {$config['id']})" . PHP_EOL;

    // Update status to processing
    db()->query("UPDATE bulk_registration_config SET status = 'processing' WHERE id = ?", [$config['id']]);

    // Get pending records
    $records = db()->fetchAll("
        SELECT * FROM bulk_registration_records
        WHERE config_id = ? AND status = 'pending'
    ", [$config['id']]);

    echo "  Records to process: " . count($records) . PHP_EOL;

    $processed = 0;
    $failed = 0;
    $errors = [];

    foreach ($records as $record) {
        // Check for duplicate email
        $existing = db()->fetch("SELECT id FROM users WHERE email = ?", [$record['email']]);

        if ($existing) {
            db()->query("
                UPDATE bulk_registration_records
                SET status = 'duplicate', error_message = 'Email already exists', processed_at = NOW()
                WHERE id = ?
            ", [$record['id']]);
            $failed++;
            $errors[] = "Duplicate: {$record['email']}";
            continue;
        }

        // Validate email format
        if (!filter_var($record['email'], FILTER_VALIDATE_EMAIL)) {
            db()->query("
                UPDATE bulk_registration_records
                SET status = 'failed', error_message = 'Invalid email format', processed_at = NOW()
                WHERE id = ?
            ", [$record['id']]);
            $failed++;
            $errors[] = "Invalid email: {$record['email']}";
            continue;
        }

        // Generate secure password
        $password = generate_password();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otp_expires = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));

        try {
            // Create user
            $user_id = db()->insert('users', [
                'name' => $record['full_name'],
                'email' => $record['email'],
                'phone' => $record['phone'],
                'password' => $hashed_password,
                'role' => $record['role'],
                'status' => 'active',
                'otp_code' => $otp,
                'otp_expires_at' => $otp_expires,
                'email_verified_at' => null
            ]);

            // Send welcome email with credentials and OTP
            $email_sent = send_bulk_welcome_email(
                $record['email'],
                $record['full_name'],
                $password,
                $record['role'],
                $otp
            );

            // Update record status
            db()->query("
                UPDATE bulk_registration_records
                SET status = 'processed', user_id = ?, processed_at = NOW()
                WHERE id = ?
            ", [$user_id, $record['id']]);

            // If parent, create parent record and link children
            if ($record['role'] === 'parent') {
                create_parent_record($user_id, $record);
            }

            $processed++;
            echo "  ✓ Created: {$record['full_name']} ({$record['email']})" . PHP_EOL;
        } catch (Exception $e) {
            db()->query("
                UPDATE bulk_registration_records
                SET status = 'failed', error_message = ?, processed_at = NOW()
                WHERE id = ?
            ", [$e->getMessage(), $record['id']]);
            $failed++;
            $errors[] = "Error for {$record['email']}: {$e->getMessage()}";
            echo "  ✗ Failed: {$record['full_name']} - {$e->getMessage()}" . PHP_EOL;
        }
    }

    // Update config status
    $error_log = !empty($errors) ? implode("\n", $errors) : null;
    db()->query("
        UPDATE bulk_registration_config
        SET status = 'completed',
            processed_records = ?,
            failed_records = ?,
            error_log = ?,
            processed_at = NOW()
        WHERE id = ?
    ", [$processed, $failed, $error_log, $config['id']]);

    echo "  Completed: $processed processed, $failed failed" . PHP_EOL;

    // Notify admin
    notify_admin_bulk_complete($config, $processed, $failed);
}

echo PHP_EOL . "=== Processing Complete ===" . PHP_EOL;
echo "Finished at: " . date('Y-m-d H:i:s') . PHP_EOL;

/**
 * Generate a secure random password
 */
function generate_password($length = 12)
{
    $lower = 'abcdefghijklmnopqrstuvwxyz';
    $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '0123456789';
    $special = '!@#$%&*';

    $password = '';
    $password .= $lower[random_int(0, strlen($lower) - 1)];
    $password .= $upper[random_int(0, strlen($upper) - 1)];
    $password .= $numbers[random_int(0, strlen($numbers) - 1)];
    $password .= $special[random_int(0, strlen($special) - 1)];

    $all = $lower . $upper . $numbers . $special;
    for ($i = 4; $i < $length; $i++) {
        $password .= $all[random_int(0, strlen($all) - 1)];
    }

    return str_shuffle($password);
}

/**
 * Send welcome email for bulk registration
 */
function send_bulk_welcome_email($email, $name, $password, $role, $otp)
{
    $subject = "Welcome to Verdant SMS - Your Account Details";

    $message = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: linear-gradient(135deg, #00ffff, #ff00ff); padding: 20px; text-align: center;'>
            <h1 style='color: #000; margin: 0;'>Welcome to Verdant SMS!</h1>
        </div>

        <div style='padding: 30px; background: #1a1a1a; color: #fff;'>
            <p style='font-size: 16px;'>Dear <strong>$name</strong>,</p>

            <p>Your account has been created on the Verdant School Management System.</p>

            <div style='background: #0a0a0a; border: 1px solid #00ffff; border-radius: 10px; padding: 20px; margin: 20px 0;'>
                <h3 style='color: #00ffff; margin-top: 0;'>Your Login Credentials</h3>
                <table style='width: 100%; color: #fff;'>
                    <tr>
                        <td style='padding: 8px 0;'><strong>Email:</strong></td>
                        <td style='padding: 8px 0;'>$email</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0;'><strong>Password:</strong></td>
                        <td style='padding: 8px 0; font-family: monospace; background: #222; padding: 5px;'>$password</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0;'><strong>Role:</strong></td>
                        <td style='padding: 8px 0;'>" . ucfirst($role) . "</td>
                    </tr>
                </table>
            </div>

            <div style='background: #1a0a1a; border: 1px solid #ff00ff; border-radius: 10px; padding: 20px; margin: 20px 0;'>
                <h3 style='color: #ff00ff; margin-top: 0;'>Email Verification OTP</h3>
                <p>Use this code to verify your email address:</p>
                <div style='font-size: 32px; font-family: monospace; text-align: center; letter-spacing: 10px; color: #00ffff; padding: 10px;'>
                    $otp
                </div>
                <p style='font-size: 12px; color: #888;'>This code expires in " . OTP_EXPIRY_MINUTES . " minutes.</p>
            </div>

            <div style='text-align: center; margin: 30px 0;'>
                <a href='" . APP_URL . "/login.php' style='display: inline-block; background: #00ffff; color: #000; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                    Login Now
                </a>
            </div>

            <p style='color: #ff6666;'><strong>Important:</strong> Please change your password after your first login for security.</p>
        </div>

        <div style='background: #0a0a0a; padding: 15px; text-align: center; color: #666; font-size: 12px;'>
            <p>© " . date('Y') . " Verdant School Management System</p>
            <p>Contact: " . CONTACT_EMAIL . " | " . CONTACT_PHONE . "</p>
        </div>
    </div>
    ";

    return send_email($email, $subject, $message);
}

/**
 * Create parent record and link to children
 */
function create_parent_record($user_id, $record)
{
    // Check if parents table exists and create parent profile
    try {
        // Insert into parents table if it exists
        $parent_id = db()->insert('parents', [
            'user_id' => $user_id,
            'relationship' => $record['relationship'] ?? 'Guardian'
        ]);

        // Link children if specified
        $children = [];
        if (!empty($record['child1_name'])) {
            $children[] = ['name' => $record['child1_name'], 'class' => $record['child1_class']];
        }
        if (!empty($record['child2_name'])) {
            $children[] = ['name' => $record['child2_name'], 'class' => $record['child2_class']];
        }
        if (!empty($record['child3_name'])) {
            $children[] = ['name' => $record['child3_name'], 'class' => $record['child3_class']];
        }

        // Store children info in parent_children or notes
        // This depends on your actual schema
        foreach ($children as $child) {
            // Try to find existing student
            $student = db()->fetch("
                SELECT id FROM students WHERE name LIKE ?
            ", ['%' . $child['name'] . '%']);

            if ($student) {
                // Link parent to student
                db()->query("
                    INSERT IGNORE INTO parent_student (parent_id, student_id)
                    VALUES (?, ?)
                ", [$parent_id, $student['id']]);
            }
        }
    } catch (Exception $e) {
        // Parents table may not exist, log but continue
        error_log("Parent record creation failed: " . $e->getMessage());
    }
}

/**
 * Notify admin when bulk processing is complete
 */
function notify_admin_bulk_complete($config, $processed, $failed)
{
    $admin = db()->fetch("SELECT email, name FROM users WHERE role = 'admin' LIMIT 1");

    if (!$admin) return;

    $subject = "Bulk Registration Complete: {$config['name']}";

    $message = "
    <h2>Bulk Registration Processing Complete</h2>
    <p>The registration window <strong>{$config['name']}</strong> has been processed.</p>

    <table style='border-collapse: collapse; margin: 20px 0;'>
        <tr>
            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Total Processed:</strong></td>
            <td style='padding: 8px; border: 1px solid #ccc;'>$processed</td>
        </tr>
        <tr>
            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Failed:</strong></td>
            <td style='padding: 8px; border: 1px solid #ccc;'>$failed</td>
        </tr>
        <tr>
            <td style='padding: 8px; border: 1px solid #ccc;'><strong>Processed At:</strong></td>
            <td style='padding: 8px; border: 1px solid #ccc;'>" . date('d/m/Y H:i:s') . "</td>
        </tr>
    </table>

    <p>View details in the <a href='" . APP_URL . "/admin/bulk-registration.php'>Admin Panel</a>.</p>
    ";

    send_email($admin['email'], $subject, $message);
}
