/**
 * WebAuthn Login Manager
 * Handles biometric and passkey authentication for Verdant SMS
 *
 * @package VerdantSMS
 * @since 3.0.0
 */

class BiometricLogin {
    constructor(options = {}) {
        this.apiEndpoint = options.apiEndpoint || '/attendance/api/webauthn.php';
        this.onSuccess = options.onSuccess || this.defaultOnSuccess;
        this.onError = options.onError || this.defaultOnError;
        this.onFallback = options.onFallback || this.showPasswordFallback;
    }

    /**
     * Check if WebAuthn is supported in this browser
     * @returns {Promise<object>} Capabilities object
     */
    async checkSupport() {
        const capabilities = {
            webauthn: false,
            platformAuthenticator: false,
            conditionalUI: false
        };

        if (!window.PublicKeyCredential) {
            return capabilities;
        }

        capabilities.webauthn = true;

        try {
            capabilities.platformAuthenticator =
                await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
        } catch (e) {
            console.warn('Platform authenticator check failed:', e);
        }

        try {
            if (PublicKeyCredential.isConditionalMediationAvailable) {
                capabilities.conditionalUI =
                    await PublicKeyCredential.isConditionalMediationAvailable();
            }
        } catch (e) {
            console.warn('Conditional UI check failed:', e);
        }

        return capabilities;
    }

    /**
     * Attempt biometric login
     * @param {string} email User's email
     * @returns {Promise<boolean>} Success status
     */
    async attemptBiometric(email) {
        try {
            // 1. Get authentication options from server
            const optionsResponse = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'get_auth_options',
                    email: email
                })
            });

            const optionsData = await optionsResponse.json();

            if (!optionsData.success) {
                // No credentials registered - show fallback
                this.onFallback('No biometric credentials registered');
                return false;
            }

            const options = optionsData.options;

            // 2. Convert base64url to ArrayBuffer
            options.challenge = this.base64UrlToArrayBuffer(options.challenge);
            options.allowCredentials = options.allowCredentials.map(cred => ({
                ...cred,
                id: this.base64UrlToArrayBuffer(cred.id)
            }));

            // 3. Call WebAuthn API with biometric preference
            const assertion = await navigator.credentials.get({
                publicKey: {
                    ...options,
                    userVerification: 'required'
                }
            });

            // 4. Send assertion to server for verification
            const verifyResponse = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'verify_auth',
                    email: email,
                    assertion: {
                        id: this.arrayBufferToBase64Url(assertion.rawId),
                        clientDataJSON: this.arrayBufferToBase64Url(assertion.response.clientDataJSON),
                        authenticatorData: this.arrayBufferToBase64Url(assertion.response.authenticatorData),
                        signature: this.arrayBufferToBase64Url(assertion.response.signature)
                    }
                })
            });

            const verifyData = await verifyResponse.json();

            if (verifyData.success) {
                this.onSuccess(verifyData);
                return true;
            } else {
                this.onError(verifyData.error || 'Verification failed');
                return false;
            }

        } catch (error) {
            if (error.name === 'NotAllowedError') {
                // User cancelled or timeout
                this.onFallback('Authentication cancelled');
            } else if (error.name === 'NotSupportedError') {
                this.onFallback('Biometric not supported on this device');
            } else {
                console.error('Biometric login error:', error);
                this.onError('Biometric authentication failed');
            }
            return false;
        }
    }

    /**
     * Attempt passkey login (security key or phone)
     * @param {string} email User's email
     * @returns {Promise<boolean>} Success status
     */
    async attemptPasskey(email) {
        // Same as biometric but without platform authenticator restriction
        return this.attemptBiometric(email);
    }

    /**
     * Check if user has any registered credentials
     * @param {string} email User's email
     * @returns {Promise<object>} Credential info
     */
    async checkCredentials(email) {
        try {
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'check_credentials',
                    email: email
                })
            });

            return await response.json();
        } catch (error) {
            console.error('Credential check error:', error);
            return { success: false, has_credentials: false };
        }
    }

    /**
     * Show password fallback form
     * @param {string} reason Reason for fallback
     */
    showPasswordFallback(reason = '') {
        const biometricSection = document.getElementById('biometric-login-section');
        const passwordSection = document.getElementById('password-login-section');

        if (biometricSection) {
            biometricSection.style.display = 'none';
        }

        if (passwordSection) {
            passwordSection.style.display = 'block';
        }

        if (reason) {
            console.log('Fallback reason:', reason);
        }
    }

    /**
     * Default success handler
     * @param {object} data Response data
     */
    defaultOnSuccess(data) {
        if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            window.location.href = '/';
        }
    }

    /**
     * Default error handler
     * @param {string} message Error message
     */
    defaultOnError(message) {
        const errorEl = document.getElementById('login-error');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        } else {
            alert(message);
        }
    }

    // ==================== Helper Methods ====================

    /**
     * Convert base64url to ArrayBuffer
     * @param {string} base64url Base64URL encoded string
     * @returns {ArrayBuffer}
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
     * @param {ArrayBuffer} buffer ArrayBuffer
     * @returns {string} Base64URL encoded string
     */
    arrayBufferToBase64Url(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.length; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
    }
}

