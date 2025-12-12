<?php

/**
 * Notification Center Component
 * Bell icon with unread count, notification dropdown, and history
 * Verdant SMS v3.0
 */

// Get unread notification count
function get_unread_notification_count($user_id)
{
    try {
        return db()->count('notifications', 'user_id = ? AND is_read = 0', [$user_id]);
    } catch (Exception $e) {
        return 0;
    }
}

// Get recent notifications
function get_recent_notifications($user_id, $limit = 10)
{
    try {
        return db()->fetchAll(
            "SELECT * FROM notifications
             WHERE user_id = ?
             ORDER BY created_at DESC
             LIMIT ?",
            [$user_id, $limit]
        );
    } catch (Exception $e) {
        return [];
    }
}

$user_id = $_SESSION['user_id'] ?? 0;
$unread_count = get_unread_notification_count($user_id);
$notifications = get_recent_notifications($user_id, 8);
?>

<!-- Notification Center -->
<div class="notification-center" id="notificationCenter">
    <button class="notification-bell" id="notificationBell" onclick="toggleNotifications()" aria-label="Notifications" aria-expanded="false">
        <i class="fas fa-bell"></i>
        <?php if ($unread_count > 0): ?>
            <span class="notification-badge" id="notificationBadge"><?php echo $unread_count > 99 ? '99+' : $unread_count; ?></span>
        <?php endif; ?>
    </button>

    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <h3><i class="fas fa-bell"></i> Notifications</h3>
            <div class="notification-actions">
                <button class="notification-action-btn" onclick="markAllRead()" title="Mark all as read">
                    <i class="fas fa-check-double"></i>
                </button>
                <button class="notification-action-btn" onclick="openNotificationSettings()" title="Settings">
                    <i class="fas fa-cog"></i>
                </button>
            </div>
        </div>

        <div class="notification-list" id="notificationList">
            <?php if (empty($notifications)): ?>
                <div class="notification-empty">
                    <i class="fas fa-bell-slash"></i>
                    <p>No notifications yet</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notif): ?>
                    <div class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>"
                        data-id="<?php echo $notif['id']; ?>"
                        onclick="handleNotificationClick(<?php echo $notif['id']; ?>, '<?php echo htmlspecialchars($notif['link'] ?? ''); ?>')">
                        <div class="notification-icon <?php echo $notif['type'] ?? 'info'; ?>">
                            <i class="fas fa-<?php echo getNotificationIcon($notif['type'] ?? 'info'); ?>"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title"><?php echo htmlspecialchars($notif['title']); ?></div>
                            <div class="notification-message"><?php echo htmlspecialchars($notif['message']); ?></div>
                            <div class="notification-time">
                                <i class="far fa-clock"></i>
                                <?php echo timeAgo($notif['created_at']); ?>
                            </div>
                        </div>
                        <?php if (!$notif['is_read']): ?>
                            <div class="notification-unread-dot"></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="notification-footer">
            <a href="<?php echo APP_URL; ?>/notifications.php" class="notification-view-all">
                View All Notifications <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<?php
function getNotificationIcon($type)
{
    $icons = [
        'info' => 'info-circle',
        'success' => 'check-circle',
        'warning' => 'exclamation-triangle',
        'error' => 'times-circle',
        'message' => 'envelope',
        'attendance' => 'clipboard-check',
        'grade' => 'graduation-cap',
        'fee' => 'dollar-sign',
        'announcement' => 'bullhorn',
        'assignment' => 'tasks'
    ];
    return $icons[$type] ?? 'bell';
}

function timeAgo($datetime)
{
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' min' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}
?>

