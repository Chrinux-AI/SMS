<?php

/**
 * Common Functions
 */

/**
 * Sanitize input data
 */
function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Alias for sanitize() function for backward compatibility
 */
function sanitize_input($data)
{
    return sanitize($data);
}

/**
 * Validate email
 */
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Hash password
 */
function hash_password($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 */
function verify_password($password, $hash)
{
    return password_verify($password, $hash);
}

/**
 * Generate CSRF token
 */
function generate_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check for CSRF token in headers, POST body, or JSON payload
 * Returns true if valid, false otherwise
 */
function csrf_check_from_request()
{
    // Prefer header (for AJAX)
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

    // Then POST form field
    if (!$token && isset($_POST['csrf_token'])) {
        $token = $_POST['csrf_token'];
    }

    // Finally, attempt to parse JSON body (for fetch requests)
    if (!$token) {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $data = json_decode($raw, true);
            if (is_array($data) && isset($data['csrf_token'])) {
                $token = $data['csrf_token'];
            }
        }
    }

    return $token && verify_csrf_token($token);
}

/**
 * Require CSRF to be present and valid for the current request.
 * If invalid, it will respond and exit (JSON for API requests, redirect for forms).
 */
function csrf_require()
{
    if (!csrf_check_from_request()) {
        // If this looks like an AJAX/API call, return JSON
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (strpos($accept, 'application/json') !== false || isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }

        // Otherwise, redirect back with error
        if (function_exists('redirect')) {
            redirect($_SERVER['HTTP_REFERER'] ?? '../login.php', 'Invalid CSRF token', 'error');
        } else {
            header('HTTP/1.1 403 Forbidden');
            echo 'Invalid CSRF token';
            exit;
        }
    }
}

/**
 * Check if user is logged in
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Check user role
 */
function has_role($role)
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Require login - redirect if not logged in
 */
function require_login($redirect_url = '../login.php')
{
    // TEST MODE BYPASS - Remove in production!
    if (defined('TEST_MODE') && TEST_MODE === true) {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = 'admin';
        $_SESSION['full_name'] = 'Test User';
        return;
    }

    if (!is_logged_in()) {
        redirect($redirect_url, 'Please login to access this page', 'error');
    }
}

/**
 * Alias for require_login for compatibility
 */
function check_login($redirect_url = '../login.php')
{
    require_login($redirect_url);
}

/**
 * Require admin role - redirect if not admin
 */
function require_admin($redirect_url = '../login.php')
{
    // TEST MODE BYPASS - Remove in production!
    if (defined('TEST_MODE') && TEST_MODE === true) {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = 'admin';
        $_SESSION['full_name'] = 'Test Admin';
        return;
    }

    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if (!is_logged_in()) {
        // Clear any existing session data for security
        session_destroy();
        redirect($redirect_url, 'Please login to access this page', 'error');
    }

    // Double-check admin role from database for extra security
    if (!has_role('admin')) {
        // Log unauthorized access attempt
        error_log("Unauthorized admin access attempt by user ID: " . ($_SESSION['user_id'] ?? 'unknown'));
        redirect($redirect_url, 'Access denied. Admin privileges required.', 'error');
    }

    // Verify admin status in database (prevent session hijacking)
    if (isset($_SESSION['user_id'])) {
        $user = db()->fetch("SELECT role, status FROM users WHERE id = ?", [$_SESSION['user_id']]);
        if (!$user || $user['role'] !== 'admin' || $user['status'] !== 'active') {
            // Clear compromised session
            session_destroy();
            error_log("Admin session validation failed for user ID: " . $_SESSION['user_id']);
            redirect($redirect_url, 'Session invalid. Please login again.', 'error');
        }
    }
}

/**
 * Require specific role
 */
function require_role($role, $redirect_url = '../login.php')
{
    // TEST MODE BYPASS - Remove in production!
    if (defined('TEST_MODE') && TEST_MODE === true) {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = $role;
        $_SESSION['full_name'] = 'Test ' . ucfirst($role);
        return;
    }

    if (!is_logged_in()) {
        redirect($redirect_url, 'Please login to access this page', 'error');
    }
    if (!has_role($role)) {
        redirect($redirect_url, 'Access denied. Insufficient privileges.', 'error');
    }
}

/**
 * Require teacher role - redirect if not teacher
 */
