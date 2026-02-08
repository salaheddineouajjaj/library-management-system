<?php
/**
 * User - Marketplace
 * Browse and purchase books
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireLogin();

$userId = getCurrentUserId();

// Handle add to cart
// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    
    // Check if it's an API book that needs to be imported first
    if (isset($_POST['api_source']) && $_POST['api_source'] === 'openlibrary') {
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $price = (float)$_POST['price']; 
        
        try {
            $db = getDB();
            $db->beginTransaction();
            
            // 1. Find or Create Book
            $findStmt = $db->prepare("SELECT id FROM books WHERE title = ? AND author = ? LIMIT 1");
            $findStmt->execute([$title, $author]);
            $bookId = $findStmt->fetchColumn();
            
            if (!$bookId) {
                $category = trim($_POST['category'] ?? 'General');
                $isbn = trim($_POST['isbn'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $coverUrl = trim($_POST['cover_url'] ?? '');
                
                $insertBook = $db->prepare("INSERT INTO books (title, author, category, isbn, description, cover_url, total_copies, available_copies) VALUES (?, ?, ?, ?, ?, ?, 10, 10)");
                $insertBook->execute([$title, $author, $category, $isbn, $description, $coverUrl]);
                $bookId = $db->lastInsertId();
            }
            
            // 2. Find or Create Book Sale Record
            $findSale = $db->prepare("SELECT id FROM book_sales WHERE book_id = ? LIMIT 1");
            $findSale->execute([$bookId]);
            $bookSaleId = $findSale->fetchColumn();
            
            if (!$bookSaleId) {
                // Insert sale record with the simulated price
                $insertSale = $db->prepare("INSERT INTO book_sales (book_id, price, stock, is_available) VALUES (?, ?, 50, 1)");
                $insertSale->execute([$bookId, $price]);
                $bookSaleId = $db->lastInsertId();
            }
            
            // 3. Add to Cart (Standard Logic)
            $cartStmt = $db->prepare("
                INSERT INTO cart_items (user_id, book_sale_id, quantity)
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE quantity = quantity + 1
            ");
            $cartStmt->execute([$userId, $bookSaleId]);
            
            $db->commit();
            setFlashMessage('success', 'Book imported and added to cart!');
            
        } catch (PDOException $e) {
            $db->rollBack();
            setFlashMessage('error', 'Error adding to cart: ' . $e->getMessage());
        }
    } else {
        // Standard Add to Cart (for existing local sales)
        $bookSaleId = (int)$_POST['book_sale_id'];
        try {
            $db = getDB();
            $stmt = $db->prepare("
                INSERT INTO cart_items (user_id, book_sale_id, quantity)
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE quantity = quantity + 1
            ");
            $stmt->execute([$userId, $bookSaleId]);
            setFlashMessage('success', 'Added to cart!');
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error adding to cart.');
        }
    }
    
    // Redirect back to preserve search state if possible
    header('Location: ' . url('/user/marketplace.php'));
    exit;
}

// Get cart count
try {
    $db = getDB();
    $cartStmt = $db->prepare("SELECT SUM(quantity) FROM cart_items WHERE user_id = ?");
    $cartStmt->execute([$userId]);
    $cartCount = $cartStmt->fetchColumn() ?: 0;
} catch (PDOException $e) {
    $cartCount = 0;
}

// Search/filter
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'relevance';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Get books from OpenLibrary API (Simulating a Global Marketplace)
try {
    // OpenLibrary Search API
    $queryParams = [
        'page' => $page,
        'limit' => 20,
        'fields' => 'title,author_name,cover_i,subject,first_sentence,isbn,key' 
    ];

    // Search query construction
    $qParts = [];
    if ($search) $qParts[] = $search;
    if ($category) $qParts[] = "subject:" . $category;
    
    // Default fallback if empty
    if (empty($qParts)) {
        $queryParams['q'] = 'subject:bestseller'; 
    } else {
        $queryParams['q'] = implode(' ', $qParts);
    }
    
    // API Request
    $apiUrl = "https://openlibrary.org/search.json?" . http_build_query($queryParams);
    $context = stream_context_create([
        'http' => ['ignore_errors' => true, 'timeout' => 8, 'user_agent' => 'LibraryShop/1.0']
    ]);
    
    $response = @file_get_contents($apiUrl, false, $context);
    
    if ($response === FALSE) {
        throw new Exception("Marketplace offline.");
    }
    
    $data = json_decode($response, true);
    $books = $data['docs'] ?? [];
    $totalFound = $data['num_found'] ?? 0;
    
    // Categories for filter
    $categories = [
        'Fiction', 'Science Fiction', 'Fantasy', 'Romance', 'Thriller', 'Mystery', 
        'Horror', 'Historical Fiction', 'Biography', 'History', 'Science', 
        'Technology', 'Programming', 'Business', 'Cooking', 'Travel'
    ];

} catch (Exception $e) {
    $books = [];
    $totalFound = 0;
    $categories = ['Fiction', 'Technology'];
    $error = "Could not load marketplace items.";
}

// Calculate total pages
$totalPages = ceil($totalFound / 20);
if ($totalPages > 100) $totalPages = 100;

$flash = getFlashMessage();
$pageTitle = 'Marketplace';
$currentPage = 'marketplace';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="page-header">
    <h1>🛒 Book Marketplace</h1>
    <span class="text-muted">
        Buy books directly from our global inventory (Page <?= $page ?>)
    </span>
    <a href="<?= url('/user/cart.php') ?>" class="btn btn-primary">
        🛒 Cart (<?= $cartCount ?>)
    </a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Search and Filter -->
<div class="search-box">
    <form method="GET" action="" style="display: contents;">
        <input type="text" name="search" placeholder="Search title, author..." 
               value="<?= sanitize($search) ?>">
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= sanitize($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                    <?= sanitize($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($search || $category): ?>
            <a href="<?= url('/user/marketplace.php') ?>" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Books Grid -->
<?php if (empty($books)): ?>
    <div class="card empty-state">
        <p>No books available matching your criteria.</p>
        <a href="<?= url('/user/marketplace.php') ?>" class="btn btn-primary">Browse Bestsellers</a>
    </div>
<?php else: ?>
    <div class="book-grid">
        <?php foreach ($books as $book):
            $title = $book['title'] ?? 'Unknown';
            $authors = isset($book['author_name']) ? implode(', ', array_slice($book['author_name'], 0, 2)) : 'Unknown';
            $coverId = $book['cover_i'] ?? null;
            $thumbnail = $coverId ? "https://covers.openlibrary.org/b/id/$coverId-M.jpg" : 'https://via.placeholder.com/128x196?text=No+Cover';
            
            // Extract category
            $categoryVal = isset($book['subject']) ? (is_array($book['subject']) ? $book['subject'][0] : $book['subject']) : 'General';
            
            // Generate deterministic "Virtual Price" based on title length/hash
            // This ensures the price is consistent for the same book across refreshes
            $hash = crc32($title . $authors); 
            $price = 9.99 + ($hash % 20); // Price between 9.99 and 29.99
            
            // Generate virtual stock
            $stock = 10 + ($hash % 50); 
        ?>
            <div class="book-card marketplace-card">
                 <div class="book-cover">
                    <img src="<?= $thumbnail ?>" alt="<?= sanitize($title) ?>" loading="lazy"
                         onerror="this.onerror=null;this.src='https://via.placeholder.com/128x196?text=No+Cover';">
                </div>
                <div class="book-details">
                    <h3><?= sanitize($title) ?></h3>
                    <p class="book-author">by <?= sanitize($authors) ?></p>
                    <span class="book-category"><?= sanitize($categoryVal) ?></span>
                    
                    <div class="book-price">
                        <span class="price">$<?= number_format($price, 2) ?></span>
                        <span class="stock"><?= $stock ?> in stock</span>
                    </div>
                    
                    <div class="book-actions">
                        <form method="POST" action="">
                            <input type="hidden" name="add_to_cart" value="1">
                            <input type="hidden" name="api_source" value="openlibrary">
                            <input type="hidden" name="title" value="<?= sanitize($title) ?>">
                            <input type="hidden" name="author" value="<?= sanitize($authors) ?>">
                            <input type="hidden" name="category" value="<?= sanitize($categoryVal) ?>">
                            <input type="hidden" name="isbn" value="<?= $book['isbn'][0] ?? '' ?>">
                            <input type="hidden" name="description" value="<?= $book['first_sentence'][0] ?? '' ?>">
                            <input type="hidden" name="cover_url" value="<?= $thumbnail ?>">
                            <input type="hidden" name="price" value="<?= $price ?>">
                            
                            <button type="submit" class="btn btn-primary btn-block">Add to Cart</button>
                        </form>
                        
                        <form method="POST" action="<?= url('/user/add_to_shelf.php') ?>" style="margin-top: 0.5rem;">
                            <input type="hidden" name="api_source" value="openlibrary">
                            <input type="hidden" name="title" value="<?= sanitize($title) ?>">
                            <input type="hidden" name="author" value="<?= sanitize($authors) ?>">
                            <input type="hidden" name="category" value="<?= sanitize($categoryVal) ?>">
                            <input type="hidden" name="isbn" value="<?= $book['isbn'][0] ?? '' ?>">
                            <input type="hidden" name="description" value="<?= $book['first_sentence'][0] ?? '' ?>">
                            <input type="hidden" name="cover_url" value="<?= $thumbnail ?>">
                            <input type="hidden" name="shelf" value="want_to_read">
                            <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                            
                            <button type="submit" class="btn btn-secondary btn-block">+ Library</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Modern Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="<?= url('/user/marketplace.php?page=' . ($page - 1) . '&search=' . urlencode($search) . '&category=' . urlencode($category)) ?>">
                &laquo; Prev
            </a>
        <?php else: ?>
            <span class="disabled">&laquo; Prev</span>
        <?php endif; ?>
        
        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        for ($i = $start; $i <= $end; $i++):
        ?>
            <?php if ($i == $page): ?>
                <span class="current-page"><?= $i ?></span>
            <?php else: ?>
                <a href="<?= url('/user/marketplace.php?page=' . $i . '&search=' . urlencode($search) . '&category=' . urlencode($category)) ?>">
                    <?= $i ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($page < $totalPages): ?>
            <a href="<?= url('/user/marketplace.php?page=' . ($page + 1) . '&search=' . urlencode($search) . '&category=' . urlencode($category)) ?>">
                Next &raquo;
            </a>
        <?php else: ?>
            <span class="disabled">Next &raquo;</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer_user.php'; ?>

