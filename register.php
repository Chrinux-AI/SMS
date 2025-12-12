<?php

/**
 * Verdant SMS - Advanced Registration Portal
 * Supports all 25 user roles with Cyberpunk UI
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';

$message = '';
$message_type = '';

// Check if registration is enabled
$setting = db()->fetch("SELECT setting_value FROM system_settings WHERE setting_key = 'registration_enabled'");
$registration_enabled = $setting ? (bool)$setting['setting_value'] : true;

if (!$registration_enabled) {
    $message = 'Registration is currently disabled. Please contact the administrator.';
    $message_type = 'error';
}

// Define all 25 roles with their metadata
$role_categories = [
    'leadership' => [
        'title' => 'School Leadership',
        'icon' => 'crown',
        'color' => 'gold',
        'roles' => [
            'superadmin' => ['name' => 'Super Administrator', 'icon' => 'user-astronaut', 'desc' => 'Multi-school global control', 'restricted' => true],
            'owner' => ['name' => 'Owner / Director', 'icon' => 'building', 'desc' => 'Strategic & financial oversight', 'restricted' => true],
            'principal' => ['name' => 'Principal', 'icon' => 'user-tie', 'desc' => 'Academic leadership', 'restricted' => true],
            'vice-principal' => ['name' => 'Vice Principal', 'icon' => 'user-shield', 'desc' => 'Discipline & operations', 'restricted' => true],
        ]
    ],
    'administration' => [
        'title' => 'Administration',
        'icon' => 'building-columns',
        'color' => 'cyan',
        'roles' => [
            'admin' => ['name' => 'Administrator', 'icon' => 'user-gear', 'desc' => 'System administration', 'restricted' => true],
            'admin-officer' => ['name' => 'Admin Officer', 'icon' => 'id-card', 'desc' => 'Front desk & certificates', 'restricted' => false],
            'accountant' => ['name' => 'Accountant', 'icon' => 'calculator', 'desc' => 'Financial operations', 'restricted' => false],
        ]
    ],
    'academic' => [
        'title' => 'Academic Staff',
        'icon' => 'graduation-cap',
        'color' => 'green',
        'roles' => [
            'teacher' => ['name' => 'Teacher', 'icon' => 'chalkboard-teacher', 'desc' => 'Teaching & assessment', 'restricted' => false],
            'class-teacher' => ['name' => 'Class Teacher', 'icon' => 'people-group', 'desc' => 'Homeroom & welfare', 'restricted' => false],
            'subject-coordinator' => ['name' => 'Subject Coordinator', 'icon' => 'book-open-reader', 'desc' => 'Department coordination', 'restricted' => false],
        ]
    ],
    'support' => [
        'title' => 'Support Services',
        'icon' => 'hands-holding-child',
        'color' => 'purple',
        'roles' => [
            'librarian' => ['name' => 'Librarian', 'icon' => 'book', 'desc' => 'Library management', 'restricted' => false],
            'counselor' => ['name' => 'Counselor', 'icon' => 'brain', 'desc' => 'Student guidance', 'restricted' => false],
            'nurse' => ['name' => 'School Nurse', 'icon' => 'heart-pulse', 'desc' => 'Health services', 'restricted' => false],
        ]
    ],
    'facilities' => [
        'title' => 'Facility Management',
        'icon' => 'building-user',
        'color' => 'orange',
        'roles' => [
            'transport' => ['name' => 'Transport Manager', 'icon' => 'bus', 'desc' => 'School transport', 'restricted' => false],
            'hostel' => ['name' => 'Hostel Warden', 'icon' => 'bed', 'desc' => 'Residential facilities', 'restricted' => false],
            'canteen' => ['name' => 'Canteen Manager', 'icon' => 'utensils', 'desc' => 'Food services', 'restricted' => false],
            'general' => ['name' => 'General Staff', 'icon' => 'screwdriver-wrench', 'desc' => 'Maintenance & operations', 'restricted' => false],
        ]
    ],
    'users' => [
        'title' => 'Students & Parents',
        'icon' => 'users',
        'color' => 'blue',
        'roles' => [
            'student' => ['name' => 'Student', 'icon' => 'user-graduate', 'desc' => 'Learning portal access', 'restricted' => false],
            'parent' => ['name' => 'Parent / Guardian', 'icon' => 'user-group', 'desc' => 'Child monitoring', 'restricted' => false],
            'alumni' => ['name' => 'Alumni', 'icon' => 'users-rays', 'desc' => 'Graduate network', 'restricted' => false],
        ]
    ],
];

// Flatten roles for validation
$all_roles = [];
$restricted_roles = ['superadmin', 'owner', 'principal', 'vice-principal', 'admin'];
foreach ($role_categories as $category) {
    foreach ($category['roles'] as $role_key => $role_data) {
        $all_roles[$role_key] = $role_data;
    }
}

// Handle registration submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register']) && $registration_enabled) {
    $errors = [];

    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please refresh the page and try again.';
    }

    if (empty($errors)) {
        try {
            $username = sanitize($_POST['username'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $first_name = sanitize($_POST['first_name'] ?? '');
            $last_name = sanitize($_POST['last_name'] ?? '');
            $role = sanitize($_POST['role'] ?? '');
            $phone = sanitize($_POST['phone'] ?? '');

            // Block restricted role registration
            if (in_array($role, $restricted_roles)) {
                $errors[] = ucfirst(str_replace('-', ' ', $role)) . ' registration requires administrator approval. Please contact the school.';
            }

            // Validation
            if (empty($username)) $errors[] = 'Username is required';
            if (empty($email)) $errors[] = 'Email is required';
            if (empty($password)) $errors[] = 'Password is required';
            if (empty($first_name)) $errors[] = 'First name is required';
            if (empty($last_name)) $errors[] = 'Last name is required';
            if (empty($role)) $errors[] = 'Role is required';

            if (strlen($username) < 3) $errors[] = 'Username must be at least 3 characters';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address';
            if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters';
            if ($password !== $confirm_password) $errors[] = 'Passwords do not match';
            if (!array_key_exists($role, $all_roles)) $errors[] = 'Invalid role selected';

            // Role-specific validation
            if ($role === 'student') {
                if (empty($_POST['date_of_birth'])) $errors[] = 'Date of birth is required for students';
                if (empty($_POST['grade_level'])) $errors[] = 'Grade level is required for students';
            }

            // Check for existing user
            $existing = db()->fetch("SELECT id FROM users WHERE username = ?", [$username]);
            if ($existing) $errors[] = 'Username already exists';

            $existing = db()->fetch("SELECT id FROM users WHERE email = ?", [$email]);
            if ($existing) $errors[] = 'Email already registered';

            if (empty($errors)) {
                // Generate verification token with 10-minute expiration
                $verification_token = bin2hex(random_bytes(32));
                $token_expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                $user_data = [
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'role' => $role,
                    'phone' => $phone,
                    'status' => 'pending',
                    'email_verified' => 0,
                    'email_verification_token' => $verification_token,
                    'token_expires_at' => $token_expires_at,
                    'approved' => 0
                ];

                $user_id = db()->insert('users', $user_data);

                if ($user_id) {
                    $assigned_id = null;

                    // Role-specific record creation
                    if ($role === 'student') {
                        $year = date('Y');
                        $count = db()->count('students') + 1;
                        $student_id = $year . str_pad($count, 4, '0', STR_PAD_LEFT);
                        $assigned_id = 'STU' . $student_id;

                        $student_data = [
                            'user_id' => $user_id,
                            'student_id' => $student_id,
                            'assigned_student_id' => $student_id,
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'email' => $email,
                            'phone' => $phone,
                            'date_of_birth' => $_POST['date_of_birth'],
                            'grade_level' => (int)$_POST['grade_level']
                        ];
                        db()->insert('students', $student_data);
                    } elseif ($role === 'teacher' || $role === 'class-teacher' || $role === 'subject-coordinator') {
                        $year = date('Y');
                        $count = db()->count('teachers') + 1;
                        $teacher_id = $year . str_pad($count, 4, '0', STR_PAD_LEFT);
                        $assigned_id = 'EMP' . $teacher_id;
                    } elseif ($role === 'parent') {
                        $assigned_id = 'PAR' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    } else {
                        $assigned_id = strtoupper(substr(str_replace('-', '', $role), 0, 3)) . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    }

                    // Update assigned_id
                    db()->update('users', ['assigned_id' => $assigned_id], 'id = ?', [$user_id]);

                    // Send verification email
                    $email_sent = function_exists('send_verification_email')
                        ? send_verification_email($email, $first_name . ' ' . $last_name, $verification_token, $assigned_id, $role)
                        : false;

                    if ($email_sent) {
                        $message = '✅ Registration successful! Please check your email (' . htmlspecialchars($email) . ') to verify your account. ⏱️ Verification link expires in 10 minutes.';
                        $message_type = 'success';
                    } else {
                        $message = '✅ Registration successful! Please wait for admin approval. If email verification is required, contact the administrator.';
                        $message_type = 'success';
                    }
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
            }
        } catch (Exception $e) {
            $errors[] = 'An error occurred: ' . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $message = implode('<br>', $errors);
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Verdant SMS</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#00BFFF">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #00BFFF;
            --secondary: #8A2BE2;
            --accent: #00FF7F;
            --warning: #FFD700;
            --danger: #FF4757;
            --dark: #0a0a0f;
            --darker: #05050a;
            --card-bg: rgba(15, 15, 25, 0.95);
            --glass: rgba(255, 255, 255, 0.03);
            --border: rgba(0, 191, 255, 0.2);
            --glow: 0 0 20px rgba(0, 191, 255, 0.3);
            --text-primary: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.6);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            overflow-y: scroll;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--darker);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            background:
                radial-gradient(ellipse at 20% 20%, rgba(0, 191, 255, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(138, 43, 226, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(0, 255, 127, 0.08) 0%, transparent 70%),
                var(--darker);
        }

        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image:
                linear-gradient(rgba(0, 191, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 191, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
            pointer-events: none;
        }

        @keyframes gridMove {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(50px, 50px);
            }
        }

        /* Header */
        .register-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 1rem 2rem;
            background: rgba(10, 10, 15, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: white;
        }

        .logo-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-links {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: var(--glow);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(0, 191, 255, 0.5);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: var(--dark);
        }

        /* Main Container */
        .register-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 2rem 3rem;
        }

        /* Page Title */
        .page-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-title p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-weight: 500;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .alert.success {
            background: rgba(0, 255, 127, 0.1);
            border: 1px solid var(--accent);
            color: var(--accent);
        }

        .alert.error {
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid var(--danger);
            color: var(--danger);
        }

        .alert.warning {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid var(--warning);
            color: var(--warning);
        }

        .alert i {
            font-size: 1.2rem;
            margin-top: 2px;
        }

        /* Registration Steps */
        .reg-steps {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 50px;
            opacity: 0.5;
            transition: all 0.3s;
        }

        .step.active {
            opacity: 1;
            border-color: var(--primary);
            box-shadow: var(--glow);
        }

        .step.completed {
            opacity: 1;
            border-color: var(--accent);
            background: rgba(0, 255, 127, 0.1);
        }

        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .step.completed .step-number {
            background: var(--accent);
        }

        .step-label {
            font-weight: 600;
        }

        /* Role Selection Grid */
        .role-categories {
            display: grid;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .category {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
        }

        .category-header {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.1), rgba(138, 43, 226, 0.1));
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .category-header.gold {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.15), rgba(255, 165, 0, 0.1));
        }

        .category-header.cyan {
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.15), rgba(0, 150, 200, 0.1));
        }

        .category-header.green {
            background: linear-gradient(135deg, rgba(0, 255, 127, 0.15), rgba(0, 200, 100, 0.1));
        }

        .category-header.purple {
            background: linear-gradient(135deg, rgba(138, 43, 226, 0.15), rgba(100, 30, 180, 0.1));
        }

        .category-header.orange {
            background: linear-gradient(135deg, rgba(255, 165, 0, 0.15), rgba(255, 130, 0, 0.1));
        }

        .category-header.blue {
            background: linear-gradient(135deg, rgba(0, 120, 255, 0.15), rgba(0, 100, 200, 0.1));
        }

        .category-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .category-icon.gold {
            color: var(--warning);
        }

        .category-icon.cyan {
            color: var(--primary);
        }

        .category-icon.green {
            color: var(--accent);
        }

        .category-icon.purple {
            color: var(--secondary);
        }

        .category-icon.orange {
            color: #FF9F43;
        }

        .category-icon.blue {
            color: #0984E3;
        }

        .category-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
        }

        .category-body {
            padding: 1.5rem;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }

        /* Role Card */
        .role-card {
            position: relative;
            cursor: pointer;
        }

        .role-card input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
            z-index: 10;
        }

        .role-card-content {
            padding: 1.25rem;
            background: rgba(255, 255, 255, 0.02);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s;
        }

        .role-card:hover .role-card-content {
            border-color: var(--primary);
            background: rgba(0, 191, 255, 0.05);
        }

        .role-card input:checked+.role-card-content {
            border-color: var(--accent);
            background: rgba(0, 255, 127, 0.1);
            box-shadow: 0 0 25px rgba(0, 255, 127, 0.3);
        }

        .role-card.restricted .role-card-content {
            opacity: 0.5;
        }

        .role-card.restricted::after {
            content: 'Restricted';
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.7rem;
            background: var(--danger);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            z-index: 5;
        }

        .role-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: white;
            flex-shrink: 0;
        }

        .role-info h4 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .role-info p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* User Details Form */
        .details-form {
            display: none;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto 2rem;
        }

        .details-form.active {
            display: block;
            animation: slideIn 0.4s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .form-header h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group input,
        .form-group select {
            padding: 0.9rem 1rem;
            background: rgba(0, 191, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 1rem;
            font-family: 'Rajdhani', sans-serif;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(0, 191, 255, 0.3);
            background: rgba(0, 191, 255, 0.08);
        }

        .form-group input::placeholder {
            color: var(--text-muted);
        }

        .pw-wrapper {
            position: relative;
        }

        .pw-wrapper input {
            width: 100%;
            padding-right: 45px;
        }

        .pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .pw-toggle:hover {
            color: var(--primary);
        }

        /* Conditional Fields */
        .conditional-fields {
            display: none;
            grid-column: span 2;
        }

        .conditional-fields.active {
            display: block;
        }

        .conditional-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        /* Submit Button */
        .submit-section {
            grid-column: span 2;
            margin-top: 1rem;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 12px;
            color: white;
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 30px rgba(0, 191, 255, 0.5);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Back Button Container */
        .back-btn-container {
            text-align: center;
            margin-top: 1rem;
            display: none;
        }

        /* Login Link */
        .login-link {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-muted);
        }

        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .login-link a:hover {
            color: var(--accent);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .register-header {
                padding: 1rem;
            }

            .logo-text {
                display: none;
            }

            .reg-steps {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: span 1;
            }

            .conditional-grid {
                grid-template-columns: 1fr;
            }

            .submit-section {
                grid-column: span 1;
            }

            .category-body {
                grid-template-columns: 1fr;
            }

            .conditional-fields {
                grid-column: span 1;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--darker);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }

        /* Footer */
        .register-footer {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
            font-size: 0.9rem;
            border-top: 1px solid var(--border);
            margin-top: 3rem;
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>
    <div class="grid-overlay"></div>

    <!-- Header -->
    <header class="register-header">
        <a href="index.php" class="logo">
            <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
            <span class="logo-text">Verdant SMS</span>
        </a>
        <div class="header-links">
            <a href="index.php" class="btn btn-outline"><i class="fas fa-home"></i> Home</a>
            <a href="login.php" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</a>
        </div>
    </header>

    <!-- Main Container -->
    <main class="register-container">
        <!-- Page Title -->
        <div class="page-title">
            <h1><i class="fas fa-user-plus"></i> Create Your Account</h1>
            <p>Join Verdant School Management System - Select your role and register</p>
        </div>

        <?php if ($message): ?>
            <div class="alert <?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : ($message_type === 'warning' ? 'exclamation-triangle' : 'times-circle'); ?>"></i>
                <div><?php echo $message; ?></div>
            </div>
        <?php endif; ?>

        <?php if ($registration_enabled && (!$message || $message_type !== 'success')): ?>
            <!-- Registration Steps -->
            <div class="reg-steps">
                <div class="step active" id="step1">
                    <span class="step-number">1</span>
                    <span class="step-label">Select Role</span>
                </div>
                <div class="step" id="step2">
                    <span class="step-number">2</span>
                    <span class="step-label">Your Details</span>
                </div>
                <div class="step" id="step3">
                    <span class="step-number">3</span>
                    <span class="step-label">Verify Email</span>
                </div>
            </div>

            <form method="POST" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <!-- Role Selection -->
                <div class="role-categories" id="roleSection">
                    <?php foreach ($role_categories as $cat_key => $category): ?>
                        <div class="category">
                            <div class="category-header <?php echo $category['color']; ?>">
                                <div class="category-icon <?php echo $category['color']; ?>">
                                    <i class="fas fa-<?php echo $category['icon']; ?>"></i>
                                </div>
                                <span class="category-title"><?php echo $category['title']; ?></span>
                            </div>
                            <div class="category-body">
                                <?php foreach ($category['roles'] as $role_key => $role): ?>
                                    <label class="role-card <?php echo $role['restricted'] ? 'restricted' : ''; ?>">
                                        <input type="radio" name="role" value="<?php echo $role_key; ?>" required <?php echo $role['restricted'] ? 'disabled' : ''; ?>>
                                        <div class="role-card-content">
                                            <div class="role-icon">
                                                <i class="fas fa-<?php echo $role['icon']; ?>"></i>
                                            </div>
                                            <div class="role-info">
                                                <h4><?php echo $role['name']; ?></h4>
                                                <p><?php echo $role['desc']; ?></p>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- User Details Form -->
                <div class="details-form" id="detailsForm">
                    <div class="form-header">
                        <h2><i class="fas fa-user-edit"></i> Complete Your Profile</h2>
                        <p>Fill in your details to create your account</p>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> First Name</label>
                            <input type="text" name="first_name" placeholder="Enter first name" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Last Name</label>
                            <input type="text" name="last_name" placeholder="Enter last name" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-at"></i> Username</label>
                            <input type="text" name="username" placeholder="Choose a username" minlength="3" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Phone Number</label>
                            <input type="tel" name="phone" placeholder="Enter phone number">
                        </div>
                        <div class="form-group full-width">
                            <label><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" name="email" placeholder="Enter your email address" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Password</label>
                            <div class="pw-wrapper">
                                <input type="password" name="password" id="password" placeholder="Create password (min 8 chars)" minlength="8" required>
                                <button type="button" class="pw-toggle" onclick="togglePassword('password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Confirm Password</label>
                            <div class="pw-wrapper">
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" required>
                                <button type="button" class="pw-toggle" onclick="togglePassword('confirm_password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Student Fields -->
                        <div class="conditional-fields" id="studentFields">
                            <div class="conditional-grid">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar"></i> Date of Birth</label>
                                    <input type="date" name="date_of_birth" id="date_of_birth">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-graduation-cap"></i> Grade/Level</label>
                                    <select name="grade_level" id="grade_level">
                                        <option value="">Select Level</option>
                                        <option value="1">Grade 1</option>
                                        <option value="2">Grade 2</option>
                                        <option value="3">Grade 3</option>
                                        <option value="4">Grade 4</option>
                                        <option value="5">Grade 5</option>
                                        <option value="6">Grade 6</option>
                                        <option value="7">Grade 7</option>
                                        <option value="8">Grade 8</option>
                                        <option value="9">Grade 9</option>
                                        <option value="10">Grade 10</option>
                                        <option value="11">Grade 11</option>
                                        <option value="12">Grade 12</option>
                                        <option value="100">100 Level</option>
                                        <option value="200">200 Level</option>
                                        <option value="300">300 Level</option>
                                        <option value="400">400 Level</option>
                                        <option value="500">500 Level</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Parent Fields -->
                        <div class="conditional-fields" id="parentFields">
                            <div class="conditional-grid">
                                <div class="form-group full-width">
                                    <label><i class="fas fa-child"></i> Child's Student ID (if known)</label>
                                    <input type="text" name="child_student_id" placeholder="Enter your child's student ID (optional)">
                                </div>
                            </div>
                        </div>

                        <div class="submit-section">
                            <button type="submit" name="register" class="btn-submit" id="submitBtn" disabled>
                                <i class="fas fa-rocket"></i> Create Account
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="back-btn-container" id="backBtn">
                    <button type="button" class="btn btn-outline" onclick="showRoles()">
                        <i class="fas fa-arrow-left"></i> Back to Role Selection
                    </button>
                </div>
            </form>

            <div class="login-link">
                Already have an account? <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login here</a>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="register-footer">
        <p>&copy; <?php echo date('Y'); ?> Verdant SMS. All rights reserved. Version 3.0.0</p>
    </footer>

    <script>
        // Password toggle
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        // Role selection handling
        const roleInputs = document.querySelectorAll('input[name="role"]');
        const detailsForm = document.getElementById('detailsForm');
        const roleSection = document.getElementById('roleSection');
        const studentFields = document.getElementById('studentFields');
        const parentFields = document.getElementById('parentFields');
        const submitBtn = document.getElementById('submitBtn');
        const backBtn = document.getElementById('backBtn');
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');

        roleInputs.forEach(input => {
            input.addEventListener('change', function() {
                const selectedRole = this.value;

                // Show details form
                detailsForm.classList.add('active');
                roleSection.style.display = 'none';
                backBtn.style.display = 'block';
                submitBtn.disabled = false;

                // Update steps
                step1.classList.remove('active');
                step1.classList.add('completed');
                step2.classList.add('active');

                // Handle conditional fields
                studentFields.classList.remove('active');
                parentFields.classList.remove('active');
                document.getElementById('date_of_birth').required = false;
                document.getElementById('grade_level').required = false;

                if (selectedRole === 'student') {
                    studentFields.classList.add('active');
                    document.getElementById('date_of_birth').required = true;
                    document.getElementById('grade_level').required = true;
                } else if (selectedRole === 'parent') {
                    parentFields.classList.add('active');
                }

                // Scroll to form
                detailsForm.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });

        function showRoles() {
            roleSection.style.display = 'grid';
            detailsForm.classList.remove('active');
            backBtn.style.display = 'none';
            submitBtn.disabled = true;

            // Reset steps
            step1.classList.add('active');
            step1.classList.remove('completed');
            step2.classList.remove('active');

            // Scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters!');
                return false;
            }
        });
    </script>
</body>

</html>