<?php

/**
 * Verdant SMS - Cyberpunk Registration Portal
 * Pure neon. Pure art. Zero white backgrounds.
 * @version 3.0-evergreen
 */
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

$errors = [];
$success = '';
$form_data = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'role' => 'student',
    'student_id' => '',
    'grade_level' => '',
    'department' => '',
    'subject' => ''
];

// Process registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $form_data = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'role' => $_POST['role'] ?? 'student',
        'student_id' => trim($_POST['student_id'] ?? ''),
        'grade_level' => trim($_POST['grade_level'] ?? ''),
        'department' => trim($_POST['department'] ?? ''),
        'subject' => trim($_POST['subject'] ?? '')
    ];
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($form_data['first_name'])) {
        $errors[] = 'First name is required';
    }
    if (empty($form_data['last_name'])) {
        $errors[] = 'Last name is required';
    }
    if (empty($form_data['email']) || !filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }

    // Role-specific validation
    $allowed_roles = ['student', 'teacher', 'parent', 'alumni'];
    if (!in_array($form_data['role'], $allowed_roles)) {
        $errors[] = 'Invalid role selected';
    }

    if ($form_data['role'] === 'student') {
        if (empty($form_data['student_id'])) {
            $errors[] = 'Student ID is required';
        }
        if (empty($form_data['grade_level'])) {
            $errors[] = 'Grade level is required';
        }
    }

    if ($form_data['role'] === 'teacher') {
        if (empty($form_data['department'])) {
            $errors[] = 'Department is required';
        }
        if (empty($form_data['subject'])) {
            $errors[] = 'Subject specialization is required';
        }
    }

    // Check for existing email
    if (empty($errors)) {
        try {
            $existing = db()->fetchOne("SELECT id FROM users WHERE email = ?", [$form_data['email']]);
            if ($existing) {
                $errors[] = 'Email is already registered';
            }
        } catch (Exception $e) {
            $errors[] = 'Database error. Please try again.';
        }
    }

    // Create user
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verification_token = bin2hex(random_bytes(32));

            $user_data = [
                'first_name' => $form_data['first_name'],
                'last_name' => $form_data['last_name'],
                'email' => $form_data['email'],
                'phone' => $form_data['phone'],
                'password' => $hashed_password,
                'role' => $form_data['role'],
                'status' => 'pending',
                'email_verified' => 0,
                'verification_token' => $verification_token,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $user_id = db()->insert('users', $user_data);

            // Create role-specific record
            if ($form_data['role'] === 'student' && $user_id) {
                db()->insert('students', [
                    'user_id' => $user_id,
                    'student_id' => $form_data['student_id'],
                    'grade_level' => $form_data['grade_level'],
                    'enrollment_date' => date('Y-m-d'),
                    'status' => 'pending'
                ]);
            } elseif ($form_data['role'] === 'teacher' && $user_id) {
                db()->insert('teachers', [
                    'user_id' => $user_id,
                    'department' => $form_data['department'],
                    'subject_specialization' => $form_data['subject'],
                    'join_date' => date('Y-m-d'),
                    'status' => 'pending'
                ]);
            }

            // Send verification email
            $verify_url = getenv('APP_URL') . "/verify-email.php?token=" . $verification_token;
            $email_sent = send_email(
                $form_data['email'],
                'Verify Your Verdant SMS Account',
                "Welcome to Verdant SMS!\n\nPlease verify your email by clicking:\n{$verify_url}\n\nThis link expires in 24 hours."
            );

            $success = 'Account created! Check your email to verify your account.';
            $form_data = [
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'phone' => '',
                'role' => 'student',
                'student_id' => '',
                'grade_level' => '',
                'department' => '',
                'subject' => ''
            ];
        } catch (Exception $e) {
            $errors[] = 'Registration failed. Please try again.';
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Verdant SMS | Cyberpunk Registration</title>
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
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ═══════════════════════════════════════════════════════════════
           VERDANT SMS - CYBERPUNK REGISTRATION
           No white. No boring. Pure neon art.
           ═══════════════════════════════════════════════════════════════ */

        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --neon-cyan: #00f5ff;
            --neon-purple: #bf00ff;
            --neon-pink: #ff006e;
            --neon-green: #00ff88;
            --neon-orange: #ff9500;
            --dark-bg: #0a0a0f;
            --darker-bg: #050508;
            --card-bg: rgba(10, 10, 20, 0.85);
            --glass-border: rgba(0, 245, 255, 0.2);
            --text-primary: #e0e0ff;
            --text-secondary: #8888aa;
        }

        html,
        body {
            height: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Rajdhani', sans-serif;
            background: var(--darker-bg);
            color: var(--text-primary);
            min-height: 100vh;
            position: relative;
        }

        /* ═══ Animated Grid Background ═══ */
        .cyber-grid-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .cyber-grid-bg::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            background:
                linear-gradient(90deg, rgba(0, 245, 255, 0.03) 1px, transparent 1px) 0 0 / 60px 60px,
                linear-gradient(rgba(0, 245, 255, 0.03) 1px, transparent 1px) 0 0 / 60px 60px;
            animation: gridMove 20s linear infinite;
        }

        .cyber-grid-bg::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(ellipse at 20% 30%, rgba(191, 0, 255, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 70%, rgba(0, 245, 255, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(0, 255, 136, 0.05) 0%, transparent 70%);
        }

        @keyframes gridMove {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(60px, 60px);
            }
        }

        /* ═══ Floating Particles ═══ */
        .particles {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--neon-cyan);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--neon-cyan), 0 0 20px var(--neon-cyan);
            animation: float 15s infinite ease-in-out;
        }

        .particle:nth-child(2n) {
            background: var(--neon-purple);
            box-shadow: 0 0 10px var(--neon-purple);
        }

        .particle:nth-child(3n) {
            background: var(--neon-pink);
            box-shadow: 0 0 10px var(--neon-pink);
        }

        .particle:nth-child(5n) {
            background: var(--neon-green);
            box-shadow: 0 0 10px var(--neon-green);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }

            10% {
                opacity: 1;
                transform: scale(1);
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translateY(-10vh) scale(0);
                opacity: 0;
            }
        }

        /* ═══ Main Container ═══ */
        .register-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* ═══ Glassmorphic Form Card ═══ */
        .register-card {
            width: 100%;
            max-width: 680px;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            padding: 3rem;
            position: relative;
            overflow: hidden;
            box-shadow:
                0 0 60px rgba(0, 245, 255, 0.1),
                0 0 100px rgba(191, 0, 255, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        /* Animated border glow */
        .register-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg,
                    var(--neon-cyan), var(--neon-purple), var(--neon-pink),
                    var(--neon-green), var(--neon-cyan));
            background-size: 400% 400%;
            border-radius: 26px;
            z-index: -1;
            animation: borderGlow 8s ease infinite;
            opacity: 0.7;
        }

        .register-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--card-bg);
            border-radius: 24px;
            z-index: -1;
        }

        @keyframes borderGlow {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* ═══ Logo & Title ═══ */
        .brand-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--neon-green), var(--neon-cyan));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--dark-bg);
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.4);
            animation: logoPulse 3s ease-in-out infinite;
        }

        @keyframes logoPulse {

            0%,
            100% {
                box-shadow: 0 0 30px rgba(0, 255, 136, 0.4);
            }

            50% {
                box-shadow: 0 0 50px rgba(0, 245, 255, 0.6);
            }
        }

        .brand-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.2rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--neon-cyan), var(--neon-purple), var(--neon-pink));
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: titleShine 3s linear infinite;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        @keyframes titleShine {
            0% {
                background-position: 0% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        .brand-subtitle {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin-top: 0.5rem;
            letter-spacing: 2px;
        }

        /* ═══ Alerts ═══ */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            animation: alertSlide 0.4s ease;
        }

        @keyframes alertSlide {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background: rgba(255, 0, 110, 0.15);
            border: 1px solid var(--neon-pink);
            color: #ff6b9d;
        }

        .alert-success {
            background: rgba(0, 255, 136, 0.15);
            border: 1px solid var(--neon-green);
            color: #00ff88;
        }

        .alert i {
            font-size: 1.25rem;
            margin-top: 2px;
        }

        .alert ul {
            margin: 0;
            padding-left: 1rem;
        }

        .alert li {
            margin: 0.25rem 0;
        }

        /* ═══ Role Selector ═══ */
        .role-selector {
            margin-bottom: 2rem;
        }

        .role-selector-label {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.9rem;
            color: var(--neon-cyan);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1rem;
            display: block;
        }

        .role-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        @media (max-width: 600px) {
            .role-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .role-card {
            position: relative;
        }

        .role-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .role-card label {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.25rem 0.75rem;
            background: rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .role-card label:hover {
            border-color: rgba(0, 245, 255, 0.5);
            background: rgba(0, 245, 255, 0.05);
        }

        .role-card input:checked+label {
            border-color: var(--neon-cyan);
            background: rgba(0, 245, 255, 0.1);
            box-shadow: 0 0 25px rgba(0, 245, 255, 0.3), inset 0 0 20px rgba(0, 245, 255, 0.05);
        }

        .role-card .role-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .role-card.student .role-icon {
            background: linear-gradient(135deg, #00f5ff, #0088ff);
            color: #000;
        }

        .role-card.teacher .role-icon {
            background: linear-gradient(135deg, #bf00ff, #8000ff);
            color: #fff;
        }

        .role-card.parent .role-icon {
            background: linear-gradient(135deg, #ff006e, #ff4d94);
            color: #fff;
        }

        .role-card.alumni .role-icon {
            background: linear-gradient(135deg, #00ff88, #00cc6a);
            color: #000;
        }

        .role-card input:checked+label .role-icon {
            transform: scale(1.1);
            box-shadow: 0 0 20px currentColor;
        }

        .role-card .role-name {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
            transition: color 0.3s;
        }

        .role-card input:checked+label .role-name {
            color: var(--neon-cyan);
        }

        /* ═══ Form Groups ═══ */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        @media (max-width: 500px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: color 0.3s;
        }

        .form-group:focus-within .form-label {
            color: var(--neon-cyan);
        }

        .form-input {
            width: 100%;
            padding: 0.9rem 1rem;
            padding-left: 2.75rem;
            background: rgba(0, 0, 0, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text-primary);
            font-family: 'Rajdhani', sans-serif;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--neon-cyan);
            box-shadow: 0 0 20px rgba(0, 245, 255, 0.2), inset 0 0 10px rgba(0, 245, 255, 0.05);
            background: rgba(0, 245, 255, 0.05);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            bottom: 0.95rem;
            color: var(--text-secondary);
            transition: color 0.3s;
        }

        .form-group:focus-within .input-icon {
            color: var(--neon-cyan);
        }

        /* ═══ Role-Specific Fields ═══ */
        .role-fields {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .role-fields.active {
            display: block;
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

        .section-divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            gap: 1rem;
        }

        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--glass-border), transparent);
        }

        .section-divider span {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.75rem;
            color: var(--neon-purple);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* ═══ Select Dropdown ═══ */
        .form-select {
            width: 100%;
            padding: 0.9rem 1rem;
            padding-left: 2.75rem;
            background: rgba(0, 0, 0, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text-primary);
            font-family: 'Rajdhani', sans-serif;
            font-size: 1rem;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%2300f5ff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--neon-cyan);
            box-shadow: 0 0 20px rgba(0, 245, 255, 0.2);
        }

        .form-select option {
            background: #1a1a2e;
            color: var(--text-primary);
        }

        /* ═══ Password Strength ═══ */
        .password-strength {
            margin-top: 0.5rem;
            display: flex;
            gap: 4px;
        }

        .strength-bar {
            flex: 1;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            transition: background 0.3s;
        }

        .strength-bar.weak {
            background: #ff006e;
        }

        .strength-bar.medium {
            background: #ff9500;
        }

        .strength-bar.strong {
            background: #00ff88;
        }

        /* ═══ Submit Button ═══ */
        .submit-btn {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--neon-cyan), var(--neon-purple));
            border: none;
            border-radius: 12px;
            color: #000;
            font-family: 'Orbitron', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s ease;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(0, 245, 255, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn i {
            margin-left: 0.5rem;
        }

        /* ═══ Footer Links ═══ */
        .form-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--glass-border);
        }

        .form-footer p {
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .form-footer a {
            color: var(--neon-cyan);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .form-footer a:hover {
            color: var(--neon-purple);
            text-shadow: 0 0 10px var(--neon-purple);
        }

        /* ═══ Success Animation ═══ */
        .success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            animation: fadeIn 0.5s ease;
        }

        .success-content {
            text-align: center;
            animation: scaleIn 0.5s ease 0.2s both;
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.5);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .success-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, var(--neon-green), var(--neon-cyan));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #000;
            animation: successPulse 1.5s ease-in-out infinite;
        }

        @keyframes successPulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(0, 255, 136, 0.4);
            }

            50% {
                box-shadow: 0 0 0 30px rgba(0, 255, 136, 0);
            }
        }

        .success-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            color: var(--neon-green);
            margin-bottom: 1rem;
        }

        .success-message {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .success-btn {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: linear-gradient(135deg, var(--neon-green), var(--neon-cyan));
            border-radius: 12px;
            color: #000;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s;
        }

        .success-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.5);
        }

        /* ═══ Decorative Elements ═══ */
        .corner-decoration {
            position: absolute;
            width: 60px;
            height: 60px;
            border: 2px solid transparent;
        }

        .corner-decoration.top-left {
            top: -1px;
            left: -1px;
            border-top-color: var(--neon-cyan);
            border-left-color: var(--neon-cyan);
            border-radius: 24px 0 0 0;
        }

        .corner-decoration.top-right {
            top: -1px;
            right: -1px;
            border-top-color: var(--neon-purple);
            border-right-color: var(--neon-purple);
            border-radius: 0 24px 0 0;
        }

        .corner-decoration.bottom-left {
            bottom: -1px;
            left: -1px;
            border-bottom-color: var(--neon-pink);
            border-left-color: var(--neon-pink);
            border-radius: 0 0 0 24px;
        }

        .corner-decoration.bottom-right {
            bottom: -1px;
            right: -1px;
            border-bottom-color: var(--neon-green);
            border-right-color: var(--neon-green);
            border-radius: 0 0 24px 0;
        }

        /* ═══ Loading State ═══ */
        .submit-btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .submit-btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top-color: #000;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ═══ Responsive ═══ */
        @media (max-width: 768px) {
            .register-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }

            .brand-title {
                font-size: 1.6rem;
                letter-spacing: 1px;
            }

            .brand-logo {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Animated Background -->
    <div class="cyber-grid-bg"></div>

    <!-- Floating Particles -->
    <div class="particles">
        <?php for ($i = 0; $i < 20; $i++): ?>
            <div class="particle" style="left: <?= rand(0, 100) ?>%; animation-delay: <?= rand(0, 15) ?>s; animation-duration: <?= rand(12, 20) ?>s;"></div>
        <?php endfor; ?>
    </div>

    <!-- Registration Container -->
    <div class="register-container">
        <div class="register-card">
            <!-- Corner Decorations -->
            <div class="corner-decoration top-left"></div>
            <div class="corner-decoration top-right"></div>
            <div class="corner-decoration bottom-left"></div>
            <div class="corner-decoration bottom-right"></div>

            <!-- Brand Header -->
            <div class="brand-header">
                <div class="brand-logo">
                    <i class="fas fa-leaf"></i>
                </div>
                <h1 class="brand-title">Join Verdant</h1>
                <p class="brand-subtitle">Create Your Digital Identity</p>
            </div>

            <!-- Alerts -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success-overlay" id="successOverlay">
                    <div class="success-content">
                        <div class="success-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <h2 class="success-title">Welcome Aboard!</h2>
                        <p class="success-message"><?= htmlspecialchars($success) ?></p>
                        <a href="../login.php" class="success-btn">Go to Login <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form method="POST" action="" id="registerForm">
                <!-- Role Selector -->
                <div class="role-selector">
                    <label class="role-selector-label">Select Your Role</label>
                    <div class="role-cards">
                        <div class="role-card student">
                            <input type="radio" name="role" id="role_student" value="student"
                                <?= $form_data['role'] === 'student' ? 'checked' : '' ?>>
                            <label for="role_student">
                                <div class="role-icon"><i class="fas fa-graduation-cap"></i></div>
                                <span class="role-name">Student</span>
                            </label>
                        </div>
                        <div class="role-card teacher">
                            <input type="radio" name="role" id="role_teacher" value="teacher"
                                <?= $form_data['role'] === 'teacher' ? 'checked' : '' ?>>
                            <label for="role_teacher">
                                <div class="role-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                                <span class="role-name">Teacher</span>
                            </label>
                        </div>
                        <div class="role-card parent">
                            <input type="radio" name="role" id="role_parent" value="parent"
                                <?= $form_data['role'] === 'parent' ? 'checked' : '' ?>>
                            <label for="role_parent">
                                <div class="role-icon"><i class="fas fa-users"></i></div>
                                <span class="role-name">Parent</span>
                            </label>
                        </div>
                        <div class="role-card alumni">
                            <input type="radio" name="role" id="role_alumni" value="alumni"
                                <?= $form_data['role'] === 'alumni' ? 'checked' : '' ?>>
                            <label for="role_alumni">
                                <div class="role-icon"><i class="fas fa-user-graduate"></i></div>
                                <span class="role-name">Alumni</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="section-divider">
                    <span>Personal Info</span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="first_name">First Name</label>
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="first_name" name="first_name" class="form-input"
                            placeholder="John" value="<?= htmlspecialchars($form_data['first_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="last_name">Last Name</label>
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="last_name" name="last_name" class="form-input"
                            placeholder="Doe" value="<?= htmlspecialchars($form_data['last_name']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" class="form-input"
                            placeholder="john@example.com" value="<?= htmlspecialchars($form_data['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <i class="fas fa-phone input-icon"></i>
                        <input type="tel" id="phone" name="phone" class="form-input"
                            placeholder="+1 234 567 890" value="<?= htmlspecialchars($form_data['phone']) ?>">
                    </div>
                </div>

                <!-- Student-Specific Fields -->
                <div class="role-fields" id="studentFields">
                    <div class="section-divider">
                        <span>Student Details</span>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="student_id">Student ID</label>
                            <i class="fas fa-id-card input-icon"></i>
                            <input type="text" id="student_id" name="student_id" class="form-input"
                                placeholder="STU-2025-001" value="<?= htmlspecialchars($form_data['student_id']) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="grade_level">Grade Level</label>
                            <i class="fas fa-layer-group input-icon"></i>
                            <select id="grade_level" name="grade_level" class="form-select">
                                <option value="">Select Grade</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>" <?= $form_data['grade_level'] == $i ? 'selected' : '' ?>>Grade <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Teacher-Specific Fields -->
                <div class="role-fields" id="teacherFields">
                    <div class="section-divider">
                        <span>Teacher Details</span>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="department">Department</label>
                            <i class="fas fa-building input-icon"></i>
                            <select id="department" name="department" class="form-select">
                                <option value="">Select Department</option>
                                <option value="science" <?= $form_data['department'] === 'science' ? 'selected' : '' ?>>Science</option>
                                <option value="mathematics" <?= $form_data['department'] === 'mathematics' ? 'selected' : '' ?>>Mathematics</option>
                                <option value="english" <?= $form_data['department'] === 'english' ? 'selected' : '' ?>>English</option>
                                <option value="social_studies" <?= $form_data['department'] === 'social_studies' ? 'selected' : '' ?>>Social Studies</option>
                                <option value="arts" <?= $form_data['department'] === 'arts' ? 'selected' : '' ?>>Arts</option>
                                <option value="physical_education" <?= $form_data['department'] === 'physical_education' ? 'selected' : '' ?>>Physical Education</option>
                                <option value="computer_science" <?= $form_data['department'] === 'computer_science' ? 'selected' : '' ?>>Computer Science</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="subject">Subject Specialization</label>
                            <i class="fas fa-book input-icon"></i>
                            <input type="text" id="subject" name="subject" class="form-input"
                                placeholder="e.g., Physics, Calculus" value="<?= htmlspecialchars($form_data['subject']) ?>">
                        </div>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="section-divider">
                    <span>Security</span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" class="form-input"
                            placeholder="••••••••" required minlength="8">
                        <div class="password-strength" id="passwordStrength">
                            <div class="strength-bar" id="str1"></div>
                            <div class="strength-bar" id="str2"></div>
                            <div class="strength-bar" id="str3"></div>
                            <div class="strength-bar" id="str4"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm Password</label>
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn" id="submitBtn">
                    <span>Create Account</span>
                    <i class="fas fa-rocket"></i>
                </button>

                <!-- Footer -->
                <div class="form-footer">
                    <p>Already have an account?</p>
                    <a href="../login.php">Sign In <i class="fas fa-arrow-right"></i></a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ═══ Role Field Toggle ═══
        const roleInputs = document.querySelectorAll('input[name="role"]');
        const studentFields = document.getElementById('studentFields');
        const teacherFields = document.getElementById('teacherFields');

        function updateRoleFields() {
            const selectedRole = document.querySelector('input[name="role"]:checked')?.value;

            studentFields.classList.remove('active');
            teacherFields.classList.remove('active');

            if (selectedRole === 'student') {
                studentFields.classList.add('active');
            } else if (selectedRole === 'teacher') {
                teacherFields.classList.add('active');
            }
        }

        roleInputs.forEach(input => {
            input.addEventListener('change', updateRoleFields);
        });

        // Initialize on load
        updateRoleFields();

        // ═══ Password Strength Indicator ═══
        const passwordInput = document.getElementById('password');
        const strengthBars = document.querySelectorAll('.strength-bar');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            strengthBars.forEach((bar, index) => {
                bar.classList.remove('weak', 'medium', 'strong');
                if (index < strength) {
                    if (strength <= 1) bar.classList.add('weak');
                    else if (strength <= 2) bar.classList.add('medium');
                    else bar.classList.add('strong');
                }
            });
        });

        // ═══ Form Submit Animation ═══
        const form = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.querySelector('span').textContent = 'Creating...';
        });

        // ═══ Input Focus Effects ═══
        document.querySelectorAll('.form-input, .form-select').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.form-group').classList.add('focused');
            });
            input.addEventListener('blur', function() {
                this.closest('.form-group').classList.remove('focused');
            });
        });

        // ═══ Confetti on Success ═══
        <?php if (!empty($success)): ?>

            function createConfetti() {
                const colors = ['#00f5ff', '#bf00ff', '#ff006e', '#00ff88', '#ff9500'];
                const container = document.getElementById('successOverlay');

                for (let i = 0; i < 100; i++) {
                    const confetti = document.createElement('div');
                    confetti.style.cssText = `
                    position: absolute;
                    width: ${Math.random() * 10 + 5}px;
                    height: ${Math.random() * 10 + 5}px;
                    background: ${colors[Math.floor(Math.random() * colors.length)]};
                    left: ${Math.random() * 100}%;
                    top: -20px;
                    border-radius: ${Math.random() > 0.5 ? '50%' : '0'};
                    animation: confettiFall ${Math.random() * 3 + 2}s linear forwards;
                    animation-delay: ${Math.random() * 0.5}s;
                `;
                    container.appendChild(confetti);
                }
            }

            // Add confetti animation
            const style = document.createElement('style');
            style.textContent = `
            @keyframes confettiFall {
                to {
                    transform: translateY(100vh) rotate(${Math.random() * 720}deg);
                    opacity: 0;
                }
            }
        `;
            document.head.appendChild(style);

            setTimeout(createConfetti, 500);
        <?php endif; ?>
    </script>
</body>

</html>