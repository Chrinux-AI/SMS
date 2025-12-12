<?php

/**
 * Role-Based Access Control Check
 * Include at the top of restricted pages
 *
 * Usage:
 *   require_once '../auth/check_role.php';
 *   check_role('admin'); // Single role
 *   check_role(['admin', 'superadmin']); // Multiple roles allowed
 *
 * Verdant SMS v3.0
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files if not already included
if (!function_exists('db')) {
    require_once __DIR__ . '/../includes/config.php';
    require_once __DIR__ . '/../includes/database.php';
}

if (!function_exists('redirect')) {
    require_once __DIR__ . '/../includes/functions.php';
}

/**
 * Check if user has the required role(s)
 *
 * @param string|array $allowed_roles Single role or array of allowed roles
 * @param string $redirect_url URL to redirect on failure
 * @return void
 */
function check_role($allowed_roles, $redirect_url = '../login.php')
{
    // Convert single role to array
    if (is_string($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        if (function_exists('redirect')) {
            redirect($redirect_url, 'Please login to access this page', 'error');
        } else {
            header('Location: ' . $redirect_url);
            exit;
        }
    }

    // Get user's role from session
    $user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? null;

    // Check if user has an allowed role
    if (!$user_role || !in_array($user_role, $allowed_roles)) {
        // Log unauthorized access attempt
        error_log("Unauthorized access attempt by user ID {$_SESSION['user_id']} (role: {$user_role}) to roles: " . implode(', ', $allowed_roles));

        if (function_exists('redirect')) {
            redirect($redirect_url, 'Access denied. You do not have permission to view this page.', 'error');
        } else {
            header('Location: ' . $redirect_url);
            exit;
        }
    }

    // Verify role in database (prevent session tampering)
    try {
        $user = db()->fetch("SELECT id, role, status FROM users WHERE id = ?", [$_SESSION['user_id']]);

        if (!$user) {
            session_destroy();
            header('Location: ' . $redirect_url);
            exit;
        }

        if (!in_array($user['role'], $allowed_roles)) {
            error_log("Role mismatch for user ID {$_SESSION['user_id']}: session={$user_role}, db={$user['role']}");

            // Update session with correct role
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_role'] = $user['role'];

            if (function_exists('redirect')) {
                redirect($redirect_url, 'Access denied. Role mismatch detected.', 'error');
            } else {
                header('Location: ' . $redirect_url);
                exit;
            }
        }

        if ($user['status'] !== 'active') {
            session_destroy();
            if (function_exists('redirect')) {
                redirect($redirect_url, 'Your account is not active. Please contact administrator.', 'error');
            } else {
                header('Location: ' . $redirect_url);
                exit;
            }
        }
    } catch (Exception $e) {
        error_log("Database error in check_role: " . $e->getMessage());
        // Allow access if database check fails (graceful degradation)
        // but log the error for investigation
    }
}

/**
 * Quick check if current user has a specific role (returns boolean)
 *
 * @param string|array $roles Role(s) to check
 * @return bool
 */
function user_has_role($roles)
{
    if (is_string($roles)) {
        $roles = [$roles];
    }

    $user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? null;
    return $user_role && in_array($user_role, $roles);
}

/**
 * Get current user's role
 *
 * @return string|null
 */
function get_current_role()
{
    return $_SESSION['user_role'] ?? $_SESSION['role'] ?? null;
}

/**
 * Check if user is authenticated (any role)
 *
 * @return bool
 */
function is_authenticated()
{
    return isset($_SESSION['user_id']);
}
