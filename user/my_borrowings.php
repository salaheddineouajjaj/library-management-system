<?php
/**
 * User - My Borrowings
 * 
 * Shows user's current borrowed books with return option.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = "My Borrowed Books";
$currentPage = "borrowed";

$userId = getCurrentUserId();

try {
    $db = getDB();
    
    // Update overdue status for this user
    $db->prepare("
        UPDATE borrowings 
        SET status = 'overdue' 
        WHERE user_id = ? 
          AND returned_at IS NULL 
          AND due_at < NOW() 
          AND status = 'borrowed'
    ")->execute([$userId]);
    
    // Get current borrowings
    $stmt = $db->prepare("
        SELECT b.*, bk.title, bk.author, bk.category, bk.cover_url
        FROM borrowings b
        JOIN books bk ON b.book_id = bk.id
        WHERE b.user_id = ? AND b.status IN ('borrowed', 'overdue')
        ORDER BY b.due_at ASC
    ");
    $stmt->execute([$userId]);
    $borrowings = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $borrowings = [];
}

$flash = getFlashMessage();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="page-header">
    <h1>📚 My Borrowed Books</h1>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<?php if (empty($borrowings)): ?>
    <div class="card empty-state">
        <p>You don't have any books borrowed at the moment.</p>
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
                        <th>Borrowed On</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrowings as $borrow): ?>
                        <?php
                        $dueDate = strtotime($borrow['due_at']);
                        $daysLeft = ceil(($dueDate - time()) / 86400);
                        $isOverdue = $borrow['status'] === 'overdue';
                        $coverUrl = !empty($borrow['cover_url']) ? $borrow['cover_url'] : 'https://via.placeholder.com/128x196?text=No+Cover';
                        ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <img src="<?= $coverUrl ?>" alt="<?= sanitize($borrow['title']) ?>" 
                                         style="width: 45px; height: 65px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                         onerror="this.onerror=null;this.src='https://via.placeholder.com/128x196?text=No+Cover';">
                                    <strong><?= sanitize($borrow['title']) ?></strong>
                                </div>
                            </td>
                            <td><?= sanitize($borrow['author']) ?></td>
                            <td><span class="badge badge-info"><?= sanitize($borrow['category']) ?></span></td>
                            <td><?= date('M d, Y', strtotime($borrow['borrowed_at'])) ?></td>
                            <td class="<?= $isOverdue ? 'text-danger' : '' ?>">
                                <?= date('M d, Y', $dueDate) ?>
                                <?php if ($isOverdue): ?>
                                    <br><small>(<?= abs($daysLeft) ?> days overdue)</small>
                                <?php else: ?>
                                    <br><small>(<?= $daysLeft ?> days left)</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($isOverdue): ?>
                                    <span class="badge badge-danger">Overdue</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" action="<?= url('/user/return_book.php') ?>">
                                    <input type="hidden" name="borrowing_id" value="<?= $borrow['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success"
                                            onclick="return confirm('Return this book?');">
                                        Return
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <p class="text-muted mt-2">
            You have <?= count($borrowings) ?> book(s) currently borrowed.
            Maximum allowed: <?= MAX_BORROWED_BOOKS ?> books.
        </p>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer_user.php'; ?>

