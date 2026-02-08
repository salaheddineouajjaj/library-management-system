<?php
/**
 * User - Borrow Book Handler
 * 
 * Processes book borrowing requests.
 * Called via POST from browse_books.php.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireLogin();

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('/user/browse_books.php'));
    exit;
}

$bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
// Handle API Borrowing
if (isset($_POST['api_source']) && $_POST['api_source'] === 'openlibrary') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? 'Unknown');
    $category = trim($_POST['category'] ?? 'General');
    $isbn = trim($_POST['isbn'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $coverUrl = trim($_POST['cover_url'] ?? '');
    
    // Quick validation
    if (empty($title)) {
        setFlashMessage('error', 'Invalid book data.');
        header('Location: ' . url('/user/browse_books.php'));
        exit;
    }
    
    try {
        $db = getDB();
        
        // Check if book exists by Title (and Author) to avoid duplicates
        // Note: In production, matching by ISBN is better, but OpenLibrary data varies.
        $findStmt = $db->prepare("SELECT id FROM books WHERE title = ? AND author = ? LIMIT 1");
        $findStmt->execute([$title, $author]);
        $existingBookId = $findStmt->fetchColumn();
        
        if ($existingBookId) {
            $bookId = $existingBookId;
            
            // Repair: If existing book has no cover, update it now
            $repairStmt = $db->prepare("UPDATE books SET cover_url = ? WHERE id = ? AND (cover_url IS NULL OR cover_url = '')");
            $repairStmt->execute([$coverUrl, $bookId]);
        } else {
            // Insert new book
            // Defaulting copies to 5 for "virtual" books
            $insertBookSql = "INSERT INTO books (title, author, category, isbn, description, cover_url, total_copies, available_copies) VALUES (?, ?, ?, ?, ?, ?, 5, 5)";
            $stmt = $db->prepare($insertBookSql);
            $stmt->execute([$title, $author, $category, $isbn, $description, $coverUrl]);
            $bookId = $db->lastInsertId();
            
            // AUTOMATICALLY ADD TO MARKETPLACE
            // Price defaults to $9.99, Stock 10
            $insertSaleSql = "INSERT INTO book_sales (book_id, price, stock, is_available) VALUES (?, 9.99, 10, 1)";
            $saleStmt = $db->prepare($insertSaleSql);
            $saleStmt->execute([$bookId]);
        }
    } catch (PDOException $e) {
        setFlashMessage('error', 'Error processing book: ' . $e->getMessage());
        header('Location: ' . url('/user/browse_books.php'));
        exit;
    }
}

$userId = getCurrentUserId();

if ($bookId <= 0) {
    setFlashMessage('error', 'Invalid book selected.');
    header('Location: ' . url('/user/browse_books.php'));
    exit;
}

try {
    $db = getDB();
    
    // Start transaction
    $db->beginTransaction();
    
    // Check user's current borrow count
    $countStmt = $db->prepare("
        SELECT COUNT(*) FROM borrowings 
        WHERE user_id = ? AND status IN ('borrowed', 'overdue')
    ");
    $countStmt->execute([$userId]);
    $currentCount = $countStmt->fetchColumn();
    
    if ($currentCount >= MAX_BORROWED_BOOKS) {
        $db->rollBack();
        setFlashMessage('error', 'You have reached the maximum borrowing limit (' . MAX_BORROWED_BOOKS . ' books).');
        header('Location: ' . url('/user/browse_books.php'));
        exit;
    }
    
    // Check if book exists and is available
    $bookStmt = $db->prepare("SELECT * FROM books WHERE id = ? FOR UPDATE");
    $bookStmt->execute([$bookId]);
    $book = $bookStmt->fetch();
    
    if (!$book) {
        $db->rollBack();
        setFlashMessage('error', 'Book not found.');
        header('Location: ' . url('/user/browse_books.php'));
        exit;
    }

    if ($book['available_copies'] <= 0) {
        $db->rollBack();
        setFlashMessage('error', 'Sorry, this book is not currently available.');
        header('Location: ' . url('/user/browse_books.php'));
        exit;
    }
    
    // Check if user already has this book borrowed
    $alreadyStmt = $db->prepare("
        SELECT id FROM borrowings 
        WHERE user_id = ? AND book_id = ? AND status IN ('borrowed', 'overdue')
    ");
    $alreadyStmt->execute([$userId, $bookId]);
    
    if ($alreadyStmt->fetch()) {
        $db->rollBack();
        setFlashMessage('error', 'You already have this book borrowed.');
        header('Location: ' . url('/user/browse_books.php'));
        exit;
    }
    
    // Create borrowing record
    $dueDate = date('Y-m-d H:i:s', strtotime('+' . LOAN_PERIOD_DAYS . ' days'));
    
    $insertStmt = $db->prepare("
        INSERT INTO borrowings (user_id, book_id, borrowed_at, due_at, status)
        VALUES (?, ?, NOW(), ?, 'borrowed')
    ");
    $insertStmt->execute([$userId, $bookId, $dueDate]);
    
    // Decrease available copies
    $updateStmt = $db->prepare("
        UPDATE books SET available_copies = available_copies - 1 WHERE id = ?
    ");
    $updateStmt->execute([$bookId]);
    
    $db->commit();
    
    setFlashMessage('success', 'Successfully borrowed "' . $book['title'] . '"! Due date: ' . date('M d, Y', strtotime($dueDate)));
    
} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    setFlashMessage('error', 'Database error. Please try again.');
}

header('Location: ' . url('/user/browse_books.php'));
exit;

