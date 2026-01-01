<!--
Verdant SMS - Chat Widget
WhatsApp/Telegram Clone - UI Component
Include this in any page to enable messaging
-->

<link rel="stylesheet" href="../assets/css/chat-widget.css">

<div id="chat-widget" class="chat-widget">
  <!-- Chat Toggle Button -->
  <button id="chat-toggle-btn" class="chat-toggle-btn" aria-label="Toggle Chat">
    <i class="fas fa-comments"></i>
    <span class="unread-badge" id="unread-count">0</span>
  </button>

  <!-- Chat Window -->
  <div id="chat-window" class="chat-window">
    <!-- Header -->
    <div class="chat-header">
      <div class="chat-header-left">
        <button id="back-to-conversations-btn" class="icon-btn">
          <i class="fas fa-arrow-left"></i>
        </button>
        <div class="chat-user-info">
          <h3 id="current-chat-name">Conversations</h3>
          <span id="current-chat-status" class="chat-status"></span>
        </div>
      </div>
      <div class="chat-header-right">
        <button id="voice-call-btn" class="icon-btn" title="Voice Call">
          <i class="fas fa-phone"></i>
        </button>
        <button id="video-call-btn" class="icon-btn" title="Video Call">
          <i class="fas fa-video"></i>
        </button>
        <button id="chat-settings-btn" class="icon-btn" title="Settings">
          <i class="fas fa-ellipsis-v"></i>
        </button>
        <button id="close-chat-btn" class="icon-btn" title="Close">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>

    <!-- Connection Status -->
    <div id="connection-status" class="connection-status offline">
      Connecting...
    </div>

    <!-- Conversations List -->
    <div id="conversations-list" class="conversations-list">
      <div class="conversations-search">
        <input type="search" id="conversation-search" placeholder="Search conversations...">
      </div>
      <div id="conversations-container" class="conversations-container">
        <!-- Conversations will be loaded here -->
        <div class="loading">Loading conversations...</div>
      </div>
    </div>

    <!-- Messages View -->
    <div id="messages-view" class="messages-view" style="display: none;">
      <!-- Messages Container -->
      <div id="messages-container" class="messages-container">
        <!-- Messages will appear here -->
      </div>

      <!-- Typing Indicator -->
      <div id="typing-indicator" class="typing-indicator" style="display: none;">
        Someone is typing...
      </div>

      <!-- Message Input -->
      <div class="message-input-container">
        <button id="attach-file-btn" class="icon-btn" title="Attach File">
          <i class="fas fa-paperclip"></i>
        </button>
        <input type="file" id="file-input" multiple hidden>

        <input
          type="text"
          id="message-input"
          placeholder="Type a message..."
          autocomplete="off">

        <button id="emoji-btn" class="icon-btn" title="Emoji">
          <i class="fas fa-smile"></i>
        </button>

        <button id="voice-record-btn" class="icon-btn" title="Hold to record voice note">
          <i class="fas fa-microphone"></i>
        </button>

        <button id="send-message-btn" class="icon-btn send-btn" title="Send">
          <i class="fas fa-paper-plane"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Video Call Modal -->
<div id="video-call-modal" class="video-call-modal" style="display: none;">
  <div class="video-call-container">
    <video id="remote-video" class="remote-video" autoplay></video>
    <video id="local-video" class="local-video" autoplay muted></video>

    <div class="call-info">
      <h3 id="call-status">Connecting...</h3>
      <p id="call-duration">00:00</p>
    </div>

    <div class="call-controls">
      <button id="toggle-mic-btn" class="call-control-btn">
        <i class="fas fa-microphone"></i>
      </button>
      <button id="toggle-camera-btn" class="call-control-btn">
        <i class="fas fa-video"></i>
      </button>
      <button id="end-call-btn" class="call-control-btn end-call">
        <i class="fas fa-phone-slash"></i>
      </button>
    </div>
  </div>
</div>

<!-- Reaction Picker -->
<div id="reaction-picker" class="reaction-picker" style="display: none;">
  <button onclick="chatClient.sendReaction(window.currentMessageId, '👍')">👍</button>
  <button onclick="chatClient.sendReaction(window.currentMessageId, '❤️')">❤️</button>
  <button onclick="chatClient.sendReaction(window.currentMessageId, '😂')">😂</button>
  <button onclick="chatClient.sendReaction(window.currentMessageId, '😮')">😮</button>
  <button onclick="chatClient.sendReaction(window.currentMessageId, '😢')">😢</button>
  <button onclick="chatClient.sendReaction(window.currentMessageId, '🙏')">🙏</button>
</div>

<script src="../assets/js/verdant-chat-client.js"></script>

