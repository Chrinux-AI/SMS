<?php
/**
 * School Registration Page
 * Register a new school on Verdant SMS
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';

$pageTitle = "Register Your School - Verdant SMS";
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schoolName = trim($_POST['school_name'] ?? '');
    $adminName = trim($_POST['admin_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($schoolName) || empty($adminName) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        // In production, create the school and admin account
        // For now, show success
        $success = true;
    }
}

$nigerianStates = [
    'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno',
    'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe', 'Imo',
    'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos', 'Nasarawa',
    'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers', 'Sokoto', 'Taraba',
    'Yobe', 'Zamfara'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #00D4FF; --success: #00FF87; --danger: #FF4757; --bg-dark: #0A0E17; --bg-card: #111827; --border: rgba(255,255,255,0.08); --text: #F3F4F6; --text-muted: #9CA3AF; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-dark); color: var(--text); min-height: 100vh; display: flex; }
        .left-panel { flex: 1; background: linear-gradient(135deg, rgba(0,255,135,0.1), rgba(0,212,255,0.1)); display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 3rem; }
        .left-panel h1 { font-size: 2.5rem; margin-bottom: 1rem; text-align: center; }
        .left-panel h1 span { background: linear-gradient(90deg, var(--success), var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .left-panel p { color: var(--text-muted); text-align: center; max-width: 400px; margin-bottom: 2rem; }
        .features-list { list-style: none; }
        .features-list li { padding: 0.75rem 0; display: flex; align-items: center; gap: 0.75rem; }
        .features-list li i { color: var(--success); font-size: 1rem; }
        .right-panel { flex: 1; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .register-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 24px; padding: 2.5rem; max-width: 500px; width: 100%; }
        .register-card h2 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .register-card .subtitle { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-muted); }
        .form-group input, .form-group select { width: 100%; background: var(--bg-dark); border: 1px solid var(--border); border-radius: 10px; padding: 0.85rem 1rem; color: var(--text); font-size: 0.95rem; font-family: inherit; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary); }
        .btn-register { display: block; width: 100%; padding: 1rem; background: linear-gradient(135deg, var(--success), var(--primary)); border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; color: #000; cursor: pointer; margin-top: 0.5rem; }
        .btn-register:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0,255,135,0.2); }
        .alert { padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .alert-success { background: rgba(0,255,135,0.15); border: 1px solid var(--success); color: var(--success); }
        .alert-error { background: rgba(255,71,87,0.15); border: 1px solid var(--danger); color: var(--danger); }
        .login-link { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted); }
        .login-link a { color: var(--primary); text-decoration: none; }
        @media (max-width: 900px) {
            body { flex-direction: column; }
            .left-panel { padding: 2rem 1rem; }
            .left-panel h1 { font-size: 1.75rem; }
        }
    </style>
</head>
<body>
    <div class="left-panel">
        <h1>Start Your <span>Verdant</span> Journey</h1>
        <p>Join thousands of Nigerian schools using Verdant SMS to manage their institutions.</p>
        <ul class="features-list">
            <li><i class="fas fa-check-circle"></i> 100% Free to start</li>
            <li><i class="fas fa-check-circle"></i> All core features included</li>
            <li><i class="fas fa-check-circle"></i> Nigerian curriculum compliant</li>
            <li><i class="fas fa-check-circle"></i> AI-powered tools</li>
            <li><i class="fas fa-check-circle"></i> Secure multi-tenant system</li>
        </ul>
    </div>

    <div class="right-panel">
        <div class="register-card">
            <h2>Register Your School</h2>
            <p class="subtitle">Create your free account in minutes</p>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Registration successful! Check your email to verify and login.
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form method="POST">
                <div class="form-group">
                    <label>School Name *</label>
                    <input type="text" name="school_name" required placeholder="e.g., Victory Model College">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Admin Name *</label>
                        <input type="text" name="admin_name" required placeholder="Your full name">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" placeholder="+234 800 000 0000">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" required placeholder="admin@school.com">
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <select name="state">
                            <option value="">Select State</option>
                            <?php foreach ($nigerianStates as $state): ?>
                                <option value="<?= $state ?>"><?= $state ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" name="password" required placeholder="Min 8 characters">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password *</label>
                        <input type="password" name="confirm_password" required placeholder="Re-enter password">
                    </div>
                </div>

                <button type="submit" class="btn-register">
                    <i class="fas fa-rocket"></i> Create My School
                </button>
            </form>
            <?php endif; ?>

            <div class="login-link">
                Already have an account? <a href="../login.php">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>
