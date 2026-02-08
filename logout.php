<?php
/**
 * Logout Page
 * 
 * Destroys the user session and redirects to login.
 */

require_once __DIR__ . '/includes/auth_check.php';

// Clear all session data and destroy session
logoutUser();

// Redirect to login page
header('Location: ' . url('/login.php'));
exit;

