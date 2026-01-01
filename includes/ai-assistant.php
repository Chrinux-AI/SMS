<?php
/**
 * Verdant AI Learning Assistant - Floating Chatbot Component
 * Include this on every page for AI assistance
 */

// Get user context
$userRole = $_SESSION['role'] ?? 'visitor';
$userName = $_SESSION['full_name'] ?? 'Guest';
$userClass = $_SESSION['class'] ?? '';

// Role-specific greeting
$greetings = [
    'student' => "Hi! I'm Verdant AI. Ask me anything about your lessons, homework, or exams. I follow the NERDC curriculum!",
    'teacher' => "Hello, Teacher! I can help with lesson plans, quizzes, grading tips, and NERDC curriculum alignment.",
    'parent' => "Welcome! I can explain your child's progress, suggest home study tips, and answer education questions.",
    'admin' => "Hi Admin! I can help with analytics, reports, and system insights.",
    'visitor' => "Welcome to Verdant SMS! Ask me anything about our AI-powered school management system."
];

$greeting = $greetings[$userRole] ?? $greetings['visitor'];
?>

<!-- AI Learning Assistant Styles -->
<style>
.ai-assistant {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: 'Inter', -apple-system, sans-serif;
}

.ai-trigger {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00FF87, #00D4FF);
    border: none;
    cursor: pointer;
    box-shadow: 0 8px 32px rgba(0, 255, 135, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: #000;
    transition: all 0.3s ease;
    animation: pulse-glow 2s infinite;
}

.ai-trigger:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 40px rgba(0, 255, 135, 0.6);
}

@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 8px 32px rgba(0, 255, 135, 0.4); }
    50% { box-shadow: 0 8px 48px rgba(0, 212, 255, 0.6); }
}

.ai-chat-window {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 380px;
    max-width: calc(100vw - 40px);
    height: 500px;
    max-height: calc(100vh - 120px);
    background: #0D1117;
    border: 1px solid rgba(0, 212, 255, 0.3);
    border-radius: 20px;
    display: none;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(20px);
}

.ai-chat-window.active {
    display: flex;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.ai-chat-header {
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, rgba(0, 255, 135, 0.15), rgba(0, 212, 255, 0.1));
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ai-chat-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.ai-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00FF87, #00D4FF);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #000;
}

.ai-chat-header h4 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #F3F4F6;
    margin: 0;
}

.ai-chat-header p {
    font-size: 0.75rem;
    color: #9CA3AF;
    margin: 0;
}

.ai-close-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #9CA3AF;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.ai-close-btn:hover {
    background: #FF4757;
    border-color: #FF4757;
    color: #fff;
}

.ai-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.ai-message {
    max-width: 85%;
    padding: 0.85rem 1rem;
    border-radius: 16px;
    font-size: 0.9rem;
    line-height: 1.5;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.ai-message.ai {
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.15), rgba(168, 85, 247, 0.1));
    border: 1px solid rgba(0, 212, 255, 0.2);
    color: #E5E7EB;
    align-self: flex-start;
    border-bottom-left-radius: 4px;
}

.ai-message.user {
    background: #00FF87;
    color: #000;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
}

.ai-typing {
    display: flex;
    gap: 4px;
    padding: 1rem;
    align-self: flex-start;
}

.ai-typing span {
    width: 8px;
    height: 8px;
    background: #00D4FF;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.ai-typing span:nth-child(2) { animation-delay: 0.2s; }
.ai-typing span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-10px); }
}

.ai-chat-input {
    padding: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    gap: 0.5rem;
}

.ai-chat-input input {
    flex: 1;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    color: #F3F4F6;
    font-size: 0.9rem;
    outline: none;
    transition: border-color 0.2s;
}

.ai-chat-input input:focus {
    border-color: #00D4FF;
}

.ai-chat-input input::placeholder {
    color: #6B7280;
}

.ai-input-actions {
    display: flex;
    gap: 0.35rem;
}

.ai-input-btn {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    transition: all 0.2s;
}

.ai-voice-btn {
    background: rgba(168, 85, 247, 0.2);
    color: #A855F7;
}

.ai-voice-btn:hover {
    background: #A855F7;
    color: #fff;
}

