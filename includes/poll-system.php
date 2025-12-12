<?php

/**
 * Quick Poll System Component
 * Interactive polls with real-time results
 * Verdant SMS v3.0
 */

if (!isset($_SESSION['user_id'])) return;

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'student';

// Get active polls
$active_polls = getActivePolls($user_id);

function getActivePolls($user_id)
{
    try {
        return db()->fetchAll(
            "SELECT p.*,
                    (SELECT COUNT(*) FROM poll_votes WHERE poll_id = p.id) as total_votes,
                    (SELECT option_id FROM poll_votes WHERE poll_id = p.id AND user_id = ?) as user_vote
             FROM polls p
             WHERE p.status = 'active'
             AND (p.expires_at IS NULL OR p.expires_at > NOW())
             ORDER BY p.created_at DESC
             LIMIT 3",
            [$user_id]
        );
    } catch (Exception $e) {
        return [];
    }
}

function getPollOptions($poll_id)
{
    try {
        return db()->fetchAll(
            "SELECT po.*,
                    (SELECT COUNT(*) FROM poll_votes pv WHERE pv.option_id = po.id) as vote_count
             FROM poll_options po
             WHERE po.poll_id = ?
             ORDER BY po.id",
            [$poll_id]
        );
    } catch (Exception $e) {
        return [];
    }
}
?>

