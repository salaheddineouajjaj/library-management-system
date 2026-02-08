<?php
/**
 * Authentication & Authorization Helper
 *
 * Functions to check login status and user roles.
 * Include this file in pages that require authentication.
 */

// Include config for BASE_URL
require_once __DIR__ . '/../config/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate URL with base path
 *
 * @param string $path Path relative to project root (e.g., '/login.php')
 * @return string Full URL with base path
 */
function url($path) {
    return BASE_URL . $path;
}

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if current user is an admin
 * 
 * @return bool True if admin, false otherwise
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Check if current user is a regular user
 * 
 * @return bool True if regular user, false otherwise
 */
function isUser() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'user';
}

/**
 * Get current user's ID
 * 
 * @return int|null User ID or null if not logged in
 */
function getCurrentUserId() {
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}

/**
 * Get current user's name
 * 
 * @return string|null User name or null if not logged in
 */
function getCurrentUserName() {
    return isLoggedIn() ? $_SESSION['user_name'] : null;
}

/**
 * Require user to be logged in
 * Redirects to login page if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . url('/login.php'));
        exit;
    }
}

/**
 * Require user to be an admin
 * Redirects to login if not authenticated, or to user dashboard if not admin
 */
function requireAdmin() {
    if (!isLoggedIn()) {
        header('Location: ' . url('/login.php'));
        exit;
    }
    if (!isAdmin()) {
        header('Location: ' . url('/user/dashboard.php'));
        exit;
    }
}

/**
 * Require user to be a regular user (not admin)
 * Redirects to login if not authenticated, or to admin dashboard if admin
 */
function requireUser() {
    if (!isLoggedIn()) {
        header('Location: ' . url('/login.php'));
        exit;
    }
    if (isAdmin()) {
        header('Location: ' . url('/admin/dashboard.php'));
        exit;
    }
}

/**
 * Set session data after successful login
 * 
 * @param array $user User data from database
 */
function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
}

/**
 * Clear session data on logout
 */
function logoutUser() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Sanitize user input to prevent XSS
 * 
 * @param string $input Raw user input
 * @return string Sanitized input
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Display a flash message (stored in session)
 * 
 * @param string $type Message type: 'success', 'error', 'warning', 'info'
 * @param string $message The message text
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null Flash message array or null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

