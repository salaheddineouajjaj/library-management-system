<?php
/**
 * User - My Library (Personal Shelves)
 * 
 * Displays user's personal reading lists organized by shelves:
 * - Currently Reading
 * - Want to Read (Next Up)
 * - Finished
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = "My Library";
$currentPage = "library";

$userId = getCurrentUserId();
$activeShelf = $_GET['shelf'] ?? 'all';

try {
    $db = getDB();
    
    // Get books from each shelf
    $shelves = [
        'currently_reading' => [],
        'want_to_read' => [],
        'finished' => []
    ];
    
    foreach ($shelves as $shelf => $books) {
        $stmt = $db->prepare("
            SELECT rl.*, b.title, b.author, b.category, b.cover_url, b.id as book_id
            FROM reading_list rl
            JOIN books b ON rl.book_id = b.id
            WHERE rl.user_id = ? AND rl.shelf = ?
            ORDER BY rl.added_at DESC
        ");
        $stmt->execute([$userId, $shelf]);
        $shelves[$shelf] = $stmt->fetchAll();
    }
    
    // Get total counts
    $counts = [
        'currently_reading' => count($shelves['currently_reading']),
        'want_to_read' => count($shelves['want_to_read']),
        'finished' => count($shelves['finished'])
    ];
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $shelves = ['currently_reading' => [], 'want_to_read' => [], 'finished' => []];
    $counts = ['currently_reading' => 0, 'want_to_read' => 0, 'finished' => 0];
}

$flash = getFlashMessage();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="page-header">
    <h1>📚 My Library</h1>
    <p class="text-muted">Organize your reading journey</p>
</div>

<div class="library-container">
    <!-- Library Header with Tabs and Search -->
    <div class="library-header">
        <div class="library-tabs">
            <a href="?shelf=all" class="library-tab <?= $activeShelf === 'all' ? 'active' : '' ?>">
                Shelves
            </a>
            <a href="?shelf=all" class="library-tab">
                All Books
            </a>
        </div>
        
        <div class="library-search">
            <input type="text" placeholder="Search in My Library" class="library-search-input">
            <span class="search-icon">🔍</span>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
            <?= sanitize($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($shelves['currently_reading'])): ?>
        <!-- Currently Reading Shelf -->
        <section class="library-shelf-section">
            <div class="shelf-title-bar">
                <h2 class="shelf-title">Currently reading</h2>
            </div>
            
            <div class="wooden-shelf">
                <div class="shelf-books">
                    <?php foreach (array_slice($shelves['currently_reading'], 0, 6) as $item): 
                        $coverUrl = !empty($item['cover_url']) ? $item['cover_url'] : 'https://via.placeholder.com/128x196?text=No+Cover';
                    ?>
                        <div class="shelf-book-item">
                            <a href="<?= url('/user/manage_shelf.php?book_id=' . $item['book_id']) ?>">
                                <img src="<?= $coverUrl ?>" alt="<?= sanitize($item['title']) ?>"
                                     onerror="this.onerror=null;this.src='https://via.placeholder.com/128x196?text=No+Cover';">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="shelf-wood"></div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($shelves['want_to_read'])): ?>
        <!-- Next Up / Want to Read Shelf -->
        <section class="library-shelf-section">
            <div class="shelf-title-bar">
                <h2 class="shelf-title">Next up</h2>
                <a href="?shelf=want_to_read" class="shelf-view-all">Full shelf →</a>
            </div>
            
            <div class="wooden-shelf">
                <div class="shelf-books">
                    <?php foreach (array_slice($shelves['want_to_read'], 0, 6) as $item): 
                        $coverUrl = !empty($item['cover_url']) ? $item['cover_url'] : 'https://via.placeholder.com/128x196?text=No+Cover';
                    ?>
                        <div class="shelf-book-item">
                            <a href="<?= url('/user/manage_shelf.php?book_id=' . $item['book_id']) ?>">
                                <img src="<?= $coverUrl ?>" alt="<?= sanitize($item['title']) ?>"
                                     onerror="this.onerror=null;this.src='https://via.placeholder.com/128x196?text=No+Cover';">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="shelf-wood"></div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($shelves['finished'])): ?>
        <!-- Finished Shelf -->
        <section class="library-shelf-section">
            <div class="shelf-title-bar">
                <h2 class="shelf-title">Finished</h2>
                <a href="?shelf=finished" class="shelf-view-all">Full shelf →</a>
            </div>
            
            <div class="wooden-shelf">
                <div class="shelf-books">
                    <?php foreach (array_slice($shelves['finished'], 0, 6) as $item): 
                        $coverUrl = !empty($item['cover_url']) ? $item['cover_url'] : 'https://via.placeholder.com/128x196?text=No+Cover';
                    ?>
                        <div class="shelf-book-item">
                            <a href="<?= url('/user/manage_shelf.php?book_id=' . $item['book_id']) ?>">
                                <img src="<?= $coverUrl ?>" alt="<?= sanitize($item['title']) ?>"
                                     onerror="this.onerror=null;this.src='https://via.placeholder.com/128x196?text=No+Cover';">
                                <?php if ($item['rating']): ?>
                                    <div class="book-shelf-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star-small <?= $i <= $item['rating'] ? 'filled' : '' ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="shelf-wood"></div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (empty($shelves['currently_reading']) && empty($shelves['want_to_read']) && empty($shelves['finished'])): ?>
        <div class="empty-library">
            <div class="empty-library-icon">📚</div>
            <h3>Your library is empty</h3>
            <p>Start building your personal library by adding books to your shelves!</p>
            <a href="<?= url('/user/browse_books.php') ?>" class="btn btn-primary">Browse Books</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer_user.php'; ?>
