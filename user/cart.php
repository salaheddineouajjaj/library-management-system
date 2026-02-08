<?php
/**
 * User - Shopping Cart
 * View and checkout cart items
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireLogin();

$userId = getCurrentUserId();
$db = getDB();

// Handle remove from cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $itemId = (int)$_POST['item_id'];
    $stmt = $db->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
    $stmt->execute([$itemId, $userId]);
    header('Location: ' . url('/user/cart.php'));
    exit;
}

// Handle checkout
// Handle checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    
    if (empty($address) || empty($contact)) {
        setFlashMessage('error', 'Please provide both your address and contact number (WhatsApp).');
    } else {
        try {
            $db->beginTransaction();
            
            // Check if columns exist, if not add them (Migration on the fly)
            try {
                $db->query("SELECT delivery_address FROM orders LIMIT 1");
            } catch (PDOException $e) {
                $db->query("ALTER TABLE orders ADD COLUMN delivery_address TEXT NOT NULL, ADD COLUMN contact_number VARCHAR(20) NOT NULL, ADD COLUMN payment_method ENUM('card', 'cash_on_delivery') NOT NULL DEFAULT 'cash_on_delivery'");
            }
            
            // Get cart items
            $cartStmt = $db->prepare("
                SELECT ci.*, bs.price, bs.stock, bs.book_id
                FROM cart_items ci
                JOIN book_sales bs ON ci.book_sale_id = bs.id
                WHERE ci.user_id = ?
            ");
            $cartStmt->execute([$userId]);
            $cartItems = $cartStmt->fetchAll();
            
            if (empty($cartItems)) {
                throw new Exception('Cart is empty.');
            }
            
            // Calculate total and check stock
            $total = 0;
            foreach ($cartItems as $item) {
                if ($item['quantity'] > $item['stock']) {
                    throw new Exception("Not enough stock for one or more items.");
                }
                $total += $item['price'] * $item['quantity'];
            }
            
            // Create order with Contact Info
            $orderStmt = $db->prepare("
                INSERT INTO orders (user_id, total_amount, status, delivery_address, contact_number, payment_method, created_at)
                VALUES (?, ?, 'pending', ?, ?, 'cash_on_delivery', NOW())
            ");
            $orderStmt->execute([$userId, $total, $address, $contact]);
            $orderId = $db->lastInsertId();
            
            // Add order items and update stock
            foreach ($cartItems as $item) {
                // Add order item
                $itemStmt = $db->prepare("
                    INSERT INTO order_items (order_id, book_id, quantity, price_at_purchase)
                    VALUES (?, ?, ?, ?)
                ");
                $itemStmt->execute([$orderId, $item['book_id'], $item['quantity'], $item['price']]);
                
                // Update stock
                $stockStmt = $db->prepare("UPDATE book_sales SET stock = stock - ? WHERE id = ?");
                $stockStmt->execute([$item['quantity'], $item['book_sale_id']]);
            }
            
            // Clear cart
            $clearStmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $clearStmt->execute([$userId]);
            
            $db->commit();
            setFlashMessage('success', 'Order placed successfully! We will contact you on WhatsApp for delivery.');
            header('Location: ' . url('/user/orders.php'));
            exit;
            
        } catch (Exception $e) {
            $db->rollBack();
            setFlashMessage('error', $e->getMessage());
        }
    }
}

// Get cart items
$stmt = $db->prepare("
    SELECT ci.*, bs.price, b.title, b.author
    FROM cart_items ci
    JOIN book_sales bs ON ci.book_sale_id = bs.id
    JOIN books b ON bs.book_id = b.id
    WHERE ci.user_id = ?
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

$flash = getFlashMessage();
$pageTitle = 'Shopping Cart';
$currentPage = 'marketplace';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="page-header">
    <h1>🛒 Shopping Cart</h1>
    <a href="<?= url('/user/marketplace.php') ?>" class="btn btn-secondary">← Continue Shopping</a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<?php if (empty($cartItems)): ?>
    <div class="card empty-state">
        <p>Your cart is empty.</p>
        <a href="<?= url('/user/marketplace.php') ?>" class="btn btn-primary">Browse Books</a>
    </div>
<?php else: ?>
    <div class="grid-layout" style="grid-template-columns: 2fr 1fr; gap: var(--spacing-lg);">
        <!-- Cart Items Column -->
        <div class="card">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <strong><?= sanitize($item['title']) ?></strong><br>
                                    <small class="text-muted">by <?= sanitize($item['author']) ?></small>
                                </td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                        <button type="submit" name="remove_item" class="btn btn-sm btn-danger">&times;</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
             <div style="margin-top: var(--spacing-md); text-align: right;">
                <h3>Total: $<?= number_format($total, 2) ?></h3>
            </div>
        </div>

        <!-- Checkout Form Column -->
        <div class="card" style="height: fit-content;">
            <h3>🚚 Delivery Details</h3>
            <p class="text-muted mb-3">Pay with Cash on Delivery</p>
            
            <form method="POST">
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea name="address" id="address" rows="3" required placeholder="Street, City, Building..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="contact">WhatsApp Number</label>
                    <input type="text" name="contact" id="contact" required placeholder="+1 234 567 890">
                </div>
                
                <hr>
                
                <div class="form-group">
                    <button type="submit" name="checkout" class="btn btn-primary btn-block">
                        Confirm Order - $<?= number_format($total, 2) ?>
                        <br><small>(Cash on Delivery)</small>
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer_user.php'; ?>

