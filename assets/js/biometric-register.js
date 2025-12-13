/**
 * Biometric Registration Manager
 * Handles WebAuthn credential registration for user profiles
 *
 * @package VerdantSMS
 * @since 3.0.0
 */

class BiometricRegister {
    constructor(options = {}) {
        this.apiEndpoint = options.apiEndpoint || '/attendance/api/webauthn.php';
        this.onSuccess = options.onSuccess || this.defaultOnSuccess;
        this.onError = options.onError || this.defaultOnError;
        this.container = options.container || document.getElementById('biometric-section');
    }

    /**
     * Check browser and device capabilities
     * @returns {Promise<object>} Capabilities object
     */
    async checkCapabilities() {
        const capabilities = {
            webauthn: false,
            platformAuthenticator: false,
            canRegisterBiometric: false,
            canRegisterPasskey: false
        };

        if (!window.PublicKeyCredential) {
            return capabilities;
        }

        capabilities.webauthn = true;
        capabilities.canRegisterPasskey = true;

        try {
            capabilities.platformAuthenticator =
                await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
            capabilities.canRegisterBiometric = capabilities.platformAuthenticator;
        } catch (e) {
            console.warn('Platform authenticator check failed:', e);
        }

        return capabilities;
    }

    /**
     * Load user's registered credentials from server
     * @returns {Promise<array>} List of credentials
     */
    async loadCredentials() {
        try {
            const response = await fetch(`${this.apiEndpoint}?action=list`, {
                method: 'GET',
                credentials: 'include'
            });

            const data = await response.json();
            return data.success ? data.credentials : [];
        } catch (error) {
            console.error('Failed to load credentials:', error);
            return [];
        }
    }

    /**
     * Register a new biometric credential (fingerprint/Face ID)
     * @param {string} deviceName Optional device name
     * @returns {Promise<boolean>} Success status
     */
    async registerBiometric(deviceName = '') {
        return this.register('biometric', deviceName);
    }

    /**
     * Register a new passkey (security key/phone)
     * @param {string} deviceName Optional device name
     * @returns {Promise<boolean>} Success status
     */
    async registerPasskey(deviceName = '') {
        return this.register('passkey', deviceName);
    }

