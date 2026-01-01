/**
 * Verdant SMS - Real-Time Chat Client
 * WhatsApp/Telegram Clone - Frontend JavaScript
 * Features: Text, Voice Notes, Video Calling, File Sharing, Reactions
 */

class VerdantChatClient {
  constructor() {
    this.ws = null;
    this.userId = null;
    this.userName = null;
    this.userRole = null;
    this.currentConversationId = null;
    this.isConnected = false;
    this.reconnectAttempts = 0;
    this.maxReconnectAttempts = 5;
    this.mediaRecorder = null;
    this.audioChunks = [];
    this.peerConnections = {};

    this.initializeEventListeners();
  }

  /**
   * Connect to WebSocket server
   */
  connect(userId, userName, userRole, token) {
    this.userId = userId;
    this.userName = userName;
    this.userRole = userRole;

    try {
      this.ws = new WebSocket("ws://localhost:8080");

      this.ws.onopen = () => {
        console.log("✅ Connected to chat server");
        this.isConnected = true;
        this.reconnectAttempts = 0;

        // Authenticate
        this.send({
          type: "auth",
          user_id: this.userId,
          name: this.userName,
          role: this.userRole,
          token: token,
        });

        this.updateConnectionStatus("online");
      };

      this.ws.onmessage = (event) => {
        try {
          const data = JSON.parse(event.data);
          this.handleMessage(data);
        } catch (error) {
          console.error("❌ Message parse error:", error);
        }
      };

      this.ws.onerror = (error) => {
        console.error("❌ WebSocket error:", error);
        this.updateConnectionStatus("error");
      };

      this.ws.onclose = () => {
        console.log("🔌 Disconnected from chat server");
        this.isConnected = false;
        this.updateConnectionStatus("offline");
        this.attemptReconnect();
      };
    } catch (error) {
      console.error("❌ Connection error:", error);
      this.updateConnectionStatus("error");
    }
  }

  /**
   * Attempt reconnection with exponential backoff
   */
  attemptReconnect() {
    if (this.reconnectAttempts < this.maxReconnectAttempts) {
      this.reconnectAttempts++;
      const delay = Math.min(1000 * Math.pow(2, this.reconnectAttempts), 30000);

      console.log(
        `🔄 Reconnecting in ${delay / 1000}s (attempt ${
          this.reconnectAttempts
        }/${this.maxReconnectAttempts})`
      );

      setTimeout(() => {
        this.connect(
          this.userId,
          this.userName,
          this.userRole,
          localStorage.getItem("chat_token")
        );
      }, delay);
    } else {
      console.error("❌ Max reconnection attempts reached");
      this.showNotification(
        "Connection lost. Please refresh the page.",
        "error"
      );
    }
  }

  /**
   * Send data to server
   */
  send(data) {
    if (this.ws && this.ws.readyState === WebSocket.OPEN) {
      this.ws.send(JSON.stringify(data));
    } else {
      console.error("❌ WebSocket not connected");
      this.showNotification("Connection lost. Reconnecting...", "warning");
    }
  }

  /**
   * Handle incoming messages
   */
  handleMessage(data) {
    switch (data.type) {
      case "auth_success":
        this.handleAuthSuccess(data);
        break;

      case "auth_error":
        this.handleAuthError(data);
        break;

      case "message":
        this.handleNewMessage(data);
        break;

      case "voice_note":
        this.handleVoiceNote(data);
        break;

      case "video_call":
        this.handleVideoCall(data);
        break;

      case "file":
        this.handleFileMessage(data);
        break;

      case "typing":
        this.handleTyping(data);
        break;

      case "read_receipt":
        this.handleReadReceipt(data);
        break;

      case "reaction":
        this.handleReaction(data);
        break;

      case "user_status":
        this.handleUserStatus(data);
        break;

      default:
        console.log("Unknown message type:", data.type);
    }
  }

  /**
   * Handle successful authentication
   */
  handleAuthSuccess(data) {
    console.log("✅ Authenticated successfully");
    localStorage.setItem("chat_token", data.token);
    this.loadConversations();
  }

  /**
   * Handle authentication error
   */
  handleAuthError(data) {
    console.error("❌ Authentication failed:", data.message);
    this.showNotification(
      "Authentication failed. Please login again.",
      "error"
    );
  }

