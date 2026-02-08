<?php
/**
 * User - Private Messages
 * Send and receive private messages
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireLogin();

$userId = getCurrentUserId();
$db = getDB();

// Handle send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiverId = (int)$_POST['receiver_id'];
    $message = trim($_POST['message'] ?? '');
    
    if ($receiverId && $message) {
        $stmt = $db->prepare("
            INSERT INTO private_messages (sender_id, receiver_id, message, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $receiverId, $message]);
        setFlashMessage('success', 'Message sent!');
        header('Location: ' . url('/user/messages.php'));
        exit;
    }
}

// Get all users for messaging (except current user)
$usersStmt = $db->prepare("SELECT id, name FROM users WHERE id != ? AND is_active = 1 ORDER BY name");
$usersStmt->execute([$userId]);
$users = $usersStmt->fetchAll();

// Get conversations (grouped by other user)
$convStmt = $db->prepare("
    SELECT 
        CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as other_user_id,
        u.name as other_user_name,
        MAX(pm.created_at) as last_message_time,
        SUM(CASE WHEN pm.receiver_id = ? AND pm.is_read = 0 THEN 1 ELSE 0 END) as unread_count
    FROM private_messages pm
    JOIN users u ON u.id = CASE WHEN pm.sender_id = ? THEN pm.receiver_id ELSE pm.sender_id END
    WHERE pm.sender_id = ? OR pm.receiver_id = ?
    GROUP BY other_user_id, other_user_name
    ORDER BY last_message_time DESC
");
$convStmt->execute([$userId, $userId, $userId, $userId, $userId]);
$conversations = $convStmt->fetchAll();

$flash = getFlashMessage();
$pageTitle = 'Messages';
$currentPage = 'messages';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="page-header">
    <h1>✉️ Private Messages</h1>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- New Message Form -->
<div class="card">
    <div class="card-header">
        <h3>📝 Send New Message</h3>
    </div>
    <form method="POST" action="">
        <div class="form-group">
            <label for="receiver_id">To:</label>
            <select id="receiver_id" name="receiver_id" required>
                <option value="">Select a user...</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>"><?= sanitize($user['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="3" placeholder="Type your message..." required></textarea>
        </div>
        <button type="submit" name="send_message" class="btn btn-primary">Send Message</button>
    </form>
</div>

<!-- Conversations -->
<div class="card">
    <div class="card-header">
        <h3>💬 Conversations</h3>
    </div>
    
    <?php if (empty($conversations)): ?>
        <p class="text-muted">No conversations yet. Start one above!</p>
    <?php else: ?>
        <div class="conversations-list">
            <?php foreach ($conversations as $conv): ?>
                <a href="<?= url('/user/conversation.php?with=' . $conv['other_user_id']) ?>" class="conversation-item">
                    <div class="conv-user">
                        <span class="user-avatar">👤</span>
                        <span class="user-name"><?= sanitize($conv['other_user_name']) ?></span>
                        <?php if ($conv['unread_count'] > 0): ?>
                            <span class="badge badge-primary"><?= $conv['unread_count'] ?> new</span>
                        <?php endif; ?>
                    </div>
                    <span class="conv-time"><?= date('M d, H:i', strtotime($conv['last_message_time'])) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer_user.php'; ?>

