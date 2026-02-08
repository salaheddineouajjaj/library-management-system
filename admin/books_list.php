<?php
/**
 * Admin - Books List
 * 
 * Displays all books with search/filter and links to add/edit/delete.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = "Manage Books";
$currentPage = "books";

// Get search parameters
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

try {
    $db = getDB();
    
    // Build query with optional filters
    $sql = "SELECT * FROM books WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($category)) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll();
    
    // Get unique categories for filter dropdown
    $catStmt = $db->query("SELECT DISTINCT category FROM books ORDER BY category");
    $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $books = [];
    $categories = [];
}

// Get flash message
$flash = getFlashMessage();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_admin.php';
?>

<div class="page-header">
    <h1>📖 Manage Books</h1>
    <a href="<?= url('/admin/book_form.php') ?>" class="btn btn-primary">+ Add New Book</a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Search and Filter -->
<div class="card">
    <form method="GET" action="" class="search-box">
        <input type="text" name="search" placeholder="Search by title, author, or ISBN..." 
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
            <a href="<?= url('/admin/books_list.php') ?>" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Books Table -->
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>ISBN</th>
                    <th>Copies</th>
                    <th>Available</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($books)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No books found. <a href="<?= url('/admin/book_form.php') ?>">Add your first book</a>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?= $book['id'] ?></td>
                            <td><strong><?= sanitize($book['title']) ?></strong></td>
                            <td><?= sanitize($book['author']) ?></td>
                            <td><span class="badge badge-info"><?= sanitize($book['category']) ?></span></td>
                            <td><?= sanitize($book['isbn'] ?? '-') ?></td>
                            <td><?= $book['total_copies'] ?></td>
                            <td>
                                <?php if ($book['available_copies'] > 0): ?>
                                    <span class="text-success"><?= $book['available_copies'] ?></span>
                                <?php else: ?>
                                    <span class="text-danger">0</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('/admin/book_form.php') ?>?id=<?= $book['id'] ?>"
                                       class="btn btn-sm btn-secondary">Edit</a>
                                    <form method="POST" action="<?= url('/admin/book_delete.php') ?>"
                                          style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $book['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this book?');">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="text-muted" style="margin-top: 1rem;">
        Total: <?= count($books) ?> book(s)
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>

