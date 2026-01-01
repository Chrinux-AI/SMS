<?php

/**
 * About Us - Visitor Page
 * Public page showing school system overview
 */
$page_title = 'About Us - Verdant SMS';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Learn about Verdant SMS - The complete school management system for Nigerian schools.">
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
            margin-bottom: 4rem;
        }

        .page-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .content-section {
            background: rgba(20, 20, 30, 0.8);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 3rem;
            margin-bottom: 2rem;
        }

        .content-section h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }

        .content-section p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 1rem;
            font-size: 1.05rem;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .value-card {
            background: rgba(0, 191, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
        }

        .value-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }

        .value-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .value-card h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .value-card p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.95rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }

        .stat-card {
            text-align: center;
            padding: 2rem;
            background: rgba(0, 191, 255, 0.1);
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        .stat-card .number {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            color: var(--accent);
            font-weight: 700;
        }

        .stat-card .label {
            color: rgba(255, 255, 255, 0.7);
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .content-section {
                padding: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <?php include '../includes/visitor-nav.php'; ?>

    <main>
        <div class="page-header">
            <h1>About Verdant SMS</h1>
            <p>Empowering Nigerian education with modern, comprehensive school management technology</p>
        </div>

        <section class="content-section">
            <h2><i class="fas fa-bullseye"></i> Our Mission</h2>
            <p>Verdant School Management System was created with a singular mission: to transform how Nigerian schools operate. We believe that every school, regardless of size or location, deserves access to world-class management tools.</p>
            <p>Our platform bridges the gap between traditional school administration and modern digital efficiency, helping educators focus on what matters most - nurturing the next generation of Nigerian leaders.</p>
        </section>

        <section class="content-section">
            <h2><i class="fas fa-heart"></i> Our Core Values</h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Security First</h3>
                    <p>Bank-grade encryption and biometric authentication protect your data</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-users"></i>
                    <h3>Student-Centered</h3>
                    <p>Every feature is designed to enhance student success and growth</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-bolt"></i>
                    <h3>Innovation</h3>
                    <p>Constantly evolving with AI analytics and modern technologies</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-hands-helping"></i>
                    <h3>Accessibility</h3>
                    <p>Works on any device, even offline, ensuring no one is left behind</p>
                </div>
            </div>
        </section>

        <section class="content-section">
            <h2><i class="fas fa-chart-line"></i> Impact & Reach</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number">42</div>
                    <div class="label">Integrated Modules</div>
                </div>
                <div class="stat-card">
                    <div class="number">P1-SSS3</div>
                    <div class="label">Class Coverage</div>
                </div>
                <div class="stat-card">
                    <div class="number">8</div>
                    <div class="label">Beautiful Themes</div>
                </div>
                <div class="stat-card">
                    <div class="number">100%</div>
                    <div class="label">Nigerian Curriculum</div>
                </div>
            </div>
        </section>

        <section class="content-section">
            <h2><i class="fas fa-flag"></i> Built for Nigeria</h2>
            <p>Unlike generic school management systems, Verdant SMS is purpose-built for Nigerian schools:</p>
            <ul style="margin-top: 1rem; margin-left: 2rem; color: rgba(255,255,255,0.8);">
                <li>Nigerian grading system (A1-F9)</li>
                <li>3-term academic calendar structure</li>
                <li>WAEC/NECO integration ready</li>
                <li>Nigerian curriculum subjects (Primary, JSS, SSS)</li>
                <li>Naira (₦) payment integration via Paystack & Flutterwave</li>
                <li>SMS notifications via Nigerian networks</li>
            </ul>
        </section>
    </main>

    <?php include '../includes/theme-selector.php'; ?>
</body>

</html>