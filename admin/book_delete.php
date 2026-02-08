<?php
/**
 * Admin - Delete Book
 * 
 * Handles book deletion (hard delete).
 * Called via POST from books_list.php.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireAdmin();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('/admin/books_list.php'));
    exit;
}

$bookId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($bookId <= 0) {
    setFlashMessage('error', 'Invalid book ID.');
    header('Location: ' . url('/admin/books_list.php'));
    exit;
}

try {
    $db = getDB();
    
    // Check if book exists
    $stmt = $db->prepare("SELECT title FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
    
    if (!$book) {
        setFlashMessage('error', 'Book not found.');
        header('Location: ' . url('/admin/books_list.php'));
        exit;
    }
    
    // Check if book has active borrowings
    $borrowStmt = $db->prepare("
        SELECT COUNT(*) FROM borrowings 
        WHERE book_id = ? AND status = 'borrowed'
    ");
    $borrowStmt->execute([$bookId]);
    $activeBorrowings = $borrowStmt->fetchColumn();
    
    if ($activeBorrowings > 0) {
        setFlashMessage('error', 'Cannot delete book: it has ' . $activeBorrowings . ' active borrowing(s).');
        header('Location: ' . url('/admin/books_list.php'));
        exit;
    }
    
    // Delete the book (cascades to borrowings due to FK)
    $deleteStmt = $db->prepare("DELETE FROM books WHERE id = ?");
    $deleteStmt->execute([$bookId]);
    
    setFlashMessage('success', 'Book "' . $book['title'] . '" deleted successfully.');
    
} catch (PDOException $e) {
    setFlashMessage('error', 'Database error: ' . $e->getMessage());
}

header('Location: ' . url('/admin/books_list.php'));
exit;

