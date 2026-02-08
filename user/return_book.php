<?php
/**
 * User - Return Book Handler
 * 
 * Processes book return requests.
 * Called via POST from my_borrowings.php.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('/user/my_borrowings.php'));
    exit;
}

$borrowingId = isset($_POST['borrowing_id']) ? (int)$_POST['borrowing_id'] : 0;
$userId = getCurrentUserId();

if ($borrowingId <= 0) {
    setFlashMessage('error', 'Invalid request.');
    header('Location: ' . url('/user/my_borrowings.php'));
    exit;
}

try {
    $db = getDB();
    
    // Start transaction
    $db->beginTransaction();
    
    // Get the borrowing record (must belong to current user)
    $stmt = $db->prepare("
        SELECT b.*, bk.title 
        FROM borrowings b
        JOIN books bk ON b.book_id = bk.id
        WHERE b.id = ? AND b.user_id = ? AND b.status IN ('borrowed', 'overdue')
        FOR UPDATE
    ");
    $stmt->execute([$borrowingId, $userId]);
    $borrowing = $stmt->fetch();
    
    if (!$borrowing) {
        $db->rollBack();
        setFlashMessage('error', 'Borrowing record not found or already returned.');
        header('Location: ' . url('/user/my_borrowings.php'));
        exit;
    }
    
    // Update borrowing status
    $updateStmt = $db->prepare("
        UPDATE borrowings 
        SET returned_at = NOW(), status = 'returned' 
        WHERE id = ?
    ");
    $updateStmt->execute([$borrowingId]);
    
    // Increase available copies
    $bookStmt = $db->prepare("
        UPDATE books SET available_copies = available_copies + 1 WHERE id = ?
    ");
    $bookStmt->execute([$borrowing['book_id']]);
    
    $db->commit();
    
    setFlashMessage('success', 'Successfully returned "' . $borrowing['title'] . '"!');
    
} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    setFlashMessage('error', 'Database error. Please try again.');
}

header('Location: ' . url('/user/my_borrowings.php'));
exit;