function require_teacher($redirect_url = '../login.php')
{
    // TEST MODE BYPASS - Remove in production!
    if (defined('TEST_MODE') && TEST_MODE === true) {
        $_SESSION['user_id'] = 2;
        $_SESSION['user_role'] = 'teacher';
        $_SESSION['full_name'] = 'Test Teacher';
        return;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!is_logged_in()) {
        session_destroy();
        redirect($redirect_url, 'Please login to access this page', 'error');
    }
    if (!has_role('teacher')) {
        error_log("Unauthorized teacher access attempt by user ID: " . ($_SESSION['user_id'] ?? 'unknown'));
        redirect($redirect_url, 'Access denied. Teacher privileges required.', 'error');
    }
    if (isset($_SESSION['user_id'])) {
        $user = db()->fetchOne("SELECT role, status FROM users WHERE id = ?", [$_SESSION['user_id']]);
        if (!$user || $user['role'] !== 'teacher' || $user['status'] !== 'active') {
            session_destroy();
            error_log("Teacher session validation failed for user ID: " . $_SESSION['user_id']);
            redirect($redirect_url, 'Session invalid. Please login again.', 'error');
        }
    }
}

/**
 * Require student role - redirect if not student
 */
function require_student($redirect_url = '../login.php')
{
    // TEST MODE BYPASS - Remove in production!
    if (defined('TEST_MODE') && TEST_MODE === true) {
        $_SESSION['user_id'] = 3;
        $_SESSION['user_role'] = 'student';
        $_SESSION['full_name'] = 'Test Student';
        return;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!is_logged_in()) {
        session_destroy();
        redirect($redirect_url, 'Please login to access this page', 'error');
    }
    if (!has_role('student')) {
        error_log("Unauthorized student access attempt by user ID: " . ($_SESSION['user_id'] ?? 'unknown'));
        redirect($redirect_url, 'Access denied. Student privileges required.', 'error');
    }
    if (isset($_SESSION['user_id'])) {
        $user = db()->fetchOne("SELECT role, status FROM users WHERE id = ?", [$_SESSION['user_id']]);
        if (!$user || $user['role'] !== 'student' || $user['status'] !== 'active') {
            session_destroy();
            error_log("Student session validation failed for user ID: " . $_SESSION['user_id']);
            redirect($redirect_url, 'Session invalid. Please login again.', 'error');
        }
    }
}

/**
 * Require parent role - redirect if not parent
 */
function require_parent($redirect_url = '../login.php')
{
    // TEST MODE BYPASS - Remove in production!
    if (defined('TEST_MODE') && TEST_MODE === true) {
        $_SESSION['user_id'] = 4;
        $_SESSION['user_role'] = 'parent';
        $_SESSION['full_name'] = 'Test Parent';
        return;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!is_logged_in()) {
        session_destroy();
        redirect($redirect_url, 'Please login to access this page', 'error');
    }
    if (!has_role('parent')) {
        error_log("Unauthorized parent access attempt by user ID: " . ($_SESSION['user_id'] ?? 'unknown'));
        redirect($redirect_url, 'Access denied. Parent privileges required.', 'error');
    }
    if (isset($_SESSION['user_id'])) {
        $user = db()->fetchOne("SELECT role, status FROM users WHERE id = ?", [$_SESSION['user_id']]);
        if (!$user || $user['role'] !== 'parent' || $user['status'] !== 'active') {
            session_destroy();
            error_log("Parent session validation failed for user ID: " . $_SESSION['user_id']);
            redirect($redirect_url, 'Session invalid. Please login again.', 'error');
        }
    }
}

/**
 * Redirect with message
 */
function redirect($url, $message = '', $type = 'info')
{
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $url");
    exit();
}

/**
 * Get and clear flash message
 */
