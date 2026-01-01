<?php
/**
 * Verdant SMS v3.0 — National Homepage
 * Clean, secure visitor gateway with no role exposure
 */

require_once __DIR__ . '/includes/config.php';

$pageTitle = "Verdant SMS — Nigeria's #1 Free School Management System";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Verdant SMS - Free, AI-powered school management system built for Nigerian schools. Multi-tenant, Naira pricing, NERDC compliant.">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00D4FF;
            --success: #00FF87;
            --warning: #FFB800;
            --danger: #FF4757;
            --purple: #A855F7;
            --pink: #EC4899;
            --bg-dark: #0A0E17;
            --bg-card: #111827;
            --bg-surface: #1A1F2E;
            --border: rgba(255,255,255,0.08);
            --text: #F3F4F6;
            --text-muted: #9CA3AF;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(10, 14, 23, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .navbar-logo {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--success), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #000;
            font-weight: 800;
        }

        .navbar-title {
            font-size: 1.25rem;
            font-weight: 700;
            background: linear-gradient(90deg, var(--success), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .navbar-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .navbar-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .navbar-links a:hover {
            color: var(--primary);
        }

        .navbar-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--success), var(--primary));
            color: #000;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--text);
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* ===== HERO ===== */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8rem 2rem 4rem;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(0, 255, 135, 0.15) 0%, transparent 60%);
            pointer-events: none;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0, 212, 255, 0.1) 0%, transparent 60%);
            pointer-events: none;
        }

        .hero-content {
            max-width: 900px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(0, 255, 135, 0.1);
            border: 1px solid rgba(0, 255, 135, 0.3);
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-size: 0.85rem;
            color: var(--success);
            margin-bottom: 2rem;
        }

        .hero-badge i {
            font-size: 0.75rem;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }

        .hero-title .highlight {
            background: linear-gradient(90deg, var(--success), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-muted);
            max-width: 650px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 3rem;
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1rem;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 4rem;
            flex-wrap: wrap;
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
        }

        .hero-stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        /* ===== FEATURES SECTION ===== */
        .section {
            padding: 6rem 2rem;
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 4rem;
        }

        .section-title {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--text-muted);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.3s;
        }

        .feature-card:hover {
            border-color: rgba(0, 212, 255, 0.3);
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.15), rgba(168, 85, 247, 0.15));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1.25rem;
        }

        .feature-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* ===== PRICING SECTION ===== */
        .pricing-section {
            background: var(--bg-surface);
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .pricing-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .pricing-card.popular {
            border-color: var(--success);
            box-shadow: 0 0 40px rgba(0, 255, 135, 0.1);
        }

        .pricing-card.popular::before {
            content: 'Most Popular';
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--success);
            color: #000;
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .pricing-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .pricing-card .price {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
            margin: 1rem 0;
        }

        .pricing-card .price span {
            font-size: 1rem;
            font-weight: 400;
            color: var(--text-muted);
        }

        .pricing-card .features {
            list-style: none;
            margin: 1.5rem 0;
            text-align: left;
        }

        .pricing-card .features li {
            padding: 0.5rem 0;
            font-size: 0.9rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pricing-card .features li i {
            color: var(--success);
        }

        /* ===== CTA SECTION ===== */
        .cta-section {
            background: linear-gradient(135deg, rgba(0, 255, 135, 0.1), rgba(0, 212, 255, 0.1));
            border-top: 1px solid rgba(0, 255, 135, 0.2);
            border-bottom: 1px solid rgba(0, 212, 255, 0.2);
        }

        .cta-content {
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }

        .cta-content h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .cta-content p {
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        /* ===== FOOTER ===== */
        .footer {
            padding: 4rem 2rem 2rem;
            border-top: 1px solid var(--border);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            max-width: 1200px;
            margin: 0 auto 3rem;
        }

        .footer-brand p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 1rem;
            line-height: 1.6;
        }

        .footer-links h4 {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text);
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .footer-bottom .flag {
            color: var(--success);
        }

        /* ===== AI CHATBOT ===== */
        .chatbot-trigger {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--success), var(--primary));
            border: none;
            color: #000;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 10px 40px rgba(0, 212, 255, 0.3);
            transition: all 0.3s;
            z-index: 1000;
        }

        .chatbot-trigger:hover {
            transform: scale(1.1);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .navbar-links {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }

            .hero-title {
                font-size: 2.25rem;
            }

            .hero-stats {
                gap: 2rem;
            }

            .hero-stat-value {
                font-size: 2rem;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">
            <div class="navbar-logo">V</div>
            <span class="navbar-title">Verdant SMS</span>
        </a>

        <ul class="navbar-links">
            <li><a href="visitor/features.php">Features</a></li>
            <li><a href="visitor/pricing.php">Pricing</a></li>
            <li><a href="visitor/demo.php">Demo</a></li>
            <li><a href="visitor/contact.php">Contact</a></li>
        </ul>

        <div class="navbar-actions">
            <a href="login.php" class="btn btn-outline">Login</a>
            <a href="visitor/register-school.php" class="btn btn-primary">Start Free</a>
        </div>

        <button class="mobile-menu-btn">
            <i class="fas fa-bars"></i>
        </button>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-sparkles"></i>
                Nigeria's #1 Free School System
            </div>

            <h1 class="hero-title">
                The Future of <span class="highlight">School Management</span> is Here
            </h1>

            <p class="hero-subtitle">
                Free, AI-powered school management built for Nigerian schools.
                Multi-tenant architecture, Naira pricing, NERDC compliant,
                and beautiful from day one.
            </p>

            <div class="hero-actions">
                <a href="visitor/register-school.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-rocket"></i> Start Your School Free
                </a>
                <a href="visitor/demo.php" class="btn btn-outline btn-lg">
                    <i class="fas fa-play"></i> Watch Demo
                </a>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-value">100%</div>
                    <div class="hero-stat-label">Free Forever</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">25+</div>
                    <div class="hero-stat-label">User Roles</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">AI</div>
                    <div class="hero-stat-label">Powered</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">₦</div>
                    <div class="hero-stat-label">Naira Pricing</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Everything Your School Needs</h2>
            <p class="section-subtitle">
                From attendance to AI-powered lesson planning, Verdant has it all.
            </p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-brain"></i></div>
                <h3>AI Lesson Planner</h3>
                <p>Generate NERDC-compliant lesson plans in seconds. Input topic, get objectives, activities, and assessments.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-users"></i></div>
                <h3>Multi-Tenant Architecture</h3>
                <p>Each school is completely isolated. Your data stays secure, always.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-clipboard-check"></i></div>
                <h3>Smart Attendance</h3>
                <p>Mark attendance with biometric support. Real-time tracking for students and staff.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-naira-sign"></i></div>
                <h3>Fee Management in ₦</h3>
                <p>Track fees, send reminders, accept payments via Flutterwave. All in Naira.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-graduation-cap"></i></div>
                <h3>Exam & Results</h3>
                <p>Create exams, auto-grade, generate report cards. Parents get instant access.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-comments"></i></div>
                <h3>AI Chatbot</h3>
                <p>24/7 assistant for staff, students, and parents. Answers questions instantly.</p>
            </div>
        </div>
    </section>

    <!-- PRICING -->
    <section class="section pricing-section" id="pricing">
        <div class="section-header">
            <h2 class="section-title">Simple, Transparent Pricing</h2>
            <p class="section-subtitle">
                Start free. Upgrade when you need more.
            </p>
        </div>

        <div class="pricing-grid">
            <div class="pricing-card">
                <h3>Free</h3>
                <div class="price">₦0 <span>forever</span></div>
                <ul class="features">
                    <li><i class="fas fa-check"></i> Unlimited users</li>
                    <li><i class="fas fa-check"></i> All core features</li>
                    <li><i class="fas fa-check"></i> Self-hosted</li>
                    <li><i class="fas fa-check"></i> Community support</li>
                </ul>
                <a href="visitor/register-school.php" class="btn btn-outline" style="width: 100%;">Get Started</a>
            </div>

            <div class="pricing-card popular">
                <h3>Basic Cloud</h3>
                <div class="price">₦50,000 <span>/year</span></div>
                <ul class="features">
                    <li><i class="fas fa-check"></i> Everything in Free</li>
                    <li><i class="fas fa-check"></i> Cloud hosting</li>
                    <li><i class="fas fa-check"></i> Daily backups</li>
                    <li><i class="fas fa-check"></i> Email support</li>
                </ul>
                <a href="visitor/pricing.php" class="btn btn-primary" style="width: 100%;">Choose Plan</a>
            </div>

            <div class="pricing-card">
                <h3>Pro Cloud</h3>
                <div class="price">₦150,000 <span>/year</span></div>
                <ul class="features">
                    <li><i class="fas fa-check"></i> Everything in Basic</li>
                    <li><i class="fas fa-check"></i> AI Lesson Planner</li>
                    <li><i class="fas fa-check"></i> Custom subdomain</li>
                    <li><i class="fas fa-check"></i> Priority support</li>
                </ul>
                <a href="visitor/pricing.php" class="btn btn-outline" style="width: 100%;">Choose Plan</a>
            </div>

            <div class="pricing-card">
                <h3>Enterprise</h3>
                <div class="price">Custom</div>
                <ul class="features">
                    <li><i class="fas fa-check"></i> Everything in Pro</li>
                    <li><i class="fas fa-check"></i> Dedicated server</li>
                    <li><i class="fas fa-check"></i> Custom features</li>
                    <li><i class="fas fa-check"></i> On-site training</li>
                </ul>
                <a href="visitor/contact.php" class="btn btn-outline" style="width: 100%;">Contact Sales</a>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="section cta-section">
        <div class="cta-content">
            <h2>Ready to Transform Your School?</h2>
            <p>Join schools across Nigeria using Verdant to manage attendance, fees, exams, and more.</p>
            <a href="visitor/register-school.php" class="btn btn-primary btn-lg">
                <i class="fas fa-rocket"></i> Start Free Today
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="index.php" class="navbar-brand">
                    <div class="navbar-logo">V</div>
                    <span class="navbar-title">Verdant SMS</span>
                </a>
                <p>Free, AI-powered school management system built for Nigerian schools. Open source, secure, and beautiful.</p>
            </div>

            <div class="footer-links">
                <h4>Product</h4>
                <ul>
                    <li><a href="visitor/features.php">Features</a></li>
                    <li><a href="visitor/pricing.php">Pricing</a></li>
                    <li><a href="visitor/demo.php">Demo</a></li>
                    <li><a href="visitor/faq.php">FAQ</a></li>
                </ul>
            </div>

            <div class="footer-links">
                <h4>Company</h4>
                <ul>
                    <li><a href="visitor/about.php">About</a></li>
                    <li><a href="visitor/contact.php">Contact</a></li>
                    <li><a href="visitor/blog.php">Blog</a></li>
                    <li><a href="https://github.com/Chrinux-AI/SMS" target="_blank">GitHub</a></li>
                </ul>
            </div>

            <div class="footer-links">
                <h4>Legal</h4>
                <ul>
                    <li><a href="visitor/privacy.php">Privacy Policy</a></li>
                    <li><a href="visitor/terms.php">Terms of Service</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Verdant SMS by Chrinux-AI. Built with <i class="fas fa-heart" style="color: var(--danger);"></i> for <span class="flag">Nigeria 🇳🇬</span></p>
        </div>
    </footer>

    <!-- AI CHATBOT TRIGGER -->
    <button class="chatbot-trigger" id="chatbotTrigger" title="Chat with AI Assistant">
        <i class="fas fa-leaf"></i>
    </button>

    <script>
        // Chatbot trigger
        document.getElementById('chatbotTrigger').addEventListener('click', function() {
            alert('AI Chatbot coming soon! For now, contact us at hello@verdantsms.com');
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>