<style>
    .notification-center {
        position: relative;
    }

    .notification-bell {
        position: relative;
        background: transparent;
        border: none;
        color: var(--theme-text-primary, #fff);
        font-size: 1.3rem;
        cursor: pointer;
        padding: 10px;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .notification-bell:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--theme-primary, #10b981);
    }

    .notification-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        font-size: 0.65rem;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.5);
        animation: badgePulse 2s ease-in-out infinite;
    }

    @keyframes badgePulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    .notification-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 380px;
        max-height: 500px;
        background: var(--theme-bg-card, rgba(20, 20, 30, 0.98));
        border: 1px solid var(--theme-border, rgba(255, 255, 255, 0.1));
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        display: none;
        flex-direction: column;
        z-index: 10000;
        overflow: hidden;
    }

    .notification-dropdown.active {
        display: flex;
        animation: dropdownSlide 0.3s ease;
    }

    @keyframes dropdownSlide {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--theme-border, rgba(255, 255, 255, 0.1));
    }

    .notification-header h3 {
        margin: 0;
        font-size: 1rem;
        color: var(--theme-text-primary, #fff);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .notification-actions {
        display: flex;
        gap: 8px;
    }

    .notification-action-btn {
        background: transparent;
        border: none;
        color: var(--theme-text-muted, rgba(255, 255, 255, 0.6));
        cursor: pointer;
        padding: 6px;
        border-radius: 6px;
        transition: all 0.2s;
    }

    .notification-action-btn:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--theme-primary, #10b981);
    }

    .notification-list {
        flex: 1;
        overflow-y: auto;
        max-height: 360px;
    }

    .notification-empty {
        padding: 40px 20px;
        text-align: center;
        color: var(--theme-text-muted, rgba(255, 255, 255, 0.5));
    }

    .notification-empty i {
        font-size: 3rem;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .notification-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 20px;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 1px solid var(--theme-border, rgba(255, 255, 255, 0.05));
        position: relative;
    }

    .notification-item:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .notification-item.unread {
        background: rgba(16, 185, 129, 0.05);
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .notification-icon.info {
        background: rgba(59, 130, 246, 0.2);
        color: #3b82f6;
    }

    .notification-icon.success {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
    }

    .notification-icon.warning {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
    }

    .notification-icon.error {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
    }

    .notification-icon.message {
        background: rgba(139, 92, 246, 0.2);
        color: #8b5cf6;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-title {
        font-weight: 600;
        color: var(--theme-text-primary, #fff);
        margin-bottom: 4px;
        font-size: 0.9rem;
    }

    .notification-message {
        color: var(--theme-text-muted, rgba(255, 255, 255, 0.7));
        font-size: 0.85rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .notification-time {
        font-size: 0.75rem;
        color: var(--theme-text-muted, rgba(255, 255, 255, 0.5));
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .notification-unread-dot {
        width: 8px;
        height: 8px;
        background: var(--theme-primary, #10b981);
        border-radius: 50%;
        flex-shrink: 0;
    }

    .notification-footer {
        padding: 12px 20px;
        border-top: 1px solid var(--theme-border, rgba(255, 255, 255, 0.1));
        text-align: center;
    }

    .notification-view-all {
        color: var(--theme-primary, #10b981);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .notification-view-all:hover {
        color: var(--theme-primary-light, #34d399);
    }

    @media (max-width: 480px) {
        .notification-dropdown {
            width: calc(100vw - 20px);
            right: -10px;
        }
    }
</style>

<script>
    let notificationDropdownOpen = false;

    function toggleNotifications() {
        const dropdown = document.getElementById('notificationDropdown');
        const bell = document.getElementById('notificationBell');
        notificationDropdownOpen = !notificationDropdownOpen;

        if (notificationDropdownOpen) {
            dropdown.classList.add('active');
            bell.setAttribute('aria-expanded', 'true');
            loadNotifications();
        } else {
            dropdown.classList.remove('active');
            bell.setAttribute('aria-expanded', 'false');
        }
    }

    function loadNotifications() {
        fetch('<?php echo APP_URL; ?>/api/notifications.php?action=recent')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    updateNotificationBadge(data.unread_count);
                }
            })
            .catch(console.error);
    }

    function updateNotificationBadge(count) {
        let badge = document.getElementById('notificationBadge');

        if (count > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.id = 'notificationBadge';
                badge.className = 'notification-badge';
                document.getElementById('notificationBell').appendChild(badge);
            }
            badge.textContent = count > 99 ? '99+' : count;
        } else if (badge) {
            badge.remove();
        }
    }

    function handleNotificationClick(id, link) {
        // Mark as read
        fetch('<?php echo APP_URL; ?>/api/notifications.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?php echo generate_csrf_token(); ?>'
            },
            body: JSON.stringify({
                action: 'mark_read',
                id: id
            })
        }).then(() => {
            const item = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (item) item.classList.remove('unread');

            // Update badge
            const badge = document.getElementById('notificationBadge');
            if (badge) {
                const count = parseInt(badge.textContent) - 1;
                updateNotificationBadge(count);
            }
        });

        // Navigate if link provided
        if (link) {
            window.location.href = link;
        }
    }

    function markAllRead() {
        fetch('<?php echo APP_URL; ?>/api/notifications.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?php echo generate_csrf_token(); ?>'
            },
            body: JSON.stringify({
                action: 'mark_all_read'
            })
        }).then(() => {
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
            updateNotificationBadge(0);
        });
    }

    function openNotificationSettings() {
        window.location.href = '<?php echo APP_URL; ?>/settings.php#notifications';
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (notificationDropdownOpen && !e.target.closest('.notification-center')) {
            toggleNotifications();
        }
    });

    // Poll for new notifications every 30 seconds
    setInterval(loadNotifications, 30000);
</script>