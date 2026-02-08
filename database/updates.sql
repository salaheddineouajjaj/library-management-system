-- ============================================
-- Library Management System - Database Updates
-- New features: Notifications, Marketplace, Chat
-- Run this AFTER schema.sql has been imported
-- ============================================

USE library_db;

-- ============================================
-- NOTIFICATIONS TABLE
-- Admin broadcasts messages to all users
-- ============================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'warning', 'success', 'new_book') NOT NULL DEFAULT 'info',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_created_at (created_at),

    CONSTRAINT fk_notifications_admin FOREIGN KEY (admin_id)
        REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Track which users have read notifications
CREATE TABLE IF NOT EXISTS notification_reads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    notification_id INT NOT NULL,
    user_id INT NOT NULL,
    read_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_read (notification_id, user_id),

    CONSTRAINT fk_notif_read_notification FOREIGN KEY (notification_id)
        REFERENCES notifications(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_notif_read_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- MARKETPLACE TABLES
-- Users can purchase books
-- ============================================

-- Books available for purchase
CREATE TABLE IF NOT EXISTS book_sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_book (book_id),
    INDEX idx_available (is_available),

    CONSTRAINT fk_book_sales_book FOREIGN KEY (book_id)
        REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- User's shopping cart
CREATE TABLE IF NOT EXISTS cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_sale_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_cart_item (user_id, book_sale_id),

    CONSTRAINT fk_cart_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_cart_book_sale FOREIGN KEY (book_sale_id)
        REFERENCES book_sales(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Completed orders
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user (user_id),
    INDEX idx_status (status),

    CONSTRAINT fk_orders_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Order items (books in each order)
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price_at_purchase DECIMAL(10, 2) NOT NULL,

    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id)
        REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_order_items_book FOREIGN KEY (book_id)
        REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- FORUM/CHAT TABLES
-- Public discussions and private messages
-- ============================================

-- Forum topics (public discussions)
CREATE TABLE IF NOT EXISTS forum_topics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    is_pinned TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_user (user_id),
    INDEX idx_created (created_at),
    INDEX idx_pinned (is_pinned),

    CONSTRAINT fk_topics_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Forum replies
CREATE TABLE IF NOT EXISTS forum_replies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    topic_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_topic (topic_id),

    CONSTRAINT fk_replies_topic FOREIGN KEY (topic_id)
        REFERENCES forum_topics(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_replies_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Private messages
CREATE TABLE IF NOT EXISTS private_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id),
    INDEX idx_unread (is_read),

    CONSTRAINT fk_pm_sender FOREIGN KEY (sender_id)
        REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_pm_receiver FOREIGN KEY (receiver_id)
        REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Add some books to marketplace
INSERT INTO book_sales (book_id, price, stock, is_available) VALUES
(1, 12.99, 10, 1),
(2, 9.99, 15, 1),
(3, 11.99, 8, 1),
(4, 8.99, 12, 1),
(5, 14.99, 6, 1),
(6, 10.99, 10, 1),
(7, 13.99, 5, 1),
(8, 15.99, 7, 1),
(9, 11.99, 9, 1),
(10, 16.99, 20, 1);

-- Add sample notification
INSERT INTO notifications (admin_id, title, message, type) VALUES
(1, 'Welcome to the Library!', 'Welcome to our new Library Management System! Browse books, join discussions, and purchase your favorites in our marketplace.', 'info');

-- Add sample forum topic
INSERT INTO forum_topics (user_id, title, content, is_pinned) VALUES
(1, 'Welcome to the Library Forum!', 'This is a place to discuss books, ask for recommendations, and connect with fellow readers. Be respectful and enjoy!', 1);
