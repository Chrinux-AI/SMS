<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Verdant SMS - 42-Module School Management System with AI Analytics, Biometric Attendance & LMS Integration for Modern Educational Institutions.">
    <meta name="keywords" content="school management system, education software, AI analytics, biometric attendance, student portal, teacher portal, parent communication, LMS integration, 42 modules">
    <meta name="theme-color" content="#00BFFF">
    <meta name="author" content="Verdant SMS">

    <!-- PWA Support -->
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="assets/images/icons/icon-192x192.png">

    <title>Verdant SMS - 42-Module School Management System</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #00BFFF;
            --secondary-color: #8A2BE2;
            --accent-color: #00FF7F;
            --dark-bg: #0A0A0A;
            --card-bg: rgba(30, 30, 30, 0.8);
            --text-primary: #FFFFFF;
            --text-secondary: rgba(255, 255, 255, 0.8);
            --text-muted: rgba(255, 255, 255, 0.6);
            --border-color: rgba(0, 191, 255, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0A0A0A 0%, #1a1a2e 50%, #0A0A0A 100%);
            color: var(--text-secondary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Header Navigation */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid var(--border-color);
            z-index: 1000;
            padding: 1rem 0;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .brand-text h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-text p {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: -4px;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 10px 30px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(0, 191, 255, 0.5);
        }

        /* Mobile Menu */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 2rem 4rem;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 50% 50%, rgba(0, 191, 255, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            z-index: 1;
        }

        .hero-content h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-content p {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 15px 35px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            box-shadow: 0 0 20px rgba(0, 191, 255, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 30px rgba(0, 191, 255, 0.6);
        }

        .btn-secondary {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-secondary:hover {
            background: rgba(0, 191, 255, 0.1);
            transform: translateY(-3px);
        }

        /* Auth Card */
        .auth-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 0 40px rgba(0, 191, 255, 0.2);
        }

        .auth-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .auth-tab {
            flex: 1;
            padding: 1rem;
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .auth-tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .auth-form {
            display: none;
        }

        .auth-form.active {
            display: block;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 10px rgba(0, 191, 255, 0.3);
        }

        .forgot-password {
            text-align: right;
            margin-top: -0.5rem;
            margin-bottom: 1rem;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(0, 191, 255, 0.5);
        }

        /* About Section */
        .about {
            padding: 6rem 2rem;
            background: rgba(10, 10, 10, 0.5);
        }

        .section-title {
            text-align: center;
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            text-align: center;
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .about-card {
            text-align: center;
            padding: 2rem;
        }

        .about-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }

        .about-card h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .about-card p {
            color: var(--text-muted);
            line-height: 1.8;
        }

        /* Features/Services Section */
        .features {
            padding: 6rem 2rem;
        }

        .features-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2.5rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 2.5rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 0 30px rgba(0, 191, 255, 0.3);
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .feature-card .module-badge {
            display: inline-block;
            background: rgba(0, 191, 255, 0.2);
            color: var(--primary-color);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--text-muted);
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            padding: 0.5rem 0;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .feature-list li::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--accent-color);
        }

        /* Contact Section */
        .contact {
            padding: 6rem 2rem;
            background: rgba(10, 10, 10, 0.5);
        }

        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .contact-item {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            flex-shrink: 0;
        }

        .contact-details h3 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }

        .contact-details p {
            color: var(--text-muted);
            line-height: 1.6;
        }

        .contact-details a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .demo-form {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 2.5rem;
        }

        .demo-form h3 {
            font-size: 1.8rem;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        /* Footer */
        .footer {
            background: rgba(5, 5, 5, 0.95);
            border-top: 2px solid var(--border-color);
            padding: 3rem 2rem 1.5rem;
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .footer-about h3 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .footer-about p {
            color: var(--text-muted);
            line-height: 1.8;
            margin-bottom: 1rem;
        }

        .footer-section h4 {
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .footer-bottom a {
            color: var(--primary-color);
            text-decoration: none;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-buttons {
                justify-content: center;
            }

            .contact-container {
                grid-template-columns: 1fr;
            }

            .footer-container {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .mobile-toggle {
                display: block;
            }

            .nav-links {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 80px);
                background: rgba(10, 10, 10, 0.98);
                flex-direction: column;
                padding: 2rem;
                transition: left 0.3s ease;
                gap: 1rem;
            }

            .nav-links.active {
                left: 0;
            }

            .hero-content h2 {
                font-size: 2.5rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .footer-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Header Navigation -->
    <header class="header">
        <div class="nav-container">
            <a href="#" class="brand">
                <div class="brand-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="brand-text">
                    <h1>Verdant SMS</h1>
                    <p>42-Module Platform</p>
                </div>
            </a>

            <button class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>

            <nav>
                <ul class="nav-links" id="navLinks">
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#features">Services</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="login.php" class="btn-login">Client Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-container" style="grid-template-columns: 1fr; text-align: center; max-width: 900px;">
            <div class="hero-content">
                <h2>Modernize Your Institution with AI-Powered School Management</h2>
                <p>Comprehensive 42-module platform featuring AI analytics, biometric attendance, and LMS integration for academic excellence, seamless communication, and efficient administration. Transform your educational institution with Verdant SMS.</p>
                <div class="hero-buttons" style="justify-content: center;">
                    <a href="#features" class="btn btn-primary">
                        <i class="fas fa-rocket"></i> Explore Our Services
                    </a>
                    <a href="#contact" class="btn btn-secondary">
                        <i class="fas fa-calendar-alt"></i> Get a Demo
                    </a>
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Client Login
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="about" id="about">
        <h2 class="section-title">Why Choose Verdant SMS?</h2>
        <p class="section-subtitle">Trusted by leading educational institutions worldwide. 42 integrated modules supporting 18 user roles with advanced AI analytics.</p>

        <div class="about-container">
            <div class="about-card">
                <div class="about-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure & Reliable</h3>
                <p>Bank-level security with encrypted data storage and GDPR compliance. Your institutional data is safe with us.</p>
            </div>

            <div class="about-card">
                <div class="about-icon">
                    <i class="fas fa-globe"></i>
                </div>
                <h3>Cloud-Based Access</h3>
                <p>Access from anywhere, anytime. Desktop, tablet, or mobile - seamless experience across all devices.</p>
            </div>

            <div class="about-card">
                <div class="about-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>Dedicated support team available round the clock to assist your institution. We're here when you need us.</p>
            </div>

            <div class="about-card">
                <div class="about-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <h3>Fully Integrated</h3>
                <p>All modules work seamlessly together. No data silos, no duplicate entries - just streamlined operations.</p>
            </div>
        </div>
    </section>

    <!-- Features/Services Section -->
    <section class="features" id="features">
        <h2 class="section-title">Comprehensive Services Rendered</h2>
        <p class="section-subtitle">Integrated modules designed to streamline every aspect of school management</p>

        <div class="features-container">
            <div class="features-grid">
                <!-- Academic & Grading -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <span class="module-badge">Admissions & Records</span>
                    <h3>Student Management</h3>
                    <p>Centralized student profiles, online admissions, and comprehensive record management with automated ID assignment (e.g., STU20250001). Track academic progress from enrollment to graduation.</p>
                    <ul class="feature-list">
                        <li>Online admissions processing</li>
                        <li>Automated student ID assignment</li>
                        <li>Comprehensive academic records</li>
                        <li>Real-time performance tracking</li>
                    </ul>
                </div>

                <!-- Parent/Teacher Communication -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <span class="module-badge">Class Management</span>
                    <h3>Teacher Portal</h3>
                    <p>Dedicated dashboard for teachers to manage classes, track student performance, submit grades, and communicate with parents through real-time chat and broadcast messaging.</p>
                    <ul class="feature-list">
                        <li>Class setup & management dashboard</li>
                        <li>Grade submission & tracking</li>
                        <li>Student performance analytics</li>
                        <li>Parent-teacher communication hub</li>
                    </ul>
                </div>

                <!-- Digital Attendance -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <span class="module-badge">Tracking & Biometrics</span>
                    <h3>Attendance System</h3>
                    <p>Advanced, paperless attendance logging utilizing QR codes, manual entry, and Biometric (fingerprint) support with real-time parent notifications and comprehensive tracking.</p>
                    <ul class="feature-list">
                        <li>Biometric fingerprint scanning</li>
                        <li>QR code check-in system</li>
                        <li>Manual attendance entry</li>
                        <li>Instant parent SMS/WhatsApp alerts</li>
                    </ul>
                </div>

                <!-- Parent Portal -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="module-badge">Child Monitoring</span>
                    <h3>Parent Portal</h3>
                    <p>Specialized access for parents to view children's grades, monitor attendance in real-time, facilitate secure online fee payments, and communicate directly with teachers.</p>
                    <ul class="feature-list">
                        <li>Real-time grade viewing</li>
                        <li>Attendance monitoring & alerts</li>
                        <li>Online fee payment processing</li>
                        <li>Direct teacher communication</li>
                    </ul>
                </div>

                <!-- Messaging & Chat -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <span class="module-badge">Communication Hub</span>
                    <h3>Messaging & Chat</h3>
                    <p>Real-time chat, instant messaging, and system-wide broadcast capabilities for staff, students, and parents with integrated Twilio WhatsApp notifications and email alerts.</p>
                    <ul class="feature-list">
                        <li>Real-time chat interface</li>
                        <li>Broadcast messaging system</li>
                        <li>WhatsApp integration (Twilio)</li>
                        <li>Email & SMS notifications</li>
                    </ul>
                </div>

                <!-- Academics -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="module-badge">Curriculum & Exams</span>
                    <h3>Academics</h3>
                    <p>Comprehensive tools for defining subjects, managing syllabus content, creating and scheduling examinations, and processing student assignments with automated grading support.</p>
                    <ul class="feature-list">
                        <li>Subject & syllabus management</li>
                        <li>Examination creation & scheduling</li>
                        <li>Assignment submission & grading</li>
                        <li>Curriculum tracking & reporting</li>
                    </ul>
                </div>

                <!-- Timetable -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span class="module-badge">Schedule Management</span>
                    <h3>Timetable</h3>
                    <p>Efficient creation and distribution of complex class and exam schedules across all user devices with conflict detection and automatic synchronization.</p>
                    <ul class="feature-list">
                        <li>Automated timetable generation</li>
                        <li>Conflict detection & resolution</li>
                        <li>Multi-device synchronization</li>
                        <li>Exam scheduling coordination</li>
                    </ul>
                </div>

                <!-- Reports & Analytics -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span class="module-badge">AI Analytics & Insights</span>
                    <h3>Reports & Analytics</h3>
                    <p>Advanced reporting module offering data visualization, AI-powered predictions, detailed security audit logs, and comprehensive institutional performance metrics.</p>
                    <ul class="feature-list">
                        <li>AI-powered predictive analytics</li>
                        <li>Interactive data visualization</li>
                        <li>Security audit logs & monitoring</li>
                        <li>Custom report generation</li>
                    </ul>
                </div>

                <!-- Finance & Fees -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <span class="module-badge">Financial Automation</span>
                    <h3>Finance & Fees</h3>
                    <p>Full management of fees, online fee collection, automated invoicing, integrated payroll processing, and comprehensive financial reporting for institutional transparency.</p>
                    <ul class="feature-list">
                        <li>Online fee collection & receipts</li>
                        <li>Automated invoice generation</li>
                        <li>Integrated HR payroll system</li>
                        <li>Financial dashboard & reports</li>
                    </ul>
                </div>
            </div>

            <!-- Extended Modules Note -->
            <div style="margin-top: 4rem; padding: 3rem; background: rgba(0, 191, 255, 0.05); border: 1px solid var(--border-color); border-radius: 15px; text-align: center;">
                <h3 style="color: var(--text-primary); font-size: 1.8rem; margin-bottom: 1rem;">
                    <i class="fas fa-puzzle-piece" style="color: var(--primary-color); margin-right: 10px;"></i>
                    Extended Functionality
                </h3>
                <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.8; max-width: 900px; margin: 0 auto;">
                    Beyond the 9 core modules showcased above, Verdant SMS provides <strong style="color: var(--primary-color);">33 Extended Modules</strong> (totaling 42 modules) covering specialized areas including:
                </p>
                <div style="margin-top: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; max-width: 1000px; margin-left: auto; margin-right: auto;">
                    <span style="color: var(--text-secondary); padding: 0.5rem;">📚 Library Management</span>
                    <span style="color: var(--text-secondary); padding: 0.5rem;">🚌 Transport Logistics</span>
                    <span style="color: var(--text-secondary); padding: 0.5rem;">🏠 Hostel Accommodation</span>
                    <span style="color: var(--text-secondary); padding: 0.5rem;">📦 Inventory Management</span>
                    <span style="color: var(--text-secondary); padding: 0.5rem;">🏥 Medical Records</span>
                    <span style="color: var(--text-secondary); padding: 0.5rem;">🎓 LMS Integration (LTI 1.3)</span>
                    <span style="color: var(--text-secondary); padding: 0.5rem;">🎖️ Alumni Tracking</span>
                    <span style="color: var(--text-secondary); padding: 0.5rem;">📱 PWA Support</span>
                    <span style="color: var(--text-secondary); padding: 0.5rem;">🍽️ Canteen Management</span>
                    <span style="color: var(--text-secondary); padding: 0.5rem;">👥 HR & Payroll</span>
                    <span style="color: var(--text-secondary); padding: 0.5rem;">🎪 Events & Activities</span>
                    <span style="color: var(--text-secondary); padding: 0.5rem;">📜 Certificates & IDs</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
        <h2 class="section-title">Get in Touch</h2>
        <p class="section-subtitle">Ready to transform your institution? Request a free demo or contact our team</p>

        <div class="contact-container">
            <div class="contact-info">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Call Us</h3>
                        <p><strong>+1 (555) 123-4567</strong></p>
                        <p>Mon - Fri, 9am - 5pm EST</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Email Us</h3>
                        <p><a href="mailto:sales@verdantsms.com">sales@verdantsms.com</a> - Sales Inquiries</p>
                        <p><a href="mailto:support@verdantsms.com">support@verdantsms.com</a> - Support</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Head Office</h3>
                        <p>123 Education Boulevard<br>
                            Suite 500<br>
                            New York, NY 10001<br>
                            United States</p>
                    </div>
                </div>
            </div>

            <!-- Demo Request Form -->
            <div class="demo-form">
                <h3>Request a Free Demo</h3>
                <form action="api/contact.php" method="POST">
                    <div class="form-group">
                        <label for="demo-name">Your Name *</label>
                        <input type="text" id="demo-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="demo-institution">Institution Name *</label>
                        <input type="text" id="demo-institution" name="institution" required>
                    </div>
                    <div class="form-group">
                        <label for="demo-email">Email *</label>
                        <input type="email" id="demo-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="demo-message">Message / Inquiry</label>
                        <textarea id="demo-message" name="message" rows="4" placeholder="Tell us about your requirements..."></textarea>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Request Demo
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-about">
                <h3>Verdant SMS</h3>
                <p>42-module school management platform trusted by educational institutions worldwide. AI analytics, biometric attendance, LMS integration, and comprehensive administration for academic excellence.</p>
                <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-muted);">
                    <strong>Version:</strong> 3.0.0 | <strong>License:</strong> Proprietary | <strong>Status:</strong> Production Ready
                </p>
                <p style="margin-top: 1.5rem;">
                    <strong>Follow Us:</strong><br>
                    <a href="#" style="color: var(--primary-color); margin-right: 15px;"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" style="color: var(--primary-color); margin-right: 15px;"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" style="color: var(--primary-color); margin-right: 15px;"><i class="fab fa-linkedin fa-lg"></i></a>
                    <a href="#" style="color: var(--primary-color);"><i class="fab fa-instagram fa-lg"></i></a>
                </p>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#features">Services</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="system-overview.php">System Overview</a></li>
                    <li><a href="README.md">Documentation</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>For Institutions</h4>
                <ul class="footer-links">
                    <li><a href="#contact">Request Demo</a></li>
                    <li><a href="#features">42 Modules Overview</a></li>
                    <li><a href="#about">Why Verdant SMS?</a></li>
                    <li><a href="login.php">LMS Integration (LTI 1.3)</a></li>
                    <li><a href="mailto:support@verdantsms.com">Support</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>User Access</h4>
                <ul class="footer-links">
                    <li><a href="login.php">Client Login</a></li>
                    <li><a href="register.php">Register Account</a></li>
                    <li><a href="forgot-password.php">Reset Password</a></li>
                    <li><a href="admin/dashboard.php">Admin Portal</a></li>
                    <li><a href="teacher/dashboard.php">Teacher Portal</a></li>
                    <li><a href="student/dashboard.php">Student Portal</a></li>
                    <li><a href="parent/dashboard.php">Parent Portal</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 Verdant SMS - 42-Module School Management System. All rights reserved.</p>
            <p style="margin-top: 10px;">
                <a href="privacy-policy.php">Privacy Policy</a> |
                <a href="terms-of-service.php">Terms of Service</a>
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const navLinks = document.getElementById('navLinks');

        mobileToggle?.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            const icon = mobileToggle.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });

        // Close mobile menu when clicking a link
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                const icon = mobileToggle?.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-bars');
                    icon.classList.remove('fa-times');
                }
            });
        });

        // Removed auth tabs (forms moved to separate login.php page)

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

        // Client Login now redirects to login.php
    </script>

    <!-- PWA Manager -->
    <script src="assets/js/pwa-manager.js"></script>
</body>

</html>