<?php
/**
 * User - View Topic
 * View topic and replies
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireLogin();

$userId = getCurrentUserId();
$topicId = (int)($_GET['id'] ?? 0);

if (!$topicId) {
    header('Location: ' . url('/user/forum.php'));
    exit;
}

$db = getDB();

// Get topic
$stmt = $db->prepare("
    SELECT t.*, u.name as author_name
    FROM forum_topics t
    JOIN users u ON t.user_id = u.id
    WHERE t.id = ?
");
$stmt->execute([$topicId]);
$topic = $stmt->fetch();

if (!$topic) {
    header('Location: ' . url('/user/forum.php'));
    exit;
}

// Handle new reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $content = trim($_POST['content'] ?? '');
    
    if ($content) {
        $stmt = $db->prepare("
            INSERT INTO forum_replies (topic_id, user_id, content, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$topicId, $userId, $content]);
        
        // Update topic timestamp
        $db->prepare("UPDATE forum_topics SET updated_at = NOW() WHERE id = ?")->execute([$topicId]);
        
        setFlashMessage('success', 'Reply posted!');
        header('Location: ' . url('/user/topic.php?id=' . $topicId));
        exit;
    }
}

// Get replies
$stmt = $db->prepare("
    SELECT r.*, u.name as author_name
    FROM forum_replies r
    JOIN users u ON r.user_id = u.id
    WHERE r.topic_id = ?
    ORDER BY r.created_at ASC
");
$stmt->execute([$topicId]);
$replies = $stmt->fetchAll();

$flash = getFlashMessage();
$pageTitle = $topic['title'];
$currentPage = 'forum';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="page-header">
    <a href="<?= url('/user/forum.php') ?>" class="btn btn-secondary">← Back to Forum</a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Topic -->
<div class="card forum-post">
    <?php if ($topic['is_pinned']): ?>
        <span class="badge badge-info">📌 Pinned</span>
    <?php endif; ?>
    <h2><?= sanitize($topic['title']) ?></h2>
    <div class="post-meta">
        <span>👤 <?= sanitize($topic['author_name']) ?></span>
        <span>📅 <?= date('M d, Y H:i', strtotime($topic['created_at'])) ?></span>
    </div>
    <div class="post-content">
        <?= nl2br(sanitize($topic['content'])) ?>
    </div>
</div>

<!-- Replies -->
<div class="card">
    <div class="card-header">
        <h3>💬 Replies (<?= count($replies) ?>)</h3>
    </div>
    
    <?php if (empty($replies)): ?>
        <p class="text-muted">No replies yet. Be the first to respond!</p>
    <?php else: ?>
        <div class="forum-replies">
            <?php foreach ($replies as $reply): ?>
                <div class="forum-reply">
                    <div class="reply-meta">
                        <span class="reply-author">👤 <?= sanitize($reply['author_name']) ?></span>
                        <span class="reply-date"><?= date('M d, Y H:i', strtotime($reply['created_at'])) ?></span>
                    </div>
                    <div class="reply-content">
                        <?= nl2br(sanitize($reply['content'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Reply Form -->
<div class="card">
    <div class="card-header">
        <h3>✍️ Write a Reply</h3>
    </div>
    <form method="POST" action="">
        <div class="form-group">
            <textarea name="content" rows="3" placeholder="Share your thoughts..." required></textarea>
        </div>
        <button type="submit" name="reply" class="btn btn-primary">Post Reply</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer_user.php'; ?>

