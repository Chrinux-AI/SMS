<?php
/**
 * Visitor Portal - Homepage
 * Landing page for visitors exploring Verdant SMS
 */
$page_title = "Visitor Portal - Verdant SMS";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore Verdant SMS - Nigeria's premier school management system. View features, request demos, and learn more.">
    <title><?php echo $page_title; ?></title>
    <?php include '../includes/head-meta.php'; ?>
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
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--darker);
            color: #fff;
            line-height: 1.6;
        }
        .bg-animation {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            background: radial-gradient(ellipse at 20% 20%, rgba(0, 191, 255, 0.1) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 80%, rgba(138, 43, 226, 0.1) 0%, transparent 50%);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        main {
            padding: 100px 2rem 60px;
        }
        .hero {
            text-align: center;
            padding: 60px 0;
        }
        .hero h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero p {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.7);
            max-width: 700px;
            margin: 0 auto 2rem;
        }
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        .quick-link-card {
            background: rgba(20, 20, 30, 0.9);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
        }
        .quick-link-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
            box-shadow: 0 0 30px rgba(0, 191, 255, 0.3);
        }
        .quick-link-card i {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .quick-link-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .quick-link-card p {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
        }
        .features-section {
            padding: 60px 0;
        }
        .features-section h2 {
            font-family: 'Orbitron', sans-serif;
            text-align: center;
            font-size: 2rem;
            margin-bottom: 3rem;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        .feature-item i {
            font-size: 1.5rem;
            color: var(--primary);
            width: 40px;
        }
        .feature-item span {
            font-size: 0.95rem;
        }
        .cta-section {
            text-align: center;
            padding: 60px 2rem;
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.1), rgba(138, 43, 226, 0.1));
            border: 1px solid var(--border);
            border-radius: 30px;
            margin: 3rem 0;
        }
        .cta-section h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .btn {
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 25px rgba(0, 191, 255, 0.5);
        }
        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }
        .btn-outline:hover {
            background: var(--primary);
            color: var(--dark);
        }
    </style>
</head>
<body>
    <div class="bg-animation"></div>

    <?php include '../includes/visitor-nav.php'; ?>

    <main>
        <div class="container">
            <div class="hero">
                <h1><i class="fas fa-leaf"></i> Welcome to Verdant SMS</h1>
                <p>Nigeria's most comprehensive School Management System. Explore our features, request a demo, or register your child today.</p>
            </div>

            <div class="quick-links">
                <a href="about.php" class="quick-link-card">
                    <i class="fas fa-info-circle"></i>
                    <h3>About Us</h3>
                    <p>Learn about our mission to transform Nigerian education</p>
                </a>
                <a href="features.php" class="quick-link-card">
                    <i class="fas fa-star"></i>
                    <h3>Features</h3>
                    <p>Explore our 42 powerful modules</p>
                </a>
                <a href="demo-request.php" class="quick-link-card">
                    <i class="fas fa-desktop"></i>
                    <h3>Request Demo</h3>
                    <p>See Verdant SMS in action</p>
                </a>
                <a href="faq.php" class="quick-link-card">
                    <i class="fas fa-question-circle"></i>
                    <h3>FAQ</h3>
                    <p>Answers to common questions</p>
                </a>
                <a href="contact.php" class="quick-link-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Contact Us</h3>
                    <p>Get in touch with our team</p>
                </a>
                <a href="../register.php" class="quick-link-card">
                    <i class="fas fa-user-plus"></i>
                    <h3>Register</h3>
                    <p>Start your registration process</p>
                </a>
            </div>

            <section class="features-section">
                <h2><i class="fas fa-rocket"></i> Why Choose Verdant SMS?</h2>
                <div class="features-grid">
                    <div class="feature-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Nigerian Curriculum (P1-SSS3)</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-chart-line"></i>
                        <span>WAEC/NECO Grading (A1-F9)</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Works on Any Device</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Bank-Grade Security</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-fingerprint"></i>
                        <span>Biometric Login</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-credit-card"></i>
                        <span>Online Fee Payment</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-comments"></i>
                        <span>Real-time Messaging</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-bus"></i>
                        <span>Transport Tracking</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-book"></i>
                        <span>Digital Library</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-brain"></i>
                        <span>AI-Powered Analytics</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Timetable Generator</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-file-invoice"></i>
                        <span>Smart Report Cards</span>
                    </div>
                </div>
            </section>

            <section class="cta-section">
                <h2>Ready to Transform Your School?</h2>
                <p style="color: rgba(255,255,255,0.7); max-width: 600px; margin: 0 auto;">Join hundreds of Nigerian schools already using Verdant SMS to streamline their operations.</p>
                <div class="cta-buttons">
                    <a href="demo-request.php" class="btn btn-primary">
                        <i class="fas fa-play"></i> Request Free Demo
                    </a>
                    <a href="../login.php" class="btn btn-outline">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </div>
            </section>
        </div>
    </main>

    <?php include '../includes/theme-selector.php'; ?>
</body>
</html>
