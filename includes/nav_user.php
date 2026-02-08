<?php
/**
 * User Top Navigation
 * 
 * Horizontal top navigation bar for user pages.
 * Include after header.php on user pages.
 * 
 * Expects $currentPage variable to highlight active menu item.
 */

require_once __DIR__ . '/auth_check.php';
requireLogin();

// Redirect admins to admin dashboard
if (isAdmin()) {
    header('Location: ' . url('/admin/dashboard.php'));
    exit;
}

// Get current page for active state
if (!isset($currentPage)) {
    $currentPage = '';
}
?>
<div class="user-layout">
    <nav class="top-nav">
        <div class="top-nav-inner">
            <a href="<?= url('/user/dashboard.php') ?>" class="top-nav-brand">
                📚 Library
            </a>

            <ul class="top-nav-menu">
                <li>
                    <a href="<?= url('/user/dashboard.php') ?>" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?= url('/user/browse_books.php') ?>" class="<?= $currentPage === 'browse' ? 'active' : '' ?>">
                        Browse Books
                    </a>
                </li>
                <li>
                    <a href="<?= url('/user/my_library.php') ?>" class="<?= $currentPage === 'library' ? 'active' : '' ?>">
                        My Library
                    </a>
                </li>
                <li>
                    <a href="<?= url('/user/ai_suggestions.php') ?>" class="<?= $currentPage === 'ai' ? 'active' : '' ?>">
                        AI Suggestions
                    </a>
                </li>
                <li>
                    <a href="<?= url('/user/marketplace.php') ?>" class="<?= $currentPage === 'marketplace' ? 'active' : '' ?>">
                        Shop
                    </a>
                </li>
                <li>
                    <a href="<?= url('/logout.php') ?>" class="logout-link">
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    
    <main class="user-content">

