<?php

/**
 * Create Default Admin Account
 * This script creates a default admin account and clears all existing users
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/email-helper.php';

echo "Creating Default Admin Account...\n\n";

try {
    // Disable foreign key checks
    db()->query("SET FOREIGN_KEY_CHECKS = 0");

    echo "Step 1: Clearing existing users...\n";

    // Clear all user-related tables
    $tables = [
        'attendance_records',
        'class_enrollments',
        'classes',
        'messages',
        'notifications',
        'parents',
        'students',
        'teachers',
        'users'
    ];

    foreach ($tables as $table) {
        try {
            db()->query("TRUNCATE TABLE $table");
            echo "  ✓ Cleared table: $table\n";
        } catch (Exception $e) {
            // If TRUNCATE fails, try DELETE
            db()->query("DELETE FROM $table");
            echo "  ✓ Deleted from table: $table\n";
        }
    }

    // Re-enable foreign key checks
    db()->query("SET FOREIGN_KEY_CHECKS = 1");

    echo "\nStep 2: Creating admin account...\n";

    // Default admin credentials
    $email = 'christolabiyi35@gmail.com';
    $password = 'Finekit@1410';
    $first_name = 'Christopher';
    $last_name = 'Olabiyi';
    $username = 'christopherolabiyi';

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert admin account
    $sql = "INSERT INTO users (username, first_name, last_name, email, password_hash, role, status, email_verified, approved, created_at)
            VALUES (:username, :first_name, :last_name, :email, :password_hash, :role, :status, :email_verified, :approved, :created_at)";

    $params = [
        'username' => $username,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'password_hash' => $password_hash,
        'role' => 'admin',
        'status' => 'active',
        'email_verified' => 1,
        'approved' => 1,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $stmt = db()->query($sql, $params);
    $admin_id = $stmt ? db()->getConnection()->lastInsertId() : false;

    if ($admin_id) {
        echo "  ✓ Admin account created successfully!\n";
        echo "\nAdmin Account Details:\n";
        echo "  Email: $email\n";
        echo "  Password: $password\n";
        echo "  Name: $first_name $last_name\n";
        echo "  User ID: $admin_id\n";

        // Send welcome email
        echo "\nStep 3: Sending welcome email...\n";

        $to = $email;
        $subject = "Admin Account Created - School Management System";
        $current_year = date('Y');
        $email_message = <<<HTML
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Admin Account Created</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f4f4f4; }
                .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; }
                .header { background: linear-gradient(135deg, #00BFFF 0%, #8A2BE2 100%); color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; }
                .button { display: inline-block; background: #00BFFF; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { background: #f9f9f9; padding: 20px; text-align: center; color: #666; font-size: 12px; }
                .credentials { background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Admin Account Created!</h1>
                </div>
                <div class='content'>
                    <p>Hello <strong>{$first_name} {$last_name}</strong>,</p>
                    <p>Your administrator account has been successfully created for the Attendance Management System.</p>

                    <div class='credentials'>
                        <h3>Login Credentials:</h3>
                        <p><strong>Email:</strong> {$email}</p>
                        <p><strong>Password:</strong> {$password}</p>
                        <p><strong>Role:</strong> Administrator</p>
                    </div>

                    <p>You can now login to the system and manage users, classes, and attendance records.</p>

                    <p style='text-align: center;'>
                        <a href='http://localhost/attendance/login.php' class='button'>Login to Dashboard</a>
                    </p>

                    <p><strong>Important:</strong> All previous data has been cleared. You're starting with a fresh system!</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message from the Attendance Management System.</p>
                    <p>&copy; {$current_year} School Management System. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
HTML;

        if (quick_send_email($to, $subject, $email_message)) {
            echo "  ✓ Welcome email sent to $email\n";
        } else {
            echo "  ⚠ Warning: Email could not be sent. Check your mail server configuration.\n";
        }

        echo "\n✅ Setup Complete!\n";
        echo "\nYou can now login at: http://localhost/attendance/login.php\n";
        echo "Email: $email\n";
        echo "Password: $password\n";
    } else {
        echo "  ✗ Failed to create admin account.\n";
        $errorInfo = db()->getConnection()->errorInfo();
        echo "  Error: " . ($errorInfo[2] ?? 'Unknown error') . "\n";
    }
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";
