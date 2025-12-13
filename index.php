<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/theme-loader.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Verdant SMS - Complete 42-Module School Management System for Nigerian Schools. Empowering Education Excellence.">
    <meta name="theme-color" content="#00BFFF">
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="assets/images/icons/icon-192x192.png">
    <title>Verdant SMS - Advanced School Management Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Orbitron:wght@400;500;700;900&display=swap" rel="stylesheet">
    <?php output_theme_css(); ?>
    <style>
        :root {
            --primary: #00BFFF;
            --secondary: #8A2BE2;
            --accent: #00FF7F;
            --warning: #FFD700;
            --danger: #FF4757;
            --dark: #0a0a0f;
            --darker: #05050a;
            --card-bg: rgba(20, 20, 30, 0.8);
            --glass: rgba(255, 255, 255, 0.05);
            --border: rgba(0, 191, 255, 0.2);
            --glow: 0 0 20px rgba(0, 191, 255, 0.3);
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
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background:
                radial-gradient(ellipse at 20% 20%, rgba(0, 191, 255, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(138, 43, 226, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(0, 255, 127, 0.05) 0%, transparent 70%);
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
        }

        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Header */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 1rem 2rem;
            background: rgba(10, 10, 15, 0.9);
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
            font-size: 1.5rem;
            color: white;
        }

        .logo-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        nav a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        nav a:hover {
            color: var(--primary);
            background: rgba(0, 191, 255, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
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

        /* Mobile menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 120px 2rem 60px;
            position: relative;
        }

        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-text h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }

        .hero-text h1 span {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-text p {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 2rem;
            max-width: 500px;
        }

        .hero-stats {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .stat-box {
            text-align: center;
            padding: 1rem;
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 12px;
        }

        .stat-box .number {
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-box .label {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* Dashboard Preview */
        .dashboard-preview {
            position: relative;
            perspective: 1000px;
        }

        .preview-window {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--glow), 0 25px 50px rgba(0, 0, 0, 0.5);
            transform: rotateY(-5deg) rotateX(5deg);
            transition: transform 0.5s;
        }

        .preview-window:hover {
            transform: rotateY(0) rotateX(0);
        }

        .preview-header {
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.2), rgba(138, 43, 226, 0.2));
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 1px solid var(--border);
        }

        .preview-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .preview-dot.red { background: #ff5f57; }
        .preview-dot.yellow { background: #febc2e; }
        .preview-dot.green { background: #28c840; }

        .preview-body {
            padding: 1.5rem;
            display: grid;
            gap: 1rem;
        }

        .mini-card {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .mini-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .mini-card-icon.blue { background: rgba(0, 191, 255, 0.2); color: var(--primary); }
        .mini-card-icon.purple { background: rgba(138, 43, 226, 0.2); color: var(--secondary); }
        .mini-card-icon.green { background: rgba(0, 255, 127, 0.2); color: var(--accent); }
        .mini-card-icon.yellow { background: rgba(255, 215, 0, 0.2); color: var(--warning); }

        .mini-card-info h4 {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .mini-card-info p {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
        }

        /* Section Styling */
        section {
            padding: 100px 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(0, 191, 255, 0.1);
            border: 1px solid var(--border);
            border-radius: 20px;
            font-size: 0.85rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .section-header h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .section-header p {
            color: rgba(255, 255, 255, 0.6);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.4s;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--glow);
            border-color: var(--primary);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.95rem;
        }

        /* Nigeria Badge */
        .nigeria-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #008751, #00A368);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            color: white;
            margin-bottom: 1rem;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.1), rgba(138, 43, 226, 0.1));
            border: 1px solid var(--border);
            border-radius: 30px;
            padding: 4rem 2rem;
            text-align: center;
            margin: 4rem auto;
            max-width: 1000px;
        }

        .cta-section h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .cta-section p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Contact Section */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .contact-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
        }

        .contact-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
        }

        .contact-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .contact-card h4 {
            margin-bottom: 0.5rem;
        }

        .contact-card p {
            color: rgba(255, 255, 255, 0.7);
        }

        .contact-card a {
            color: var(--primary);
            text-decoration: none;
        }

        .contact-card a:hover {
            text-decoration: underline;
        }

        /* Footer */
        footer {
            background: rgba(5, 5, 10, 0.9);
            border-top: 1px solid var(--border);
            padding: 4rem 2rem 2rem;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-brand p {
            color: rgba(255, 255, 255, 0.6);
            margin-top: 1rem;
            font-size: 0.95rem;
        }

        .footer-column h4 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 0.75rem;
        }

        .footer-column ul a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-column ul a:hover {
            color: var(--primary);
        }

        .footer-bottom {
            max-width: 1400px;
            margin: 0 auto;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }

            .hero-stats {
                justify-content: center;
            }

            .hero-buttons {
                justify-content: center;
            }

            .dashboard-preview {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }

            nav {
                display: none;
            }

            nav.active {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(10, 10, 15, 0.98);
                padding: 1rem;
                border-bottom: 1px solid var(--border);
            }

            .footer-content {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .hero-text h1 {
                font-size: 2rem;
            }

            .section-header h2 {
                font-size: 1.75rem;
            }

            .hero-stats {
                flex-wrap: wrap;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <!-- Animated Background -->
    <div class="bg-animation"></div>
    <div class="grid-overlay"></div>

    <!-- Header -->
    <header>
        <a href="/" class="logo">
            <div class="logo-icon"><i class="fas fa-leaf"></i></div>
            <span class="logo-text">Verdant SMS</span>
        </a>
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>
        <nav id="mainNav">
            <a href="#about">About</a>
            <a href="#features">Features</a>
            <a href="visitor/demo-request.php">Request Demo</a>
            <a href="#contact">Contact</a>
            <a href="login.php" class="btn btn-outline">Login</a>
            <a href="register.php" class="btn btn-primary">Student Registration</a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <div class="nigeria-badge">
                    <i class="fas fa-flag"></i> Built for Nigerian Schools
                </div>
                <h1>Empowering <span>Nigerian Education</span> Excellence</h1>
                <p>Verdant SMS is a complete 42-module School Management System designed specifically for Nigerian schools. From Primary to Senior Secondary, we've got you covered.</p>
                
                <div class="hero-stats">
                    <div class="stat-box">
                        <div class="number">42</div>
                        <div class="label">Modules</div>
                    </div>
                    <div class="stat-box">
                        <div class="number">8</div>
                        <div class="label">Themes</div>
                    </div>
                    <div class="stat-box">
                        <div class="number">P1-SSS3</div>
                        <div class="label">Classes</div>
                    </div>
                </div>
                
                <div class="hero-buttons">
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="register.php" class="btn btn-outline">
                        <i class="fas fa-user-plus"></i> Student Registration
                    </a>
                </div>
            </div>

            <div class="dashboard-preview">
                <div class="preview-window">
                    <div class="preview-header">
                        <div class="preview-dot red"></div>
                        <div class="preview-dot yellow"></div>
                        <div class="preview-dot green"></div>
                    </div>
                    <div class="preview-body">
                        <div class="mini-card">
                            <div class="mini-card-icon blue"><i class="fas fa-user-graduate"></i></div>
                            <div class="mini-card-info">
                                <h4>Total Students</h4>
                                <p>2,847</p>
                            </div>
                        </div>
                        <div class="mini-card">
                            <div class="mini-card-icon purple"><i class="fas fa-chalkboard-teacher"></i></div>
                            <div class="mini-card-info">
                                <h4>Teaching Staff</h4>
                                <p>156</p>
                            </div>
                        </div>
                        <div class="mini-card">
                            <div class="mini-card-icon green"><i class="fas fa-percentage"></i></div>
                            <div class="mini-card-info">
                                <h4>Attendance Today</h4>
                                <p>94.2%</p>
                            </div>
                        </div>
                        <div class="mini-card">
                            <div class="mini-card-icon yellow"><i class="fas fa-money-bill-wave"></i></div>
                            <div class="mini-card-info">
                                <h4>Fees Collected</h4>
                                <p>₦12.5M</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about">
        <div class="section-header">
            <span class="section-badge"><i class="fas fa-info-circle"></i> About Us</span>
            <h2>What is Verdant SMS?</h2>
            <p>A complete school management solution tailored for Nigerian education</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(0, 191, 255, 0.2); color: var(--primary);">
                    <i class="fas fa-school"></i>
                </div>
                <h3>Nigerian Curriculum</h3>
                <p>Fully aligned with Nigerian education system - Primary 1 to SSS 3, WAEC/NECO grading (A1-F9), and 3-term academic calendar.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(138, 43, 226, 0.2); color: var(--secondary);">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure & Modern</h3>
                <p>Bank-grade security with biometric login, OTP verification, and encrypted data. Your students' information is safe with us.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(0, 255, 127, 0.2); color: var(--accent);">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Works Everywhere</h3>
                <p>Progressive Web App (PWA) that works on any device - desktop, tablet, or phone. Even works offline!</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features">
        <div class="section-header">
            <span class="section-badge"><i class="fas fa-star"></i> 42 Modules</span>
            <h2>Everything Your School Needs</h2>
            <p>Comprehensive tools for every aspect of school management</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(0, 191, 255, 0.2); color: var(--primary);">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3>Admissions & Enrollment</h3>
                <p>Online entrance exams, automated enrollment, and digital registration with document uploads.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(255, 215, 0, 0.2); color: var(--warning);">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h3>Attendance Management</h3>
                <p>Biometric, QR code, and manual attendance. Automatic SMS/email alerts to parents for absences.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(138, 43, 226, 0.2); color: var(--secondary);">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Grades & Report Cards</h3>
                <p>Nigerian grading system (A1-F9), CA scores, exam marks, position rankings, and PDF report cards.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(0, 255, 127, 0.2); color: var(--accent);">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h3>Fee Management</h3>
                <p>Paystack/Flutterwave integration, installment plans, receipts, and outstanding fee tracking.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(255, 71, 87, 0.2); color: var(--danger);">
                    <i class="fas fa-book"></i>
                </div>
                <h3>Library System</h3>
                <p>Book cataloging, issue/return tracking, overdue fines, e-books, and past questions repository.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(0, 191, 255, 0.2); color: var(--primary);">
                    <i class="fas fa-bus"></i>
                </div>
                <h3>Transport & Hostel</h3>
                <p>Bus routes, driver management, hostel allocation, and room assignments.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section>
        <div class="cta-section">
            <h2>Ready to Transform Your School?</h2>
            <p>Join hundreds of Nigerian schools already using Verdant SMS to streamline their operations and enhance student success.</p>
            <div class="cta-buttons">
                <a href="visitor/demo-request.php" class="btn btn-primary">
                    <i class="fas fa-calendar-check"></i> Request a Demo
                </a>
                <a href="#contact" class="btn btn-outline">
                    <i class="fas fa-phone"></i> Contact Us
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact">
        <div class="section-header">
            <span class="section-badge"><i class="fas fa-envelope"></i> Contact</span>
            <h2>Get in Touch</h2>
            <p>We're here to help you get started</p>
        </div>

        <div class="contact-grid">
            <div class="contact-card">
                <i class="fas fa-envelope"></i>
                <h4>Email</h4>
                <p><a href="mailto:christolabiyi35@gmail.com">christolabiyi35@gmail.com</a></p>
            </div>
            <div class="contact-card">
                <i class="fas fa-phone"></i>
                <h4>Phone</h4>
                <p><a href="tel:+2348167714860">+234 816 771 4860</a></p>
            </div>
            <div class="contact-card">
                <i class="fab fa-whatsapp"></i>
                <h4>WhatsApp</h4>
                <p><a href="https://wa.me/2348167714860" target="_blank">Chat with us</a></p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-brand">
                <a href="/" class="logo">
                    <div class="logo-icon"><i class="fas fa-leaf"></i></div>
                    <span class="logo-text">Verdant SMS</span>
                </a>
                <p>Empowering Nigerian education with modern technology. From Primary to Senior Secondary, we provide comprehensive school management solutions.</p>
            </div>

            <div class="footer-column">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="visitor/demo-request.php">Request Demo</a></li>
                    <li><a href="visitor/faq.php">FAQ</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Support</h4>
                <ul>
                    <li><a href="#contact">Contact Us</a></li>
                    <li><a href="docs/SETUP_GUIDE.md">Setup Guide</a></li>
                    <li><a href="forum/">Community</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Legal</h4>
                <ul>
                    <li><a href="visitor/privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="LICENSE">License</a></li>
                    <li><a href="SECURITY.md">Security</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 Verdant SMS. All rights reserved. Version 3.0.0</p>
            <div style="display: flex; gap: 1rem;">
                <a href="https://github.com/Chrinux-AI/SMS" style="color: var(--primary);"><i class="fab fa-github"></i></a>
                <a href="https://wa.me/2348167714860" style="color: var(--primary);"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('mainNav').classList.toggle('active');
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.style.background = 'rgba(5, 5, 10, 0.98)';
                header.style.boxShadow = '0 0 30px rgba(0, 191, 255, 0.2)';
            } else {
                header.style.background = 'rgba(10, 10, 15, 0.9)';
                header.style.boxShadow = 'none';
            }
        });

        // Animate elements on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.feature-card, .contact-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });
    </script>

    <?php include 'includes/theme-selector.php'; ?>
</body>

</html>
