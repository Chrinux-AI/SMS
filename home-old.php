<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Verdant School Management System - Complete 42-Module Education Platform with AI Analytics, Biometric Attendance & LMS Integration">
    <meta name="keywords" content="school management, education, attendance, LMS, student portal, teacher portal">
    <meta name="theme-color" content="#00BFFF">

    <!-- PWA Support -->
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="assets/images/icons/icon-192x192.png">

    <title>Verdant SMS - School Management System</title>

    <!-- Cyberpunk UI Framework -->
    <link rel="stylesheet" href="assets/css/cyberpunk-ui.css">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Homepage Specific Styles */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: linear-gradient(135deg, #0A0A0A 0%, #1a1a2e 50%, #0A0A0A 100%);
            overflow: hidden;
        }

        .hero-content {
            text-align: center;
            z-index: 10;
            max-width: 1200px;
            padding: 40px;
        }

        .hero-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 4rem;
            font-weight: 900;
            background: linear-gradient(135deg, #00BFFF, #8A2BE2, #00FF7F);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            text-shadow: 0 0 40px rgba(0, 191, 255, 0.5);
            animation: titleGlow 3s ease-in-out infinite;
        }

        @keyframes titleGlow {

            0%,
            100% {
                filter: brightness(1);
            }

            50% {
                filter: brightness(1.3);
            }
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 15px;
            font-weight: 300;
        }

        .hero-description {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 50px;
            line-height: 1.8;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 60px;
        }

        .cta-btn {
            padding: 18px 45px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cta-btn-primary {
            background: linear-gradient(135deg, #00BFFF, #0080FF);
            color: white;
            border: 2px solid #00BFFF;
            box-shadow: 0 0 30px rgba(0, 191, 255, 0.4);
        }

        .cta-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 40px rgba(0, 191, 255, 0.6);
        }

        .cta-btn-secondary {
            background: rgba(138, 43, 226, 0.1);
            color: #8A2BE2;
            border: 2px solid #8A2BE2;
            box-shadow: 0 0 20px rgba(138, 43, 226, 0.3);
        }

        .cta-btn-secondary:hover {
            background: rgba(138, 43, 226, 0.2);
            transform: translateY(-3px);
            box-shadow: 0 0 30px rgba(138, 43, 226, 0.5);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
        }

        .feature-card {
            background: rgba(30, 30, 30, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 191, 255, 0.2);
            border-radius: 15px;
            padding: 35px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .feature-card:hover {
            border-color: #00BFFF;
            box-shadow: 0 0 30px rgba(0, 191, 255, 0.3);
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 3.5rem;
            background: linear-gradient(135deg, #00BFFF, #8A2BE2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }

        .feature-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.3rem;
            color: #00BFFF;
            margin-bottom: 12px;
        }

        .feature-description {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .quick-links {
            background: rgba(20, 20, 40, 0.8);
            border-top: 2px solid rgba(0, 191, 255, 0.3);
            padding: 60px 40px;
        }

        .section-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            text-align: center;
            background: linear-gradient(135deg, #00BFFF, #00FF7F);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 50px;
        }

        .links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .link-card {
            background: rgba(30, 30, 30, 0.6);
            border: 1px solid rgba(0, 191, 255, 0.2);
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
        }

        .link-card:hover {
            border-color: #00BFFF;
            box-shadow: 0 0 20px rgba(0, 191, 255, 0.4);
            transform: scale(1.05);
        }

        .link-card i {
            font-size: 2.5rem;
            color: #00BFFF;
            margin-bottom: 15px;
        }

        .link-card-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }

        .link-card-desc {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .stats-section {
            padding: 80px 40px;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .stat-item {
            padding: 30px;
        }

        .stat-number {
            font-family: 'Orbitron', sans-serif;
            font-size: 3.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #00BFFF, #00FF7F);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
            margin-top: 10px;
        }

        .footer {
            background: rgba(10, 10, 10, 0.9);
            border-top: 2px solid rgba(0, 191, 255, 0.3);
            padding: 40px;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .cta-btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="cyber-bg">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">VERDANT SMS</h1>
            <h2 class="hero-subtitle">School Management System</h2>
            <p class="hero-description">
                Complete 42-Module Education Platform with AI Analytics, Biometric Attendance,<br>
                LMS Integration, PWA Support & Advanced Cyberpunk UI
            </p>

            <div class="cta-buttons">
                <a href="login.php" class="cta-btn cta-btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="register.php" class="cta-btn cta-btn-secondary">
                    <i class="fas fa-user-plus"></i> Register Now
                </a>
                <a href="docs/README.html" class="cta-btn cta-btn-secondary">
                    <i class="fas fa-book"></i> Documentation
                </a>
            </div>

            <!-- Key Features -->
            <div class="features-grid">
                <div class="feature-card" onclick="window.location.href='login.php'">
                    <div class="feature-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="feature-title">Student Portal</div>
                    <div class="feature-description">Attendance, grades, assignments, LMS integration, and study groups</div>
                </div>

                <div class="feature-card" onclick="window.location.href='login.php'">
                    <div class="feature-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="feature-title">Teacher Portal</div>
                    <div class="feature-description">Class management, grading, attendance tracking, and parent communication</div>
                </div>

                <div class="feature-card" onclick="window.location.href='login.php'">
                    <div class="feature-icon"><i class="fas fa-users-cog"></i></div>
                    <div class="feature-title">Admin Panel</div>
                    <div class="feature-description">Complete system control, analytics, reports, and user management</div>
                </div>

                <div class="feature-card" onclick="window.location.href='login.php'">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <div class="feature-title">Parent Portal</div>
                    <div class="feature-description">Monitor children's progress, attendance, fees, and teacher communication</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <h2 class="section-title">Platform Capabilities</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">42</div>
                <div class="stat-label">Modules</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">18</div>
                <div class="stat-label">User Roles</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100+</div>
                <div class="stat-label">Features</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Access</div>
            </div>
        </div>
    </section>

    <!-- Quick Links Section -->
    <section class="quick-links">
        <h2 class="section-title">Quick Access</h2>
        <div class="links-grid">
            <!-- Core -->
            <a href="login.php" class="link-card">
                <i class="fas fa-sign-in-alt"></i>
                <div class="link-card-title">Login</div>
                <div class="link-card-desc">Access your account</div>
            </a>

            <a href="register.php" class="link-card">
                <i class="fas fa-user-plus"></i>
                <div class="link-card-title">Register</div>
                <div class="link-card-desc">Create new account</div>
            </a>

            <a href="forgot-password.php" class="link-card">
                <i class="fas fa-key"></i>
                <div class="link-card-title">Reset Password</div>
                <div class="link-card-desc">Recover your account</div>
            </a>

            <!-- Admin Quick Links -->
            <a href="admin/dashboard.php" class="link-card">
                <i class="fas fa-tachometer-alt"></i>
                <div class="link-card-title">Admin Dashboard</div>
                <div class="link-card-desc">System overview</div>
            </a>

            <a href="admin/students.php" class="link-card">
                <i class="fas fa-user-graduate"></i>
                <div class="link-card-title">Student Management</div>
                <div class="link-card-desc">Manage students</div>
            </a>

            <a href="admin/teachers.php" class="link-card">
                <i class="fas fa-chalkboard-teacher"></i>
                <div class="link-card-title">Teacher Management</div>
                <div class="link-card-desc">Manage teachers</div>
            </a>

            <a href="admin/classes.php" class="link-card">
                <i class="fas fa-door-open"></i>
                <div class="link-card-title">Classes</div>
                <div class="link-card-desc">Class management</div>
            </a>

            <a href="admin/attendance.php" class="link-card">
                <i class="fas fa-clipboard-check"></i>
                <div class="link-card-title">Attendance</div>
                <div class="link-card-desc">Track attendance</div>
            </a>

            <!-- Student Links -->
            <a href="student/dashboard.php" class="link-card">
                <i class="fas fa-home"></i>
                <div class="link-card-title">Student Portal</div>
                <div class="link-card-desc">Student dashboard</div>
            </a>

            <a href="student/checkin.php" class="link-card">
                <i class="fas fa-qrcode"></i>
                <div class="link-card-title">Check-in</div>
                <div class="link-card-desc">Mark attendance</div>
            </a>

            <a href="student/lms-portal.php" class="link-card">
                <i class="fas fa-graduation-cap"></i>
                <div class="link-card-title">LMS Portal</div>
                <div class="link-card-desc">Learning management</div>
            </a>

            <!-- Teacher Links -->
            <a href="teacher/dashboard.php" class="link-card">
                <i class="fas fa-chart-line"></i>
                <div class="link-card-title">Teacher Portal</div>
                <div class="link-card-desc">Teacher dashboard</div>
            </a>

            <a href="teacher/my-classes.php" class="link-card">
                <i class="fas fa-book-open"></i>
                <div class="link-card-title">My Classes</div>
                <div class="link-card-desc">View classes</div>
            </a>

            <a href="teacher/grades.php" class="link-card">
                <i class="fas fa-award"></i>
                <div class="link-card-title">Gradebook</div>
                <div class="link-card-desc">Manage grades</div>
            </a>

            <!-- Parent Links -->
            <a href="parent/dashboard.php" class="link-card">
                <i class="fas fa-users"></i>
                <div class="link-card-title">Parent Portal</div>
                <div class="link-card-desc">Parent dashboard</div>
            </a>

            <a href="parent/children.php" class="link-card">
                <i class="fas fa-child"></i>
                <div class="link-card-title">My Children</div>
                <div class="link-card-desc">View children</div>
            </a>

            <!-- System Links -->
            <a href="messages.php" class="link-card">
                <i class="fas fa-envelope"></i>
                <div class="link-card-title">Messages</div>
                <div class="link-card-desc">Communication</div>
            </a>

            <a href="notices.php" class="link-card">
                <i class="fas fa-bullhorn"></i>
                <div class="link-card-title">Notices</div>
                <div class="link-card-desc">Announcements</div>
            </a>

            <a href="admin/reports.php" class="link-card">
                <i class="fas fa-chart-bar"></i>
                <div class="link-card-title">Reports</div>
                <div class="link-card-desc">Analytics & reports</div>
            </a>

            <a href="admin/enhanced-analytics.php" class="link-card">
                <i class="fas fa-brain"></i>
                <div class="link-card-title">AI Analytics</div>
                <div class="link-card-desc">Advanced insights</div>
            </a>

            <a href="docs/README.html" class="link-card">
                <i class="fas fa-book"></i>
                <div class="link-card-title">Documentation</div>
                <div class="link-card-desc">User guides</div>
            </a>

            <a href="system-overview.php" class="link-card">
                <i class="fas fa-info-circle"></i>
                <div class="link-card-title">System Overview</div>
                <div class="link-card-desc">Platform info</div>
            </a>

            <a href="admin/settings.php" class="link-card">
                <i class="fas fa-cog"></i>
                <div class="link-card-title">Settings</div>
                <div class="link-card-desc">System configuration</div>
            </a>

            <a href="chat.php" class="link-card">
                <i class="fas fa-comments"></i>
                <div class="link-card-title">Live Chat</div>
                <div class="link-card-desc">Real-time messaging</div>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Verdant School Management System. All rights reserved.</p>
        <p style="margin-top: 10px;">
            <a href="docs/README.html" style="color: #00BFFF; text-decoration: none;">Documentation</a> |
            <a href="SECURITY.md" style="color: #00BFFF; text-decoration: none;">Security</a> |
            <a href="https://github.com/Chrinux-AI/SMS" style="color: #00BFFF; text-decoration: none;">GitHub</a>
        </p>
    </footer>

    <!-- PWA Install Prompt -->
    <script src="assets/js/pwa-manager.js"></script>
</body>

</html>