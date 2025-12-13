<?php

/**
 * FAQ - Visitor Page
 * Frequently Asked Questions
 */
$page_title = 'FAQ - Verdant SMS';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Frequently asked questions about Verdant SMS school management system.">
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
            font-size: 1.1rem;
        }

        .faq-section {
            margin-bottom: 3rem;
        }

        .faq-section h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .faq-item {
            background: rgba(20, 20, 30, 0.9);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .faq-question {
            padding: 1.25rem 1.5rem;
            background: none;
            border: none;
            color: #fff;
            width: 100%;
            text-align: left;
            font-size: 1.05rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .faq-question:hover {
            background: rgba(0, 191, 255, 0.05);
        }

        .faq-question i {
            color: var(--primary);
            transition: transform 0.3s;
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .faq-item.active .faq-answer {
            max-height: 500px;
        }

        .faq-answer-content {
            padding: 0 1.5rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            border-top: 1px solid var(--border);
        }

        .faq-answer-content p {
            margin-top: 1rem;
        }

        .faq-answer-content ul {
            margin: 1rem 0 0 1.5rem;
        }

        .faq-answer-content li {
            margin-bottom: 0.5rem;
        }

        .cta-box {
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.1), rgba(138, 43, 226, 0.1));
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            margin-top: 3rem;
        }

        .cta-box h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .cta-box p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 1.5rem;
        }

        .cta-box a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 2rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .cta-box a:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 25px rgba(0, 191, 255, 0.4);
        }

        @media (max-width: 640px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .faq-question {
                font-size: 0.95rem;
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <?php include '../includes/visitor-nav.php'; ?>

    <main>
        <div class="page-header">
            <h1><i class="fas fa-question-circle"></i> Frequently Asked Questions</h1>
            <p>Find answers to common questions about Verdant SMS</p>
        </div>

        <div class="faq-section">
            <h2><i class="fas fa-user-graduate"></i> Student Registration</h2>

            <div class="faq-item">
                <button class="faq-question">
                    How do I register my child as a student?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Students must first pass the entrance examination before they can register:</p>
                        <ul>
                            <li>Visit the school to register for the entrance exam</li>
                            <li>Take the online exam (timed MCQ)</li>
                            <li>If passed, you'll receive an Entrance ID (VERDANT-EXAM-XXXXXXXX)</li>
                            <li>Use this ID to complete registration on our website</li>
                            <li>The school admin will approve the registration</li>
                            <li>You'll receive an email to verify your account</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    What documents are required for registration?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>You'll need the following documents:</p>
                        <ul>
                            <li>Birth certificate or age declaration</li>
                            <li>Passport photographs (digital)</li>
                            <li>Previous school report cards (for transfers)</li>
                            <li>Immunization records</li>
                            <li>Parent/Guardian identification</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-section">
            <h2><i class="fas fa-money-bill-wave"></i> Fees & Payments</h2>

            <div class="faq-item">
                <button class="faq-question">
                    What payment methods are accepted?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>We accept multiple payment methods:</p>
                        <ul>
                            <li><strong>Online:</strong> Paystack, Flutterwave (card, bank transfer, USSD)</li>
                            <li><strong>Bank Transfer:</strong> Direct transfer to school account</li>
                            <li><strong>Installments:</strong> Payment plans available per term</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    Can I pay fees in installments?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Yes! We offer flexible payment plans. Contact the school's accounts department to arrange an installment schedule that works for your family.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-section">
            <h2><i class="fas fa-mobile-alt"></i> System Access</h2>

            <div class="faq-item">
                <button class="faq-question">
                    Can I access the system on my phone?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Yes! Verdant SMS is a Progressive Web App (PWA) that works on any device:</p>
                        <ul>
                            <li>Desktop computers and laptops</li>
                            <li>Tablets (iPad, Android tablets)</li>
                            <li>Smartphones (iPhone, Android)</li>
                            <li>Even works offline with limited functionality!</li>
                        </ul>
                        <p>You can add it to your home screen for app-like access.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    How do I reset my password?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>To reset your password:</p>
                        <ul>
                            <li>Go to the login page</li>
                            <li>Click "Forgot Password"</li>
                            <li>Enter your registered email</li>
                            <li>You'll receive an OTP to verify your identity</li>
                            <li>Set a new secure password</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    What is biometric login?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Verdant SMS supports passwordless login using:</p>
                        <ul>
                            <li><strong>Fingerprint:</strong> Use your phone or laptop fingerprint sensor</li>
                            <li><strong>Face ID:</strong> Use facial recognition on supported devices</li>
                            <li><strong>Windows Hello:</strong> Use Windows biometric features</li>
                        </ul>
                        <p>This is more secure and convenient than passwords!</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="faq-section">
            <h2><i class="fas fa-graduation-cap"></i> Academic Information</h2>

            <div class="faq-item">
                <button class="faq-question">
                    What grading system do you use?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>We use the standard Nigerian grading scale:</p>
                        <ul>
                            <li>A1 = 75-100 (Excellent)</li>
                            <li>B2 = 70-74, B3 = 65-69 (Very Good/Good)</li>
                            <li>C4 = 60-64, C5 = 55-59, C6 = 50-54 (Credit)</li>
                            <li>D7 = 45-49, E8 = 40-44 (Pass)</li>
                            <li>F9 = 0-39 (Fail)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    How are report cards generated?
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Report cards are automatically generated each term with:</p>
                        <ul>
                            <li>Subject scores (CA + Exam)</li>
                            <li>Grade for each subject</li>
                            <li>Position in class</li>
                            <li>Class average comparison</li>
                            <li>Teacher and principal remarks</li>
                        </ul>
                        <p>Parents can download PDF copies from the parent portal.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="cta-box">
            <h3>Still have questions?</h3>
            <p>Our support team is ready to help you with any questions you may have.</p>
            <a href="/visitor/contact.php">
                <i class="fas fa-envelope"></i> Contact Support
            </a>
        </div>
    </main>

    <script>
        // FAQ Accordion functionality
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const item = button.parentElement;
                const wasActive = item.classList.contains('active');

                // Close all other items
                document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));

                // Toggle current item
                if (!wasActive) {
                    item.classList.add('active');
                }
            });
        });
    </script>

    <?php include '../includes/theme-selector.php'; ?>
</body>

</html>