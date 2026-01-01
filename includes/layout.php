<?php

/**
 * Universal Layout Template
 * Standardized page structure for all pages
 * Ensures consistency across all roles
 */

if (!isset($page_title)) {
    $page_title = 'Page';
}

if (!isset($page_icon)) {
    $page_icon = 'home';
}

// Get user info
$user_id = $_SESSION['user_id'] ?? 0;
$user_name = $_SESSION['full_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'guest';
$user_initials = strtoupper(substr($user_name, 0, 2));

// Determine if we're in a role folder
$current_dir = dirname($_SERVER['PHP_SELF']);
$is_role_folder = preg_match('/\/(admin|student|teacher|parent|principal|vice-principal|accountant|librarian|transport|hostel|canteen|nurse|counselor|admin-officer|class-teacher|subject-coordinator|alumni|general|superadmin|owner)\//', $current_dir);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/head-meta.php'; ?>
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo APP_NAME; ?></title>
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/apple-touch-icon.png">
    <link rel="manifest" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>manifest.json">
    <meta name="msapplication-TileColor" content="#00BFFF">
    <meta name="msapplication-TileImage" content="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/mstile-150x150.png">
    <meta name="theme-color" content="#0a0a0f">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Cyberpunk UI Framework -->
    <link href="<?php echo $is_role_folder ? '../' : ''; ?>assets/css/cyberpunk-ui.css" rel="stylesheet">
    
    <!-- Dynamic Theme CSS -->
    <?php 
    require_once __DIR__ . '/theme-loader.php';
    output_theme_css();
    ?>
</head>
<body class="cyber-bg <?php echo get_theme_body_class(); ?>">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>

    <div class="cyber-layout">
        <?php 
        // Include navigation based on role
        if ($is_role_folder) {
            include __DIR__ . '/cyber-nav.php';
        } else {
            include __DIR__ . '/cyber-nav.php';
        }
        ?>

        <!-- Main Content -->
        <main class="cyber-main">
            <!-- Header -->
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb">
                        <i class="fas fa-<?php echo $page_icon; ?>"></i>
                    </div>
                    <h1 class="page-title"><?php echo htmlspecialchars($page_title); ?></h1>
                </div>

                <div class="header-actions">
                    <!-- User Info -->
                    <div class="user-card" style="padding: 8px 15px; margin: 0;">
                        <div class="user-avatar" style="width: 35px; height: 35px; font-size: 0.9rem;">
                            <?php echo $user_initials; ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name" style="font-size: 0.85rem;"><?php echo htmlspecialchars($user_name); ?></div>
                            <div class="user-role"><?php echo ucfirst($user_role); ?></div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="cyber-content slide-in">
                <?php 
                // Page content will be included here
                if (isset($page_content)) {
                    echo $page_content;
                }
                ?>
            </div>
        </main>
    </div>

    <?php include __DIR__ . '/chatbot-unified.php'; ?>
    
    <script src="<?php echo $is_role_folder ? '../' : ''; ?>assets/js/main.js"></script>
    <script src="<?php echo $is_role_folder ? '../' : ''; ?>assets/js/pwa-manager.js"></script>
</body>
</html>


