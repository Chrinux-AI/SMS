<?php

/**
 * Push Notifications Manager
 * Web Push notification system with subscription management
 * Verdant SMS v3.0
 */

// Ensure push subscriptions table
function ensure_push_table()
{
    try {
        db()->query("CREATE TABLE IF NOT EXISTS push_subscriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            endpoint TEXT NOT NULL,
            p256dh_key VARCHAR(255),
            auth_key VARCHAR(255),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_used TIMESTAMP NULL,
            is_active TINYINT(1) DEFAULT 1,
            INDEX idx_user (user_id),
            INDEX idx_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (Exception $e) {
        // Table exists
    }
}

// Get VAPID public key (generate if not exists)
function get_vapid_public_key()
{
    // In production, these should be stored securely in .env
    $public_key = getenv('VAPID_PUBLIC_KEY');
    if (!$public_key) {
        // Default test key - REPLACE IN PRODUCTION
        $public_key = 'BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuBkr3qBUYIHBQFLXYp5Nksh8U';
    }
    return $public_key;
}

// Save push subscription
function save_push_subscription($user_id, $subscription)
{
    ensure_push_table();

    $endpoint = $subscription['endpoint'] ?? '';
    $p256dh = $subscription['keys']['p256dh'] ?? '';
    $auth = $subscription['keys']['auth'] ?? '';
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);

    // Remove existing subscription for this endpoint
    db()->query("DELETE FROM push_subscriptions WHERE endpoint = ?", [$endpoint]);

    // Insert new subscription
    db()->insert('push_subscriptions', [
        'user_id' => $user_id,
        'endpoint' => $endpoint,
        'p256dh_key' => $p256dh,
        'auth_key' => $auth,
        'user_agent' => $ua
    ]);

    return true;
}

// Remove push subscription
function remove_push_subscription($user_id, $endpoint)
{
    ensure_push_table();
    db()->query("DELETE FROM push_subscriptions WHERE user_id = ? AND endpoint = ?", [$user_id, $endpoint]);
    return true;
}

// Get user's subscriptions
function get_user_subscriptions($user_id)
{
    ensure_push_table();
    return db()->fetchAll("SELECT * FROM push_subscriptions WHERE user_id = ? AND is_active = 1", [$user_id]);
}

// Check if user has push enabled
function has_push_enabled($user_id)
{
    ensure_push_table();
    return db()->count('push_subscriptions', 'user_id = ? AND is_active = 1', [$user_id]) > 0;
}

$user_id = $_SESSION['user_id'] ?? 0;
$push_enabled = has_push_enabled($user_id);
$vapid_key = get_vapid_public_key();
?>

<!-- Push Notification Manager Component -->
<div class="push-manager" id="pushManager">
    <button class="push-toggle-btn <?php echo $push_enabled ? 'enabled' : ''; ?>"
        id="pushToggleBtn"
        onclick="togglePushNotifications()"
        title="<?php echo $push_enabled ? 'Notifications enabled' : 'Enable notifications'; ?>">
        <i class="fas fa-<?php echo $push_enabled ? 'bell' : 'bell-slash'; ?>"></i>
        <span class="push-status"><?php echo $push_enabled ? 'On' : 'Off'; ?></span>
    </button>
</div>

<!-- Push Permission Modal -->
<div class="push-permission-modal" id="pushPermissionModal">
    <div class="push-modal-content">
        <div class="push-modal-icon">
            <i class="fas fa-bell"></i>
        </div>
        <h3>Enable Notifications</h3>
        <p>Stay updated with important announcements, messages, and alerts.</p>
        <div class="push-benefits">
            <div class="benefit-item"><i class="fas fa-check"></i> Instant alerts for new messages</div>
            <div class="benefit-item"><i class="fas fa-check"></i> Attendance reminders</div>
            <div class="benefit-item"><i class="fas fa-check"></i> Grade updates</div>
            <div class="benefit-item"><i class="fas fa-check"></i> Important announcements</div>
        </div>
        <div class="push-modal-actions">
            <button class="btn-secondary" onclick="closePushModal()">Maybe Later</button>
            <button class="btn-primary" onclick="enablePushNotifications()">
                <i class="fas fa-bell"></i> Enable Notifications
            </button>
        </div>
    </div>
</div>

<style>
    /* Push Manager Styles */
    .push-manager {
        display: inline-flex;
    }

    .push-toggle-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border: none;
        background: rgba(156, 163, 175, 0.15);
        color: #9ca3af;
        border-radius: 20px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .push-toggle-btn:hover {
        background: rgba(156, 163, 175, 0.25);
    }

    .push-toggle-btn.enabled {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .push-toggle-btn.enabled:hover {
        background: rgba(16, 185, 129, 0.25);
    }

    /* Push Permission Modal */
    .push-permission-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 3000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
    }

    .push-permission-modal.active {
        opacity: 1;
        visibility: visible;
    }

    .push-modal-content {
        background: var(--card-bg, white);
        border-radius: 20px;
        padding: 32px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        transform: scale(0.9);
        transition: transform 0.3s;
    }

    .push-permission-modal.active .push-modal-content {
        transform: scale(1);
    }

    .push-modal-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        animation: ring 2s ease-in-out infinite;
    }

    @keyframes ring {

        0%,
        100% {
            transform: rotate(0deg);
        }

        10%,
        30% {
            transform: rotate(-10deg);
        }

        20%,
        40% {
            transform: rotate(10deg);
        }

        50% {
            transform: rotate(0deg);
        }
    }

    .push-modal-content h3 {
        margin: 0 0 8px;
        font-size: 1.5rem;
    }

    .push-modal-content p {
        color: var(--text-muted, #6b7280);
        margin: 0 0 20px;
    }

    .push-benefits {
        text-align: left;
        margin-bottom: 24px;
    }

    .benefit-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        font-size: 0.9rem;
    }

    .benefit-item i {
        color: #10b981;
        width: 20px;
    }

    .push-modal-actions {
        display: flex;
        gap: 12px;
    }

    .push-modal-actions button {
        flex: 1;
        padding: 12px 16px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .push-modal-actions .btn-secondary {
        background: transparent;
        border: 1px solid var(--border-color, #e5e7eb);
        color: var(--text-secondary, #6b7280);
    }

    .push-modal-actions .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        color: white;
    }

    .push-modal-actions .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    /* Dark theme */
    .dark-theme .push-modal-content {
        background: #1f2937;
    }

    /* Responsive */
    @media (max-width: 480px) {
        .push-modal-content {
            padding: 24px;
        }

        .push-modal-actions {
            flex-direction: column;
        }
    }
</style>

<script>
    // Push Notification Manager
    const PushManager = {
        vapidKey: '<?php echo $vapid_key; ?>',
        isEnabled: <?php echo $push_enabled ? 'true' : 'false'; ?>,

        // Check if push is supported
        isSupported() {
            return 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
        },

        // Get current permission status
        getPermission() {
            return Notification.permission;
        },

        // Request permission
        async requestPermission() {
            const permission = await Notification.requestPermission();
            return permission === 'granted';
        },

        // Convert VAPID key
        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        },

        // Subscribe to push
        async subscribe() {
            try {
                const registration = await navigator.serviceWorker.ready;

                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(this.vapidKey)
                });

                // Send subscription to server
                const response = await fetch('<?php echo APP_URL; ?>/api/push-subscribe.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo generate_csrf_token(); ?>'
                    },
                    body: JSON.stringify({
                        action: 'subscribe',
                        subscription: subscription.toJSON()
                    })
                });

                const data = await response.json();
                return data.success;
            } catch (error) {
                console.error('Push subscription failed:', error);
                return false;
            }
        },

        // Unsubscribe from push
        async unsubscribe() {
            try {
                const registration = await navigator.serviceWorker.ready;
                const subscription = await registration.pushManager.getSubscription();

                if (subscription) {
                    // Notify server
                    await fetch('<?php echo APP_URL; ?>/api/push-subscribe.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': '<?php echo generate_csrf_token(); ?>'
                        },
                        body: JSON.stringify({
                            action: 'unsubscribe',
                            endpoint: subscription.endpoint
                        })
                    });

                    await subscription.unsubscribe();
                }

                return true;
            } catch (error) {
                console.error('Push unsubscribe failed:', error);
                return false;
            }
        },

        // Update UI
        updateUI(enabled) {
            const btn = document.getElementById('pushToggleBtn');
            if (!btn) return;

            const icon = btn.querySelector('i');
            const status = btn.querySelector('.push-status');

            if (enabled) {
                btn.classList.add('enabled');
                icon.className = 'fas fa-bell';
                status.textContent = 'On';
                btn.title = 'Notifications enabled';
            } else {
                btn.classList.remove('enabled');
                icon.className = 'fas fa-bell-slash';
                status.textContent = 'Off';
                btn.title = 'Enable notifications';
            }

            this.isEnabled = enabled;
        }
    };

    // Toggle push notifications
    async function togglePushNotifications() {
        if (!PushManager.isSupported()) {
            alert('Push notifications are not supported in this browser');
            return;
        }

        if (PushManager.isEnabled) {
            // Disable
            if (await PushManager.unsubscribe()) {
                PushManager.updateUI(false);
            }
        } else {
            // Check permission
            const permission = PushManager.getPermission();

            if (permission === 'denied') {
                alert('Notifications are blocked. Please enable them in your browser settings.');
                return;
            }

            if (permission === 'default') {
                // Show permission modal
                document.getElementById('pushPermissionModal').classList.add('active');
            } else {
                // Already granted, subscribe
                await enablePushNotifications();
            }
        }
    }

    // Enable push notifications
    async function enablePushNotifications() {
        closePushModal();

        const granted = await PushManager.requestPermission();
        if (!granted) {
            alert('Permission denied. You can enable notifications in browser settings.');
            return;
        }

        const subscribed = await PushManager.subscribe();
        if (subscribed) {
            PushManager.updateUI(true);

            // Show success message
            if ('Notification' in window) {
                new Notification('Notifications Enabled! 🔔', {
                    body: 'You will now receive important updates from Verdant SMS.',
                    icon: '<?php echo APP_URL; ?>/assets/images/icon-192.png'
                });
            }
        } else {
            alert('Failed to enable notifications. Please try again.');
        }
    }

    // Close push modal
    function closePushModal() {
        document.getElementById('pushPermissionModal').classList.remove('active');
    }

    // Check on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (PushManager.isSupported()) {
            // Check current subscription status
            navigator.serviceWorker.ready.then(async (registration) => {
                const subscription = await registration.pushManager.getSubscription();
                PushManager.updateUI(!!subscription);
            });
        } else {
            // Hide push button if not supported
            const manager = document.getElementById('pushManager');
            if (manager) manager.style.display = 'none';
        }
    });

    // Handle push modal close on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePushModal();
        }
    });
</script>