<?php

/**
 * Study Groups Component
 * Create and manage study groups with peer collaboration
 * Verdant SMS v3.0
 */

if (!isset($_SESSION['user_id'])) return;

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'student';

// Get user's study groups
$my_groups = getMyStudyGroups($user_id);
$available_groups = getAvailableGroups($user_id);

function getMyStudyGroups($user_id)
{
    try {
        return db()->fetchAll(
            "SELECT sg.*,
                    (SELECT COUNT(*) FROM study_group_members WHERE group_id = sg.id) as member_count,
                    sgm.role as member_role
             FROM study_groups sg
             JOIN study_group_members sgm ON sg.id = sgm.group_id
             WHERE sgm.user_id = ?
             ORDER BY sg.updated_at DESC
             LIMIT 5",
            [$user_id]
        );
    } catch (Exception $e) {
        return [];
    }
}

function getAvailableGroups($user_id)
{
    try {
        return db()->fetchAll(
            "SELECT sg.*,
                    (SELECT COUNT(*) FROM study_group_members WHERE group_id = sg.id) as member_count,
                    u.first_name as creator_name
             FROM study_groups sg
             LEFT JOIN users u ON sg.created_by = u.id
             WHERE sg.is_public = 1
             AND sg.id NOT IN (SELECT group_id FROM study_group_members WHERE user_id = ?)
             ORDER BY member_count DESC
             LIMIT 5",
            [$user_id]
        );
    } catch (Exception $e) {
        return [];
    }
}
?>

