<?php

/**
 * School Management System Bot - AI Assistant Widget
 * Context-aware chatbot with role-based responses
 * Floating widget available on all pages
 */

// Get user context
$user_name = $_SESSION['full_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'guest';
$user_id = $_SESSION['user_id'] ?? 0;
?>

<!-- School Management System Bot Floating Widget -->
<div id="samsBot" class="sams-bot-widget">
    <button id="samsBotToggle" class="bot-toggle-btn" onclick="toggleSamsBot()" title="Open School Management System Assistant">
        <i class="fas fa-robot"></i>
        <span class="bot-pulse"></span>
    </button>

    <div id="samsBotPanel" class="bot-panel" style="display: none;">
        <div class="bot-header">
            <div class="bot-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="bot-info">
                <div class="bot-name">School Management System Assistant</div>
                <div class="bot-status">
                    <span class="status-dot"></span> Ready to Help
                </div>
            </div>
            <button onclick="toggleSamsBot()" class="bot-close" title="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="bot-context-bar">
            <i class="fas fa-user-circle"></i>
            <span><?php echo htmlspecialchars($user_name); ?> • <?php echo ucfirst($user_role); ?></span>
        </div>

        <div id="botMessages" class="bot-messages">
            <div class="bot-message bot">
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    <p>👋 Hi <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?>! I'm your School Management System Assistant.</p>
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
                    <p><small style="opacity: 0.7;">💡 Tip: Click quick actions below or type your question!</small></p>
                </div>
            </div>
        </div>

        <div class="bot-quick-actions">
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
                <button onclick="quickAsk('Summarize today\\'s attendance')" class="quick-btn">
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
                <button onclick="quickAsk('Show my children\\'s attendance')" class="quick-btn">
                    <i class="fas fa-child"></i> Attendance
                </button>
                <button onclick="quickAsk('Are there any pending fees?')" class="quick-btn">
                    <i class="fas fa-wallet"></i> Fee Status
                </button>
                <button onclick="quickAsk('How do I book a teacher meeting?')" class="quick-btn">
                    <i class="fas fa-calendar-check"></i> Book Meeting
                </button>
                <button onclick="quickAsk('Check children\\'s grades')" class="quick-btn">
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

        <div class="bot-input-area">
            <input type="text" id="botInput" placeholder="Ask me anything..." class="bot-input" onkeypress="handleBotEnter(event)">
            <button onclick="sendBotMessage()" class="bot-send-btn" title="Send message">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
    .sams-bot-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 10000;
        font-family: 'Inter', sans-serif;
    }

    .bot-toggle-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--cyber-cyan), var(--cyber-purple));
        border: none;
        color: white;
        font-size: 1.8rem;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0, 191, 255, 0.4);
        transition: all 0.3s ease;
        position: relative;
    }

    .bot-toggle-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 30px rgba(0, 191, 255, 0.6);
    }

    .bot-pulse {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 15px;
        height: 15px;
        background: var(--cyber-green);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.2);
            opacity: 0.7;
        }
    }

    .bot-panel {
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 400px;
        max-width: calc(100vw - 40px);
        height: 600px;
        max-height: calc(100vh - 120px);
        background: rgba(20, 20, 30, 0.95);
        backdrop-filter: blur(20px);
        border: 2px solid var(--cyber-cyan);
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

    .bot-header {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: linear-gradient(135deg, rgba(0, 191, 255, 0.2), rgba(138, 43, 226, 0.2));
        border-bottom: 1px solid var(--cyber-cyan);
        border-radius: 18px 18px 0 0;
    }

    .bot-avatar {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, var(--cyber-cyan), var(--cyber-purple));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .bot-info {
        flex: 1;
    }

    .bot-name {
        font-weight: bold;
        color: var(--cyber-cyan);
        font-size: 1.1rem;
    }

    .bot-status {
        font-size: 0.85rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background: var(--cyber-green);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    .bot-close {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 1.2rem;
        cursor: pointer;
        padding: 5px;
        transition: color 0.3s;
    }

    .bot-close:hover {
        color: var(--cyber-red);
    }

    .bot-context-bar {
        padding: 10px 20px;
        background: rgba(0, 191, 255, 0.1);
        border-bottom: 1px solid rgba(0, 191, 255, 0.2);
        font-size: 0.85rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .bot-message {
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

    .bot-message.user {
        flex-direction: row-reverse;
    }

    .message-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--cyber-cyan), var(--cyber-purple));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }

    .bot-message.user .message-avatar {
        background: linear-gradient(135deg, var(--cyber-green), var(--cyber-blue));
    }

    .message-content {
        background: rgba(0, 191, 255, 0.1);
        padding: 12px 15px;
        border-radius: 15px;
        border: 1px solid rgba(0, 191, 255, 0.2);
        max-width: 75%;
        color: var(--text-body);
        line-height: 1.6;
    }

    .bot-message.user .message-content {
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

    .bot-quick-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding: 15px 20px;
        border-top: 1px solid rgba(0, 191, 255, 0.2);
    }

    .quick-btn {
        padding: 8px 12px;
        background: rgba(0, 191, 255, 0.1);
        border: 1px solid var(--cyber-cyan);
        border-radius: 8px;
        color: var(--cyber-cyan);
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .quick-btn:hover {
        background: rgba(0, 191, 255, 0.2);
        transform: translateY(-2px);
    }

    .bot-input-area {
        display: flex;
        gap: 10px;
        padding: 15px 20px;
        background: rgba(0, 0, 0, 0.3);
        border-top: 1px solid rgba(0, 191, 255, 0.2);
        border-radius: 0 0 18px 18px;
    }

    .bot-input {
        flex: 1;
        padding: 12px 15px;
        background: rgba(0, 191, 255, 0.05);
        border: 1px solid var(--cyber-cyan);
        border-radius: 25px;
        color: white;
        font-size: 0.95rem;
    }

    .bot-input:focus {
        outline: none;
        border-color: var(--cyber-cyan);
        box-shadow: 0 0 15px rgba(0, 191, 255, 0.2);
    }

    .bot-send-btn {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, var(--cyber-cyan), var(--cyber-purple));
        border: none;
        border-radius: 50%;
        color: white;
        cursor: pointer;
        transition: all 0.3s;
    }

    .bot-send-btn:hover {
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
        background: var(--cyber-cyan);
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

        0%,
        60%,
        100% {
            transform: translateY(0);
        }

        30% {
            transform: translateY(-10px);
        }
    }
</style>

<script>
    function toggleSamsBot() {
        const panel = document.getElementById('samsBotPanel');
        const isVisible = panel.style.display === 'block';
        panel.style.display = isVisible ? 'none' : 'block';

        if (!isVisible) {
            document.getElementById('botInput').focus();
        }
    }

    function quickAsk(question) {
        document.getElementById('botInput').value = question;
        sendBotMessage();
    }

    function handleBotEnter(event) {
        if (event.key === 'Enter') {
            sendBotMessage();
        }
    }

    async function sendBotMessage() {
        const input = document.getElementById('botInput');
        const message = input.value.trim();

        if (!message) return;

        // Add user message
        addBotMessage(message, 'user');
        input.value = '';

        // Show typing indicator
        const typingId = addTypingIndicator();

        try {
            // Send to AI API
            const response = await fetch('/attendance/api/sams-bot.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    user_role: '<?php echo $user_role; ?>',
                    user_id: '<?php echo $user_id; ?>'
                })
            });

            const data = await response.json();

            // Remove typing indicator
            removeTypingIndicator(typingId);

            // Add bot response
            if (data.success) {
                addBotMessage(data.response, 'bot');
            } else {
                addBotMessage('Sorry, I encountered an error. Please try again.', 'bot');
            }
        } catch (error) {
            removeTypingIndicator(typingId);
            addBotMessage('Sorry, I\'m having trouble connecting. Please check your internet and try again.', 'bot');
        }
    }

    function addBotMessage(content, type) {
        const messagesDiv = document.getElementById('botMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `bot-message ${type}`;

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
    }

    function addTypingIndicator() {
        const messagesDiv = document.getElementById('botMessages');
        const typingDiv = document.createElement('div');
        const id = 'typing-' + Date.now();
        typingDiv.id = id;
        typingDiv.className = 'bot-message bot';
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
    }

    function removeTypingIndicator(id) {
        const element = document.getElementById(id);
        if (element) element.remove();
    }
</script>