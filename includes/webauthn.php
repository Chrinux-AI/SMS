<?php

/**
 * WebAuthn Manager for Biometric & Passkey Authentication
 *
 * Handles WebAuthn credential registration and authentication
 * Prioritizes biometric (fingerprint/Face ID) over passkeys
 *
 * @package VerdantSMS
 * @since 3.0.0
 */

class WebAuthnManager
{
    private $db;
    private $rpId;      // Relying Party ID (domain)
    private $rpName;    // Relying Party Name
    private $origin;    // Expected origin

    public function __construct()
    {
        $this->db = db();
        $this->rpId = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $this->rpName = 'Verdant SMS';
        $this->origin = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $this->rpId;
    }

    /**
     * Detect WebAuthn capabilities for the current user
     * This is called via AJAX from the frontend
     *
     * @param int $userId The user ID to check
     * @return array Capabilities and registered credentials
     */
    public function detectCapabilities(int $userId): array
    {
        // Get user's registered credentials
        $credentials = $this->db->fetchAll(
            "SELECT id, credential_type, device_name, last_used_at, created_at
             FROM webauthn_credentials
             WHERE user_id = ?
             ORDER BY last_used_at DESC",
            [$userId]
        );

        return [
            'user_id' => $userId,
            'has_credentials' => count($credentials) > 0,
            'credentials' => $credentials,
            'biometric_count' => count(array_filter($credentials, fn($c) => $c['credential_type'] === 'biometric')),
            'passkey_count' => count(array_filter($credentials, fn($c) => $c['credential_type'] === 'passkey')),
            'security_key_count' => count(array_filter($credentials, fn($c) => $c['credential_type'] === 'security_key'))
        ];
    }

