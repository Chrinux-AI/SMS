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
    <meta name="description" content="Verdant SMS - Complete 42-Module School Management System with AI Analytics, Biometric Attendance & LMS Integration.">
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
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(50px, 50px);
            }
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

        .preview-dot.red {
            background: #ff5f57;
        }

        .preview-dot.yellow {
            background: #febc2e;
        }

        .preview-dot.green {
            background: #28c840;
        }

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

        .mini-card-icon.blue {
            background: rgba(0, 191, 255, 0.2);
            color: var(--primary);
        }

        .mini-card-icon.purple {
            background: rgba(138, 43, 226, 0.2);
            color: var(--secondary);
        }

        .mini-card-icon.green {
            background: rgba(0, 255, 127, 0.2);
            color: var(--accent);
        }

        .mini-card-icon.yellow {
            background: rgba(255, 215, 0, 0.2);
            color: var(--warning);
        }

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

        /* Role Previews Section */
        .role-previews {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .role-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s;
            position: relative;
        }

        .role-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--glow);
            border-color: var(--primary);
        }

        .role-card-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.1), rgba(138, 43, 226, 0.1));
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .role-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .role-card-header h3 {
            font-size: 1.25rem;
        }

        .role-card-header span {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .role-card-body {
            padding: 1.5rem;
        }

        .role-features {
            list-style: none;
            margin-bottom: 1.5rem;
        }

        .role-features li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: rgba(255, 255, 255, 0.8);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .role-features li i {
            color: var(--accent);
            font-size: 0.85rem;
        }

        .role-card-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--border);
        }

        /* Dashboard Showcase */
        .dashboard-showcase {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2rem;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            min-height: 600px;
        }

        .showcase-sidebar {
            background: rgba(0, 0, 0, 0.3);
            padding: 1.5rem;
            border-right: 1px solid var(--border);
        }

        .showcase-nav {
            list-style: none;
        }

        .showcase-nav li {
            margin-bottom: 0.5rem;
        }

        .showcase-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .showcase-nav a:hover,
        .showcase-nav a.active {
            background: rgba(0, 191, 255, 0.1);
            color: var(--primary);
        }

        .showcase-nav a i {
            width: 20px;
            text-align: center;
        }

        .showcase-content {
            padding: 2rem;
        }

        .showcase-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .showcase-header h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
        }

        .showcase-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .showcase-stat {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
            transition: all 0.3s;
        }

        .showcase-stat:hover {
            border-color: var(--primary);
            box-shadow: var(--glow);
        }

        .showcase-stat i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .showcase-stat .value {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .showcase-stat .label {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .showcase-chart {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            height: 200px;
            display: flex;
            align-items: flex-end;
            gap: 0.5rem;
        }

        .chart-bar {
            flex: 1;
            background: linear-gradient(to top, var(--primary), var(--secondary));
            border-radius: 5px 5px 0 0;
            transition: all 0.3s;
            position: relative;
        }

        .chart-bar:hover {
            opacity: 0.8;
        }

        .chart-bar::after {
            content: attr(data-label);
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Module Grid */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .module-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .module-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: var(--glow);
        }

        .module-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .module-card h4 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .module-card p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
        }

        /* Features Section */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.4s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
            box-shadow: var(--glow);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.95rem;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.1), rgba(138, 43, 226, 0.1));
            border: 1px solid var(--border);
            border-radius: 30px;
            padding: 4rem;
            text-align: center;
            margin: 100px auto;
            max-width: 1000px;
        }

        .cta-section h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .cta-section p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.5);
            border-top: 1px solid var(--border);
            padding: 4rem 2rem 2rem;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr repeat(4, 1fr);
            gap: 3rem;
        }

        .footer-brand p {
            color: rgba(255, 255, 255, 0.6);
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .footer-column h4 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column li {
            margin-bottom: 0.5rem;
        }

        .footer-column a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .footer-column a:hover {
            color: var(--primary);
        }

        .footer-bottom {
            max-width: 1400px;
            margin: 3rem auto 0;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
        }

        /* Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
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

            .features-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-showcase {
                grid-template-columns: 1fr;
            }

            .showcase-sidebar {
                display: none;
            }

            .showcase-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-content {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            nav {
                display: none;
            }

            .hero-stats {
                flex-wrap: wrap;
            }

            .role-previews {
                grid-template-columns: 1fr;
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

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--darker);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>
    <div class="grid-overlay"></div>

    <!-- Header -->
    <header>
        <a href="index.php" class="logo">
            <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
            <span class="logo-text">Verdant SMS</span>
        </a>
        <nav>
            <a href="#features">Features</a>
            <a href="#dashboards">Dashboards</a>
            <a href="#modules">Modules</a>
            <a href="#roles">User Roles</a>
            <button onclick="openThemeModal()" class="btn btn-outline" style="border-color: #a855f7; color: #a855f7;">
                <i class="fas fa-palette"></i> Theme
            </button>
            <a href="login.php" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="register.php" class="btn btn-primary"><i class="fas fa-rocket"></i> Get Started</a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>The Future of <span>School Management</span> is Here</h1>
                <p>Complete 42-module platform with AI analytics, biometric attendance, LMS integration, and stunning cyberpunk UI. Built for modern educational institutions.</p>

                <div class="hero-stats">
                    <div class="stat-box">
                        <div class="number">42</div>
                        <div class="label">Modules</div>
                    </div>
                    <div class="stat-box">
                        <div class="number">25</div>
                        <div class="label">User Roles</div>
                    </div>
                    <div class="stat-box">
                        <div class="number">8</div>
                        <div class="label">Themes</div>
                    </div>
                    <div class="stat-box">
                        <div class="number">24/7</div>
                        <div class="label">Support</div>
                    </div>
                </div>

                <div class="hero-buttons">
                    <a href="login.php" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Access Portal</a>
                    <a href="register.php" class="btn btn-outline"><i class="fas fa-user-plus"></i> Register Now</a>
                </div>

                <div style="margin-top:2rem;display:flex;justify-content:center;">
                    <!-- Typing SVG indicator -->
                    <?php include 'assets/svg/typing-indicator.svg'; ?>
                </div>
            </div>

            <div class="dashboard-preview float-animation">
                <div class="preview-window">
                    <div class="preview-header">
                        <span class="preview-dot red"></span>
                        <span class="preview-dot yellow"></span>
                        <span class="preview-dot green"></span>
                        <span style="margin-left: auto; color: rgba(255,255,255,0.5); font-size: 0.8rem;">Admin Dashboard</span>
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
                                <h4>Teachers</h4>
                                <p>186</p>
                            </div>
                        </div>
                        <div class="mini-card">
                            <div class="mini-card-icon green"><i class="fas fa-check-circle"></i></div>
                            <div class="mini-card-info">
                                <h4>Today's Attendance</h4>
                                <p>94.2%</p>
                            </div>
                        </div>
                        <div class="mini-card">
                            <div class="mini-card-icon yellow"><i class="fas fa-dollar-sign"></i></div>
                            <div class="mini-card-info">
                                <h4>Fee Collection</h4>
                                <p>$124,580</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features">
        <div class="section-header">
            <span class="section-badge"><i class="fas fa-star"></i> Core Features</span>
            <h2>Powerful Tools for Modern Education</h2>
            <p>Everything you need to run your educational institution efficiently</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(0, 191, 255, 0.2); color: var(--primary);">
                    <i class="fas fa-fingerprint"></i>
                </div>
                <h3>Biometric Attendance</h3>
                <p>Advanced fingerprint, QR code, and facial recognition with instant parent notifications</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(138, 43, 226, 0.2); color: var(--secondary);">
                    <i class="fas fa-brain"></i>
                </div>
                <h3>AI Analytics</h3>
                <p>Predictive insights, performance trends, and data-driven decision making</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(0, 255, 127, 0.2); color: var(--accent);">
                    <i class="fas fa-book-reader"></i>
                </div>
                <h3>LMS Integration</h3>
                <p>Full LTI 1.3 compatible learning management with course content & assessments</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(255, 215, 0, 0.2); color: var(--warning);">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>PWA Support</h3>
                <p>Works offline, installable on any device with native app-like experience</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(255, 71, 87, 0.2); color: var(--danger);">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Enterprise Security</h3>
                <p>Role-based access, audit logs, encryption, and GDPR compliance</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(0, 191, 255, 0.2); color: var(--primary);">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>Real-time Communication</h3>
                <p>Instant messaging, WhatsApp integration, SMS alerts, and announcements</p>
            </div>
        </div>
    </section>

    <!-- Dashboard Showcase Section -->
    <section id="dashboards">
        <div class="section-header">
            <span class="section-badge"><i class="fas fa-desktop"></i> Live Preview</span>
            <h2>Experience Our Dashboard</h2>
            <p>Interactive preview of the admin control panel</p>
        </div>

        <div class="dashboard-showcase">
            <div class="showcase-sidebar">
                <ul class="showcase-nav">
                    <li><a href="#" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                    <li><a href="admin/students.php"><i class="fas fa-user-graduate"></i> Students</a></li>
                    <li><a href="admin/attendance.php"><i class="fas fa-clipboard-check"></i> Attendance</a></li>
                    <li><a href="admin/classes.php"><i class="fas fa-chalkboard"></i> Classes</a></li>
                    <li><a href="admin/fee-management.php"><i class="fas fa-dollar-sign"></i> Finance</a></li>
                    <li><a href="admin/reports.php"><i class="fas fa-file-alt"></i> Reports</a></li>
                    <li><a href="admin/analytics.php"><i class="fas fa-brain"></i> Analytics</a></li>
                    <li><a href="admin/communication.php"><i class="fas fa-comments"></i> Messages</a></li>
                    <li><a href="admin/settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                </ul>
            </div>

            <div class="showcase-content">
                <div class="showcase-header">
                    <h3><i class="fas fa-chart-line"></i> Dashboard Overview</h3>
                    <span style="color: rgba(255,255,255,0.5);">Live Data Preview</span>
                </div>

                <div class="showcase-grid">
                    <div class="showcase-stat">
                        <i class="fas fa-user-graduate" style="color: var(--primary);"></i>
                        <div class="value" style="color: var(--primary);">2,847</div>
                        <div class="label">Students</div>
                    </div>
                    <div class="showcase-stat">
                        <i class="fas fa-chalkboard-teacher" style="color: var(--secondary);"></i>
                        <div class="value" style="color: var(--secondary);">186</div>
                        <div class="label">Teachers</div>
                    </div>
                    <div class="showcase-stat">
                        <i class="fas fa-door-open" style="color: var(--accent);"></i>
                        <div class="value" style="color: var(--accent);">124</div>
                        <div class="label">Classes</div>
                    </div>
                    <div class="showcase-stat">
                        <i class="fas fa-percentage" style="color: var(--warning);"></i>
                        <div class="value" style="color: var(--warning);">94.2%</div>
                        <div class="label">Attendance</div>
                    </div>
                </div>

                <h4 style="margin-bottom: 1rem; color: rgba(255,255,255,0.8);"><i class="fas fa-chart-bar"></i> Weekly Attendance Trend</h4>
                <div class="showcase-chart">
                    <div class="chart-bar" style="height: 85%;" data-label="Mon"></div>
                    <div class="chart-bar" style="height: 92%;" data-label="Tue"></div>
                    <div class="chart-bar" style="height: 88%;" data-label="Wed"></div>
                    <div class="chart-bar" style="height: 95%;" data-label="Thu"></div>
                    <div class="chart-bar" style="height: 90%;" data-label="Fri"></div>
                    <div class="chart-bar" style="height: 60%;" data-label="Sat"></div>
                    <div class="chart-bar" style="height: 0%;" data-label="Sun"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Role-Based Previews -->
    <section id="roles">
        <div class="section-header">
            <span class="section-badge"><i class="fas fa-users"></i> 18 User Roles</span>
            <h2>Tailored Experience for Everyone</h2>
            <p>Dedicated dashboards and features for each user type</p>
        </div>

        <div class="role-previews">
            <!-- Admin -->
            <div class="role-card">
                <div class="role-card-header">
                    <div class="role-icon" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <h3>Administrator</h3>
                        <span>Full system control</span>
                    </div>
                </div>
                <div class="role-card-body">
                    <ul class="role-features">
                        <li><i class="fas fa-check"></i> Complete student & staff management</li>
                        <li><i class="fas fa-check"></i> Financial reports & fee collection</li>
                        <li><i class="fas fa-check"></i> System configuration & settings</li>
                        <li><i class="fas fa-check"></i> AI-powered analytics dashboard</li>
                        <li><i class="fas fa-check"></i> Audit logs & security management</li>
                    </ul>
                </div>
                <div class="role-card-footer">
                    <a href="admin/dashboard.php" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fas fa-arrow-right"></i> View Admin Dashboard
                    </a>
                </div>
            </div>

            <!-- Teacher -->
            <div class="role-card">
                <div class="role-card-header">
                    <div class="role-icon" style="background: linear-gradient(135deg, var(--accent), var(--primary)); color: white;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <h3>Teacher</h3>
                        <span>Class management</span>
                    </div>
                </div>
                <div class="role-card-body">
                    <ul class="role-features">
                        <li><i class="fas fa-check"></i> Take & manage attendance</li>
                        <li><i class="fas fa-check"></i> Grade assignments & exams</li>
                        <li><i class="fas fa-check"></i> Communicate with parents</li>
                        <li><i class="fas fa-check"></i> Create assignments & materials</li>
                        <li><i class="fas fa-check"></i> Student performance tracking</li>
                    </ul>
                </div>
                <div class="role-card-footer">
                    <a href="teacher/dashboard.php" class="btn btn-outline" style="width: 100%; justify-content: center;">
                        <i class="fas fa-arrow-right"></i> View Teacher Dashboard
                    </a>
                </div>
            </div>

            <!-- Student -->
            <div class="role-card">
                <div class="role-card-header">
                    <div class="role-icon" style="background: linear-gradient(135deg, var(--secondary), var(--danger)); color: white;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <h3>Student</h3>
                        <span>Learning portal</span>
                    </div>
                </div>
                <div class="role-card-body">
                    <ul class="role-features">
                        <li><i class="fas fa-check"></i> View grades & attendance</li>
                        <li><i class="fas fa-check"></i> Submit assignments online</li>
                        <li><i class="fas fa-check"></i> Access course materials</li>
                        <li><i class="fas fa-check"></i> Check timetable & exams</li>
                        <li><i class="fas fa-check"></i> Digital ID card</li>
                    </ul>
                </div>
                <div class="role-card-footer">
                    <a href="student/dashboard.php" class="btn btn-outline" style="width: 100%; justify-content: center;">
                        <i class="fas fa-arrow-right"></i> View Student Dashboard
                    </a>
                </div>
            </div>

            <!-- Parent -->
            <div class="role-card">
                <div class="role-card-header">
                    <div class="role-icon" style="background: linear-gradient(135deg, var(--warning), #FF6B35); color: white;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h3>Parent</h3>
                        <span>Child monitoring</span>
                    </div>
                </div>
                <div class="role-card-body">
                    <ul class="role-features">
                        <li><i class="fas fa-check"></i> Real-time attendance alerts</li>
                        <li><i class="fas fa-check"></i> View children's grades</li>
                        <li><i class="fas fa-check"></i> Pay fees online</li>
                        <li><i class="fas fa-check"></i> Message teachers directly</li>
                        <li><i class="fas fa-check"></i> Book parent-teacher meetings</li>
                    </ul>
                </div>
                <div class="role-card-footer">
                    <a href="parent/dashboard.php" class="btn btn-outline" style="width: 100%; justify-content: center;">
                        <i class="fas fa-arrow-right"></i> View Parent Dashboard
                    </a>
                </div>
            </div>

            <!-- Librarian -->
            <div class="role-card">
                <div class="role-card-header">
                    <div class="role-icon" style="background: linear-gradient(135deg, #6C5CE7, #A29BFE); color: white;">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <h3>Librarian</h3>
                        <span>Library management</span>
                    </div>
                </div>
                <div class="role-card-body">
                    <ul class="role-features">
                        <li><i class="fas fa-check"></i> Book catalog management</li>
                        <li><i class="fas fa-check"></i> Issue & return tracking</li>
                        <li><i class="fas fa-check"></i> Overdue fine management</li>
                        <li><i class="fas fa-check"></i> Digital resource library</li>
                        <li><i class="fas fa-check"></i> Inventory reports</li>
                    </ul>
                </div>
                <div class="role-card-footer">
                    <a href="librarian/dashboard.php" class="btn btn-outline" style="width: 100%; justify-content: center;">
                        <i class="fas fa-arrow-right"></i> View Library Portal
                    </a>
                </div>
            </div>

            <!-- Accountant -->
            <div class="role-card">
                <div class="role-card-header">
                    <div class="role-icon" style="background: linear-gradient(135deg, #00B894, #55EFC4); color: white;">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div>
                        <h3>Accountant</h3>
                        <span>Financial management</span>
                    </div>
                </div>
                <div class="role-card-body">
                    <ul class="role-features">
                        <li><i class="fas fa-check"></i> Fee collection & receipts</li>
                        <li><i class="fas fa-check"></i> Expense tracking</li>
                        <li><i class="fas fa-check"></i> Payroll processing</li>
                        <li><i class="fas fa-check"></i> Financial reports</li>
                        <li><i class="fas fa-check"></i> Budget management</li>
                    </ul>
                </div>
                <div class="role-card-footer">
                    <a href="accountant/dashboard/index.php" class="btn btn-outline" style="width: 100%; justify-content: center;">
                        <i class="fas fa-arrow-right"></i> View Finance Portal
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- All Modules Grid -->
    <section id="modules">
        <div class="section-header">
            <span class="section-badge"><i class="fas fa-th"></i> 42 Integrated Modules</span>
            <h2>Complete School Management Suite</h2>
            <p>Every tool you need in one unified platform</p>
        </div>

        <div class="modules-grid">
            <a href="admin/students.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(0, 191, 255, 0.2); color: var(--primary);"><i class="fas fa-user-graduate"></i></div>
                <h4>Student Management</h4>
                <p>Admissions, profiles, documents & IDs</p>
            </a>

            <a href="admin/attendance.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(0, 255, 127, 0.2); color: var(--accent);"><i class="fas fa-fingerprint"></i></div>
                <h4>Attendance System</h4>
                <p>Biometric, QR code & manual tracking</p>
            </a>

            <a href="admin/classes.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(138, 43, 226, 0.2); color: var(--secondary);"><i class="fas fa-chalkboard"></i></div>
                <h4>Class Management</h4>
                <p>Sections, subjects & enrollments</p>
            </a>

            <a href="admin/fee-management.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(255, 215, 0, 0.2); color: var(--warning);"><i class="fas fa-dollar-sign"></i></div>
                <h4>Fee Management</h4>
                <p>Invoicing, payments & reports</p>
            </a>

            <a href="admin/analytics.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(255, 71, 87, 0.2); color: var(--danger);"><i class="fas fa-brain"></i></div>
                <h4>AI Analytics</h4>
                <p>Predictive insights & reports</p>
            </a>

            <a href="admin/communication.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(0, 191, 255, 0.2); color: var(--primary);"><i class="fas fa-comments"></i></div>
                <h4>Communication</h4>
                <p>Messaging, SMS & notifications</p>
            </a>

            <a href="librarian/dashboard.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(108, 92, 231, 0.2); color: #6C5CE7;"><i class="fas fa-book"></i></div>
                <h4>Library System</h4>
                <p>Catalog, circulation & e-books</p>
            </a>

            <a href="transport/dashboard.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(253, 121, 168, 0.2); color: #FD79A8;"><i class="fas fa-bus"></i></div>
                <h4>Transport</h4>
                <p>Routes, tracking & management</p>
            </a>

            <a href="hostel/dashboard.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(116, 185, 255, 0.2); color: #74B9FF;"><i class="fas fa-bed"></i></div>
                <h4>Hostel Management</h4>
                <p>Rooms, allocation & mess</p>
            </a>

            <a href="admin/timetable.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(0, 184, 148, 0.2); color: #00B894;"><i class="fas fa-calendar-alt"></i></div>
                <h4>Timetable</h4>
                <p>Auto-generation & scheduling</p>
            </a>

            <a href="admin/reports.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(9, 132, 227, 0.2); color: #0984E3;"><i class="fas fa-file-alt"></i></div>
                <h4>Reports & Analytics</h4>
                <p>Comprehensive data exports</p>
            </a>

            <a href="admin/events.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(255, 159, 67, 0.2); color: #FF9F43;"><i class="fas fa-calendar-check"></i></div>
                <h4>Events & Calendar</h4>
                <p>School events & activities</p>
            </a>

            <a href="nurse/dashboard.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(234, 84, 85, 0.2); color: #EA5455;"><i class="fas fa-heartbeat"></i></div>
                <h4>Health Center</h4>
                <p>Medical records & tracking</p>
            </a>

            <a href="counselor/dashboard.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(40, 167, 69, 0.2); color: #28A745;"><i class="fas fa-hand-holding-heart"></i></div>
                <h4>Counseling</h4>
                <p>Student support & guidance</p>
            </a>

            <a href="canteen/dashboard.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(255, 193, 7, 0.2); color: #FFC107;"><i class="fas fa-utensils"></i></div>
                <h4>Canteen</h4>
                <p>Menu, orders & inventory</p>
            </a>

            <a href="admin/security-logs.php" class="module-card">
                <div class="module-card-icon" style="background: rgba(220, 53, 69, 0.2); color: #DC3545;"><i class="fas fa-shield-alt"></i></div>
                <h4>Security & Audit</h4>
                <p>Access logs & compliance</p>
            </a>
        </div>
    </section>

    <!-- CTA Section -->
    <section>
        <div class="cta-section">
            <h2>Ready to Transform Your School?</h2>
            <p>Join 500+ educational institutions already using Verdant SMS to streamline operations and enhance learning outcomes.</p>
            <div class="cta-buttons">
                <a href="register.php" class="btn btn-primary"><i class="fas fa-rocket"></i> Start Free Trial</a>
                <a href="login.php" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Login to Dashboard</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-brand">
                <a href="index.php" class="logo">
                    <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
                    <span class="logo-text">Verdant SMS</span>
                </a>
                <p>Complete 42-module school management platform with AI analytics, biometric attendance, and LMS integration. Built for the future of education.</p>
            </div>

            <div class="footer-column">
                <h4>Platform</h4>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#modules">All Modules</a></li>
                    <li><a href="#dashboards">Dashboards</a></li>
                    <li><a href="docs/">Documentation</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Portals</h4>
                <ul>
                    <li><a href="admin/dashboard.php">Admin Portal</a></li>
                    <li><a href="teacher/dashboard.php">Teacher Portal</a></li>
                    <li><a href="student/dashboard.php">Student Portal</a></li>
                    <li><a href="parent/dashboard.php">Parent Portal</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Support</h4>
                <ul>
                    <li><a href="docs/SETUP_GUIDE.md">Setup Guide</a></li>
                    <li><a href="docs/">Help Center</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="forum/">Community</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Legal</h4>
                <ul>
                    <li><a href="SECURITY.md">Security</a></li>
                    <li><a href="LICENSE">License</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 Verdant SMS. All rights reserved. Version 3.0.0</p>
            <div style="display: flex; gap: 1rem;">
                <a href="#" style="color: var(--primary);"><i class="fab fa-github"></i></a>
                <a href="#" style="color: var(--primary);"><i class="fab fa-twitter"></i></a>
                <a href="#" style="color: var(--primary);"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
    </footer>

    <script>
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
        const observerOptions = {
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card, .role-card, .module-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });
    </script>

    <?php include 'includes/theme-selector.php'; ?>
</body>

</html>