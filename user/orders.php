<?php
/**
 * User - Order History
 * View past purchases
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireLogin();

$userId = getCurrentUserId();

// Get orders with items
try {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT o.*, 
               GROUP_CONCAT(CONCAT(b.title, ' (x', oi.quantity, ')') SEPARATOR ', ') as items
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN books b ON oi.book_id = b.id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $orders = [];
}

$flash = getFlashMessage();
$pageTitle = 'My Orders';
$currentPage = 'marketplace';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="page-header">
    <h1>📦 My Orders</h1>
    <a href="<?= url('/user/marketplace.php') ?>" class="btn btn-secondary">← Back to Shop</a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<?php if (empty($orders)): ?>
    <div class="card empty-state">
        <p>You haven't made any purchases yet.</p>
        <a href="<?= url('/user/marketplace.php') ?>" class="btn btn-primary">Browse Books</a>
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
                            <td><?= sanitize($order['items']) ?></td>
                            <td><strong>$<?= number_format($order['total_amount'], 2) ?></strong></td>
                            <td>
                                <span class="badge badge-<?= $order['status'] === 'completed' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($order['status']) ?>
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

