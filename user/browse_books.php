<?php
/**
 * User - Browse Books
 * 
 * Lists all available books with search and borrow option.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = "Browse Books";
$currentPage = "browse";

// Get search parameters
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

try {
    // OpenLibrary API Logic
    if (empty($search) && empty($category)) {
        $search = 'programming'; // Default "Discovery"
    }

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    // Build API Query
    // OpenLibrary Search API: https://openlibrary.org/dev/docs/api/search
    $queryParams = [
        'page' => $page,
        'limit' => 20,
        // Fields to return to make payload smaller/faster
        'fields' => 'title,author_name,cover_i,subject,first_sentence,isbn,key' 
    ];

    // Search query
    $qParts = [];
    if ($search) $qParts[] = $search;
    if ($category) $qParts[] = "subject:" . $category; // Use specific subject field for better results
    
    // If we have a query, use q=, otherwise fallback to "random" discovery (e.g. subject:accessible)
    if (!empty($qParts)) {
        $queryParams['q'] = implode(' ', $qParts);
    } else {
        $queryParams['q'] = 'subject:programming'; // Fallback
    }

    // Sorting
    $sort = $_GET['sort'] ?? 'relevance';
    if ($sort === 'new') $queryParams['sort'] = 'new';
    elseif ($sort === 'old') $queryParams['sort'] = 'old';
    elseif ($sort === 'random') $queryParams['sort'] = 'random';
    
    // Construct URL
    $apiUrl = "https://openlibrary.org/search.json?" . http_build_query($queryParams);
    
    // Fetch from API
    $context = stream_context_create([
        'http' => [
            'ignore_errors' => true, 
            'timeout' => 10, // Increased timeout
            'user_agent' => 'LibrarySystem/1.0 (Student Project)' // Polite User Agent
        ]
    ]);
    
    $response = @file_get_contents($apiUrl, false, $context);
    
    if ($response === FALSE) {
        // Fallback for network issues: return generic error or empty
       $books = [];
       $totalFound = 0;
       $error = "Could not connect to book server. Please check your internet.";
    } else {
        $data = json_decode($response, true);
        $books = $data['docs'] ?? [];
        $totalFound = $data['num_found'] ?? 0;
    }
    
    // Calculate total pages (capped at 100)
    $totalPages = ceil($totalFound / 20);
    if ($totalPages > 100) $totalPages = 100;
    
    // Categories
    $categories = [
        'Fiction', 'Science Fiction', 'Fantasy', 'Romance', 'Thriller', 'Mystery', 
        'Horror', 'Historical Fiction', 'Biography', 'History', 'Science', 
        'Technology', 'Programming', 'Business', 'Self-Help', 'Psychology', 
        'Philosophy', 'Art', 'Cooking', 'Travel'
    ];
    
    $currentBorrowCount = 0;
    $borrowedBookIds = [];

} catch (Exception $e) {
    $error = "API error: " . $e->getMessage();
    $books = [];
    $categories = ['Fiction', 'Technology', 'Science'];
    $borrowedBookIds = [];
    $currentBorrowCount = 0;
    $totalFound = 0;
    $totalPages = 0;
}

$flash = getFlashMessage();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="page-header">
    <h1>📖 Browse Books (OpenLibrary)</h1>
    <span class="text-muted">
        Found <?= number_format($totalFound) ?> books (Page <?= $page ?> of <?= $totalPages ?>)
    </span>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Search and Filter -->
<div class="search-box">
    <form method="GET" action="" style="display: contents;">
        <input type="text" name="search" placeholder="Search title, author, isbn..." 
               value="<?= sanitize($search) ?>">
               
        <select name="category">
            <option value="">All Subjects</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= sanitize($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                    <?= sanitize($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <select name="sort">
            <option value="relevance" <?= $sort === 'relevance' ? 'selected' : '' ?>>Most Relevant</option>
            <option value="new" <?= $sort === 'new' ? 'selected' : '' ?>>Newest First</option>
            <option value="old" <?= $sort === 'old' ? 'selected' : '' ?>>Oldest First</option>
            <option value="random" <?= $sort === 'random' ? 'selected' : '' ?>>Randomize</option>
        </select>
        
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($search || $category || $sort !== 'relevance'): ?>
            <a href="<?= url('/user/browse_books.php') ?>" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Books Grid -->
<?php if (empty($books)): ?>
    <div class="card empty-state">
        <p>No books found matching your criteria.</p>
        <a href="<?= url('/user/browse_books.php?search=popular') ?>" class="btn btn-primary">Discover Books</a>
    </div>
<?php else: ?>
    <div class="book-grid">
        <?php foreach ($books as $book): 
            $title = $book['title'] ?? 'Unknown Title';
            $authors = isset($book['author_name']) ? implode(', ', array_slice($book['author_name'], 0, 2)) : 'Unknown Author';
            $coverId = $book['cover_i'] ?? null;
            $thumbnail = $coverId ? "https://covers.openlibrary.org/b/id/$coverId-M.jpg" : 'https://via.placeholder.com/128x196?text=No+Cover';
            
            // Extract a subject as category
            $categoryVal = isset($book['subject']) ? (is_array($book['subject']) ? $book['subject'][0] : $book['subject']) : 'General';
            
            // Snippet or first sentence
            $description = $book['first_sentence'][0] ?? 'No description available.';
            $description = strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;

            // Prepare ISBN for importing
            $isbn = $book['isbn'][0] ?? ($book['isbn'][1] ?? 'N/A');
        ?>
            <div class="book-card">
                <div class="book-cover">
                    <img src="<?= $thumbnail ?>" alt="<?= sanitize($title) ?>" loading="lazy" 
                         onerror="this.onerror=null;this.src='https://via.placeholder.com/128x196?text=No+Cover';">
                </div>
                <div class="book-details">
                    <h3><?= sanitize($title) ?></h3>
                    <p class="book-author">by <?= sanitize($authors) ?></p>
                    <span class="book-category"><?= sanitize($categoryVal) ?></span>
                    <p class="book-description"><?= sanitize($description) ?></p>
                    
                    <div class="book-actions">
                        <form method="POST" action="<?= url('/user/borrow_book.php') ?>" style="display: inline-block;">
                            <input type="hidden" name="api_source" value="openlibrary">
                            <input type="hidden" name="title" value="<?= sanitize($title) ?>">
                            <input type="hidden" name="author" value="<?= sanitize($authors) ?>">
                            <input type="hidden" name="category" value="<?= sanitize($categoryVal) ?>">
                            <input type="hidden" name="isbn" value="<?= sanitize($isbn) ?>">
                            <input type="hidden" name="description" value="<?= sanitize($description) ?>">
                            <input type="hidden" name="cover_url" value="<?= sanitize($thumbnail) ?>">
                            
                            <button type="submit" class="btn btn-sm btn-primary">Borrow</button>
                        </form>
                        
                        <form method="POST" action="<?= url('/user/add_to_shelf.php') ?>" style="display: inline-block;">
                            <input type="hidden" name="api_source" value="openlibrary">
                            <input type="hidden" name="title" value="<?= sanitize($title) ?>">
                            <input type="hidden" name="author" value="<?= sanitize($authors) ?>">
                            <input type="hidden" name="category" value="<?= sanitize($categoryVal) ?>">
                            <input type="hidden" name="isbn" value="<?= sanitize($isbn) ?>">
                            <input type="hidden" name="description" value="<?= sanitize($description) ?>">
                            <input type="hidden" name="cover_url" value="<?= sanitize($thumbnail) ?>">
                            <input type="hidden" name="shelf" value="want_to_read">
                            <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                            
                            <button type="submit" class="btn btn-sm btn-secondary">+ Library</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Modern Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <!-- Previous -->
        <?php if ($page > 1): ?>
            <a href="<?= url('/user/browse_books.php?page=' . ($page - 1) . '&search=' . urlencode($search) . '&category=' . urlencode($category) . '&sort=' . $sort) ?>">
                &laquo; Prev
            </a>
        <?php else: ?>
            <span class="disabled">&laquo; Prev</span>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        if ($start > 1) {
            echo '<a href="' . url('/user/browse_books.php?page=1&search=' . urlencode($search) . '&category=' . urlencode($category) . '&sort=' . $sort) . '">1</a>';
            if ($start > 2) echo '<span class="dots">...</span>';
        }
        
        for ($i = $start; $i <= $end; $i++):
        ?>
            <?php if ($i == $page): ?>
                <span class="current-page"><?= $i ?></span>
            <?php else: ?>
                <a href="<?= url('/user/browse_books.php?page=' . $i . '&search=' . urlencode($search) . '&category=' . urlencode($category) . '&sort=' . $sort) ?>">
                    <?= $i ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) echo '<span class="dots">...</span>';
            echo '<a href="' . url('/user/browse_books.php?page=' . $totalPages . '&search=' . urlencode($search) . '&category=' . urlencode($category) . '&sort=' . $sort) . '">' . $totalPages . '</a>';
        }
        ?>

        <!-- Next -->
        <?php if ($page < $totalPages): ?>
            <a href="<?= url('/user/browse_books.php?page=' . ($page + 1) . '&search=' . urlencode($search) . '&category=' . urlencode($category) . '&sort=' . $sort) ?>">
                Next &raquo;
            </a>
        <?php else: ?>
            <span class="disabled">Next &raquo;</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <p class="text-muted text-center mt-2">
        Powered by OpenLibrary API
    </p>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer_user.php'; ?>