function get_flash_message()
{
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Format date
 */
function format_date($date, $format = 'Y-m-d')
{
    if (!$date) return '-';
    return date($format, strtotime($date));
}

/**
 * Format datetime
 */
function format_datetime($datetime, $format = 'M j, Y g:i A')
{
    if (!$datetime) return '-';
    return date($format, strtotime($datetime));
}

/**
 * Calculate attendance percentage
 */
function calculate_attendance_percentage($present, $total)
{
    if ($total == 0) return 0;
    return round(($present / $total) * 100, 2);
}

/**
 * Log activity
 */
function log_activity($user_id, $action, $entity_type = null, $entity_id = null, $details = null)
{
    $data = [
        'user_id' => $user_id,
        'action' => $action,
        'entity_type' => $entity_type,
        'entity_id' => $entity_id,
        'details' => $details ? json_encode($details) : null,
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'created_at' => date('Y-m-d H:i:s')
    ];

    return db()->insert('audit_logs', $data);
}

/**
 * Send notification
 */
function send_notification($user_id, $title, $message, $type = 'info', $channels = ['in-app'])
{
    $data = [
        'user_id' => $user_id,
        'title' => $title,
        'message' => $message,
        'type' => $type,
        'channels' => json_encode($channels),
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ];

    return db()->insert('notifications', $data);
}

/**
 * Get student attendance summary
 */
function get_student_attendance_summary($student_id, $start_date = null, $end_date = null)
{
    $where = 'student_id = :student_id';
    $params = ['student_id' => $student_id];

    if ($start_date) {
        $where .= ' AND attendance_date >= :start_date';
        $params['start_date'] = $start_date;
    }

    if ($end_date) {
        $where .= ' AND attendance_date <= :end_date';
        $params['end_date'] = $end_date;
    }

    $sql = "SELECT
                status,
                COUNT(*) as count
            FROM attendance_records
            WHERE {$where}
            GROUP BY status";

    $results = db()->fetchAll($sql, $params);

    $summary = [
        'present' => 0,
        'absent' => 0,
        'late' => 0,
        'excused' => 0,
        'total' => 0
    ];

    foreach ($results as $row) {
        $summary[$row['status']] = (int)$row['count'];
        $summary['total'] += (int)$row['count'];
    }

    $summary['attendance_rate'] = calculate_attendance_percentage(
        $summary['present'] + $summary['late'],
        $summary['total']
    );

    return $summary;
}

/**
 * Check for chronic absenteeism
 */
function is_chronically_absent($student_id, $days = 90)
{
    $end_date = date('Y-m-d');
    $start_date = date('Y-m-d', strtotime("-{$days} days"));

    $summary = get_student_attendance_summary($student_id, $start_date, $end_date);

    return $summary['total'] > 0 &&
        $summary['attendance_rate'] < (100 - CHRONIC_ABSENTEEISM_THRESHOLD);
}

/**
 * Generate secure random string
 */
function generate_random_string($length = 32)
{
    return bin2hex(random_bytes($length / 2));
}

/**
 * Check file upload
 */
function validate_file_upload($file)
{
    $errors = [];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error';
        return $errors;
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = 'File size exceeds maximum allowed';
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        $errors[] = 'File type not allowed';
    }

    return $errors;
}

/**
 * Upload file
 */
function upload_file($file, $subfolder = '')
{
    $errors = validate_file_upload($file);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $upload_dir = UPLOAD_PATH . '/' . $subfolder;
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = generate_random_string() . '.' . $extension;
    $filepath = $upload_dir . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'relative_path' => $subfolder . '/' . $filename
        ];
    }

    return ['success' => false, 'errors' => ['Failed to move uploaded file']];
}

/**
 * Send email notification
 */
/**
 * Send email using Gmail SMTP via PHPMailer
 * Requires: composer require phpmailer/phpmailer
 * Configuration in config.php: SMTP_HOST, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD
 */
