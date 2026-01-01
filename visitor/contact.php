<?php
/**
 * Verdant SMS - Contact Page
 */
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = "Contact Us - Verdant SMS";
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $school = trim($_POST['school'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } else {
        // In production, this would send an email or save to database
        $success = true;
    }
}
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
        body { font-family: 'Inter', sans-serif; background: var(--bg-dark); color: var(--text); min-height: 100vh; }
        .navbar { padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
        .navbar-brand { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
        .navbar-logo { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, var(--success), var(--primary)); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #000; }
        .navbar-title { font-size: 1.1rem; font-weight: 700; background: linear-gradient(90deg, var(--success), var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .container { max-width: 600px; margin: 0 auto; padding: 4rem 2rem; }
        h1 { font-size: 2rem; margin-bottom: 0.5rem; text-align: center; }
        .subtitle { color: var(--text-muted); text-align: center; margin-bottom: 2rem; }
        .form-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 2rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.9rem; font-weight: 500; margin-bottom: 0.5rem; }
        .form-group input, .form-group textarea { width: 100%; background: var(--bg-dark); border: 1px solid var(--border); border-radius: 10px; padding: 0.85rem 1rem; color: var(--text); font-size: 0.95rem; font-family: inherit; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--primary); }
        .form-group textarea { min-height: 120px; resize: vertical; }
        .btn { display: block; width: 100%; padding: 1rem; border-radius: 12px; font-size: 1rem; font-weight: 600; border: none; cursor: pointer; }
        .btn-primary { background: linear-gradient(135deg, var(--success), var(--primary)); color: #000; }
        .alert { padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; }
        .alert-success { background: rgba(0,255,135,0.15); border: 1px solid var(--success); color: var(--success); }
        .alert-error { background: rgba(255,71,87,0.15); border: 1px solid var(--danger); color: var(--danger); }
        .contact-info { margin-top: 2rem; text-align: center; }
        .contact-info p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem; }
        .contact-info a { color: var(--primary); text-decoration: none; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="navbar-brand">
            <div class="navbar-logo"><i class="fas fa-leaf"></i></div>
            <span class="navbar-title">Verdant SMS</span>
        </a>
    </nav>

    <div class="container">
        <h1>Contact Us</h1>
        <p class="subtitle">Have questions? We'd love to hear from you.</p>

        <div class="form-card">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Thank you! We'll get back to you within 24 hours.
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Your Name *</label>
                    <input type="text" name="name" required placeholder="John Doe">
                </div>
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" required placeholder="you@example.com">
                </div>
                <div class="form-group">
                    <label>School Name</label>
                    <input type="text" name="school" placeholder="Your School Name">
                </div>
                <div class="form-group">
                    <label>Message *</label>
                    <textarea name="message" required placeholder="How can we help you?"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>

        <div class="contact-info">
            <p><i class="fas fa-envelope"></i> <a href="mailto:hello@verdantsms.com">hello@verdantsms.com</a></p>
            <p><i class="fab fa-github"></i> <a href="https://github.com/Chrinux-AI/SMS" target="_blank">GitHub</a></p>
        </div>
    </div>
</body>
</html>
