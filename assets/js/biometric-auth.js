/**
 * Biometric Authentication Handler
 * Handles WebAuthn API for fingerprint/face recognition
 */

class BiometricAuth {
    constructor() {
        this.apiBase = '/attendance/api/webauthn.php';
        this.supported = this.checkSupport();
    }

    /**
     * Check if WebAuthn is supported
     */
    checkSupport() {
        return window.PublicKeyCredential !== undefined &&
               navigator.credentials !== undefined;
    }

    /**
     * Convert base64 to ArrayBuffer
     */
    base64ToArrayBuffer(base64) {
        const binaryString = window.atob(base64);
        const bytes = new Uint8Array(binaryString.length);
        for (let i = 0; i < binaryString.length; i++) {
            bytes[i] = binaryString.charCodeAt(i);
        }
        return bytes.buffer;
    }

    /**
     * Convert ArrayBuffer to base64
     */
    arrayBufferToBase64(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return window.btoa(binary);
    }

    /**
     * Register new biometric credential (passkey)
     */
    async register(credentialName = 'My Device') {
        if (!this.supported) {
            throw new Error('Passkey authentication is not supported on this device');
        }

        try {
            // Get registration options from server
            const response = await fetch(`${this.apiBase}?action=register_options`);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to start registration');
            }

            // Convert base64url to ArrayBuffer for WebAuthn API
            const options = data.options;
            options.challenge = this.base64urlToArrayBuffer(options.challenge);
            options.user.id = this.base64urlToArrayBuffer(options.user.id);

            if (options.excludeCredentials) {
                options.excludeCredentials = options.excludeCredentials.map(cred => ({
                    ...cred,
                    id: this.base64urlToArrayBuffer(cred.id)
                }));
            }

            // Create credential using WebAuthn
            const credential = await navigator.credentials.create({ publicKey: options });

            if (!credential) {
                throw new Error('Failed to create passkey');
            }

            // Prepare credential data for server
            const credentialData = {
                credential: {
                    id: this.arrayBufferToBase64url(credential.rawId),
                    type: credential.type,
                    response: {
                        clientDataJSON: this.arrayBufferToBase64url(credential.response.clientDataJSON),
                        attestationObject: this.arrayBufferToBase64url(credential.response.attestationObject)
                    },
                    transports: credential.response.getTransports ? credential.response.getTransports() : []
                },
                name: credentialName
            };

            // Send credential to server
            const registerResponse = await fetch(`${this.apiBase}?action=register`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(credentialData)
            });

            const result = await registerResponse.json();

            if (!result.success) {
                throw new Error(result.message || 'Failed to complete registration');
            }

            return result;

        } catch (error) {
            console.error('Passkey registration error:', error);
            throw error;
        }
    }

    /**
     * Convert base64url to ArrayBuffer
     */
    base64urlToArrayBuffer(base64url) {
        const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
        const padding = '='.repeat((4 - base64.length % 4) % 4);
        const binaryString = window.atob(base64 + padding);
        const bytes = new Uint8Array(binaryString.length);
        for (let i = 0; i < binaryString.length; i++) {
            bytes[i] = binaryString.charCodeAt(i);
        }
        return bytes.buffer;
    }

    /**
     * Convert ArrayBuffer to base64url
     */
    arrayBufferToBase64url(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        const base64 = window.btoa(binary);
        return base64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    }

    /**
     * Login with passkey
     */
    async login(email = '') {
        if (!this.supported) {
            throw new Error('Passkey authentication is not supported on this device');
        }

        try {
            // Get login options from server
            const response = await fetch(`${this.apiBase}?action=login_options`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email })
            });
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to start login');
            }

            // Convert base64url to ArrayBuffer for WebAuthn API
            const options = data.options;
            options.challenge = this.base64urlToArrayBuffer(options.challenge);

            if (options.allowCredentials && options.allowCredentials.length > 0) {
                options.allowCredentials = options.allowCredentials.map(cred => ({
                    ...cred,
                    id: this.base64urlToArrayBuffer(cred.id)
                }));
            }

            // Get assertion using WebAuthn
            const assertion = await navigator.credentials.get({ publicKey: options });

            if (!assertion) {
                throw new Error('Passkey verification failed');
            }

            // Send assertion to server
            const loginData = {
                credential: {
                    id: this.arrayBufferToBase64url(assertion.rawId),
                    type: assertion.type,
                    response: {
                        authenticatorData: this.arrayBufferToBase64url(assertion.response.authenticatorData),
                        clientDataJSON: this.arrayBufferToBase64url(assertion.response.clientDataJSON),
                        signature: this.arrayBufferToBase64url(assertion.response.signature),
                        userHandle: assertion.response.userHandle ?
                            this.arrayBufferToBase64url(assertion.response.userHandle) : null
                    }
                }
            };

            const loginResponse = await fetch(`${this.apiBase}?action=login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(loginData)
            });

            const result = await loginResponse.json();

            if (!result.success) {
                throw new Error(result.message || 'Login failed');
            }

            // Redirect if provided
            if (result.redirect) {
                window.location.href = result.redirect;
            }

            return result;

        } catch (error) {
            console.error('Passkey login error:', error);
            throw error;
        }
    }

    /**
     * Simplified fingerprint scan for attendance
     */
    async quickScan() {
        try {
            const result = await this.login();
            return {
                success: true,
                user: result.user,
                timestamp: new Date().toISOString()
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    /**
     * List registered credentials
     */
    async listCredentials() {
        const response = await fetch(`${this.apiBase}?action=list_credentials`);
        return await response.json();
    }

    /**
     * Delete credential
     */
    async deleteCredential(credentialId) {
        const response = await fetch(`${this.apiBase}?action=delete_credential`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `credential_id=${encodeURIComponent(credentialId)}`
        });
        return await response.json();
    }
}

// Create global instance
window.biometricAuth = new BiometricAuth();

// Export for modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BiometricAuth;
}
