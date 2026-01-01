<?php

/**
 * Advanced System Settings Page
 * Comprehensive settings for system administration
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

require_role('admin');

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$message = '';
$message_type = '';

// Get current settings
function getSettings()
{
  $settings = [];
  $rows = db()->fetchAll("SELECT setting_key, setting_value FROM system_settings");
  foreach ($rows as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
  }
  return $settings;
}

// Save a setting
function saveSetting($key, $value)
{
  $existing = db()->fetch("SELECT id FROM system_settings WHERE setting_key = ?", [$key]);
  if ($existing) {
    db()->execute("UPDATE system_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?", [$value, $key]);
  } else {
    db()->insert('system_settings', [
      'setting_key' => $key,
      'setting_value' => $value,
      'created_at' => date('Y-m-d H:i:s')
    ]);
  }
}

$settings = getSettings();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // General Settings
  if (isset($_POST['save_general'])) {
    saveSetting('school_name', sanitize($_POST['school_name'] ?? ''));
    saveSetting('school_address', sanitize($_POST['school_address'] ?? ''));
    saveSetting('school_phone', sanitize($_POST['school_phone'] ?? ''));
    saveSetting('school_email', sanitize($_POST['school_email'] ?? ''));
    saveSetting('school_website', sanitize($_POST['school_website'] ?? ''));
    saveSetting('school_motto', sanitize($_POST['school_motto'] ?? ''));
    saveSetting('academic_year', sanitize($_POST['academic_year'] ?? ''));
    saveSetting('current_term', sanitize($_POST['current_term'] ?? ''));
    $message = 'General settings saved successfully!';
    $message_type = 'success';
  }

  // Registration Settings
  if (isset($_POST['save_registration'])) {
    saveSetting('registration_enabled', isset($_POST['registration_enabled']) ? '1' : '0');
    saveSetting('email_verification_required', isset($_POST['email_verification_required']) ? '1' : '0');
    saveSetting('admin_approval_required', isset($_POST['admin_approval_required']) ? '1' : '0');
    saveSetting('allowed_roles', implode(',', $_POST['allowed_roles'] ?? []));
    saveSetting('max_students_per_class', sanitize($_POST['max_students_per_class'] ?? '40'));
    $message = 'Registration settings saved successfully!';
    $message_type = 'success';
  }

  // Attendance Settings
  if (isset($_POST['save_attendance'])) {
    saveSetting('attendance_start_time', sanitize($_POST['attendance_start_time'] ?? '08:00'));
    saveSetting('attendance_end_time', sanitize($_POST['attendance_end_time'] ?? '09:00'));
    saveSetting('late_threshold_minutes', sanitize($_POST['late_threshold_minutes'] ?? '15'));
    saveSetting('biometric_enabled', isset($_POST['biometric_enabled']) ? '1' : '0');
    saveSetting('qr_code_enabled', isset($_POST['qr_code_enabled']) ? '1' : '0');
    saveSetting('manual_attendance_enabled', isset($_POST['manual_attendance_enabled']) ? '1' : '0');
    saveSetting('parent_notification_enabled', isset($_POST['parent_notification_enabled']) ? '1' : '0');
    $message = 'Attendance settings saved successfully!';
    $message_type = 'success';
  }

  // Email Settings
  if (isset($_POST['save_email'])) {
    saveSetting('smtp_host', sanitize($_POST['smtp_host'] ?? ''));
    saveSetting('smtp_port', sanitize($_POST['smtp_port'] ?? '587'));
    saveSetting('smtp_username', sanitize($_POST['smtp_username'] ?? ''));
    if (!empty($_POST['smtp_password'])) {
      saveSetting('smtp_password', $_POST['smtp_password']);
    }
    saveSetting('smtp_encryption', sanitize($_POST['smtp_encryption'] ?? 'tls'));
    saveSetting('from_email', sanitize($_POST['from_email'] ?? ''));
    saveSetting('from_name', sanitize($_POST['from_name'] ?? ''));
    $message = 'Email settings saved successfully!';
    $message_type = 'success';
  }

  // Security Settings
  if (isset($_POST['save_security'])) {
    saveSetting('session_timeout', sanitize($_POST['session_timeout'] ?? '1800'));
    saveSetting('max_login_attempts', sanitize($_POST['max_login_attempts'] ?? '5'));
    saveSetting('lockout_duration', sanitize($_POST['lockout_duration'] ?? '900'));
    saveSetting('password_min_length', sanitize($_POST['password_min_length'] ?? '8'));
    saveSetting('two_factor_enabled', isset($_POST['two_factor_enabled']) ? '1' : '0');
    saveSetting('audit_logging_enabled', isset($_POST['audit_logging_enabled']) ? '1' : '0');
    $message = 'Security settings saved successfully!';
    $message_type = 'success';
  }

  // Grading Settings
  if (isset($_POST['save_grading'])) {
    saveSetting('grading_system', sanitize($_POST['grading_system'] ?? 'percentage'));
    saveSetting('pass_mark', sanitize($_POST['pass_mark'] ?? '50'));
    saveSetting('grade_a_min', sanitize($_POST['grade_a_min'] ?? '70'));
    saveSetting('grade_b_min', sanitize($_POST['grade_b_min'] ?? '60'));
    saveSetting('grade_c_min', sanitize($_POST['grade_c_min'] ?? '50'));
    saveSetting('grade_d_min', sanitize($_POST['grade_d_min'] ?? '40'));
    saveSetting('show_class_position', isset($_POST['show_class_position']) ? '1' : '0');
    $message = 'Grading settings saved successfully!';
    $message_type = 'success';
  }

  // Fee Settings
  if (isset($_POST['save_fees'])) {
    saveSetting('currency', sanitize($_POST['currency'] ?? 'USD'));
    saveSetting('currency_symbol', sanitize($_POST['currency_symbol'] ?? '$'));
    saveSetting('payment_gateway', sanitize($_POST['payment_gateway'] ?? 'manual'));
    saveSetting('late_fee_percentage', sanitize($_POST['late_fee_percentage'] ?? '5'));
    saveSetting('fee_reminder_days', sanitize($_POST['fee_reminder_days'] ?? '7'));
    saveSetting('online_payment_enabled', isset($_POST['online_payment_enabled']) ? '1' : '0');
    $message = 'Fee settings saved successfully!';
    $message_type = 'success';
  }

  // Notification Settings
  if (isset($_POST['save_notifications'])) {
    saveSetting('email_notifications_enabled', isset($_POST['email_notifications_enabled']) ? '1' : '0');
    saveSetting('sms_notifications_enabled', isset($_POST['sms_notifications_enabled']) ? '1' : '0');
    saveSetting('push_notifications_enabled', isset($_POST['push_notifications_enabled']) ? '1' : '0');
    saveSetting('whatsapp_enabled', isset($_POST['whatsapp_enabled']) ? '1' : '0');
    saveSetting('twilio_sid', sanitize($_POST['twilio_sid'] ?? ''));
    saveSetting('twilio_token', sanitize($_POST['twilio_token'] ?? ''));
    saveSetting('twilio_phone', sanitize($_POST['twilio_phone'] ?? ''));
    $message = 'Notification settings saved successfully!';
    $message_type = 'success';
  }

  // Theme Settings
  if (isset($_POST['save_theme'])) {
    saveSetting('default_theme', sanitize($_POST['default_theme'] ?? 'cyberpunk'));
    saveSetting('allow_user_theme', isset($_POST['allow_user_theme']) ? '1' : '0');
    saveSetting('primary_color', sanitize($_POST['primary_color'] ?? '#00BFFF'));
    saveSetting('secondary_color', sanitize($_POST['secondary_color'] ?? '#8A2BE2'));
    saveSetting('logo_url', sanitize($_POST['logo_url'] ?? ''));
    $message = 'Theme settings saved successfully!';
    $message_type = 'success';
  }

  // Refresh settings
  $settings = getSettings();
}

$page_title = "System Settings";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?> - School Management System</title>
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/apple-touch-icon.png">
    <link rel="manifest" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>manifest.json">
    <meta name="msapplication-TileColor" content="#00BFFF">
    <meta name="msapplication-TileImage" content="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/mstile-150x150.png">
    <meta name="theme-color" content="#0a0a0f">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="../assets/css/cyberpunk-ui.css" rel="stylesheet">
  <style>
    :root {
      --primary: #00BFFF;
      --secondary: #8A2BE2;
      --accent: #00FF7F;
      --danger: #FF4757;
      --warning: #FFD700;
      --dark: #0a0a0f;
      --card-bg: rgba(20, 20, 30, 0.9);
      --border: rgba(0, 191, 255, 0.2);
    }

    .settings-page {
      padding: 2rem;
      max-width: 1400px;
      margin: 0 auto;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
    }

    .page-header h1 {
      font-family: 'Orbitron', sans-serif;
      color: var(--primary);
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .tabs-container {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
      margin-bottom: 2rem;
      background: var(--card-bg);
      padding: 0.75rem;
      border-radius: 12px;
      border: 1px solid var(--border);
    }

    .tab-btn {
      padding: 0.75rem 1.25rem;
      background: transparent;
      border: 1px solid transparent;
      border-radius: 8px;
      color: rgba(255, 255, 255, 0.7);
      cursor: pointer;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.3s;
    }

    .tab-btn:hover {
      background: rgba(0, 191, 255, 0.1);
      color: var(--primary);
    }

    .tab-btn.active {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      border-color: var(--primary);
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .settings-card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 2rem;
      margin-bottom: 1.5rem;
    }

    .settings-card h2 {
      color: var(--primary);
      font-size: 1.25rem;
      margin-bottom: 1.5rem;
      padding-bottom: 0.75rem;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-group label {
      display: block;
      color: rgba(255, 255, 255, 0.8);
      margin-bottom: 0.5rem;
      font-weight: 500;
      font-size: 0.9rem;
    }

    .form-group label i {
      margin-right: 0.5rem;
      color: var(--primary);
    }

    .form-control {
      width: 100%;
      padding: 0.75rem 1rem;
      background: rgba(0, 0, 0, 0.3);
      border: 1px solid var(--border);
      border-radius: 8px;
      color: white;
      font-size: 0.95rem;
      transition: all 0.3s;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 191, 255, 0.1);
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.3);
    }

    select.form-control {
      cursor: pointer;
    }

    .toggle-group {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 8px;
      margin-bottom: 0.75rem;
    }

    .toggle-info h4 {
      color: white;
      margin: 0 0 0.25rem 0;
      font-size: 0.95rem;
    }

    .toggle-info p {
      color: rgba(255, 255, 255, 0.5);
      margin: 0;
      font-size: 0.8rem;
    }

    .toggle-switch {
      position: relative;
      width: 50px;
      height: 26px;
    }

    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .toggle-slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(100, 100, 100, 0.5);
      border-radius: 26px;
      transition: 0.4s;
    }

    .toggle-slider:before {
      position: absolute;
      content: "";
      height: 20px;
      width: 20px;
      left: 3px;
      bottom: 3px;
      background: white;
      border-radius: 50%;
      transition: 0.4s;
    }

    input:checked+.toggle-slider {
      background: linear-gradient(135deg, var(--primary), var(--accent));
    }

    input:checked+.toggle-slider:before {
      transform: translateX(24px);
    }

    .btn {
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.3s;
      font-size: 0.95rem;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 20px rgba(0, 191, 255, 0.4);
    }

    .btn-secondary {
      background: rgba(100, 100, 100, 0.3);
      color: white;
      border: 1px solid var(--border);
    }

    .btn-danger {
      background: linear-gradient(135deg, var(--danger), #c0392b);
      color: white;
    }

    .alert {
      padding: 1rem 1.5rem;
      border-radius: 10px;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .alert-success {
      background: rgba(0, 255, 127, 0.1);
      border: 1px solid var(--accent);
      color: var(--accent);
    }

    .alert-error {
      background: rgba(255, 71, 87, 0.1);
      border: 1px solid var(--danger);
      color: var(--danger);
    }

    .checkbox-group {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 0.75rem;
    }

    .checkbox-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 8px;
      cursor: pointer;
    }

    .checkbox-item input {
      width: 18px;
      height: 18px;
      accent-color: var(--primary);
    }

    .checkbox-item label {
      cursor: pointer;
      margin: 0;
      color: rgba(255, 255, 255, 0.8);
    }

    .color-input-group {
      display: flex;
      gap: 0.5rem;
      align-items: center;
    }

    .color-input-group input[type="color"] {
      width: 50px;
      height: 40px;
      padding: 0;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }

    .color-input-group input[type="text"] {
      flex: 1;
    }

    .info-box {
      background: rgba(0, 191, 255, 0.1);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1rem;
    }

    .info-box p {
      color: rgba(255, 255, 255, 0.7);
      margin: 0;
      font-size: 0.85rem;
    }

    .info-box i {
      color: var(--primary);
      margin-right: 0.5rem;
    }

    @media (max-width: 768px) {
      .tabs-container {
        overflow-x: auto;
        flex-wrap: nowrap;
      }

      .tab-btn {
        white-space: nowrap;
      }

      .form-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body class="cyber-bg">
  <div class="starfield"></div>
  <div class="cyber-grid"></div>

  <?php include '../includes/cyber-nav.php'; ?>

  <div class="settings-page">
    <div class="page-header">
      <h1><i class="fas fa-sliders-h"></i> System Settings</h1>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-<?php echo $message_type; ?>">
        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
        <?php echo $message; ?>
      </div>
    <?php endif; ?>

    <!-- Settings Tabs -->
    <div class="tabs-container">
      <button class="tab-btn active" data-tab="general"><i class="fas fa-school"></i> General</button>
      <button class="tab-btn" data-tab="registration"><i class="fas fa-user-plus"></i> Registration</button>
      <button class="tab-btn" data-tab="attendance"><i class="fas fa-clipboard-check"></i> Attendance</button>
      <button class="tab-btn" data-tab="grading"><i class="fas fa-graduation-cap"></i> Grading</button>
      <button class="tab-btn" data-tab="fees"><i class="fas fa-dollar-sign"></i> Fees</button>
      <button class="tab-btn" data-tab="email"><i class="fas fa-envelope"></i> Email</button>
      <button class="tab-btn" data-tab="notifications"><i class="fas fa-bell"></i> Notifications</button>
      <button class="tab-btn" data-tab="security"><i class="fas fa-shield-alt"></i> Security</button>
      <button class="tab-btn" data-tab="theme"><i class="fas fa-palette"></i> Theme</button>
    </div>

    <!-- General Settings -->
    <div class="tab-content active" id="general">
      <form method="POST">
        <div class="settings-card">
          <h2><i class="fas fa-school"></i> School Information</h2>
          <div class="form-grid">
            <div class="form-group">
              <label><i class="fas fa-building"></i> School Name</label>
              <input type="text" name="school_name" class="form-control" value="<?php echo htmlspecialchars($settings['school_name'] ?? ''); ?>" placeholder="Enter school name">
            </div>
            <div class="form-group">
              <label><i class="fas fa-quote-right"></i> School Motto</label>
              <input type="text" name="school_motto" class="form-control" value="<?php echo htmlspecialchars($settings['school_motto'] ?? ''); ?>" placeholder="Enter school motto">
            </div>
            <div class="form-group">
              <label><i class="fas fa-map-marker-alt"></i> Address</label>
              <input type="text" name="school_address" class="form-control" value="<?php echo htmlspecialchars($settings['school_address'] ?? ''); ?>" placeholder="Enter school address">
            </div>
            <div class="form-group">
              <label><i class="fas fa-phone"></i> Phone Number</label>
              <input type="tel" name="school_phone" class="form-control" value="<?php echo htmlspecialchars($settings['school_phone'] ?? ''); ?>" placeholder="Enter phone number">
            </div>
            <div class="form-group">
              <label><i class="fas fa-envelope"></i> Email Address</label>
              <input type="email" name="school_email" class="form-control" value="<?php echo htmlspecialchars($settings['school_email'] ?? ''); ?>" placeholder="Enter email address">
            </div>
            <div class="form-group">
              <label><i class="fas fa-globe"></i> Website</label>
              <input type="url" name="school_website" class="form-control" value="<?php echo htmlspecialchars($settings['school_website'] ?? ''); ?>" placeholder="https://www.school.edu">
            </div>
            <div class="form-group">
              <label><i class="fas fa-calendar-alt"></i> Academic Year</label>
              <input type="text" name="academic_year" class="form-control" value="<?php echo htmlspecialchars($settings['academic_year'] ?? date('Y') . '/' . (date('Y') + 1)); ?>" placeholder="2024/2025">
            </div>
            <div class="form-group">
              <label><i class="fas fa-calendar"></i> Current Term/Semester</label>
              <select name="current_term" class="form-control">
                <option value="1" <?php echo ($settings['current_term'] ?? '') == '1' ? 'selected' : ''; ?>>First Term</option>
                <option value="2" <?php echo ($settings['current_term'] ?? '') == '2' ? 'selected' : ''; ?>>Second Term</option>
                <option value="3" <?php echo ($settings['current_term'] ?? '') == '3' ? 'selected' : ''; ?>>Third Term</option>
              </select>
            </div>
          </div>
          <button type="submit" name="save_general" class="btn btn-primary">
            <i class="fas fa-save"></i> Save General Settings
          </button>
        </div>
      </form>
    </div>

    <!-- Registration Settings -->
    <div class="tab-content" id="registration">
      <form method="POST">
        <div class="settings-card">
          <h2><i class="fas fa-user-plus"></i> Registration Options</h2>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>Enable Registration</h4>
              <p>Allow new users to register accounts</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="registration_enabled" <?php echo ($settings['registration_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>Email Verification Required</h4>
              <p>Users must verify email before login</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="email_verification_required" <?php echo ($settings['email_verification_required'] ?? '1') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>Admin Approval Required</h4>
              <p>New accounts need admin approval</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="admin_approval_required" <?php echo ($settings['admin_approval_required'] ?? '1') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="form-group" style="margin-top: 1.5rem;">
            <label><i class="fas fa-users"></i> Allowed Registration Roles</label>
            <?php $allowed = explode(',', $settings['allowed_roles'] ?? 'student,parent,teacher'); ?>
            <div class="checkbox-group">
              <div class="checkbox-item">
                <input type="checkbox" name="allowed_roles[]" value="student" <?php echo in_array('student', $allowed) ? 'checked' : ''; ?>>
                <label>Student</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="allowed_roles[]" value="parent" <?php echo in_array('parent', $allowed) ? 'checked' : ''; ?>>
                <label>Parent</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="allowed_roles[]" value="teacher" <?php echo in_array('teacher', $allowed) ? 'checked' : ''; ?>>
                <label>Teacher</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="allowed_roles[]" value="librarian" <?php echo in_array('librarian', $allowed) ? 'checked' : ''; ?>>
                <label>Librarian</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="allowed_roles[]" value="accountant" <?php echo in_array('accountant', $allowed) ? 'checked' : ''; ?>>
                <label>Accountant</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="allowed_roles[]" value="nurse" <?php echo in_array('nurse', $allowed) ? 'checked' : ''; ?>>
                <label>Nurse</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="allowed_roles[]" value="counselor" <?php echo in_array('counselor', $allowed) ? 'checked' : ''; ?>>
                <label>Counselor</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="allowed_roles[]" value="alumni" <?php echo in_array('alumni', $allowed) ? 'checked' : ''; ?>>
                <label>Alumni</label>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label><i class="fas fa-users-class"></i> Max Students Per Class</label>
            <input type="number" name="max_students_per_class" class="form-control" value="<?php echo htmlspecialchars($settings['max_students_per_class'] ?? '40'); ?>" min="1" max="100">
          </div>

          <button type="submit" name="save_registration" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Registration Settings
          </button>
        </div>
      </form>
    </div>

    <!-- Attendance Settings -->
    <div class="tab-content" id="attendance">
      <form method="POST">
        <div class="settings-card">
          <h2><i class="fas fa-clipboard-check"></i> Attendance Configuration</h2>

          <div class="form-grid">
            <div class="form-group">
              <label><i class="fas fa-clock"></i> Attendance Start Time</label>
              <input type="time" name="attendance_start_time" class="form-control" value="<?php echo htmlspecialchars($settings['attendance_start_time'] ?? '08:00'); ?>">
            </div>
            <div class="form-group">
              <label><i class="fas fa-clock"></i> Attendance End Time</label>
              <input type="time" name="attendance_end_time" class="form-control" value="<?php echo htmlspecialchars($settings['attendance_end_time'] ?? '09:00'); ?>">
            </div>
            <div class="form-group">
              <label><i class="fas fa-hourglass-half"></i> Late Threshold (minutes)</label>
              <input type="number" name="late_threshold_minutes" class="form-control" value="<?php echo htmlspecialchars($settings['late_threshold_minutes'] ?? '15'); ?>" min="1" max="60">
            </div>
          </div>

          <h3 style="color: var(--primary); margin: 1.5rem 0 1rem; font-size: 1rem;">Attendance Methods</h3>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>Biometric Attendance</h4>
              <p>Enable fingerprint/face recognition</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="biometric_enabled" <?php echo ($settings['biometric_enabled'] ?? '0') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>QR Code Attendance</h4>
              <p>Allow QR code scanning for attendance</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="qr_code_enabled" <?php echo ($settings['qr_code_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>Manual Attendance</h4>
              <p>Allow teachers to manually mark attendance</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="manual_attendance_enabled" <?php echo ($settings['manual_attendance_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>Parent Notifications</h4>
              <p>Notify parents of attendance status</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="parent_notification_enabled" <?php echo ($settings['parent_notification_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <button type="submit" name="save_attendance" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Attendance Settings
          </button>
        </div>
      </form>
    </div>

    <!-- Grading Settings -->
    <div class="tab-content" id="grading">
      <form method="POST">
        <div class="settings-card">
          <h2><i class="fas fa-graduation-cap"></i> Grading System</h2>

          <div class="form-grid">
            <div class="form-group">
              <label><i class="fas fa-chart-line"></i> Grading System Type</label>
              <select name="grading_system" class="form-control">
                <option value="percentage" <?php echo ($settings['grading_system'] ?? 'percentage') == 'percentage' ? 'selected' : ''; ?>>Percentage</option>
                <option value="letter" <?php echo ($settings['grading_system'] ?? '') == 'letter' ? 'selected' : ''; ?>>Letter Grade</option>
                <option value="gpa" <?php echo ($settings['grading_system'] ?? '') == 'gpa' ? 'selected' : ''; ?>>GPA (4.0 Scale)</option>
              </select>
            </div>
            <div class="form-group">
              <label><i class="fas fa-check-circle"></i> Pass Mark (%)</label>
              <input type="number" name="pass_mark" class="form-control" value="<?php echo htmlspecialchars($settings['pass_mark'] ?? '50'); ?>" min="0" max="100">
            </div>
          </div>

          <h3 style="color: var(--primary); margin: 1.5rem 0 1rem; font-size: 1rem;">Grade Boundaries</h3>

          <div class="form-grid">
            <div class="form-group">
              <label><i class="fas fa-star"></i> Grade A Minimum (%)</label>
              <input type="number" name="grade_a_min" class="form-control" value="<?php echo htmlspecialchars($settings['grade_a_min'] ?? '70'); ?>" min="0" max="100">
            </div>
            <div class="form-group">
              <label><i class="fas fa-star-half-alt"></i> Grade B Minimum (%)</label>
              <input type="number" name="grade_b_min" class="form-control" value="<?php echo htmlspecialchars($settings['grade_b_min'] ?? '60'); ?>" min="0" max="100">
            </div>
            <div class="form-group">
              <label><i class="far fa-star"></i> Grade C Minimum (%)</label>
              <input type="number" name="grade_c_min" class="form-control" value="<?php echo htmlspecialchars($settings['grade_c_min'] ?? '50'); ?>" min="0" max="100">
            </div>
            <div class="form-group">
              <label><i class="far fa-circle"></i> Grade D Minimum (%)</label>
              <input type="number" name="grade_d_min" class="form-control" value="<?php echo htmlspecialchars($settings['grade_d_min'] ?? '40'); ?>" min="0" max="100">
            </div>
          </div>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>Show Class Position</h4>
              <p>Display student ranking in class</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="show_class_position" <?php echo ($settings['show_class_position'] ?? '1') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <button type="submit" name="save_grading" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Grading Settings
          </button>
        </div>
      </form>
    </div>

    <!-- Fee Settings -->
    <div class="tab-content" id="fees">
      <form method="POST">
        <div class="settings-card">
          <h2><i class="fas fa-dollar-sign"></i> Fee Configuration</h2>

          <div class="form-grid">
            <div class="form-group">
              <label><i class="fas fa-coins"></i> Currency</label>
              <select name="currency" class="form-control">
                <option value="USD" <?php echo ($settings['currency'] ?? 'USD') == 'USD' ? 'selected' : ''; ?>>US Dollar (USD)</option>
                <option value="EUR" <?php echo ($settings['currency'] ?? '') == 'EUR' ? 'selected' : ''; ?>>Euro (EUR)</option>
                <option value="GBP" <?php echo ($settings['currency'] ?? '') == 'GBP' ? 'selected' : ''; ?>>British Pound (GBP)</option>
                <option value="NGN" <?php echo ($settings['currency'] ?? '') == 'NGN' ? 'selected' : ''; ?>>Nigerian Naira (NGN)</option>
                <option value="INR" <?php echo ($settings['currency'] ?? '') == 'INR' ? 'selected' : ''; ?>>Indian Rupee (INR)</option>
                <option value="ZAR" <?php echo ($settings['currency'] ?? '') == 'ZAR' ? 'selected' : ''; ?>>South African Rand (ZAR)</option>
              </select>
            </div>
            <div class="form-group">
              <label><i class="fas fa-dollar-sign"></i> Currency Symbol</label>
              <input type="text" name="currency_symbol" class="form-control" value="<?php echo htmlspecialchars($settings['currency_symbol'] ?? '$'); ?>" maxlength="5">
            </div>
            <div class="form-group">
              <label><i class="fas fa-credit-card"></i> Payment Gateway</label>
              <select name="payment_gateway" class="form-control">
                <option value="manual" <?php echo ($settings['payment_gateway'] ?? 'manual') == 'manual' ? 'selected' : ''; ?>>Manual Payment</option>
                <option value="stripe" <?php echo ($settings['payment_gateway'] ?? '') == 'stripe' ? 'selected' : ''; ?>>Stripe</option>
                <option value="paypal" <?php echo ($settings['payment_gateway'] ?? '') == 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                <option value="flutterwave" <?php echo ($settings['payment_gateway'] ?? '') == 'flutterwave' ? 'selected' : ''; ?>>Flutterwave</option>
                <option value="paystack" <?php echo ($settings['payment_gateway'] ?? '') == 'paystack' ? 'selected' : ''; ?>>Paystack</option>
              </select>
            </div>
            <div class="form-group">
              <label><i class="fas fa-percentage"></i> Late Fee Percentage</label>
              <input type="number" name="late_fee_percentage" class="form-control" value="<?php echo htmlspecialchars($settings['late_fee_percentage'] ?? '5'); ?>" min="0" max="50" step="0.5">
            </div>
            <div class="form-group">
              <label><i class="fas fa-bell"></i> Fee Reminder Days Before Due</label>
              <input type="number" name="fee_reminder_days" class="form-control" value="<?php echo htmlspecialchars($settings['fee_reminder_days'] ?? '7'); ?>" min="1" max="30">
            </div>
          </div>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>Online Payment</h4>
              <p>Enable online fee payment</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="online_payment_enabled" <?php echo ($settings['online_payment_enabled'] ?? '0') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <button type="submit" name="save_fees" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Fee Settings
          </button>
        </div>
      </form>
    </div>

    <!-- Email Settings -->
    <div class="tab-content" id="email">
      <form method="POST">
        <div class="settings-card">
          <h2><i class="fas fa-envelope"></i> SMTP Configuration</h2>

          <div class="info-box">
            <p><i class="fas fa-info-circle"></i> Configure your SMTP server for sending system emails (verification, notifications, etc.)</p>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label><i class="fas fa-server"></i> SMTP Host</label>
              <input type="text" name="smtp_host" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>" placeholder="smtp.gmail.com">
            </div>
            <div class="form-group">
              <label><i class="fas fa-plug"></i> SMTP Port</label>
              <input type="number" name="smtp_port" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>" placeholder="587">
            </div>
            <div class="form-group">
              <label><i class="fas fa-user"></i> SMTP Username</label>
              <input type="text" name="smtp_username" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_username'] ?? ''); ?>" placeholder="your-email@gmail.com">
            </div>
            <div class="form-group">
              <label><i class="fas fa-key"></i> SMTP Password</label>
              <input type="password" name="smtp_password" class="form-control" placeholder="Leave blank to keep current">
            </div>
            <div class="form-group">
              <label><i class="fas fa-lock"></i> Encryption</label>
              <select name="smtp_encryption" class="form-control">
                <option value="tls" <?php echo ($settings['smtp_encryption'] ?? 'tls') == 'tls' ? 'selected' : ''; ?>>TLS</option>
                <option value="ssl" <?php echo ($settings['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                <option value="none" <?php echo ($settings['smtp_encryption'] ?? '') == 'none' ? 'selected' : ''; ?>>None</option>
              </select>
            </div>
            <div class="form-group">
              <label><i class="fas fa-envelope"></i> From Email</label>
              <input type="email" name="from_email" class="form-control" value="<?php echo htmlspecialchars($settings['from_email'] ?? ''); ?>" placeholder="noreply@school.edu">
            </div>
            <div class="form-group">
              <label><i class="fas fa-signature"></i> From Name</label>
              <input type="text" name="from_name" class="form-control" value="<?php echo htmlspecialchars($settings['from_name'] ?? ''); ?>" placeholder="School Management System">
            </div>
          </div>

          <button type="submit" name="save_email" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Email Settings
          </button>
        </div>
      </form>
    </div>

    <!-- Notification Settings -->
    <div class="tab-content" id="notifications">
      <form method="POST">
        <div class="settings-card">
          <h2><i class="fas fa-bell"></i> Notification Channels</h2>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>Email Notifications</h4>
              <p>Send notifications via email</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="email_notifications_enabled" <?php echo ($settings['email_notifications_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>SMS Notifications</h4>
              <p>Send notifications via SMS</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="sms_notifications_enabled" <?php echo ($settings['sms_notifications_enabled'] ?? '0') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>Push Notifications</h4>
              <p>Browser push notifications</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="push_notifications_enabled" <?php echo ($settings['push_notifications_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>WhatsApp Notifications</h4>
              <p>Send via WhatsApp Business API</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="whatsapp_enabled" <?php echo ($settings['whatsapp_enabled'] ?? '0') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <h3 style="color: var(--primary); margin: 1.5rem 0 1rem; font-size: 1rem;">Twilio Configuration (SMS/WhatsApp)</h3>

          <div class="form-grid">
            <div class="form-group">
              <label><i class="fas fa-id-card"></i> Twilio Account SID</label>
              <input type="text" name="twilio_sid" class="form-control" value="<?php echo htmlspecialchars($settings['twilio_sid'] ?? ''); ?>" placeholder="ACxxxxxxxxxx">
            </div>
            <div class="form-group">
              <label><i class="fas fa-key"></i> Twilio Auth Token</label>
              <input type="password" name="twilio_token" class="form-control" value="<?php echo htmlspecialchars($settings['twilio_token'] ?? ''); ?>" placeholder="Auth Token">
            </div>
            <div class="form-group">
              <label><i class="fas fa-phone"></i> Twilio Phone Number</label>
              <input type="text" name="twilio_phone" class="form-control" value="<?php echo htmlspecialchars($settings['twilio_phone'] ?? ''); ?>" placeholder="+1234567890">
            </div>
          </div>

          <button type="submit" name="save_notifications" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Notification Settings
          </button>
        </div>
      </form>
    </div>

    <!-- Security Settings -->
    <div class="tab-content" id="security">
      <form method="POST">
        <div class="settings-card">
          <h2><i class="fas fa-shield-alt"></i> Security Configuration</h2>

          <div class="form-grid">
            <div class="form-group">
              <label><i class="fas fa-clock"></i> Session Timeout (seconds)</label>
              <input type="number" name="session_timeout" class="form-control" value="<?php echo htmlspecialchars($settings['session_timeout'] ?? '1800'); ?>" min="300" max="86400">
              <small style="color: rgba(255,255,255,0.5);">Default: 1800 (30 minutes)</small>
            </div>
            <div class="form-group">
              <label><i class="fas fa-ban"></i> Max Login Attempts</label>
              <input type="number" name="max_login_attempts" class="form-control" value="<?php echo htmlspecialchars($settings['max_login_attempts'] ?? '5'); ?>" min="3" max="10">
            </div>
            <div class="form-group">
              <label><i class="fas fa-lock"></i> Lockout Duration (seconds)</label>
              <input type="number" name="lockout_duration" class="form-control" value="<?php echo htmlspecialchars($settings['lockout_duration'] ?? '900'); ?>" min="60" max="3600">
              <small style="color: rgba(255,255,255,0.5);">Default: 900 (15 minutes)</small>
            </div>
            <div class="form-group">
              <label><i class="fas fa-key"></i> Minimum Password Length</label>
              <input type="number" name="password_min_length" class="form-control" value="<?php echo htmlspecialchars($settings['password_min_length'] ?? '8'); ?>" min="6" max="32">
            </div>
          </div>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>Two-Factor Authentication</h4>
              <p>Require 2FA for admin accounts</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="two_factor_enabled" <?php echo ($settings['two_factor_enabled'] ?? '0') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>Audit Logging</h4>
              <p>Log all administrative actions</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="audit_logging_enabled" <?php echo ($settings['audit_logging_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <button type="submit" name="save_security" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Security Settings
          </button>
        </div>
      </form>
    </div>

    <!-- Theme Settings -->
    <div class="tab-content" id="theme">
      <form method="POST">
        <div class="settings-card">
          <h2><i class="fas fa-palette"></i> Theme & Appearance</h2>

          <div class="form-grid">
            <div class="form-group">
              <label><i class="fas fa-brush"></i> Default Theme</label>
              <select name="default_theme" class="form-control">
                <option value="cyberpunk" <?php echo ($settings['default_theme'] ?? 'cyberpunk') == 'cyberpunk' ? 'selected' : ''; ?>>Cyberpunk (Dark Neon)</option>
                <option value="nature" <?php echo ($settings['default_theme'] ?? '') == 'nature' ? 'selected' : ''; ?>>Nature (Green)</option>
                <option value="classic" <?php echo ($settings['default_theme'] ?? '') == 'classic' ? 'selected' : ''; ?>>Classic (Professional)</option>
              </select>
            </div>
            <div class="form-group">
              <label><i class="fas fa-image"></i> Logo URL</label>
              <input type="url" name="logo_url" class="form-control" value="<?php echo htmlspecialchars($settings['logo_url'] ?? ''); ?>" placeholder="https://example.com/logo.png">
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label><i class="fas fa-paint-brush"></i> Primary Color</label>
              <div class="color-input-group">
                <input type="color" name="primary_color" value="<?php echo htmlspecialchars($settings['primary_color'] ?? '#00BFFF'); ?>">
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($settings['primary_color'] ?? '#00BFFF'); ?>" readonly>
              </div>
            </div>
            <div class="form-group">
              <label><i class="fas fa-fill-drip"></i> Secondary Color</label>
              <div class="color-input-group">
                <input type="color" name="secondary_color" value="<?php echo htmlspecialchars($settings['secondary_color'] ?? '#8A2BE2'); ?>">
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($settings['secondary_color'] ?? '#8A2BE2'); ?>" readonly>
              </div>
            </div>
          </div>

          <div class="toggle-group">
            <div class="toggle-info">
              <h4>Allow User Theme Selection</h4>
              <p>Let users choose their own theme</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" name="allow_user_theme" <?php echo ($settings['allow_user_theme'] ?? '1') == '1' ? 'checked' : ''; ?>>
              <span class="toggle-slider"></span>
            </label>
          </div>

          <button type="submit" name="save_theme" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Theme Settings
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        // Remove active class from all tabs and contents
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

        // Add active class to clicked tab and corresponding content
        btn.classList.add('active');
        document.getElementById(btn.dataset.tab).classList.add('active');
      });
    });

    // Color input sync
    document.querySelectorAll('input[type="color"]').forEach(colorInput => {
      colorInput.addEventListener('input', (e) => {
        const textInput = e.target.parentElement.querySelector('input[type="text"]');
        if (textInput) {
          textInput.value = e.target.value;
        }
      });
    });
  </script>

  <script src="../assets/js/main.js"></script>
</body>

</html>
