<?php

/**
 * Universal Head Meta Tags Include
 * Favicon, theme color, PWA manifest, and other meta tags
 * Include this in EVERY page for consistent branding
 */

// Ensure APP_URL is defined (preferred) or fallback to BASE_URL
if (!defined('APP_URL')) {
    if (defined('BASE_URL')) {
        define('APP_URL', BASE_URL);
    } else {
        define('APP_URL', '/attendance');
    }
}

// Ensure APP_NAME is defined
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Verdant SMS');
}
?>

<!-- Favicon Suite -->
<link rel="icon" type="image/svg+xml" href="<?= APP_URL ?>/assets/images/verdant-icon.svg">
<link rel="icon" type="image/x-icon" href="<?= APP_URL ?>/favicon.ico">
<link rel="icon" type="image/png" sizes="32x32" href="<?= APP_URL ?>/assets/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?= APP_URL ?>/assets/icons/favicon-16x16.png">
<link rel="icon" type="image/png" sizes="96x96" href="<?= APP_URL ?>/assets/icons/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="192x192" href="<?= APP_URL ?>/assets/icons/favicon-192x192.png">
<link rel="icon" type="image/png" sizes="512x512" href="<?= APP_URL ?>/assets/icons/favicon-512x512.png">

<!-- Apple Touch Icons -->
<link rel="apple-touch-icon" sizes="180x180" href="<?= APP_URL ?>/assets/icons/apple-touch-icon.png">

<!-- Android Chrome Icons -->
<link rel="icon" type="image/png" sizes="192x192" href="<?= APP_URL ?>/assets/icons/android-chrome-192x192.png">
<link rel="icon" type="image/png" sizes="512x512" href="<?= APP_URL ?>/assets/icons/android-chrome-512x512.png">

<!-- Microsoft Tiles -->
<meta name="msapplication-TileColor" content="#22C55E">
<meta name="msapplication-TileImage" content="<?= APP_URL ?>/assets/icons/mstile-150x150.png">
<meta name="msapplication-config" content="<?= APP_URL ?>/browserconfig.xml">

<!-- PWA Manifest -->
<link rel="manifest" href="<?= APP_URL ?>/manifest.json">

<!-- Theme Color -->
<meta name="theme-color" content="#22C55E">
<meta name="color-scheme" content="dark light">

<!-- Additional Meta Tags -->
<meta name="application-name" content="<?= htmlspecialchars(APP_NAME) ?>">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="<?= htmlspecialchars(APP_NAME) ?>">

<!-- Open Graph / Social Media -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?= htmlspecialchars(APP_NAME) ?>">
<meta property="og:description" content="Comprehensive School Management System">
<meta property="og:image" content="<?= APP_URL ?>/assets/icons/favicon-512x512.png">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="<?= htmlspecialchars(APP_NAME) ?>">
<meta name="twitter:description" content="Comprehensive School Management System">
<meta name="twitter:image" content="<?= APP_URL ?>/assets/icons/favicon-512x512.png">
