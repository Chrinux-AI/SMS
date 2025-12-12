<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Features - Verdant SMS | 42-Module School Management System</title>
    <meta name="description" content="Explore Verdant SMS advanced features: AI Analytics, Biometric Authentication, LMS Integration, PWA Support, and more cutting-edge educational technology.">

    <!-- Favicons -->
    <link rel="icon" href="assets/images/favicon.ico">
    <link rel="manifest" href="manifest.json">

    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/homepage.css">

    <style>
        .page-header {
            background: linear-gradient(135deg, #0A0A0A 0%, #1a1a2e 50%, #0A0A0A 100%);
            padding: 120px 2rem 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 50% 50%, rgba(0, 191, 255, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .page-header h1 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-header p {
            font-size: 1.25rem;
            color: var(--text-muted);
            max-width: 700px;
            margin: 0 auto;
        }

        .features-section {
            padding: 6rem 2rem;
            background: rgba(10, 10, 10, 0.5);
        }

        .feature-detail {
            max-width: 1200px;
            margin: 0 auto 6rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .feature-detail:nth-child(even) {
            direction: rtl;
        }

        .feature-detail:nth-child(even)>* {
            direction: ltr;
        }

        .feature-content h2 {
            font-size: 2.5rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .feature-badge {
            display: inline-block;
            background: rgba(0, 191, 255, 0.2);
            color: var(--primary-color);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .feature-content p {
            font-size: 1.1rem;
            color: var(--text-muted);
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
        }

        .feature-list li {
            padding: 1rem;
            margin-bottom: 0.75rem;
            background: rgba(30, 30, 30, 0.6);
            border-left: 3px solid var(--primary-color);
            border-radius: 8px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .feature-list li i {
            color: var(--accent-color);
            font-size: 1.2rem;
        }

        .feature-visual {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 400px;
        }

        .feature-icon-large {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            color: white;
            box-shadow: 0 0 60px rgba(0, 191, 255, 0.4);
        }

        .comparison-table {
            max-width: 1200px;
            margin: 4rem auto;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            overflow: hidden;
        }

        .comparison-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 1.5rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .comparison-table th {
            background: rgba(0, 191, 255, 0.1);
            color: var(--primary-color);
            font-weight: 700;
        }

        .comparison-table td {
            color: var(--text-secondary);
        }

        .check-icon {
            color: var(--accent-color);
            font-size: 1.2rem;
        }

        @media (max-width: 968px) {
            .feature-detail {
                grid-template-columns: 1fr;
                direction: ltr !important;
            }

            .page-header h1 {
                font-size: 2.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation (copied from index.php) -->
    <?php include 'includes/navigation.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Advanced Features</h1>
            <p>Cutting-edge technology powering the future of educational management. Discover the intelligent systems that set Verdant SMS apart.</p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">

            <!-- AI Analytics -->
            <div class="feature-detail" id="ai-analytics">
                <div class="feature-content">
                    <span class="feature-badge">Artificial Intelligence</span>
                    <h2>AI-Powered Analytics & Predictions</h2>
                    <p>Leverage machine learning algorithms to gain actionable insights into student performance, attendance patterns, and institutional trends. Our AI engine predicts at-risk students, optimizes resource allocation, and provides data-driven recommendations.</p>

                    <ul class="feature-list">
                        <li>
                            <i class="fas fa-chart-line"></i>
                            <span><strong>Predictive Analytics</strong> - Identify at-risk students before they fall behind</span>
                        </li>
                        <li>
                            <i class="fas fa-brain"></i>
                            <span><strong>Learning Pattern Recognition</strong> - Understand how students learn best</span>
                        </li>
                        <li>
                            <i class="fas fa-lightbulb"></i>
                            <span><strong>Smart Recommendations</strong> - AI-driven curriculum and intervention suggestions</span>
                        </li>
                        <li>
                            <i class="fas fa-database"></i>
                            <span><strong>Real-time Dashboards</strong> - Interactive visualizations with drill-down capabilities</span>
                        </li>
                    </ul>

                    <a href="demo-request.php?feature=ai" class="btn btn-primary">
                        <i class="fas fa-calendar-check"></i> Request AI Demo
                    </a>
                </div>

                <div class="feature-visual">
                    <div class="feature-icon-large">
                        <i class="fas fa-brain"></i>
                    </div>
                </div>
            </div>

            <!-- Biometric Authentication -->
            <div class="feature-detail" id="biometric">
                <div class="feature-content">
                    <span class="feature-badge">Security & Access Control</span>
                    <h2>Biometric Authentication System</h2>
                    <p>Next-generation security with fingerprint scanning, facial recognition, and QR code authentication. Ensure accurate attendance tracking and secure facility access with military-grade biometric technology.</p>

                    <ul class="feature-list">
                        <li>
                            <i class="fas fa-fingerprint"></i>
                            <span><strong>Fingerprint Scanner</strong> - High-precision optical sensors with 99.9% accuracy</span>
                        </li>
                        <li>
                            <i class="fas fa-qrcode"></i>
                            <span><strong>QR Code Check-in</strong> - Contactless attendance with mobile app integration</span>
                        </li>
                        <li>
                            <i class="fas fa-user-shield"></i>
                            <span><strong>Facial Recognition</strong> - AI-powered face detection for secure entry (optional)</span>
                        </li>
                        <li>
                            <i class="fas fa-bell"></i>
                            <span><strong>Instant Notifications</strong> - Real-time SMS/WhatsApp alerts to parents</span>
                        </li>
                    </ul>

                    <a href="modules.php#attendance" class="btn btn-primary">
                        <i class="fas fa-info-circle"></i> Learn More
                    </a>
                </div>

                <div class="feature-visual">
                    <div class="feature-icon-large">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                </div>
            </div>

            <!-- LMS Integration -->
            <div class="feature-detail" id="lms">
                <div class="feature-content">
                    <span class="feature-badge">Learning Management</span>
                    <h2>LMS Integration (LTI 1.3 Compatible)</h2>
                    <p>Seamlessly integrate with leading Learning Management Systems like Moodle, Canvas, Blackboard, and Google Classroom. Support LTI 1.3 standards for deep integration, single sign-on, and automatic grade passback.</p>

                    <ul class="feature-list">
                        <li>
                            <i class="fas fa-plug"></i>
                            <span><strong>LTI 1.3 Standard</strong> - Industry-standard integration protocol</span>
                        </li>
                        <li>
                            <i class="fas fa-sync-alt"></i>
                            <span><strong>Auto Grade Sync</strong> - Automatic grade passback to LMS gradebook</span>
                        </li>
                        <li>
                            <i class="fas fa-key"></i>
                            <span><strong>Single Sign-On (SSO)</strong> - One login for all platforms</span>
                        </li>
                        <li>
                            <i class="fas fa-book-reader"></i>
                            <span><strong>Course Mapping</strong> - Link SMS courses to LMS modules automatically</span>
                        </li>
                    </ul>

                    <a href="integrations.php#lms" class="btn btn-primary">
                        <i class="fas fa-plug"></i> View Integrations
                    </a>
                </div>

                <div class="feature-visual">
                    <div class="feature-icon-large">
                        <i class="fas fa-book-reader"></i>
                    </div>
                </div>
            </div>

            <!-- PWA Support -->
            <div class="feature-detail" id="pwa">
                <div class="feature-content">
                    <span class="feature-badge">Mobile Technology</span>
                    <h2>Progressive Web App (PWA) Support</h2>
                    <p>Install Verdant SMS as a native app on any device without app store downloads. Enjoy offline capabilities, push notifications, and app-like performance directly from your browser.</p>

                    <ul class="feature-list">
                        <li>
                            <i class="fas fa-mobile-alt"></i>
                            <span><strong>Install Anywhere</strong> - Works on iOS, Android, Windows, macOS, Linux</span>
                        </li>
                        <li>
                            <i class="fas fa-wifi-slash"></i>
                            <span><strong>Offline Mode</strong> - Access critical data without internet connection</span>
                        </li>
                        <li>
                            <i class="fas fa-bell"></i>
                            <span><strong>Push Notifications</strong> - Real-time alerts even when app is closed</span>
                        </li>
                        <li>
                            <i class="fas fa-bolt"></i>
                            <span><strong>Lightning Fast</strong> - Native app performance with web flexibility</span>
                        </li>
                    </ul>

                    <a href="documentation.php#pwa" class="btn btn-primary">
                        <i class="fas fa-book"></i> Read Documentation
                    </a>
                </div>

                <div class="feature-visual">
                    <div class="feature-icon-large">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                </div>
            </div>

            <!-- Real-time Communication -->
            <div class="feature-detail" id="realtime">
                <div class="feature-content">
                    <span class="feature-badge">Communication</span>
                    <h2>Real-time Messaging & Notifications</h2>
                    <p>Integrated communication hub with instant messaging, broadcast announcements, WhatsApp integration (via Twilio), and multi-channel notification delivery.</p>

                    <ul class="feature-list">
                        <li>
                            <i class="fas fa-comments"></i>
                            <span><strong>Live Chat</strong> - Real-time messaging between staff, students, and parents</span>
                        </li>
                        <li>
                            <i class="fab fa-whatsapp"></i>
                            <span><strong>WhatsApp Integration</strong> - Automated notifications via WhatsApp Business API</span>
                        </li>
                        <li>
                            <i class="fas fa-bullhorn"></i>
                            <span><strong>Broadcast System</strong> - Send announcements to entire classes or departments</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span><strong>Multi-channel Delivery</strong> - Email, SMS, WhatsApp, in-app notifications</span>
                        </li>
                    </ul>

                    <a href="modules.php#messaging" class="btn btn-primary">
                        <i class="fas fa-comments"></i> Explore Messaging
                    </a>
                </div>

                <div class="feature-visual">
                    <div class="feature-icon-large">
                        <i class="fas fa-comments"></i>
                    </div>
                </div>
            </div>

            <!-- Data Security -->
            <div class="feature-detail" id="security">
                <div class="feature-content">
                    <span class="feature-badge">Enterprise Security</span>
                    <h2>Bank-Level Data Security & Compliance</h2>
                    <p>Your data is protected with industry-leading encryption, regular security audits, GDPR compliance, and automated backup systems. We take security seriously so you can focus on education.</p>

                    <ul class="feature-list">
                        <li>
                            <i class="fas fa-lock"></i>
                            <span><strong>AES-256 Encryption</strong> - Military-grade data encryption at rest and in transit</span>
                        </li>
                        <li>
                            <i class="fas fa-shield-alt"></i>
                            <span><strong>GDPR Compliant</strong> - Full compliance with data protection regulations</span>
                        </li>
                        <li>
                            <i class="fas fa-database"></i>
                            <span><strong>Automated Backups</strong> - Daily encrypted backups with 99.9% uptime SLA</span>
                        </li>
                        <li>
                            <i class="fas fa-user-lock"></i>
                            <span><strong>Role-Based Access</strong> - Granular permissions for 18 user roles</span>
                        </li>
                    </ul>

                    <a href="security.php" class="btn btn-primary">
                        <i class="fas fa-shield-alt"></i> Security Details
                    </a>
                </div>

                <div class="feature-visual">
                    <div class="feature-icon-large">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Comparison Table -->
    <section class="features-section" style="background: rgba(10, 10, 10, 0.8);">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 class="section-title">Verdant SMS vs Traditional Systems</h2>
                <p class="section-subtitle">See why leading institutions choose our platform</p>
            </div>

            <div class="comparison-table">
                <table>
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>Verdant SMS</th>
                            <th>Traditional Systems</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>AI-Powered Analytics</strong></td>
                            <td><i class="fas fa-check-circle check-icon"></i> Advanced ML predictions</td>
                            <td>Basic reports only</td>
                        </tr>
                        <tr>
                            <td><strong>Biometric Attendance</strong></td>
                            <td><i class="fas fa-check-circle check-icon"></i> Fingerprint + QR + Facial</td>
                            <td>Manual or RFID only</td>
                        </tr>
                        <tr>
                            <td><strong>LMS Integration</strong></td>
                            <td><i class="fas fa-check-circle check-icon"></i> LTI 1.3 certified</td>
                            <td>Limited or none</td>
                        </tr>
                        <tr>
                            <td><strong>PWA Support</strong></td>
                            <td><i class="fas fa-check-circle check-icon"></i> Full offline capability</td>
                            <td>Requires app stores</td>
                        </tr>
                        <tr>
                            <td><strong>WhatsApp Integration</strong></td>
                            <td><i class="fas fa-check-circle check-icon"></i> Native Twilio integration</td>
                            <td>Not available</td>
                        </tr>
                        <tr>
                            <td><strong>Real-time Updates</strong></td>
                            <td><i class="fas fa-check-circle check-icon"></i> WebSocket-powered live data</td>
                            <td>Requires page refresh</td>
                        </tr>
                        <tr>
                            <td><strong>Multi-language Support</strong></td>
                            <td><i class="fas fa-check-circle check-icon"></i> i18n framework ready</td>
                            <td>English only</td>
                        </tr>
                        <tr>
                            <td><strong>Cloud Infrastructure</strong></td>
                            <td><i class="fas fa-check-circle check-icon"></i> Auto-scaling, 99.9% uptime</td>
                            <td>On-premise only</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container cta-content">
            <h2>Experience These Features Firsthand</h2>
            <p>Schedule a personalized demo to see how Verdant SMS can transform your institution.</p>
            <div class="cta-buttons">
                <a href="demo-request.php" class="btn btn-white btn-large">
                    <i class="fas fa-calendar-check"></i> Schedule Demo
                </a>
                <a href="contact.php" class="btn btn-outline-white btn-large">
                    <i class="fas fa-envelope"></i> Contact Sales
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/homepage.js"></script>
</body>

</html>