<?php

/**
 * School Management System - Configuration File
 */

// Base Paths
define('BASE_PATH', dirname(__DIR__));
define('INCLUDES_PATH', BASE_PATH . '/includes');

// Database Configuration
define('DB_HOST', '/opt/lampp/var/mysql/mysql.sock');  // Socket path
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'attendance_system');
define('DB_CHARSET', 'utf8mb4');

// Application Settings
define('APP_NAME', 'SMS');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/attendance');
define('TIMEZONE', 'America/New_York');

// Security Settings
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('PASSWORD_MIN_LENGTH', 12);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 900); // 15 minutes

// File Upload Settings
define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('MAX_FILE_SIZE', 10485760); // 10MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);

// Attendance Settings
define('ATTENDANCE_STATUSES', ['present', 'absent', 'late', 'excused']);
define('CHRONIC_ABSENTEEISM_THRESHOLD', 10); // percentage

// Email Settings - Gmail SMTP Configuration
// IMPORTANT: You must set up Gmail App Password first! See EMAIL-SMTP-SETUP.md for instructions
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: 'your-email@gmail.com');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: 'your-app-password');  // Gmail App Password - use .env file
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: 'your-email@gmail.com');
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'SMS');
define('SMTP_ENCRYPTION', 'tls');  // Use 'tls' for port 587, or 'ssl' for port 465

// WhatsApp Configuration (Twilio)
// See WHATSAPP-API-SETUP.md for setup instructions
define('TWILIO_ACCOUNT_SID', getenv('TWILIO_ACCOUNT_SID') ?: 'your_twilio_account_sid');
define('TWILIO_AUTH_TOKEN', getenv('TWILIO_AUTH_TOKEN') ?: 'your_twilio_auth_token');  // Use .env file
define('TWILIO_WHATSAPP_FROM', getenv('TWILIO_WHATSAPP_FROM') ?: 'whatsapp:+14155238886');
define('ADMIN_WHATSAPP_NUMBER', getenv('ADMIN_WHATSAPP_NUMBER') ?: 'whatsapp:+1234567890');

// Set timezone
date_default_timezone_set(TIMEZONE);

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration (must be set before session_start)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);
}
