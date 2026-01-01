<?php
/**
 * Verdant SMS - Pricing Page with Flutterwave Integration
 */
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = "Pricing - Verdant SMS";
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
        :root { --primary: #00D4FF; --success: #00FF87; --warning: #FFB800; --purple: #A855F7; --bg-dark: #0A0E17; --bg-card: #111827; --border: rgba(255,255,255,0.08); --text: #F3F4F6; --text-muted: #9CA3AF; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-dark); color: var(--text); }
        .navbar { padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
        .navbar-brand { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
        .navbar-logo { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, var(--success), var(--primary)); display: flex; align-items: center; justify-content: center; font-weight: 800; color: #000; }
        .navbar-title { font-size: 1.1rem; font-weight: 700; background: linear-gradient(90deg, var(--success), var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero { padding: 6rem 2rem 4rem; text-align: center; }
        .hero h1 { font-size: 2.5rem; margin-bottom: 1rem; }
        .hero p { color: var(--text-muted); max-width: 600px; margin: 0 auto; }
        .pricing-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .pricing-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 2.5rem 2rem; text-align: center; position: relative; transition: all 0.3s; }
        .pricing-card:hover { transform: translateY(-5px); }
        .pricing-card.popular { border-color: var(--success); box-shadow: 0 0 50px rgba(0,255,135,0.15); }
        .pricing-card.popular::before { content: 'MOST POPULAR'; position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--success); color: #000; padding: 0.35rem 1.25rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700; letter-spacing: 1px; }
        .plan-name { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; }
        .plan-desc { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem; }
        .plan-price { font-size: 3.5rem; font-weight: 800; margin-bottom: 0.25rem; }
        .plan-price span { font-size: 1rem; font-weight: 400; color: var(--text-muted); }
        .pricing-card.popular .plan-price { color: var(--success); }
        .plan-period { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 2rem; }
        .plan-features { list-style: none; text-align: left; margin-bottom: 2rem; }
        .plan-features li { padding: 0.6rem 0; font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem; border-bottom: 1px solid var(--border); }
        .plan-features li:last-child { border-bottom: none; }
        .plan-features li i { color: var(--success); font-size: 0.85rem; }
        .plan-icon { width: 60px; height: 60px; border-radius: 50%; background: rgba(0,212,255,0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; color: var(--primary); }
        .pricing-card.popular .plan-icon { background: rgba(0,255,135,0.15); color: var(--success); }
        .btn { display: block; padding: 1rem 1.5rem; border-radius: 12px; font-size: 0.95rem; font-weight: 600; text-decoration: none; text-align: center; transition: all 0.3s; border: none; cursor: pointer; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .btn-primary { background: linear-gradient(135deg, var(--success), var(--primary)); color: #000; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0,212,255,0.3); }
        .faq { max-width: 800px; margin: 4rem auto; padding: 2rem; }
        .faq h2 { text-align: center; margin-bottom: 2rem; }
        .faq-item { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 1rem; overflow: hidden; }
        .faq-q { padding: 1.25rem; font-weight: 500; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
        .faq-q:hover { background: rgba(255,255,255,0.02); }
        .faq-a { padding: 0 1.25rem 1.25rem; color: var(--text-muted); font-size: 0.9rem; display: none; }
        .faq-item.open .faq-a { display: block; }
        .guarantee { text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, rgba(0,255,135,0.1), rgba(0,212,255,0.1)); }
        .guarantee h2 { margin-bottom: 1rem; }
        .guarantee p { color: var(--text-muted); max-width: 500px; margin: 0 auto; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="navbar-brand">
            <div class="navbar-logo"><i class="fas fa-leaf"></i></div>
            <span class="navbar-title">Verdant SMS</span>
        </a>
        <a href="../login.php" style="color: var(--text-muted); text-decoration: none;">Login</a>
    </nav>

    <section class="hero">
        <h1>Simple, Transparent Pricing</h1>
        <p>Start free. Upgrade when you're ready. All prices in Nigerian Naira (₦).</p>
    </section>

    <div class="pricing-grid">
        <div class="pricing-card">
            <div class="plan-icon"><i class="fas fa-seedling"></i></div>
            <div class="plan-name">Starter</div>
            <div class="plan-desc">For small schools getting started</div>
            <div class="plan-price">₦5,000 <span>/year</span></div>
            <div class="plan-period">Limited features</div>
            <ul class="plan-features">
                <li><i class="fas fa-check"></i> Up to 50 students</li>
                <li><i class="fas fa-check"></i> Basic attendance & fees</li>
                <li><i class="fas fa-check"></i> 3 teacher accounts</li>
                <li><i class="fas fa-check"></i> Basic AI chatbot (10 queries/day)</li>
                <li><i class="fas fa-times" style="color: #FF4757;"></i> No cloud hosting</li>
                <li><i class="fas fa-times" style="color: #FF4757;"></i> No SMS notifications</li>
            </ul>
            <a href="register-school.php" class="btn btn-outline"><i class="fas fa-rocket"></i> Start Now</a>
        </div>

        <div class="pricing-card popular">
            <div class="plan-icon"><i class="fas fa-leaf"></i></div>
            <div class="plan-name">Basic Cloud</div>
            <div class="plan-desc">For growing schools</div>
            <div class="plan-price">₦50,000 <span>/year</span></div>
            <div class="plan-period">Most popular choice</div>
            <ul class="plan-features">
                <li><i class="fas fa-check"></i> Up to 300 students</li>
                <li><i class="fas fa-check"></i> Full attendance & fee management</li>
                <li><i class="fas fa-check"></i> 10 staff accounts</li>
                <li><i class="fas fa-check"></i> AI chatbot (50 queries/day)</li>
                <li><i class="fas fa-check"></i> Cloud hosting + SSL</li>
                <li><i class="fas fa-check"></i> Daily backups</li>
                <li><i class="fas fa-times" style="color: #FF4757;"></i> No AI lesson planner</li>
            </ul>
            <button class="btn btn-primary" onclick="payWithFlutterwave('basic', 50000)">
                <i class="fas fa-credit-card"></i> Pay with Flutterwave
            </button>
        </div>

        <div class="pricing-card">
            <div class="plan-icon"><i class="fas fa-tree"></i></div>
            <div class="plan-name">Pro Cloud</div>
            <div class="plan-desc">For established schools</div>
            <div class="plan-price">₦150,000 <span>/year</span></div>
            <div class="plan-period">Full AI features</div>
            <ul class="plan-features">
                <li><i class="fas fa-check"></i> Up to 1,000 students</li>
                <li><i class="fas fa-check"></i> Unlimited staff accounts</li>
                <li><i class="fas fa-check"></i> <strong>Unlimited AI queries</strong></li>
                <li><i class="fas fa-check"></i> <strong>AI Lesson Planner (NERDC)</strong></li>
                <li><i class="fas fa-check"></i> Custom subdomain</li>
                <li><i class="fas fa-check"></i> SMS notifications (500/month)</li>
                <li><i class="fas fa-check"></i> Priority support</li>
                <li><i class="fas fa-check"></i> Advanced analytics</li>
            </ul>
            <button class="btn btn-outline" onclick="payWithFlutterwave('pro', 150000)">
                <i class="fas fa-crown"></i> Upgrade to Pro
            </button>
        </div>

        <div class="pricing-card">
            <div class="plan-icon"><i class="fas fa-building"></i></div>
            <div class="plan-name">Enterprise</div>
            <div class="plan-desc">For large institutions</div>
            <div class="plan-price">Custom</div>
            <div class="plan-period">Contact for quote</div>
            <ul class="plan-features">
                <li><i class="fas fa-check"></i> <strong>Unlimited students</strong></li>
                <li><i class="fas fa-check"></i> Unlimited everything</li>
                <li><i class="fas fa-check"></i> Dedicated server</li>
                <li><i class="fas fa-check"></i> Custom AI training</li>
                <li><i class="fas fa-check"></i> Unlimited SMS</li>
                <li><i class="fas fa-check"></i> On-site training</li>
                <li><i class="fas fa-check"></i> 24/7 phone support</li>
                <li><i class="fas fa-check"></i> SLA guarantee</li>
            </ul>
            <a href="contact.php" class="btn btn-outline"><i class="fas fa-phone"></i> Contact Sales</a>
        </div>
    </div>

    <section class="faq">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-item">
            <div class="faq-q">Is the Free plan really free forever? <i class="fas fa-chevron-down"></i></div>
            <div class="faq-a">Yes! Verdant SMS core is open source and free forever. You can host it yourself with unlimited students.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q">What payment methods do you accept? <i class="fas fa-chevron-down"></i></div>
            <div class="faq-a">We accept payments via Flutterwave: card (Visa/Mastercard), bank transfer, and USSD. All in Nigerian Naira.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q">Can I upgrade or downgrade my plan? <i class="fas fa-chevron-down"></i></div>
            <div class="faq-a">Yes, you can upgrade anytime. Downgrades take effect at the end of your billing period.</div>
        </div>
        <div class="faq-item">
            <div class="faq-q">Is my data secure? <i class="fas fa-chevron-down"></i></div>
            <div class="faq-a">Absolutely. Each school is isolated in our multi-tenant architecture. Data is encrypted and backed up daily.</div>
        </div>
    </section>

    <section class="guarantee">
        <h2>🛡️ 30-Day Money Back Guarantee</h2>
        <p>Not satisfied? Get a full refund within 30 days. No questions asked.</p>
    </section>

    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <script>
        // FAQ Toggle
        document.querySelectorAll('.faq-q').forEach(q => {
            q.addEventListener('click', () => {
                q.parentElement.classList.toggle('open');
            });
        });

        // Flutterwave Payment
        function payWithFlutterwave(plan, amount) {
            FlutterwaveCheckout({
                public_key: "FLWPUBK_TEST-XXXXX", // Replace with your Flutterwave public key
                tx_ref: "verdant_" + Date.now(),
                amount: amount,
                currency: "NGN",
                payment_options: "card, banktransfer, ussd",
                customer: {
                    email: prompt("Enter your school email:") || "school@example.com",
                    name: prompt("Enter school name:") || "My School",
                },
                customizations: {
                    title: "Verdant SMS",
                    description: plan.charAt(0).toUpperCase() + plan.slice(1) + " Cloud Plan",
                    logo: "https://verdantsms.com/assets/logo.png",
                },
                callback: function(data) {
                    // Send to webhook for verification
                    window.location.href = "../payment/verify.php?tx_ref=" + data.tx_ref;
                },
                onclose: function() {
                    console.log("Payment cancelled");
                }
            });
        }
    </script>
</body>
</html>
