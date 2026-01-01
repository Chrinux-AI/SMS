<?php
/**
 * Verdant SMS - Payment Configuration
 * Flutterwave Integration for Naira Payments
 */

return [
    // Flutterwave Configuration
    'flutterwave' => [
        'public_key' => getenv('FLUTTERWAVE_PUBLIC_KEY') ?: 'FLWPUBK_TEST-xxxxxxxxxxxxxx',
        'secret_key' => getenv('FLUTTERWAVE_SECRET_KEY') ?: 'FLWSECK_TEST-xxxxxxxxxxxxxx',
        'encryption_key' => getenv('FLUTTERWAVE_ENCRYPTION_KEY') ?: 'FLWSECK_TESTxxxxxxxxxxxxxx',
        'environment' => getenv('FLUTTERWAVE_ENV') ?: 'sandbox', // 'sandbox' or 'live'
        'webhook_secret' => getenv('FLUTTERWAVE_WEBHOOK_SECRET') ?: '',
    ],

    // Pricing Plans in Nigerian Naira (₦)
    'plans' => [
        'free' => [
            'name' => 'Free',
            'price' => 0,
            'billing' => 'forever',
            'features' => [
                'Unlimited students',
                'All core features',
                'Self-hosted',
                'Community support',
            ],
        ],
        'basic' => [
            'name' => 'Basic Cloud',
            'price' => 50000, // ₦50,000/year
            'billing' => 'yearly',
            'features' => [
                'Everything in Free',
                'Cloud hosting',
                'Daily backups',
                'Email support',
                'SSL certificate',
            ],
        ],
        'pro' => [
            'name' => 'Pro Cloud',
            'price' => 150000, // ₦150,000/year
            'billing' => 'yearly',
            'features' => [
                'Everything in Basic',
                'AI Lesson Planner',
                'Custom subdomain',
                'Priority support',
                'Advanced analytics',
                'Biometric integration',
            ],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price' => null, // Custom pricing
            'billing' => 'custom',
            'features' => [
                'Everything in Pro',
                'Dedicated server',
                'Custom features',
                'On-site training',
                'SLA guarantee',
                'White-label option',
            ],
        ],
    ],

    // Currency settings
    'currency' => [
        'code' => 'NGN',
        'symbol' => '₦',
        'name' => 'Nigerian Naira',
    ],

    // Webhook URL (update for production)
    'webhook_url' => '/payment/webhook.php',

    // Payment redirect URLs
    'success_url' => '/payment/success.php',
    'cancel_url' => '/payment/cancel.php',
];
