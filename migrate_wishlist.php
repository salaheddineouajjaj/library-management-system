<?php
require_once __DIR__ . '/config/db.php';
try {
    $db = getDB();
    
    // Create wishlist table for "Plan to Read" feature
    $db->exec("CREATE TABLE IF NOT EXISTS wishlist (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        book_id INT NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_wishlist (user_id, book_id),
        CONSTRAINT fk_wishlist_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_wishlist_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");
    
    echo "Wishlist table created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
