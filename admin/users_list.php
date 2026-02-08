<?php
/**
 * Admin - Users List
 * 
 * Displays all users with search and links to add/edit/delete.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = "Manage Users";
$currentPage = "users";

// Get search parameter
$search = trim($_GET['search'] ?? '');
$role = trim($_GET['role'] ?? '');

try {
    $db = getDB();
    
    // Build query with optional filters
    $sql = "SELECT u.*, 
            (SELECT COUNT(*) FROM borrowings b WHERE b.user_id = u.id AND b.status = 'borrowed') as active_borrowings
            FROM users u WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($role)) {
        $sql .= " AND u.role = ?";
        $params[] = $role;
    }
    
    $sql .= " ORDER BY u.created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $users = [];
}

$flash = getFlashMessage();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_admin.php';
?>

<div class="page-header">
    <h1>👥 Manage Users</h1>
    <a href="<?= url('/admin/user_form.php') ?>" class="btn btn-primary">+ Add New User</a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
        <?= sanitize($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Search and Filter -->
<div class="card">
    <form method="GET" action="" class="search-box">
        <input type="text" name="search" placeholder="Search by name or email..." 
               value="<?= sanitize($search) ?>">
        <select name="role">
            <option value="">All Roles</option>
            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>User</option>
        </select>
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($search || $role): ?>
            <a href="<?= url('/admin/users_list.php') ?>" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Users Table -->
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Active Borrows</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><strong><?= sanitize($user['name']) ?></strong></td>
                            <td><?= sanitize($user['email']) ?></td>
                            <td>
                                <span class="badge <?= $user['role'] === 'admin' ? 'badge-warning' : 'badge-info' ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $user['active_borrowings'] ?></td>
                            <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('/admin/user_form.php') ?>?id=<?= $user['id'] ?>"
                                       class="btn btn-sm btn-secondary">Edit</a>
                                    <?php if ($user['id'] !== getCurrentUserId()): ?>
                                        <form method="POST" action="<?= url('/admin/user_delete.php') ?>" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this user?');">
                                                Delete
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="text-muted" style="margin-top: 1rem;">
        Total: <?= count($users) ?> user(s)
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>

