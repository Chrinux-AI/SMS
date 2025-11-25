<?php

/**
 * General Navigation Component
 * Simple navigation for general/universal pages (Settings, FAQ, Help)
 * No role-specific tabs or admin controls
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$user_name = $_SESSION['full_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'guest';
$user_initials = strtoupper(substr($user_name, 0, 2));

// Determine role-specific dashboard
$dashboard_map = [
    'admin' => '../admin/dashboard.php',
    'teacher' => '../teacher/dashboard.php',
    'student' => '../student/dashboard.php',
    'parent' => '../parent/dashboard.php'
];
$dashboard_url = $dashboard_map[$user_role] ?? '../index.php';

?>

<div class="cyber-layout">
    <!-- Sidebar -->
    ?>

    <!-- Hamburger Menu Button -->
    <button class="hamburger-btn" id="sidebarToggle" aria-label="Toggle Sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="cyber-sidebar" id="cyberSidebar">
        <!-- Sidebar Brand -->
        <div class="sidebar-brand">
            <div class="brand-orb">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h2>School Management System</h2>
            <p class="brand-subtitle">MANAGEMENT SYSTEM</p>
        </div>

        <!-- User Profile Section -->
        <div class="user-profile">
            <div class="user-avatar">
                <?php echo $user_initials; ?>
                <span class="online-indicator"></span>
            </div>
            <div class="user-info">
                <h3><?php echo htmlspecialchars($user_name); ?></h3>
                <p class="user-role">
                    <i class="fas fa-circle"></i>
                    <?php echo ucfirst($user_role); ?>
                </p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="nav-menu">
            <div class="nav-section">
                <h4 class="nav-section-title">
                    <i class="fas fa-home"></i> General
                </h4>

                <a href="<?php echo $dashboard_url; ?>" class="nav-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Back to Dashboard</span>
                </a>

                <a href="settings.php" class="nav-item <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>

                <a href="faq.php" class="nav-item <?php echo ($current_page == 'faq.php') ? 'active' : ''; ?>">
                    <i class="fas fa-question-circle"></i>
                    <span>FAQ</span>
                </a>

                <a href="help.php" class="nav-item <?php echo ($current_page == 'help.php') ? 'active' : ''; ?>">
                    <i class="fas fa-life-ring"></i>
                    <span>Help Center</span>
                </a>
            </div>

            <div class="nav-section">
                <h4 class="nav-section-title">
                    <i class="fas fa-comments"></i> Communication
                </h4>

                <a href="../messages.php" class="nav-item">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </a>

                <a href="../notices.php" class="nav-item">
                    <i class="fas fa-bullhorn"></i>
                    <span>Notices</span>
                </a>

                <a href="../<?php echo $user_role; ?>/chat.php" class="nav-item">
                    <i class="fas fa-comment-dots"></i>
                    <span>Chat</span>
                </a>
            </div>

            <div class="nav-section">
                <h4 class="nav-section-title">
                    <i class="fas fa-user"></i> Account
                </h4>

                <a href="../logout.php" class="nav-item nav-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <p>&copy; 2025 School Management System</p>
            <p class="version">v2.0</p>
        </div>
    </aside>

    <!-- Sidebar Toggle Script -->
    <script>
        (function() {
            const sidebar = document.getElementById('cyberSidebar');
            const toggle = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            if (toggle && sidebar && overlay) {
                toggle.addEventListener('click', () => {
                    const isMobile = window.innerWidth <= 1024;

                    if (isMobile) {
                        sidebar.classList.toggle('active');
                        overlay.classList.toggle('active');
                    } else {
                        sidebar.classList.toggle('hidden');
                    }
                });

                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });

                const menuItems = sidebar.querySelectorAll('.nav-item');
                menuItems.forEach(item => {
                    item.addEventListener('click', () => {
                        if (window.innerWidth <= 1024) {
                            sidebar.classList.remove('active');
                            overlay.classList.remove('active');
                        }
                    });
                });
            }
        })();
    </script>

    <!-- Main Content -->
    <main class="cyber-main" id="mainContent">