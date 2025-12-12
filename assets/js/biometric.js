/**
 * VERDANT SMS v3.0 — WebAuthn/Biometric Manager
 * Client-side WebAuthn API handler for fingerprint/Face ID/passkey login
 */

class BiometricManager {
    constructor() {
        this.apiBase = '/api/webauthn.php';
    }

    /**
     * Check if WebAuthn is supported
     */
    isSupported() {
        return window.PublicKeyCredential !== undefined;
    }

    /**
     * Check if platform authenticator (fingerprint/Face ID) is available
     */
    async isPlatformAuthenticatorAvailable() {
        if (!this.isSupported()) return false;
        try {
            return await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
        } catch {
            return false;
        }
    }

    /**
     * Convert base64url to ArrayBuffer
     */
    base64urlToBuffer(base64url) {
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
    bufferToBase64url(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.length; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    }

    /**
     * Register a new biometric credential
     */
    async register(credentialName = 'My Device') {
        try {
            // Get registration options from server
            const optionsRes = await fetch(`${this.apiBase}?action=register_options`);
            const optionsData = await optionsRes.json();
            
            if (!optionsData.success) {
                throw new Error(optionsData.message);
            }

            const options = optionsData.options;
            
            // Convert base64url to ArrayBuffer
            options.challenge = this.base64urlToBuffer(options.challenge);
            options.user.id = this.base64urlToBuffer(options.user.id);
            
            if (options.excludeCredentials) {
                options.excludeCredentials = options.excludeCredentials.map(cred => ({
                    ...cred,
                    id: this.base64urlToBuffer(cred.id)
                }));
            }

            // Create credential
            const credential = await navigator.credentials.create({
                publicKey: options
            });

            // Prepare credential for server
            const credentialData = {
                id: this.bufferToBase64url(credential.rawId),
                type: credential.type,
                response: {
                    clientDataJSON: this.bufferToBase64url(credential.response.clientDataJSON),
                    attestationObject: this.bufferToBase64url(credential.response.attestationObject)
                },
                transports: credential.response.getTransports ? credential.response.getTransports() : []
            };

            // Send to server
            const registerRes = await fetch(`${this.apiBase}?action=register`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    credential: credentialData,
                    name: credentialName
                })
            });

            const registerData = await registerRes.json();
            
            if (!registerData.success) {
                throw new Error(registerData.message);
            }

            return { success: true, message: 'Biometric registered successfully!' };

        } catch (error) {
            console.error('Biometric registration error:', error);
            return { success: false, message: error.message || 'Failed to register biometric' };
        }
    }

    /**
     * Authenticate with biometric
     */
    async login(email = '') {
        try {
            // Get authentication options from server
            const optionsRes = await fetch(`${this.apiBase}?action=login_options`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email })
            });
            const optionsData = await optionsRes.json();
            
            if (!optionsData.success) {
                throw new Error(optionsData.message);
            }

            const options = optionsData.options;
            
            // Convert base64url to ArrayBuffer
            options.challenge = this.base64urlToBuffer(options.challenge);
            
            if (options.allowCredentials && options.allowCredentials.length > 0) {
                options.allowCredentials = options.allowCredentials.map(cred => ({
                    ...cred,
                    id: this.base64urlToBuffer(cred.id)
                }));
            }

            // Get credential
            const credential = await navigator.credentials.get({
                publicKey: options
            });

            // Prepare credential for server
            const credentialData = {
                id: this.bufferToBase64url(credential.rawId),
                type: credential.type,
                response: {
                    clientDataJSON: this.bufferToBase64url(credential.response.clientDataJSON),
                    authenticatorData: this.bufferToBase64url(credential.response.authenticatorData),
                    signature: this.bufferToBase64url(credential.response.signature),
                    userHandle: credential.response.userHandle ? 
                        this.bufferToBase64url(credential.response.userHandle) : null
                }
            };

            // Send to server
            const loginRes = await fetch(`${this.apiBase}?action=login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ credential: credentialData })
            });

            const loginData = await loginRes.json();
            
            if (!loginData.success) {
                throw new Error(loginData.message);
            }

            return { 
                success: true, 
                message: 'Login successful!',
                redirect: loginData.redirect 
            };

        } catch (error) {
            console.error('Biometric login error:', error);
            return { success: false, message: error.message || 'Failed to authenticate' };
        }
    }

    /**
     * List registered credentials
     */
    async listCredentials() {
        try {
            const res = await fetch(`${this.apiBase}?action=list`);
            const data = await res.json();
            return data;
        } catch (error) {
            return { success: false, message: error.message };
        }
    }

    /**
     * Delete a credential
     */
    async deleteCredential(id) {
        try {
            const res = await fetch(`${this.apiBase}?action=delete`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            const data = await res.json();
            return data;
        } catch (error) {
            return { success: false, message: error.message };
        }
    }
}

// Global instance
window.biometricManager = new BiometricManager();

// UI Helper Functions
function showBiometricButton() {
    if (window.biometricManager.isSupported()) {
        document.querySelectorAll('.biometric-btn').forEach(btn => {
            btn.style.display = 'inline-flex';
        });
    }
}

async function biometricLogin(email = '') {
    const btn = document.querySelector('.biometric-login-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authenticating...';
    }
    
    const result = await window.biometricManager.login(email);
    
    if (result.success) {
        window.location.href = result.redirect;
    } else {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-fingerprint"></i> Login with Biometrics';
        }
        alert(result.message);
    }
}

async function registerBiometric() {
    const name = prompt('Name this device (e.g., "My Laptop", "Phone"):', 'My Device');
    if (!name) return;
    
    const btn = document.querySelector('.biometric-register-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
    }
    
    const result = await window.biometricManager.register(name);
    
    if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-fingerprint"></i> Register Biometrics';
    }
    
    alert(result.message);
    
    if (result.success) {
        location.reload();
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', showBiometricButton);
