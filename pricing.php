<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Plans - Verdant SMS | Flexible School Management Solutions</title>
    <meta name="description" content="Affordable pricing plans for schools of all sizes. From startups to enterprise institutions, find the perfect Verdant SMS plan for your needs.">

    <!-- Favicons -->
    <link rel="icon" href="assets/images/favicon.ico">
    <link rel="manifest" href="manifest.json">

    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/homepage.css">

    <style>
        .pricing-toggle {
            text-align: center;
            margin: 3rem auto;
            max-width: 400px;
        }

        .toggle-switch {
            display: inline-flex;
            background: rgba(30, 30, 30, 0.8);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            padding: 6px;
            gap: 6px;
        }

        .toggle-option {
            padding: 12px 32px;
            border-radius: 50px;
            background: transparent;
            color: var(--text-muted);
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .toggle-option.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .savings-badge {
            display: inline-block;
            background: var(--accent-color);
            color: #000;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-left: 8px;
        }

        .pricing-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2.5rem;
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        .pricing-card {
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .pricing-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-10px);
            box-shadow: 0 0 40px rgba(0, 191, 255, 0.3);
        }

        .pricing-card.popular {
            border-color: var(--primary-color);
            box-shadow: 0 0 40px rgba(0, 191, 255, 0.3);
        }

        .popular-badge {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 8px 24px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .plan-name {
            font-size: 1.8rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .plan-price {
            font-size: 3.5rem;
            color: var(--primary-color);
            font-weight: 900;
            margin: 1.5rem 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .currency {
            font-size: 2rem;
            opacity: 0.7;
        }

        .period {
            font-size: 1.2rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .plan-description {
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-size: 1rem;
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 2.5rem 0;
            text-align: left;
        }

        .plan-features li {
            padding: 0.75rem 0;
            color: var(--text-secondary);
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .plan-features li i {
            color: var(--accent-color);
            margin-top: 4px;
            flex-shrink: 0;
        }

        .plan-features li.unavailable {
            opacity: 0.4;
        }

        .plan-features li.unavailable i {
            color: var(--text-muted);
        }

        .plan-cta {
            width: 100%;
            padding: 16px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            margin-top: 2rem;
        }

        .faq-section {
            max-width: 900px;
            margin: 6rem auto;
            padding: 0 2rem;
        }

        .faq-item {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .faq-question {
            width: 100%;
            padding: 1.5rem;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.1rem;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            color: var(--primary-color);
        }

        .faq-question i {
            transition: transform 0.3s ease;
        }

        .faq-question.active i {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .faq-answer.active {
            max-height: 500px;
        }

        .faq-answer-content {
            padding: 0 1.5rem 1.5rem;
            color: var(--text-muted);
            line-height: 1.8;
        }

        .comparison-section {
            background: rgba(10, 10, 10, 0.5);
            padding: 6rem 2rem;
        }

        .comparison-table-wrapper {
            max-width: 1200px;
            margin: 3rem auto;
            overflow-x: auto;
        }

        .comparison-table {
            width: 100%;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            overflow: hidden;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .comparison-table th {
            background: rgba(0, 191, 255, 0.1);
            color: var(--primary-color);
            font-weight: 700;
        }

        .comparison-table th:first-child {
            text-align: left;
        }

        .comparison-table td:first-child {
            text-align: left;
            font-weight: 600;
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            .pricing-cards {
                grid-template-columns: 1fr;
            }

            .plan-price {
                font-size: 2.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <?php include 'includes/navigation.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Simple, Transparent Pricing</h1>
            <p>Choose the perfect plan for your institution. All plans include core features with flexible scaling options.</p>
        </div>
    </section>

    <!-- Pricing Toggle -->
    <div class="pricing-toggle">
        <div class="toggle-switch">
            <button class="toggle-option active" data-period="monthly">Monthly</button>
            <button class="toggle-option" data-period="annual">
                Annual <span class="savings-badge">Save 20%</span>
            </button>
        </div>
    </div>

    <!-- Pricing Cards -->
    <section class="pricing-cards">

        <!-- Starter Plan -->
        <div class="pricing-card" id="starter">
            <h3 class="plan-name">Starter</h3>
            <p class="plan-description">Perfect for small schools and startups</p>
            <div class="plan-price">
                <span class="currency">$</span>
                <span class="amount" data-monthly="99" data-annual="79">99</span>
                <span class="period">/month</span>
            </div>

            <ul class="plan-features">
                <li><i class="fas fa-check-circle"></i> <span>Up to <strong>500 students</strong></span></li>
                <li><i class="fas fa-check-circle"></i> <span>9 Core Modules</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Attendance System (QR Code)</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Parent Portal Access</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Basic Reporting</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Email Support</span></li>
                <li><i class="fas fa-check-circle"></i> <span>5GB Cloud Storage</span></li>
                <li class="unavailable"><i class="fas fa-times-circle"></i> <span>AI Analytics</span></li>
                <li class="unavailable"><i class="fas fa-times-circle"></i> <span>Biometric Attendance</span></li>
                <li class="unavailable"><i class="fas fa-times-circle"></i> <span>LMS Integration</span></li>
            </ul>

            <a href="demo-request.php?plan=starter" class="btn btn-outline plan-cta">
                Get Started
            </a>
        </div>

        <!-- Professional Plan (Popular) -->
        <div class="pricing-card popular" id="professional">
            <span class="popular-badge">Most Popular</span>
            <h3 class="plan-name">Professional</h3>
            <p class="plan-description">For growing schools with advanced needs</p>
            <div class="plan-price">
                <span class="currency">$</span>
                <span class="amount" data-monthly="299" data-annual="239">299</span>
                <span class="period">/month</span>
            </div>

            <ul class="plan-features">
                <li><i class="fas fa-check-circle"></i> <span>Up to <strong>2,000 students</strong></span></li>
                <li><i class="fas fa-check-circle"></i> <span>All 42 Modules</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Biometric Attendance (Fingerprint + QR)</span></li>
                <li><i class="fas fa-check-circle"></i> <span>AI Analytics & Predictions</span></li>
                <li><i class="fas fa-check-circle"></i> <span>LMS Integration (LTI 1.3)</span></li>
                <li><i class="fas fa-check-circle"></i> <span>WhatsApp Integration</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Priority Support (24/7)</span></li>
                <li><i class="fas fa-check-circle"></i> <span>50GB Cloud Storage</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Custom Branding</span></li>
                <li><i class="fas fa-check-circle"></i> <span>API Access</span></li>
            </ul>

            <a href="demo-request.php?plan=professional" class="btn btn-primary plan-cta">
                Start Free Trial
            </a>
        </div>

        <!-- Enterprise Plan -->
        <div class="pricing-card" id="enterprise">
            <h3 class="plan-name">Enterprise</h3>
            <p class="plan-description">Unlimited scale for large institutions</p>
            <div class="plan-price">
                <span class="amount" style="font-size: 2rem;">Custom</span>
            </div>

            <ul class="plan-features">
                <li><i class="fas fa-check-circle"></i> <span><strong>Unlimited students</strong></span></li>
                <li><i class="fas fa-check-circle"></i> <span>All Professional Features</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Dedicated Server Hosting</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Advanced AI Customization</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Multi-campus Support</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Custom Module Development</span></li>
                <li><i class="fas fa-check-circle"></i> <span>White-label Solution</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Dedicated Account Manager</span></li>
                <li><i class="fas fa-check-circle"></i> <span>On-premise Deployment Option</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Unlimited Cloud Storage</span></li>
            </ul>

            <a href="contact.php?plan=enterprise" class="btn btn-outline plan-cta">
                Contact Sales
            </a>
        </div>

    </section>

    <!-- Detailed Comparison -->
    <section class="comparison-section">
        <div class="container">
            <h2 class="section-title">Detailed Feature Comparison</h2>
            <p class="section-subtitle">Compare all plans side-by-side</p>

            <div class="comparison-table-wrapper">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>Starter</th>
                            <th>Professional</th>
                            <th>Enterprise</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Student Capacity</td>
                            <td>500</td>
                            <td>2,000</td>
                            <td>Unlimited</td>
                        </tr>
                        <tr>
                            <td>Modules Included</td>
                            <td>9 Core</td>
                            <td>All 42</td>
                            <td>All 42 + Custom</td>
                        </tr>
                        <tr>
                            <td>AI Analytics</td>
                            <td>-</td>
                            <td><i class="fas fa-check-circle check-icon"></i></td>
                            <td><i class="fas fa-check-circle check-icon"></i></td>
                        </tr>
                        <tr>
                            <td>Biometric Attendance</td>
                            <td>QR Only</td>
                            <td>Fingerprint + QR</td>
                            <td>All Methods</td>
                        </tr>
                        <tr>
                            <td>LMS Integration</td>
                            <td>-</td>
                            <td><i class="fas fa-check-circle check-icon"></i></td>
                            <td><i class="fas fa-check-circle check-icon"></i></td>
                        </tr>
                        <tr>
                            <td>Cloud Storage</td>
                            <td>5GB</td>
                            <td>50GB</td>
                            <td>Unlimited</td>
                        </tr>
                        <tr>
                            <td>Support</td>
                            <td>Email</td>
                            <td>24/7 Priority</td>
                            <td>Dedicated Manager</td>
                        </tr>
                        <tr>
                            <td>Custom Branding</td>
                            <td>-</td>
                            <td><i class="fas fa-check-circle check-icon"></i></td>
                            <td><i class="fas fa-check-circle check-icon"></i></td>
                        </tr>
                        <tr>
                            <td>API Access</td>
                            <td>-</td>
                            <td><i class="fas fa-check-circle check-icon"></i></td>
                            <td><i class="fas fa-check-circle check-icon"></i></td>
                        </tr>
                        <tr>
                            <td>Multi-campus</td>
                            <td>-</td>
                            <td>-</td>
                            <td><i class="fas fa-check-circle check-icon"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <h2 class="section-title" style="text-align: center; margin-bottom: 3rem;">Frequently Asked Questions</h2>

        <div class="faq-item">
            <button class="faq-question">
                <span>What payment methods do you accept?</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    We accept all major credit cards (Visa, MasterCard, American Express), bank transfers, and PayPal. Enterprise customers can also request invoice billing with NET30 terms.
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">
                <span>Can I switch plans later?</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    Yes! You can upgrade or downgrade your plan at any time. When upgrading, you'll be charged the prorated difference. When downgrading, credits will be applied to your next billing cycle.
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">
                <span>Is there a free trial available?</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    Yes! The Professional plan includes a 30-day free trial with full access to all features. No credit card required to start. For Enterprise plans, we offer a customized proof-of-concept period.
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">
                <span>What happens to my data if I cancel?</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    Your data is always yours. Upon cancellation, you have 90 days to export all your data in standard formats (CSV, JSON, PDF). We provide comprehensive export tools and can assist with data migration if needed.
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">
                <span>Do you offer discounts for non-profit schools?</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    Yes! We offer special pricing for non-profit educational institutions, including up to 30% discount on annual plans. Contact our sales team with your non-profit documentation to learn more.
                </div>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question">
                <span>Is training included in the price?</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    Professional and Enterprise plans include comprehensive onboarding training for administrators and teachers. We also provide ongoing video tutorials, documentation, and webinars for all plans.
                </div>
            </div>
        </div>

    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container cta-content">
            <h2>Ready to Transform Your Institution?</h2>
            <p>Start your free trial today or schedule a personalized demo with our team.</p>
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
    <script>
        // Pricing toggle
        const toggleOptions = document.querySelectorAll('.toggle-option');
        const priceAmounts = document.querySelectorAll('.amount');

        toggleOptions.forEach(option => {
            option.addEventListener('click', () => {
                toggleOptions.forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');

                const period = option.dataset.period;
                priceAmounts.forEach(amount => {
                    amount.textContent = amount.dataset[period];
                });
            });
        });

        // FAQ accordion
        const faqQuestions = document.querySelectorAll('.faq-question');

        faqQuestions.forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                const isActive = question.classList.contains('active');

                // Close all
                faqQuestions.forEach(q => {
                    q.classList.remove('active');
                    q.nextElementSibling.classList.remove('active');
                });

                // Open clicked if it wasn't active
                if (!isActive) {
                    question.classList.add('active');
                    answer.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>