/**
 * Initialize biometric login on page load
 */
async function initBiometricLogin() {
    const emailInput = document.getElementById('email');
    const biometricBtn = document.getElementById('biometric-login-btn');
    const biometricSection = document.getElementById('biometric-login-section');
    const passwordSection = document.getElementById('password-login-section');

    const login = new BiometricLogin({
        onSuccess: (data) => {
            // Show success message briefly before redirect
            const successEl = document.createElement('div');
            successEl.className = 'login-success';
            successEl.innerHTML = '<i class="fas fa-check-circle"></i> Login successful!';
            document.querySelector('.login-form')?.prepend(successEl);

            setTimeout(() => {
                window.location.href = data.redirect || '/';
            }, 500);
        },
        onError: (message) => {
            const errorEl = document.getElementById('login-error');
            if (errorEl) {
                errorEl.textContent = message;
                errorEl.style.display = 'block';
            }
        },
        onFallback: (reason) => {
            if (biometricSection) biometricSection.style.display = 'none';
            if (passwordSection) passwordSection.style.display = 'block';
            console.log('Fallback to password:', reason);
        }
    });

    // Check WebAuthn support
    const capabilities = await login.checkSupport();

    if (!capabilities.webauthn) {
        // No WebAuthn support - show password form
        if (biometricSection) biometricSection.style.display = 'none';
        if (passwordSection) passwordSection.style.display = 'block';
        return;
    }

    // Handle biometric login button click
    if (biometricBtn) {
        biometricBtn.addEventListener('click', async (e) => {
            e.preventDefault();

            const email = emailInput?.value?.trim();
            if (!email) {
                login.onError('Please enter your email first');
                return;
            }

            biometricBtn.disabled = true;
            biometricBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authenticating...';

            const success = await login.attemptBiometric(email);

            if (!success) {
                biometricBtn.disabled = false;
                biometricBtn.innerHTML = '<i class="fas fa-fingerprint"></i> Login with Biometric';
            }
        });
    }

    // Check if email field has value (e.g., from remember me)
    if (emailInput && emailInput.value) {
        const credentials = await login.checkCredentials(emailInput.value);
        if (credentials.has_credentials) {
            // User has credentials - prioritize biometric login
            if (biometricSection) biometricSection.style.display = 'block';
            if (passwordSection) passwordSection.classList.add('secondary-option');
        }
    }

    // Check credentials when email changes
    if (emailInput) {
        let debounceTimer;
        emailInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(async () => {
                const email = emailInput.value.trim();
                if (email && email.includes('@')) {
                    const credentials = await login.checkCredentials(email);
                    if (credentials.has_credentials && biometricSection) {
                        biometricSection.style.display = 'block';
                    }
                }
            }, 500);
        });
    }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBiometricLogin);
} else {
    initBiometricLogin();
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { BiometricLogin, initBiometricLogin };
}