function send_email($to, $subject, $message, $from_name = null)
{
    // Use from_name from config if not provided
    if ($from_name === null) {
        $from_name = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'School Management System';
    }

    // Check if PHPMailer is installed
    $autoload_path = BASE_PATH . '/vendor/autoload.php';
    if (!file_exists($autoload_path)) {
        // Fallback to basic PHP mail() if PHPMailer not installed
        error_log('PHPMailer not installed. Run: composer require phpmailer/phpmailer');
        return send_email_basic($to, $subject, $message, $from_name);
    }

    require_once $autoload_path;

    // Use PHPMailer classes - must be after require_once
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
        $mail->Password   = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
        $mail->SMTPSecure = defined('SMTP_ENCRYPTION') ? SMTP_ENCRYPTION : 'tls';
        $mail->Port       = defined('SMTP_PORT') ? SMTP_PORT : 587;

        // Check if password is set
        if (empty($mail->Password)) {
            error_log('SMTP_PASSWORD not set in config.php. See EMAIL-SMTP-SETUP.md for instructions.');
            return false;
        }

        // Recipients
        $mail->setFrom(
            defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'christolabiyi35@gmail.com',
            $from_name
        );
        $mail->addAddress($to);
        $mail->addReplyTo(
            defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'christolabiyi35@gmail.com',
            $from_name
        );

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->CharSet = 'UTF-8';

        // Wrap message in HTML template
        $html_message = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
                .footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
                .info-box { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>School Management System</h1>
                </div>
                <div class='content'>
                    $message
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " School Management System. All rights reserved.</p>
                    <p>This is an automated message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->Body = $html_message;
        $mail->AltBody = strip_tags($message); // Plain text version

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Fallback email function using basic PHP mail() if PHPMailer not available
 */
function send_email_basic($to, $subject, $message, $from_name = 'School Management System')
{
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        'From: ' . $from_name . ' <noreply@school.com>',
        'Reply-To: noreply@school.com',
        'X-Mailer: PHP/' . phpversion()
    ];

    $html_message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
            .footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 10px 10px; }
            .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
            .info-box { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>School Management System</h1>
            </div>
            <div class='content'>
                $message
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " School Management System. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    return mail($to, $subject, $html_message, implode("\r\n", $headers));
}

/**
 * Send WhatsApp notification via Twilio API
 * Falls back to logging if Twilio not configured
 * Configuration in config.php: TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN, TWILIO_WHATSAPP_FROM
 */
function send_whatsapp($phone, $message)
{
    // WhatsApp notifications disabled by admin request
    error_log("WhatsApp disabled: Would have sent to $phone - $message");
    return false;

    // Clean phone number (remove spaces, dashes, etc.)
    $phone = preg_replace('/[^0-9+]/', '', $phone);

    // Check if Twilio is configured
    if (!defined('TWILIO_ACCOUNT_SID') || empty(TWILIO_ACCOUNT_SID)) {
        // Fallback to logging if Twilio not configured
        $log_file = BASE_PATH . '/logs/whatsapp.log';

        // Ensure logs directory exists
        $log_dir = dirname($log_file);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0777, true);
        }

        $log_message = "\n[" . date('Y-m-d H:i:s') . "] To: $phone\n";
        $log_message .= "Message: $message\n";
        $log_message .= "---\n";

        file_put_contents($log_file, $log_message, FILE_APPEND);
        error_log("WhatsApp: Twilio not configured. Message logged to file.");
        return true;
    }

    // Check if Twilio SDK is installed
    $autoload_path = BASE_PATH . '/vendor/autoload.php';
    if (!file_exists($autoload_path)) {
        error_log('Twilio SDK not installed. Run: composer require twilio/sdk');
        // Fallback to logging
        return send_whatsapp_log_only($phone, $message);
    }

    require_once $autoload_path;

    try {
        // Initialize Twilio client
        $twilio = new \Twilio\Rest\Client(
            TWILIO_ACCOUNT_SID,
            TWILIO_AUTH_TOKEN
        );

        // Ensure phone number has 'whatsapp:' prefix
        if (strpos($phone, 'whatsapp:') === false) {
            $phone = 'whatsapp:' . $phone;
        }

        // Get from number (Twilio WhatsApp sandbox or approved number)
        $from = defined('TWILIO_WHATSAPP_FROM') ? TWILIO_WHATSAPP_FROM : 'whatsapp:+14155238886';

        // Send message
        $sent_message = $twilio->messages->create(
            $phone,  // To
            [
                'from' => $from,
                'body' => $message
            ]
        );

        // Log successful send
        error_log("WhatsApp sent successfully. SID: {$sent_message->sid} To: $phone");

        // Also log to file for record keeping
        $log_file = BASE_PATH . '/logs/whatsapp.log';
        $log_message = "\n[" . date('Y-m-d H:i:s') . "] ✅ SENT via Twilio\n";
        $log_message .= "To: $phone\n";
        $log_message .= "SID: {$sent_message->sid}\n";
        $log_message .= "Message: $message\n";
        $log_message .= "---\n";
        file_put_contents($log_file, $log_message, FILE_APPEND);

        return true;
    } catch (\Exception $e) {
        error_log("WhatsApp sending failed: " . $e->getMessage());

        // Log failed attempt
        $log_file = BASE_PATH . '/logs/whatsapp.log';
        $log_message = "\n[" . date('Y-m-d H:i:s') . "] ❌ FAILED\n";
        $log_message .= "To: $phone\n";
        $log_message .= "Error: " . $e->getMessage() . "\n";
        $log_message .= "Message: $message\n";
        $log_message .= "---\n";
        file_put_contents($log_file, $log_message, FILE_APPEND);

        return false;
    }
}

