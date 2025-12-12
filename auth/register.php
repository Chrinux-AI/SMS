<?php

/**
 * Verdant SMS - Student Registration Portal
 * SECURITY: Only Students can register publicly
 * All other roles created by Admin only
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
    'student_id' => '',
    'grade_level' => '',
    'parent_name' => '',
    'parent_phone' => '',
    'parent_email' => '',
    'address' => ''
];

// Process registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please refresh and try again.';
    } else {
        // Sanitize inputs
        $form_data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'student_id' => trim($_POST['student_id'] ?? ''),
            'grade_level' => trim($_POST['grade_level'] ?? ''),
            'parent_name' => trim($_POST['parent_name'] ?? ''),
            'parent_phone' => trim($_POST['parent_phone'] ?? ''),
            'parent_email' => trim($_POST['parent_email'] ?? ''),
            'address' => trim($_POST['address'] ?? '')
        ];
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validation - Student Info
        if (empty($form_data['first_name'])) {
            $errors[] = 'First name is required';
        }
        if (empty($form_data['last_name'])) {
            $errors[] = 'Last name is required';
        }
        if (empty($form_data['email']) || !filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid student email is required';
        }
        if (empty($form_data['student_id'])) {
            $errors[] = 'Student ID / Enrollment Number is required';
        }
        if (empty($form_data['grade_level'])) {
            $errors[] = 'Grade level is required';
        }

        // Validation - Parent/Guardian Info (Required for verification)
        if (empty($form_data['parent_name'])) {
            $errors[] = 'Parent/Guardian name is required';
        }
        if (empty($form_data['parent_phone'])) {
            $errors[] = 'Parent/Guardian phone is required';
        }

        // Password validation
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }
        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match';
        }

        // Check for existing email
        if (empty($errors)) {
            try {
                $existing = db()->fetchOne("SELECT id FROM users WHERE email = ?", [$form_data['email']]);
                if ($existing) {
                    $errors[] = 'This email is already registered';
                }

                // Check for existing student ID
                $existing_sid = db()->fetchOne("SELECT id FROM students WHERE student_id = ?", [$form_data['student_id']]);
                if ($existing_sid) {
                    $errors[] = 'This Student ID is already registered';
                }
            } catch (Exception $e) {
                $errors[] = 'Database error. Please try again.';
            }
        }

        // Create pending user account
        if (empty($errors)) {
            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $verification_token = bin2hex(random_bytes(32));

                // Insert user with PENDING status - Admin must approve
                $user_id = db()->insert('users', [
                    'first_name' => $form_data['first_name'],
                    'last_name' => $form_data['last_name'],
                    'full_name' => $form_data['first_name'] . ' ' . $form_data['last_name'],
                    'email' => $form_data['email'],
                    'phone' => $form_data['phone'],
                    'password_hash' => $hashed_password,
                    'role' => 'student',
                    'status' => 'pending',  // CRITICAL: Pending until Admin approves
                    'email_verified' => 0,
                    'approved' => 0,  // Not approved - Admin must review
                    'verification_token' => $verification_token,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                if ($user_id) {
                    // Create student record with parent info
                    db()->insert('students', [
                        'user_id' => $user_id,
                        'student_id' => $form_data['student_id'],
                        'grade_level' => $form_data['grade_level'],
                        'parent_name' => $form_data['parent_name'],
                        'parent_phone' => $form_data['parent_phone'],
                        'parent_email' => $form_data['parent_email'],
                        'address' => $form_data['address'],
                        'enrollment_date' => date('Y-m-d'),
                        'status' => 'pending'
                    ]);

                    // Send verification email
                    $verify_url = (getenv('APP_URL') ?: 'http://localhost/attendance') . "/verify-email.php?token=" . $verification_token;
                    send_email(
                        $form_data['email'],
                        'Verify Your Verdant SMS Account',
                        "Hello " . $form_data['first_name'] . ",\n\n" .
                            "Welcome to Verdant School Management System!\n\n" .
                            "Please verify your email by clicking:\n{$verify_url}\n\n" .
                            "After verification, your account will be reviewed by an administrator.\n" .
                            "You will receive a notification once approved.\n\n" .
                            "This link expires in 24 hours."
                    );

                    $success = true;
                }
            } catch (Exception $e) {
                $errors[] = 'Registration failed. Please try again.';
                error_log("Registration error: " . $e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration | Verdant SMS</title>
    <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --neon-cyan: #00f5ff;
            --neon-green: #00ff88;
            --neon-purple: #bf00ff;
            --neon-pink: #ff006e;
            --dark-bg: #0a0a0f;
            --card-bg: rgba(10, 10, 20, 0.9);
            --glass-border: rgba(0, 245, 255, 0.2);
            --text-primary: #e0e0ff;
            --text-secondary: #8888aa;
            --error-red: #ff4757;
            --success-green: #00ff88;
        }

        html,
        body {
            height: 100%;
            overflow-x: hidden;
            font-family: 'Rajdhani', sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
        }

        /* Animated Grid Background */
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
                radial-gradient(ellipse at 20% 30%, rgba(0, 255, 136, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 70%, rgba(0, 245, 255, 0.1) 0%, transparent 50%);
        }

        @keyframes gridMove {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(60px, 60px);
            }
        }

        /* Main Container */
        .register-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* Form Card */
        .register-card {
            width: 100%;
            max-width: 600px;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            padding: 2.5rem;
            position: relative;
            box-shadow: 0 0 60px rgba(0, 255, 136, 0.1);
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--neon-green), var(--neon-cyan), var(--neon-green));
            background-size: 400% 400%;
            border-radius: 26px;
            z-index: -1;
            animation: borderGlow 6s ease infinite;
            opacity: 0.6;
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

        /* Header */
        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--neon-green), var(--neon-cyan));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--dark-bg);
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.4);
        }

        .brand-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--neon-green), var(--neon-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .brand-subtitle {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-top: 0.5rem;
        }

        /* Security Notice */
        .security-notice {
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid var(--neon-green);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .security-notice i {
            color: var(--neon-green);
            font-size: 1.2rem;
            margin-top: 2px;
        }

        .security-notice p {
            font-size: 0.9rem;
            color: var(--neon-green);
            line-height: 1.5;
        }

        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .alert-error {
            background: rgba(255, 71, 87, 0.15);
            border: 1px solid var(--error-red);
            color: var(--error-red);
        }

        .alert ul {
            margin: 0;
            padding-left: 1rem;
        }

        .alert li {
            margin: 0.25rem 0;
        }

        /* Section Dividers */
        .section-header {
            display: flex;
            align-items: center;
            margin: 1.5rem 0 1rem;
            gap: 1rem;
        }

        .section-header::before,
        .section-header::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--glass-border), transparent);
        }

        .section-header span {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.75rem;
            color: var(--neon-cyan);
            text-transform: uppercase;
            letter-spacing: 2px;
            white-space: nowrap;
        }

        /* Form Groups */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 500px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-label .required {
            color: var(--error-red);
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.8rem 1rem;
            padding-left: 2.5rem;
            background: rgba(0, 0, 0, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: var(--text-primary);
            font-family: 'Rajdhani', sans-serif;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--neon-cyan);
            box-shadow: 0 0 15px rgba(0, 245, 255, 0.2);
            background: rgba(0, 245, 255, 0.05);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .input-icon {
            position: absolute;
            left: 0.8rem;
            bottom: 0.85rem;
            color: var(--text-secondary);
        }

        .form-group:focus-within .input-icon {
            color: var(--neon-cyan);
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%2300f5ff' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.8rem center;
            background-size: 1rem;
        }

        .form-select option {
            background: #1a1a2e;
            color: var(--text-primary);
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--neon-green), var(--neon-cyan));
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

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(0, 255, 136, 0.4);
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

        /* Footer */
        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--glass-border);
            color: var(--text-secondary);
        }

        .form-footer a {
            color: var(--neon-cyan);
            text-decoration: none;
            font-weight: 600;
        }

        .form-footer a:hover {
            text-shadow: 0 0 10px var(--neon-cyan);
        }

        /* Success Screen */
        .success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .success-content {
            text-align: center;
            max-width: 500px;
            padding: 2rem;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, var(--neon-green), var(--neon-cyan));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #000;
            animation: successPulse 1.5s ease-in-out infinite;
        }

        @keyframes successPulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(0, 255, 136, 0.4);
            }

            50% {
                box-shadow: 0 0 0 25px rgba(0, 255, 136, 0);
            }
        }

        .success-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            color: var(--neon-green);
            margin-bottom: 1rem;
        }

        .success-message {
            color: var(--text-secondary);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .success-steps {
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid var(--neon-green);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: left;
            margin-bottom: 1.5rem;
        }

        .success-steps h4 {
            color: var(--neon-green);
            margin-bottom: 1rem;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.9rem;
        }

        .success-steps ol {
            color: var(--text-primary);
            padding-left: 1.5rem;
        }

        .success-steps li {
            margin-bottom: 0.5rem;
        }

        .success-btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: transparent;
            border: 2px solid var(--neon-cyan);
            border-radius: 10px;
            color: var(--neon-cyan);
            font-family: 'Orbitron', sans-serif;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .success-btn:hover {
            background: var(--neon-cyan);
            color: #000;
        }
    </style>
</head>

<body>
    <div class="cyber-grid-bg"></div>

    <?php if ($success): ?>
        <!-- Success Overlay -->
        <div class="success-overlay">
            <div class="success-content">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2 class="success-title">Registration Submitted!</h2>
                <p class="success-message">
                    Your student account has been created and is now <strong>pending approval</strong>.
                </p>
                <div class="success-steps">
                    <h4><i class="fas fa-list-ol"></i> Next Steps:</h4>
                    <ol>
                        <li>Check your email and <strong>verify your email address</strong></li>
                        <li>Wait for <strong>Admin approval</strong> (usually 1-2 business days)</li>
                        <li>You'll receive an email when your account is activated</li>
                        <li>Once approved, you can log in to your student dashboard</li>
                    </ol>
                </div>
                <a href="../login.php" class="success-btn">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    <?php else: ?>

        <div class="register-container">
            <div class="register-card">
                <!-- Header -->
                <div class="brand-header">
                    <div class="brand-logo">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h1 class="brand-title">Student Registration</h1>
                    <p class="brand-subtitle">Create your student account</p>
                </div>

                <!-- Security Notice -->
                <div class="security-notice">
                    <i class="fas fa-shield-alt"></i>
                    <p>
                        <strong>Note:</strong> Only students can register here. Teachers, parents, and staff accounts
                        are created by the school administrator. Your registration will be reviewed before activation.
                    </p>
                </div>

                <!-- Errors -->
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

                <!-- Registration Form -->
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

                    <!-- Student Information -->
                    <div class="section-header">
                        <span><i class="fas fa-user-graduate"></i> Student Information</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">First Name <span class="required">*</span></label>
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" name="first_name" class="form-input"
                                placeholder="John" value="<?= htmlspecialchars($form_data['first_name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name <span class="required">*</span></label>
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" name="last_name" class="form-input"
                                placeholder="Doe" value="<?= htmlspecialchars($form_data['last_name']) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Student ID / Enrollment No. <span class="required">*</span></label>
                            <i class="fas fa-id-card input-icon"></i>
                            <input type="text" name="student_id" class="form-input"
                                placeholder="STU-2025-001" value="<?= htmlspecialchars($form_data['student_id']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Grade Level <span class="required">*</span></label>
                            <i class="fas fa-layer-group input-icon"></i>
                            <select name="grade_level" class="form-select" required>
                                <option value="">Select Grade</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>" <?= $form_data['grade_level'] == $i ? 'selected' : '' ?>>Grade <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Student Email <span class="required">*</span></label>
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email" class="form-input"
                                placeholder="student@email.com" value="<?= htmlspecialchars($form_data['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Student Phone</label>
                            <i class="fas fa-phone input-icon"></i>
                            <input type="tel" name="phone" class="form-input"
                                placeholder="+1 234 567 890" value="<?= htmlspecialchars($form_data['phone']) ?>">
                        </div>
                    </div>

                    <!-- Parent/Guardian Information -->
                    <div class="section-header">
                        <span><i class="fas fa-users"></i> Parent/Guardian Information</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Parent/Guardian Name <span class="required">*</span></label>
                            <i class="fas fa-user-tie input-icon"></i>
                            <input type="text" name="parent_name" class="form-input"
                                placeholder="Parent's Full Name" value="<?= htmlspecialchars($form_data['parent_name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Parent/Guardian Phone <span class="required">*</span></label>
                            <i class="fas fa-phone-alt input-icon"></i>
                            <input type="tel" name="parent_phone" class="form-input"
                                placeholder="+1 234 567 890" value="<?= htmlspecialchars($form_data['parent_phone']) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Parent/Guardian Email</label>
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="parent_email" class="form-input"
                                placeholder="parent@email.com" value="<?= htmlspecialchars($form_data['parent_email']) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Home Address</label>
                            <i class="fas fa-home input-icon"></i>
                            <input type="text" name="address" class="form-input"
                                placeholder="123 Main Street" value="<?= htmlspecialchars($form_data['address']) ?>">
                        </div>
                    </div>

                    <!-- Account Security -->
                    <div class="section-header">
                        <span><i class="fas fa-lock"></i> Account Security</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Password <span class="required">*</span></label>
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" class="form-input"
                                placeholder="Min. 8 characters" required minlength="8">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm Password <span class="required">*</span></label>
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="confirm_password" class="form-input"
                                placeholder="Confirm password" required>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Submit Registration
                    </button>

                    <!-- Footer -->
                    <div class="form-footer">
                        <p>Already have an account? <a href="../login.php">Sign In</a></p>
                        <p style="margin-top: 0.5rem; font-size: 0.85rem;">
                            Teachers & Staff: Contact your administrator for account creation
                        </p>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</body>

</html>