    /**
     * Generic registration method
     * @param {string} type 'biometric' or 'passkey'
     * @param {string} deviceName Optional device name
     * @returns {Promise<boolean>} Success status
     */
    async register(type = 'biometric', deviceName = '') {
        try {
            // 1. Get registration options from server
            const optionsResponse = await fetch(`${this.apiEndpoint}?action=register_options`, {
                method: 'GET',
                credentials: 'include'
            });

            const optionsData = await optionsResponse.json();

            if (!optionsData.success) {
                this.onError(optionsData.message || 'Failed to get registration options');
                return false;
            }

            const options = optionsData.options;

            // 2. Convert base64url to ArrayBuffer
            options.challenge = this.base64UrlToArrayBuffer(options.challenge);
            options.user.id = this.base64UrlToArrayBuffer(options.user.id);

            // Convert excludeCredentials if present
            if (options.excludeCredentials) {
                options.excludeCredentials = options.excludeCredentials.map(cred => ({
                    ...cred,
                    id: this.base64UrlToArrayBuffer(cred.id)
                }));
            }

            // Set authenticator selection based on type
            if (type === 'biometric') {
                options.authenticatorSelection = {
                    authenticatorAttachment: 'platform',
                    userVerification: 'required',
                    residentKey: 'preferred'
                };
            } else {
                options.authenticatorSelection = {
                    userVerification: 'required',
                    residentKey: 'preferred'
                };
            }

            // 3. Call WebAuthn API
            const credential = await navigator.credentials.create({
                publicKey: options
            });

            // 4. Send credential to server
            const verifyResponse = await fetch(`${this.apiEndpoint}?action=register`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({
                    name: deviceName || this.detectDeviceName(),
                    credential: {
                        id: this.arrayBufferToBase64Url(credential.rawId),
                        type: credential.type,
                        transports: credential.response.getTransports ? credential.response.getTransports() : [],
                        response: {
                            clientDataJSON: this.arrayBufferToBase64Url(credential.response.clientDataJSON),
                            attestationObject: this.arrayBufferToBase64Url(credential.response.attestationObject)
                        }
                    }
                })
            });

            const verifyData = await verifyResponse.json();

            if (verifyData.success) {
                this.onSuccess(type, verifyData);
                await this.refreshCredentialsList();
                return true;
            } else {
                this.onError(verifyData.message || 'Registration failed');
                return false;
            }

        } catch (error) {
            if (error.name === 'NotAllowedError') {
                this.onError('Registration was cancelled or timed out');
            } else if (error.name === 'NotSupportedError') {
                this.onError('This device does not support biometric authentication');
            } else if (error.name === 'InvalidStateError') {
                this.onError('This authenticator is already registered');
            } else {
                console.error('Registration error:', error);
                this.onError('Failed to register credential');
            }
            return false;
        }
    }

    /**
     * Delete a credential
     * @param {number} credentialId The credential database ID
     * @returns {Promise<boolean>} Success status
     */
    async deleteCredential(credentialId) {
        if (!confirm('Are you sure you want to remove this login method?')) {
            return false;
        }

        try {
            const response = await fetch(`${this.apiEndpoint}?action=delete`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({
                    id: credentialId
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Credential removed successfully', 'success');
                await this.refreshCredentialsList();
                return true;
            } else {
                this.onError(data.message || 'Failed to delete credential');
                return false;
            }
        } catch (error) {
            console.error('Delete error:', error);
            this.onError('Failed to delete credential');
            return false;
        }
    }

    /**
     * Refresh the credentials list UI
     */
    async refreshCredentialsList() {
        const listEl = document.getElementById('registered-methods');
        if (!listEl) return;

        const credentials = await this.loadCredentials();

        if (credentials.length === 0) {
            listEl.innerHTML = `
                <h4 style="margin-bottom: 1rem; color: var(--text-primary);">
                    <i class="fas fa-shield-alt"></i> Registered Methods
                </h4>
                <div class="no-credentials">
                    <i class="fas fa-shield-alt"></i>
                    <p>No passwordless methods registered yet</p>
                </div>
            `;
            return;
        }

        listEl.innerHTML = `
            <h4 style="margin-bottom: 1rem; color: var(--text-primary);">
                <i class="fas fa-shield-alt"></i> Registered Methods (${credentials.length})
            </h4>
        ` + credentials.map(cred => `
            <div class="credential-item" data-id="${cred.id}">
                <div class="credential-icon">
                    <i class="fas fa-fingerprint"></i>
                </div>
                <div class="credential-info">
                    <strong>${cred.credential_name || 'My Device'}</strong>
                    <span class="credential-type">Biometric / Passkey</span>
    }

    /**
     * Initialize the UI based on capabilities
     */
    async initUI() {
        const capabilities = await this.checkCapabilities();

        const biometricSection = document.getElementById('biometric-section');
        const passkeySection = document.getElementById('passkey-section');
        const noWebAuthnMessage = document.getElementById('no-webauthn-message');

        if (!capabilities.webauthn) {
            // WebAuthn not supported
            if (biometricSection) biometricSection.style.display = 'none';
            if (passkeySection) passkeySection.style.display = 'none';
            if (noWebAuthnMessage) noWebAuthnMessage.style.display = 'block';
            return;
        }

        if (capabilities.canRegisterBiometric) {
            // Show biometric as primary option
            if (biometricSection) biometricSection.style.display = 'block';
            if (passkeySection) passkeySection.style.display = 'block';
        } else {
            // Only show passkey option
            if (biometricSection) biometricSection.style.display = 'none';
            if (passkeySection) passkeySection.style.display = 'block';
        }

        // Load existing credentials
        await this.refreshCredentialsList();

        // Setup button handlers
        this.setupEventHandlers();
    }

    /**
     * Setup event handlers for registration buttons
     */
    setupEventHandlers() {
        const biometricBtn = document.getElementById('registerBiometric');
        const passkeyBtn = document.getElementById('registerPasskey');

        if (biometricBtn) {
            biometricBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                biometricBtn.disabled = true;
                biometricBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';

                await this.registerBiometric();

                biometricBtn.disabled = false;
                biometricBtn.innerHTML = '<i class="fas fa-fingerprint"></i> Register Fingerprint / Face ID';
            });
        }

        if (passkeyBtn) {
            passkeyBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                passkeyBtn.disabled = true;
                passkeyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';

                await this.registerPasskey();

                passkeyBtn.disabled = false;
                passkeyBtn.innerHTML = '<i class="fas fa-key"></i> Register Security Key / Phone';
            });
        }
    }

    // ==================== Helper Methods ====================

    /**
     * Detect device name from user agent
     */
    detectDeviceName() {
        const ua = navigator.userAgent;

        if (/iPhone/.test(ua)) return 'iPhone';
        if (/iPad/.test(ua)) return 'iPad';
        if (/Mac/.test(ua)) return 'Mac';
        if (/Android/.test(ua)) return 'Android Device';
        if (/Windows/.test(ua)) return 'Windows PC';
        if (/Linux/.test(ua)) return 'Linux Device';

        return 'Unknown Device';
    }

    /**
     * Get icon for credential type
     */
    getCredentialIcon(type) {
        const icons = {
            'biometric': '<i class="fas fa-fingerprint"></i>',
            'passkey': '<i class="fas fa-key"></i>',
            'security_key': '<i class="fas fa-usb"></i>'
        };
        return icons[type] || '<i class="fas fa-shield-alt"></i>';
    }

    /**
     * Format credential type for display
     */
    formatCredentialType(type) {
        const types = {
            'biometric': 'Fingerprint / Face ID',
            'passkey': 'Passkey',
            'security_key': 'Security Key'
        };
        return types[type] || type;
    }

    /**
     * Format date for display
     */
    formatDate(dateStr) {
        if (!dateStr) return 'Never';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-NG', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    /**
     * Convert base64url to ArrayBuffer
     */
    base64UrlToArrayBuffer(base64url) {
        const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
        const padding = '='.repeat((4 - base64.length % 4) % 4);
        const binary = atob(base64 + padding);
        const bytes = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        return bytes.buffer;
    }

    /**
     * Convert ArrayBuffer to base64url
     */
    arrayBufferToBase64Url(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.length; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    }

    /**
     * Default success handler
     */
    defaultOnSuccess(type, data) {
        const message = type === 'biometric'
            ? 'Fingerprint / Face ID registered successfully!'
            : 'Passkey registered successfully!';

        this.showNotification(message, 'success');
    }

    /**
     * Default error handler
     */
    defaultOnError(message) {
        this.showNotification(message, 'error');
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        // Try to use existing notification system
        if (typeof showToast === 'function') {
            showToast(message, type);
            return;
        }

        // Fallback notification
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            background: ${type === 'success' ? '#4CAF50' : '#f44336'};
            color: white;
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Global instance for easy access
let biometricRegister;

/**
 * Initialize biometric registration when DOM is ready
 */
function initBiometricRegister() {
    biometricRegister = new BiometricRegister({
        onSuccess: (type, data) => {
            biometricRegister.showNotification(
                `${type === 'biometric' ? 'Biometric' : 'Passkey'} registered successfully!`,
                'success'
            );
        },
        onError: (message) => {
            biometricRegister.showNotification(message, 'error');
        }
    });

    biometricRegister.initUI();
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBiometricRegister);
} else {
    initBiometricRegister();
}

// CSS animations (inject if not present)
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }

    .credential-item {
        display: flex;
        align-items: center;
        padding: 15px;
        background: rgba(255,255,255,0.05);
        border-radius: 8px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }

    .credential-item:hover {
        background: rgba(255,255,255,0.1);
    }

    .credential-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #00f0ff, #00d4aa);
        border-radius: 50%;
        margin-right: 15px;
        font-size: 1.5rem;
        color: #000;
    }

    .credential-info {
        flex: 1;
    }

    .credential-info strong {
        display: block;
        margin-bottom: 5px;
    }

    .credential-type {
        display: inline-block;
        padding: 2px 8px;
        background: rgba(0,240,255,0.2);
        border-radius: 4px;
        font-size: 0.8rem;
        margin-right: 10px;
    }

    .credential-date {
        font-size: 0.75rem;
        opacity: 0.7;
    }

    .btn-delete-credential {
        padding: 10px;
        background: rgba(255,0,0,0.2);
        border: none;
        border-radius: 8px;
        color: #ff4444;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-delete-credential:hover {
        background: rgba(255,0,0,0.4);
    }

    .no-credentials {
        text-align: center;
        padding: 30px;
        opacity: 0.7;
    }

    .no-credentials i {
        font-size: 3rem;
        margin-bottom: 15px;
        display: block;
    }
`;
document.head.appendChild(style);

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { BiometricRegister, initBiometricRegister };
}
