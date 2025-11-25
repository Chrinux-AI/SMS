<?php

/**
 * Admin Navigation Component
 * Include this file in all admin pages for consistent navigation
 */
?>

<nav class="nav-menu">
    <a href="dashboard.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-chart-line"></i> Dashboard
    </a>
    <a href="enhanced-analytics.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'enhanced-analytics.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-brain"></i> AI Analytics
    </a>
    <a href="timetable.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'timetable.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-calendar-alt"></i> Timetable
    </a>
    <a href="communication.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'communication.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-comments"></i> Communication
    </a>
    <a href="facilities.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'facilities.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-tools"></i> Facilities
    </a>
    <a href="announcements.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'announcements.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-bullhorn"></i> Announcements
    </a>
    <a href="system-monitor.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'system-monitor.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-shield-alt"></i> System Monitor
    </a>
    <a href="realtime-sync.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'realtime-sync.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-sync"></i> Real-time Sync
    </a>
    <a href="cloud-storage.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'cloud-storage.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-cloud"></i> Cloud Storage
    </a>
    <a href="mobile-api.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'mobile-api.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-mobile-alt"></i> Mobile API
    </a>
    <a href="students.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'students.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-user-graduate"></i> Students
    </a>
    <a href="teachers.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'teachers.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-chalkboard-teacher"></i> Teachers
    </a>
    <a href="classes.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'classes.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-book"></i> Classes
    </a>
    <a href="attendance.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'attendance.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-clipboard-check"></i> Attendance
    </a>
    <a href="reports.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-chart-bar"></i> Reports
    </a>
    <a href="users.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-users"></i> Users
    </a>
    <a href="registrations.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'registrations.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-user-check"></i> Registrations
    </a>
    <a href="id-management.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'id-management.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-id-card"></i> ID Management
    </a>
    <a href="advanced-admin.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'advanced-admin.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-rocket"></i> Advanced Admin
    </a>
    <a href="system-management.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'system-management.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-database"></i> System Management
    </a>
    <a href="settings.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'class="active"' : ''; ?>>
        <i class="fas fa-cog"></i> Settings
    </a>
</nav>