<?php

/**
 * Mobile Bottom Navigation Component
 * Touch-friendly bottom nav bar for mobile devices
 * Verdant SMS v3.0
 */

$role = $_SESSION['role'] ?? 'student';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Define mobile nav items per role
function get_mobile_nav_items($role)
{
    $items = [
        'admin' => [
            ['icon' => 'home', 'label' => 'Home', 'link' => '/admin/dashboard.php', 'page' => 'dashboard'],
            ['icon' => 'users', 'label' => 'Users', 'link' => '/admin/users.php', 'page' => 'users'],
            ['icon' => 'chart-bar', 'label' => 'Reports', 'link' => '/admin/reports.php', 'page' => 'reports'],
            ['icon' => 'bell', 'label' => 'Alerts', 'link' => '/admin/notices.php', 'page' => 'notices'],
            ['icon' => 'bars', 'label' => 'Menu', 'action' => 'toggleMobileMenu()', 'page' => 'menu'],
        ],
        'teacher' => [
            ['icon' => 'home', 'label' => 'Home', 'link' => '/teacher/dashboard.php', 'page' => 'dashboard'],
            ['icon' => 'clipboard-check', 'label' => 'Attendance', 'link' => '/teacher/attendance.php', 'page' => 'attendance'],
            ['icon' => 'edit', 'label' => 'Grades', 'link' => '/teacher/grades.php', 'page' => 'grades'],
            ['icon' => 'users', 'label' => 'Classes', 'link' => '/teacher/classes.php', 'page' => 'classes'],
            ['icon' => 'bars', 'label' => 'Menu', 'action' => 'toggleMobileMenu()', 'page' => 'menu'],
        ],
        'student' => [
            ['icon' => 'home', 'label' => 'Home', 'link' => '/student/dashboard.php', 'page' => 'dashboard'],
            ['icon' => 'book', 'label' => 'Courses', 'link' => '/student/courses.php', 'page' => 'courses'],
            ['icon' => 'chart-line', 'label' => 'Grades', 'link' => '/student/grades.php', 'page' => 'grades'],
            ['icon' => 'calendar', 'label' => 'Schedule', 'link' => '/student/timetable.php', 'page' => 'timetable'],
            ['icon' => 'bars', 'label' => 'Menu', 'action' => 'toggleMobileMenu()', 'page' => 'menu'],
        ],
        'parent' => [
            ['icon' => 'home', 'label' => 'Home', 'link' => '/parent/dashboard.php', 'page' => 'dashboard'],
            ['icon' => 'user-graduate', 'label' => 'Progress', 'link' => '/parent/progress.php', 'page' => 'progress'],
            ['icon' => 'calendar-check', 'label' => 'Attendance', 'link' => '/parent/attendance.php', 'page' => 'attendance'],
            ['icon' => 'envelope', 'label' => 'Messages', 'link' => '/messages.php', 'page' => 'messages'],
            ['icon' => 'bars', 'label' => 'Menu', 'action' => 'toggleMobileMenu()', 'page' => 'menu'],
        ],
    ];

    return $items[$role] ?? $items['student'];
}

$nav_items = get_mobile_nav_items($role);
?>

