<?php

/**
 * Database Reset & Clean Setup Script
 * This will delete all existing data and create fresh accounts
 */

require_once '../includes/config.php';
require_once '../includes/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/attendance/manifest.json">
    <meta name="theme-color" content="#00BFFF">
    <link rel="apple-touch-icon" href="/attendance/assets/images/icons/icon-192x192.png">
    <title>System Reset</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #0A0A0A;
            color: #00BFFF;
            padding: 40px;
        }
        .log { margin: 10px 0; }
        .success { color: #00FF7F; }
        .error { color: #FF4500; }
        .info { color: #FFD700; }
    </style>
</head>
<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>

        <main class="cyber-main">

    <div class="starfield"></div>
    <div class="cyber-grid"></div>
<h1>🔧 System Reset & Database Clean Up</h1>
<div id='log'>";

try {
    $db = db();

    // Truncate all tables
    $tables = ['attendance_records', 'students', 'teachers', 'classes', 'users'];

    echo "<div class='log info'>Clearing all existing data...</div>";

    foreach ($tables as $table) {
        try {
            $db->execute("TRUNCATE TABLE `$table`");
            echo "<div class='log success'>✓ Cleared table: $table</div>";
        } catch (Exception $e) {
            echo "<div class='log info'>→ Table $table already empty or doesn't exist</div>";
        }
    }

    echo "<br><div class='log info'>Creating fresh admin account...</div>";

    // Create admin account
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $db->execute(
        "INSERT INTO users (email, password, first_name, last_name, role, created_at)
         VALUES (:email, :password, :first_name, :last_name, :role, NOW())",
        [
            'email' => 'admin@attendance.com',
            'password' => $admin_password,
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'role' => 'admin'
        ]
    );

    echo "<div class='log success'>✓ Admin account created</div>";
    echo "<div class='log success'>  → Email: admin@attendance.com</div>";
    echo "<div class='log success'>  → Password: admin123</div>";

    echo "<br><div class='log info'>Creating sample teacher account...</div>";

    // Create teacher account
    $teacher_password = password_hash('teacher123', PASSWORD_DEFAULT);
    $db->execute(
        "INSERT INTO users (email, password, first_name, last_name, role, created_at)
         VALUES (:email, :password, :first_name, :last_name, :role, NOW())",
        [
            'email' => 'teacher@attendance.com',
            'password' => $teacher_password,
            'first_name' => 'John',
            'last_name' => 'Teacher',
            'role' => 'teacher'
        ]
    );

    echo "<div class='log success'>✓ Teacher account created</div>";
    echo "<div class='log success'>  → Email: teacher@attendance.com</div>";
    echo "<div class='log success'>  → Password: teacher123</div>";

    echo "<br><div class='log success'>✅ DATABASE RESET COMPLETE!</div>";
    echo "<br><div class='log info'>You can now login with:</div>";
    echo "<div class='log info'>Admin: admin@attendance.com / admin123</div>";
    echo "<div class='log info'>Teacher: teacher@attendance.com / teacher123</div>";
    echo "<br><div class='log'><a href='../login.php' style='color: #00BFFF; text-decoration: underline;'>→ Go to Login Page</a></div>";
} catch (Exception $e) {
    echo "<div class='log error'>✗ ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</div>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body></html>";
