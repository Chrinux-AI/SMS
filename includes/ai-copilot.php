<?php

/**
 * AI Copilot Assistant Component
 * Voice input, smart suggestions, and predictive actions
 * Verdant SMS v3.0
 */

$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? 'student';
$user_name = $_SESSION['full_name'] ?? $_SESSION['first_name'] ?? 'User';

// Get contextual suggestions based on role and time
function get_smart_suggestions($role, $user_id)
{
    $hour = (int)date('H');
    $day = date('l');
    $suggestions = [];

    // Time-based suggestions
    if ($hour >= 7 && $hour < 9) {
        $suggestions[] = ['icon' => 'clipboard-check', 'text' => 'Take morning attendance', 'action' => 'attendance'];
    } elseif ($hour >= 12 && $hour < 14) {
        $suggestions[] = ['icon' => 'utensils', 'text' => 'Check lunch break schedule', 'action' => 'schedule'];
    } elseif ($hour >= 15 && $hour < 17) {
        $suggestions[] = ['icon' => 'chart-bar', 'text' => 'Review today\'s reports', 'action' => 'reports'];
    }

    // Day-based suggestions
    if ($day === 'Monday') {
        $suggestions[] = ['icon' => 'calendar-week', 'text' => 'Plan this week\'s activities', 'action' => 'calendar'];
    } elseif ($day === 'Friday') {
        $suggestions[] = ['icon' => 'file-alt', 'text' => 'Generate weekly summary', 'action' => 'summary'];
    }

    // Role-specific suggestions
    switch ($role) {
        case 'admin':
            $suggestions[] = ['icon' => 'user-check', 'text' => 'Review pending approvals', 'action' => 'approvals'];
            $suggestions[] = ['icon' => 'bell', 'text' => 'Send announcement', 'action' => 'announcement'];
            break;
        case 'teacher':
            $suggestions[] = ['icon' => 'edit', 'text' => 'Enter grades for recent class', 'action' => 'grades'];
            $suggestions[] = ['icon' => 'tasks', 'text' => 'Create new assignment', 'action' => 'assignment'];
            break;
        case 'student':
            $suggestions[] = ['icon' => 'book-open', 'text' => 'Check upcoming assignments', 'action' => 'assignments'];
            $suggestions[] = ['icon' => 'chart-line', 'text' => 'View my progress', 'action' => 'progress'];
            break;
        case 'parent':
            $suggestions[] = ['icon' => 'user-graduate', 'text' => 'Check child\'s attendance', 'action' => 'child-attendance'];
            $suggestions[] = ['icon' => 'envelope', 'text' => 'Message teacher', 'action' => 'message'];
            break;
    }

    return array_slice($suggestions, 0, 4);
}

// Get recent actions for quick access
function get_recent_actions($user_id, $limit = 5)
{
    try {
        return db()->fetchAll(
            "SELECT action, entity_type, created_at FROM activity_logs
             WHERE user_id = ?
             ORDER BY created_at DESC
             LIMIT ?",
            [$user_id, $limit]
        );
    } catch (Exception $e) {
        return [];
    }
}

$suggestions = get_smart_suggestions($role, $user_id);
$recent_actions = get_recent_actions($user_id);
?>