<!-- Mobile Bottom Navigation -->
<nav class="mobile-bottom-nav" id="mobileBottomNav" role="navigation" aria-label="Mobile navigation">
    <?php foreach ($nav_items as $item):
        $is_active = $current_page === $item['page'];
        $class = 'mobile-nav-item' . ($is_active ? ' active' : '');
    ?>
        <?php if (isset($item['link'])): ?>
            <a href="<?php echo APP_URL . $item['link']; ?>" class="<?php echo $class; ?>">
                <i class="fas fa-<?php echo $item['icon']; ?>"></i>
                <span><?php echo $item['label']; ?></span>
                <?php if ($is_active): ?><span class="active-indicator"></span><?php endif; ?>
            </a>
        <?php else: ?>
            <button class="<?php echo $class; ?>" onclick="<?php echo $item['action']; ?>">
                <i class="fas fa-<?php echo $item['icon']; ?>"></i>
                <span><?php echo $item['label']; ?></span>
            </button>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay">
    <div class="mobile-menu-content" id="mobileMenuContent">
        <div class="mobile-menu-header">
            <h3>Menu</h3>
            <button class="mobile-menu-close" onclick="closeMobileMenu()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mobile-menu-user">
            <div class="mobile-user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="mobile-user-info">
                <span class="mobile-user-name"><?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?></span>
                <span class="mobile-user-role"><?php echo ucfirst($role); ?></span>
            </div>
        </div>
        <div class="mobile-menu-links">
            <a href="<?php echo APP_URL; ?>/profile.php" class="mobile-menu-link">
                <i class="fas fa-user-circle"></i> My Profile
            </a>
            <a href="<?php echo APP_URL; ?>/messages.php" class="mobile-menu-link">
                <i class="fas fa-envelope"></i> Messages
            </a>
            <a href="<?php echo APP_URL; ?>/notifications.php" class="mobile-menu-link">
                <i class="fas fa-bell"></i> Notifications
            </a>
            <a href="<?php echo APP_URL; ?>/settings.php" class="mobile-menu-link">
                <i class="fas fa-cog"></i> Settings
            </a>
            <hr>
            <a href="<?php echo APP_URL; ?>/logout.php" class="mobile-menu-link danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>