/**
 * Fallback function to log WhatsApp messages when Twilio not configured
 */
function send_whatsapp_log_only($phone, $message)
{
    $log_file = BASE_PATH . '/logs/whatsapp.log';

    // Ensure logs directory exists
    $log_dir = dirname($log_file);
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
    }

    $log_message = "\n[" . date('Y-m-d H:i:s') . "] To: $phone\n";
    $log_message .= "Message: $message\n";
    $log_message .= "Note: Twilio SDK not installed or not configured\n";
    $log_message .= "---\n";

    file_put_contents($log_file, $log_message, FILE_APPEND);
    return true;
}

/**
 * Send registration notification
 */
function send_registration_notification($user_id, $email, $name, $role)
{
    // Generate temporary registration ID for tracking
    $temp_id = 'REG' . str_pad($user_id, 6, '0', STR_PAD_LEFT);

    // Email to user
    $subject = "Registration Received - Awaiting Approval";
    $message = "
        <h2>Hello $name,</h2>
        <p>Thank you for registering with the School Management System.</p>
        <div class='info-box'>
            <strong>Your registration details:</strong><br>
            Name: $name<br>
            Email: $email<br>
            Role: " . ucfirst($role) . "<br>
            <strong>Registration ID: <span style='font-size: 18px; color: #667eea;'>$temp_id</span></strong><br>
            Status: <strong style='color: orange;'>Pending Approval</strong>
        </div>
        <p><strong>Important Information:</strong></p>
        <ul>
            <li>Your account is currently pending approval by an administrator</li>
            <li>Please save your Registration ID: <strong>$temp_id</strong></li>
            <li>You will receive your official Student/Employee ID once approved</li>
            <li>Login credentials will be provided after approval</li>
        </ul>
        <p>We will notify you via email once your account has been approved.</p>
        <p>If you have any questions, please contact the school administration.</p>
    ";
    send_email($email, $subject, $message);

    // Email to admin
    $admin_email = 'christolabiyi35@gmail.com';
    $admin_subject = "New Registration - Pending Approval";
    $admin_message = "
        <h2>🔔 New User Registration</h2>
        <div class='info-box'>
            <strong>Registration Details:</strong><br>
            Name: $name<br>
            Email: $email<br>
            Role: " . ucfirst($role) . "<br>
            <strong>Registration ID: <span style='font-size: 18px; color: #667eea;'>$temp_id</span></strong><br>
            User ID: $user_id<br>
            Registration Time: " . date('Y-m-d H:i:s') . "
        </div>
        <p><strong>Action Required:</strong> Please review and approve/reject this registration.</p>
        <p><strong>Access:</strong> Login to the attendance system and navigate to the admin section.</p>

        <div style='margin-top: 20px; padding: 15px; background: #f0f9ff; border-left: 4px solid #3b82f6;'>
            <strong>Next Steps:</strong><br>
            • Review complete application details<br>
            • Assign official Student/Employee ID<br>
            • Send approval notification with login credentials<br>
            • User will receive their permanent ID via email
        </div>
    ";
    send_email($admin_email, $admin_subject, $admin_message);

    // WhatsApp to admin
    $whatsapp_message = "🔔 New Registration\n\n" .
        "Name: $name\n" .
        "Email: $email\n" .
        "Role: " . ucfirst($role) . "\n\n" .
        "Please review pending registrations in the admin panel.";
    send_whatsapp('+2348167714860', $whatsapp_message);
}

/**
 * Send approval notification with ID
 */
