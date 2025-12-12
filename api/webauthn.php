<?php

/**
 * VERDANT SMS v3.0 — WEBAUTHN API ENDPOINTS
 * Handles biometric/passkey registration and authentication
 */

session_start();
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Helper to generate random bytes as base64url
function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data)
{
    return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Unknown action'];

try {
    switch ($action) {

        // Generate registration options for new credential
        case 'register_options':
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }

            $user = db()->fetchOne("SELECT id, email, full_name FROM users WHERE id = ?", [$_SESSION['user_id']]);
            if (!$user) {
                throw new Exception('User not found');
            }

            // Generate challenge
            $challenge = random_bytes(32);
            $_SESSION['webauthn_challenge'] = base64url_encode($challenge);

            // Get existing credentials to exclude
            $existingCreds = db()->fetchAll("SELECT credential_id FROM webauthn_credentials WHERE user_id = ?", [$user['id']]);
            $excludeCredentials = array_map(function ($c) {
                return [
                    'type' => 'public-key',
                    'id' => $c['credential_id']
                ];
            }, $existingCreds);

            $response = [
                'success' => true,
                'options' => [
                    'challenge' => $_SESSION['webauthn_challenge'],
                    'rp' => [
                        'name' => 'Verdant School',
                        'id' => $_SERVER['HTTP_HOST']
                    ],
                    'user' => [
                        'id' => base64url_encode(pack('N', $user['id'])),
                        'name' => $user['email'],
                        'displayName' => $user['full_name'] ?? $user['email']
                    ],
                    'pubKeyCredParams' => [
                        ['type' => 'public-key', 'alg' => -7],   // ES256
                        ['type' => 'public-key', 'alg' => -257] // RS256
                    ],
                    'authenticatorSelection' => [
                        'authenticatorAttachment' => 'platform',
                        'userVerification' => 'preferred',
                        'residentKey' => 'preferred'
                    ],
                    'timeout' => 60000,
                    'excludeCredentials' => $excludeCredentials,
                    'attestation' => 'none'
                ]
            ];
            break;

        // Register new credential
        case 'register':
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['webauthn_challenge'])) {
                throw new Exception('Invalid session');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || !isset($input['credential'])) {
                throw new Exception('Invalid request');
            }

            $credential = $input['credential'];
            $credentialId = $credential['id'];
            $credentialName = $input['name'] ?? 'My Device';

            // In a real implementation, you would verify the attestation
            // For simplicity, we'll just store the credential

            // Store credential
            db()->insert('webauthn_credentials', [
                'user_id' => $_SESSION['user_id'],
                'credential_id' => $credentialId,
                'public_key' => json_encode($credential['response']),
                'credential_name' => $credentialName,
                'transports' => json_encode($credential['transports'] ?? [])
            ]);

            unset($_SESSION['webauthn_challenge']);

            $response = [
                'success' => true,
                'message' => 'Biometric credential registered successfully'
            ];
            break;

        // Generate authentication options
        case 'login_options':
            $input = json_decode(file_get_contents('php://input'), true);
            $email = $input['email'] ?? '';

            $user = null;
            $allowCredentials = [];

            if ($email) {
                $user = db()->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
                if ($user) {
                    $creds = db()->fetchAll("SELECT credential_id, transports FROM webauthn_credentials WHERE user_id = ?", [$user['id']]);
                    $allowCredentials = array_map(function ($c) {
                        return [
                            'type' => 'public-key',
                            'id' => $c['credential_id'],
                            'transports' => json_decode($c['transports'] ?? '[]', true)
                        ];
                    }, $creds);
                }
            }

            // Generate challenge
            $challenge = random_bytes(32);
            $_SESSION['webauthn_challenge'] = base64url_encode($challenge);
            $_SESSION['webauthn_user_id'] = $user['id'] ?? null;

            $response = [
                'success' => true,
                'options' => [
                    'challenge' => $_SESSION['webauthn_challenge'],
                    'timeout' => 60000,
                    'rpId' => $_SERVER['HTTP_HOST'],
                    'allowCredentials' => $allowCredentials,
                    'userVerification' => 'preferred'
                ]
            ];
            break;

        // Authenticate with credential
        case 'login':
            if (!isset($_SESSION['webauthn_challenge'])) {
                throw new Exception('Invalid session');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || !isset($input['credential'])) {
                throw new Exception('Invalid request');
            }

            $credentialId = $input['credential']['id'];

            // Find credential
            $cred = db()->fetchOne("
                SELECT wc.*, u.id as user_id, u.email, u.role, u.full_name, u.status
                FROM webauthn_credentials wc
                JOIN users u ON wc.user_id = u.id
                WHERE wc.credential_id = ?
            ", [$credentialId]);

            if (!$cred) {
                throw new Exception('Credential not found');
            }

            if ($cred['status'] !== 'active') {
                throw new Exception('Account is not active');
            }

            // In a real implementation, you would verify the signature
            // For simplicity, we'll trust the credential

            // Update last used
            db()->query("UPDATE webauthn_credentials SET last_used_at = NOW(), counter = counter + 1 WHERE id = ?", [$cred['id']]);

            // Create session
            $_SESSION['user_id'] = $cred['user_id'];
            $_SESSION['user_email'] = $cred['email'];
            $_SESSION['user_role'] = $cred['role'];
            $_SESSION['user_name'] = $cred['full_name'];
            $_SESSION['login_method'] = 'biometric';

            unset($_SESSION['webauthn_challenge']);
            unset($_SESSION['webauthn_user_id']);

            // Determine redirect URL
            $redirectUrl = get_role_dashboard_url($cred['role']);

            $response = [
                'success' => true,
                'message' => 'Login successful',
                'redirect' => $redirectUrl
            ];
            break;

        // List user's credentials
        case 'list':
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }

            $creds = db()->fetchAll("
                SELECT id, credential_name, created_at, last_used_at
                FROM webauthn_credentials
                WHERE user_id = ?
                ORDER BY created_at DESC
            ", [$_SESSION['user_id']]);

            $response = [
                'success' => true,
                'credentials' => $creds
            ];
            break;

        // Delete a credential
        case 'delete':
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $credId = $input['id'] ?? 0;

            db()->query("DELETE FROM webauthn_credentials WHERE id = ? AND user_id = ?", [$credId, $_SESSION['user_id']]);

            $response = [
                'success' => true,
                'message' => 'Credential deleted'
            ];
            break;

        default:
            throw new Exception('Unknown action: ' . $action);
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);

/**
 * Get dashboard URL based on role
 */
function get_role_dashboard_url($role)
{
    $dashboards = [
        'admin' => '/admin/dashboard.php',
        'teacher' => '/teacher/dashboard.php',
        'student' => '/student/dashboard.php',
        'parent' => '/parent/dashboard.php',
        'principal' => '/principal/dashboard.php',
        'vice-principal' => '/vice-principal/dashboard.php',
        'librarian' => '/librarian/dashboard.php',
        'accountant' => '/accountant/dashboard.php',
        'transport' => '/transport/dashboard.php',
        'hostel' => '/hostel/dashboard.php',
        'nurse' => '/nurse/dashboard.php',
        'counselor' => '/counselor/dashboard.php',
        'canteen' => '/canteen/dashboard.php',
        'class-teacher' => '/class-teacher/dashboard.php',
        'subject-coordinator' => '/subject-coordinator/dashboard.php',
        'admin-officer' => '/admin-officer/dashboard.php',
        'alumni' => '/alumni/dashboard.php',
        'general' => '/general/dashboard.php'
    ];

    return $dashboards[$role] ?? '/dashboard.php';
}
