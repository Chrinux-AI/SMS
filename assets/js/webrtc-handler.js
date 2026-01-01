/**
 * WebRTC Handler - Voice and Video Calling
 * Handles peer-to-peer connections, ICE candidates, and call management
 */

class WebRTCHandler {
    constructor(options = {}) {
        this.peerConnection = null;
        this.localStream = null;
        this.remoteStream = null;
        this.currentCallId = null;
        this.callType = null; // 'voice' or 'video'
        this.isMuted = false;
        this.isVideoEnabled = true;
        this.isSpeakerOn = true;

        // STUN/TURN servers
        this.iceServers = options.iceServers || [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' },
            { urls: 'stun:stun2.l.google.com:19302' }
        ];

        // Callbacks
        this.onCallStarted = options.onCallStarted || (() => {});
        this.onCallEnded = options.onCallEnded || (() => {});
        this.onCallAccepted = options.onCallAccepted || (() => {});
        this.onCallDeclined = options.onCallDeclined || (() => {});
        this.onRemoteStream = options.onRemoteStream || (() => {});
        this.onError = options.onError || ((err) => console.error('WebRTC Error:', err));
        this.onIceCandidate = options.onIceCandidate || (() => {});
        this.onSignalingMessage = options.onSignalingMessage || (() => {});

        // API endpoint
        this.apiUrl = options.apiUrl || '/attendance/api/chat/call.php';
    }

