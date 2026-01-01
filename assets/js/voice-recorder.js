/**
 * Voice Recorder - WhatsApp-style voice note recording
 * Hold to record, release to send, swipe to cancel
 */

class VoiceRecorder {
    constructor(options = {}) {
        this.mediaRecorder = null;
        this.audioChunks = [];
        this.isRecording = false;
        this.startTime = null;
        this.timerInterval = null;
        this.audioStream = null;
        this.analyser = null;
        this.audioContext = null;
        this.cancelThreshold = 100; // pixels to swipe left to cancel
        this.startX = 0;

        // Callbacks
        this.onStart = options.onStart || (() => {});
        this.onStop = options.onStop || (() => {});
        this.onCancel = options.onCancel || (() => {});
        this.onError = options.onError || ((err) => console.error(err));
        this.onDurationUpdate = options.onDurationUpdate || (() => {});
        this.onWaveformUpdate = options.onWaveformUpdate || (() => {});

        // Check browser support
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            console.warn('Voice recording not supported in this browser');
        }
    }

    async startRecording() {
        try {
            // Request microphone access
            this.audioStream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    sampleRate: 44100
                }
            });

            // Set up audio context for visualization
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            this.analyser = this.audioContext.createAnalyser();
            const source = this.audioContext.createMediaStreamSource(this.audioStream);
            source.connect(this.analyser);
            this.analyser.fftSize = 256;

            // Create media recorder
            const mimeType = MediaRecorder.isTypeSupported('audio/webm')
                ? 'audio/webm'
                : 'audio/ogg';

            this.mediaRecorder = new MediaRecorder(this.audioStream, { mimeType });
            this.audioChunks = [];

            this.mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    this.audioChunks.push(event.data);
                }
            };

            this.mediaRecorder.onstop = () => {
                this.stopVisualization();
            };

            // Start recording
            this.mediaRecorder.start(100); // Collect data every 100ms
            this.isRecording = true;
            this.startTime = Date.now();

            // Start timer
            this.timerInterval = setInterval(() => {
                const elapsed = Math.floor((Date.now() - this.startTime) / 1000);
                this.onDurationUpdate(this.formatDuration(elapsed));
            }, 1000);

            // Start waveform visualization
            this.visualize();

            this.onStart();

            return true;
        } catch (error) {
            this.onError(error);
            return false;
        }
    }

    async stopRecording(shouldCancel = false) {
        if (!this.isRecording) return null;

        this.isRecording = false;
        clearInterval(this.timerInterval);

        return new Promise((resolve) => {
            this.mediaRecorder.onstop = () => {
                this.stopVisualization();

                if (shouldCancel) {
                    this.audioChunks = [];
                    this.onCancel();
                    resolve(null);
                } else {
                    const mimeType = this.mediaRecorder.mimeType;
                    const audioBlob = new Blob(this.audioChunks, { type: mimeType });
                    const duration = Math.floor((Date.now() - this.startTime) / 1000);

                    this.onStop({
                        blob: audioBlob,
                        duration: duration,
                        mimeType: mimeType
                    });

                    resolve({
                        blob: audioBlob,
                        duration: duration,
                        mimeType: mimeType
                    });
                }

                this.audioChunks = [];
            };

            this.mediaRecorder.stop();

            // Stop all tracks
            if (this.audioStream) {
                this.audioStream.getTracks().forEach(track => track.stop());
                this.audioStream = null;
            }
        });
    }

    cancelRecording() {
        return this.stopRecording(true);
    }

    visualize() {
        if (!this.analyser || !this.isRecording) return;

        const bufferLength = this.analyser.frequencyBinCount;
        const dataArray = new Uint8Array(bufferLength);

        const draw = () => {
            if (!this.isRecording) return;

            requestAnimationFrame(draw);
            this.analyser.getByteFrequencyData(dataArray);

            // Convert to normalized values (0-1)
            const normalized = Array.from(dataArray.slice(0, 20)).map(v => v / 255);
            this.onWaveformUpdate(normalized);
        };

        draw();
    }

    stopVisualization() {
        if (this.audioContext) {
            this.audioContext.close();
            this.audioContext = null;
        }
        this.analyser = null;
    }

    formatDuration(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    }

    // Touch event handlers for hold-to-record
    handleTouchStart(event, x) {
        this.startX = x;
        this.startRecording();
    }

    handleTouchMove(event, x) {
        if (!this.isRecording) return;

        const deltaX = this.startX - x;
        if (deltaX > this.cancelThreshold) {
            // User swiped left - cancel
            this.cancelRecording();
        }
    }

    handleTouchEnd(event) {
        if (this.isRecording) {
            this.stopRecording();
        }
    }
}

/**
 * Voice Note Player - Playback with waveform visualization
 */
class VoiceNotePlayer {
    constructor(container, audioUrl, duration) {
        this.container = container;
        this.audioUrl = audioUrl;
        this.duration = duration;
        this.audio = null;
        this.isPlaying = false;
        this.progressInterval = null;

        this.render();
    }

    render() {
        this.container.innerHTML = `
            <div class="voice-note-player">
                <button class="voice-play-btn" title="Play">
                    <i class="fas fa-play"></i>
                </button>
                <div class="voice-waveform">
                    ${this.generateWaveformBars()}
                </div>
                <span class="voice-duration">${this.formatDuration(this.duration)}</span>
            </div>
        `;

        this.playBtn = this.container.querySelector('.voice-play-btn');
        this.waveform = this.container.querySelector('.voice-waveform');
        this.durationSpan = this.container.querySelector('.voice-duration');

        this.playBtn.addEventListener('click', () => this.togglePlay());

        // Create audio element
        this.audio = new Audio(this.audioUrl);
        this.audio.addEventListener('ended', () => this.onEnded());
        this.audio.addEventListener('timeupdate', () => this.onTimeUpdate());
    }

    generateWaveformBars() {
        // Generate random waveform bars for visual effect
        let bars = '';
        for (let i = 0; i < 30; i++) {
            const height = 5 + Math.random() * 20;
            bars += `<div class="wave-bar" style="height: ${height}px;"></div>`;
        }
        return bars;
    }

    togglePlay() {
        if (this.isPlaying) {
            this.pause();
        } else {
            this.play();
        }
    }

    play() {
        this.audio.play();
        this.isPlaying = true;
        this.playBtn.innerHTML = '<i class="fas fa-pause"></i>';
        this.animateWaveform();
    }

    pause() {
        this.audio.pause();
        this.isPlaying = false;
        this.playBtn.innerHTML = '<i class="fas fa-play"></i>';
        this.stopWaveformAnimation();
    }

    onEnded() {
        this.isPlaying = false;
        this.playBtn.innerHTML = '<i class="fas fa-play"></i>';
        this.stopWaveformAnimation();
        this.durationSpan.textContent = this.formatDuration(this.duration);
    }

    onTimeUpdate() {
        const remaining = Math.ceil(this.duration - this.audio.currentTime);
        this.durationSpan.textContent = this.formatDuration(remaining);
    }

    animateWaveform() {
        const bars = this.waveform.querySelectorAll('.wave-bar');
        this.progressInterval = setInterval(() => {
            bars.forEach(bar => {
                const height = 5 + Math.random() * 20;
                bar.style.height = `${height}px`;
            });
        }, 100);
    }

    stopWaveformAnimation() {
        clearInterval(this.progressInterval);
    }

    formatDuration(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    }
}

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { VoiceRecorder, VoiceNotePlayer };
}

// Global instance for chat
window.VoiceRecorder = VoiceRecorder;
window.VoiceNotePlayer = VoiceNotePlayer;
