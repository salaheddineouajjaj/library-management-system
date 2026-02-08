<?php
/**
 * Add to Shelf Handler
 * Quick add books to reading list from browse/marketplace pages
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('/user/browse_books.php'));
    exit;
}

$userId = getCurrentUserId();
$shelf = $_POST['shelf'] ?? 'want_to_read';
$redirect = $_POST['redirect'] ?? url('/user/browse_books.php');

// Handle API book import
if (isset($_POST['api_source']) && $_POST['api_source'] === 'openlibrary') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? 'Unknown');
    $category = trim($_POST['category'] ?? 'General');
    $isbn = trim($_POST['isbn'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $coverUrl = trim($_POST['cover_url'] ?? '');
    
    if (empty($title)) {
        setFlashMessage('error', 'Invalid book data.');
        header('Location: ' . $redirect);
        exit;
    }
    
    try {
        $db = getDB();
        $db->beginTransaction();
        
        // Find or create book
        $findStmt = $db->prepare("SELECT id FROM books WHERE title = ? AND author = ? LIMIT 1");
        $findStmt->execute([$title, $author]);
        $bookId = $findStmt->fetchColumn();
        
        if (!$bookId) {
            // Insert new book
            $insertBookSql = "INSERT INTO books (title, author, category, isbn, description, cover_url, total_copies, available_copies) VALUES (?, ?, ?, ?, ?, ?, 5, 5)";
            $stmt = $db->prepare($insertBookSql);
            $stmt->execute([$title, $author, $category, $isbn, $description, $coverUrl]);
            $bookId = $db->lastInsertId();
            
            // Add to marketplace
            $insertSaleSql = "INSERT INTO book_sales (book_id, price, stock, is_available) VALUES (?, 9.99, 10, 1)";
            $saleStmt = $db->prepare($insertSaleSql);
            $saleStmt->execute([$bookId]);
        } else {
            // Repair cover if missing
            $repairStmt = $db->prepare("UPDATE books SET cover_url = ? WHERE id = ? AND (cover_url IS NULL OR cover_url = '')");
            $repairStmt->execute([$coverUrl, $bookId]);
        }
        
        // Add to reading list
        $checkStmt = $db->prepare("SELECT id FROM reading_list WHERE user_id = ? AND book_id = ?");
        $checkStmt->execute([$userId, $bookId]);
        
        if ($checkStmt->fetch()) {
            // Update existing
            $updateStmt = $db->prepare("UPDATE reading_list SET shelf = ?, added_at = NOW() WHERE user_id = ? AND book_id = ?");
            $updateStmt->execute([$shelf, $userId, $bookId]);
            setFlashMessage('success', 'Book moved to ' . str_replace('_', ' ', $shelf) . '!');
        } else {
            // Insert new
            $insertStmt = $db->prepare("INSERT INTO reading_list (user_id, book_id, shelf) VALUES (?, ?, ?)");
            $insertStmt->execute([$userId, $bookId, $shelf]);
            setFlashMessage('success', 'Book added to ' . str_replace('_', ' ', $shelf) . '!');
        }
        
        $db->commit();
        
    } catch (PDOException $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        setFlashMessage('error', 'Error adding book to library: ' . $e->getMessage());
    }
}

header('Location: ' . $redirect);
exit;
