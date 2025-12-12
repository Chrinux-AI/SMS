<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Verdant SMS | Get in Touch with Our Team</title>
    <meta name="description" content="Contact Verdant SMS for sales inquiries, support, partnerships, or general questions. We're here to help your institution succeed.">

    <link rel="icon" href="assets/images/favicon.ico">
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/homepage.css">

    <style>
        .contact-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 4rem 2rem;
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 4rem;
        }

        .contact-info {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 3rem;
            height: fit-content;
        }

        .contact-info h2 {
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .contact-method {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background: rgba(30, 30, 30, 0.6);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .contact-method:hover {
            border-left: 3px solid var(--primary-color);
            transform: translateX(5px);
        }

        .contact-method i {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .contact-method h3 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }

        .contact-method p {
            color: var(--text-muted);
            margin: 0;
        }

        .contact-method a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
        }

        .contact-method a:hover {
            text-decoration: underline;
        }

        .office-hours {
            background: rgba(0, 191, 255, 0.1);
            border: 1px solid var(--primary-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .office-hours h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .office-hours p {
            color: var(--text-secondary);
            margin: 0.5rem 0;
        }

        .contact-form-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 3rem;
        }

        .contact-form-section h2 {
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .department-tabs {
            display: flex;
            gap: 1rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .dept-tab {
            padding: 12px 20px;
            background: rgba(30, 30, 30, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dept-tab:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .dept-tab.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-color: var(--primary-color);
            color: white;
        }

        @media (max-width: 968px) {
            .contact-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/navigation.php'; ?>

    <section class="page-header">
        <div class="container">
            <h1>Get in Touch</h1>
            <p>We're here to answer your questions and help you get started with Verdant SMS.</p>
        </div>
    </section>

    <div class="contact-container">
        <!-- Contact Information -->
        <div class="contact-info">
            <h2>Contact Information</h2>

            <div class="contact-method">
                <i class="fas fa-phone-alt"></i>
                <h3>Phone</h3>
                <p>Sales: <a href="tel:+1-800-VERDANT">+1 (800) VERDANT</a></p>
                <p>Support: <a href="tel:+1-800-SUPPORT">+1 (800) SUPPORT</a></p>
            </div>

            <div class="contact-method">
                <i class="fas fa-envelope"></i>
                <h3>Email</h3>
                <p>Sales: <a href="mailto:sales@verdant-sms.com">sales@verdant-sms.com</a></p>
                <p>Support: <a href="mailto:support@verdant-sms.com">support@verdant-sms.com</a></p>
                <p>General: <a href="mailto:info@verdant-sms.com">info@verdant-sms.com</a></p>
            </div>

            <div class="contact-method">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Head Office</h3>
                <p>123 Education Boulevard<br>
                    Silicon Valley, CA 94025<br>
                    United States</p>
            </div>

            <div class="contact-method">
                <i class="fab fa-whatsapp"></i>
                <h3>WhatsApp Business</h3>
                <p><a href="https://wa.me/18005555555">+1 (800) 555-5555</a></p>
                <p style="font-size: 0.9rem; margin-top: 0.5rem;">For quick questions and support</p>
            </div>

            <div class="contact-method">
                <i class="fas fa-comment-dots"></i>
                <h3>Live Chat</h3>
                <p>Available on our website</p>
                <p style="font-size: 0.9rem; margin-top: 0.5rem;">Chat with our team in real-time</p>
            </div>

            <div class="office-hours">
                <h3><i class="fas fa-clock"></i> Office Hours</h3>
                <p><strong>Monday - Friday:</strong> 8:00 AM - 8:00 PM EST</p>
                <p><strong>Saturday:</strong> 9:00 AM - 5:00 PM EST</p>
                <p><strong>Sunday:</strong> Closed</p>
                <p style="margin-top: 1rem; font-size: 0.9rem;">
                    <i class="fas fa-info-circle"></i> Enterprise customers have 24/7 priority support
                </p>
            </div>

            <div style="margin-top: 2rem;">
                <h3 style="color: var(--text-primary); margin-bottom: 1rem;">Follow Us</h3>
                <div style="display: flex; gap: 1rem;">
                    <a href="#" class="btn btn-outline" style="width: 50px; height: 50px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="btn btn-outline" style="width: 50px; height: 50px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="#" class="btn btn-outline" style="width: 50px; height: 50px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" class="btn btn-outline" style="width: 50px; height: 50px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-section">
            <h2>Send Us a Message</h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Fill out the form below and we'll get back to you within 24 hours.</p>

            <!-- Department Tabs -->
            <div class="department-tabs">
                <button class="dept-tab active" data-dept="sales">
                    <i class="fas fa-shopping-cart"></i> Sales
                </button>
                <button class="dept-tab" data-dept="support">
                    <i class="fas fa-headset"></i> Support
                </button>
                <button class="dept-tab" data-dept="partnership">
                    <i class="fas fa-handshake"></i> Partnerships
                </button>
                <button class="dept-tab" data-dept="general">
                    <i class="fas fa-envelope"></i> General
                </button>
            </div>

            <form id="contactForm">
                <input type="hidden" id="department" name="department" value="sales">

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
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone">
                </div>

                <div class="form-group">
                    <label for="institution">Institution/Organization</label>
                    <input type="text" id="institution" name="institution">
                </div>

                <div class="form-group">
                    <label for="subject">Subject *</label>
                    <input type="text" id="subject" name="subject" required>
                </div>

                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" required style="min-height: 150px;"></textarea>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" required style="width: auto;">
                        <span>I agree to the <a href="privacy-policy.php" style="color: var(--primary-color);">Privacy Policy</a> and consent to being contacted about Verdant SMS.</span>
                    </label>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>

                <div class="success-message" id="successMessage">
                    <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <h3>Message Sent Successfully!</h3>
                    <p>Thank you for contacting Verdant SMS. We've received your message and will respond within 24 hours.</p>
                </div>
            </form>
        </div>
    </div>

    <!-- Map Section (Optional) -->
    <section class="features-section" style="background: rgba(10, 10, 10, 0.8);">
        <div class="container" style="text-align: center;">
            <h2 class="section-title">Our Global Presence</h2>
            <p class="section-subtitle">Serving educational institutions across 50+ countries</p>

            <div style="max-width: 1000px; margin: 3rem auto; background: rgba(30, 30, 30, 0.6); border: 1px solid var(--border-color); border-radius: 20px; padding: 3rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                    <div>
                        <h3 style="color: var(--primary-color); font-size: 2.5rem; margin-bottom: 0.5rem;">50+</h3>
                        <p style="color: var(--text-muted);">Countries Worldwide</p>
                    </div>
                    <div>
                        <h3 style="color: var(--primary-color); font-size: 2.5rem; margin-bottom: 0.5rem;">1,200+</h3>
                        <p style="color: var(--text-muted);">Institutions</p>
                    </div>
                    <div>
                        <h3 style="color: var(--primary-color); font-size: 2.5rem; margin-bottom: 0.5rem;">500K+</h3>
                        <p style="color: var(--text-muted);">Active Users</p>
                    </div>
                    <div>
                        <h3 style="color: var(--primary-color); font-size: 2.5rem; margin-bottom: 0.5rem;">24/7</h3>
                        <p style="color: var(--text-muted);">Support Available</p>
                    </div>
                </div>
            </div>

            <div style="margin-top: 3rem;">
                <h3 style="color: var(--text-primary); margin-bottom: 1.5rem;">Regional Offices</h3>
                <div class="solution-grid" style="max-width: 1000px; margin: 0 auto;">
                    <div class="solution-card">
                        <h4 style="color: var(--primary-color);">North America</h4>
                        <p>123 Education Blvd<br>Silicon Valley, CA 94025<br>USA</p>
                    </div>
                    <div class="solution-card">
                        <h4 style="color: var(--primary-color);">Europe</h4>
                        <p>45 Innovation Street<br>London EC1A 1BB<br>United Kingdom</p>
                    </div>
                    <div class="solution-card">
                        <h4 style="color: var(--primary-color);">Asia Pacific</h4>
                        <p>78 Tech Park Avenue<br>Singapore 018956<br>Singapore</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/homepage.js"></script>
    <script>
        // Pre-fill from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const planParam = urlParams.get('plan');

        if (planParam) {
            document.getElementById('subject').value = `Inquiry about ${planParam.charAt(0).toUpperCase() + planParam.slice(1)} Plan`;
        }

        // Department tabs
        const deptTabs = document.querySelectorAll('.dept-tab');
        const deptInput = document.getElementById('department');

        deptTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                deptTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                deptInput.value = tab.dataset.dept;
            });
        });

        // Form submission
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = e.target.querySelector('.submit-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            submitBtn.disabled = true;

            setTimeout(() => {
                e.target.style.display = 'none';
                document.getElementById('successMessage').classList.add('show');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 1500);
        });
    </script>
</body>

</html>