<!-- AI Copilot Assistant -->
<div class="ai-copilot" id="aiCopilot">
    <!-- Copilot Toggle Button -->
    <button class="copilot-toggle" id="copilotToggle" onclick="toggleCopilot()" aria-label="AI Assistant" aria-expanded="false">
        <div class="copilot-icon">
            <i class="fas fa-robot"></i>
        </div>
        <div class="copilot-pulse"></div>
    </button>

    <!-- Copilot Panel -->
    <div class="copilot-panel" id="copilotPanel">
        <div class="copilot-header">
            <div class="copilot-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="copilot-title">
                <h3>AI Assistant</h3>
                <span class="copilot-status"><i class="fas fa-circle"></i> Online</span>
            </div>
            <button class="copilot-close" onclick="closeCopilot()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Voice Input Section -->
        <div class="copilot-voice" id="voiceSection">
            <button class="voice-btn" id="voiceBtn" onclick="toggleVoiceInput()">
                <i class="fas fa-microphone"></i>
            </button>
            <div class="voice-status" id="voiceStatus">
                <span>Tap to speak</span>
            </div>
            <div class="voice-waveform" id="voiceWaveform">
                <div class="wave-bar"></div>
                <div class="wave-bar"></div>
                <div class="wave-bar"></div>
                <div class="wave-bar"></div>
                <div class="wave-bar"></div>
            </div>
        </div>

        <!-- Text Input -->
        <div class="copilot-input-wrapper">
            <input type="text" class="copilot-input" id="copilotInput"
                placeholder="Ask me anything..."
                onkeypress="handleCopilotInput(event)">
            <button class="copilot-send" onclick="sendCopilotQuery()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>

        <!-- Smart Suggestions -->
        <div class="copilot-suggestions" id="copilotSuggestions">
            <div class="suggestions-header">
                <i class="fas fa-lightbulb"></i> Suggested Actions
            </div>
            <div class="suggestions-list">
                <?php foreach ($suggestions as $suggestion): ?>
                    <button class="suggestion-chip" onclick="executeSuggestion('<?php echo $suggestion['action']; ?>')">
                        <i class="fas fa-<?php echo $suggestion['icon']; ?>"></i>
                        <span><?php echo htmlspecialchars($suggestion['text']); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="copilot-messages" id="copilotMessages">
            <div class="copilot-message bot">
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    <p>Hello <?php echo htmlspecialchars($user_name); ?>! 👋</p>
                    <p>I'm your AI assistant. How can I help you today?</p>
                </div>
            </div>
        </div>

        <!-- Quick Commands -->
        <div class="copilot-commands">
            <div class="commands-header">Quick Commands</div>
            <div class="commands-grid">
                <button class="command-btn" onclick="executeCommand('search')">
                    <i class="fas fa-search"></i>
                    <span>Search</span>
                </button>
                <button class="command-btn" onclick="executeCommand('help')">
                    <i class="fas fa-question-circle"></i>
                    <span>Help</span>
                </button>
                <button class="command-btn" onclick="executeCommand('navigate')">
                    <i class="fas fa-compass"></i>
                    <span>Navigate</span>
                </button>
                <button class="command-btn" onclick="executeCommand('report')">
                    <i class="fas fa-bug"></i>
                    <span>Report</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* AI Copilot Styles */
    .ai-copilot {
        position: fixed;
        bottom: 100px;
        right: 24px;
        z-index: 1500;
    }

    .copilot-toggle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        color: white;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(139, 92, 246, 0.4);
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .copilot-toggle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 30px rgba(139, 92, 246, 0.5);
    }

    .copilot-toggle.active {
        transform: rotate(360deg) scale(1);
    }

    .copilot-icon {
        font-size: 1.5rem;
        z-index: 2;
    }

    .copilot-pulse {
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        border: 2px solid rgba(139, 92, 246, 0.5);
        animation: copilot-pulse 2s ease-out infinite;
    }

    @keyframes copilot-pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }

        100% {
            transform: scale(1.5);
            opacity: 0;
        }
    }

    /* Copilot Panel */
    .copilot-panel {
        position: absolute;
        bottom: 70px;
        right: 0;
        width: 380px;
        max-height: 600px;
        background: var(--card-bg, #1a1a2e);
        border-radius: 20px;
        box-shadow: 0 10px 50px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(139, 92, 246, 0.3);
        overflow: hidden;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px) scale(0.95);
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .copilot-panel.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
    }

    .copilot-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(99, 102, 241, 0.1));
        border-bottom: 1px solid rgba(139, 92, 246, 0.2);
    }

    .copilot-avatar {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .copilot-title {
        flex: 1;
    }

    .copilot-title h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary, white);
    }

    .copilot-status {
        font-size: 0.75rem;
        color: #10b981;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .copilot-status i {
        font-size: 0.5rem;
    }

    .copilot-close {
        width: 32px;
        height: 32px;
        border: none;
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-muted, #9ca3af);
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
    }

    .copilot-close:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
    }

    /* Voice Input */
    .copilot-voice {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: rgba(139, 92, 246, 0.05);
        border-bottom: 1px solid rgba(139, 92, 246, 0.1);
    }

    .voice-btn {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: 2px solid rgba(139, 92, 246, 0.3);
        background: transparent;
        color: #8b5cf6;
        cursor: pointer;
        font-size: 1.25rem;
        transition: all 0.2s;
    }

    .voice-btn:hover {
        background: rgba(139, 92, 246, 0.1);
        border-color: #8b5cf6;
    }

    .voice-btn.recording {
        background: #ef4444;
        border-color: #ef4444;
        color: white;
        animation: voice-pulse 1s ease infinite;
    }

    @keyframes voice-pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    .voice-status {
        flex: 1;
    }

    .voice-status span {
        color: var(--text-muted, #9ca3af);
        font-size: 0.875rem;
    }

    .voice-waveform {
        display: none;
        gap: 3px;
        align-items: center;
        height: 24px;
    }

    .voice-waveform.active {
        display: flex;
    }

    .wave-bar {
        width: 3px;
        height: 100%;
        background: #8b5cf6;
        border-radius: 3px;
        animation: wave 0.5s ease infinite;
    }

    .wave-bar:nth-child(1) {
        animation-delay: 0s;
    }

    .wave-bar:nth-child(2) {
        animation-delay: 0.1s;
    }

    .wave-bar:nth-child(3) {
        animation-delay: 0.2s;
    }

    .wave-bar:nth-child(4) {
        animation-delay: 0.3s;
    }

    .wave-bar:nth-child(5) {
        animation-delay: 0.4s;
    }

    @keyframes wave {

        0%,
        100% {
            transform: scaleY(0.3);
        }

        50% {
            transform: scaleY(1);
        }
    }

    /* Text Input */
    .copilot-input-wrapper {
        display: flex;
        gap: 8px;
        padding: 12px 16px;
        border-bottom: 1px solid rgba(139, 92, 246, 0.1);
    }

    .copilot-input {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid rgba(139, 92, 246, 0.2);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary, white);
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.2s;
    }

    .copilot-input:focus {
        border-color: #8b5cf6;
    }

    .copilot-input::placeholder {
        color: var(--text-muted, #6b7280);
    }

    .copilot-send {
        width: 40px;
        height: 40px;
        border: none;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        color: white;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .copilot-send:hover {
        transform: scale(1.05);
    }

    /* Suggestions */
    .copilot-suggestions {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(139, 92, 246, 0.1);
    }

    .suggestions-header {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted, #9ca3af);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .suggestions-header i {
        color: #fbbf24;
    }

    .suggestions-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .suggestion-chip {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        background: rgba(139, 92, 246, 0.1);
        border: 1px solid rgba(139, 92, 246, 0.2);
        border-radius: 20px;
        color: var(--text-primary, white);
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.15s;
    }

    .suggestion-chip:hover {
        background: rgba(139, 92, 246, 0.2);
        border-color: #8b5cf6;
    }

    .suggestion-chip i {
        color: #8b5cf6;
        font-size: 0.75rem;
    }

    /* Messages */
    .copilot-messages {
        max-height: 200px;
        overflow-y: auto;
        padding: 16px;
    }

    .copilot-message {
        display: flex;
        gap: 10px;
        margin-bottom: 12px;
    }

    .copilot-message.bot .message-avatar {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .copilot-message.user {
        flex-direction: row-reverse;
    }

    .copilot-message.user .message-avatar {
        background: #10b981;
    }

    .message-content {
        background: rgba(255, 255, 255, 0.05);
        padding: 10px 14px;
        border-radius: 12px;
        max-width: 80%;
    }

    .copilot-message.user .message-content {
        background: rgba(139, 92, 246, 0.2);
    }

    .message-content p {
        margin: 0 0 6px;
        font-size: 0.875rem;
        color: var(--text-primary, white);
        line-height: 1.5;
    }

    .message-content p:last-child {
        margin-bottom: 0;
    }

    /* Quick Commands */
    .copilot-commands {
        padding: 12px 16px 16px;
        background: rgba(0, 0, 0, 0.2);
    }

    .commands-header {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted, #9ca3af);
        margin-bottom: 10px;
    }

    .commands-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }

    .command-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        padding: 12px 8px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: var(--text-primary, white);
        cursor: pointer;
        transition: all 0.15s;
    }

    .command-btn:hover {
        background: rgba(139, 92, 246, 0.2);
        border-color: rgba(139, 92, 246, 0.3);
    }

    .command-btn i {
        font-size: 1rem;
        color: #8b5cf6;
    }

    .command-btn span {
        font-size: 0.7rem;
    }

    /* Responsive */
    @media (max-width: 480px) {
        .ai-copilot {
            bottom: 140px;
            right: 16px;
        }

        .copilot-panel {
            width: calc(100vw - 32px);
            right: -8px;
            max-height: 70vh;
        }

        .copilot-toggle {
            width: 52px;
            height: 52px;
        }
    }

    /* Dark theme adjustments */
    .dark-theme .copilot-panel {
        background: #0f172a;
    }
</style>

<script>
    // AI Copilot JavaScript
    const AICopilot = {
        isOpen: false,
        isRecording: false,
        recognition: null,

        // Initialize speech recognition
        initVoice() {
            if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                this.recognition = new SpeechRecognition();
                this.recognition.continuous = false;
                this.recognition.interimResults = true;
                this.recognition.lang = 'en-US';

                this.recognition.onstart = () => {
                    this.isRecording = true;
                    document.getElementById('voiceBtn').classList.add('recording');
                    document.getElementById('voiceStatus').innerHTML = '<span style="color: #ef4444;">Listening...</span>';
                    document.getElementById('voiceWaveform').classList.add('active');
                };

                this.recognition.onresult = (event) => {
                    const transcript = Array.from(event.results)
                        .map(result => result[0].transcript)
                        .join('');

                    document.getElementById('copilotInput').value = transcript;

                    if (event.results[0].isFinal) {
                        this.stopVoice();
                        this.processQuery(transcript);
                    }
                };

                this.recognition.onerror = (event) => {
                    console.error('Speech recognition error:', event.error);
                    this.stopVoice();
                    document.getElementById('voiceStatus').innerHTML = '<span style="color: #ef4444;">Error: ' + event.error + '</span>';
                };

                this.recognition.onend = () => {
                    this.stopVoice();
                };
            }
        },

        startVoice() {
            if (this.recognition) {
                this.recognition.start();
            } else {
                alert('Voice input is not supported in this browser. Please use Chrome or Edge.');
            }
        },

        stopVoice() {
            this.isRecording = false;
            document.getElementById('voiceBtn').classList.remove('recording');
            document.getElementById('voiceStatus').innerHTML = '<span>Tap to speak</span>';
            document.getElementById('voiceWaveform').classList.remove('active');
            if (this.recognition) {
                this.recognition.stop();
            }
        },

        // Process user query
        async processQuery(query) {
            if (!query.trim()) return;

            // Add user message
            this.addMessage(query, 'user');
            document.getElementById('copilotInput').value = '';

            // Show typing indicator
            const typingId = this.showTyping();

            try {
                // Send to AI endpoint
                const response = await fetch('<?php echo APP_URL; ?>/api/ai-copilot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo generate_csrf_token(); ?>'
                    },
                    body: JSON.stringify({
                        query,
                        context: this.getContext()
                    })
                });

                const data = await response.json();
                this.removeTyping(typingId);

                if (data.success) {
                    this.addMessage(data.response, 'bot');

                    // Execute action if provided
                    if (data.action) {
                        this.executeAction(data.action);
                    }
                } else {
                    this.addMessage('Sorry, I encountered an error. Please try again.', 'bot');
                }
            } catch (error) {
                this.removeTyping(typingId);
                this.addMessage('I\'m having trouble connecting. Please check your internet connection.', 'bot');
            }
        },

        addMessage(text, type) {
            const container = document.getElementById('copilotMessages');
            const messageHtml = `
            <div class="copilot-message ${type}">
                <div class="message-avatar"><i class="fas fa-${type === 'bot' ? 'robot' : 'user'}"></i></div>
                <div class="message-content"><p>${this.escapeHtml(text)}</p></div>
            </div>
        `;
            container.insertAdjacentHTML('beforeend', messageHtml);
            container.scrollTop = container.scrollHeight;
        },

        showTyping() {
            const id = 'typing-' + Date.now();
            const container = document.getElementById('copilotMessages');
            const typingHtml = `
            <div class="copilot-message bot" id="${id}">
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-content"><p><i class="fas fa-ellipsis-h fa-beat"></i></p></div>
            </div>
        `;
            container.insertAdjacentHTML('beforeend', typingHtml);
            container.scrollTop = container.scrollHeight;
            return id;
        },

        removeTyping(id) {
            document.getElementById(id)?.remove();
        },

        getContext() {
            return {
                page: window.location.pathname,
                role: '<?php echo $role; ?>',
                time: new Date().toISOString()
            };
        },

        executeAction(action) {
            switch (action.type) {
                case 'navigate':
                    window.location.href = action.url;
                    break;
                case 'open_modal':
                    if (typeof window[action.function] === 'function') {
                        window[action.function]();
                    }
                    break;
                case 'execute':
                    if (typeof window[action.function] === 'function') {
                        window[action.function](...(action.args || []));
                    }
                    break;
            }
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => {
        AICopilot.initVoice();
    });

    // Global functions
    function toggleCopilot() {
        const panel = document.getElementById('copilotPanel');
        const toggle = document.getElementById('copilotToggle');

        AICopilot.isOpen = !AICopilot.isOpen;
        panel.classList.toggle('active', AICopilot.isOpen);
        toggle.classList.toggle('active', AICopilot.isOpen);
        toggle.setAttribute('aria-expanded', AICopilot.isOpen);

        if (AICopilot.isOpen) {
            document.getElementById('copilotInput').focus();
        }
    }

    function closeCopilot() {
        AICopilot.isOpen = false;
        document.getElementById('copilotPanel').classList.remove('active');
        document.getElementById('copilotToggle').classList.remove('active');
    }

    function toggleVoiceInput() {
        if (AICopilot.isRecording) {
            AICopilot.stopVoice();
        } else {
            AICopilot.startVoice();
        }
    }

    function handleCopilotInput(event) {
        if (event.key === 'Enter') {
            sendCopilotQuery();
        }
    }

    function sendCopilotQuery() {
        const input = document.getElementById('copilotInput');
        AICopilot.processQuery(input.value);
    }

    function executeSuggestion(action) {
        const actions = {
            'attendance': '<?php echo APP_URL; ?>/<?php echo $role; ?>/attendance.php',
            'grades': '<?php echo APP_URL; ?>/<?php echo $role; ?>/grades.php',
            'reports': '<?php echo APP_URL; ?>/<?php echo $role; ?>/reports.php',
            'calendar': '<?php echo APP_URL; ?>/<?php echo $role; ?>/calendar.php',
            'approvals': '<?php echo APP_URL; ?>/admin/approve-users.php',
            'announcement': '<?php echo APP_URL; ?>/admin/announcements.php',
            'assignments': '<?php echo APP_URL; ?>/student/assignments.php',
            'progress': '<?php echo APP_URL; ?>/student/progress.php',
            'child-attendance': '<?php echo APP_URL; ?>/parent/attendance.php',
            'message': '<?php echo APP_URL; ?>/messages.php'
        };

        if (actions[action]) {
            window.location.href = actions[action];
        } else {
            AICopilot.addMessage(`Executing: ${action}`, 'bot');
        }
    }

    function executeCommand(cmd) {
        switch (cmd) {
            case 'search':
                if (typeof openGlobalSearch === 'function') {
                    openGlobalSearch();
                } else {
                    AICopilot.addMessage('What would you like to search for?', 'bot');
                }
                break;
            case 'help':
                AICopilot.addMessage('I can help you with:\n• Navigation - "Go to attendance"\n• Searching - "Find student John"\n• Actions - "Create announcement"\n• Reports - "Show weekly report"', 'bot');
                break;
            case 'navigate':
                AICopilot.addMessage('Where would you like to go? Say or type a page name.', 'bot');
                break;
            case 'report':
                AICopilot.addMessage('Please describe the issue you\'d like to report.', 'bot');
                break;
        }
        closeCopilot();
    }

    // Keyboard shortcut (Ctrl + /)
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.key === '/') {
            e.preventDefault();
            toggleCopilot();
        }
        if (e.key === 'Escape' && AICopilot.isOpen) {
            closeCopilot();
        }
    });
</script>