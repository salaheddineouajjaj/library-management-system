<?php
/**
 * User Dashboard
 * 
 * Shows welcome message, stats, recommendations, and AI link.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/recommendations.php';

$pageTitle = "Dashboard";
$currentPage = "dashboard";

$userId = getCurrentUserId();
$userName = getCurrentUserName();

try {
    $db = getDB();
    
    // Update overdue status
    $db->prepare("
        UPDATE borrowings 
        SET status = 'overdue' 
        WHERE user_id = ? AND returned_at IS NULL AND due_at < NOW() AND status = 'borrowed'
    ")->execute([$userId]);
    
    // Get user statistics
    $stats = [];
    
    // Currently borrowed books
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM borrowings 
        WHERE user_id = ? AND status IN ('borrowed', 'overdue')
    ");
    $stmt->execute([$userId]);
    $stats['current'] = $stmt->fetchColumn();
    
    // Overdue books
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM borrowings WHERE user_id = ? AND status = 'overdue'
    ");
    $stmt->execute([$userId]);
    $stats['overdue'] = $stmt->fetchColumn();
    
    // Total borrowed (all time)
    $stmt = $db->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id = ?");
    $stmt->execute([$userId]);
    $stats['total'] = $stmt->fetchColumn();
    
    // Get books due soon (within 3 days)
    $stmt = $db->prepare("
        SELECT b.*, bk.title, bk.author
        FROM borrowings b
        JOIN books bk ON b.book_id = bk.id
        WHERE b.user_id = ? 
          AND b.status IN ('borrowed', 'overdue')
          AND b.due_at <= DATE_ADD(NOW(), INTERVAL 3 DAY)
        ORDER BY b.due_at ASC
        LIMIT 5
    ");
    $stmt->execute([$userId]);
    $dueSoon = $stmt->fetchAll();
    
    // Get recommendations
    $recommendations = getRecommendations($userId, 6);

    // Get unread notifications
    $notifStmt = $db->prepare("
        SELECT n.* FROM notifications n
        WHERE n.id NOT IN (
            SELECT notification_id FROM notification_reads WHERE user_id = ?
        )
        ORDER BY n.created_at DESC
        LIMIT 5
    ");
    $notifStmt->execute([$userId]);
    $notifications = $notifStmt->fetchAll();

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $stats = ['current' => 0, 'overdue' => 0, 'total' => 0];
    $dueSoon = [];
    $recommendations = ['books' => [], 'reason' => '', 'error' => true];
    $notifications = [];
}

$flash = getFlashMessage();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="page-header">
    <h1>👋 Welcome, <?= sanitize($userName) ?>!</h1>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Notifications -->
<?php if (!empty($notifications)): ?>
    <div class="card">
        <div class="card-header">
            <h3>🔔 Notifications</h3>
        </div>
        <?php foreach ($notifications as $notif): ?>
            <div class="notification-item type-<?= $notif['type'] ?>">
                <strong><?= sanitize($notif['title']) ?></strong>
                <p><?= sanitize($notif['message']) ?></p>
                <small class="text-muted"><?= date('M d, Y', strtotime($notif['created_at'])) ?></small>
                <form method="POST" action="<?= url('/user/mark_notification_read.php') ?>" style="display:inline;">
                    <input type="hidden" name="notification_id" value="<?= $notif['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-secondary">Dismiss</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $stats['current'] ?></div>
        <div class="stat-label">Books Borrowed</div>
    </div>
    <div class="stat-card <?= $stats['overdue'] > 0 ? 'danger' : 'success' ?>">
        <div class="stat-value"><?= $stats['overdue'] ?></div>
        <div class="stat-label">Overdue</div>
    </div>
    <div class="stat-card info">
        <div class="stat-value"><?= $stats['total'] ?></div>
        <div class="stat-label">Total Borrowed</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= MAX_BORROWED_BOOKS - $stats['current'] ?></div>
        <div class="stat-label">Slots Available</div>
    </div>
</div>

<!-- Due Soon Warning -->
<?php if (!empty($dueSoon)): ?>
    <div class="card" style="border-color: var(--color-warning);">
        <div class="card-header">
            <h3>⚠️ Due Soon or Overdue</h3>
            <a href="<?= url('/user/my_borrowings.php') ?>" class="btn btn-sm btn-warning">View All</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dueSoon as $book): ?>
                        <?php $isOverdue = strtotime($book['due_at']) < time(); ?>
                        <tr>
                            <td>
                                <strong><?= sanitize($book['title']) ?></strong><br>
                                <small class="text-muted"><?= sanitize($book['author']) ?></small>
                            </td>
                            <td class="<?= $isOverdue ? 'text-danger' : 'text-warning' ?>">
                                <?= date('M d, Y', strtotime($book['due_at'])) ?>
                            </td>
                            <td>
                                <?php if ($isOverdue): ?>
                                    <span class="badge badge-danger">Overdue</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Due Soon</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- AI Suggestions Link -->
<div class="card ai-card">
    <h3>🤖 Need Help Finding Your Next Read?</h3>
    <p class="text-muted">
        Try our AI-powered book suggestion feature! Describe what you're looking for,
        and our AI will recommend books based on your preferences.
    </p>
    <a href="<?= url('/user/ai_suggestions.php') ?>" class="btn btn-primary">Get AI Suggestions</a>
</div>

<!-- Recommendations Section -->
<section class="recommendations-section">
    <h2>📚 Recommended For You</h2>
    <p class="text-muted"><?= sanitize($recommendations['reason']) ?></p>

    <?php if (empty($recommendations['books'])): ?>
        <div class="card empty-state">
            <p>No recommendations available yet. Start borrowing books to get personalized suggestions!</p>
            <a href="<?= url('/user/browse_books.php') ?>" class="btn btn-primary">Browse Books</a>
        </div>
    <?php else: ?>
        <div class="book-grid">
            <?php foreach ($recommendations['books'] as $book): 
                $coverUrl = !empty($book['cover_url']) ? $book['cover_url'] : 'https://via.placeholder.com/128x196?text=No+Cover';
            ?>
                <div class="book-card">
                    <div class="book-cover">
                        <img src="<?= $coverUrl ?>" alt="<?= sanitize($book['title']) ?>" loading="lazy"
                             onerror="this.onerror=null;this.src='https://via.placeholder.com/128x196?text=No+Cover';">
                    </div>
                    <div class="book-details">
                        <h3><?= sanitize($book['title']) ?></h3>
                        <p class="book-author">by <?= sanitize($book['author']) ?></p>
                        <span class="book-category"><?= sanitize($book['category']) ?></span>

                        <?php if (!empty($book['description'])): ?>
                            <p class="book-description"><?= sanitize($book['description']) ?></p>
                        <?php endif; ?>

                        <div class="book-availability">
                            <span class="copies available">
                                <?= $book['available_copies'] ?> available
                            </span>

                            <?php if ($stats['current'] < MAX_BORROWED_BOOKS): ?>
                                <form method="POST" action="<?= url('/user/borrow_book.php') ?>">
                                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-primary">Borrow</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer_user.php'; ?>

