<?php
/**
 * Verdant SMS - Visitor Features Page
 */
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = "Features - Verdant SMS";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #00D4FF; --success: #00FF87; --purple: #A855F7; --bg-dark: #0A0E17; --bg-card: #111827; --border: rgba(255,255,255,0.08); --text: #F3F4F6; --text-muted: #9CA3AF; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-dark); color: var(--text); }
        .navbar { padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
        .navbar-brand { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
        .navbar-logo { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, var(--success), var(--primary)); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #000; }
        .navbar-title { font-size: 1.1rem; font-weight: 700; background: linear-gradient(90deg, var(--success), var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn { padding: 0.75rem 1.25rem; border-radius: 8px; font-size: 0.9rem; font-weight: 600; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, var(--success), var(--primary)); color: #000; }
        .hero { padding: 6rem 2rem 4rem; text-align: center; }
        .hero h1 { font-size: 2.5rem; margin-bottom: 1rem; }
        .hero p { color: var(--text-muted); max-width: 600px; margin: 0 auto; }
        .features { padding: 4rem 2rem; max-width: 1000px; margin: 0 auto; }
        .feature-section { margin-bottom: 4rem; }
        .feature-section h2 { font-size: 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
        .feature-section h2 i { color: var(--primary); font-size: 1.25rem; }
        .feature-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; }
        .feature-item { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; }
        .feature-item h4 { font-size: 1rem; margin-bottom: 0.5rem; }
        .feature-item p { font-size: 0.85rem; color: var(--text-muted); }
        .cta { text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, rgba(0,255,135,0.1), rgba(0,212,255,0.1)); }
        .cta h2 { margin-bottom: 1rem; }
        .cta p { color: var(--text-muted); margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="navbar-brand">
            <div class="navbar-logo">V</div>
            <span class="navbar-title">Verdant SMS</span>
        </a>
        <a href="register-school.php" class="btn btn-primary">Start Free</a>
    </nav>

    <section class="hero">
        <h1>Powerful Features for Nigerian Schools</h1>
        <p>Everything you need to manage your school efficiently, from attendance to AI-powered lesson planning.</p>
    </section>

    <section class="features">
        <div class="feature-section">
            <h2><i class="fas fa-brain"></i> AI-Powered Tools</h2>
            <div class="feature-list">
                <div class="feature-item">
                    <h4>AI Lesson Planner</h4>
                    <p>Generate NERDC-compliant lesson plans with objectives, activities, and assessments in seconds.</p>
                </div>
                <div class="feature-item">
                    <h4>AI Chatbot Assistant</h4>
                    <p>24/7 support for staff, students, and parents. Answers questions instantly.</p>
                </div>
                <div class="feature-item">
                    <h4>AI Bulk Registration</h4>
                    <p>Import students from Google Forms. AI extracts and validates data automatically.</p>
                </div>
            </div>
        </div>

        <div class="feature-section">
            <h2><i class="fas fa-users"></i> User Management</h2>
            <div class="feature-list">
                <div class="feature-item">
                    <h4>25+ User Roles</h4>
                    <p>Admin, Teachers, Students, Parents, Accountant, Librarian, Transport, Hostel, and more.</p>
                </div>
                <div class="feature-item">
                    <h4>Multi-Tenant Architecture</h4>
                    <p>Each school is completely isolated. Your data stays secure, always.</p>
                </div>
                <div class="feature-item">
                    <h4>Biometric Attendance</h4>
                    <p>Fingerprint and face recognition for secure check-ins (coming soon).</p>
                </div>
            </div>
        </div>

        <div class="feature-section">
            <h2><i class="fas fa-naira-sign"></i> Finance & Fees</h2>
            <div class="feature-list">
                <div class="feature-item">
                    <h4>Fee Management in ₦</h4>
                    <p>Track fees, generate invoices, send reminders. All in Nigerian Naira.</p>
                </div>
                <div class="feature-item">
                    <h4>Flutterwave Payments</h4>
                    <p>Accept payments online via Flutterwave. Card, bank transfer, USSD.</p>
                </div>
                <div class="feature-item">
                    <h4>Financial Reports</h4>
                    <p>Detailed revenue, expense, and outstanding fee reports.</p>
                </div>
            </div>
        </div>

        <div class="feature-section">
            <h2><i class="fas fa-graduation-cap"></i> Academics</h2>
            <div class="feature-list">
                <div class="feature-item">
                    <h4>Exam Management</h4>
                    <p>Create exams, auto-grade, generate report cards for students.</p>
                </div>
                <div class="feature-item">
                    <h4>NERDC Compliant</h4>
                    <p>Aligned with Nigerian Education Research and Development Council standards.</p>
                </div>
                <div class="feature-item">
                    <h4>Result Analysis</h4>
                    <p>Class averages, subject performance, student rankings.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <h2>Ready to Get Started?</h2>
        <p>Join schools across Nigeria using Verdant SMS.</p>
        <a href="register-school.php" class="btn btn-primary">Start Your School Free</a>
    </section>
</body>
</html>
