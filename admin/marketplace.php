<?php
/**
 * Admin - Marketplace Management
 * Manage books for sale
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireAdmin();

$db = getDB();

// Handle add/update book sale
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = (int)$_POST['book_id'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $isAvailable = isset($_POST['is_available']) ? 1 : 0;
    
    if ($bookId && $price > 0) {
        $stmt = $db->prepare("
            INSERT INTO book_sales (book_id, price, stock, is_available)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE price = ?, stock = ?, is_available = ?
        ");
        $stmt->execute([$bookId, $price, $stock, $isAvailable, $price, $stock, $isAvailable]);
        setFlashMessage('success', 'Book sale updated!');
        header('Location: ' . url('/admin/marketplace.php'));
        exit;
    }
}

// Get all books with sale info
$stmt = $db->query("
    SELECT b.*, bs.id as sale_id, bs.price, bs.stock, bs.is_available
    FROM books b
    LEFT JOIN book_sales bs ON b.id = bs.book_id
    ORDER BY b.title
");
$books = $stmt->fetchAll();

// Get recent orders
$ordersStmt = $db->query("
    SELECT o.*, u.name as user_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
");
$recentOrders = $ordersStmt->fetchAll();

$flash = getFlashMessage();
$pageTitle = 'Marketplace';
$currentPage = 'marketplace';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_admin.php';
?>

<div class="page-header">
    <h1>🛒 Marketplace Management</h1>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Books for Sale -->
<div class="card">
    <div class="card-header">
        <h3>📚 Books for Sale</h3>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= sanitize($book['title']) ?></td>
                        <td><?= sanitize($book['author']) ?></td>
                        <td>
                            <?= $book['price'] ? '$' . number_format($book['price'], 2) : '-' ?>
                        </td>
                        <td><?= $book['stock'] ?? 0 ?></td>
                        <td>
                            <?php if ($book['sale_id']): ?>
                                <span class="badge badge-<?= $book['is_available'] ? 'success' : 'warning' ?>">
                                    <?= $book['is_available'] ? 'Active' : 'Inactive' ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Not Listed</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="" class="inline-form">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <input type="number" name="price" step="0.01" min="0" 
                                       value="<?= $book['price'] ?? '' ?>" placeholder="Price" style="width:80px;">
                                <input type="number" name="stock" min="0" 
                                       value="<?= $book['stock'] ?? 0 ?>" placeholder="Stock" style="width:60px;">
                                <label>
                                    <input type="checkbox" name="is_available" <?= ($book['is_available'] ?? 0) ? 'checked' : '' ?>>
                                    Active
                                </label>
                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Orders -->
<div class="card">
    <div class="card-header">
        <h3>📦 Recent Orders</h3>
    </div>
    <?php if (empty($recentOrders)): ?>
        <p class="text-muted">No orders yet.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= sanitize($order['user_name']) ?></td>
                            <td>$<?= number_format($order['total_amount'], 2) ?></td>
                            <td>
                                <span class="badge badge-<?= $order['status'] === 'completed' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>

