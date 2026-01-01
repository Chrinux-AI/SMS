/**
 * Enhanced Chat System JavaScript
 * WhatsApp/Telegram-style messaging with voice notes and calling
 */

class VerdantChat {
    constructor() {
        this.currentConversationId = null;
        this.currentUserId = null;
        this.pollingInterval = null;
        this.typingTimeout = null;
        this.replyToMessageId = null;
        this.selectedFiles = [];
        this.mediaRecorder = null;
        this.audioChunks = [];
        this.isRecording = false;
        this.voiceNoteDuration = 0;
        this.voiceNoteInterval = null;
        this.localStream = null;
        this.peerConnection = null;
        this.init();
    }

    init() {
        this.currentUserId = parseInt(document.getElementById('currentUserId')?.value || 0);
        this.loadConversations();
        this.startRealTimeUpdates(); // Use SSE instead of polling
        this.updateOnlineStatus();
        setInterval(() => this.updateOnlineStatus(), 30000);
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Voice note button
        const voiceBtn = document.getElementById('voiceNoteBtn');
        if (voiceBtn) {
            voiceBtn.addEventListener('mousedown', () => this.startVoiceRecording());
            voiceBtn.addEventListener('mouseup', () => this.stopVoiceRecording());
            voiceBtn.addEventListener('mouseleave', () => this.stopVoiceRecording());
            voiceBtn.addEventListener('touchstart', (e) => {
                e.preventDefault();
                this.startVoiceRecording();
            });
            voiceBtn.addEventListener('touchend', (e) => {
                e.preventDefault();
                this.stopVoiceRecording();
            });
        }

        // Call buttons
        const voiceCallBtn = document.getElementById('voiceCallBtn');
        const videoCallBtn = document.getElementById('videoCallBtn');
        if (voiceCallBtn) voiceCallBtn.addEventListener('click', () => this.initiateCall('voice'));
        if (videoCallBtn) videoCallBtn.addEventListener('click', () => this.initiateCall('video'));

        // Message input
        const messageInput = document.getElementById('messageInput');
        if (messageInput) {
            messageInput.addEventListener('input', () => this.handleTyping());
            messageInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });
        }
    }

    async loadConversations() {
        try {
            const response = await fetch(`${APP_URL}/api/chat/conversations.php?action=list`);
            const data = await response.json();

            if (data.success) {
                this.displayConversations(data.conversations);
            }
        } catch (error) {
            console.error('Error loading conversations:', error);
        }
    }

    displayConversations(conversations) {
        const list = document.getElementById('conversationsList');
        if (!list) return;

        if (conversations.length === 0) {
            list.innerHTML = '<div style="padding: 20px; text-align: center; color: #6b7280;">No conversations yet. Start a new chat!</div>';
            return;
        }

        list.innerHTML = conversations.map(conv => {
            const name = conv.conversation_type === 'direct' 
                ? (conv.other_user ? `${conv.other_user.first_name} ${conv.other_user.last_name}` : 'Unknown')
                : conv.name;
            const initials = this.getInitials(name);
            const isOnline = conv.other_user?.is_online || false;
            const unreadBadge = conv.unread_count > 0 
                ? `<span class="unread-badge">${conv.unread_count}</span>` 
                : '';
            const onlineIndicator = isOnline ? '<span class="online-indicator"></span>' : '';
            const pinnedIcon = conv.is_pinned ? '<i class="fas fa-thumbtack" style="color: var(--cyber-cyan); margin-right: 5px;"></i>' : '';
            const lastMessagePreview = this.getLastMessagePreview(conv.last_message, conv.last_message_type);

            return `
                <div class="conversation-item ${conv.unread_count > 0 ? 'unread' : ''} ${conv.is_pinned ? 'pinned' : ''}"
                     onclick="chatSystem.openConversation(${conv.id})">
                    <div class="avatar">
                        ${initials}
                        ${onlineIndicator}
                    </div>
                    <div class="conversation-info">
                        <div class="conversation-name">
                            <span>${pinnedIcon}${name}</span>
                            ${unreadBadge}
                        </div>
                        <div class="conversation-preview">${lastMessagePreview}</div>
                    </div>
                    <div class="conversation-time">${this.formatTime(conv.last_message_at)}</div>
                </div>
            `;
        }).join('');
    }

    getLastMessagePreview(message, type) {
        if (!message) return 'No messages yet';
        
        switch (type) {
            case 'image':
                return '📷 Photo';
            case 'video':
                return '🎥 Video';
            case 'audio':
            case 'voice_note':
                return '🎤 Voice note';
            case 'document':
                return '📄 Document';
            case 'location':
                return '📍 Location';
            case 'contact':
                return '👤 Contact';
            default:
                return message.length > 50 ? message.substring(0, 50) + '...' : message;
        }
    }

    async openConversation(conversationId) {
        this.currentConversationId = conversationId;

        // Restart real-time updates for new conversation
        if (this.eventSource) {
            this.eventSource.close();
        }
        this.startRealTimeUpdates();

        // Update UI
        const emptyState = document.getElementById('emptyState');
        const activeChat = document.getElementById('activeChat');
        if (emptyState) emptyState.style.display = 'none';
        if (activeChat) activeChat.style.display = 'flex';

        // Load conversation details
        try {
            const response = await fetch(`${APP_URL}/api/chat/conversations.php?action=get&id=${conversationId}`);
            const data = await response.json();

            if (data.success) {
                const conv = data.conversation;
                const name = conv.conversation_type === 'direct' 
                    ? (conv.participants.find(p => p.user_id !== this.currentUserId)?.first_name + ' ' + 
                       conv.participants.find(p => p.user_id !== this.currentUserId)?.last_name)
                    : conv.name;
                
                document.getElementById('chatName').textContent = name || 'Chat';
                document.getElementById('chatAvatar').textContent = this.getInitials(name);
                
                // Update online status
                if (conv.conversation_type === 'direct') {
                    const otherUser = conv.participants.find(p => p.user_id !== this.currentUserId);
                    const isOnline = otherUser?.is_online || false;
                    const statusEl = document.getElementById('chatStatus');
                    if (statusEl) {
                        statusEl.textContent = isOnline ? 'Online' : 'Offline';
                        statusEl.className = 'chat-status' + (isOnline ? '' : ' offline');
                    }
                }
            }
        } catch (error) {
            console.error('Error loading conversation:', error);
        }

        // Mark active
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('active');
        });
        event?.currentTarget?.classList.add('active');

        // Load messages
        await this.loadMessages(conversationId);
        this.markAsRead(conversationId);
    }

    async loadMessages(conversationId, beforeId = 0) {
        try {
            const url = `${APP_URL}/api/chat/messages.php?action=get&conversation_id=${conversationId}&limit=50${beforeId ? '&before_id=' + beforeId : ''}`;
            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                this.displayMessages(data.messages);
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    displayMessages(messages) {
        const container = document.getElementById('messagesContainer');
        if (!container) return;

        container.innerHTML = messages.map(msg => {
            const isSent = msg.sender_id == this.currentUserId;
            const timestamp = this.formatTime(msg.created_at);
            const readReceipt = isSent ? this.getReadReceipt(msg.id) : '';

            // Reply preview
            let replyHtml = '';
            if (msg.reply_to_id && msg.reply_to) {
                replyHtml = `
                    <div class="message-reply" onclick="chatSystem.scrollToMessage(${msg.reply_to_id})">
                        <strong>${msg.reply_to.first_name} ${msg.reply_to.last_name}</strong>
                        <div>${this.getMessagePreview(msg.reply_to)}</div>
                    </div>
                `;
            }

            // Message content based on type
            let contentHtml = '';
            switch (msg.message_type) {
                case 'image':
                    contentHtml = `
                        <img src="${msg.media_url}" alt="Image" style="max-width: 100%; border-radius: 8px; cursor: pointer;" onclick="chatSystem.openMediaViewer('${msg.media_url}')">
                        ${msg.content ? `<div style="margin-top: 8px;">${this.escapeHtml(msg.content)}</div>` : ''}
                    `;
                    break;
                case 'video':
                    contentHtml = `
                        <video src="${msg.media_url}" controls style="max-width: 100%; border-radius: 8px;"></video>
                        ${msg.content ? `<div style="margin-top: 8px;">${this.escapeHtml(msg.content)}</div>` : ''}
                    `;
                    break;
                case 'voice_note':
                    contentHtml = this.renderVoiceNote(msg);
                    break;
                case 'document':
                    contentHtml = `
                        <div class="message-attachment">
                            <i class="fas fa-file attachment-icon"></i>
                            <div>
                                <a href="${msg.media_url}" target="_blank" style="color: var(--cyber-cyan);">${this.escapeHtml(msg.content || 'Document')}</a>
                                <div style="font-size: 0.8rem; color: #8892a6;">${this.formatFileSize(msg.media_size)}</div>
                            </div>
                        </div>
                    `;
                    break;
                case 'location':
                    const location = JSON.parse(msg.metadata || '{}');
                    contentHtml = `
                        <div class="message-location">
                            <i class="fas fa-map-marker-alt" style="color: #ef4444; font-size: 1.5rem;"></i>
                            <div>
                                <strong>Location</strong>
                                <div style="font-size: 0.85rem; color: #8892a6;">${location.address || 'Shared location'}</div>
                            </div>
                            <a href="https://maps.google.com/?q=${location.lat},${location.lng}" target="_blank" 
                               style="color: var(--cyber-cyan); text-decoration: none;">
                                <i class="fas fa-external-link-alt"></i> Open in Maps
                            </a>
                        </div>
                    `;
                    break;
                default:
                    contentHtml = this.escapeHtml(msg.content);
            }

            // Reactions
            let reactionsHtml = '';
            if (msg.reactions && msg.reactions.length > 0) {
                reactionsHtml = `
                    <div class="message-reactions">
                        ${msg.reactions.map(r => `
                            <span class="reaction ${r.user_id === this.currentUserId ? 'active' : ''}"
                                  onclick="chatSystem.toggleReaction(${msg.id}, '${r.reaction}')">
                                ${r.reaction} ${r.count}
                            </span>
                        `).join('')}
                    </div>
                `;
            }

            return `
                <div class="message-group ${isSent ? 'sent' : 'received'}" data-message-id="${msg.id}">
                    <div class="message" oncontextmenu="event.preventDefault(); chatSystem.showMessageMenu(event, ${msg.id}, ${isSent})">
                        ${replyHtml}
                        <div class="message-bubble">
                            ${contentHtml}
                        </div>
                        <div class="message-meta">
                            <span>${timestamp}</span>
                            ${msg.is_edited ? '<span><i class="fas fa-edit"></i> Edited</span>' : ''}
                            ${readReceipt}
                        </div>
                        ${reactionsHtml}
                    </div>
                </div>
            `;
        }).join('');
    }

    renderVoiceNote(msg) {
        const voiceNote = msg.voice_note_url ? {
            url: msg.voice_note_url,
            duration: msg.voice_note_duration || 0,
            waveform: msg.voice_note_waveform ? JSON.parse(msg.voice_note_waveform) : []
        } : null;

        if (!voiceNote) return '<div>Voice note</div>';

        return `
            <div class="voice-note-player" data-message-id="${msg.id}">
                <button class="voice-play-btn" onclick="chatSystem.playVoiceNote(${msg.id}, '${voiceNote.url}', ${voiceNote.duration})">
                    <i class="fas fa-play"></i>
                </button>
                <div class="voice-waveform">
                    ${this.renderWaveform(voiceNote.waveform)}
                </div>
                <div class="voice-duration">${this.formatDuration(voiceNote.duration)}</div>
            </div>
        `;
    }

    renderWaveform(waveform) {
        if (!waveform || waveform.length === 0) {
            // Generate default waveform bars
            waveform = Array.from({length: 50}, () => Math.random() * 100);
        }
        return waveform.map((height, i) => `
            <div class="wave-bar" style="height: ${height}%;" data-index="${i}"></div>
        `).join('');
    }

    async sendMessage() {
        const input = document.getElementById('messageInput');
        const message = input?.value.trim() || '';

        if (!message && this.selectedFiles.length === 0) return;
        if (!this.currentConversationId) return;

        try {
            const formData = new FormData();
            formData.append('action', 'send');
            formData.append('conversation_id', this.currentConversationId);
            formData.append('content', message);
            formData.append('message_type', 'text');

            if (this.replyToMessageId) {
                formData.append('reply_to_id', this.replyToMessageId);
                this.replyToMessageId = null;
                this.hideReplyPreview();
            }

            // Add files
            this.selectedFiles.forEach(file => {
                formData.append('files[]', file);
            });

            const response = await fetch(`${APP_URL}/api/chat/messages.php`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                if (input) input.value = '';
                this.selectedFiles = [];
                await this.loadMessages(this.currentConversationId);
                await this.loadConversations();
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    }

    // Voice Note Recording
    async startVoiceRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            this.mediaRecorder = new MediaRecorder(stream);
            this.audioChunks = [];
            this.voiceNoteDuration = 0;

            this.mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    this.audioChunks.push(event.data);
                }
            };

            this.mediaRecorder.onstop = () => {
                this.processVoiceNote();
                stream.getTracks().forEach(track => track.stop());
            };

            this.mediaRecorder.start();
            this.isRecording = true;
            this.updateVoiceButton(true);
            this.startVoiceDurationTimer();

            // Show recording UI
            this.showRecordingUI();
        } catch (error) {
            console.error('Error starting voice recording:', error);
            alert('Microphone access denied. Please allow microphone access to record voice notes.');
        }
    }

    stopVoiceRecording() {
        if (!this.isRecording) return;

        if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
            this.mediaRecorder.stop();
        }
        this.isRecording = false;
        this.updateVoiceButton(false);
        this.stopVoiceDurationTimer();
        this.hideRecordingUI();
    }

    async processVoiceNote() {
        if (this.audioChunks.length === 0) return;
        if (this.voiceNoteDuration < 1) return; // Minimum 1 second

        const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
        const reader = new FileReader();

        reader.onloadend = async () => {
            const base64Audio = reader.result;
            const waveform = this.generateWaveform(this.audioChunks);

            try {
                const response = await fetch(`${APP_URL}/api/chat/voice-note.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        conversation_id: this.currentConversationId,
                        audio_data: base64Audio,
                        duration: this.voiceNoteDuration,
                        waveform: waveform
                    })
                });

                const data = await response.json();

                if (data.success) {
                    await this.loadMessages(this.currentConversationId);
                    await this.loadConversations();
                }
            } catch (error) {
                console.error('Error uploading voice note:', error);
            }
        };

        reader.readAsDataURL(audioBlob);
    }

    generateWaveform(audioChunks) {
        // Simplified waveform generation
        // In production, use Web Audio API for accurate waveform
        return Array.from({length: 50}, () => Math.random() * 100);
    }

    startVoiceDurationTimer() {
        this.voiceNoteInterval = setInterval(() => {
            this.voiceNoteDuration++;
            const durationEl = document.getElementById('voiceNoteDuration');
            if (durationEl) {
                durationEl.textContent = this.formatDuration(this.voiceNoteDuration);
            }
        }, 1000);
    }

    stopVoiceDurationTimer() {
        if (this.voiceNoteInterval) {
            clearInterval(this.voiceNoteInterval);
            this.voiceNoteInterval = null;
        }
    }

    updateVoiceButton(isRecording) {
        const voiceBtn = document.getElementById('voiceNoteBtn');
        if (voiceBtn) {
            voiceBtn.classList.toggle('recording', isRecording);
            voiceBtn.innerHTML = isRecording 
                ? '<i class="fas fa-stop"></i>' 
                : '<i class="fas fa-microphone"></i>';
        }
    }

    showRecordingUI() {
        const inputContainer = document.querySelector('.chat-input-container');
        if (inputContainer) {
            inputContainer.innerHTML = `
                <div style="display: flex; align-items: center; gap: 15px; padding: 15px;">
                    <div class="recording-indicator">
                        <div class="recording-dot"></div>
                    </div>
                    <div style="flex: 1;">
                        <div style="color: white; font-weight: 600;">Recording voice note...</div>
                        <div id="voiceNoteDuration" style="color: #8892a6; font-size: 0.85rem;">0:00</div>
                    </div>
                    <button onclick="chatSystem.stopVoiceRecording()" style="background: #ef4444; border: none; color: white; padding: 10px 20px; border-radius: 8px; cursor: pointer;">
                        <i class="fas fa-stop"></i> Stop
                    </button>
                </div>
            `;
        }
    }

    hideRecordingUI() {
        // Restore original input UI
        this.loadChatInput();
    }

    loadChatInput() {
        const inputContainer = document.querySelector('.chat-input-container');
        if (inputContainer) {
            inputContainer.innerHTML = `
                <div class="chat-input-wrapper">
                    <div class="input-actions">
                        <button class="input-btn" id="voiceNoteBtn" title="Hold to record voice note">
                            <i class="fas fa-microphone"></i>
                        </button>
                        <button class="input-btn" onclick="chatSystem.attachFile()" title="Attach file">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <button class="input-btn" onclick="chatSystem.openEmojiPicker()" title="Emoji">
                            <i class="fas fa-smile"></i>
                        </button>
                    </div>
                    <textarea class="chat-input" id="messageInput" placeholder="Type a message..." rows="1"></textarea>
                    <button class="input-btn send-btn" onclick="chatSystem.sendMessage()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            `;
            this.setupEventListeners();
        }
    }

    // Calling Features
    async initiateCall(type) {
        if (!this.currentConversationId) {
            alert('Please select a conversation first');
            return;
        }

        try {
            const response = await fetch(`${APP_URL}/api/chat/call.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'initiate',
                    conversation_id: this.currentConversationId,
                    call_type: type
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showCallUI(type, 'outgoing', data.call_id);
                this.setupWebRTC(data.call_id, type);
            }
        } catch (error) {
            console.error('Error initiating call:', error);
        }
    }

    showCallUI(type, direction, callId) {
        const callModal = document.createElement('div');
        callModal.id = 'callModal';
        callModal.className = 'call-modal';
        callModal.innerHTML = `
            <div class="call-container">
                <div class="call-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="call-name" id="callName">Calling...</div>
                <div class="call-status" id="callStatus">Connecting...</div>
                <div class="call-controls">
                    <button class="call-control-btn" onclick="chatSystem.endCall(${callId})" style="background: #ef4444;">
                        <i class="fas fa-phone-slash"></i>
                    </button>
                    ${type === 'video' ? `
                        <button class="call-control-btn" id="toggleVideo" onclick="chatSystem.toggleVideo()">
                            <i class="fas fa-video"></i>
                        </button>
                        <button class="call-control-btn" id="toggleMute" onclick="chatSystem.toggleMute()">
                            <i class="fas fa-microphone"></i>
                        </button>
                    ` : ''}
                </div>
                <div id="callVideoContainer" style="display: none;">
                    <video id="localVideo" autoplay muted style="width: 150px; height: 100px; position: absolute; bottom: 100px; right: 20px; border-radius: 8px;"></video>
                    <video id="remoteVideo" autoplay style="width: 100%; height: 100%; object-fit: cover;"></video>
                </div>
            </div>
        `;
        document.body.appendChild(callModal);
    }

    async setupWebRTC(callId, type) {
        try {
            this.localStream = await navigator.mediaDevices.getUserMedia({
                audio: true,
                video: type === 'video'
            });

            if (type === 'video') {
                const localVideo = document.getElementById('localVideo');
                if (localVideo) {
                    localVideo.srcObject = this.localStream;
                }
            }

            // Setup peer connection (simplified - full WebRTC implementation needed)
            this.peerConnection = new RTCPeerConnection({
                iceServers: [{ urls: 'stun:stun.l.google.com:19302' }]
            });

            this.localStream.getTracks().forEach(track => {
                this.peerConnection.addTrack(track, this.localStream);
            });

            // Handle ICE candidates and offer/answer exchange
            // This is a simplified version - full implementation requires signaling server

        } catch (error) {
            console.error('Error setting up WebRTC:', error);
        }
    }

    async endCall(callId) {
        try {
            await fetch(`${APP_URL}/api/chat/call.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'end',
                    call_id: callId
                })
            });

            // Stop local stream
            if (this.localStream) {
                this.localStream.getTracks().forEach(track => track.stop());
                this.localStream = null;
            }

            // Close peer connection
            if (this.peerConnection) {
                this.peerConnection.close();
                this.peerConnection = null;
            }

            // Remove call UI
            const callModal = document.getElementById('callModal');
            if (callModal) callModal.remove();
        } catch (error) {
            console.error('Error ending call:', error);
        }
    }

    toggleMute() {
        if (this.localStream) {
            const audioTracks = this.localStream.getAudioTracks();
            audioTracks.forEach(track => {
                track.enabled = !track.enabled;
            });
        }
    }

    toggleVideo() {
        if (this.localStream) {
            const videoTracks = this.localStream.getVideoTracks();
            videoTracks.forEach(track => {
                track.enabled = !track.enabled;
            });
        }
    }

    // Typing Indicator
    handleTyping() {
        clearTimeout(this.typingTimeout);

        this.sendTypingIndicator(true);

        this.typingTimeout = setTimeout(() => {
            this.sendTypingIndicator(false);
        }, 3000);
    }

    async sendTypingIndicator(isTyping) {
        if (!this.currentConversationId) return;

        try {
            await fetch(`${APP_URL}/api/chat/typing.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    conversation_id: this.currentConversationId,
                    is_typing: isTyping
                })
            });
        } catch (error) {
            console.error('Error sending typing indicator:', error);
        }
    }

    async checkTypingIndicator() {
        if (!this.currentConversationId) return;

        try {
            const response = await fetch(`${APP_URL}/api/chat/typing.php?action=get&conversation_id=${this.currentConversationId}`);
            const data = await response.json();

            const indicator = document.getElementById('typingIndicator');
            if (data.success && data.typing_users && data.typing_users.length > 0) {
                const names = data.typing_users.map(u => u.first_name).join(', ');
                const typingText = document.getElementById('typingText');
                if (typingText) typingText.textContent = `${names} ${data.typing_users.length > 1 ? 'are' : 'is'} typing...`;
                if (indicator) indicator.style.display = 'block';
            } else {
                if (indicator) indicator.style.display = 'none';
            }
        } catch (error) {
            console.error('Error checking typing:', error);
        }
    }

    // Read Receipts
    async markAsRead(conversationId) {
        try {
            await fetch(`${APP_URL}/api/chat/read.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    conversation_id: conversationId
                })
            });
        } catch (error) {
            console.error('Error marking as read:', error);
        }
    }

    async getReadReceipt(messageId) {
        try {
            const response = await fetch(`${APP_URL}/api/chat/read.php?action=get&message_id=${messageId}`);
            const data = await response.json();

            if (data.success && data.read_by && data.read_by.length > 0) {
                return `<i class="fas fa-check-double" style="color: #10b981;"></i>`;
            }
            return `<i class="fas fa-check-double" style="color: #6b7280;"></i>`;
        } catch (error) {
            return '';
        }
    }

    // Reactions
    async toggleReaction(messageId, reaction) {
        try {
            const response = await fetch(`${APP_URL}/api/chat/reaction.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message_id: messageId,
                    reaction: reaction,
                    action: 'add' // Toggle logic handled server-side
                })
            });

            const data = await response.json();

            if (data.success) {
                await this.loadMessages(this.currentConversationId);
            }
        } catch (error) {
            console.error('Error toggling reaction:', error);
        }
    }

    // Real-time Updates (SSE)
    startRealTimeUpdates() {
        if (typeof EventSource === 'undefined') {
            // Fallback to polling
            this.startPolling();
            return;
        }

        const conversationId = this.currentConversationId || 0;
        const eventSource = new EventSource(`${APP_URL}/api/sse.php?conversation_id=${conversationId}`);

        eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);

            switch (data.type) {
                case 'new_message':
                    this.handleNewMessage(data.message);
                    break;
                case 'typing':
                    this.handleTypingIndicator(data.users);
                    break;
                case 'typing_stopped':
                    this.hideTypingIndicator();
                    break;
                case 'read_receipt':
                    this.handleReadReceipt(data.receipts);
                    break;
                case 'presence_update':
                    this.handlePresenceUpdate(data.presence);
                    break;
                case 'new_conversation':
                    this.loadConversations();
                    break;
                case 'heartbeat':
                    // Keep connection alive
                    break;
            }
        };

        eventSource.onerror = (error) => {
            console.error('SSE error:', error);
            eventSource.close();
            // Fallback to polling
            this.startPolling();
        };

        this.eventSource = eventSource;
    }

    handleNewMessage(message) {
        if (message.conversation_id === this.currentConversationId) {
            // Add message to current view
            const container = document.getElementById('messagesContainer');
            if (container) {
                const messageHtml = this.renderMessage(message);
                container.insertAdjacentHTML('beforeend', messageHtml);
                this.scrollToBottom();
            }
        } else {
            // Update conversation list
            this.loadConversations();
        }
    }

    handleTypingIndicator(users) {
        const indicator = document.getElementById('typingIndicator');
        const typingText = document.getElementById('typingText');
        if (indicator && typingText) {
            const names = users.map(u => u.first_name).join(', ');
            typingText.textContent = `${names} ${users.length > 1 ? 'are' : 'is'} typing...`;
            indicator.style.display = 'block';
        }
    }

    hideTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) indicator.style.display = 'none';
    }

    handleReadReceipt(receipts) {
        // Update read receipts in UI
        receipts.forEach(receipt => {
            const messageEl = document.querySelector(`[data-message-id="${receipt.message_id}"]`);
            if (messageEl) {
                const metaEl = messageEl.querySelector('.message-meta');
                if (metaEl) {
                    metaEl.innerHTML += `<i class="fas fa-check-double" style="color: #10b981;"></i>`;
                }
            }
        });
    }

    handlePresenceUpdate(presence) {
        presence.forEach(p => {
            // Update online status in conversation list and chat header
            const statusEl = document.getElementById('chatStatus');
            if (statusEl && p.user_id !== this.currentUserId) {
                statusEl.textContent = p.is_online ? 'Online' : 'Offline';
                statusEl.className = 'chat-status' + (p.is_online ? '' : ' offline');
            }
        });
    }

    renderMessage(msg) {
        const isSent = msg.sender_id == this.currentUserId;
        const timestamp = this.formatTime(msg.created_at);

        return `
            <div class="message-group ${isSent ? 'sent' : 'received'}" data-message-id="${msg.id}">
                <div class="message">
                    <div class="message-bubble">
                        ${this.escapeHtml(msg.content)}
                    </div>
                    <div class="message-meta">
                        <span>${timestamp}</span>
                    </div>
                </div>
            </div>
        `;
    }

    // Polling (fallback)
    startPolling() {
        this.pollingInterval = setInterval(() => {
            if (this.currentConversationId) {
                this.loadMessages(this.currentConversationId);
                this.checkTypingIndicator();
            }
            this.loadConversations();
        }, 3000);
    }

    // Online Status
    async updateOnlineStatus() {
        try {
            await fetch(`${APP_URL}/api/chat/presence.php?action=update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    is_online: true,
                    status: 'online'
                })
            });
        } catch (error) {
            console.error('Error updating online status:', error);
        }
    }

    // Utility Functions
    getInitials(name) {
        if (!name) return '?';
        const parts = name.split(' ');
        if (parts.length >= 2) {
            return (parts[0][0] + parts[1][0]).toUpperCase();
        }
        return name.substring(0, 2).toUpperCase();
    }

    formatTime(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        const minutes = Math.floor(diff / 60000);

        if (minutes < 1) return 'Just now';
        if (minutes < 60) return `${minutes}m ago`;
        if (minutes < 1440) return `${Math.floor(minutes / 60)}h ago`;
        return date.toLocaleDateString();
    }

    formatDuration(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    }

    formatFileSize(bytes) {
        if (!bytes) return '';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    getMessagePreview(msg) {
        if (!msg) return '';
        if (msg.message_type === 'image') return '📷 Photo';
        if (msg.message_type === 'voice_note') return '🎤 Voice note';
        return msg.content ? (msg.content.length > 50 ? msg.content.substring(0, 50) + '...' : msg.content) : '';
    }

    scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    scrollToMessage(messageId) {
        const messageEl = document.querySelector(`[data-message-id="${messageId}"]`);
        if (messageEl) {
            messageEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            messageEl.style.background = 'rgba(0, 191, 255, 0.2)';
            setTimeout(() => {
                messageEl.style.background = '';
            }, 2000);
        }
    }

    attachFile() {
        document.getElementById('fileInput')?.click();
    }

    openEmojiPicker() {
        // TODO: Implement emoji picker
        console.log('Emoji picker');
    }

    showMessageMenu(event, messageId, isSent) {
        // TODO: Implement context menu
        console.log('Message menu', messageId);
    }

    playVoiceNote(messageId, url, duration) {
        // TODO: Implement voice note playback with waveform animation
        const audio = new Audio(url);
        audio.play();
    }
}

// Initialize chat system
const chatSystem = new VerdantChat();

// Make available globally
window.chatSystem = chatSystem;


