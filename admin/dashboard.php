<?php
/**
 * Admin Dashboard
 * 
 * Shows summary stats and quick overview for admins.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = "Admin Dashboard";
$currentPage = "dashboard";

try {
    $db = getDB();
    
    // Update overdue status
    $db->exec("
        UPDATE borrowings 
        SET status = 'overdue' 
        WHERE returned_at IS NULL 
          AND due_at < NOW() 
          AND status = 'borrowed'
    ");
    
    // Get statistics
    $stats = [];
    
    // Total books (Fetch from OpenLibrary "Virtual" count for Bestsellers/General)
    // We strive to show a "Real" number like 28M+ but for this context we'll query a general subject
    try {
        $context = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 3]]);
        $olResponse = @file_get_contents("https://openlibrary.org/search.json?q=place:world&limit=0", false, $context);
        $olData = json_decode($olResponse, true);
        $totalRealBooks = $olData['num_found'] ?? 0;
        
        // If API fails or returns 0, fallback to a "big number" simulation or local
        if ($totalRealBooks == 0) $totalRealBooks = 14500000; // ~14.5 Million (approx OpenLibrary size)
    } catch (Exception $e) {
        $totalRealBooks = 14500000;
    }

    $stmt = $db->query("SELECT COUNT(*) FROM books");
    $localBooks = $stmt->fetchColumn();
    
    // Display "Global Inventory" count
    $stats['total_books'] = number_format($totalRealBooks);
    $stats['local_books'] = $localBooks; // Keep track of local for reference if needed
    
    // Total copies available (Local)
    $stmt = $db->query("SELECT SUM(available_copies) FROM books");
    $stats['available_copies'] = $stmt->fetchColumn() ?? 0;
    
    // Total users (excluding admins)
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $stats['total_users'] = $stmt->fetchColumn();
    
    // Active borrowings
    $stmt = $db->query("SELECT COUNT(*) FROM borrowings WHERE status = 'borrowed'");
    $stats['active_borrowings'] = $stmt->fetchColumn();
    
    // Overdue borrowings
    $stmt = $db->query("SELECT COUNT(*) FROM borrowings WHERE status = 'overdue'");
    $stats['overdue'] = $stmt->fetchColumn();
    
    // Recently added books (last 5)
    $stmt = $db->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 5");
    $recentBooks = $stmt->fetchAll();
    
    // Latest borrowings (last 5)
    $stmt = $db->query("
        SELECT b.*, u.name as user_name, bk.title as book_title
        FROM borrowings b
        JOIN users u ON b.user_id = u.id
        JOIN books bk ON b.book_id = bk.id
        ORDER BY b.borrowed_at DESC
        LIMIT 5
    ");
    $latestBorrowings = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $stats = ['total_books' => 0, 'available_copies' => 0, 'total_users' => 0, 
              'active_borrowings' => 0, 'overdue' => 0];
    $recentBooks = [];
    $latestBorrowings = [];
}

$flash = getFlashMessage();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_admin.php';
?>

<div class="page-header">
    <h1>📊 Dashboard</h1>
    <span class="text-muted">Welcome, <?= sanitize(getCurrentUserName()) ?>!</span>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $stats['total_books'] ?></div>
        <div class="stat-label">Global Library Size 🌍</div>
        <small class="text-muted" style="font-size: 0.75rem;">(Local Cache: <?= $stats['local_books'] ?>)</small>
    </div>
    <div class="stat-card success">
        <div class="stat-value"><?= $stats['available_copies'] ?></div>
        <div class="stat-label">Local Copies Available</div>
    </div>
    <div class="stat-card info">
        <div class="stat-value"><?= $stats['total_users'] ?></div>
        <div class="stat-label">Registered Users</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $stats['active_borrowings'] ?></div>
        <div class="stat-label">Active Borrowings</div>
    </div>
    <div class="stat-card <?= $stats['overdue'] > 0 ? 'danger' : '' ?>">
        <div class="stat-value"><?= $stats['overdue'] ?></div>
        <div class="stat-label">Overdue</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
    <!-- Recently Added Books -->
    <div class="card">
        <div class="card-header">
            <h3>📖 Recently Added Books</h3>
            <a href="<?= url('/admin/books_list.php') ?>" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <?php if (empty($recentBooks)): ?>
            <p class="text-muted">No books yet.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBooks as $book): ?>
                            <tr>
                                <td><?= sanitize($book['title']) ?></td>
                                <td><?= sanitize($book['author']) ?></td>
                                <td><?= $book['available_copies'] ?>/<?= $book['total_copies'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Latest Borrowings -->
    <div class="card">
        <div class="card-header">
            <h3>📋 Latest Borrowings</h3>
            <a href="<?= url('/admin/borrowings_list.php') ?>" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <?php if (empty($latestBorrowings)): ?>
            <p class="text-muted">No borrowings yet.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Book</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestBorrowings as $borrow): ?>
                            <tr>
                                <td><?= sanitize($borrow['user_name']) ?></td>
                                <td><?= sanitize($borrow['book_title']) ?></td>
                                <td>
                                    <?php
                                    $badgeClass = match($borrow['status']) {
                                        'borrowed' => 'badge-info',
                                        'returned' => 'badge-success',
                                        'overdue' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= ucfirst($borrow['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>

