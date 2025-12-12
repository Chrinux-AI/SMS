<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Verdant SMS - Complete 42-Module School Management System with AI Analytics, Biometric Attendance & LMS Integration for Modern Educational Institutions.">
    <meta name="keywords" content="school management system, education software, AI analytics, biometric attendance, student portal, teacher portal, LMS integration, 42 modules">
    <meta name="theme-color" content="#00BFFF">
    <meta name="author" content="Verdant SMS">

    <!-- PWA Support -->
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="assets/images/icons/icon-192x192.png">

    <title>Verdant SMS - Transform Your Institution with Advanced School Management</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/homepage.css">
</head>

<body>
    <!-- Navigation Header -->
    <header class="main-header" id="mainHeader">
        <div class="container">
            <nav class="main-nav">
                <a href="index.php" class="brand">
                    <div class="brand-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="brand-text">
                        <h1>Verdant SMS</h1>
                        <p>42-Module Platform</p>
                    </div>
                </a>

                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle Menu">
                    <i class="fas fa-bars"></i>
                </button>

                <ul class="nav-menu" id="navMenu">
                    <li class="nav-item has-dropdown">
                        <a href="#" class="nav-link">
                            Platform <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-grid">
                                <div class="dropdown-column">
                                    <h4><i class="fas fa-th"></i> Core Modules</h4>
                                    <a href="modules.php#student-management"><i class="fas fa-user-graduate"></i> Student Management</a>
                                    <a href="modules.php#teacher-portal"><i class="fas fa-chalkboard-teacher"></i> Teacher Portal</a>
                                    <a href="modules.php#attendance"><i class="fas fa-fingerprint"></i> Attendance System</a>
                                    <a href="modules.php#parent-portal"><i class="fas fa-users"></i> Parent Portal</a>
                                </div>
                                <div class="dropdown-column">
                                    <h4><i class="fas fa-star"></i> Advanced Features</h4>
                                    <a href="features.php#ai-analytics"><i class="fas fa-brain"></i> AI Analytics</a>
                                    <a href="features.php#biometric"><i class="fas fa-fingerprint"></i> Biometric Auth</a>
                                    <a href="features.php#lms"><i class="fas fa-book-reader"></i> LMS Integration</a>
                                    <a href="features.php#pwa"><i class="fas fa-mobile-alt"></i> PWA Support</a>
                                </div>
                                <div class="dropdown-column">
                                    <h4><i class="fas fa-puzzle-piece"></i> All Modules</h4>
                                    <a href="modules.php" class="highlight"><i class="fas fa-th-large"></i> View All 42 Modules</a>
                                    <a href="integrations.php"><i class="fas fa-plug"></i> Integrations</a>
                                    <a href="api-documentation.php"><i class="fas fa-code"></i> API Documentation</a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item has-dropdown">
                        <a href="#" class="nav-link">
                            Solutions <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-grid">
                                <div class="dropdown-column">
                                    <h4><i class="fas fa-school"></i> By Institution Type</h4>
                                    <a href="solutions.php?type=k12"><i class="fas fa-child"></i> K-12 Schools</a>
                                    <a href="solutions.php?type=university"><i class="fas fa-university"></i> Universities</a>
                                    <a href="solutions.php?type=training"><i class="fas fa-chalkboard"></i> Training Centers</a>
                                    <a href="solutions.php?type=academy"><i class="fas fa-trophy"></i> Academies</a>
                                </div>
                                <div class="dropdown-column">
                                    <h4><i class="fas fa-users"></i> By User Role</h4>
                                    <a href="solutions.php?role=admin"><i class="fas fa-user-shield"></i> Administrators</a>
                                    <a href="solutions.php?role=teacher"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
                                    <a href="solutions.php?role=student"><i class="fas fa-user-graduate"></i> Students</a>
                                    <a href="solutions.php?role=parent"><i class="fas fa-user-friends"></i> Parents</a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item"><a href="pricing.php" class="nav-link">Pricing</a></li>

                    <li class="nav-item has-dropdown">
                        <a href="#" class="nav-link">
                            Resources <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="case-studies.php"><i class="fas fa-briefcase"></i> Case Studies</a>
                            <a href="documentation.php"><i class="fas fa-book"></i> Documentation</a>
                            <a href="blog.php"><i class="fas fa-newspaper"></i> Blog</a>
                            <a href="webinars.php"><i class="fas fa-video"></i> Webinars</a>
                            <a href="support.php"><i class="fas fa-life-ring"></i> Support Center</a>
                        </div>
                    </li>

                    <li class="nav-item"><a href="about.php" class="nav-link">About</a></li>
                    <li class="nav-item"><a href="contact.php" class="nav-link">Contact</a></li>
                    <li class="nav-item nav-cta">
                        <a href="demo-request.php" class="btn btn-demo">
                            <i class="fas fa-calendar-alt"></i> Request Demo
                        </a>
                    </li>
                    <li class="nav-item nav-cta">
                        <a href="login.php" class="btn btn-login">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-background">
            <div class="hero-particles"></div>
            <div class="hero-gradient"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="badge-icon"><i class="fas fa-award"></i></span>
                    <span class="badge-text">Trusted by 500+ Educational Institutions Worldwide</span>
                </div>
                <h1 class="hero-title">
                    Transform Your <span class="highlight">Educational Institution</span> with AI-Powered Management
                </h1>
                <p class="hero-subtitle">
                    Complete 42-module platform featuring AI analytics, biometric attendance, LMS integration, and advanced cyberpunk UI. Streamline operations, enhance communication, and drive academic excellence.
                </p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number">42</div>
                        <div class="stat-label">Integrated Modules</div>
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
                        <div class="stat-label">Support</div>
                    </div>
                </div>
                <div class="hero-actions">
                    <a href="demo-request.php" class="btn btn-primary btn-large">
                        <i class="fas fa-rocket"></i> Get Started Free
                    </a>
                    <a href="modules.php" class="btn btn-secondary btn-large">
                        <i class="fas fa-play-circle"></i> Explore Platform
                    </a>
                    <a href="#demo-video" class="btn btn-text">
                        <i class="fas fa-play"></i> Watch Demo (2:30)
                    </a>
                </div>
                <div class="hero-trust">
                    <p>Trusted by leading institutions:</p>
                    <div class="trust-logos">
                        <span class="trust-logo">Harvard</span>
                        <span class="trust-logo">MIT</span>
                        <span class="trust-logo">Stanford</span>
                        <span class="trust-logo">Oxford</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Overview Section -->
    <section class="features-overview" id="features">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Platform Capabilities</span>
                <h2 class="section-title">Everything You Need in One Platform</h2>
                <p class="section-subtitle">Comprehensive suite of tools to manage every aspect of your educational institution</p>
            </div>

            <div class="features-grid">
                <div class="feature-card" data-aos="fade-up">
                    <div class="feature-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3>Student Management</h3>
                    <p>Complete student lifecycle management from admission to graduation with automated ID assignment and comprehensive records.</p>
                    <a href="modules.php#student-management" class="feature-link">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <h3>Biometric Attendance</h3>
                    <p>Advanced attendance tracking with fingerprint, QR code, and facial recognition with real-time parent notifications.</p>
                    <a href="features.php#biometric" class="feature-link">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3>AI Analytics</h3>
                    <p>Predictive analytics, performance insights, and data-driven decision making powered by artificial intelligence.</p>
                    <a href="features.php#ai-analytics" class="feature-link">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="feature-card" data-aos="fade-up">
                    <div class="feature-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>Teacher Portal</h3>
                    <p>Dedicated dashboard for class management, grade submission, student performance tracking, and parent communication.</p>
                    <a href="modules.php#teacher-portal" class="feature-link">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3>LMS Integration</h3>
                    <p>Full LTI 1.3 compatible learning management system with course management and online assessments.</p>
                    <a href="features.php#lms" class="feature-link">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3>Finance & Fees</h3>
                    <p>Complete financial management with online payments, automated invoicing, and integrated payroll system.</p>
                    <a href="modules.php#finance" class="feature-link">
                        Learn More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="features-cta">
                <a href="modules.php" class="btn btn-outline-primary">
                    <i class="fas fa-th-large"></i> View All 42 Modules
                </a>
            </div>
        </div>
    </section>

    <!-- Module Categories Section -->
    <section class="module-categories">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Complete Solution</span>
                <h2 class="section-title">42 Integrated Modules</h2>
                <p class="section-subtitle">Explore our comprehensive suite organized by category</p>
            </div>

            <div class="categories-grid">
                <a href="modules.php?category=core" class="category-card">
                    <div class="category-icon"><i class="fas fa-star"></i></div>
                    <h3>Core Management</h3>
                    <span class="module-count">9 Modules</span>
                    <p>Essential tools for daily operations</p>
                    <ul class="category-features">
                        <li>Student Management</li>
                        <li>Teacher Portal</li>
                        <li>Attendance System</li>
                        <li>Parent Portal</li>
                    </ul>
                    <span class="view-link">View Modules <i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="modules.php?category=academic" class="category-card">
                    <div class="category-icon"><i class="fas fa-book"></i></div>
                    <h3>Academic Tools</h3>
                    <span class="module-count">12 Modules</span>
                    <p>Curriculum, exams, and learning</p>
                    <ul class="category-features">
                        <li>Timetable Management</li>
                        <li>Examination System</li>
                        <li>Grades & Reports</li>
                        <li>LMS Integration</li>
                    </ul>
                    <span class="view-link">View Modules <i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="modules.php?category=communication" class="category-card">
                    <div class="category-icon"><i class="fas fa-comments"></i></div>
                    <h3>Communication</h3>
                    <span class="module-count">6 Modules</span>
                    <p>Messaging and collaboration</p>
                    <ul class="category-features">
                        <li>Real-time Chat</li>
                        <li>WhatsApp Integration</li>
                        <li>Announcements</li>
                        <li>Video Conferencing</li>
                    </ul>
                    <span class="view-link">View Modules <i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="modules.php?category=operations" class="category-card">
                    <div class="category-icon"><i class="fas fa-cogs"></i></div>
                    <h3>Operations</h3>
                    <span class="module-count">15 Modules</span>
                    <p>Facilities and resource management</p>
                    <ul class="category-features">
                        <li>Transport & Routes</li>
                        <li>Hostel Management</li>
                        <li>Library System</li>
                        <li>Inventory Control</li>
                    </ul>
                    <span class="view-link">View Modules <i class="fas fa-arrow-right"></i></span>
                </a>
            </div>
        </div>
    </section>

    <!-- Interactive Demo Section -->
    <section class="interactive-demo" id="demo-video">
        <div class="container">
            <div class="demo-content">
                <div class="demo-video">
                    <div class="video-placeholder">
                        <i class="fas fa-play-circle"></i>
                        <p>Watch 2:30 Platform Demo</p>
                    </div>
                    <!-- Video embed would go here -->
                </div>
                <div class="demo-info">
                    <span class="section-badge">See It In Action</span>
                    <h2>Experience Verdant SMS</h2>
                    <p>Watch how leading institutions are transforming their operations with our platform</p>
                    <div class="demo-highlights">
                        <div class="highlight-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Complete walkthrough of core features</span>
                        </div>
                        <div class="highlight-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Real-world use cases and workflows</span>
                        </div>
                        <div class="highlight-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Integration with existing systems</span>
                        </div>
                    </div>
                    <a href="demo-request.php" class="btn btn-primary">
                        <i class="fas fa-calendar-check"></i> Schedule Live Demo
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Success Stories</span>
                <h2 class="section-title">Trusted by Educational Leaders</h2>
                <p class="section-subtitle">See what our clients are saying about Verdant SMS</p>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"Verdant SMS transformed our institution. The AI analytics feature alone saved us hundreds of hours in manual reporting. Highly recommended!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="author-info">
                            <h4>Dr. Sarah Johnson</h4>
                            <p>Principal, Green Valley Academy</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"The biometric attendance system integrated seamlessly. Parents love the real-time notifications, and we've reduced administrative overhead by 60%."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="author-info">
                            <h4>Michael Chen</h4>
                            <p>IT Director, Riverside International School</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"Outstanding platform! The 24/7 support team is incredibly responsive. Our teachers adapted to the system within days, not weeks."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="author-info">
                            <h4>Prof. Emily Rodriguez</h4>
                            <p>Dean of Academics, Metropolitan University</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="testimonials-cta">
                <a href="case-studies.php" class="btn btn-outline-primary">
                    <i class="fas fa-briefcase"></i> Read More Success Stories
                </a>
            </div>
        </div>
    </section>

    <!-- Pricing Preview Section -->
    <section class="pricing-preview">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Flexible Plans</span>
                <h2 class="section-title">Choose Your Perfect Plan</h2>
                <p class="section-subtitle">Scalable pricing for institutions of all sizes</p>
            </div>

            <div class="pricing-grid">
                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3>Starter</h3>
                        <p class="pricing-description">Perfect for small schools</p>
                    </div>
                    <div class="pricing-price">
                        <span class="price-currency">$</span>
                        <span class="price-amount">299</span>
                        <span class="price-period">/month</span>
                    </div>
                    <ul class="pricing-features">
                        <li><i class="fas fa-check"></i> Up to 500 students</li>
                        <li><i class="fas fa-check"></i> 20 core modules</li>
                        <li><i class="fas fa-check"></i> Email support</li>
                        <li><i class="fas fa-check"></i> Basic analytics</li>
                        <li><i class="fas fa-check"></i> Mobile apps</li>
                    </ul>
                    <a href="pricing.php#starter" class="btn btn-outline">Get Started</a>
                </div>

                <div class="pricing-card featured">
                    <div class="pricing-badge">Most Popular</div>
                    <div class="pricing-header">
                        <h3>Professional</h3>
                        <p class="pricing-description">For growing institutions</p>
                    </div>
                    <div class="pricing-price">
                        <span class="price-currency">$</span>
                        <span class="price-amount">799</span>
                        <span class="price-period">/month</span>
                    </div>
                    <ul class="pricing-features">
                        <li><i class="fas fa-check"></i> Up to 2,000 students</li>
                        <li><i class="fas fa-check"></i> All 42 modules</li>
                        <li><i class="fas fa-check"></i> Priority support</li>
                        <li><i class="fas fa-check"></i> AI analytics</li>
                        <li><i class="fas fa-check"></i> Biometric integration</li>
                        <li><i class="fas fa-check"></i> Custom branding</li>
                    </ul>
                    <a href="pricing.php#professional" class="btn btn-primary">Get Started</a>
                </div>

                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3>Enterprise</h3>
                        <p class="pricing-description">For large institutions</p>
                    </div>
                    <div class="pricing-price">
                        <span class="price-currency">$</span>
                        <span class="price-amount">Custom</span>
                    </div>
                    <ul class="pricing-features">
                        <li><i class="fas fa-check"></i> Unlimited students</li>
                        <li><i class="fas fa-check"></i> All features</li>
                        <li><i class="fas fa-check"></i> 24/7 dedicated support</li>
                        <li><i class="fas fa-check"></i> Custom integrations</li>
                        <li><i class="fas fa-check"></i> On-premise deployment</li>
                        <li><i class="fas fa-check"></i> SLA guarantee</li>
                    </ul>
                    <a href="contact.php?plan=enterprise" class="btn btn-outline">Contact Sales</a>
                </div>
            </div>

            <div class="pricing-note">
                <p><i class="fas fa-info-circle"></i> All plans include free migration assistance and training</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Transform Your Institution?</h2>
                <p>Join 500+ educational institutions worldwide using Verdant SMS</p>
                <div class="cta-actions">
                    <a href="demo-request.php" class="btn btn-white btn-large">
                        <i class="fas fa-calendar-alt"></i> Request Free Demo
                    </a>
                    <a href="contact.php" class="btn btn-outline-white btn-large">
                        <i class="fas fa-phone"></i> Talk to Sales
                    </a>
                </div>
                <p class="cta-note">
                    <i class="fas fa-shield-alt"></i> No credit card required • Free 30-day trial • Cancel anytime
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column footer-about">
                    <div class="footer-brand">
                        <div class="brand-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3>Verdant SMS</h3>
                    </div>
                    <p>Complete 42-module school management platform with AI analytics, biometric attendance, and LMS integration.</p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div class="footer-column">
                    <h4>Platform</h4>
                    <ul class="footer-links">
                        <li><a href="modules.php">All 42 Modules</a></li>
                        <li><a href="features.php">Features</a></li>
                        <li><a href="integrations.php">Integrations</a></li>
                        <li><a href="api-documentation.php">API Docs</a></li>
                        <li><a href="security.php">Security</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4>Solutions</h4>
                    <ul class="footer-links">
                        <li><a href="solutions.php?type=k12">K-12 Schools</a></li>
                        <li><a href="solutions.php?type=university">Universities</a></li>
                        <li><a href="solutions.php?type=training">Training Centers</a></li>
                        <li><a href="solutions.php?type=academy">Academies</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4>Resources</h4>
                    <ul class="footer-links">
                        <li><a href="case-studies.php">Case Studies</a></li>
                        <li><a href="documentation.php">Documentation</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        <li><a href="webinars.php">Webinars</a></li>
                        <li><a href="support.php">Support</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4>Company</h4>
                    <ul class="footer-links">
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="careers.php">Careers</a></li>
                        <li><a href="press.php">Press Kit</a></li>
                        <li><a href="partners.php">Partners</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="footer-legal">
                    <p>&copy; 2025 Verdant SMS. All rights reserved.</p>
                    <div class="legal-links">
                        <a href="privacy-policy.php">Privacy Policy</a>
                        <a href="terms-of-service.php">Terms of Service</a>
                        <a href="cookie-policy.php">Cookie Policy</a>
                    </div>
                </div>
                <div class="footer-badges">
                    <span class="badge">Version 3.0.0</span>
                    <span class="badge">Production Ready</span>
                    <span class="badge"><i class="fas fa-shield-alt"></i> SOC 2 Certified</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="assets/js/homepage.js"></script>
    <script src="assets/js/pwa-manager.js"></script>
</body>

</html>