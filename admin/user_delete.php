<?php
/**
 * Admin - Delete User
 * 
 * Handles user deletion.
 * Prevents deleting yourself.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('/admin/users_list.php'));
    exit;
}

$userId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($userId <= 0) {
    setFlashMessage('error', 'Invalid user ID.');
    header('Location: ' . url('/admin/users_list.php'));
    exit;
}

// Prevent self-deletion
if ($userId === getCurrentUserId()) {
    setFlashMessage('error', 'You cannot delete your own account.');
    header('Location: ' . url('/admin/users_list.php'));
    exit;
}

try {
    $db = getDB();
    
    // Check if user exists
    $stmt = $db->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        setFlashMessage('error', 'User not found.');
        header('Location: ' . url('/admin/users_list.php'));
        exit;
    }
    
    // Check for active borrowings
    $borrowStmt = $db->prepare("
        SELECT COUNT(*) FROM borrowings 
        WHERE user_id = ? AND status = 'borrowed'
    ");
    $borrowStmt->execute([$userId]);
    $activeBorrowings = $borrowStmt->fetchColumn();
    
    if ($activeBorrowings > 0) {
        setFlashMessage('error', 'Cannot delete user: they have ' . $activeBorrowings . ' active borrowing(s).');
        header('Location: ' . url('/admin/users_list.php'));
        exit;
    }
    
    // Delete user (cascades to borrowings)
    $deleteStmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $deleteStmt->execute([$userId]);
    
    setFlashMessage('success', 'User "' . $user['name'] . '" deleted successfully.');
    
} catch (PDOException $e) {
    setFlashMessage('error', 'Database error: ' . $e->getMessage());
}

header('Location: ' . url('/admin/users_list.php'));
exit;

