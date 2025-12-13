<?php

/**
 * Contact Us - Visitor Page
 * Public contact form and information
 */
require_once '../includes/config.php';
require_once '../includes/database.php';

$page_title = 'Contact Us - Verdant SMS';
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Honeypot check
    if (!empty($_POST['company'])) {
        $error = 'Spam detected.';
    } elseif (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            // Rate limiting: 3 messages per email per hour
            $count = db()->fetchColumn(
                "SELECT COUNT(*) FROM contact_messages WHERE email = ? AND created_at > NOW() - INTERVAL 1 HOUR",
                [$email]
            );

            if ($count >= 3) {
                $error = 'You have sent too many messages. Please try again later.';
            } else {
                db()->insert('contact_messages', [
                    'name' => $name,
                    'email' => $email,
                    'subject' => $subject ?: 'General Inquiry',
                    'message' => $message,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $success = true;
            }
        } catch (Exception $e) {
            $error = 'Something went wrong. Please try again later.';
            error_log("Contact form error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Contact Verdant SMS support team.">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Orbitron:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00BFFF;
            --secondary: #8A2BE2;
            --accent: #00FF7F;
            --dark: #0a0a0f;
            --darker: #05050a;
            --border: rgba(0, 191, 255, 0.2);
            --danger: #FF4757;
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
            color: #fff;
            line-height: 1.6;
        }

        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(ellipse at 20% 20%, rgba(0, 191, 255, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(138, 43, 226, 0.1) 0%, transparent 50%);
        }

        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 2rem 60px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 3rem;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .info-card {
            background: rgba(20, 20, 30, 0.9);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.3s;
        }

        .info-card:hover {
            border-color: var(--primary);
            transform: translateX(5px);
        }

        .info-card .icon {
            width: 60px;
            height: 60px;
            background: rgba(0, 191, 255, 0.1);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary);
            flex-shrink: 0;
        }

        .info-card h3 {
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .info-card p {
            color: rgba(255, 255, 255, 0.7);
        }

        .info-card a {
            color: var(--primary);
            text-decoration: none;
        }

        .info-card a:hover {
            text-decoration: underline;
        }

        .whatsapp-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            background: #25D366;
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .whatsapp-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 25px rgba(37, 211, 102, 0.4);
        }

        .form-card {
            background: rgba(20, 20, 30, 0.9);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
        }

        .form-group label .required {
            color: var(--danger);
        }

        .form-control {
            width: 100%;
            padding: 0.9rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(0, 191, 255, 0.2);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 25px rgba(0, 191, 255, 0.4);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: rgba(0, 255, 127, 0.1);
            border: 1px solid rgba(0, 255, 127, 0.3);
            color: var(--accent);
        }

        .alert-error {
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid rgba(255, 71, 87, 0.3);
            color: var(--danger);
        }

        .honeypot {
            position: absolute;
            left: -9999px;
        }

        @media (max-width: 900px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .form-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <?php include '../includes/visitor-nav.php'; ?>

    <main>
        <div class="page-header">
            <h1><i class="fas fa-envelope"></i> Contact Us</h1>
            <p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>

        <div class="contact-grid">
            <div class="contact-info">
                <div class="info-card">
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <h3>Email</h3>
                        <p><a href="mailto:christolabiyi35@gmail.com">christolabiyi35@gmail.com</a></p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="icon"><i class="fas fa-phone"></i></div>
                    <div>
                        <h3>Phone</h3>
                        <p><a href="tel:+2348167714860">+234 816 771 4860</a></p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <h3>Office Hours</h3>
                        <p>Monday - Friday: 8:00 AM - 6:00 PM<br>Saturday: 9:00 AM - 2:00 PM</p>
                    </div>
                </div>

                <a href="https://wa.me/2348167714860" target="_blank" class="whatsapp-btn">
                    <i class="fab fa-whatsapp" style="font-size: 1.5rem;"></i>
                    Chat on WhatsApp
                </a>
            </div>

            <div class="form-card">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Thank you! Your message has been sent. We'll get back to you soon.
                    </div>
                <?php else: ?>
                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <!-- Honeypot -->
                        <div class="honeypot">
                            <input type="text" name="company" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label>Your Name <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter your name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Email Address <span class="required">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="What is this about?" value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Message <span class="required">*</span></label>
                            <textarea name="message" class="form-control" placeholder="Type your message here..." required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include '../includes/theme-selector.php'; ?>
</body>

</html>