    async initializeCall(callType, targetUserId, conversationId) {
        this.callType = callType;

        try {
            // Get local media stream
            const constraints = {
                audio: true,
                video: callType === 'video'
            };

            this.localStream = await navigator.mediaDevices.getUserMedia(constraints);

            // Create peer connection
            this.createPeerConnection();

            // Add local stream tracks to peer connection
            this.localStream.getTracks().forEach(track => {
                this.peerConnection.addTrack(track, this.localStream);
            });

            // Create offer
            const offer = await this.peerConnection.createOffer();
            await this.peerConnection.setLocalDescription(offer);

            // Send call request to server
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'initiate',
                    conversation_id: conversationId,
                    target_user_id: targetUserId,
                    call_type: callType,
                    offer: offer
                })
            });

            const data = await response.json();

            if (data.success) {
                this.currentCallId = data.call_id;
                this.onCallStarted({
                    callId: data.call_id,
                    callType: callType,
                    localStream: this.localStream
                });

                // Start polling for answer
                this.pollForCallStatus();
            } else {
                throw new Error(data.error || 'Failed to initiate call');
            }

            return data;
        } catch (error) {
            this.onError(error);
            this.endCall();
            return { success: false, error: error.message };
        }
    }

    createPeerConnection() {
        const config = {
            iceServers: this.iceServers,
            iceCandidatePoolSize: 10
        };

        this.peerConnection = new RTCPeerConnection(config);

        // Handle ICE candidates
        this.peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                this.onIceCandidate(event.candidate);
                // Send to signaling server
                this.sendSignalingMessage({
                    type: 'ice-candidate',
                    candidate: event.candidate
                });
            }
        };

        // Handle remote stream
        this.peerConnection.ontrack = (event) => {
            this.remoteStream = event.streams[0];
            this.onRemoteStream(this.remoteStream);
        };

        // Handle connection state changes
        this.peerConnection.onconnectionstatechange = () => {
            console.log('Connection state:', this.peerConnection.connectionState);
            if (this.peerConnection.connectionState === 'disconnected' ||
                this.peerConnection.connectionState === 'failed') {
                this.endCall();
            }
        };

        // Handle ICE connection state
        this.peerConnection.oniceconnectionstatechange = () => {
            console.log('ICE state:', this.peerConnection.iceConnectionState);
        };
    }

    async answerCall(callId, offer) {
        this.currentCallId = callId;

        try {
            // Get local media stream
            const constraints = {
                audio: true,
                video: this.callType === 'video'
            };

            this.localStream = await navigator.mediaDevices.getUserMedia(constraints);

            // Create peer connection
            this.createPeerConnection();

            // Add local stream tracks
            this.localStream.getTracks().forEach(track => {
                this.peerConnection.addTrack(track, this.localStream);
            });

            // Set remote description (offer)
            await this.peerConnection.setRemoteDescription(new RTCSessionDescription(offer));

            // Create answer
            const answer = await this.peerConnection.createAnswer();
            await this.peerConnection.setLocalDescription(answer);

            // Send answer to server
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'answer',
                    call_id: callId,
                    answer: answer
                })
            });

            const data = await response.json();

            if (data.success) {
                this.onCallAccepted({
                    callId: callId,
                    localStream: this.localStream
                });
            }

            return data;
        } catch (error) {
            this.onError(error);
            this.endCall();
            return { success: false, error: error.message };
        }
    }

    async declineCall(callId) {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'decline',
                    call_id: callId
                })
            });

            const data = await response.json();
            this.onCallDeclined();
            return data;
        } catch (error) {
            this.onError(error);
            return { success: false, error: error.message };
        }
    }

    async endCall() {
        try {
            // Send end call to server
            if (this.currentCallId) {
                await fetch(this.apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'end',
                        call_id: this.currentCallId
                    })
                });
            }
        } catch (error) {
            console.error('Error ending call:', error);
        }

        // Clean up
        this.cleanup();
        this.onCallEnded();
    }

    cleanup() {
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

        this.remoteStream = null;
        this.currentCallId = null;
        this.callType = null;
    }

    // Media controls
    toggleMute() {
        if (this.localStream) {
            const audioTrack = this.localStream.getAudioTracks()[0];
            if (audioTrack) {
                audioTrack.enabled = !audioTrack.enabled;
                this.isMuted = !audioTrack.enabled;
            }
        }
        return this.isMuted;
    }

    toggleVideo() {
        if (this.localStream) {
            const videoTrack = this.localStream.getVideoTracks()[0];
            if (videoTrack) {
                videoTrack.enabled = !videoTrack.enabled;
                this.isVideoEnabled = videoTrack.enabled;
            }
        }
        return this.isVideoEnabled;
    }

    toggleSpeaker() {
        // Note: Speaker toggle is handled by the audio element
        this.isSpeakerOn = !this.isSpeakerOn;
        return this.isSpeakerOn;
    }

    async switchCamera() {
        if (!this.localStream) return;

        const videoTrack = this.localStream.getVideoTracks()[0];
        if (!videoTrack) return;

        const constraints = videoTrack.getConstraints();
        const facingMode = constraints.facingMode === 'user' ? 'environment' : 'user';

        try {
            const newStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode }
            });

            const newVideoTrack = newStream.getVideoTracks()[0];

            // Replace track in peer connection
            const sender = this.peerConnection.getSenders().find(s => s.track.kind === 'video');
            if (sender) {
                await sender.replaceTrack(newVideoTrack);
            }

            // Stop old track
            videoTrack.stop();

            // Update local stream
            this.localStream.removeTrack(videoTrack);
            this.localStream.addTrack(newVideoTrack);
        } catch (error) {
            this.onError(error);
        }
    }

    async handleRemoteAnswer(answer) {
        if (!this.peerConnection) return;

        await this.peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
    }

    async handleIceCandidate(candidate) {
        if (!this.peerConnection) return;

        try {
            await this.peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
        } catch (error) {
            console.error('Error adding ICE candidate:', error);
        }
    }

    sendSignalingMessage(message) {
        // Override this or use callback to send signaling messages
        this.onSignalingMessage(message);
    }

    async pollForCallStatus() {
        if (!this.currentCallId) return;

        const pollInterval = setInterval(async () => {
            if (!this.currentCallId) {
                clearInterval(pollInterval);
                return;
            }

            try {
                const response = await fetch(`${this.apiUrl}?action=status&call_id=${this.currentCallId}`);
                const data = await response.json();

                if (data.success) {
                    if (data.status === 'answered' && data.answer) {
                        clearInterval(pollInterval);
                        await this.handleRemoteAnswer(data.answer);
                    } else if (data.status === 'declined' || data.status === 'ended') {
                        clearInterval(pollInterval);
                        this.cleanup();
                        this.onCallDeclined();
                    }
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        }, 1000);

        // Stop polling after 60 seconds (call timeout)
        setTimeout(() => {
            clearInterval(pollInterval);
            if (this.currentCallId) {
                this.endCall();
            }
        }, 60000);
    }
}

// Call UI Manager
class CallUIManager {
    constructor(webrtcHandler) {
        this.webrtc = webrtcHandler;
        this.callModal = null;
        this.callTimer = null;
        this.callDuration = 0;

        this.createCallModal();
    }

    createCallModal() {
        this.callModal = document.createElement('div');
        this.callModal.className = 'call-modal';
        this.callModal.style.display = 'none';
        this.callModal.innerHTML = `
            <div class="call-container">
                <div class="call-avatar" id="callAvatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="call-name" id="callName">User</div>
                <div class="call-status" id="callStatus">Calling...</div>
                <div class="call-timer" id="callTimer" style="display: none;">00:00</div>

                <video id="remoteVideo" autoplay playsinline style="display: none;"></video>
                <video id="localVideo" autoplay playsinline muted style="display: none;"></video>

                <div class="call-controls">
                    <button class="call-control-btn" id="muteBtn" onclick="callUI.toggleMute()">
                        <i class="fas fa-microphone"></i>
                    </button>
                    <button class="call-control-btn" id="videoBtn" onclick="callUI.toggleVideo()" style="display: none;">
                        <i class="fas fa-video"></i>
                    </button>
                    <button class="call-control-btn" id="speakerBtn" onclick="callUI.toggleSpeaker()">
                        <i class="fas fa-volume-up"></i>
                    </button>
                    <button class="call-control-btn end-call-btn" onclick="callUI.endCall()" style="background: #ef4444;">
                        <i class="fas fa-phone-slash"></i>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(this.callModal);
    }

    showOutgoingCall(userName, callType) {
        document.getElementById('callName').textContent = userName;
        document.getElementById('callStatus').textContent = 'Calling...';
        document.getElementById('callAvatar').innerHTML = `<span>${this.getInitials(userName)}</span>`;
        document.getElementById('videoBtn').style.display = callType === 'video' ? 'flex' : 'none';

        this.callModal.style.display = 'flex';
    }

    showIncomingCall(userName, callType) {
        document.getElementById('callName').textContent = userName;
        document.getElementById('callStatus').textContent = 'Incoming call...';
        document.getElementById('callAvatar').innerHTML = `<span>${this.getInitials(userName)}</span>`;

        this.callModal.style.display = 'flex';
    }

    showOngoingCall() {
        document.getElementById('callStatus').style.display = 'none';
        document.getElementById('callTimer').style.display = 'block';

        this.callDuration = 0;
        this.callTimer = setInterval(() => {
            this.callDuration++;
            document.getElementById('callTimer').textContent = this.formatDuration(this.callDuration);
        }, 1000);
    }

    hideCallModal() {
        this.callModal.style.display = 'none';
        clearInterval(this.callTimer);

        document.getElementById('remoteVideo').style.display = 'none';
        document.getElementById('localVideo').style.display = 'none';
    }

    setLocalStream(stream) {
        const video = document.getElementById('localVideo');
        video.srcObject = stream;
        video.style.display = 'block';
    }

    setRemoteStream(stream) {
        const video = document.getElementById('remoteVideo');
        video.srcObject = stream;
        video.style.display = 'block';
        this.showOngoingCall();
    }

    toggleMute() {
        const isMuted = this.webrtc.toggleMute();
        const btn = document.getElementById('muteBtn');
        btn.innerHTML = `<i class="fas fa-microphone${isMuted ? '-slash' : ''}"></i>`;
        btn.style.background = isMuted ? 'rgba(239, 68, 68, 0.5)' : '';
    }

    toggleVideo() {
        const isEnabled = this.webrtc.toggleVideo();
        const btn = document.getElementById('videoBtn');
        btn.innerHTML = `<i class="fas fa-video${!isEnabled ? '-slash' : ''}"></i>`;
        btn.style.background = !isEnabled ? 'rgba(239, 68, 68, 0.5)' : '';
    }

    toggleSpeaker() {
        const isOn = this.webrtc.toggleSpeaker();
        const btn = document.getElementById('speakerBtn');
        btn.innerHTML = `<i class="fas fa-volume-${isOn ? 'up' : 'mute'}"></i>`;
    }

    endCall() {
        this.webrtc.endCall();
        this.hideCallModal();
    }

    getInitials(name) {
        const parts = name.split(' ');
        return parts.map(p => p[0]).join('').toUpperCase().substring(0, 2);
    }

    formatDuration(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }
}

// Export
window.WebRTCHandler = WebRTCHandler;
window.CallUIManager = CallUIManager;