.ai-voice-btn.recording {
    background: #FF4757;
    color: #fff;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.ai-send-btn {
    background: linear-gradient(135deg, #00FF87, #00D4FF);
    color: #000;
}

.ai-send-btn:hover {
    transform: scale(1.05);
}

.ai-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.ai-suggestion {
    padding: 0.4rem 0.75rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    font-size: 0.75rem;
    color: #9CA3AF;
    cursor: pointer;
    transition: all 0.2s;
}

.ai-suggestion:hover {
    background: rgba(0, 212, 255, 0.15);
    border-color: #00D4FF;
    color: #00D4FF;
}

/* Mobile responsive */
@media (max-width: 480px) {
    .ai-chat-window {
        width: calc(100vw - 20px);
        right: 10px;
        bottom: 80px;
        height: calc(100vh - 100px);
    }

    .ai-trigger {
        width: 54px;
        height: 54px;
        right: 10px;
        bottom: 10px;
    }
}

/* Theme support */
[data-theme="light"] .ai-chat-window {
    background: #FFFFFF;
    border-color: rgba(0, 0, 0, 0.1);
}

[data-theme="light"] .ai-message.ai {
    background: #F3F4F6;
    border-color: #E5E7EB;
    color: #1F2937;
}
</style>

<!-- AI Learning Assistant HTML -->
<div class="ai-assistant">
    <!-- Trigger Button -->
    <button class="ai-trigger" id="aiTrigger" title="Verdant AI Assistant">
        <i class="fas fa-leaf"></i>
    </button>

    <!-- Chat Window -->
    <div class="ai-chat-window" id="aiChatWindow">
        <div class="ai-chat-header">
            <div class="ai-chat-header-left">
                <div class="ai-avatar">🌿</div>
                <div>
                    <h4>Verdant AI</h4>
                    <p>NERDC-aligned Learning Assistant</p>
                </div>
            </div>
            <button class="ai-close-btn" id="aiClose">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="ai-chat-messages" id="aiMessages">
            <div class="ai-message ai">
                <?= htmlspecialchars($greeting) ?>
            </div>
        </div>

        <div class="ai-suggestions" id="aiSuggestions">
            <?php if ($userRole === 'student'): ?>
                <span class="ai-suggestion" data-prompt="Help me with my homework">📚 Homework help</span>
                <span class="ai-suggestion" data-prompt="Explain photosynthesis">🌱 Explain topic</span>
                <span class="ai-suggestion" data-prompt="Give me a math quiz">📝 Practice quiz</span>
            <?php elseif ($userRole === 'teacher'): ?>
                <span class="ai-suggestion" data-prompt="Create a lesson plan">📋 Lesson plan</span>
                <span class="ai-suggestion" data-prompt="Generate quiz questions">❓ Quiz questions</span>
                <span class="ai-suggestion" data-prompt="NERDC curriculum help">📖 Curriculum</span>
            <?php elseif ($userRole === 'parent'): ?>
                <span class="ai-suggestion" data-prompt="How is my child doing?">📊 Progress report</span>
                <span class="ai-suggestion" data-prompt="Home study tips">🏠 Study tips</span>
            <?php else: ?>
                <span class="ai-suggestion" data-prompt="What is Verdant SMS?">ℹ️ About Verdant</span>
                <span class="ai-suggestion" data-prompt="Show me features">✨ Features</span>
                <span class="ai-suggestion" data-prompt="Pricing plans">💰 Pricing</span>
            <?php endif; ?>
        </div>

        <div class="ai-chat-input">
            <input type="text" id="aiInput" placeholder="Ask me anything..." autocomplete="off">
            <div class="ai-input-actions">
                <button class="ai-input-btn ai-voice-btn" id="aiVoiceBtn" title="Voice input">
                    <i class="fas fa-microphone"></i>
                </button>
                <button class="ai-input-btn ai-send-btn" id="aiSendBtn" title="Send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- AI Assistant JavaScript -->
<script>
(function() {
    const trigger = document.getElementById('aiTrigger');
    const chatWindow = document.getElementById('aiChatWindow');
    const closeBtn = document.getElementById('aiClose');
    const messagesContainer = document.getElementById('aiMessages');
    const input = document.getElementById('aiInput');
    const sendBtn = document.getElementById('aiSendBtn');
    const voiceBtn = document.getElementById('aiVoiceBtn');
    const suggestions = document.getElementById('aiSuggestions');

    let isRecording = false;
    let recognition = null;

    // Toggle chat window
    trigger.addEventListener('click', () => {
        chatWindow.classList.toggle('active');
        if (chatWindow.classList.contains('active')) {
            input.focus();
        }
    });

    closeBtn.addEventListener('click', () => {
        chatWindow.classList.remove('active');
    });

    // Send message
    function sendMessage(text) {
        if (!text.trim()) return;

        // Add user message
        addMessage(text, 'user');
        input.value = '';

        // Hide suggestions after first message
        suggestions.style.display = 'none';

        // Show typing indicator
        showTyping();

        // Send to AI endpoint - use absolute path from root
        const basePath = window.location.pathname.split('/attendance')[0] + '/attendance';
        fetch(basePath + '/api/ai-chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                message: text,
                role: '<?= $userRole ?>',
                class: '<?= $userClass ?>'
            })
        })
        .then(res => res.json())
        .then(data => {
            hideTyping();
            addMessage(data.response || 'Sorry, I couldn\'t process that. Please try again.', 'ai');
        })
        .catch(() => {
            hideTyping();
            addMessage('I\'m having trouble connecting. Please check your internet or try again later.', 'ai');
        });
    }

    function addMessage(text, type) {
        const msg = document.createElement('div');
        msg.className = 'ai-message ' + type;
        msg.textContent = text;
        messagesContainer.appendChild(msg);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function showTyping() {
        const typing = document.createElement('div');
        typing.className = 'ai-typing';
        typing.id = 'aiTyping';
        typing.innerHTML = '<span></span><span></span><span></span>';
        messagesContainer.appendChild(typing);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function hideTyping() {
        const typing = document.getElementById('aiTyping');
        if (typing) typing.remove();
    }

    // Send button click
    sendBtn.addEventListener('click', () => sendMessage(input.value));

    // Enter key
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage(input.value);
    });

    // Suggestion clicks
    suggestions.querySelectorAll('.ai-suggestion').forEach(btn => {
        btn.addEventListener('click', () => {
            sendMessage(btn.dataset.prompt);
        });
    });

    // Voice input (Web Speech API) with punctuation recognition
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-NG'; // Nigerian English

        // Add punctuation to transcript
        function addPunctuation(text) {
            if (!text) return text;

            // Trim and capitalize first letter
            text = text.trim();
            text = text.charAt(0).toUpperCase() + text.slice(1);

            // Question word patterns
            const questionWords = /^(what|who|where|when|why|how|which|is|are|do|does|did|can|could|would|will|shall|should|have|has|had)/i;
            const questionPhrases = /(what is|how do|can you|could you|would you|will you|do you|are you|is this|is there|is it|where is|who is|when is|why is|how is|what are|how can)/i;

            // List separators
            text = text.replace(/\b(first|second|third|then|next|also|and|finally)\b/gi, (m) => ', ' + m);
            text = text.replace(/^,\s*/g, ''); // Remove leading comma
            text = text.replace(/,\s*,/g, ','); // Remove double commas

            // Add appropriate ending punctuation
            if (!text.match(/[.?!,]$/)) {
                if (questionWords.test(text) || questionPhrases.test(text) || text.includes('please explain') || text.includes('can you')) {
                    text += '?';
                } else {
                    text += '.';
                }
            }

            return text;
        }

        recognition.onresult = (event) => {
            let transcript = event.results[0][0].transcript;
            transcript = addPunctuation(transcript);
            input.value = transcript;
            sendMessage(transcript);
        };

        recognition.onend = () => {
            isRecording = false;
            voiceBtn.classList.remove('recording');
            voiceBtn.innerHTML = '<i class="fas fa-microphone"></i>';
        };

        recognition.onerror = (event) => {
            isRecording = false;
            voiceBtn.classList.remove('recording');
            voiceBtn.innerHTML = '<i class="fas fa-microphone"></i>';
            if (event.error !== 'no-speech') {
                addMessage('🎤 Voice not recognized. Please try again or type your question.', 'ai');
            }
        };

        voiceBtn.addEventListener('click', () => {
            if (isRecording) {
                recognition.stop();
                voiceBtn.innerHTML = '<i class="fas fa-microphone"></i>';
            } else {
                recognition.start();
                isRecording = true;
                voiceBtn.classList.add('recording');
                voiceBtn.innerHTML = '<i class="fas fa-stop"></i>';
            }
        });
    } else {
        voiceBtn.style.display = 'none';
    }
})();
</script>