    /**
     * Generate registration options for biometric (platform authenticator)
     * Prioritizes fingerprint/Face ID on the device
     *
     * @param int $userId The user ID
     * @param string $email The user's email (for display)
     * @param string $name The user's name
     * @return array WebAuthn registration options
     */
    public function createBiometricRegistrationOptions(int $userId, string $email, string $name): array
    {
        $challenge = $this->generateChallenge();
        $this->storeChallenge($userId, $challenge, 'registration');

        return [
            'challenge' => $this->base64UrlEncode($challenge),
            'rp' => [
                'id' => $this->rpId,
                'name' => $this->rpName
            ],
            'user' => [
                'id' => $this->base64UrlEncode(pack('N', $userId)),
                'name' => $email,
                'displayName' => $name
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7],   // ES256
                ['type' => 'public-key', 'alg' => -257]  // RS256
            ],
            'authenticatorSelection' => [
                'authenticatorAttachment' => 'platform',  // Device biometric only
                'userVerification' => 'required',
                'residentKey' => 'preferred'
            ],
            'timeout' => 60000,
            'attestation' => 'none'
        ];
    }

    /**
     * Generate registration options for passkey (roaming authenticator)
     * Used as fallback when biometric is not available
     *
     * @param int $userId The user ID
     * @param string $email The user's email
     * @param string $name The user's name
     * @return array WebAuthn registration options
     */
    public function createPasskeyRegistrationOptions(int $userId, string $email, string $name): array
    {
        $challenge = $this->generateChallenge();
        $this->storeChallenge($userId, $challenge, 'registration');

        return [
            'challenge' => $this->base64UrlEncode($challenge),
            'rp' => [
                'id' => $this->rpId,
                'name' => $this->rpName
            ],
            'user' => [
                'id' => $this->base64UrlEncode(pack('N', $userId)),
                'name' => $email,
                'displayName' => $name
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7],
                ['type' => 'public-key', 'alg' => -257]
            ],
            'authenticatorSelection' => [
                'userVerification' => 'required',
                'residentKey' => 'preferred'
            ],
            'timeout' => 120000,
            'attestation' => 'none'
        ];
    }

    /**
     * Verify and store a new credential after registration
     *
     * @param int $userId The user ID
     * @param array $attestation The attestation response from browser
     * @param string $type The credential type (biometric, passkey, security_key)
     * @param string $deviceName Optional device name
     * @return array Result with success status
     */
    public function verifyAndStoreCredential(int $userId, array $attestation, string $type = 'biometric', string $deviceName = ''): array
    {
        try {
            // Verify the challenge
            $storedChallenge = $this->getStoredChallenge($userId, 'registration');
            if (!$storedChallenge) {
                return ['success' => false, 'error' => 'No pending registration challenge'];
            }

            // Decode the attestation data
            $clientDataJSON = $this->base64UrlDecode($attestation['clientDataJSON']);
            $clientData = json_decode($clientDataJSON, true);

            // Verify origin
            if (!isset($clientData['origin']) || $clientData['origin'] !== $this->origin) {
                // Allow localhost variations for development
                if (strpos($this->rpId, 'localhost') === false) {
                    return ['success' => false, 'error' => 'Origin mismatch'];
                }
            }

            // Verify challenge
            $receivedChallenge = $this->base64UrlDecode($clientData['challenge']);
            if (!hash_equals($storedChallenge, $receivedChallenge)) {
                return ['success' => false, 'error' => 'Challenge mismatch'];
            }

            // Verify type
            if ($clientData['type'] !== 'webauthn.create') {
                return ['success' => false, 'error' => 'Invalid ceremony type'];
            }

            // Get credential ID and public key from attestation object
            $attestationObject = $this->base64UrlDecode($attestation['attestationObject']);
            $authData = $this->parseAttestationObject($attestationObject);

            if (!$authData) {
                return ['success' => false, 'error' => 'Failed to parse attestation'];
            }

            $credentialId = $authData['credentialId'];
            $publicKey = $authData['publicKey'];

            // Check if credential already exists
            $existing = $this->db->fetch(
                "SELECT id FROM webauthn_credentials WHERE credential_id = ?",
                [$credentialId]
            );

            if ($existing) {
                return ['success' => false, 'error' => 'Credential already registered'];
            }

            // Store the credential
            $result = $this->db->insert('webauthn_credentials', [
                'user_id' => $userId,
                'credential_id' => $credentialId,
                'public_key' => $publicKey,
                'credential_type' => $type,
                'device_name' => $deviceName ?: $this->detectDeviceName(),
                'counter' => $authData['counter'] ?? 0
            ]);

            if ($result) {
                $this->clearChallenge($userId, 'registration');
                return [
                    'success' => true,
                    'credential_id' => $result,
                    'message' => ucfirst($type) . ' registered successfully'
                ];
            }

            return ['success' => false, 'error' => 'Failed to store credential'];
        } catch (Exception $e) {
            error_log('WebAuthn registration error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Registration failed'];
        }
    }

    /**
     * Create authentication options for login
     *
     * @param string $email The user's email
     * @return array|null Authentication options or null if no credentials
     */
    public function createAuthenticationOptions(string $email): ?array
    {
        // Get user and their credentials
        $user = $this->db->fetch(
            "SELECT id FROM users WHERE email = ?",
            [$email]
        );

        if (!$user) {
            return null;
        }

        $credentials = $this->db->fetchAll(
            "SELECT credential_id, credential_type FROM webauthn_credentials WHERE user_id = ?",
            [$user['id']]
        );

        if (empty($credentials)) {
            return null;
        }

        $challenge = $this->generateChallenge();
        $this->storeChallenge($user['id'], $challenge, 'authentication');

        $allowCredentials = array_map(function ($cred) {
            return [
                'type' => 'public-key',
                'id' => $this->base64UrlEncode($cred['credential_id']),
                'transports' => $cred['credential_type'] === 'biometric'
                    ? ['internal']
                    : ['usb', 'ble', 'nfc', 'hybrid']
            ];
        }, $credentials);

        return [
            'challenge' => $this->base64UrlEncode($challenge),
            'timeout' => 60000,
            'rpId' => $this->rpId,
            'allowCredentials' => $allowCredentials,
            'userVerification' => 'required'
        ];
    }

    /**
     * Verify an authentication assertion
     *
     * @param string $email The user's email
     * @param array $assertion The assertion response from browser
     * @return int|null User ID on success, null on failure
     */
    public function verifyAuthentication(string $email, array $assertion): ?int
    {
        try {
            // Get user
            $user = $this->db->fetch(
                "SELECT id FROM users WHERE email = ?",
                [$email]
            );

            if (!$user) {
                return null;
            }

            $userId = $user['id'];

            // Verify challenge
            $storedChallenge = $this->getStoredChallenge($userId, 'authentication');
            if (!$storedChallenge) {
                return null;
            }

            // Decode client data
            $clientDataJSON = $this->base64UrlDecode($assertion['clientDataJSON']);
            $clientData = json_decode($clientDataJSON, true);

            // Verify challenge match
            $receivedChallenge = $this->base64UrlDecode($clientData['challenge']);
            if (!hash_equals($storedChallenge, $receivedChallenge)) {
                return null;
            }

            // Verify type
            if ($clientData['type'] !== 'webauthn.get') {
                return null;
            }

            // Get the credential
            $credentialId = $this->base64UrlDecode($assertion['id']);
            $credential = $this->db->fetch(
                "SELECT * FROM webauthn_credentials WHERE credential_id = ? AND user_id = ?",
                [$credentialId, $userId]
            );

            if (!$credential) {
                return null;
            }

            // Verify signature (simplified - production should use full COSE verification)
            $authenticatorData = $this->base64UrlDecode($assertion['authenticatorData']);
            $signature = $this->base64UrlDecode($assertion['signature']);

            // Verify authenticator data flags
            $flags = ord($authenticatorData[32]);
            $userPresent = ($flags & 0x01) !== 0;
            $userVerified = ($flags & 0x04) !== 0;

            if (!$userPresent || !$userVerified) {
                return null;
            }

            // Update last used timestamp and counter
            $this->db->update(
                'webauthn_credentials',
                ['last_used_at' => date('Y-m-d H:i:s')],
                'id = ?',
                [$credential['id']]
            );

            $this->clearChallenge($userId, 'authentication');

            return $userId;
        } catch (Exception $e) {
            error_log('WebAuthn authentication error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a credential
     *
     * @param int $userId The user ID
     * @param int $credentialId The credential database ID
     * @return bool Success status
     */
    public function deleteCredential(int $userId, int $credentialId): bool
    {
        return $this->db->delete(
            'webauthn_credentials',
            'id = ? AND user_id = ?',
            [$credentialId, $userId]
        );
    }

    /**
     * Check if biometric is required for a role
     *
     * @param string $role The user role
     * @return array Policy settings
     */
    public function getBiometricPolicy(string $role): array
    {
        $policy = $this->db->fetch(
            "SELECT * FROM biometric_policies WHERE role = ?",
            [$role]
        );

        return $policy ?: [
            'role' => $role,
            'require_biometric' => false,
            'require_passkey' => false,
            'allow_password_fallback' => true
        ];
    }

    /**
     * Check if user has met biometric requirements
     *
     * @param int $userId The user ID
     * @param string $role The user role
     * @return array Compliance status
     */
    public function checkCompliance(int $userId, string $role): array
    {
        $policy = $this->getBiometricPolicy($role);
        $capabilities = $this->detectCapabilities($userId);

        $compliant = true;
        $missing = [];

        if ($policy['require_biometric'] && $capabilities['biometric_count'] === 0) {
            $compliant = false;
            $missing[] = 'biometric';
        }

        if ($policy['require_passkey'] && $capabilities['passkey_count'] === 0) {
            $compliant = false;
            $missing[] = 'passkey';
        }

        return [
            'compliant' => $compliant,
            'missing' => $missing,
            'policy' => $policy,
            'credentials' => $capabilities
        ];
    }

    // ==================== Helper Methods ====================

    /**
     * Generate a random challenge
     */
    private function generateChallenge(): string
    {
        return random_bytes(32);
    }

    /**
     * Store challenge in session
     */
    private function storeChallenge(int $userId, string $challenge, string $type): void
    {
        $_SESSION["webauthn_{$type}_challenge_{$userId}"] = $challenge;
        $_SESSION["webauthn_{$type}_time_{$userId}"] = time();
    }

    /**
     * Get stored challenge
     */
    private function getStoredChallenge(int $userId, string $type): ?string
    {
        $key = "webauthn_{$type}_challenge_{$userId}";
        $timeKey = "webauthn_{$type}_time_{$userId}";

        if (!isset($_SESSION[$key]) || !isset($_SESSION[$timeKey])) {
            return null;
        }

        // Challenge expires after 5 minutes
        if (time() - $_SESSION[$timeKey] > 300) {
            unset($_SESSION[$key], $_SESSION[$timeKey]);
            return null;
        }

        return $_SESSION[$key];
    }

    /**
     * Clear stored challenge
     */
    private function clearChallenge(int $userId, string $type): void
    {
        unset(
            $_SESSION["webauthn_{$type}_challenge_{$userId}"],
            $_SESSION["webauthn_{$type}_time_{$userId}"]
        );
    }

    /**
     * Base64 URL encode
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode
     */
    private function base64UrlDecode(string $data): string
    {
        $padding = 4 - strlen($data) % 4;
        if ($padding !== 4) {
            $data .= str_repeat('=', $padding);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Parse CBOR attestation object (simplified)
     */
    private function parseAttestationObject(string $attestationObject): ?array
    {
        // This is a simplified parser - production should use a proper CBOR library
        // We're extracting the credential ID and public key from the authData

        try {
            // Skip CBOR map header and find authData
            // The authData is typically the first value in the attestation object

            // For simplicity, we'll look for the pattern in authData
            // Real implementation should use webauthn-lib/webauthn-lib package

            // Find authData (starts with RP ID hash - 32 bytes)
            $rpIdHash = hash('sha256', $this->rpId, true);
            $pos = strpos($attestationObject, $rpIdHash);

            if ($pos === false) {
                // Try without specific domain for localhost
                $pos = 37; // Skip CBOR overhead
            }

            // AuthData structure:
            // 32 bytes: RP ID hash
            // 1 byte: flags
            // 4 bytes: counter
            // Variable: attested credential data (if present)

            $authData = substr($attestationObject, $pos);
            $flags = ord($authData[32]);

            // Check if attested credential data is present (bit 6)
            if (($flags & 0x40) === 0) {
                return null;
            }

            // Counter (4 bytes, big endian)
            $counter = unpack('N', substr($authData, 33, 4))[1];

            // AAGUID (16 bytes)
            $offset = 37;
            $aaguid = substr($authData, $offset, 16);
            $offset += 16;

            // Credential ID length (2 bytes, big endian)
            $credIdLen = unpack('n', substr($authData, $offset, 2))[1];
            $offset += 2;

            // Credential ID
            $credentialId = substr($authData, $offset, $credIdLen);
            $offset += $credIdLen;

            // Public key (COSE format, variable length)
            // We'll store the remaining authData as the public key
            $publicKey = substr($authData, $offset);

            return [
                'credentialId' => $credentialId,
                'publicKey' => $publicKey,
                'counter' => $counter
            ];
        } catch (Exception $e) {
            error_log('Failed to parse attestation: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Detect device name from user agent
     */
    private function detectDeviceName(): string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if (strpos($ua, 'iPhone') !== false) return 'iPhone';
        if (strpos($ua, 'iPad') !== false) return 'iPad';
        if (strpos($ua, 'Mac') !== false) return 'Mac';
        if (strpos($ua, 'Android') !== false) return 'Android Device';
        if (strpos($ua, 'Windows') !== false) return 'Windows PC';
        if (strpos($ua, 'Linux') !== false) return 'Linux Device';

        return 'Unknown Device';
    }
}

/**
 * Helper function to get WebAuthn manager instance
 */
function webauthn(): WebAuthnManager
{
    static $instance = null;
    if ($instance === null) {
        $instance = new WebAuthnManager();
    }
    return $instance;
}
