<?php
/**
 * Chatbot Singleton - Ensures only ONE chatbot instance per page
 * Include this file in footer instead of multiple chatbot includes
 */

// Prevent duplicate chatbot instances
if (!defined('CHATBOT_LOADED')) {
    define('CHATBOT_LOADED', true);

    // Only show chatbot for logged-in users
    if (isset($_SESSION['user_id'])) {
        $chatbot_user_name = $_SESSION['full_name'] ?? 'User';
        $chatbot_user_role = $_SESSION['role'] ?? 'student';
?>
<!-- Verdant AI Chatbot Singleton -->
<div id="verdant-chatbot-widget" class="chatbot-widget" style="display: none;">
    <div class="chatbot-container">
        <div class="chatbot-header">
            <div class="chatbot-title">
                <i class="fas fa-robot"></i>
                <span>Verdant AI Assistant</span>
            </div>
            <div class="chatbot-controls">
                <button class="chatbot-minimize" onclick="VerdantChatbot.minimize()" title="Minimize">
                    <i class="fas fa-minus"></i>
                </button>
                <button class="chatbot-close" onclick="VerdantChatbot.close()" title="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="chatbot-messages" id="chatbot-messages">
            <div class="chatbot-message bot">
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    Hello <?php echo htmlspecialchars($chatbot_user_name); ?>! 👋
                    I'm your Verdant AI assistant. How can I help you today?
                </div>
            </div>
        </div>
        <div class="chatbot-input-container">
            <input type="text" id="chatbot-input" class="chatbot-input"
                   placeholder="Type your message..."
                   onkeypress="if(event.key==='Enter')VerdantChatbot.send()">
            <button class="chatbot-send" onclick="VerdantChatbot.send()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<!-- Floating Chatbot Button -->
<button id="chatbot-fab" class="chatbot-fab" onclick="VerdantChatbot.toggle()" title="Chat with AI">
    <i class="fas fa-comments"></i>
    <span class="chatbot-fab-badge" id="chatbot-badge" style="display: none;">1</span>
</button>

<style>
.chatbot-fab {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #22C55E 0%, #00BFFF 100%);
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(34, 197, 94, 0.4);
    z-index: 9998;
    transition: all 0.3s ease;
}

.chatbot-fab:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 30px rgba(34, 197, 94, 0.6);
}

.chatbot-fab-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ef4444;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chatbot-widget {
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 380px;
    max-height: 500px;
    z-index: 9999;
}

.chatbot-container {
    background: rgba(10, 14, 39, 0.98);
    border: 1px solid rgba(0, 191, 255, 0.3);
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 480px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
}

.chatbot-header {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(0, 191, 255, 0.2));
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(0, 191, 255, 0.2);
}

.chatbot-title {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #22C55E;
    font-weight: 600;
}

.chatbot-title i {
    font-size: 1.2rem;
}

.chatbot-controls {
    display: flex;
    gap: 8px;
}

.chatbot-controls button {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: #9ca3af;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.chatbot-controls button:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.chatbot-message {
    display: flex;
    gap: 10px;
    align-items: flex-start;
}

.chatbot-message.user {
    flex-direction: row-reverse;
}

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #22C55E, #00BFFF);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
    flex-shrink: 0;
}

.chatbot-message.user .message-avatar {
    background: linear-gradient(135deg, #8B5CF6, #EC4899);
}

.message-content {
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid rgba(34, 197, 94, 0.2);
    padding: 10px 14px;
    border-radius: 12px;
    color: white;
    max-width: 80%;
    font-size: 0.9rem;
    line-height: 1.5;
}

.chatbot-message.user .message-content {
    background: rgba(139, 92, 246, 0.2);
    border-color: rgba(139, 92, 246, 0.3);
}

.chatbot-input-container {
    padding: 15px;
    border-top: 1px solid rgba(0, 191, 255, 0.2);
    display: flex;
    gap: 10px;
}

.chatbot-input {
    flex: 1;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(0, 191, 255, 0.3);
    border-radius: 25px;
    padding: 10px 18px;
    color: white;
    font-size: 0.9rem;
}

.chatbot-input:focus {
    outline: none;
    border-color: #22C55E;
    box-shadow: 0 0 10px rgba(34, 197, 94, 0.3);
}

.chatbot-send {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #22C55E, #00BFFF);
    border: none;
    color: white;
    cursor: pointer;
    transition: all 0.2s;
}

.chatbot-send:hover {
    transform: scale(1.05);
}

@media (max-width: 480px) {
    .chatbot-widget {
        right: 10px;
        left: 10px;
        width: auto;
        bottom: 80px;
    }

    .chatbot-fab {
        right: 20px;
        bottom: 20px;
    }
}
</style>

<script>
const VerdantChatbot = {
    isOpen: false,

    toggle() {
        this.isOpen = !this.isOpen;
        document.getElementById('verdant-chatbot-widget').style.display = this.isOpen ? 'block' : 'none';
        document.getElementById('chatbot-badge').style.display = 'none';
    },

    close() {
        this.isOpen = false;
        document.getElementById('verdant-chatbot-widget').style.display = 'none';
    },

    minimize() {
        this.close();
    },

    send() {
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        if (!message) return;

        // Add user message
        this.addMessage(message, 'user');
        input.value = '';

        // Show typing indicator
        this.showTyping();

        // Send to API
        fetch('<?php echo APP_URL ?? "/attendance"; ?>/api/chatbot.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                message: message,
                role: '<?php echo $chatbot_user_role; ?>'
            })
        })
        .then(res => res.json())
        .then(data => {
            this.hideTyping();
            this.addMessage(data.response || "I'm sorry, I couldn't process that request.", 'bot');
        })
        .catch(() => {
            this.hideTyping();
            this.addMessage("Sorry, I'm having trouble connecting. Please try again.", 'bot');
        });
    },

    addMessage(text, type) {
        const container = document.getElementById('chatbot-messages');
        const avatar = type === 'bot' ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user"></i>';

        container.innerHTML += `
            <div class="chatbot-message ${type}">
                <div class="message-avatar">${avatar}</div>
                <div class="message-content">${text}</div>
            </div>
        `;
        container.scrollTop = container.scrollHeight;
    },

    showTyping() {
        const container = document.getElementById('chatbot-messages');
        container.innerHTML += `
            <div class="chatbot-message bot" id="typing-indicator">
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-content"><em>Typing...</em></div>
            </div>
        `;
        container.scrollTop = container.scrollHeight;
    },

    hideTyping() {
        const typing = document.getElementById('typing-indicator');
        if (typing) typing.remove();
    }
};
</script>
<?php
    }
}
?>