function send_approval_notification($user_id, $email, $name, $role, $assigned_id, $username)
{
    $id_type = $role === 'student' ? 'Student ID' : ($role === 'teacher' ? 'Employee ID' : 'User ID');

    // Email to user with enhanced design
    $subject = "🎉 Account Approved - Welcome to " . APP_NAME . "!";
    $message = "
        <div style='max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='margin: 0; font-size: 28px;'>🎉 Congratulations $name!</h1>
                <p style='margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;'>Your account has been approved and activated</p>
            </div>

            <div style='background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);'>
                <div style='background: #f8fafc; padding: 25px; border-radius: 10px; border-left: 5px solid #10b981; margin-bottom: 25px;'>
                    <h3 style='color: #1e293b; margin: 0 0 15px 0; font-size: 18px;'>
                        <i class='fas fa-id-card'></i> Your Official Account Details
                    </h3>
                    <div style='line-height: 1.8; color: #374151;'>
                        <div style='font-size: 24px; font-weight: bold; color: #667eea; margin-bottom: 10px;'>
                            <strong>$id_type: $assigned_id</strong>
                        </div>
                        <strong>Username:</strong> $username<br>
                        <strong>Email:</strong> $email<br>
                        <strong>Role:</strong> " . ucfirst($role) . "<br>
                        <strong>Status:</strong> <span style='color: #10b981; font-weight: bold;'>✅ ACTIVE</span>
                    </div>
                </div>

                <div style='background: #fef3c7; padding: 20px; border-radius: 8px; border-left: 4px solid #f59e0b; margin-bottom: 25px;'>
                    <h4 style='color: #92400e; margin: 0 0 10px 0;'>📝 Important Information</h4>
                    <ul style='color: #92400e; margin: 0; padding-left: 20px;'>
                        <li><strong>Save your $id_type: $assigned_id</strong> - You'll need this for attendance</li>
                        <li>Use your <strong>username ($username)</strong> to login</li>
                        <li>Your password remains the same as when you registered</li>
                    </ul>
                </div>

                <div style='text-align: center; margin-top: 30px;'>
                    <div style='background: #e0f2fe; padding: 20px; border-radius: 8px;'>
                        <h4 style='color: #0277bd; margin: 0 0 10px 0;'>🚀 Ready to Get Started?</h4>
                        <p style='color: #0277bd; margin: 0;'>Login to the attendance system to access your dashboard and start marking attendance.</p>
                    </div>
                </div>

                <div style='margin-top: 25px; text-align: center; color: #64748b; font-size: 14px;'>
                    <p>Welcome to " . APP_NAME . "! If you have any questions, contact the administration.</p>
                </div>
            </div>
        </div>
    ";
    send_email($email, $subject, $message);

    // Enhanced admin notification
    $admin_email = 'christolabiyi35@gmail.com';
    $admin_subject = "✅ User Approved - $name ($assigned_id)";
    $admin_message = "
        <h2>✅ User Account Approved</h2>
        <div style='background: #f0fdf4; padding: 20px; border-radius: 8px; border-left: 4px solid #10b981;'>
            <h3 style='color: #166534; margin: 0 0 15px 0;'>Account Successfully Activated</h3>
            <div style='line-height: 1.6; color: #374151;'>
                <strong>User:</strong> $name<br>
                <strong>$id_type:</strong> <span style='font-size: 18px; color: #667eea; font-weight: bold;'>$assigned_id</span><br>
                <strong>Username:</strong> $username<br>
                <strong>Email:</strong> $email<br>
                <strong>Role:</strong> " . ucfirst($role) . "<br>
                <strong>Approved by:</strong> " . $_SESSION['full_name'] . "<br>
                <strong>Approval Time:</strong> " . date('Y-m-d H:i:s') . "
            </div>
        </div>

        <p><strong>✅ Actions Completed:</strong></p>
        <ul>
            <li>User account status changed to ACTIVE</li>
            <li>Official $id_type assigned: <strong>$assigned_id</strong></li>
            <li>Welcome email sent to user with all details</li>
            <li>User can now login and access the system</li>
        </ul>
    ";
    send_email($admin_email, $admin_subject, $admin_message);

    // WhatsApp notification
    $whatsapp_message = "✅ Account Approved!\n\n" .
        "Hello $name,\n\n" .
        "Your account has been activated.\n\n" .
        "Your $id_type: *$assigned_id*\n" .
        "Username: $username\n\n" .
        "Please save this ID for future use.\n\n" .
        "Access the attendance system to login.";
    send_whatsapp('+2348167714860', $whatsapp_message);
}

/**
 * Send rejection notification
 */
function send_rejection_notification($email, $name, $reason = '')
{
    $subject = "Registration Not Approved";
    $message = "
        <h2>Hello $name,</h2>
        <p>We regret to inform you that your registration could not be approved at this time.</p>
        " . ($reason ? "<div class='info-box'><strong>Reason:</strong> $reason</div>" : "") . "
        <p>If you believe this is an error or would like to discuss this decision, please contact the school administration.</p>
    ";
    send_email($email, $subject, $message);
}

/**
 * Send email verification link
 */