<script>
  // Chat Widget UI Controls
  document.addEventListener('DOMContentLoaded', function() {
    const chatWidget = document.getElementById('chat-widget');
    const chatWindow = document.getElementById('chat-window');
    const chatToggleBtn = document.getElementById('chat-toggle-btn');
    const closeChatBtn = document.getElementById('close-chat-btn');
    const conversationsList = document.getElementById('conversations-list');
    const messagesView = document.getElementById('messages-view');
    const backBtn = document.getElementById('back-to-conversations-btn');
    const attachFileBtn = document.getElementById('attach-file-btn');
    const fileInput = document.getElementById('file-input');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-message-btn');

    // Toggle chat window
    chatToggleBtn.addEventListener('click', function() {
      chatWindow.classList.toggle('open');
    });

    // Close chat
    closeChatBtn.addEventListener('click', function() {
      chatWindow.classList.remove('open');
    });

    // Back to conversations
    backBtn.addEventListener('click', function() {
      conversationsList.style.display = 'flex';
      messagesView.style.display = 'none';
      backBtn.style.display = 'none';
    });

    // Attach file
    attachFileBtn.addEventListener('click', function() {
      fileInput.click();
    });

    // Send message on Enter
    messageInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendBtn.click();
      }
    });

    // Open conversation
    window.openConversation = function(conversationId, name, status) {
      if (chatClient) {
        chatClient.currentConversationId = conversationId;
      }

      conversationsList.style.display = 'none';
      messagesView.style.display = 'flex';
      backBtn.style.display = 'block';

      document.getElementById('current-chat-name').textContent = name;
      document.getElementById('current-chat-status').textContent = status || '';

      // Load messages for this conversation
      if (chatClient) {
        chatClient.loadMessages(conversationId);
      }
    };

    // Show reaction picker
    window.showReactionPicker = function(messageId) {
      window.currentMessageId = messageId;
      const picker = document.getElementById('reaction-picker');
      picker.style.display = 'flex';

      setTimeout(() => {
        picker.style.display = 'none';
      }, 5000);
    };
  });
</script>

<style>
  /* Quick inline styles - Move to separate CSS file for production */
  .chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
  }

  .chat-toggle-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00ff88, #00cc6a);
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(0, 255, 136, 0.4);
    transition: all 0.3s;
    position: relative;
  }

  .chat-toggle-btn:hover {
    transform: scale(1.1);
  }

  .unread-badge {
    position: absolute;
    top: 0;
    right: 0;
    background: #ff3366;
    color: white;
    font-size: 12px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 10px;
    display: none;
  }

  .unread-badge.has-unread {
    display: block;
  }

  .chat-window {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 400px;
    height: 600px;
    max-height: calc(100vh - 120px);
    background: rgba(10, 10, 30, 0.95);
    border: 1px solid rgba(0, 255, 136, 0.3);
    border-radius: 15px;
    display: none;
    flex-direction: column;
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 50px rgba(0, 255, 136, 0.2);
  }

  .chat-window.open {
    display: flex;
  }

  .chat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid rgba(0, 255, 136, 0.2);
    background: rgba(0, 255, 136, 0.1);
  }

  .chat-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .chat-header-right {
    display: flex;
    gap: 10px;
  }

  .icon-btn {
    background: none;
    border: none;
    color: #00ff88;
    cursor: pointer;
    font-size: 18px;
    padding: 5px;
    transition: all 0.3s;
  }

  .icon-btn:hover {
    color: #00cc6a;
    transform: scale(1.1);
  }

  .chat-user-info h3 {
    color: #00ff88;
    font-size: 16px;
    margin: 0;
  }

  .chat-status {
    color: #888;
    font-size: 12px;
  }

  .connection-status {
    padding: 5px;
    text-align: center;
    font-size: 12px;
    background: rgba(255, 51, 102, 0.2);
    color: #ff3366;
    display: none;
  }

  .connection-status.online {
    background: rgba(0, 255, 136, 0.2);
    color: #00ff88;
  }

  .conversations-list,
  .messages-view {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .conversations-search {
    padding: 10px;
    border-bottom: 1px solid rgba(0, 255, 136, 0.2);
  }

  .conversations-search input {
    width: 100%;
    padding: 10px;
    background: rgba(0, 255, 136, 0.05);
    border: 1px solid rgba(0, 255, 136, 0.2);
    border-radius: 5px;
    color: #fff;
  }

  .conversations-container {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
  }

  .messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .message {
    display: flex;
    flex-direction: column;
    max-width: 70%;
    animation: slideIn 0.3s;
  }

  .message.sent {
    align-self: flex-end;
  }

  .message.received {
    align-self: flex-start;
  }

  .message-content {
    background: rgba(0, 255, 136, 0.1);
    border: 1px solid rgba(0, 255, 136, 0.2);
    padding: 10px;
    border-radius: 10px;
    color: #fff;
  }

  .message.sent .message-content {
    background: linear-gradient(135deg, rgba(0, 255, 136, 0.2), rgba(0, 204, 106, 0.2));
  }

  .message-time {
    font-size: 10px;
    color: #888;
    margin-top: 5px;
  }

  .message-input-container {
    display: flex;
    gap: 10px;
    padding: 15px;
    border-top: 1px solid rgba(0, 255, 136, 0.2);
    background: rgba(0, 255, 136, 0.05);
  }

  .message-input-container input[type="text"] {
    flex: 1;
    padding: 10px;
    background: rgba(0, 255, 136, 0.05);
    border: 1px solid rgba(0, 255, 136, 0.2);
    border-radius: 20px;
    color: #fff;
  }

  .send-btn {
    background: linear-gradient(135deg, #00ff88, #00cc6a);
    color: white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Mobile responsive */
  @media (max-width: 768px) {
    .chat-window {
      width: calc(100vw - 40px);
      height: calc(100vh - 120px);
      right: 20px;
    }
  }
</style>
