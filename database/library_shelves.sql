-- ============================================
-- Personal Library Shelves Feature
-- Run this to add the reading list functionality
-- ============================================

USE library_db;

-- Create reading_list table to track user's personal shelves
CREATE TABLE IF NOT EXISTS reading_list (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    shelf ENUM('currently_reading', 'want_to_read', 'finished') NOT NULL DEFAULT 'want_to_read',
    added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    started_at DATETIME NULL,
    finished_at DATETIME NULL,
    rating INT NULL CHECK (rating >= 1 AND rating <= 5),
    notes TEXT NULL,
    
    UNIQUE KEY unique_user_book (user_id, book_id),
    INDEX idx_user_shelf (user_id, shelf),
    
    CONSTRAINT fk_reading_list_user FOREIGN KEY (user_id) 
        REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_reading_list_book FOREIGN KEY (book_id) 
        REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
