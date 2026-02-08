<?php
/**
 * Manage Shelf - Add/Move/Remove books from shelves
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireLogin();

$userId = getCurrentUserId();
$bookId = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;
$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getDB();
        
        if ($action === 'add_to_shelf') {
            $shelf = $_POST['shelf'] ?? 'want_to_read';
            
            // Check if book exists in any shelf
            $checkStmt = $db->prepare("SELECT shelf FROM reading_list WHERE user_id = ? AND book_id = ?");
            $checkStmt->execute([$userId, $bookId]);
            $existing = $checkStmt->fetch();
            
            if ($existing) {
                // Update existing shelf
                $stmt = $db->prepare("UPDATE reading_list SET shelf = ?, added_at = NOW() WHERE user_id = ? AND book_id = ?");
                $stmt->execute([$shelf, $userId, $bookId]);
                setFlashMessage('success', 'Book moved to ' . str_replace('_', ' ', $shelf) . '!');
            } else {
                // Add new entry
                $stmt = $db->prepare("INSERT INTO reading_list (user_id, book_id, shelf) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $bookId, $shelf]);
                setFlashMessage('success', 'Book added to ' . str_replace('_', ' ', $shelf) . '!');
            }
            
        } elseif ($action === 'remove_from_shelf') {
            $stmt = $db->prepare("DELETE FROM reading_list WHERE user_id = ? AND book_id = ?");
            $stmt->execute([$userId, $bookId]);
            setFlashMessage('success', 'Book removed from your library!');
            
        } elseif ($action === 'mark_finished') {
            $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
            $notes = trim($_POST['notes'] ?? '');
            
            $stmt = $db->prepare("
                UPDATE reading_list 
                SET shelf = 'finished', finished_at = NOW(), rating = ?, notes = ?
                WHERE user_id = ? AND book_id = ?
            ");
            $stmt->execute([$rating, $notes, $userId, $bookId]);
            setFlashMessage('success', 'Book marked as finished!');
        }
        
    } catch (PDOException $e) {
        setFlashMessage('error', 'Error updating shelf: ' . $e->getMessage());
    }
    
    // Redirect back
    $redirect = $_POST['redirect'] ?? url('/user/my_library.php');
    header('Location: ' . $redirect);
    exit;
}

// If GET request, show the manage form
try {
    $db = getDB();
    
    // Get book details
    $stmt = $db->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
    
    if (!$book) {
        setFlashMessage('error', 'Book not found');
        header('Location: ' . url('/user/my_library.php'));
        exit;
    }
    
    // Check current shelf
    $shelfStmt = $db->prepare("SELECT * FROM reading_list WHERE user_id = ? AND book_id = ?");
    $shelfStmt->execute([$userId, $bookId]);
    $currentShelf = $shelfStmt->fetch();
    
} catch (PDOException $e) {
    setFlashMessage('error', 'Database error');
    header('Location: ' . url('/user/my_library.php'));
    exit;
}

$pageTitle = "Manage Book";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="manage-shelf-container">
    <div class="manage-shelf-card">
        <!-- Book Display Section -->
        <div class="book-showcase">
            <?php 
            $coverUrl = !empty($book['cover_url']) ? $book['cover_url'] : 'https://via.placeholder.com/200x300?text=No+Cover';
            ?>
            <img src="<?= $coverUrl ?>" alt="<?= sanitize($book['title']) ?>" 
                 class="showcase-cover"
                 onerror="this.onerror=null;this.src='https://via.placeholder.com/200x300?text=No+Cover';">
            
            <div class="book-info">
                <h2><?= sanitize($book['title']) ?></h2>
                <p class="author-name">by <?= sanitize($book['author']) ?></p>
                <span class="category-badge"><?= sanitize($book['category']) ?></span>
                
                <?php if ($book['description']): ?>
                    <p class="book-description"><?= sanitize(substr($book['description'], 0, 200)) ?>...</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Current Shelf Status -->
        <?php if ($currentShelf): ?>
            <div class="current-shelf-status">
                <span class="status-label">Currently on shelf:</span>
                <span class="shelf-name">
                    <?php
                    $shelfIcons = [
                        'want_to_read' => '📋',
                        'currently_reading' => '📖',
                        'finished' => '✅'
                    ];
                    $shelfNames = [
                        'want_to_read' => 'Want to Read',
                        'currently_reading' => 'Currently Reading',
                        'finished' => 'Finished'
                    ];
                    echo $shelfIcons[$currentShelf['shelf']] . ' ' . $shelfNames[$currentShelf['shelf']];
                    ?>
                </span>
            </div>
        <?php endif; ?>
        
        <!-- Shelf Management Form -->
        <div class="shelf-management">
            <h3>Manage This Book</h3>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="add_to_shelf">
                <input type="hidden" name="redirect" value="<?= sanitize($_SERVER['HTTP_REFERER'] ?? url('/user/my_library.php')) ?>">
                
                <div class="shelf-options">
                    <label class="shelf-option">
                        <input type="radio" name="shelf" value="want_to_read" 
                               <?= ($currentShelf && $currentShelf['shelf'] === 'want_to_read') ? 'checked' : '' ?>>
                        <div class="option-card">
                            <span class="option-icon">📋</span>
                            <span class="option-label">Want to Read</span>
                        </div>
                    </label>
                    
                    <label class="shelf-option">
                        <input type="radio" name="shelf" value="currently_reading"
                               <?= ($currentShelf && $currentShelf['shelf'] === 'currently_reading') ? 'checked' : '' ?>>
                        <div class="option-card">
                            <span class="option-icon">📖</span>
                            <span class="option-label">Currently Reading</span>
                        </div>
                    </label>
                    
                    <label class="shelf-option">
                        <input type="radio" name="shelf" value="finished"
                               <?= ($currentShelf && $currentShelf['shelf'] === 'finished') ? 'checked' : '' ?>>
                        <div class="option-card">
                            <span class="option-icon">✅</span>
                            <span class="option-label">Finished</span>
                        </div>
                    </label>
                </div>
                
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary btn-large">
                        <?= $currentShelf ? 'Update Shelf' : 'Add to Shelf' ?>
                    </button>
                    
                    <?php if ($currentShelf): ?>
                        <button type="submit" name="action" value="remove_from_shelf" 
                                class="btn btn-danger btn-large"
                                onclick="return confirm('Remove this book from your library?')">
                            Remove from Library
                        </button>
                    <?php endif; ?>
                </div>
            </form>
            
            <?php if ($currentShelf && $currentShelf['shelf'] !== 'finished'): ?>
                <div class="mark-finished-section">
                    <h3>Mark as Finished</h3>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="mark_finished">
                        <input type="hidden" name="redirect" value="<?= url('/user/my_library.php?shelf=finished') ?>">
                        
                        <div class="rating-section">
                            <label>Your Rating:</label>
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label class="star-label">
                                        <input type="radio" name="rating" value="<?= $i ?>">
                                        <span class="star-icon">★</span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Your Thoughts:</label>
                            <textarea name="notes" rows="4" placeholder="What did you think about this book?"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-large">Mark as Finished</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="back-link">
            <a href="<?= url('/user/my_library.php') ?>" class="btn btn-secondary">← Back to My Library</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_user.php'; ?>
