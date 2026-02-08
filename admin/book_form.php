<?php
/**
 * Admin - Book Form
 * 
 * Add new book or edit existing book.
 * Uses GET ?id=X for edit mode.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$currentPage = "books";
$error = '';
$book = null;
$isEdit = false;

// Check if editing existing book
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $db = getDB();
    
    // If editing, fetch the book
    if ($bookId > 0) {
        $stmt = $db->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$bookId]);
        $book = $stmt->fetch();
        
        if (!$book) {
            setFlashMessage('error', 'Book not found.');
            header('Location: ' . url('/admin/books_list.php'));
            exit;
        }
        $isEdit = true;
    }
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $isbn = trim($_POST['isbn'] ?? '') ?: null;
        $publisher = trim($_POST['publisher'] ?? '') ?: null;
        $publish_year = !empty($_POST['publish_year']) ? (int)$_POST['publish_year'] : null;
        $total_copies = (int)($_POST['total_copies'] ?? 1);
        $available_copies = (int)($_POST['available_copies'] ?? 1);
        $description = trim($_POST['description'] ?? '') ?: null;
        
        // Validation
        if (empty($title) || empty($author) || empty($category)) {
            $error = 'Title, Author, and Category are required.';
        } elseif ($total_copies < 1) {
            $error = 'Total copies must be at least 1.';
        } elseif ($available_copies > $total_copies) {
            $error = 'Available copies cannot exceed total copies.';
        } elseif ($available_copies < 0) {
            $error = 'Available copies cannot be negative.';
        } else {
            if ($isEdit) {
                // Update existing book
                $stmt = $db->prepare("
                    UPDATE books SET 
                        title = ?, author = ?, category = ?, isbn = ?, 
                        publisher = ?, publish_year = ?, total_copies = ?,
                        available_copies = ?, description = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $title, $author, $category, $isbn, 
                    $publisher, $publish_year, $total_copies,
                    $available_copies, $description, $bookId
                ]);
                setFlashMessage('success', 'Book updated successfully.');
            } else {
                // Insert new book
                $stmt = $db->prepare("
                    INSERT INTO books 
                        (title, author, category, isbn, publisher, publish_year, 
                         total_copies, available_copies, description, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $title, $author, $category, $isbn, 
                    $publisher, $publish_year, $total_copies,
                    $available_copies, $description
                ]);
                setFlashMessage('success', 'Book added successfully.');
            }

            header('Location: ' . url('/admin/books_list.php'));
            exit;
        }
        
        // Keep form data on error
        $book = [
            'title' => $title, 'author' => $author, 'category' => $category,
            'isbn' => $isbn, 'publisher' => $publisher, 'publish_year' => $publish_year,
            'total_copies' => $total_copies, 'available_copies' => $available_copies,
            'description' => $description
        ];
    }
    
    // Get existing categories for suggestions
    $catStmt = $db->query("SELECT DISTINCT category FROM books ORDER BY category");
    $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

$pageTitle = $isEdit ? "Edit Book" : "Add Book";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_admin.php';
?>

<div class="page-header">
    <h1><?= $isEdit ? '✏️ Edit Book' : '➕ Add New Book' ?></h1>
    <a href="<?= url('/admin/books_list.php') ?>" class="btn btn-secondary">← Back to List</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?= sanitize($error) ?></div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="">
        <div class="form-row">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" 
                       value="<?= sanitize($book['title'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="author">Author *</label>
                <input type="text" id="author" name="author" 
                       value="<?= sanitize($book['author'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="category">Category *</label>
                <input type="text" id="category" name="category" 
                       value="<?= sanitize($book['category'] ?? '') ?>" 
                       list="category-list" required>
                <datalist id="category-list">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= sanitize($cat) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="form-group">
                <label for="isbn">ISBN</label>
                <input type="text" id="isbn" name="isbn"
                       value="<?= sanitize($book['isbn'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="publisher">Publisher</label>
                <input type="text" id="publisher" name="publisher"
                       value="<?= sanitize($book['publisher'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="publish_year">Publish Year</label>
                <input type="number" id="publish_year" name="publish_year"
                       value="<?= sanitize($book['publish_year'] ?? '') ?>"
                       min="1000" max="<?= date('Y') + 1 ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="total_copies">Total Copies *</label>
                <input type="number" id="total_copies" name="total_copies"
                       value="<?= (int)($book['total_copies'] ?? 1) ?>"
                       min="1" required>
            </div>
            <div class="form-group">
                <label for="available_copies">Available Copies *</label>
                <input type="number" id="available_copies" name="available_copies"
                       value="<?= (int)($book['available_copies'] ?? 1) ?>"
                       min="0" required>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description"
                      placeholder="Brief description of the book..."><?= sanitize($book['description'] ?? '') ?></textarea>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Update Book' : 'Add Book' ?>
            </button>
            <a href="<?= url('/admin/books_list.php') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>