  /**
   * Send text message
   */
  sendMessage(conversationId, content, replyTo = null) {
    if (!content.trim()) return;

    this.send({
      type: "message",
      conversation_id: conversationId,
      content: content.trim(),
      reply_to: replyTo,
    });

    // Clear input
    document.getElementById("message-input").value = "";
  }

  /**
   * Handle new message
   */
  handleNewMessage(data) {
    this.appendMessage(data);

    // Play notification sound if not current conversation
    if (data.conversation_id !== this.currentConversationId) {
      this.playNotificationSound();
      this.showNotification(`${data.sender_name}: ${data.content}`, "info");
    }

    // Update conversation list
    this.updateConversationPreview(data);

    // Send read receipt if message is in current conversation
    if (data.conversation_id === this.currentConversationId) {
      this.sendReadReceipt(data.message_id);
    }
  }

  /**
   * Start recording voice note
   */
  async startVoiceRecording() {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });

      this.mediaRecorder = new MediaRecorder(stream);
      this.audioChunks = [];

      this.mediaRecorder.ondataavailable = (event) => {
        this.audioChunks.push(event.data);
      };

      this.mediaRecorder.onstop = async () => {
        const audioBlob = new Blob(this.audioChunks, { type: "audio/webm" });
        await this.sendVoiceNote(audioBlob);
        stream.getTracks().forEach((track) => track.stop());
      };

      this.mediaRecorder.start();
      this.updateRecordingUI(true);
    } catch (error) {
      console.error("❌ Microphone access denied:", error);
      this.showNotification("Microphone access denied", "error");
    }
  }

  /**
   * Stop recording voice note
   */
  stopVoiceRecording() {
    if (this.mediaRecorder && this.mediaRecorder.state === "recording") {
      this.mediaRecorder.stop();
      this.updateRecordingUI(false);
    }
  }

  /**
   * Send voice note to server
   */
  async sendVoiceNote(audioBlob) {
    const reader = new FileReader();

    reader.onload = () => {
      const base64Audio = reader.result.split(",")[1];

      this.send({
        type: "voice_note",
        conversation_id: this.currentConversationId,
        audio_data: base64Audio,
        duration: Math.round(audioBlob.size / 16000), // Estimate duration
      });
    };

    reader.readAsDataURL(audioBlob);
  }

  /**
   * Handle voice note message
   */
  handleVoiceNote(data) {
    this.appendVoiceNote(data);
    this.updateConversationPreview(data);
  }

  /**
   * Initiate video call
   */
  initiateVideoCall(conversationId, callType = "video") {
    this.send({
      type: "video_call",
      action: "initiate",
      conversation_id: conversationId,
      call_type: callType,
    });

    this.showVideoCallModal("outgoing", callType);
  }

  /**
   * Handle video call events
   */
  handleVideoCall(data) {
    switch (data.action) {
      case "incoming":
        this.showIncomingCallModal(data);
        break;

      case "accepted":
        this.startWebRTCCall(data);
        break;

      case "declined":
        this.handleCallDeclined(data);
        break;

      case "ended":
        this.handleCallEnded(data);
        break;

      case "offer":
        this.handleWebRTCOffer(data);
        break;

      case "answer":
        this.handleWebRTCAnswer(data);
        break;

      case "ice_candidate":
        this.handleICECandidate(data);
        break;
    }
  }

  /**
   * Start WebRTC call
   */
  async startWebRTCCall(data) {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({
        video: data.call_type === "video",
        audio: true,
      });

      const peerConnection = new RTCPeerConnection({
        iceServers: [{ urls: "stun:stun.l.google.com:19302" }],
      });

      // Add local stream
      stream.getTracks().forEach((track) => {
        peerConnection.addTrack(track, stream);
      });

      // Display local video
      document.getElementById("local-video").srcObject = stream;

      // Handle remote stream
      peerConnection.ontrack = (event) => {
        document.getElementById("remote-video").srcObject = event.streams[0];
      };

      // Handle ICE candidates
      peerConnection.onicecandidate = (event) => {
        if (event.candidate) {
          this.send({
            type: "video_call",
            action: "ice_candidate",
            call_id: data.call_id,
            candidate: event.candidate,
          });
        }
      };

      // Create and send offer
      const offer = await peerConnection.createOffer();
      await peerConnection.setLocalDescription(offer);

      this.send({
        type: "video_call",
        action: "offer",
        call_id: data.call_id,
        offer: offer,
      });

      this.peerConnections[data.call_id] = peerConnection;
    } catch (error) {
      console.error("❌ WebRTC error:", error);
      this.showNotification("Camera/microphone access denied", "error");
    }
  }

  /**
   * Upload and send file
   */
  async uploadFile(file) {
    if (file.size > 50 * 1024 * 1024) {
      // 50MB limit
      this.showNotification("File too large (max 50MB)", "error");
      return;
    }

    const reader = new FileReader();

    reader.onload = () => {
      const base64File = reader.result.split(",")[1];

      this.send({
        type: "file",
        conversation_id: this.currentConversationId,
        file_data: base64File,
        file_name: file.name,
        file_size: file.size,
        mime_type: file.type,
      });

      this.showNotification("Uploading file...", "info");
    };

    reader.readAsDataURL(file);
  }

  /**
   * Handle file message
   */
  handleFileMessage(data) {
    this.appendFileMessage(data);
    this.updateConversationPreview(data);
  }

  /**
   * Send typing indicator
   */
  sendTypingIndicator() {
    if (!this.typingTimeout) {
      this.send({
        type: "typing",
        conversation_id: this.currentConversationId,
      });
    }

    clearTimeout(this.typingTimeout);
    this.typingTimeout = setTimeout(() => {
      this.typingTimeout = null;
    }, 3000);
  }

  /**
   * Handle typing indicator
   */
  handleTyping(data) {
    const typingIndicator = document.getElementById("typing-indicator");
    if (
      typingIndicator &&
      data.conversation_id === this.currentConversationId
    ) {
      typingIndicator.textContent = `${data.sender_name} is typing...`;
      typingIndicator.style.display = "block";

      setTimeout(() => {
        typingIndicator.style.display = "none";
      }, 3000);
    }
  }

  /**
   * Send read receipt
   */
  sendReadReceipt(messageId) {
    this.send({
      type: "read_receipt",
      message_id: messageId,
    });
  }

  /**
   * Handle read receipt
   */
  handleReadReceipt(data) {
    const messageElement = document.querySelector(
      `[data-message-id="${data.message_id}"]`
    );
    if (messageElement) {
      const checkmark = messageElement.querySelector(".read-status");
      if (checkmark) {
        checkmark.classList.add("read");
        checkmark.innerHTML = "✓✓";
      }
    }
  }

  /**
   * Send reaction to message
   */
  sendReaction(messageId, emoji) {
    this.send({
      type: "reaction",
      message_id: messageId,
      emoji: emoji,
    });
  }

  /**
   * Handle reaction
   */
  handleReaction(data) {
    const messageElement = document.querySelector(
      `[data-message-id="${data.message_id}"]`
    );
    if (messageElement) {
      this.addReactionToMessage(messageElement, data);
    }
  }

  /**
   * Handle user status update
   */
  handleUserStatus(data) {
    const userElement = document.querySelector(
      `[data-user-id="${data.user_id}"]`
    );
    if (userElement) {
      const statusIndicator = userElement.querySelector(".status-indicator");
      if (statusIndicator) {
        statusIndicator.className = `status-indicator ${data.status}`;
      }
    }
  }

  /**
   * Initialize event listeners
   */
  initializeEventListeners() {
    // Message send
    document.addEventListener("click", (e) => {
      if (e.target.id === "send-message-btn") {
        const input = document.getElementById("message-input");
        this.sendMessage(this.currentConversationId, input.value);
      }
    });

    // Voice record
    document.addEventListener("mousedown", (e) => {
      if (e.target.id === "voice-record-btn") {
        this.startVoiceRecording();
      }
    });

    document.addEventListener("mouseup", (e) => {
      if (e.target.id === "voice-record-btn") {
        this.stopVoiceRecording();
      }
    });

    // File upload
    document.addEventListener("change", (e) => {
      if (e.target.id === "file-input") {
        const files = e.target.files;
        for (let file of files) {
          this.uploadFile(file);
        }
      }
    });

    // Typing indicator
    document.addEventListener("input", (e) => {
      if (e.target.id === "message-input") {
        this.sendTypingIndicator();
      }
    });

    // Video call
    document.addEventListener("click", (e) => {
      if (e.target.id === "video-call-btn") {
        this.initiateVideoCall(this.currentConversationId, "video");
      }

      if (e.target.id === "voice-call-btn") {
        this.initiateVideoCall(this.currentConversationId, "audio");
      }
    });
  }

  /**
   * UI Helper Methods
   */
  appendMessage(data) {
    const messagesContainer = document.getElementById("messages-container");
    if (!messagesContainer) return;

    const messageDiv = document.createElement("div");
    messageDiv.className = `message ${
      data.sender_id === this.userId ? "sent" : "received"
    }`;
    messageDiv.dataset.messageId = data.message_id;

    messageDiv.innerHTML = `
            <div class="message-content">
                <p>${this.escapeHtml(data.content)}</p>
                <span class="message-time">${this.formatTime(
                  data.created_at
                )}</span>
                <span class="read-status">✓</span>
            </div>
            <div class="message-actions">
                <button onclick="chatClient.showReactionPicker(${
                  data.message_id
                })">😊</button>
                <button onclick="chatClient.replyToMessage(${
                  data.message_id
                })">↩️</button>
            </div>
        `;

    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }

  appendVoiceNote(data) {
    const messagesContainer = document.getElementById("messages-container");
    if (!messagesContainer) return;

    const messageDiv = document.createElement("div");
    messageDiv.className = `message voice-note ${
      data.sender_id === this.userId ? "sent" : "received"
    }`;

    messageDiv.innerHTML = `
            <div class="voice-note-player">
                <button onclick="chatClient.playVoiceNote('${
                  data.file_path
                }')">▶️</button>
                <div class="waveform"></div>
                <span class="duration">${data.duration}s</span>
            </div>
            <span class="message-time">${this.formatTime(
              data.created_at
            )}</span>
        `;

    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }

  appendFileMessage(data) {
    const messagesContainer = document.getElementById("messages-container");
    if (!messagesContainer) return;

    const messageDiv = document.createElement("div");
    messageDiv.className = `message file ${
      data.sender_id === this.userId ? "sent" : "received"
    }`;

    const fileIcon = this.getFileIcon(data.mime_type);

    messageDiv.innerHTML = `
            <div class="file-message">
                <div class="file-icon">${fileIcon}</div>
                <div class="file-info">
                    <p class="file-name">${this.escapeHtml(data.file_name)}</p>
                    <p class="file-size">${this.formatFileSize(
                      data.file_size
                    )}</p>
                </div>
                <a href="/attendance/${
                  data.file_path
                }" download class="download-btn">⬇️</a>
            </div>
            <span class="message-time">${this.formatTime(
              data.created_at
            )}</span>
        `;

    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }

  updateConnectionStatus(status) {
    const statusElement = document.getElementById("connection-status");
    if (statusElement) {
      statusElement.className = `connection-status ${status}`;
      statusElement.textContent = status.toUpperCase();
    }
  }

  updateRecordingUI(isRecording) {
    const recordBtn = document.getElementById("voice-record-btn");
    if (recordBtn) {
      recordBtn.classList.toggle("recording", isRecording);
      recordBtn.innerHTML = isRecording ? "⏹️" : "🎤";
    }
  }

  showNotification(message, type = "info") {
    // You can use your existing notification system
    console.log(`[${type.toUpperCase()}] ${message}`);
  }

  playNotificationSound() {
    const audio = new Audio("/attendance/assets/sounds/notification.mp3");
    audio.play().catch((e) => console.log("Sound play failed:", e));
  }

  escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }

  formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleTimeString("en-US", {
      hour: "2-digit",
      minute: "2-digit",
    });
  }

  formatFileSize(bytes) {
    if (bytes < 1024) return bytes + " B";
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
    return (bytes / (1024 * 1024)).toFixed(1) + " MB";
  }

  getFileIcon(mimeType) {
    if (mimeType.startsWith("image/")) return "🖼️";
    if (mimeType.startsWith("video/")) return "🎥";
    if (mimeType.startsWith("audio/")) return "🎵";
    if (mimeType.includes("pdf")) return "📄";
    return "📎";
  }
}

// Global instance
let chatClient = null;

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
  // Get user info from session/page
  const userId = document.body.dataset.userId;
  const userName = document.body.dataset.userName;
  const userRole = document.body.dataset.userRole;
  const token = localStorage.getItem("chat_token") || "";

  if (userId && userName) {
    chatClient = new VerdantChatClient();
    chatClient.connect(userId, userName, userRole, token);
  }
});
