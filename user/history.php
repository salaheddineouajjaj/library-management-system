<?php
/**
 * User - Borrowing History
 * 
 * Shows all past borrowings for the logged-in user.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = "Borrowing History";
$currentPage = "history";

$userId = getCurrentUserId();

try {
    $db = getDB();
    
    // Get all borrowings (including returned)
    $stmt = $db->prepare("
        SELECT b.*, bk.title, bk.author, bk.category
        FROM borrowings b
        JOIN books bk ON b.book_id = bk.id
        WHERE b.user_id = ?
        ORDER BY b.borrowed_at DESC
    ");
    $stmt->execute([$userId]);
    $history = $stmt->fetchAll();
    
    // Calculate statistics
    $totalBorrowed = count($history);
    $returned = 0;
    $active = 0;
    
    foreach ($history as $item) {
        if ($item['status'] === 'returned') {
            $returned++;
        } else {
            $active++;
        }
    }
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $history = [];
    $totalBorrowed = 0;
    $returned = 0;
    $active = 0;
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="page-header">
    <h1>📜 Borrowing History</h1>
</div>

<!-- Stats -->
<div class="stats-grid" style="margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-value"><?= $totalBorrowed ?></div>
        <div class="stat-label">Total Borrowed</div>
    </div>
    <div class="stat-card success">
        <div class="stat-value"><?= $returned ?></div>
        <div class="stat-label">Returned</div>
    </div>
    <div class="stat-card info">
        <div class="stat-value"><?= $active ?></div>
        <div class="stat-label">Currently Active</div>
    </div>
</div>

<?php if (empty($history)): ?>
    <div class="card empty-state">
        <p>You haven't borrowed any books yet.</p>
        <a href="<?= url('/user/browse_books.php') ?>" class="btn btn-primary">Browse Books</a>
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Borrowed</th>
                        <th>Due Date</th>
                        <th>Returned</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $item): ?>
                        <tr>
                            <td><strong><?= sanitize($item['title']) ?></strong></td>
                            <td><?= sanitize($item['author']) ?></td>
                            <td><span class="badge badge-secondary"><?= sanitize($item['category']) ?></span></td>
                            <td><?= date('M d, Y', strtotime($item['borrowed_at'])) ?></td>
                            <td><?= date('M d, Y', strtotime($item['due_at'])) ?></td>
                            <td>
                                <?= $item['returned_at'] 
                                    ? date('M d, Y', strtotime($item['returned_at'])) 
                                    : '<span class="text-muted">-</span>' ?>
                            </td>
                            <td>
                                <?php
                                $badgeClass = match($item['status']) {
                                    'borrowed' => 'badge-info',
                                    'returned' => 'badge-success',
                                    'overdue' => 'badge-danger',
                                    default => 'badge-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst($item['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer_user.php'; ?>

