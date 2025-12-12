<?php

/**
 * Online Users Component
 * Shows real-time online users count and list
 * Verdant SMS v3.0
 */

// Ensure user_sessions table exists for tracking
function ensure_sessions_table()
{
    try {
        db()->query("CREATE TABLE IF NOT EXISTS user_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            session_id VARCHAR(255) NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_session (session_id),
            INDEX idx_user (user_id),
            INDEX idx_activity (last_activity)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (Exception $e) {
        // Table might already exist
    }
}

// Update user's last activity
function update_user_activity($user_id)
{
    if (!$user_id) return;

    try {
        ensure_sessions_table();

        $session_id = session_id();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);

        // Upsert session
        db()->query(
            "INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, last_activity)
             VALUES (?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE last_activity = NOW(), ip_address = VALUES(ip_address)",
            [$user_id, $session_id, $ip, $ua]
        );

        // Clean up old sessions (older than 30 minutes)
        db()->query("DELETE FROM user_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
    } catch (Exception $e) {
        // Silent fail
    }
}

// Get count of online users
function get_online_users_count()
{
    try {
        ensure_sessions_table();
        return db()->fetchColumn(
            "SELECT COUNT(DISTINCT user_id) FROM user_sessions WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)"
        ) ?: 0;
    } catch (Exception $e) {
        return 0;
    }
}

// Get list of online users (for admins/staff)
function get_online_users($limit = 20)
{
    try {
        ensure_sessions_table();
        return db()->fetchAll(
            "SELECT u.id, u.first_name, u.last_name, u.email, u.role, u.profile_image,
                    us.last_activity, us.ip_address
             FROM user_sessions us
             JOIN users u ON us.user_id = u.id
             WHERE us.last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
             GROUP BY u.id
             ORDER BY us.last_activity DESC
             LIMIT ?",
            [$limit]
        );
    } catch (Exception $e) {
        return [];
    }
}

// Get online users by role
function get_online_by_role()
{
    try {
        ensure_sessions_table();
        return db()->fetchAll(
            "SELECT u.role, COUNT(DISTINCT u.id) as count
             FROM user_sessions us
             JOIN users u ON us.user_id = u.id
             WHERE us.last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
             GROUP BY u.role
             ORDER BY count DESC"
        );
    } catch (Exception $e) {
        return [];
    }
}

// Update current user's activity
if (isset($_SESSION['user_id'])) {
    update_user_activity($_SESSION['user_id']);
}

$online_count = get_online_users_count();
$user_role = $_SESSION['role'] ?? '';
$can_view_details = in_array($user_role, ['admin', 'superadmin', 'principal', 'vice-principal', 'owner']);
?>

<!-- Online Users Indicator -->
<div class="online-users-indicator" id="onlineUsersIndicator">
    <button class="online-indicator-btn" onclick="toggleOnlineUsers()" aria-label="Online Users" aria-expanded="false">
        <span class="online-pulse"></span>
        <i class="fas fa-users"></i>
        <span class="online-count" id="onlineCount"><?php echo $online_count; ?></span>
    </button>

    <?php if ($can_view_details): ?>
        <div class="online-dropdown" id="onlineDropdown">
            <div class="online-header">
                <h4><i class="fas fa-circle online-dot"></i> Online Now</h4>
                <span class="online-total"><?php echo $online_count; ?> user<?php echo $online_count !== 1 ? 's' : ''; ?></span>
            </div>

            <div class="online-by-role" id="onlineByRole">
                <?php
                $by_role = get_online_by_role();
                foreach ($by_role as $role_data):
                    $role_icons = [
                        'admin' => 'user-shield',
                        'teacher' => 'chalkboard-teacher',
                        'student' => 'user-graduate',
                        'parent' => 'user-friends',
                        'principal' => 'crown',
                        'librarian' => 'book',
                        'accountant' => 'calculator',
                        'nurse' => 'heartbeat',
                        'counselor' => 'comments',
                        'transport' => 'bus',
                        'hostel' => 'bed',
                        'superadmin' => 'user-cog'
                    ];
                    $icon = $role_icons[$role_data['role']] ?? 'user';
                ?>
                    <div class="role-online-item">
                        <i class="fas fa-<?php echo $icon; ?>"></i>
                        <span class="role-name"><?php echo ucfirst($role_data['role']); ?></span>
                        <span class="role-count"><?php echo $role_data['count']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="online-list" id="onlineList">
                <?php
                $online_users = get_online_users(10);
                foreach ($online_users as $user):
                    $initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
                    $time_ago = timeAgo($user['last_activity']);
                ?>
                    <div class="online-user-item">
                        <div class="user-avatar">
                            <?php if (!empty($user['profile_image'])): ?>
                                <img src="<?php echo APP_URL . '/uploads/profiles/' . $user['profile_image']; ?>" alt="">
                            <?php else: ?>
                                <span class="avatar-initials"><?php echo $initials; ?></span>
                            <?php endif; ?>
                            <span class="status-dot online"></span>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                            <span class="user-role"><?php echo ucfirst($user['role']); ?></span>
                        </div>
                        <span class="user-activity" title="<?php echo $user['last_activity']; ?>"><?php echo $time_ago; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="online-footer">
                <a href="<?php echo APP_URL; ?>/admin/activity-monitor.php">
                    View All Activity <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    /* Online Users Indicator Styles */
    .online-users-indicator {
        position: relative;
        display: inline-flex;
    }

    .online-indicator-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border: none;
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border-radius: 20px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s ease;
        position: relative;
    }

    .online-indicator-btn:hover {
        background: rgba(16, 185, 129, 0.25);
    }

    .online-pulse {
        position: absolute;
        left: 10px;
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        animation: pulse-online 2s infinite;
    }

    @keyframes pulse-online {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        70% {
            box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    .online-indicator-btn i {
        margin-left: 8px;
    }

    .online-count {
        background: #10b981;
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
        min-width: 24px;
        text-align: center;
    }

    .online-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 320px;
        background: var(--card-bg, white);
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border: 1px solid var(--border-color, #e5e7eb);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s ease;
        z-index: 1000;
        overflow: hidden;
    }

    .online-dropdown.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .online-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
    }

    .online-header h4 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .online-dot {
        color: #10b981;
        font-size: 0.5rem;
        animation: blink 1.5s infinite;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.4;
        }
    }

    .online-total {
        font-size: 0.8rem;
        color: var(--text-muted, #6b7280);
    }

    .online-by-role {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        background: var(--bg-secondary, #f9fafb);
    }

    .role-online-item {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        background: var(--card-bg, white);
        border-radius: 16px;
        font-size: 0.75rem;
        color: var(--text-secondary, #6b7280);
        border: 1px solid var(--border-color, #e5e7eb);
    }

    .role-online-item i {
        color: var(--primary-color, #10b981);
        font-size: 0.7rem;
    }

    .role-count {
        background: var(--primary-color, #10b981);
        color: white;
        padding: 1px 6px;
        border-radius: 8px;
        font-weight: 600;
    }

    .online-list {
        max-height: 280px;
        overflow-y: auto;
        padding: 8px;
    }

    .online-user-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px;
        border-radius: 8px;
        transition: background 0.15s;
    }

    .online-user-item:hover {
        background: var(--bg-hover, #f3f4f6);
    }

    .user-avatar {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-initials {
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .status-dot {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid var(--card-bg, white);
    }

    .status-dot.online {
        background: #10b981;
    }

    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-name {
        display: block;
        font-weight: 500;
        font-size: 0.875rem;
        color: var(--text-primary, #1f2937);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-role {
        font-size: 0.75rem;
        color: var(--text-muted, #9ca3af);
    }

    .user-activity {
        font-size: 0.7rem;
        color: var(--text-muted, #9ca3af);
        white-space: nowrap;
    }

    .online-footer {
        padding: 12px 16px;
        border-top: 1px solid var(--border-color, #e5e7eb);
        text-align: center;
    }

    .online-footer a {
        color: var(--primary-color, #10b981);
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .online-footer a:hover {
        text-decoration: underline;
    }

    /* Dark theme */
    .dark-theme .online-dropdown {
        background: #1f2937;
        border-color: #374151;
    }

    .dark-theme .online-header {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.08));
    }

    .dark-theme .online-by-role {
        background: #111827;
    }

    .dark-theme .role-online-item {
        background: #1f2937;
        border-color: #374151;
    }

    .dark-theme .online-user-item:hover {
        background: #374151;
    }

    .dark-theme .user-name {
        color: #f9fafb;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .online-dropdown {
            width: calc(100vw - 32px);
            right: -60px;
        }
    }
</style>

<script>
    // Online Users Toggle
    function toggleOnlineUsers() {
        const dropdown = document.getElementById('onlineDropdown');
        const btn = document.querySelector('.online-indicator-btn');

        if (!dropdown) return;

        const isActive = dropdown.classList.contains('active');

        if (isActive) {
            dropdown.classList.remove('active');
            btn.setAttribute('aria-expanded', 'false');
        } else {
            dropdown.classList.add('active');
            btn.setAttribute('aria-expanded', 'true');
            refreshOnlineUsers();
        }
    }

    // Refresh online users count
    function refreshOnlineUsers() {
        fetch('<?php echo APP_URL; ?>/api/online-users.php?action=count')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('onlineCount').textContent = data.count;
                }
            })
            .catch(err => console.log('Failed to refresh online count'));
    }

    // Auto-refresh every 60 seconds
    setInterval(refreshOnlineUsers, 60000);

    // Close on outside click
    document.addEventListener('click', function(e) {
        const indicator = document.getElementById('onlineUsersIndicator');
        const dropdown = document.getElementById('onlineDropdown');

        if (indicator && dropdown && !indicator.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const dropdown = document.getElementById('onlineDropdown');
            if (dropdown) dropdown.classList.remove('active');
        }
    });
</script>