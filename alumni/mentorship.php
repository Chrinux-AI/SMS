<?php

/**
 * Alumni Mentorship Program
 * Connect with current students as mentors
 * Verdant SMS v3.0
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Alumni only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'alumni') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user = db()->fetch("SELECT * FROM users WHERE id = ?", [$user_id]);

$page_title = 'Mentorship Program';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
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
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
    <style>
        .mentorship-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header h1 {
            color: var(--cyber-cyan);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #888;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .mentorship-benefits {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .benefit-card {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid var(--cyber-cyan);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .benefit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 255, 255, 0.2);
        }

        .benefit-card i {
            font-size: 3rem;
            color: var(--cyber-cyan);
            margin-bottom: 1rem;
        }

        .benefit-card h3 {
            color: var(--cyber-cyan);
            margin-bottom: 0.75rem;
        }

        .benefit-card p {
            color: #888;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .signup-section {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid var(--cyber-pink);
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            margin-bottom: 3rem;
        }

        .signup-section h2 {
            color: var(--cyber-pink);
            margin-bottom: 1rem;
        }

        .signup-section p {
            color: #888;
            margin-bottom: 2rem;
        }

        .mentor-form {
            max-width: 500px;
            margin: 0 auto;
            text-align: left;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: var(--cyber-cyan);
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid var(--cyber-cyan);
            border-radius: 5px;
            color: #fff;
            font-size: 1rem;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn-submit {
            display: block;
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--cyber-cyan), var(--cyber-pink));
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: scale(1.02);
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.5);
        }

        .how-it-works {
            margin-bottom: 3rem;
        }

        .how-it-works h2 {
            color: var(--cyber-cyan);
            text-align: center;
            margin-bottom: 2rem;
        }

        .steps {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .step {
            flex: 1;
            min-width: 200px;
            text-align: center;
            padding: 1.5rem;
        }

        .step-number {
            width: 50px;
            height: 50px;
            background: var(--cyber-cyan);
            color: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0 auto 1rem;
        }

        .step h4 {
            color: var(--cyber-cyan);
            margin-bottom: 0.5rem;
        }

        .step p {
            color: #888;
            font-size: 0.9rem;
        }
    </style>
</head>

<body class="cyber-bg">
    <?php include '../includes/cyber-nav.php'; ?>

    <main class="cyber-main">
        <div class="mentorship-container">
            <div class="page-header">
                <h1><i class="fas fa-hands-helping"></i> Mentorship Program</h1>
                <p>Share your experience and guide the next generation of students towards success</p>
            </div>

            <div class="mentorship-benefits">
                <div class="benefit-card">
                    <i class="fas fa-lightbulb"></i>
                    <h3>Share Your Experience</h3>
                    <p>Guide students with real-world insights from your career journey and lessons learned.</p>
                </div>
                <div class="benefit-card">
                    <i class="fas fa-route"></i>
                    <h3>Career Guidance</h3>
                    <p>Help students navigate career choices, university applications, and professional development.</p>
                </div>
                <div class="benefit-card">
                    <i class="fas fa-network-wired"></i>
                    <h3>Expand Network</h3>
                    <p>Connect with fellow alumni and build valuable relationships across industries.</p>
                </div>
                <div class="benefit-card">
                    <i class="fas fa-heart"></i>
                    <h3>Give Back</h3>
                    <p>Make a meaningful impact on your alma mater and shape future leaders.</p>
                </div>
            </div>

            <div class="how-it-works">
                <h2><i class="fas fa-cogs"></i> How It Works</h2>
                <div class="steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h4>Sign Up</h4>
                        <p>Complete the mentor registration form below</p>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <h4>Get Matched</h4>
                        <p>We'll pair you with students based on interests & career goals</p>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <h4>Connect</h4>
                        <p>Schedule virtual or in-person mentoring sessions</p>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <h4>Impact</h4>
                        <p>Track progress and celebrate your mentee's achievements</p>
                    </div>
                </div>
            </div>

            <div class="signup-section">
                <h2><i class="fas fa-user-plus"></i> Become a Mentor</h2>
                <p>Share your expertise and make a difference in a student's life</p>

                <form class="mentor-form" method="POST" action="">
                    <div class="form-group">
                        <label for="expertise">Area of Expertise *</label>
                        <select name="expertise" id="expertise" required>
                            <option value="">Select your field</option>
                            <option value="engineering">Engineering & Technology</option>
                            <option value="medicine">Medicine & Healthcare</option>
                            <option value="law">Law & Legal Practice</option>
                            <option value="business">Business & Entrepreneurship</option>
                            <option value="education">Education</option>
                            <option value="arts">Arts & Creative Industries</option>
                            <option value="science">Science & Research</option>
                            <option value="finance">Finance & Banking</option>
                            <option value="it">Information Technology</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="availability">Availability</label>
                        <select name="availability" id="availability">
                            <option value="weekly">Weekly (1-2 hours)</option>
                            <option value="biweekly">Bi-weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bio">Brief Bio & What You Can Offer *</label>
                        <textarea name="bio" id="bio" placeholder="Tell us about your career, achievements, and how you'd like to help students..." required></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Register as Mentor
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>

</html>