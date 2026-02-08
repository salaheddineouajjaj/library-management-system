<?php
/**
 * Landing Page
 *
 * Redirects users based on their login status and role.
 * - Not logged in -> Login page
 * - Admin -> Admin dashboard
 * - User -> User dashboard
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth_check.php';

// Redirect based on login status and role
if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
} elseif (isAdmin()) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
} else {
    header('Location: ' . BASE_URL . '/user/dashboard.php');
    exit;
}

