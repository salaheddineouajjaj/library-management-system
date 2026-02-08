<?php
/**
 * User - Forum
 * Public discussion board
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireLogin();

$userId = getCurrentUserId();
$db = getDB();

// Handle new topic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_topic'])) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if ($title && $content) {
        $stmt = $db->prepare("
            INSERT INTO forum_topics (user_id, title, content, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $title, $content]);
        setFlashMessage('success', 'Topic created!');
        header('Location: ' . url('/user/forum.php'));
        exit;
    }
}

// Get topics
$stmt = $db->query("
    SELECT t.*, u.name as author_name,
           (SELECT COUNT(*) FROM forum_replies WHERE topic_id = t.id) as reply_count
    FROM forum_topics t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.is_pinned DESC, t.updated_at DESC
");
$topics = $stmt->fetchAll();

$flash = getFlashMessage();
$pageTitle = 'Forum';
$currentPage = 'forum';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="page-header">
    <h1>💬 Community Forum</h1>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- New Topic Form -->
<div class="card">
    <div class="card-header">
        <h3>📝 Start a Discussion</h3>
    </div>
    <form method="POST" action="">
        <div class="form-group">
            <label for="title">Topic Title</label>
            <input type="text" id="title" name="title" placeholder="What do you want to discuss?" required>
        </div>
        <div class="form-group">
            <label for="content">Your Message</label>
            <textarea id="content" name="content" rows="3" placeholder="Share your thoughts..." required></textarea>
        </div>
        <button type="submit" name="create_topic" class="btn btn-primary">Create Topic</button>
    </form>
</div>

<!-- Topics List -->
<div class="card">
    <div class="card-header">
        <h3>📋 Discussions</h3>
    </div>
    
    <?php if (empty($topics)): ?>
        <p class="text-muted">No discussions yet. Be the first to start one!</p>
    <?php else: ?>
        <div class="forum-topics">
            <?php foreach ($topics as $topic): ?>
                <a href="<?= url('/user/topic.php?id=' . $topic['id']) ?>" class="forum-topic-item">
                    <div class="topic-main">
                        <?php if ($topic['is_pinned']): ?>
                            <span class="badge badge-info">📌 Pinned</span>
                        <?php endif; ?>
                        <h4><?= sanitize($topic['title']) ?></h4>
                        <p class="topic-preview"><?= sanitize(substr($topic['content'], 0, 100)) ?>...</p>
                    </div>
                    <div class="topic-meta">
                        <span class="topic-author">👤 <?= sanitize($topic['author_name']) ?></span>
                        <span class="topic-date">📅 <?= date('M d, Y', strtotime($topic['created_at'])) ?></span>
                        <span class="topic-replies">💬 <?= $topic['reply_count'] ?> replies</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer_user.php'; ?>

