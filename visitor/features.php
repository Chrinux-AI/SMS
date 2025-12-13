<?php

/**
 * Features - Visitor Page
 * Public page showing system features (NO admin-specific details)
 */
$page_title = 'Features - Verdant SMS';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore the 42 modules of Verdant SMS - Complete school management for Nigerian schools.">
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
            max-width: 1400px;
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
            max-width: 700px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: rgba(20, 20, 30, 0.8);
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
            box-shadow: 0 0 30px rgba(0, 191, 255, 0.3);
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
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            padding: 0.5rem 0;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .feature-list li i {
            color: var(--accent);
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <?php include '../includes/visitor-nav.php'; ?>

    <main>
        <div class="page-header">
            <h1>42 Powerful Modules</h1>
            <p>Everything your school needs in one comprehensive platform. From admission to graduation, we've got you covered.</p>
        </div>

        <div class="features-grid">
            <!-- Admissions -->
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(0, 191, 255, 0.2); color: var(--primary);">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3>Admissions & Enrollment</h3>
                <p>Streamlined online admission process from application to enrollment.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Online entrance exams</li>
                    <li><i class="fas fa-check"></i> Document upload & verification</li>
                    <li><i class="fas fa-check"></i> Automated student ID generation</li>
                    <li><i class="fas fa-check"></i> Bulk registration via Google Forms</li>
                </ul>
            </div>

            <!-- Attendance -->
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(255, 215, 0, 0.2); color: #FFD700;">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h3>Attendance Management</h3>
                <p>Track student presence with multiple methods.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Biometric fingerprint/Face ID</li>
                    <li><i class="fas fa-check"></i> QR code check-in (PWA)</li>
                    <li><i class="fas fa-check"></i> Automatic parent SMS alerts</li>
                    <li><i class="fas fa-check"></i> Detailed attendance reports</li>
                </ul>
            </div>

            <!-- Grades -->
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(138, 43, 226, 0.2); color: var(--secondary);">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3>Grades & Report Cards</h3>
                <p>Nigerian grading system with comprehensive reports.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> A1-F9 grading scale</li>
                    <li><i class="fas fa-check"></i> CA + Exam score management</li>
                    <li><i class="fas fa-check"></i> Position rankings</li>
                    <li><i class="fas fa-check"></i> PDF report cards</li>
                </ul>
            </div>

            <!-- Fees -->
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(0, 255, 127, 0.2); color: var(--accent);">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h3>Fee Management</h3>
                <p>Complete financial management with online payments.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Paystack & Flutterwave integration</li>
                    <li><i class="fas fa-check"></i> Installment payment plans</li>
                    <li><i class="fas fa-check"></i> Automated receipts</li>
                    <li><i class="fas fa-check"></i> Outstanding fee tracking</li>
                </ul>
            </div>

            <!-- Library -->
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(255, 71, 87, 0.2); color: #FF4757;">
                    <i class="fas fa-book"></i>
                </div>
                <h3>Library Management</h3>
                <p>Digital and physical resource management.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Book cataloging with ISBN</li>
                    <li><i class="fas fa-check"></i> Issue/return tracking</li>
                    <li><i class="fas fa-check"></i> E-books & past questions</li>
                    <li><i class="fas fa-check"></i> Overdue fine calculation</li>
                </ul>
            </div>

            <!-- Communication -->
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(0, 191, 255, 0.2); color: var(--primary);">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>Communication Hub</h3>
                <p>Keep everyone informed and connected.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Internal messaging system</li>
                    <li><i class="fas fa-check"></i> SMS/Email notifications</li>
                    <li><i class="fas fa-check"></i> Announcements broadcast</li>
                    <li><i class="fas fa-check"></i> Parent-teacher chat</li>
                </ul>
            </div>

            <!-- Transport -->
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(138, 43, 226, 0.2); color: var(--secondary);">
                    <i class="fas fa-bus"></i>
                </div>
                <h3>Transport Management</h3>
                <p>Manage school transportation efficiently.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Bus route management</li>
                    <li><i class="fas fa-check"></i> Student-bus assignment</li>
                    <li><i class="fas fa-check"></i> Driver/conductor profiles</li>
                    <li><i class="fas fa-check"></i> Transport fee billing</li>
                </ul>
            </div>

            <!-- Hostel -->
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(255, 215, 0, 0.2); color: #FFD700;">
                    <i class="fas fa-bed"></i>
                </div>
                <h3>Hostel Management</h3>
                <p>Complete boarding facility management.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Room/bed allocation</li>
                    <li><i class="fas fa-check"></i> Hostel fee management</li>
                    <li><i class="fas fa-check"></i> Nightly attendance</li>
                    <li><i class="fas fa-check"></i> Visitor log system</li>
                </ul>
            </div>

            <!-- Timetable -->
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(0, 255, 127, 0.2); color: var(--accent);">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Timetable Management</h3>
                <p>Efficient class scheduling and planning.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> Auto timetable generator</li>
                    <li><i class="fas fa-check"></i> Room conflict detection</li>
                    <li><i class="fas fa-check"></i> Teacher workload balance</li>
                    <li><i class="fas fa-check"></i> Substitution management</li>
                </ul>
            </div>
        </div>
    </main>

    <?php include '../includes/theme-selector.php'; ?>
</body>

</html>