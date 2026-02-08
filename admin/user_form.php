<?php
/**
 * Admin - User Form
 * 
 * Add new user or edit existing user.
 * Uses GET ?id=X for edit mode.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

$currentPage = "users";
$error = '';
$user = null;
$isEdit = false;

$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $db = getDB();
    
    // If editing, fetch the user
    if ($userId > 0) {
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            setFlashMessage('error', 'User not found.');
            header('Location: ' . url('/admin/users_list.php'));
            exit;
        }
        $isEdit = true;
    }
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($name) || empty($email)) {
            $error = 'Name and Email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (!$isEdit && empty($password)) {
            $error = 'Password is required for new users.';
        } elseif (!empty($password) && strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif (!in_array($role, ['admin', 'user'])) {
            $error = 'Invalid role selected.';
        } else {
            // Check for duplicate email
            $checkSql = "SELECT id FROM users WHERE email = ?";
            $checkParams = [$email];
            if ($isEdit) {
                $checkSql .= " AND id != ?";
                $checkParams[] = $userId;
            }
            $checkStmt = $db->prepare($checkSql);
            $checkStmt->execute($checkParams);
            
            if ($checkStmt->fetch()) {
                $error = 'This email is already in use.';
            } else {
                if ($isEdit) {
                    // Update user
                    if (!empty($password)) {
                        $stmt = $db->prepare("
                            UPDATE users SET name = ?, email = ?, role = ?, 
                                   is_active = ?, password_hash = ? WHERE id = ?
                        ");
                        $stmt->execute([$name, $email, $role, $is_active, 
                                       password_hash($password, PASSWORD_DEFAULT), $userId]);
                    } else {
                        $stmt = $db->prepare("
                            UPDATE users SET name = ?, email = ?, role = ?, 
                                   is_active = ? WHERE id = ?
                        ");
                        $stmt->execute([$name, $email, $role, $is_active, $userId]);
                    }
                    setFlashMessage('success', 'User updated successfully.');
                } else {
                    // Insert new user
                    $stmt = $db->prepare("
                        INSERT INTO users (name, email, password_hash, role, is_active, created_at)
                        VALUES (?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), 
                                   $role, $is_active]);
                    setFlashMessage('success', 'User created successfully.');
                }

                header('Location: ' . url('/admin/users_list.php'));
                exit;
            }
        }
        
        // Keep form data on error
        $user = ['name' => $name, 'email' => $email, 'role' => $role, 'is_active' => $is_active];
    }
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

$pageTitle = $isEdit ? "Edit User" : "Add User";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_admin.php';
?>

<div class="page-header">
    <h1><?= $isEdit ? '✏️ Edit User' : '➕ Add New User' ?></h1>
    <a href="<?= url('/admin/users_list.php') ?>" class="btn btn-secondary">← Back to List</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?= sanitize($error) ?></div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="">
        <div class="form-row">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" 
                       value="<?= sanitize($user['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" 
                       value="<?= sanitize($user['email'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="password">
                    Password <?= $isEdit ? '(leave blank to keep current)' : '*' ?>
                </label>
                <input type="password" id="password" name="password" 
                       placeholder="<?= $isEdit ? 'Enter new password' : 'At least 6 characters' ?>"
                       <?= !$isEdit ? 'required' : '' ?>>
            </div>
            <div class="form-group">
                <label for="role">Role *</label>
                <select id="role" name="role" required>
                    <option value="user" <?= ($user['role'] ?? 'user') === 'user' ? 'selected' : '' ?>>
                        User
                    </option>
                    <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                        Admin
                    </option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1"
                       <?= ($user['is_active'] ?? 1) ? 'checked' : '' ?>
                       style="width: auto;">
                <span>Account is active</span>
            </label>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Update User' : 'Create User' ?>
            </button>
            <a href="<?= url('/admin/users_list.php') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer_admin.php'; ?>