<!-- Study Groups Styles -->
<style>
    .study-groups-widget {
        background: rgba(10, 10, 10, 0.95);
        border: 1px solid rgba(136, 0, 255, 0.3);
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
    }

    .study-groups-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .study-groups-title {
        color: #8800ff;
        font-size: 1.3rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .create-group-btn {
        background: linear-gradient(135deg, #8800ff, #00d4ff);
        border: none;
        color: #fff;
        padding: 10px 20px;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
    }

    .create-group-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(136, 0, 255, 0.4);
    }

    .groups-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 10px;
    }

    .group-tab {
        background: transparent;
        border: none;
        color: #888;
        padding: 10px 20px;
        cursor: pointer;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .group-tab.active {
        color: #8800ff;
    }

    .group-tab.active::after {
        content: '';
        position: absolute;
        bottom: -11px;
        left: 0;
        right: 0;
        height: 2px;
        background: #8800ff;
    }

    .groups-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 15px;
    }

    .group-card {
        background: rgba(20, 20, 20, 0.9);
        border: 1px solid rgba(136, 0, 255, 0.2);
        border-radius: 12px;
        padding: 16px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .group-card:hover {
        border-color: rgba(136, 0, 255, 0.5);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(136, 0, 255, 0.2);
    }

    .group-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .group-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(136, 0, 255, 0.3), rgba(0, 212, 255, 0.3));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .group-status {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .group-status.active {
        background: rgba(0, 255, 136, 0.2);
        color: #00ff88;
    }

    .group-status.upcoming {
        background: rgba(255, 204, 0, 0.2);
        color: #ffcc00;
    }

    .group-name {
        color: #fff;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .group-subject {
        color: #888;
        font-size: 0.9rem;
        margin-bottom: 12px;
    }

    .group-meta {
        display: flex;
        gap: 15px;
        font-size: 0.85rem;
        color: #666;
    }

    .group-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .group-members {
        display: flex;
        align-items: center;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .member-avatars {
        display: flex;
        margin-right: 10px;
    }

    .member-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8800ff, #00d4ff);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        color: #fff;
        border: 2px solid rgba(20, 20, 20, 0.9);
        margin-left: -8px;
    }

    .member-avatar:first-child {
        margin-left: 0;
    }

    .member-count-text {
        color: #888;
        font-size: 0.85rem;
    }

    .join-group-btn {
        background: rgba(136, 0, 255, 0.2);
        border: 1px solid rgba(136, 0, 255, 0.4);
        color: #8800ff;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.85rem;
        margin-left: auto;
        transition: all 0.3s ease;
    }

    .join-group-btn:hover {
        background: rgba(136, 0, 255, 0.3);
    }

    /* Create Group Modal */
    .create-group-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .create-group-modal.active {
        opacity: 1;
        visibility: visible;
    }

    .group-modal-content {
        background: rgba(20, 20, 20, 0.98);
        border: 1px solid rgba(136, 0, 255, 0.3);
        border-radius: 16px;
        width: 90%;
        max-width: 500px;
        max-height: 80vh;
        overflow-y: auto;
        padding: 24px;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }

    .create-group-modal.active .group-modal-content {
        transform: translateY(0);
    }

    .group-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(136, 0, 255, 0.2);
    }

    .group-modal-title {
        color: #8800ff;
        font-size: 1.3rem;
        font-weight: 600;
    }

    .group-modal-close {
        background: none;
        border: none;
        color: #888;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .group-form-group {
        margin-bottom: 20px;
    }

    .group-form-label {
        color: #aaa;
        font-size: 0.9rem;
        margin-bottom: 8px;
        display: block;
    }

    .group-form-input {
        width: 100%;
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(136, 0, 255, 0.3);
        border-radius: 8px;
        padding: 12px 16px;
        color: #fff;
        font-size: 1rem;
    }

    .group-form-input:focus {
        outline: none;
        border-color: #8800ff;
    }

    .group-emoji-picker {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .emoji-option {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 2px solid transparent;
        background: rgba(255, 255, 255, 0.05);
        cursor: pointer;
        font-size: 1.3rem;
        transition: all 0.3s ease;
    }

    .emoji-option:hover,
    .emoji-option.selected {
        border-color: #8800ff;
        background: rgba(136, 0, 255, 0.2);
    }

    .group-submit-btn {
        background: linear-gradient(135deg, #8800ff, #00d4ff);
        border: none;
        color: #fff;
        padding: 14px 28px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        width: 100%;
        transition: all 0.3s ease;
    }

    .group-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(136, 0, 255, 0.4);
    }

    .no-groups-message {
        text-align: center;
        padding: 40px 20px;
        color: #888;
        grid-column: 1 / -1;
    }

    .no-groups-message i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: rgba(136, 0, 255, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .study-groups-widget {
            padding: 15px;
        }

        .study-groups-header {
            flex-direction: column;
            gap: 10px;
        }

        .groups-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Study Groups HTML -->
<div class="study-groups-widget" id="studyGroupsWidget">
    <div class="study-groups-header">
        <h3 class="study-groups-title">
            <i class="fas fa-users"></i>
            Study Groups
        </h3>
        <button class="create-group-btn" onclick="StudyGroups.showCreateModal()">
            <i class="fas fa-plus"></i>
            Create Group
        </button>
    </div>

    <div class="groups-tabs">
        <button class="group-tab active" data-tab="my-groups">My Groups</button>
        <button class="group-tab" data-tab="discover">Discover</button>
        <button class="group-tab" data-tab="upcoming">Upcoming Sessions</button>
    </div>

    <div class="groups-container" id="myGroupsContainer">
        <?php if (empty($my_groups)): ?>
            <div class="no-groups-message">
                <i class="fas fa-user-friends"></i>
                <p>You haven't joined any study groups yet</p>
            </div>
        <?php else: ?>
            <?php foreach ($my_groups as $group): ?>
                <div class="group-card" onclick="StudyGroups.openGroup(<?= $group['id'] ?>)">
                    <div class="group-card-header">
                        <div class="group-icon"><?= $group['emoji'] ?? '📚' ?></div>
                        <span class="group-status active">Active</span>
                    </div>
                    <div class="group-name"><?= htmlspecialchars($group['name']) ?></div>
                    <div class="group-subject"><?= htmlspecialchars($group['subject'] ?? 'General') ?></div>
                    <div class="group-meta">
                        <span><i class="fas fa-users"></i> <?= $group['member_count'] ?> members</span>
                        <span><i class="fas fa-clock"></i> Active today</span>
                    </div>
                    <div class="group-members">
                        <div class="member-avatars">
                            <div class="member-avatar">A</div>
                            <div class="member-avatar">B</div>
                            <div class="member-avatar">C</div>
                        </div>
                        <span class="member-count-text">+<?= max(0, $group['member_count'] - 3) ?> more</span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="groups-container" id="discoverContainer" style="display: none;">
        <?php if (empty($available_groups)): ?>
            <div class="no-groups-message">
                <i class="fas fa-search"></i>
                <p>No public groups available right now</p>
            </div>
        <?php else: ?>
            <?php foreach ($available_groups as $group): ?>
                <div class="group-card">
                    <div class="group-card-header">
                        <div class="group-icon"><?= $group['emoji'] ?? '📖' ?></div>
                        <span class="group-status active">Open</span>
                    </div>
                    <div class="group-name"><?= htmlspecialchars($group['name']) ?></div>
                    <div class="group-subject"><?= htmlspecialchars($group['subject'] ?? 'General') ?></div>
                    <div class="group-meta">
                        <span><i class="fas fa-users"></i> <?= $group['member_count'] ?> members</span>
                        <span><i class="fas fa-user"></i> by <?= htmlspecialchars($group['creator_name'] ?? 'Unknown') ?></span>
                    </div>
                    <div class="group-members">
                        <button class="join-group-btn" onclick="event.stopPropagation(); StudyGroups.joinGroup(<?= $group['id'] ?>)">
                            <i class="fas fa-plus"></i> Join Group
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="groups-container" id="upcomingContainer" style="display: none;">
        <div class="no-groups-message">
            <i class="fas fa-calendar-alt"></i>
            <p>No upcoming sessions scheduled</p>
        </div>
    </div>
</div>

<!-- Create Group Modal -->
<div class="create-group-modal" id="createGroupModal">
    <div class="group-modal-content">
        <div class="group-modal-header">
            <h3 class="group-modal-title">Create Study Group</h3>
            <button class="group-modal-close" onclick="StudyGroups.hideCreateModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="createGroupForm" onsubmit="StudyGroups.submitGroup(event)">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <input type="hidden" name="emoji" id="selectedEmoji" value="📚">

            <div class="group-form-group">
                <label class="group-form-label">Group Icon</label>
                <div class="group-emoji-picker">
                    <button type="button" class="emoji-option selected" data-emoji="📚">📚</button>
                    <button type="button" class="emoji-option" data-emoji="🧮">🧮</button>
                    <button type="button" class="emoji-option" data-emoji="🔬">🔬</button>
                    <button type="button" class="emoji-option" data-emoji="📐">📐</button>
                    <button type="button" class="emoji-option" data-emoji="🌍">🌍</button>
                    <button type="button" class="emoji-option" data-emoji="💻">💻</button>
                    <button type="button" class="emoji-option" data-emoji="🎨">🎨</button>
                    <button type="button" class="emoji-option" data-emoji="🎵">🎵</button>
                </div>
            </div>

            <div class="group-form-group">
                <label class="group-form-label">Group Name</label>
                <input type="text" class="group-form-input" name="name"
                    placeholder="e.g., Chemistry Study Squad" required>
            </div>

            <div class="group-form-group">
                <label class="group-form-label">Subject</label>
                <input type="text" class="group-form-input" name="subject"
                    placeholder="e.g., Chemistry, Math, Physics">
            </div>

            <div class="group-form-group">
                <label class="group-form-label">Description</label>
                <textarea class="group-form-input" name="description" rows="3"
                    placeholder="What will your group study?"></textarea>
            </div>

            <div class="group-form-group">
                <label class="group-form-label">Visibility</label>
                <select class="group-form-input" name="is_public">
                    <option value="1">Public - Anyone can join</option>
                    <option value="0">Private - Invite only</option>
                </select>
            </div>

            <button type="submit" class="group-submit-btn">
                <i class="fas fa-rocket"></i> Create Group
            </button>
        </form>
    </div>
</div>

<!-- Study Groups JavaScript -->
<script>
    const StudyGroups = {
        init() {
            this.setupTabs();
            this.setupEmojiPicker();
        },

        setupTabs() {
            document.querySelectorAll('.group-tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    document.querySelectorAll('.group-tab').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    document.getElementById('myGroupsContainer').style.display = 'none';
                    document.getElementById('discoverContainer').style.display = 'none';
                    document.getElementById('upcomingContainer').style.display = 'none';

                    const tabName = tab.dataset.tab;
                    if (tabName === 'my-groups') {
                        document.getElementById('myGroupsContainer').style.display = 'grid';
                    } else if (tabName === 'discover') {
                        document.getElementById('discoverContainer').style.display = 'grid';
                    } else {
                        document.getElementById('upcomingContainer').style.display = 'grid';
                    }
                });
            });
        },

        setupEmojiPicker() {
            document.querySelectorAll('.emoji-option').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    document.querySelectorAll('.emoji-option').forEach(b => b.classList.remove('selected'));
                    btn.classList.add('selected');
                    document.getElementById('selectedEmoji').value = btn.dataset.emoji;
                });
            });
        },

        showCreateModal() {
            document.getElementById('createGroupModal').classList.add('active');
        },

        hideCreateModal() {
            document.getElementById('createGroupModal').classList.remove('active');
            document.getElementById('createGroupForm').reset();
        },

        async submitGroup(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch('<?= APP_URL ?>/api/study-groups.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.hideCreateModal();
                    location.reload();
                } else {
                    alert(data.message || 'Failed to create group');
                }
            } catch (error) {
                console.error('Error creating group:', error);
                alert('Failed to create group. Please try again.');
            }
        },

        async joinGroup(groupId) {
            try {
                const response = await fetch('<?= APP_URL ?>/api/study-groups.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'join',
                        group_id: groupId,
                        csrf_token: '<?= generate_csrf_token() ?>'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to join group');
                }
            } catch (error) {
                console.error('Error joining group:', error);
                alert('Failed to join group. Please try again.');
            }
        },

        openGroup(groupId) {
            window.location.href = '<?= APP_URL ?>/forum/study-group.php?id=' + groupId;
        }
    };

    StudyGroups.init();
</script>