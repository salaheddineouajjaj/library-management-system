<?php
/**
 * Mark Notification as Read
 * Handler for dismissing notifications
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notificationId = (int)($_POST['notification_id'] ?? 0);
    $userId = getCurrentUserId();
    
    if ($notificationId) {
        try {
            $db = getDB();
            $stmt = $db->prepare("
                INSERT IGNORE INTO notification_reads (notification_id, user_id, read_at)
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$notificationId, $userId]);
        } catch (PDOException $e) {
            // Silently fail
        }
    }
}

header('Location: ' . url('/user/dashboard.php'));
exit;