<style>
    /* Mobile Bottom Navigation */
    .mobile-bottom-nav {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 64px;
        background: var(--card-bg, white);
        border-top: 1px solid var(--border-color, #e5e7eb);
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
        z-index: 1000;
        padding-bottom: env(safe-area-inset-bottom, 0);
    }

    @media (max-width: 768px) {
        .mobile-bottom-nav {
            display: flex;
            align-items: stretch;
            justify-content: space-around;
        }

        /* Add padding to main content to avoid overlap */
        body {
            padding-bottom: calc(64px + env(safe-area-inset-bottom, 0));
        }

        /* Hide desktop FAB on mobile when bottom nav is visible */
        .quick-actions-fab {
            bottom: calc(80px + env(safe-area-inset-bottom, 0));
        }
    }

    .mobile-nav-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        text-decoration: none;
        color: var(--text-muted, #9ca3af);
        background: transparent;
        border: none;
        cursor: pointer;
        font-size: 0.75rem;
        transition: all 0.2s;
        position: relative;
        padding: 8px 4px;
    }

    .mobile-nav-item i {
        font-size: 1.25rem;
        transition: transform 0.2s;
    }

    .mobile-nav-item span {
        font-weight: 500;
    }

    .mobile-nav-item:hover,
    .mobile-nav-item:focus {
        color: var(--primary-color, #10b981);
    }

    .mobile-nav-item.active {
        color: var(--primary-color, #10b981);
    }

    .mobile-nav-item.active i {
        transform: scale(1.1);
    }

    .active-indicator {
        position: absolute;
        top: 4px;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 4px;
        background: var(--primary-color, #10b981);
        border-radius: 50%;
    }

    /* Mobile Menu Overlay */
    .mobile-menu-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 2000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
    }

    .mobile-menu-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .mobile-menu-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        max-height: 80vh;
        background: var(--card-bg, white);
        border-radius: 20px 20px 0 0;
        transform: translateY(100%);
        transition: transform 0.3s cubic-bezier(0.32, 0.72, 0, 1);
        overflow: hidden;
    }

    .mobile-menu-overlay.active .mobile-menu-content {
        transform: translateY(0);
    }

    .mobile-menu-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
    }

    .mobile-menu-header h3 {
        margin: 0;
        font-size: 1.1rem;
    }

    .mobile-menu-close {
        width: 36px;
        height: 36px;
        border: none;
        background: var(--bg-secondary, #f3f4f6);
        color: var(--text-secondary, #6b7280);
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .mobile-menu-user {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
    }

    .mobile-user-avatar {
        width: 48px;
        height: 48px;
        background: var(--primary-color, #10b981);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .mobile-user-info {
        display: flex;
        flex-direction: column;
    }

    .mobile-user-name {
        font-weight: 600;
        font-size: 1rem;
    }

    .mobile-user-role {
        font-size: 0.8rem;
        color: var(--text-muted, #9ca3af);
    }

    .mobile-menu-links {
        padding: 12px 0;
        max-height: 50vh;
        overflow-y: auto;
    }

    .mobile-menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        color: var(--text-primary, #1f2937);
        text-decoration: none;
        font-size: 0.95rem;
        transition: background 0.15s;
    }

    .mobile-menu-link:hover,
    .mobile-menu-link:focus {
        background: var(--bg-hover, #f3f4f6);
    }

    .mobile-menu-link i {
        width: 24px;
        text-align: center;
        color: var(--primary-color, #10b981);
    }

    .mobile-menu-link.danger {
        color: #ef4444;
    }

    .mobile-menu-link.danger i {
        color: #ef4444;
    }

    .mobile-menu-links hr {
        border: none;
        border-top: 1px solid var(--border-color, #e5e7eb);
        margin: 8px 20px;
    }

    /* Dark theme */
    .dark-theme .mobile-bottom-nav {
        background: #1f2937;
        border-color: #374151;
    }

    .dark-theme .mobile-menu-content {
        background: #1f2937;
    }

    .dark-theme .mobile-menu-header {
        border-color: #374151;
    }

    .dark-theme .mobile-menu-close {
        background: #374151;
        color: #e5e7eb;
    }

    /* Touch feedback */
    @media (hover: none) {
        .mobile-nav-item:active {
            transform: scale(0.95);
            opacity: 0.8;
        }

        .mobile-menu-link:active {
            background: var(--bg-hover, #f3f4f6);
        }
    }

    /* Haptic feedback animation */
    @keyframes tap-feedback {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(0.95);
        }

        100% {
            transform: scale(1);
        }
    }

    .mobile-nav-item:active {
        animation: tap-feedback 0.15s ease;
    }
</style>

<script>
    // Mobile Menu Functions
    function toggleMobileMenu() {
        const overlay = document.getElementById('mobileMenuOverlay');
        overlay.classList.toggle('active');

        // Haptic feedback (if available)
        if ('vibrate' in navigator) {
            navigator.vibrate(10);
        }
    }

    function closeMobileMenu() {
        const overlay = document.getElementById('mobileMenuOverlay');
        overlay.classList.remove('active');
    }

    // Close on overlay click
    document.getElementById('mobileMenuOverlay')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeMobileMenu();
        }
    });

    // Close on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMobileMenu();
        }
    });

    // Swipe to close
    let touchStartY = 0;
    let touchCurrentY = 0;

    const menuContent = document.getElementById('mobileMenuContent');
    if (menuContent) {
        menuContent.addEventListener('touchstart', function(e) {
            touchStartY = e.touches[0].clientY;
        }, {
            passive: true
        });

        menuContent.addEventListener('touchmove', function(e) {
            touchCurrentY = e.touches[0].clientY;
            const diff = touchCurrentY - touchStartY;

            if (diff > 0) {
                this.style.transform = `translateY(${diff}px)`;
            }
        }, {
            passive: true
        });

        menuContent.addEventListener('touchend', function(e) {
            const diff = touchCurrentY - touchStartY;

            if (diff > 100) {
                closeMobileMenu();
            }

            this.style.transform = '';
            touchStartY = 0;
            touchCurrentY = 0;
        });
    }

    // Hide bottom nav on scroll down, show on scroll up
    let lastScrollTop = 0;
    const bottomNav = document.getElementById('mobileBottomNav');

    window.addEventListener('scroll', function() {
        if (!bottomNav) return;

        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // Scrolling down
            bottomNav.style.transform = 'translateY(100%)';
        } else {
            // Scrolling up
            bottomNav.style.transform = 'translateY(0)';
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    }, {
        passive: true
    });
</script>