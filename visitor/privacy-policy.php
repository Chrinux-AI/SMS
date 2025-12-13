<?php

/**
 * Privacy Policy - Visitor Page
 * NDPR (Nigeria Data Protection Regulation) compliant
 */
$page_title = 'Privacy Policy - Verdant SMS';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Verdant SMS Privacy Policy - NDPR compliant data protection.">
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
            line-height: 1.8;
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
            max-width: 900px;
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
            font-size: 1rem;
        }

        .policy-section {
            background: rgba(20, 20, 30, 0.9);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .policy-section h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.4rem;
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .policy-section p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 1rem;
        }

        .policy-section ul {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .policy-section li {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.5rem;
        }

        .highlight-box {
            background: rgba(0, 191, 255, 0.1);
            border-left: 4px solid var(--primary);
            padding: 1rem 1.5rem;
            border-radius: 0 8px 8px 0;
            margin: 1.5rem 0;
        }

        .highlight-box p {
            margin: 0;
        }

        .contact-box {
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.1), rgba(138, 43, 226, 0.1));
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            margin-top: 3rem;
        }

        .contact-box h3 {
            font-family: 'Orbitron', sans-serif;
            margin-bottom: 1rem;
        }

        .contact-box a {
            color: var(--primary);
            text-decoration: none;
        }

        .contact-box a:hover {
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .policy-section {
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
            <h1><i class="fas fa-shield-alt"></i> Privacy Policy</h1>
            <p>Last updated: December 13, 2025</p>
        </div>

        <div class="policy-section">
            <h2><i class="fas fa-flag"></i> NDPR Compliance</h2>
            <p>This Privacy Policy complies with the Nigeria Data Protection Regulation (NDPR) 2019. We are committed to protecting your personal data and respecting your privacy rights.</p>
            <div class="highlight-box">
                <p><strong>Data Controller:</strong> Verdant School Management System<br>
                    <strong>Contact:</strong> christolabiyi35@gmail.com
                </p>
            </div>
        </div>

        <div class="policy-section">
            <h2><i class="fas fa-database"></i> Data We Collect</h2>
            <p>We collect the following categories of personal data:</p>
            <ul>
                <li><strong>Identity Data:</strong> Names, date of birth, gender, passport photographs</li>
                <li><strong>Contact Data:</strong> Email addresses, phone numbers, home addresses</li>
                <li><strong>Educational Data:</strong> Enrollment records, grades, attendance, exam results</li>
                <li><strong>Financial Data:</strong> Fee payment records, transaction history</li>
                <li><strong>Technical Data:</strong> IP addresses, browser type, device information</li>
                <li><strong>Biometric Data:</strong> Fingerprint/Face ID data (encrypted, for authentication only)</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2><i class="fas fa-cogs"></i> How We Use Your Data</h2>
            <p>We process your data for the following purposes:</p>
            <ul>
                <li>Student enrollment and academic record management</li>
                <li>Attendance tracking and reporting to parents/guardians</li>
                <li>Fee collection and financial management</li>
                <li>Communication between school, parents, and students</li>
                <li>Generating report cards and academic transcripts</li>
                <li>Security and access control (biometric authentication)</li>
                <li>Compliance with Nigerian educational regulations</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2><i class="fas fa-lock"></i> Data Security</h2>
            <p>We implement robust security measures to protect your data:</p>
            <ul>
                <li>256-bit SSL/TLS encryption for all data transmission</li>
                <li>Encrypted database storage (AES-256)</li>
                <li>Secure biometric data storage (never shared with third parties)</li>
                <li>Regular security audits and vulnerability assessments</li>
                <li>Role-based access control limiting data access</li>
                <li>Multi-factor authentication (password + OTP/biometric)</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2><i class="fas fa-clock"></i> Data Retention</h2>
            <p>We retain personal data for the following periods:</p>
            <ul>
                <li><strong>Active student records:</strong> Duration of enrollment + 7 years</li>
                <li><strong>Alumni records:</strong> Permanent (for transcript requests)</li>
                <li><strong>Financial records:</strong> 7 years (as required by Nigerian law)</li>
                <li><strong>System logs:</strong> 90 days</li>
                <li><strong>Biometric data:</strong> Deleted upon request or 1 year after leaving school</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2><i class="fas fa-user-shield"></i> Your Rights</h2>
            <p>Under NDPR, you have the following rights:</p>
            <ul>
                <li><strong>Right to Access:</strong> Request a copy of your personal data</li>
                <li><strong>Right to Rectification:</strong> Request correction of inaccurate data</li>
                <li><strong>Right to Erasure:</strong> Request deletion of your data (with limitations)</li>
                <li><strong>Right to Restrict Processing:</strong> Limit how we use your data</li>
                <li><strong>Right to Data Portability:</strong> Receive your data in a portable format</li>
                <li><strong>Right to Object:</strong> Object to certain types of processing</li>
                <li><strong>Right to Withdraw Consent:</strong> Withdraw consent at any time</li>
            </ul>
            <div class="highlight-box">
                <p>To exercise any of these rights, contact us at <a href="mailto:christolabiyi35@gmail.com">christolabiyi35@gmail.com</a>. We will respond within 30 days.</p>
            </div>
        </div>

        <div class="policy-section">
            <h2><i class="fas fa-share-alt"></i> Data Sharing</h2>
            <p>We may share your data with:</p>
            <ul>
                <li><strong>Examination Bodies:</strong> WAEC, NECO, JAMB (for exam registration)</li>
                <li><strong>Payment Processors:</strong> Paystack, Flutterwave (for fee payments)</li>
                <li><strong>SMS Providers:</strong> For notifications (phone numbers only)</li>
                <li><strong>Government Agencies:</strong> When required by law</li>
            </ul>
            <p>We never sell your personal data to third parties for marketing purposes.</p>
        </div>

        <div class="policy-section">
            <h2><i class="fas fa-child"></i> Children's Privacy</h2>
            <p>As an educational platform, we process data of minors (students). We:</p>
            <ul>
                <li>Require parental/guardian consent for student registration</li>
                <li>Limit data collection to what is educationally necessary</li>
                <li>Never display students' personal information publicly</li>
                <li>Provide parents full access to their children's data</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2><i class="fas fa-cookie"></i> Cookies & Tracking</h2>
            <p>We use cookies for:</p>
            <ul>
                <li><strong>Essential cookies:</strong> Session management, authentication</li>
                <li><strong>Preference cookies:</strong> Theme selection, language settings</li>
                <li><strong>Analytics cookies:</strong> Understanding system usage (anonymized)</li>
            </ul>
            <p>You can manage cookie preferences in your browser settings.</p>
        </div>

        <div class="policy-section">
            <h2><i class="fas fa-edit"></i> Policy Updates</h2>
            <p>We may update this Privacy Policy from time to time. Significant changes will be communicated via email or system notification. Continued use of the system after changes constitutes acceptance of the updated policy.</p>
        </div>

        <div class="contact-box">
            <h3><i class="fas fa-envelope"></i> Data Protection Inquiries</h3>
            <p>For any questions about this Privacy Policy or to exercise your data rights:</p>
            <p><strong>Email:</strong> <a href="mailto:christolabiyi35@gmail.com">christolabiyi35@gmail.com</a></p>
            <p><strong>Phone:</strong> <a href="tel:+2348167714860">+234 816 771 4860</a></p>
        </div>
    </main>

    <?php include '../includes/theme-selector.php'; ?>
</body>

</html>