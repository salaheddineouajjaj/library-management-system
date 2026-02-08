<?php
/**
 * Admin Sidebar Navigation
 * 
 * Vertical sidebar navigation for admin pages.
 * Include after header.php on admin pages.
 * 
 * Expects $currentPage variable to highlight active menu item.
 */

require_once __DIR__ . '/auth_check.php';
requireAdmin();

// Get current page for active state
if (!isset($currentPage)) {
    $currentPage = '';
}
?>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h1>📚 Library Admin</h1>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="<?= url('/admin/dashboard.php') ?>" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                        📊 Dashboard
                    </a>
                </li>
            </ul>

            <div class="sidebar-section">
                <div class="sidebar-section-title">Management</div>
                <ul>
                    <li>
                        <a href="<?= url('/admin/books_list.php') ?>" class="<?= $currentPage === 'books' ? 'active' : '' ?>">
                            📖 Books
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/admin/users_list.php') ?>" class="<?= $currentPage === 'users' ? 'active' : '' ?>">
                            👥 Users
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/admin/borrowings_list.php') ?>" class="<?= $currentPage === 'borrowings' ? 'active' : '' ?>">
                            📋 Borrowings
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/admin/notifications.php') ?>" class="<?= $currentPage === 'notifications' ? 'active' : '' ?>">
                            🔔 Notifications
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/admin/marketplace.php') ?>" class="<?= $currentPage === 'marketplace' ? 'active' : '' ?>">
                            🛒 Marketplace
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-section-title">Account</div>
                <ul>
                    <li>
                        <a href="<?= url('/logout.php') ?>">
                            🚪 Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <div style="padding: 1rem; margin-top: auto; border-top: 1px solid var(--color-border);">
            <div style="font-size: 0.875rem; color: var(--color-text-muted);">
                Logged in as:
            </div>
            <div style="font-weight: 500; color: var(--color-text);">
                <?= htmlspecialchars(getCurrentUserName() ?? 'Admin') ?>
            </div>
        </div>
    </aside>
    
    <main class="main-content">

