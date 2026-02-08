<?php
/**
 * Admin - Borrowings List
 * 
 * Shows all borrowings with filters.
 * Admin can mark books as returned.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = "Manage Borrowings";
$currentPage = "borrowings";

// Get filter parameters
$status = trim($_GET['status'] ?? '');
$search = trim($_GET['search'] ?? '');

// Handle return action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_id'])) {
    $returnId = (int)$_POST['return_id'];
    
    try {
        $db = getDB();
        
        // Get borrowing info
        $stmt = $db->prepare("SELECT * FROM borrowings WHERE id = ? AND status = 'borrowed'");
        $stmt->execute([$returnId]);
        $borrowing = $stmt->fetch();
        
        if ($borrowing) {
            // Mark as returned
            $updateStmt = $db->prepare("
                UPDATE borrowings SET returned_at = NOW(), status = 'returned' WHERE id = ?
            ");
            $updateStmt->execute([$returnId]);
            
            // Increase available copies
            $bookStmt = $db->prepare("
                UPDATE books SET available_copies = available_copies + 1 WHERE id = ?
            ");
            $bookStmt->execute([$borrowing['book_id']]);
            
            setFlashMessage('success', 'Book marked as returned.');
        } else {
            setFlashMessage('error', 'Borrowing not found or already returned.');
        }
    } catch (PDOException $e) {
        setFlashMessage('error', 'Database error: ' . $e->getMessage());
    }
    
    header('Location: ' . url('/admin/borrowings_list.php'));
    exit;
}

try {
    $db = getDB();
    
    // Update overdue status first
    $db->exec("
        UPDATE borrowings 
        SET status = 'overdue' 
        WHERE returned_at IS NULL 
          AND due_at < NOW() 
          AND status = 'borrowed'
    ");
    
    // Build query with filters
    $sql = "
        SELECT b.*, u.name as user_name, u.email as user_email, 
               bk.title as book_title, bk.author as book_author
        FROM borrowings b
        JOIN users u ON b.user_id = u.id
        JOIN books bk ON b.book_id = bk.id
        WHERE 1=1
    ";
    $params = [];
    
    if (!empty($status)) {
        $sql .= " AND b.status = ?";
        $params[] = $status;
    }
    
    if (!empty($search)) {
        $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR bk.title LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " ORDER BY b.borrowed_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $borrowings = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $borrowings = [];
}

$flash = getFlashMessage();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_admin.php';
?>

<div class="page-header">
    <h1>📋 Manage Borrowings</h1>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Filters -->
<div class="card">
    <form method="GET" action="" class="search-box">
        <input type="text" name="search" placeholder="Search by user or book title..." 
               value="<?= sanitize($search) ?>">
        <select name="status">
            <option value="">All Status</option>
            <option value="borrowed" <?= $status === 'borrowed' ? 'selected' : '' ?>>Borrowed</option>
            <option value="returned" <?= $status === 'returned' ? 'selected' : '' ?>>Returned</option>
            <option value="overdue" <?= $status === 'overdue' ? 'selected' : '' ?>>Overdue</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        <?php if ($search || $status): ?>
            <a href="<?= url('/admin/borrowings_list.php') ?>" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Borrowings Table -->
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Book</th>
                    <th>Borrowed</th>
                    <th>Due Date</th>
                    <th>Returned</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($borrowings)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No borrowings found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($borrowings as $borrow): ?>
                        <tr>
                            <td><?= $borrow['id'] ?></td>
                            <td>
                                <strong><?= sanitize($borrow['user_name']) ?></strong><br>
                                <small class="text-muted"><?= sanitize($borrow['user_email']) ?></small>
                            </td>
                            <td>
                                <strong><?= sanitize($borrow['book_title']) ?></strong><br>
                                <small class="text-muted"><?= sanitize($borrow['book_author']) ?></small>
                            </td>
                            <td><?= date('M d, Y', strtotime($borrow['borrowed_at'])) ?></td>
                            <td>
                                <?php
                                $dueDate = strtotime($borrow['due_at']);
                                $isOverdue = $borrow['status'] !== 'returned' && $dueDate < time();
                                ?>
                                <span class="<?= $isOverdue ? 'text-danger' : '' ?>">
                                    <?= date('M d, Y', $dueDate) ?>
                                </span>
                            </td>
                            <td>
                                <?= $borrow['returned_at']
                                    ? date('M d, Y', strtotime($borrow['returned_at']))
                                    : '<span class="text-muted">-</span>' ?>
                            </td>
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
                            <td>
                                <?php if ($borrow['status'] !== 'returned'): ?>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="return_id" value="<?= $borrow['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success"
                                                onclick="return confirm('Mark this book as returned?');">
                                            Return
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-muted" style="margin-top: 1rem;">
        Total: <?= count($borrowings) ?> borrowing(s)
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>