function send_verification_email($email, $name, $verification_token, $assigned_id = null, $role = null)
{
    $verification_link = "http://" . $_SERVER['HTTP_HOST'] . "/attendance/verify-email.php?token=" . $verification_token;

    // Determine ID type based on role
    $id_display = '';
    if ($assigned_id && $role) {
        $id_type = $role === 'student' ? 'Student ID' : ($role === 'teacher' ? 'Employee ID' : 'User ID');
        $id_display = "
            <div style='background: #e0f2fe; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #0ea5e9;'>
                <h4 style='color: #0369a1; margin: 0 0 10px 0;'>📋 Your Assigned ID</h4>
                <div style='font-size: 24px; font-weight: bold; color: #667eea; margin-bottom: 5px;'>
                    $id_type: $assigned_id
                </div>
                <p style='margin: 5px 0 0 0; color: #0369a1; font-size: 14px;'>
                    <strong>Please save this ID!</strong> You'll need it for attendance tracking and system access.
                </p>
            </div>
        ";
    }

    $subject = "Verify Your Email - School Management System";
    $message = "
        <div style='max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='margin: 0;'>📧 Email Verification</h1>
            </div>
            <div style='background: white; padding: 30px; border: 1px solid #e0e0e0;'>
                <p>Hello <strong>$name</strong>,</p>
                <p>Thank you for registering with the Attendance Management System!</p>

                $id_display

                <p>Please verify your email address by clicking the button below:</p>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='$verification_link' style='display: inline-block; background: #00BFFF; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Verify My Email</a>
                </p>
                <p>Or copy and paste this link into your browser:</p>
                <p style='background: #f9f9f9; padding: 15px; border-radius: 5px; word-break: break-all; font-size: 12px; border-left: 3px solid #00BFFF;'>$verification_link</p>
                <div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;'>
                    <p style='margin: 0;'><strong>⚠️ Important:</strong> After email verification, your account must be approved by an administrator before you can login.</p>
                </div>
            </div>
            <div style='background: #f9f9f9; padding: 20px; text-align: center; color: #666; font-size: 12px; border-radius: 0 0 10px 10px;'>
                <p style='margin: 0;'>If you didn't register for this account, please ignore this email.</p>
                <p style='margin: 10px 0 0 0;'>&copy; " . date('Y') . " School Management System. All rights reserved.</p>
            </div>
        </div>
    ";

    return send_email($email, $subject, $message);
}

/**
 * Get recent attendance records for a student
 */
function get_recent_attendance($student_id, $limit = 10)
{
    global $pdo;

    try {
        $sql = "SELECT a.*, c.name as class_name
                FROM attendance a
                LEFT JOIN classes c ON a.class_id = c.id
                WHERE a.student_id = :student_id
                ORDER BY a.date DESC, a.created_at DESC
                LIMIT :limit";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':student_id', $student_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching recent attendance: " . $e->getMessage());
        return [];
    }
}

/**
 * Dashboard Helper Functions
 */

/**
 * Calculate attendance trend for AI analytics
 */
function calculateAttendanceTrend()
{
    try {
        // Mock AI calculation - replace with actual ML model
        $trend_data = [
            'overall_trend' => 'increasing',
            'trend_percentage' => 5.2,
            'prediction_confidence' => 0.94,
            'risk_factors' => ['Monday absences', 'Weather correlation']
        ];
        return $trend_data;
    } catch (Exception $e) {
        error_log("Error calculating attendance trend: " . $e->getMessage());
        return ['overall_trend' => 'stable', 'trend_percentage' => 0];
    }
}

/**
 * Identify students at risk of chronic absenteeism
 */
function identifyRiskStudents()
{
    try {
        global $pdo;

        // Check if PDO connection is available
        if (!$pdo) {
            error_log("Database connection not available for identifyRiskStudents()");
            return [];
        }

        $sql = "SELECT s.id, s.student_id, s.first_name, s.last_name,
                COUNT(ar.id) as total_days,
                SUM(CASE WHEN ar.status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                ROUND((SUM(CASE WHEN ar.status = 'absent' THEN 1 ELSE 0 END) / COUNT(ar.id)) * 100, 1) as risk_score
                FROM students s
                LEFT JOIN attendance_records ar ON s.id = ar.student_id
                WHERE ar.attendance_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY s.id
                HAVING risk_score > 15
                ORDER BY risk_score DESC
                LIMIT 10";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error identifying risk students: " . $e->getMessage());
        return [];
    } catch (Exception $e) {
        error_log("General error in identifyRiskStudents: " . $e->getMessage());
        return [];
    }
}
/**
 * Predict performance based on attendance patterns
 */
