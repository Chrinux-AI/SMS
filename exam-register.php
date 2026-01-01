<?php

/**
 * VERDANT SMS v3.0 — PUBLIC ENTRANCE EXAM REGISTRATION
 * Students sign up to take the entrance examination
 * Requires Admin approval before exam link is sent
 */

session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$success = '';
$error = '';
$grades = [
    'Grade 1',
    'Grade 2',
    'Grade 3',
    'Grade 4',
    'Grade 5',
    'Grade 6',
    'Grade 7',
    'Grade 8',
    'Grade 9',
    'Grade 10',
    'Grade 11',
    'Grade 12'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $parent_name = trim($_POST['parent_name'] ?? '');
    $parent_phone = trim($_POST['parent_phone'] ?? '');
    $dob = $_POST['date_of_birth'] ?? '';
    $previous_school = trim($_POST['previous_school'] ?? '');
    $grade_applying = $_POST['grade_applying_for'] ?? '';

    // Validation
    if (empty($full_name) || empty($email) || empty($phone) || empty($parent_name) || empty($parent_phone)) {
        $error = 'All required fields must be filled.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if already registered
        $existing = db()->fetchOne("SELECT id FROM exam_registrations WHERE email = ?", [$email]);
        if ($existing) {
            $error = 'This email is already registered for the entrance exam.';
        } else {
            try {
                db()->insert('exam_registrations', [
                    'full_name' => $full_name,
                    'email' => $email,
                    'phone' => $phone,
                    'parent_name' => $parent_name,
                    'parent_phone' => $parent_phone,
                    'date_of_birth' => $dob ?: null,
                    'previous_school' => $previous_school,
                    'grade_applying_for' => $grade_applying,
                    'status' => 'pending'
                ]);
                $success = 'Registration successful! You will receive an email with your exam link once approved by the administration.';
            } catch (Exception $e) {
                $error = 'Registration failed. Please try again.';
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
    <title>Entrance Exam Registration — Verdant School</title>
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="assets/images/icons/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/images/icons/favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/icons/apple-touch-icon.png">
    <link rel="manifest" href="manifest.json">
    <meta name="msapplication-TileColor" content="#00BFFF">
    <meta name="msapplication-TileImage" content="assets/images/icons/mstile-150x150.png">
    <meta name="theme-color" content="#0a0a0f">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <style>
        :root {
            --neon-green: #00ff88;
            --neon-blue: #00d4ff;
            --neon-purple: #b366ff;
            --dark-bg: #0a0a0f;
            --card-bg: rgba(15, 25, 35, 0.95);
            --text-primary: #e0e6ed;
            --text-muted: #8892a0;
            --border-glow: rgba(0, 255, 136, 0.3);
            --success: #00ff88;
            --error: #ff4757;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--dark-bg);
            min-height: 100vh;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Animated Grid Background */
        .cyber-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                linear-gradient(90deg, rgba(0, 255, 136, 0.03) 1px, transparent 1px),
                linear-gradient(rgba(0, 255, 136, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
            z-index: -1;
        }

        @keyframes gridMove {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(50px, 50px);
            }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--neon-green);
            border-radius: 50%;
            opacity: 0.6;
            animation: float 15s infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.6;
            }

            100% {
                transform: translateY(-100vh) rotate(720deg);
                opacity: 0;
            }
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            padding: 20px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 2.2rem;
            background: linear-gradient(135deg, var(--neon-green), var(--neon-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 30px rgba(0, 255, 136, 0.3);
        }

        .header p {
            color: var(--text-muted);
            margin-top: 10px;
        }

        /* Form Card */
        .form-card {
            background: var(--card-bg);
            border: 1px solid var(--border-glow);
            border-radius: 20px;
            padding: 40px;
            box-shadow:
                0 0 40px rgba(0, 255, 136, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            color: var(--neon-green);
            font-size: 1rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .form-group label .required {
            color: var(--error);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px 16px;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(0, 255, 136, 0.2);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--neon-green);
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.2);
        }

        .form-group input::placeholder {
            color: var(--text-muted);
        }

        /* Alerts */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: rgba(0, 255, 136, 0.15);
            border: 1px solid rgba(0, 255, 136, 0.4);
            color: var(--success);
        }

        .alert-error {
            background: rgba(255, 71, 87, 0.15);
            border: 1px solid rgba(255, 71, 87, 0.4);
            color: var(--error);
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--neon-green), #00cc6a);
            border: none;
            border-radius: 12px;
            color: #000;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Info Box */
        .info-box {
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin-top: 25px;
        }

        .info-box h4 {
            color: var(--neon-blue);
            margin-bottom: 10px;
        }

        .info-box ul {
            list-style: none;
            padding: 0;
        }

        .info-box li {
            color: var(--text-muted);
            padding: 5px 0;
            padding-left: 25px;
            position: relative;
        }

        .info-box li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--neon-green);
        }

        /* Footer Links */
        .footer-links {
            text-align: center;
            margin-top: 25px;
            color: var(--text-muted);
        }

        .footer-links a {
            color: var(--neon-green);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--neon-blue);
        }

        /* Confetti Animation */
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            top: -10px;
            animation: confetti-fall 3s ease-out forwards;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    <div class="cyber-grid"></div>

    <div class="particles">
        <?php for ($i = 0; $i < 20; $i++): ?>
            <div class="particle" style="left: <?= rand(0, 100) ?>%; animation-delay: <?= rand(0, 15) ?>s; animation-duration: <?= rand(10, 20) ?>s;"></div>
        <?php endfor; ?>
    </div>

    <div class="container">
        <div class="header">
            <img src="assets/images/logo.svg" alt="Verdant" class="logo" onerror="this.style.display='none'">
            <h1><i class="fas fa-graduation-cap"></i> Entrance Exam Registration</h1>
            <p>Register to take the Verdant School entrance examination</p>
        </div>

        <div class="form-card">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
                <script>
                    // Confetti celebration
                    for (let i = 0; i < 50; i++) {
                        setTimeout(() => {
                            const confetti = document.createElement('div');
                            confetti.className = 'confetti';
                            confetti.style.left = Math.random() * 100 + '%';
                            confetti.style.background = ['#00ff88', '#00d4ff', '#b366ff', '#ffcc00'][Math.floor(Math.random() * 4)];
                            document.body.appendChild(confetti);
                            setTimeout(() => confetti.remove(), 3000);
                        }, i * 50);
                    }
                </script>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
                <form method="POST" action="">
                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Student Information</h3>
                        <div class="form-group">
                            <label>Full Name <span class="required">*</span></label>
                            <input type="text" name="full_name" placeholder="Enter student's full name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Email Address <span class="required">*</span></label>
                                <input type="email" name="email" placeholder="student@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Phone Number <span class="required">*</span></label>
                                <input type="tel" name="phone" placeholder="+254 700 000 000" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="date_of_birth" value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Grade Applying For</label>
                                <select name="grade_applying_for">
                                    <option value="">Select Grade</option>
                                    <?php foreach ($grades as $g): ?>
                                        <option value="<?= $g ?>" <?= ($_POST['grade_applying_for'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Previous School</label>
                            <input type="text" name="previous_school" placeholder="Name of previous school" value="<?= htmlspecialchars($_POST['previous_school'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-users"></i> Parent/Guardian Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Parent/Guardian Name <span class="required">*</span></label>
                                <input type="text" name="parent_name" placeholder="Parent's full name" required value="<?= htmlspecialchars($_POST['parent_name'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Parent Phone <span class="required">*</span></label>
                                <input type="tel" name="parent_phone" placeholder="+254 700 000 000" required value="<?= htmlspecialchars($_POST['parent_phone'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Submit Registration
                    </button>
                </form>

                <div class="info-box">
                    <h4><i class="fas fa-info-circle"></i> What Happens Next?</h4>
                    <ul>
                        <li>Your registration will be reviewed by the administration</li>
                        <li>Once approved, you'll receive an email with your exam link</li>
                        <li>Complete the online examination within the time limit</li>
                        <li>If you pass, you'll receive your Entrance ID for registration</li>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="footer-links">
                <a href="http://localhost/attendance/">← Back to Home</a> &nbsp;|&nbsp;
                <a href="login.php">Already have an account? Login</a>
            </div>
        </div>
    </div>
</body>

</html>
