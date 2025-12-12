<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Demo - Verdant SMS | See the Platform in Action</title>
    <meta name="description" content="Schedule a personalized demo of Verdant SMS. See how our 42-module school management system can transform your institution.">

    <link rel="icon" href="assets/images/favicon.ico">
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/homepage.css">

    <style>
        .demo-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: start;
        }

        .demo-form-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 3rem;
        }

        .demo-info-section h2 {
            font-size: 2.5rem;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .demo-info-section p {
            font-size: 1.1rem;
            color: var(--text-muted);
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .demo-benefits {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
        }

        .demo-benefits li {
            padding: 1rem;
            margin-bottom: 1rem;
            background: rgba(30, 30, 30, 0.6);
            border-left: 3px solid var(--accent-color);
            border-radius: 8px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .demo-benefits li i {
            color: var(--accent-color);
            font-size: 1.3rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: var(--text-secondary);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            background: rgba(30, 30, 30, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 191, 255, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 191, 255, 0.4);
        }

        .success-message {
            display: none;
            background: rgba(0, 255, 157, 0.1);
            border: 1px solid var(--accent-color);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            color: var(--accent-color);
            text-align: center;
        }

        .success-message.show {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        .testimonial-card {
            background: rgba(30, 30, 30, 0.6);
            border-left: 3px solid var(--primary-color);
            border-radius: 12px;
            padding: 2rem;
            margin: 2rem 0;
        }

        .testimonial-card p {
            font-style: italic;
            margin-bottom: 1rem;
        }

        .testimonial-author {
            font-weight: 600;
            color: var(--primary-color);
        }

        @media (max-width: 968px) {
            .demo-container {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/navigation.php'; ?>

    <section class="page-header">
        <div class="container">
            <h1>Request a Personalized Demo</h1>
            <p>See how Verdant SMS can transform your institution. Schedule a free, no-obligation demonstration tailored to your needs.</p>
        </div>
    </section>

    <div class="demo-container">
        <!-- Left Side: Info -->
        <div class="demo-info-section">
            <h2>What to Expect</h2>
            <p>Our product specialists will guide you through a personalized tour of Verdant SMS, focusing on the features most relevant to your institution.</p>

            <ul class="demo-benefits">
                <li>
                    <i class="fas fa-video"></i>
                    <span><strong>Live 1-on-1 Session</strong> - 45-minute video call with our expert team</span>
                </li>
                <li>
                    <i class="fas fa-cogs"></i>
                    <span><strong>Custom Configuration</strong> - See the system configured for your institution type</span>
                </li>
                <li>
                    <i class="fas fa-hands-helping"></i>
                    <span><strong>Interactive Q&A</strong> - Ask questions and explore specific features</span>
                </li>
                <li>
                    <i class="fas fa-file-alt"></i>
                    <span><strong>ROI Analysis</strong> - Receive a custom cost-benefit report</span>
                </li>
                <li>
                    <i class="fas fa-flask"></i>
                    <span><strong>Trial Access</strong> - Optional 30-day trial account to test the platform</span>
                </li>
            </ul>

            <div class="testimonial-card">
                <p>"The demo was incredibly thorough. Within 30 minutes, I knew Verdant SMS was the right choice for our school. We went live in just 3 weeks!"</p>
                <div class="testimonial-author">— Dr. Sarah Mitchell, Principal, Lincoln Academy</div>
            </div>

            <h3 style="color: var(--text-primary); margin: 2rem 0 1rem;">What We'll Cover:</h3>
            <ul class="feature-list-compact">
                <li><i class="fas fa-check"></i> Student enrollment and management workflows</li>
                <li><i class="fas fa-check"></i> Attendance tracking with biometric integration</li>
                <li><i class="fas fa-check"></i> Gradebook and academic reporting</li>
                <li><i class="fas fa-check"></i> Parent communication tools</li>
                <li><i class="fas fa-check"></i> Fee management and payment processing</li>
                <li><i class="fas fa-check"></i> Custom features specific to your needs</li>
            </ul>
        </div>

        <!-- Right Side: Form -->
        <div class="demo-form-section">
            <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">Schedule Your Demo</h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Fill out the form below and we'll contact you within 24 hours to schedule your personalized demonstration.</p>

            <form id="demoRequestForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="firstName" required>
                    </div>

                    <div class="form-group">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Work Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="institutionName">Institution Name *</label>
                        <input type="text" id="institutionName" name="institutionName" required>
                    </div>

                    <div class="form-group">
                        <label for="institutionType">Institution Type *</label>
                        <select id="institutionType" name="institutionType" required>
                            <option value="">Select type...</option>
                            <option value="k12">K-12 School</option>
                            <option value="university">University/College</option>
                            <option value="training">Training Center</option>
                            <option value="academy">Specialized Academy</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="studentCount">Number of Students *</label>
                        <select id="studentCount" name="studentCount" required>
                            <option value="">Select range...</option>
                            <option value="0-100">0-100</option>
                            <option value="101-500">101-500</option>
                            <option value="501-1000">501-1,000</option>
                            <option value="1001-2000">1,001-2,000</option>
                            <option value="2001+">2,001+</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="role">Your Role *</label>
                        <select id="role" name="role" required>
                            <option value="">Select role...</option>
                            <option value="principal">Principal/Head</option>
                            <option value="administrator">Administrator</option>
                            <option value="it">IT Manager</option>
                            <option value="teacher">Teacher/Faculty</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="plan">Interested In</label>
                    <select id="plan" name="plan">
                        <option value="">Select plan...</option>
                        <option value="starter">Starter Plan</option>
                        <option value="professional">Professional Plan</option>
                        <option value="enterprise">Enterprise Plan</option>
                        <option value="unsure">Not sure yet</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="features">Features of Interest (Optional)</label>
                    <textarea id="features" name="features" placeholder="e.g., Biometric attendance, AI analytics, LMS integration..."></textarea>
                </div>

                <div class="form-group">
                    <label for="timeline">When are you planning to implement? *</label>
                    <select id="timeline" name="timeline" required>
                        <option value="">Select timeline...</option>
                        <option value="immediate">Immediately</option>
                        <option value="1-3-months">1-3 months</option>
                        <option value="3-6-months">3-6 months</option>
                        <option value="6-12-months">6-12 months</option>
                        <option value="exploring">Just exploring</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Additional Comments</label>
                    <textarea id="message" name="message" placeholder="Tell us more about your needs..."></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-calendar-check"></i> Request Demo
                </button>

                <div class="success-message" id="successMessage">
                    <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <h3>Demo Request Submitted!</h3>
                    <p>Thank you for your interest in Verdant SMS. Our team will contact you within 24 hours to schedule your personalized demonstration.</p>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/homepage.js"></script>
    <script>
        // Pre-fill form from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const planParam = urlParams.get('plan');
        const featureParam = urlParams.get('feature');

        if (planParam) {
            document.getElementById('plan').value = planParam;
        }

        if (featureParam) {
            const featureMap = {
                'ai': 'AI Analytics & Predictions',
                'biometric': 'Biometric Attendance System',
                'lms': 'LMS Integration (LTI 1.3)',
                'pwa': 'Progressive Web App'
            };
            document.getElementById('features').value = featureMap[featureParam] || featureParam;
        }

        // Form submission
        document.getElementById('demoRequestForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Show loading state
            const submitBtn = e.target.querySelector('.submit-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            submitBtn.disabled = true;

            // Simulate API call (replace with actual endpoint)
            setTimeout(() => {
                // Hide form, show success
                e.target.style.display = 'none';
                document.getElementById('successMessage').classList.add('show');

                // Reset button (in case user wants to submit again)
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;

                // Optional: Send data to backend
                // fetch('api/demo-request.php', {
                //     method: 'POST',
                //     body: new FormData(e.target)
                // });
            }, 1500);
        });
    </script>
</body>

</html>