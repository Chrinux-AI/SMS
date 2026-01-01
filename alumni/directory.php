<?php

/**
 * Alumni Directory
 * Browse and connect with fellow alumni
 * Verdant SMS v3.0
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'alumni') {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Alumni Directory';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Verdant SMS</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cyberpunk-ui.css">
</head>

<body class="cyber-bg">
    <?php include '../includes/cyber-nav.php'; ?>

    <main class="cyber-main">
        <div class="cyber-container">
            <div class="page-header">
                <h1><i class="fas fa-address-book"></i> <?php echo $page_title; ?></h1>
                <p class="text-muted">Connect with fellow alumni from your school</p>
            </div>

            <!-- Search & Filters -->
            <div class="cyber-card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <form class="search-form">
                        <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <input type="text" name="search" class="cyber-input" placeholder="Search by name...">
                            <select name="graduation_year" class="cyber-input">
                                <option value="">All Graduation Years</option>
                                <?php for ($y = date('Y'); $y >= 2000; $y--): ?>
                                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                            <select name="profession" class="cyber-input">
                                <option value="">All Professions</option>
                                <option value="engineering">Engineering</option>
                                <option value="medicine">Medicine</option>
                                <option value="law">Law</option>
                                <option value="business">Business</option>
                                <option value="education">Education</option>
                                <option value="tech">Technology</option>
                                <option value="other">Other</option>
                            </select>
                            <button type="submit" class="cyber-btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alumni Grid -->
            <div class="alumni-grid">
                <div class="alumni-card">
                    <div class="alumni-avatar">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3>Sample Alumni</h3>
                    <p class="graduation-year">Class of 2020</p>
                    <p class="profession">Software Engineer</p>
                    <p class="location"><i class="fas fa-map-marker-alt"></i> Lagos, Nigeria</p>
                    <div class="alumni-actions">
                        <button class="cyber-btn btn-sm"><i class="fas fa-envelope"></i></button>
                        <button class="cyber-btn btn-sm"><i class="fab fa-linkedin"></i></button>
                    </div>
                </div>

                <!-- Placeholder message -->
                <div class="cyber-card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                    <i class="fas fa-users fa-3x" style="color: var(--cyber-cyan);"></i>
                    <h3 style="margin-top: 1rem;">Directory Coming Soon</h3>
                    <p class="text-muted">We're building the alumni network. Check back soon!</p>
                </div>
            </div>
        </div>
    </main>

    <style>
        .alumni-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .alumni-card {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid var(--cyber-cyan);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }

        .alumni-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #FFD700, #FF9F43);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: #000;
        }

        .alumni-card h3 {
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .graduation-year {
            color: var(--cyber-cyan);
            font-size: 0.9rem;
        }

        .profession {
            color: #888;
            margin: 0.5rem 0;
        }

        .location {
            color: #666;
            font-size: 0.85rem;
        }

        .alumni-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
    </style>
</body>

</html>