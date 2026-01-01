<?php

/**
 * Unified Chatbot - Verdant SMS AI Assistant
 * Merges best features from sams-bot.php and ai-copilot.php
 * Ensures only ONE chatbot instance per page
 *
 * Features:
 * - Role-based responses (from sams-bot)
 * - Voice input (from ai-copilot)
 * - Navigation assistance
 * - Context awareness
 * - Smart suggestions
 */

// Prevent multiple instances
if (defined('CHATBOT_LOADED')) {
    return;
}
define('CHATBOT_LOADED', true);

// Get user context
$user_name = $_SESSION['full_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'guest';
$user_id = $_SESSION['user_id'] ?? 0;

// Get smart suggestions based on role and time
if (!function_exists('get_smart_suggestions')) {
    function get_smart_suggestions($role, $user_id) {
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
}

$suggestions = get_smart_suggestions($user_role, $user_id);
?>

<!-- Unified Verdant Chatbot Widget -->
<div id="verdantChatbot" class="verdant-chatbot-widget">
    <!-- Toggle Button -->
    <button id="verdantChatbotToggle" class="chatbot-toggle-btn" onclick="toggleVerdantChatbot()" title="Open AI Assistant" aria-label="Open AI Assistant" aria-expanded="false">
        <i class="fas fa-robot"></i>
        <span class="chatbot-pulse"></span>
    </button>

    <!-- Chatbot Panel -->
    <div id="verdantChatbotPanel" class="chatbot-panel" style="display: none;">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="chatbot-info">
                <div class="chatbot-name">Verdant AI Assistant</div>
                <div class="chatbot-status">
                    <span class="status-dot"></span> Ready to Help
                </div>
            </div>
            <button onclick="toggleVerdantChatbot()" class="chatbot-close" title="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Context Bar -->
        <div class="chatbot-context-bar">
            <i class="fas fa-user-circle"></i>
            <span><?php echo htmlspecialchars($user_name); ?> • <?php echo ucfirst($user_role); ?></span>
        </div>

        <!-- Voice Input Section -->
        <div class="chatbot-voice" id="voiceSection">
            <button class="voice-btn" id="voiceBtn" onclick="toggleVoiceInput()" title="Tap to speak">
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

        <!-- Messages Area -->
        <div id="chatbotMessages" class="chatbot-messages">
            <div class="chatbot-message bot">
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    <p>👋 Hi <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?>! I'm your Verdant AI Assistant.</p>
                    <p><strong>I can help you with:</strong></p>
                    <ul>
                        <?php if ($user_role === 'student'): ?>
                            <li>📊 Check attendance & grades</li>
                            <li>📅 View your schedule</li>
                            <li>📝 Assignment information</li>
                            <li>💬 Navigate the system</li>
                        <?php elseif ($user_role === 'teacher'): ?>
                            <li>✍️ Draft parent messages</li>
                            <li>📈 Class statistics</li>
                            <li>👥 Student insights</li>
                            <li>🎯 Feature guidance</li>
                        <?php elseif ($user_role === 'parent'): ?>
                            <li>👨‍👩‍👧 Children's status</li>
                            <li>📚 Grade reports</li>
                            <li>💰 Fee information</li>
                            <li>📞 Contact teachers</li>
                        <?php elseif ($user_role === 'admin'): ?>
                            <li>📊 System analytics</li>
                            <li>👥 User management</li>
                            <li>🔐 Security logs</li>
                            <li>🛠️ Technical support</li>
                        <?php endif; ?>
                    </ul>
                    <p><small style="opacity: 0.7;">💡 Tip: Click quick actions below, use voice input, or type your question!</small></p>
                </div>
            </div>
        </div>

        <!-- Smart Suggestions -->
        <?php if (!empty($suggestions)): ?>
        <div class="chatbot-suggestions">
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
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="chatbot-quick-actions">
            <?php if ($user_role === 'student'): ?>
                <button onclick="quickAsk('What is my attendance percentage?')" class="quick-btn">
                    <i class="fas fa-chart-line"></i> My Attendance
                </button>
                <button onclick="quickAsk('Show my class schedule')" class="quick-btn">
                    <i class="fas fa-calendar"></i> Schedule
                </button>
                <button onclick="quickAsk('What assignments are due soon?')" class="quick-btn">
                    <i class="fas fa-tasks"></i> Assignments
                </button>
                <button onclick="quickAsk('How do I check my grades?')" class="quick-btn">
                    <i class="fas fa-graduation-cap"></i> Grades
                </button>
            <?php elseif ($user_role === 'teacher'): ?>
                <button onclick="quickAsk('Summarize today\'s attendance')" class="quick-btn">
                    <i class="fas fa-clipboard-check"></i> Today's Attendance
                </button>
                <button onclick="quickAsk('Draft parent message about field trip')" class="quick-btn">
                    <i class="fas fa-envelope"></i> Draft Message
                </button>
                <button onclick="quickAsk('How do I upload resources?')" class="quick-btn">
                    <i class="fas fa-upload"></i> Upload Guide
                </button>
                <button onclick="quickAsk('Show student behavior trends')" class="quick-btn">
                    <i class="fas fa-chart-bar"></i> Behavior Stats
                </button>
            <?php elseif ($user_role === 'parent'): ?>
                <button onclick="quickAsk('Show my children\'s attendance')" class="quick-btn">
                    <i class="fas fa-child"></i> Attendance
                </button>
                <button onclick="quickAsk('Are there any pending fees?')" class="quick-btn">
                    <i class="fas fa-wallet"></i> Fee Status
                </button>
                <button onclick="quickAsk('How do I book a teacher meeting?')" class="quick-btn">
                    <i class="fas fa-calendar-check"></i> Book Meeting
                </button>
                <button onclick="quickAsk('Check children\'s grades')" class="quick-btn">
                    <i class="fas fa-star"></i> Grades
                </button>
            <?php elseif ($user_role === 'admin'): ?>
                <button onclick="quickAsk('System health overview')" class="quick-btn">
                    <i class="fas fa-heartbeat"></i> System Health
                </button>
                <button onclick="quickAsk('How to backup database?')" class="quick-btn">
                    <i class="fas fa-database"></i> Backup Guide
                </button>
                <button onclick="quickAsk('Show recent security alerts')" class="quick-btn">
                    <i class="fas fa-shield-alt"></i> Security
                </button>
                <button onclick="quickAsk('User statistics summary')" class="quick-btn">
                    <i class="fas fa-users"></i> User Stats
                </button>
            <?php endif; ?>
        </div>

        <!-- Input Area -->
        <div class="chatbot-input-area">
            <input type="text" id="chatbotInput" placeholder="Ask me anything..." class="chatbot-input" onkeypress="handleChatbotEnter(event)">
            <button onclick="sendChatbotMessage()" class="chatbot-send-btn" title="Send message">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
    .verdant-chatbot-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 10000;
        font-family: 'Inter', sans-serif;
    }

    .chatbot-toggle-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--cyber-cyan, #00BFFF), var(--cyber-purple, #8A2BE2));
        border: none;
        color: white;
        font-size: 1.8rem;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0, 191, 255, 0.4);
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chatbot-toggle-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 30px rgba(0, 191, 255, 0.6);
    }

    .chatbot-pulse {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 15px;
        height: 15px;
        background: var(--cyber-green, #00FF7F);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.7;
        }
    }

    .chatbot-panel {
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 400px;
        max-width: calc(100vw - 40px);
        height: 600px;
        max-height: calc(100vh - 120px);
        background: rgba(20, 20, 30, 0.95);
        backdrop-filter: blur(20px);
        border: 2px solid var(--cyber-cyan, #00BFFF);
        border-radius: 20px;
        box-shadow: 0 10px 50px rgba(0, 191, 255, 0.3);
        display: flex;
        flex-direction: column;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chatbot-header {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: linear-gradient(135deg, rgba(0, 191, 255, 0.2), rgba(138, 43, 226, 0.2));
        border-bottom: 1px solid var(--cyber-cyan, #00BFFF);
        border-radius: 18px 18px 0 0;
    }

    .chatbot-avatar {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, var(--cyber-cyan, #00BFFF), var(--cyber-purple, #8A2BE2));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .chatbot-info {
        flex: 1;
    }

    .chatbot-name {
        font-weight: bold;
        color: var(--cyber-cyan, #00BFFF);
        font-size: 1.1rem;
    }

    .chatbot-status {
        font-size: 0.85rem;
        color: var(--text-muted, #9ca3af);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background: var(--cyber-green, #00FF7F);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    .chatbot-close {
        background: none;
        border: none;
        color: var(--text-muted, #9ca3af);
        font-size: 1.2rem;
        cursor: pointer;
        padding: 5px;
        transition: color 0.3s;
    }

    .chatbot-close:hover {
        color: var(--cyber-red, #ff4444);
    }

    .chatbot-context-bar {
        padding: 10px 20px;
        background: rgba(0, 191, 255, 0.1);
        border-bottom: 1px solid rgba(0, 191, 255, 0.2);
        font-size: 0.85rem;
        color: var(--text-muted, #9ca3af);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chatbot-voice {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: rgba(139, 92, 246, 0.05);
        border-bottom: 1px solid rgba(139, 92, 246, 0.1);
    }

    .voice-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid rgba(139, 92, 246, 0.3);
        background: transparent;
        color: #8b5cf6;
        cursor: pointer;
        font-size: 1.1rem;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
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
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }

    .voice-status {
        flex: 1;
        font-size: 0.85rem;
        color: var(--text-muted, #9ca3af);
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

    .wave-bar:nth-child(1) { animation-delay: 0s; }
    .wave-bar:nth-child(2) { animation-delay: 0.1s; }
    .wave-bar:nth-child(3) { animation-delay: 0.2s; }
    .wave-bar:nth-child(4) { animation-delay: 0.3s; }
    .wave-bar:nth-child(5) { animation-delay: 0.4s; }

    @keyframes wave {
        0%, 100% {
            transform: scaleY(0.3);
        }
        50% {
            transform: scaleY(1);
        }
    }

    .chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .chatbot-message {
        display: flex;
        gap: 10px;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chatbot-message.user {
        flex-direction: row-reverse;
    }

    .message-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--cyber-cyan, #00BFFF), var(--cyber-purple, #8A2BE2));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
        font-size: 0.9rem;
    }

    .chatbot-message.user .message-avatar {
        background: linear-gradient(135deg, var(--cyber-green, #00FF7F), var(--cyber-blue, #0080FF));
    }

    .message-content {
        background: rgba(0, 191, 255, 0.1);
        padding: 12px 15px;
        border-radius: 15px;
        border: 1px solid rgba(0, 191, 255, 0.2);
        max-width: 75%;
        color: var(--text-body, #ffffff);
        line-height: 1.6;
        font-size: 0.9rem;
    }

    .chatbot-message.user .message-content {
        background: rgba(0, 255, 127, 0.1);
        border-color: rgba(0, 255, 127, 0.2);
    }

    .message-content ul {
        margin: 10px 0;
        padding-left: 20px;
    }

    .message-content li {
        margin: 5px 0;
    }

    .chatbot-suggestions {
        padding: 12px 16px;
        border-top: 1px solid rgba(139, 92, 246, 0.1);
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

    .chatbot-quick-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding: 15px 20px;
        border-top: 1px solid rgba(0, 191, 255, 0.2);
    }

    .quick-btn {
        padding: 8px 12px;
        background: rgba(0, 191, 255, 0.1);
        border: 1px solid var(--cyber-cyan, #00BFFF);
        border-radius: 8px;
        color: var(--cyber-cyan, #00BFFF);
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: center;
    }

    .quick-btn:hover {
        background: rgba(0, 191, 255, 0.2);
        transform: translateY(-2px);
    }

    .chatbot-input-area {
        display: flex;
        gap: 10px;
        padding: 15px 20px;
        background: rgba(0, 0, 0, 0.3);
        border-top: 1px solid rgba(0, 191, 255, 0.2);
        border-radius: 0 0 18px 18px;
    }

    .chatbot-input {
        flex: 1;
        padding: 12px 15px;
        background: rgba(0, 191, 255, 0.05);
        border: 1px solid var(--cyber-cyan, #00BFFF);
        border-radius: 25px;
        color: white;
        font-size: 0.95rem;
    }

    .chatbot-input:focus {
        outline: none;
        border-color: var(--cyber-cyan, #00BFFF);
        box-shadow: 0 0 15px rgba(0, 191, 255, 0.2);
    }

    .chatbot-send-btn {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, var(--cyber-cyan, #00BFFF), var(--cyber-purple, #8A2BE2));
        border: none;
        border-radius: 50%;
        color: white;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chatbot-send-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(0, 191, 255, 0.4);
    }

    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 10px 15px;
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        background: var(--cyber-cyan, #00BFFF);
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }

    .typing-dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
        }
        30% {
            transform: translateY(-10px);
        }
    }

    @media (max-width: 480px) {
        .chatbot-panel {
            width: calc(100vw - 40px);
            right: 20px;
            max-height: calc(100vh - 100px);
        }
    }
</style>

<script>
    // Unified Chatbot JavaScript
    const VerdantChatbot = {
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
                    const voiceBtn = document.getElementById('voiceBtn');
                    const voiceStatus = document.getElementById('voiceStatus');
                    const voiceWaveform = document.getElementById('voiceWaveform');
                    if (voiceBtn) voiceBtn.classList.add('recording');
                    if (voiceStatus) voiceStatus.innerHTML = '<span style="color: #ef4444;">Listening...</span>';
                    if (voiceWaveform) voiceWaveform.classList.add('active');
                };

                this.recognition.onresult = (event) => {
                    const transcript = Array.from(event.results)
                        .map(result => result[0].transcript)
                        .join('');

                    const input = document.getElementById('chatbotInput');
                    if (input) input.value = transcript;

                    if (event.results[0].isFinal) {
                        this.stopVoice();
                        this.processQuery(transcript);
                    }
                };

                this.recognition.onerror = (event) => {
                    console.error('Speech recognition error:', event.error);
                    this.stopVoice();
                    const voiceStatus = document.getElementById('voiceStatus');
                    if (voiceStatus) voiceStatus.innerHTML = '<span style="color: #ef4444;">Error: ' + event.error + '</span>';
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
            const voiceBtn = document.getElementById('voiceBtn');
            const voiceStatus = document.getElementById('voiceStatus');
            const voiceWaveform = document.getElementById('voiceWaveform');
            if (voiceBtn) voiceBtn.classList.remove('recording');
            if (voiceStatus) voiceStatus.innerHTML = '<span>Tap to speak</span>';
            if (voiceWaveform) voiceWaveform.classList.remove('active');
            if (this.recognition) {
                this.recognition.stop();
            }
        },

        // Process user query
        async processQuery(query) {
            if (!query.trim()) return;

            // Add user message
            this.addMessage(query, 'user');
            const input = document.getElementById('chatbotInput');
            if (input) input.value = '';

            // Show typing indicator
            const typingId = this.showTyping();

            try {
                // Send to unified chatbot API
                const response = await fetch('<?php echo APP_URL; ?>/api/chatbot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        message: query,
                        user_role: '<?php echo $user_role; ?>',
                        user_id: '<?php echo $user_id; ?>',
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

        addMessage(content, type) {
            const messagesDiv = document.getElementById('chatbotMessages');
            if (!messagesDiv) return;

            const messageDiv = document.createElement('div');
            messageDiv.className = `chatbot-message ${type}`;

            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.innerHTML = type === 'bot' ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user"></i>';

            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            contentDiv.innerHTML = `<p>${content.replace(/\n/g, '<br>')}</p>`;

            messageDiv.appendChild(avatar);
            messageDiv.appendChild(contentDiv);
            messagesDiv.appendChild(messageDiv);

            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        },

        showTyping() {
            const messagesDiv = document.getElementById('chatbotMessages');
            if (!messagesDiv) return null;

            const typingDiv = document.createElement('div');
            const id = 'typing-' + Date.now();
            typingDiv.id = id;
            typingDiv.className = 'chatbot-message bot';
            typingDiv.innerHTML = `
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    <div class="typing-indicator">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            `;
            messagesDiv.appendChild(typingDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
            return id;
        },

        removeTyping(id) {
            const element = document.getElementById(id);
            if (element) element.remove();
        },

        getContext() {
            return {
                page: window.location.pathname,
                role: '<?php echo $user_role; ?>',
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
        }
    };

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => {
        VerdantChatbot.initVoice();
    });

    // Global functions
    function toggleVerdantChatbot() {
        const panel = document.getElementById('verdantChatbotPanel');
        const toggle = document.getElementById('verdantChatbotToggle');

        if (!panel || !toggle) return;

        VerdantChatbot.isOpen = !VerdantChatbot.isOpen;
        panel.style.display = VerdantChatbot.isOpen ? 'block' : 'none';
        toggle.setAttribute('aria-expanded', VerdantChatbot.isOpen);

        if (VerdantChatbot.isOpen) {
            const input = document.getElementById('chatbotInput');
            if (input) input.focus();
        }
    }

    function toggleVoiceInput() {
        if (VerdantChatbot.isRecording) {
            VerdantChatbot.stopVoice();
        } else {
            VerdantChatbot.startVoice();
        }
    }

    function handleChatbotEnter(event) {
        if (event.key === 'Enter') {
            sendChatbotMessage();
        }
    }

    function sendChatbotMessage() {
        const input = document.getElementById('chatbotInput');
        if (input) {
            VerdantChatbot.processQuery(input.value);
        }
    }

    function quickAsk(question) {
        const input = document.getElementById('chatbotInput');
        if (input) {
            input.value = question;
            sendChatbotMessage();
        }
    }

    function executeSuggestion(action) {
        const actions = {
            'attendance': '<?php echo APP_URL; ?>/<?php echo $user_role; ?>/attendance.php',
            'grades': '<?php echo APP_URL; ?>/<?php echo $user_role; ?>/grades.php',
            'reports': '<?php echo APP_URL; ?>/<?php echo $user_role; ?>/reports.php',
            'calendar': '<?php echo APP_URL; ?>/<?php echo $user_role; ?>/calendar.php',
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
            VerdantChatbot.addMessage(`Executing: ${action}`, 'bot');
        }
    }

    // Keyboard shortcut (Ctrl + /)
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.key === '/') {
            e.preventDefault();
            toggleVerdantChatbot();
        }
        if (e.key === 'Escape' && VerdantChatbot.isOpen) {
            toggleVerdantChatbot();
        }
    });
</script>
