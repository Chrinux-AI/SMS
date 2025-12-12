<?php

/**
 * Dashboard Widgets System
 * Customizable, draggable widget grid with role-specific widgets
 * Verdant SMS v3.0
 */

// Ensure widget preferences table exists
function ensure_widget_table()
{
    try {
        db()->query("CREATE TABLE IF NOT EXISTS user_widget_preferences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            widget_id VARCHAR(50) NOT NULL,
            position INT DEFAULT 0,
            is_visible TINYINT(1) DEFAULT 1,
            size ENUM('small', 'medium', 'large') DEFAULT 'medium',
            settings JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_widget (user_id, widget_id),
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (Exception $e) {
        // Table exists
    }
}

// Get available widgets for a role
function get_available_widgets($role)
{
    $widgets = [
        // Universal widgets
        'calendar' => [
            'id' => 'calendar',
            'title' => 'Calendar',
            'icon' => 'calendar-alt',
            'component' => 'widget-calendar',
            'roles' => ['all'],
            'default_size' => 'medium'
        ],
        'notifications' => [
            'id' => 'notifications',
            'title' => 'Recent Notifications',
            'icon' => 'bell',
            'component' => 'widget-notifications',
            'roles' => ['all'],
            'default_size' => 'small'
        ],
        'quick_links' => [
            'id' => 'quick_links',
            'title' => 'Quick Links',
            'icon' => 'link',
            'component' => 'widget-quicklinks',
            'roles' => ['all'],
            'default_size' => 'small'
        ],
        'weather' => [
            'id' => 'weather',
            'title' => 'Weather',
            'icon' => 'cloud-sun',
            'component' => 'widget-weather',
            'roles' => ['all'],
            'default_size' => 'small'
        ],

        // Admin widgets
        'user_stats' => [
            'id' => 'user_stats',
            'title' => 'User Statistics',
            'icon' => 'users',
            'component' => 'widget-user-stats',
            'roles' => ['admin', 'superadmin', 'principal', 'owner'],
            'default_size' => 'medium'
        ],
        'attendance_overview' => [
            'id' => 'attendance_overview',
            'title' => 'Attendance Overview',
            'icon' => 'clipboard-check',
            'component' => 'widget-attendance',
            'roles' => ['admin', 'principal', 'teacher', 'class-teacher'],
            'default_size' => 'large'
        ],
        'recent_registrations' => [
            'id' => 'recent_registrations',
            'title' => 'Recent Registrations',
            'icon' => 'user-plus',
            'component' => 'widget-registrations',
            'roles' => ['admin', 'superadmin'],
            'default_size' => 'medium'
        ],
        'system_health' => [
            'id' => 'system_health',
            'title' => 'System Health',
            'icon' => 'heartbeat',
            'component' => 'widget-system-health',
            'roles' => ['admin', 'superadmin', 'owner'],
            'default_size' => 'small'
        ],

        // Teacher widgets
        'my_classes' => [
            'id' => 'my_classes',
            'title' => 'My Classes Today',
            'icon' => 'chalkboard',
            'component' => 'widget-my-classes',
            'roles' => ['teacher', 'class-teacher'],
            'default_size' => 'medium'
        ],
        'pending_grades' => [
            'id' => 'pending_grades',
            'title' => 'Pending Grades',
            'icon' => 'edit',
            'component' => 'widget-pending-grades',
            'roles' => ['teacher', 'class-teacher'],
            'default_size' => 'small'
        ],
        'assignments_due' => [
            'id' => 'assignments_due',
            'title' => 'Assignments Due',
            'icon' => 'tasks',
            'component' => 'widget-assignments',
            'roles' => ['teacher', 'student'],
            'default_size' => 'medium'
        ],

        // Student widgets
        'my_grades' => [
            'id' => 'my_grades',
            'title' => 'My Grades',
            'icon' => 'chart-line',
            'component' => 'widget-my-grades',
            'roles' => ['student'],
            'default_size' => 'medium'
        ],
        'my_attendance' => [
            'id' => 'my_attendance',
            'title' => 'My Attendance',
            'icon' => 'calendar-check',
            'component' => 'widget-my-attendance',
            'roles' => ['student'],
            'default_size' => 'small'
        ],
        'upcoming_exams' => [
            'id' => 'upcoming_exams',
            'title' => 'Upcoming Exams',
            'icon' => 'file-alt',
            'component' => 'widget-exams',
            'roles' => ['student', 'teacher'],
            'default_size' => 'medium'
        ],
        'timetable_today' => [
            'id' => 'timetable_today',
            'title' => "Today's Schedule",
            'icon' => 'clock',
            'component' => 'widget-timetable',
            'roles' => ['student', 'teacher'],
            'default_size' => 'medium'
        ],

        // Parent widgets
        'child_progress' => [
            'id' => 'child_progress',
            'title' => 'Child Progress',
            'icon' => 'user-graduate',
            'component' => 'widget-child-progress',
            'roles' => ['parent'],
            'default_size' => 'large'
        ],
        'child_attendance' => [
            'id' => 'child_attendance',
            'title' => 'Child Attendance',
            'icon' => 'calendar-check',
            'component' => 'widget-child-attendance',
            'roles' => ['parent'],
            'default_size' => 'medium'
        ],
        'fee_status' => [
            'id' => 'fee_status',
            'title' => 'Fee Status',
            'icon' => 'money-bill',
            'component' => 'widget-fee-status',
            'roles' => ['parent', 'student', 'accountant'],
            'default_size' => 'small'
        ],

        // Finance widgets
        'fee_collection' => [
            'id' => 'fee_collection',
            'title' => 'Fee Collection',
            'icon' => 'chart-pie',
            'component' => 'widget-fee-collection',
            'roles' => ['accountant', 'admin', 'principal'],
            'default_size' => 'large'
        ],
        'pending_payments' => [
            'id' => 'pending_payments',
            'title' => 'Pending Payments',
            'icon' => 'exclamation-triangle',
            'component' => 'widget-pending-payments',
            'roles' => ['accountant'],
            'default_size' => 'medium'
        ],

        // Library widgets
        'books_issued' => [
            'id' => 'books_issued',
            'title' => 'Books Issued',
            'icon' => 'book',
            'component' => 'widget-books-issued',
            'roles' => ['librarian'],
            'default_size' => 'medium'
        ],
        'overdue_books' => [
            'id' => 'overdue_books',
            'title' => 'Overdue Books',
            'icon' => 'exclamation-circle',
            'component' => 'widget-overdue',
            'roles' => ['librarian'],
            'default_size' => 'medium'
        ],

        // Transport widgets
        'active_routes' => [
            'id' => 'active_routes',
            'title' => 'Active Routes',
            'icon' => 'route',
            'component' => 'widget-routes',
            'roles' => ['transport'],
            'default_size' => 'large'
        ],

        // Hostel widgets
        'room_occupancy' => [
            'id' => 'room_occupancy',
            'title' => 'Room Occupancy',
            'icon' => 'bed',
            'component' => 'widget-occupancy',
            'roles' => ['hostel'],
            'default_size' => 'medium'
        ],
    ];

    // Filter widgets for role
    $available = [];
    foreach ($widgets as $widget) {
        if (in_array('all', $widget['roles']) || in_array($role, $widget['roles'])) {
            $available[] = $widget;
        }
    }

    return $available;
}

// Get user's widget preferences
function get_user_widgets($user_id, $role)
{
    ensure_widget_table();

    // Get saved preferences
    $saved = db()->fetchAll(
        "SELECT * FROM user_widget_preferences WHERE user_id = ? ORDER BY position",
        [$user_id]
    );

    $available = get_available_widgets($role);

    if (empty($saved)) {
        // Return defaults
        $defaults = [];
        $pos = 0;
        foreach ($available as $widget) {
            $defaults[] = [
                'widget_id' => $widget['id'],
                'position' => $pos++,
                'is_visible' => 1,
                'size' => $widget['default_size'],
                'widget' => $widget
            ];
        }
        return array_slice($defaults, 0, 6); // Show first 6 by default
    }

    // Merge preferences with widget data
    $result = [];
    $available_map = array_column($available, null, 'id');

    foreach ($saved as $pref) {
        if (isset($available_map[$pref['widget_id']])) {
            $pref['widget'] = $available_map[$pref['widget_id']];
            $result[] = $pref;
        }
    }

    return $result;
}

// Save widget preferences
function save_widget_preferences($user_id, $widgets)
{
    ensure_widget_table();

    foreach ($widgets as $index => $widget) {
        db()->query(
            "INSERT INTO user_widget_preferences (user_id, widget_id, position, is_visible, size)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE position = VALUES(position), is_visible = VALUES(is_visible), size = VALUES(size)",
            [$user_id, $widget['id'], $index, $widget['visible'] ?? 1, $widget['size'] ?? 'medium']
        );
    }

    return true;
}

$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? 'student';
$user_widgets = get_user_widgets($user_id, $role);
$all_widgets = get_available_widgets($role);
?>

<!-- Dashboard Widgets Container -->
<div class="widgets-container" id="widgetsContainer">
    <div class="widgets-header">
        <h2><i class="fas fa-th-large"></i> Dashboard</h2>
        <button class="btn-customize" onclick="openWidgetCustomizer()" title="Customize Dashboard">
            <i class="fas fa-cog"></i> Customize
        </button>
    </div>

    <div class="widgets-grid" id="widgetsGrid">
        <?php foreach ($user_widgets as $widget_pref):
            if (!$widget_pref['is_visible']) continue;
            $widget = $widget_pref['widget'];
        ?>
            <div class="widget widget-<?php echo $widget_pref['size']; ?>"
                data-widget-id="<?php echo $widget['id']; ?>"
                draggable="true">
                <div class="widget-header">
                    <div class="widget-title">
                        <i class="fas fa-<?php echo $widget['icon']; ?>"></i>
                        <?php echo htmlspecialchars($widget['title']); ?>
                    </div>
                    <div class="widget-actions">
                        <button class="widget-btn" onclick="refreshWidget('<?php echo $widget['id']; ?>')" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button class="widget-btn" onclick="toggleWidgetSize('<?php echo $widget['id']; ?>')" title="Resize">
                            <i class="fas fa-expand-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="widget-content" id="widget-content-<?php echo $widget['id']; ?>">
                    <div class="widget-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        Loading...
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Widget Customizer Modal -->
<div class="widget-customizer-modal" id="widgetCustomizer">
    <div class="customizer-content">
        <div class="customizer-header">
            <h3><i class="fas fa-puzzle-piece"></i> Customize Dashboard</h3>
            <button class="customizer-close" onclick="closeWidgetCustomizer()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="customizer-body">
            <p class="customizer-hint">Drag widgets to reorder. Click to toggle visibility.</p>
            <div class="widget-list" id="widgetCustomizerList">
                <?php foreach ($all_widgets as $widget):
                    $is_active = false;
                    foreach ($user_widgets as $uw) {
                        if ($uw['widget_id'] === $widget['id'] && $uw['is_visible']) {
                            $is_active = true;
                            break;
                        }
                    }
                ?>
                    <div class="customizer-widget-item <?php echo $is_active ? 'active' : ''; ?>"
                        data-widget-id="<?php echo $widget['id']; ?>"
                        draggable="true">
                        <div class="widget-drag-handle"><i class="fas fa-grip-vertical"></i></div>
                        <div class="widget-item-icon"><i class="fas fa-<?php echo $widget['icon']; ?>"></i></div>
                        <div class="widget-item-info">
                            <span class="widget-item-title"><?php echo htmlspecialchars($widget['title']); ?></span>
                            <span class="widget-item-size"><?php echo ucfirst($widget['default_size']); ?></span>
                        </div>
                        <label class="widget-toggle">
                            <input type="checkbox" <?php echo $is_active ? 'checked' : ''; ?>
                                onchange="toggleWidget('<?php echo $widget['id']; ?>', this.checked)">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="customizer-footer">
            <button class="btn-secondary" onclick="resetWidgets()">Reset to Default</button>
            <button class="btn-primary" onclick="saveWidgetLayout()">Save Layout</button>
        </div>
    </div>
</div>

<style>
    /* Widgets Container */
    .widgets-container {
        padding: 20px;
    }

    .widgets-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .widgets-header h2 {
        margin: 0;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-customize {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border: 1px solid var(--border-color, #e5e7eb);
        background: var(--card-bg, white);
        color: var(--text-secondary, #6b7280);
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .btn-customize:hover {
        background: var(--bg-hover, #f3f4f6);
        color: var(--primary-color, #10b981);
        border-color: var(--primary-color, #10b981);
    }

    /* Widget Grid */
    .widgets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .widget {
        background: var(--card-bg, white);
        border-radius: 12px;
        border: 1px solid var(--border-color, #e5e7eb);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.2s;
        overflow: hidden;
    }

    .widget:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .widget.widget-small {
        grid-column: span 1;
    }

    .widget.widget-medium {
        grid-column: span 1;
    }

    .widget.widget-large {
        grid-column: span 2;
    }

    @media (max-width: 768px) {
        .widget.widget-large {
            grid-column: span 1;
        }
    }

    .widget.dragging {
        opacity: 0.5;
        transform: scale(1.02);
    }

    .widget-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
        background: var(--bg-secondary, #f9fafb);
    }

    .widget-title {
        font-weight: 600;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-primary, #1f2937);
    }

    .widget-title i {
        color: var(--primary-color, #10b981);
    }

    .widget-actions {
        display: flex;
        gap: 4px;
    }

    .widget-btn {
        width: 28px;
        height: 28px;
        border: none;
        background: transparent;
        color: var(--text-muted, #9ca3af);
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
    }

    .widget-btn:hover {
        background: var(--bg-hover, #e5e7eb);
        color: var(--text-primary, #374151);
    }

    .widget-content {
        padding: 16px;
        min-height: 120px;
    }

    .widget-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100px;
        color: var(--text-muted, #9ca3af);
        gap: 10px;
    }

    /* Widget Customizer Modal */
    .widget-customizer-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
    }

    .widget-customizer-modal.active {
        opacity: 1;
        visibility: visible;
    }

    .customizer-content {
        background: var(--card-bg, white);
        border-radius: 16px;
        width: 90%;
        max-width: 500px;
        max-height: 80vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transform: translateY(20px);
        transition: transform 0.3s;
    }

    .widget-customizer-modal.active .customizer-content {
        transform: translateY(0);
    }

    .customizer-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
    }

    .customizer-header h3 {
        margin: 0;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .customizer-close {
        width: 36px;
        height: 36px;
        border: none;
        background: transparent;
        color: var(--text-muted, #9ca3af);
        border-radius: 8px;
        cursor: pointer;
        font-size: 1.2rem;
    }

    .customizer-close:hover {
        background: var(--bg-hover, #f3f4f6);
        color: var(--text-primary, #374151);
    }

    .customizer-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
    }

    .customizer-hint {
        margin: 0 0 16px;
        font-size: 0.875rem;
        color: var(--text-muted, #6b7280);
    }

    .widget-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .customizer-widget-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: var(--bg-secondary, #f9fafb);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 10px;
        cursor: grab;
        transition: all 0.15s;
    }

    .customizer-widget-item:hover {
        background: var(--bg-hover, #f3f4f6);
    }

    .customizer-widget-item.active {
        border-color: var(--primary-color, #10b981);
        background: rgba(16, 185, 129, 0.05);
    }

    .widget-drag-handle {
        color: var(--text-muted, #9ca3af);
        cursor: grab;
    }

    .widget-item-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-color, #10b981);
        color: white;
        border-radius: 10px;
        font-size: 1rem;
    }

    .widget-item-info {
        flex: 1;
    }

    .widget-item-title {
        display: block;
        font-weight: 500;
        font-size: 0.9rem;
        color: var(--text-primary, #1f2937);
    }

    .widget-item-size {
        font-size: 0.75rem;
        color: var(--text-muted, #9ca3af);
    }

    .widget-toggle {
        position: relative;
        width: 44px;
        height: 24px;
    }

    .widget-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #e5e7eb;
        border-radius: 24px;
        transition: all 0.2s;
    }

    .toggle-slider::before {
        content: '';
        position: absolute;
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: all 0.2s;
    }

    .widget-toggle input:checked+.toggle-slider {
        background: var(--primary-color, #10b981);
    }

    .widget-toggle input:checked+.toggle-slider::before {
        transform: translateX(20px);
    }

    .customizer-footer {
        display: flex;
        justify-content: space-between;
        padding: 16px 20px;
        border-top: 1px solid var(--border-color, #e5e7eb);
        background: var(--bg-secondary, #f9fafb);
    }

    .customizer-footer button {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
    }

    .btn-secondary {
        background: transparent;
        border: 1px solid var(--border-color, #e5e7eb);
        color: var(--text-secondary, #6b7280);
    }

    .btn-secondary:hover {
        background: var(--bg-hover, #f3f4f6);
    }

    .btn-primary {
        background: var(--primary-color, #10b981);
        border: none;
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark, #059669);
    }

    /* Dark theme */
    .dark-theme .widget {
        background: #1f2937;
        border-color: #374151;
    }

    .dark-theme .widget-header {
        background: #111827;
        border-color: #374151;
    }

    .dark-theme .customizer-content {
        background: #1f2937;
    }

    .dark-theme .customizer-widget-item {
        background: #111827;
        border-color: #374151;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .widgets-grid {
            grid-template-columns: 1fr;
        }

        .customizer-content {
            width: 95%;
            max-height: 90vh;
        }
    }
</style>

<script>
    // Widget System JavaScript
    let widgetOrder = [];

    function openWidgetCustomizer() {
        document.getElementById('widgetCustomizer').classList.add('active');
    }

    function closeWidgetCustomizer() {
        document.getElementById('widgetCustomizer').classList.remove('active');
    }

    function toggleWidget(widgetId, visible) {
        const item = document.querySelector(`.customizer-widget-item[data-widget-id="${widgetId}"]`);
        if (item) {
            item.classList.toggle('active', visible);
        }
    }

    function resetWidgets() {
        if (confirm('Reset dashboard to default layout?')) {
            fetch('<?php echo APP_URL; ?>/api/widgets.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo generate_csrf_token(); ?>'
                    },
                    body: JSON.stringify({
                        action: 'reset'
                    })
                }).then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
        }
    }

    function saveWidgetLayout() {
        const items = document.querySelectorAll('.customizer-widget-item');
        const widgets = [];

        items.forEach((item, index) => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            widgets.push({
                id: item.dataset.widgetId,
                position: index,
                visible: checkbox.checked ? 1 : 0
            });
        });

        fetch('<?php echo APP_URL; ?>/api/widgets.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?php echo generate_csrf_token(); ?>'
                },
                body: JSON.stringify({
                    action: 'save',
                    widgets
                })
            }).then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeWidgetCustomizer();
                    location.reload();
                } else {
                    alert('Failed to save layout');
                }
            });
    }

    function refreshWidget(widgetId) {
        const content = document.getElementById(`widget-content-${widgetId}`);
        if (!content) return;

        content.innerHTML = '<div class="widget-loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';

        fetch(`<?php echo APP_URL; ?>/api/widgets.php?action=render&widget=${widgetId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.html) {
                    content.innerHTML = data.html;
                } else {
                    content.innerHTML = '<div class="widget-error">Failed to load widget</div>';
                }
            })
            .catch(() => {
                content.innerHTML = '<div class="widget-error">Failed to load widget</div>';
            });
    }

    function toggleWidgetSize(widgetId) {
        const widget = document.querySelector(`.widget[data-widget-id="${widgetId}"]`);
        if (!widget) return;

        const sizes = ['small', 'medium', 'large'];
        const current = sizes.find(s => widget.classList.contains(`widget-${s}`)) || 'medium';
        const next = sizes[(sizes.indexOf(current) + 1) % sizes.length];

        widget.classList.remove('widget-small', 'widget-medium', 'widget-large');
        widget.classList.add(`widget-${next}`);
    }

    // Initialize all widgets
    document.addEventListener('DOMContentLoaded', function() {
        const widgets = document.querySelectorAll('.widget[data-widget-id]');
        widgets.forEach(widget => {
            refreshWidget(widget.dataset.widgetId);
        });
    });

    // Drag and drop for customizer
    const list = document.getElementById('widgetCustomizerList');
    if (list) {
        let dragItem = null;

        list.addEventListener('dragstart', e => {
            dragItem = e.target.closest('.customizer-widget-item');
            if (dragItem) {
                dragItem.classList.add('dragging');
            }
        });

        list.addEventListener('dragend', e => {
            if (dragItem) {
                dragItem.classList.remove('dragging');
                dragItem = null;
            }
        });

        list.addEventListener('dragover', e => {
            e.preventDefault();
            const afterElement = getDragAfterElement(list, e.clientY);
            if (dragItem) {
                if (afterElement == null) {
                    list.appendChild(dragItem);
                } else {
                    list.insertBefore(dragItem, afterElement);
                }
            }
        });

        function getDragAfterElement(container, y) {
            const items = [...container.querySelectorAll('.customizer-widget-item:not(.dragging)')];
            return items.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return {
                        offset,
                        element: child
                    };
                }
                return closest;
            }, {
                offset: Number.NEGATIVE_INFINITY
            }).element;
        }
    }

    // Close customizer on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeWidgetCustomizer();
        }
    });
</script>