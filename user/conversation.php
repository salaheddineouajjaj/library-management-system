<?php
/**
 * User - Conversation View
 * View messages with a specific user
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireLogin();

$userId = getCurrentUserId();
$otherUserId = (int)($_GET['with'] ?? 0);

if (!$otherUserId) {
    header('Location: ' . url('/user/messages.php'));
    exit;
}

$db = getDB();

// Get other user info
$userStmt = $db->prepare("SELECT id, name FROM users WHERE id = ?");
$userStmt->execute([$otherUserId]);
$otherUser = $userStmt->fetch();

if (!$otherUser) {
    header('Location: ' . url('/user/messages.php'));
    exit;
}

// Handle send reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $message = trim($_POST['message'] ?? '');
    
    if ($message) {
        $stmt = $db->prepare("
            INSERT INTO private_messages (sender_id, receiver_id, message, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $otherUserId, $message]);
        header('Location: ' . url('/user/conversation.php?with=' . $otherUserId));
        exit;
    }
}

// Mark messages as read
$markRead = $db->prepare("
    UPDATE private_messages 
    SET is_read = 1 
    WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
");
$markRead->execute([$otherUserId, $userId]);

// Get messages
$stmt = $db->prepare("
    SELECT pm.*, 
           CASE WHEN pm.sender_id = ? THEN 'sent' ELSE 'received' END as direction
    FROM private_messages pm
    WHERE (pm.sender_id = ? AND pm.receiver_id = ?)
       OR (pm.sender_id = ? AND pm.receiver_id = ?)
    ORDER BY pm.created_at ASC
");
$stmt->execute([$userId, $userId, $otherUserId, $otherUserId, $userId]);
$messages = $stmt->fetchAll();

$pageTitle = 'Chat with ' . $otherUser['name'];
$currentPage = 'messages';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="page-header">
    <a href="<?= url('/user/messages.php') ?>" class="btn btn-secondary">← Back to Messages</a>
    <h1>💬 Chat with <?= sanitize($otherUser['name']) ?></h1>
</div>

<!-- Messages -->
<div class="card chat-container">
    <div class="chat-messages">
        <?php if (empty($messages)): ?>
            <p class="text-muted text-center">No messages yet. Start the conversation!</p>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <div class="chat-message <?= $msg['direction'] ?>">
                    <div class="message-bubble">
                        <?= nl2br(sanitize($msg['message'])) ?>
                    </div>
                    <span class="message-time"><?= date('M d, H:i', strtotime($msg['created_at'])) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Reply Form -->
    <form method="POST" action="" class="chat-input">
        <textarea name="message" rows="2" placeholder="Type a message..." required></textarea>
        <button type="submit" name="send_reply" class="btn btn-primary">Send</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer_user.php'; ?>