<!-- Poll System Styles -->
<style>
    .poll-widget {
        background: rgba(10, 10, 10, 0.95);
        border: 1px solid rgba(0, 255, 136, 0.3);
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
    }

    .poll-widget-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .poll-widget-title {
        color: #00ff88;
        font-size: 1.3rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .create-poll-btn {
        background: linear-gradient(135deg, #00ff88, #00d4ff);
        border: none;
        color: #000;
        padding: 8px 16px;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
    }

    .create-poll-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0, 255, 136, 0.3);
    }

    .poll-card {
        background: rgba(20, 20, 20, 0.9);
        border: 1px solid rgba(0, 255, 136, 0.2);
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .poll-card:hover {
        border-color: rgba(0, 255, 136, 0.4);
    }

    .poll-question {
        color: #fff;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .poll-meta {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 0.85rem;
        color: #888;
    }

    .poll-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .poll-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .poll-option {
        background: rgba(0, 255, 136, 0.05);
        border: 1px solid rgba(0, 255, 136, 0.2);
        border-radius: 8px;
        padding: 12px 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .poll-option:hover:not(.voted):not(.disabled) {
        border-color: #00ff88;
        background: rgba(0, 255, 136, 0.1);
    }

    .poll-option.selected {
        border-color: #00ff88;
        background: rgba(0, 255, 136, 0.15);
    }

    .poll-option.voted {
        cursor: default;
    }

    .poll-option-text {
        color: #fff;
        font-size: 0.95rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .poll-option-percentage {
        color: #00ff88;
        font-weight: 600;
    }

    .poll-option-bar {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        background: linear-gradient(90deg, rgba(0, 255, 136, 0.3), rgba(0, 212, 255, 0.2));
        width: 0;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .poll-option.voted .poll-option-bar {
        display: block;
    }

    .poll-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .poll-votes-count {
        color: #888;
        font-size: 0.85rem;
    }

    .poll-expires {
        color: #ffcc00;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Create Poll Modal */
    .create-poll-modal {
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

    .create-poll-modal.active {
        opacity: 1;
        visibility: visible;
    }

    .poll-modal-content {
        background: rgba(20, 20, 20, 0.98);
        border: 1px solid rgba(0, 255, 136, 0.3);
        border-radius: 16px;
        width: 90%;
        max-width: 500px;
        max-height: 80vh;
        overflow-y: auto;
        padding: 24px;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }

    .create-poll-modal.active .poll-modal-content {
        transform: translateY(0);
    }

    .poll-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(0, 255, 136, 0.2);
    }

    .poll-modal-title {
        color: #00ff88;
        font-size: 1.3rem;
        font-weight: 600;
    }

    .poll-modal-close {
        background: none;
        border: none;
        color: #888;
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .poll-modal-close:hover {
        color: #ff6b6b;
    }

    .poll-form-group {
        margin-bottom: 20px;
    }

    .poll-form-label {
        color: #aaa;
        font-size: 0.9rem;
        margin-bottom: 8px;
        display: block;
    }

    .poll-form-input {
        width: 100%;
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(0, 255, 136, 0.3);
        border-radius: 8px;
        padding: 12px 16px;
        color: #fff;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .poll-form-input:focus {
        outline: none;
        border-color: #00ff88;
        box-shadow: 0 0 10px rgba(0, 255, 136, 0.2);
    }

    .poll-options-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .poll-option-input-group {
        display: flex;
        gap: 10px;
    }

    .poll-option-input-group input {
        flex: 1;
    }

    .remove-option-btn {
        background: rgba(255, 107, 107, 0.2);
        border: 1px solid rgba(255, 107, 107, 0.3);
        color: #ff6b6b;
        width: 40px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .remove-option-btn:hover {
        background: rgba(255, 107, 107, 0.3);
    }

    .add-option-btn {
        background: rgba(0, 255, 136, 0.1);
        border: 1px dashed rgba(0, 255, 136, 0.3);
        color: #00ff88;
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
        width: 100%;
        transition: all 0.3s ease;
        margin-top: 10px;
    }

    .add-option-btn:hover {
        background: rgba(0, 255, 136, 0.2);
        border-style: solid;
    }

    .poll-submit-btn {
        background: linear-gradient(135deg, #00ff88, #00d4ff);
        border: none;
        color: #000;
        padding: 14px 28px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        width: 100%;
        transition: all 0.3s ease;
    }

    .poll-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0, 255, 136, 0.3);
    }

    .no-polls-message {
        text-align: center;
        padding: 40px 20px;
        color: #888;
    }

    .no-polls-message i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: rgba(0, 255, 136, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .poll-widget {
            padding: 15px;
        }

        .poll-widget-header {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>

<!-- Poll Widget HTML -->
<div class="poll-widget" id="pollWidget">
    <div class="poll-widget-header">
        <h3 class="poll-widget-title">
            <i class="fas fa-poll"></i>
            Quick Polls
        </h3>
        <?php if (in_array($role, ['admin', 'teacher', 'principal'])): ?>
            <button class="create-poll-btn" onclick="PollSystem.showCreateModal()">
                <i class="fas fa-plus"></i>
                Create Poll
            </button>
        <?php endif; ?>
    </div>

    <div class="polls-container" id="pollsContainer">
        <?php if (empty($active_polls)): ?>
            <div class="no-polls-message">
                <i class="fas fa-poll-h"></i>
                <p>No active polls right now</p>
            </div>
        <?php else: ?>
            <?php foreach ($active_polls as $poll):
                $options = getPollOptions($poll['id']);
                $user_voted = !empty($poll['user_vote']);
            ?>
                <div class="poll-card" data-poll-id="<?= $poll['id'] ?>">
                    <div class="poll-question"><?= htmlspecialchars($poll['question']) ?></div>
                    <div class="poll-meta">
                        <span><i class="fas fa-user"></i> <?= htmlspecialchars($poll['created_by_name'] ?? 'Admin') ?></span>
                        <span><i class="fas fa-clock"></i> <?= timeAgo($poll['created_at']) ?></span>
                    </div>
                    <div class="poll-options">
                        <?php foreach ($options as $option):
                            $percentage = $poll['total_votes'] > 0
                                ? round(($option['vote_count'] / $poll['total_votes']) * 100)
                                : 0;
                            $is_selected = $poll['user_vote'] == $option['id'];
                        ?>
                            <div class="poll-option <?= $user_voted ? 'voted' : '' ?> <?= $is_selected ? 'selected' : '' ?>"
                                data-option-id="<?= $option['id'] ?>"
                                <?php if (!$user_voted): ?>onclick="PollSystem.vote(<?= $poll['id'] ?>, <?= $option['id'] ?>)" <?php endif; ?>>
                                <div class="poll-option-bar" style="width: <?= $user_voted ? $percentage : 0 ?>%"></div>
                                <div class="poll-option-text">
                                    <span><?= htmlspecialchars($option['option_text']) ?></span>
                                    <?php if ($user_voted): ?>
                                        <span class="poll-option-percentage"><?= $percentage ?>%</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="poll-footer">
                        <span class="poll-votes-count">
                            <i class="fas fa-users"></i> <?= $poll['total_votes'] ?> votes
                        </span>
                        <?php if (!empty($poll['expires_at'])): ?>
                            <span class="poll-expires">
                                <i class="fas fa-hourglass-half"></i>
                                Expires <?= timeAgo($poll['expires_at']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Create Poll Modal -->
<div class="create-poll-modal" id="createPollModal">
    <div class="poll-modal-content">
        <div class="poll-modal-header">
            <h3 class="poll-modal-title">Create New Poll</h3>
            <button class="poll-modal-close" onclick="PollSystem.hideCreateModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="createPollForm" onsubmit="PollSystem.submitPoll(event)">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

            <div class="poll-form-group">
                <label class="poll-form-label">Question</label>
                <input type="text" class="poll-form-input" name="question"
                    placeholder="What would you like to ask?" required>
            </div>

            <div class="poll-form-group">
                <label class="poll-form-label">Options</label>
                <div class="poll-options-list" id="pollOptionsList">
                    <div class="poll-option-input-group">
                        <input type="text" class="poll-form-input" name="options[]"
                            placeholder="Option 1" required>
                    </div>
                    <div class="poll-option-input-group">
                        <input type="text" class="poll-form-input" name="options[]"
                            placeholder="Option 2" required>
                    </div>
                </div>
                <button type="button" class="add-option-btn" onclick="PollSystem.addOption()">
                    <i class="fas fa-plus"></i> Add Option
                </button>
            </div>

            <div class="poll-form-group">
                <label class="poll-form-label">Expires In (optional)</label>
                <select class="poll-form-input" name="expires">
                    <option value="">Never</option>
                    <option value="1">1 Hour</option>
                    <option value="24">24 Hours</option>
                    <option value="72">3 Days</option>
                    <option value="168">1 Week</option>
                </select>
            </div>

            <button type="submit" class="poll-submit-btn">
                <i class="fas fa-paper-plane"></i> Publish Poll
            </button>
        </form>
    </div>
</div>

<!-- Poll System JavaScript -->
<script>
    const PollSystem = {
        init() {
            this.optionCount = 2;
        },

        showCreateModal() {
            document.getElementById('createPollModal').classList.add('active');
        },

        hideCreateModal() {
            document.getElementById('createPollModal').classList.remove('active');
            document.getElementById('createPollForm').reset();
            // Reset options to 2
            const list = document.getElementById('pollOptionsList');
            list.innerHTML = `
            <div class="poll-option-input-group">
                <input type="text" class="poll-form-input" name="options[]" placeholder="Option 1" required>
            </div>
            <div class="poll-option-input-group">
                <input type="text" class="poll-form-input" name="options[]" placeholder="Option 2" required>
            </div>
        `;
            this.optionCount = 2;
        },

        addOption() {
            if (this.optionCount >= 6) {
                alert('Maximum 6 options allowed');
                return;
            }
            this.optionCount++;
            const list = document.getElementById('pollOptionsList');
            const group = document.createElement('div');
            group.className = 'poll-option-input-group';
            group.innerHTML = `
            <input type="text" class="poll-form-input" name="options[]"
                   placeholder="Option ${this.optionCount}" required>
            <button type="button" class="remove-option-btn" onclick="PollSystem.removeOption(this)">
                <i class="fas fa-times"></i>
            </button>
        `;
            list.appendChild(group);
        },

        removeOption(btn) {
            btn.closest('.poll-option-input-group').remove();
            this.optionCount--;
        },

        async submitPoll(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch('<?= APP_URL ?>/api/polls.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.hideCreateModal();
                    location.reload(); // Refresh to show new poll
                } else {
                    alert(data.message || 'Failed to create poll');
                }
            } catch (error) {
                console.error('Error creating poll:', error);
                alert('Failed to create poll. Please try again.');
            }
        },

        async vote(pollId, optionId) {
            try {
                const response = await fetch('<?= APP_URL ?>/api/polls.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'vote',
                        poll_id: pollId,
                        option_id: optionId,
                        csrf_token: '<?= generate_csrf_token() ?>'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.updatePollUI(pollId, optionId, data.results);
                } else {
                    alert(data.message || 'Failed to submit vote');
                }
            } catch (error) {
                console.error('Error voting:', error);
                alert('Failed to vote. Please try again.');
            }
        },

        updatePollUI(pollId, votedOptionId, results) {
            const pollCard = document.querySelector(`[data-poll-id="${pollId}"]`);
            if (!pollCard) return;

            const options = pollCard.querySelectorAll('.poll-option');
            options.forEach(option => {
                const optionId = parseInt(option.dataset.optionId);
                const result = results.find(r => r.id === optionId);
                const percentage = result ? result.percentage : 0;

                option.classList.add('voted');
                if (optionId === votedOptionId) {
                    option.classList.add('selected');
                }

                // Update bar
                const bar = option.querySelector('.poll-option-bar');
                if (bar) {
                    bar.style.width = percentage + '%';
                }

                // Add percentage text
                const textContainer = option.querySelector('.poll-option-text');
                let pctSpan = textContainer.querySelector('.poll-option-percentage');
                if (!pctSpan) {
                    pctSpan = document.createElement('span');
                    pctSpan.className = 'poll-option-percentage';
                    textContainer.appendChild(pctSpan);
                }
                pctSpan.textContent = percentage + '%';

                // Remove click handler
                option.onclick = null;
            });

            // Update vote count
            const totalVotes = results.reduce((sum, r) => sum + r.votes, 0);
            const voteCount = pollCard.querySelector('.poll-votes-count');
            if (voteCount) {
                voteCount.innerHTML = `<i class="fas fa-users"></i> ${totalVotes} votes`;
            }
        }
    };

    PollSystem.init();
</script>

<?php
// Helper function if not defined
if (!function_exists('timeAgo')) {
    function timeAgo($datetime)
    {
        $time = strtotime($datetime);
        $diff = time() - $time;

        if ($diff < 60) return 'just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return date('M j', $time);
    }
}
?>