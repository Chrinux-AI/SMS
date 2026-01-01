<?php

/**
 * Demo Request - Visitor Page
 * Form for requesting a demo of the system
 */
require_once '../includes/config.php';
require_once '../includes/database.php';

$page_title = 'Request Demo - Verdant SMS';
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $school_name = trim($_POST['school_name'] ?? '');
    $contact_name = trim($_POST['contact_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $student_count = $_POST['student_count'] ?? '';
    $message = trim($_POST['message'] ?? '');

    // Honeypot check
    if (!empty($_POST['website'])) {
        $error = 'Spam detected.';
    }
    // Basic validation
    elseif (empty($school_name) || empty($contact_name) || empty($email) || empty($phone)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!preg_match('/^(\+234|0)[789][01]\d{8}$/', $phone)) {
        $error = 'Please enter a valid Nigerian phone number.';
    } else {
        try {
            // Check rate limiting (1 request per email per 24 hours)
            $existing = db()->fetchOne(
                "SELECT id FROM demo_requests WHERE email = ? AND created_at > NOW() - INTERVAL 24 HOUR",
                [$email]
            );

            if ($existing) {
                $error = 'You have already submitted a demo request. Please wait 24 hours before submitting another.';
            } else {
                db()->insert('demo_requests', [
                    'school_name' => $school_name,
                    'contact_name' => $contact_name,
                    'email' => $email,
                    'phone' => $phone,
                    'student_count' => $student_count,
                    'message' => $message,
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $success = true;

                // Send notification email to admin
                require_once '../includes/functions.php';

                $admin_subject = "New Demo Request from {$school_name}";
                $admin_message = "
                    <h2>New Demo Request Received</h2>
                    <div class='info-box'>
                        <p><strong>School Name:</strong> {$school_name}</p>
                        <p><strong>Contact Person:</strong> {$contact_name}</p>
                        <p><strong>Email:</strong> {$email}</p>
                        <p><strong>Phone:</strong> {$phone}</p>
                        <p><strong>Student Count:</strong> {$student_count}</p>
                        <p><strong>Date:</strong> " . date('d/m/Y H:i:s') . "</p>
                    </div>
                    <h3>Additional Information:</h3>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                    <p><a href='mailto:{$email}' class='button'>Reply to {$contact_name}</a></p>
                ";

                send_email(
                    'christolabiyi35@gmail.com',
                    $admin_subject,
                    $admin_message,
                    'Verdant SMS Demo System'
                );

                // Send confirmation email to requester
                $requester_subject = "Demo Request Received - Verdant SMS";
                $requester_message = "
                    <h2>Thank You for Your Interest!</h2>
                    <p>Dear {$contact_name},</p>
                    <p>We have received your demo request for <strong>{$school_name}</strong>.</p>
                    <div class='info-box'>
                        <p>Our team will review your request and contact you within <strong>24 hours</strong> to schedule your personalized demo.</p>
                    </div>
                    <h3>What happens next?</h3>
                    <ul>
                        <li>Our sales team will reach out to you via email or phone</li>
                        <li>We'll schedule a convenient time for your demo</li>
                        <li>You'll see all features of Verdant SMS in action</li>
                        <li>We'll answer all your questions and discuss pricing</li>
                    </ul>
                    <p>If you have any urgent questions, feel free to contact us:</p>
                    <p><strong>Email:</strong> christolabiyi35@gmail.com<br>
                    <strong>Phone:</strong> +234 816 771 4860<br>
                    <strong>WhatsApp:</strong> <a href='https://wa.me/2348167714860'>Chat with us</a></p>
                    <p>Best regards,<br>The Verdant SMS Team</p>
                ";

                send_email(
                    $email,
                    $requester_subject,
                    $requester_message,
                    'Verdant SMS'
                );
            }
        } catch (Exception $e) {
            $error = 'Something went wrong. Please try again later.';
            error_log("Demo request error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Request a demo of Verdant SMS for your Nigerian school.">
    <title><?php echo $page_title; ?></title>
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
            max-width: 700px;
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

        select.form-control {
            cursor: pointer;
        }

        select.form-control option {
            background: var(--dark);
            color: #fff;
        }

        textarea.form-control {
            min-height: 120px;
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

        .success-message {
            text-align: center;
            padding: 3rem;
        }

        .success-message i {
            font-size: 4rem;
            color: var(--accent);
            margin-bottom: 1.5rem;
        }

        .success-message h2 {
            font-family: 'Orbitron', sans-serif;
            margin-bottom: 1rem;
        }

        .success-message p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 2rem;
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
            <h1><i class="fas fa-calendar-check"></i> Request a Demo</h1>
            <p>See Verdant SMS in action. Fill out the form and we'll contact you within 24 hours.</p>
        </div>

        <div class="form-card">
            <?php if ($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <h2>Request Received!</h2>
                    <p>Thank you for your interest in Verdant SMS. We'll contact you within 24 hours to schedule your demo.</p>
                    <a href="http://localhost/attendance/" class="btn-submit" style="text-decoration: none; display: inline-flex; width: auto; padding: 0.8rem 2rem;">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
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
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label>School Name <span class="required">*</span></label>
                        <input type="text" name="school_name" class="form-control" placeholder="e.g., Verdant International College" required value="<?php echo htmlspecialchars($_POST['school_name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Contact Person <span class="required">*</span></label>
                        <input type="text" name="contact_name" class="form-control" placeholder="e.g., Mr. Chukwudi Okonkwo" required value="<?php echo htmlspecialchars($_POST['contact_name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="e.g., admin@yourschool.edu.ng" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Phone Number <span class="required">*</span></label>
                        <input type="tel" name="phone" class="form-control" placeholder="e.g., 08012345678" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Number of Students</label>
                        <select name="student_count" class="form-control">
                            <option value="">Select range...</option>
                            <option value="1-100">1 - 100 students</option>
                            <option value="101-300">101 - 300 students</option>
                            <option value="301-500">301 - 500 students</option>
                            <option value="501-1000">501 - 1,000 students</option>
                            <option value="1001+">1,000+ students</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Additional Information</label>
                        <textarea name="message" class="form-control" placeholder="Tell us about your school and specific needs..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Submit Request
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/theme-selector.php'; ?>
</body>

</html>
