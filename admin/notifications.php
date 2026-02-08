<?php
/**
 * Admin - Manage Notifications
 * Send and view system notifications
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireAdmin();

$error = '';
$success = '';

// Handle new notification creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $type = $_POST['type'] ?? 'info';
    
    if (empty($title) || empty($message)) {
        $error = 'Title and message are required.';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("
                INSERT INTO notifications (admin_id, title, message, type, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([getCurrentUserId(), $title, $message, $type]);
            $success = 'Notification sent successfully!';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Get all notifications
try {
    $db = getDB();
    $stmt = $db->query("
        SELECT n.*, u.name as admin_name,
               (SELECT COUNT(*) FROM notification_reads WHERE notification_id = n.id) as read_count,
               (SELECT COUNT(*) FROM users WHERE role = 'user') as total_users
        FROM notifications n
        JOIN users u ON n.admin_id = u.id
        ORDER BY n.created_at DESC
    ");
    $notifications = $stmt->fetchAll();
} catch (PDOException $e) {
    $notifications = [];
}

$pageTitle = 'Notifications';
$currentPage = 'notifications';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_admin.php';
?>

<div class="page-header">
    <h1>🔔 Notifications</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?= sanitize($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= sanitize($success) ?></div>
<?php endif; ?>

<!-- Create Notification Form -->
<div class="card">
    <div class="card-header">
        <h3>📢 Send New Notification</h3>
    </div>
    <form method="POST" action="">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" placeholder="Enter notification title" required>
        </div>
        
        <div class="form-group">
            <label for="type">Type</label>
            <select id="type" name="type">
                <option value="info">ℹ️ Info</option>
                <option value="success">✅ Success</option>
                <option value="warning">⚠️ Warning</option>
                <option value="new_book">📚 New Book</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="4" placeholder="Enter notification message" required></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Send Notification</button>
    </form>
</div>

<!-- Notifications List -->
<div class="card">
    <div class="card-header">
        <h3>📋 Sent Notifications</h3>
    </div>
    
    <?php if (empty($notifications)): ?>
        <p class="text-muted">No notifications sent yet.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Sent By</th>
                        <th>Date</th>
                        <th>Read By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notifications as $notif): ?>
                        <tr>
                            <td>
                                <?php
                                $icons = ['info' => 'ℹ️', 'success' => '✅', 'warning' => '⚠️', 'new_book' => '📚'];
                                echo $icons[$notif['type']] ?? 'ℹ️';
                                ?>
                            </td>
                            <td><?= sanitize($notif['title']) ?></td>
                            <td><?= sanitize(substr($notif['message'], 0, 50)) ?>...</td>
                            <td><?= sanitize($notif['admin_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($notif['created_at'])) ?></td>
                            <td><?= $notif['read_count'] ?>/<?= $notif['total_users'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>