function predictPerformance()
{
    // Mock AI prediction - replace with actual ML model
    return [
        'predicted_grade_improvement' => 12.5,
        'confidence_level' => 0.89,
        'factors' => ['attendance_consistency', 'early_arrival_pattern'],
        'recommendations' => ['Encourage morning routine', 'Reward perfect attendance']
    ];
}

/**
 * Analyze optimal scheduling patterns
 */
function analyzeOptimalSchedule()
{
    // Mock AI analysis - replace with actual ML model
    return [
        'optimal_start_time' => '08:30',
        'peak_attention_periods' => ['09:00-10:30', '14:00-15:30'],
        'recommended_break_intervals' => 90,
        'subject_optimization' => [
            'math' => 'morning',
            'arts' => 'afternoon',
            'physical_education' => 'late_morning'
        ]
    ];
}

/**
 * Get number of connected devices for real-time sync
 */
function getConnectedDevices()
{
    // Mock data - replace with actual device tracking
    return rand(15, 25);
}

/**
 * Get real-time data packets count
 */
function getRealTimePackets()
{
    // Mock data - replace with actual packet monitoring
    return rand(450, 650);
}

/**
 * Generate smart insights from data
 */
function generateSmartInsights()
{
    // Mock insights - replace with actual data analysis
    return [
        'attendance_peak_day' => 'Tuesday',
        'common_absence_reasons' => ['illness', 'family_events', 'transportation'],
        'improvement_suggestions' => [
            'Send reminder notifications on Sunday evening',
            'Implement early warning system for at-risk students',
            'Create incentive programs for perfect attendance'
        ],
        'seasonal_patterns' => [
            'winter_months' => 'Higher absence rate due to illness',
            'spring_months' => 'Improved attendance with better weather'
        ]
    ];
}

/**
 * Get active mobile sessions count
 */
function getMobileActiveSessions()
{
    // Mock data - replace with actual session tracking
    return rand(85, 120);
}

/**
 * Get API requests count for today
 */
function getApiRequestsToday()
{
    // Mock data - replace with actual API monitoring
    return rand(1200, 1800);
}

/**
 * Get blocked security attempts count
 */
function getBlockedAttempts()
{
    // Mock data - replace with actual security monitoring
    return rand(3, 12);
}

/**
 * Get active authentication tokens count
 */
function getActiveTokens()
{
    // Mock data - replace with actual token management
    return rand(45, 85);
}

/**
 * Time ago helper function
 */
function timeAgo($datetime)
{
    if (empty($datetime)) {
        return 'Never';
    }

    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    $difference = time() - $timestamp;

    if ($difference < 1) {
        return 'Just now';
    }

    $periods = [
        'year' => 31536000,
        'month' => 2592000,
        'week' => 604800,
        'day' => 86400,
        'hour' => 3600,
        'minute' => 60,
        'second' => 1
    ];

    foreach ($periods as $period => $seconds) {
        $count = floor($difference / $seconds);
        if ($count > 0) {
            return $count . ' ' . $period . ($count > 1 ? 's' : '') . ' ago';
        }
    }

    return 'Just now';
}

/**
 * Format number with suffix (K, M, B)
 */
function formatNumber($number)
{
    if ($number < 1000) {
        return number_format($number);
    } elseif ($number < 1000000) {
        return round($number / 1000, 1) . 'K';
    } elseif ($number < 1000000000) {
        return round($number / 1000000, 1) . 'M';
    } else {
        return round($number / 1000000000, 1) . 'B';
    }
}

/**
 * Get percentage change
 */
function getPercentageChange($current, $previous)
{
    if ($previous == 0) {
        return $current > 0 ? '+100%' : '0%';
    }

    $change = (($current - $previous) / $previous) * 100;
    $sign = $change >= 0 ? '+' : '';

    return $sign . round($change, 1) . '%';
}

/**
 * Get status badge HTML
 */
function getStatusBadge($status)
{
    $badges = [
        'present' => '<span class="badge badge-success">Present</span>',
        'absent' => '<span class="badge badge-danger">Absent</span>',
        'late' => '<span class="badge badge-warning">Late</span>',
        'excused' => '<span class="badge badge-info">Excused</span>',
        'active' => '<span class="badge badge-success">Active</span>',
        'inactive' => '<span class="badge badge-secondary">Inactive</span>',
    ];

    return $badges[strtolower($status)] ?? '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
}
