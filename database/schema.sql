-- ============================================
-- Library Management System - Database Schema
-- For MySQL 5.7+ / MariaDB 10.2+
-- Run this in phpMyAdmin or MySQL CLI
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS library_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE library_db;

-- ============================================
-- USERS TABLE
-- Stores all users: admins and regular users
-- ============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB;

-- ============================================
-- BOOKS TABLE
-- Stores all library books
-- ============================================
CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    isbn VARCHAR(20) NULL,
    publisher VARCHAR(255) NULL,
    publish_year INT NULL,
    total_copies INT NOT NULL DEFAULT 1,
    available_copies INT NOT NULL DEFAULT 1,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_title (title),
    INDEX idx_author (author),
    INDEX idx_category (category),
    INDEX idx_available (available_copies),
    
    -- Ensure available copies never exceeds total copies
    CONSTRAINT chk_copies CHECK (available_copies <= total_copies AND available_copies >= 0)
) ENGINE=InnoDB;

-- ============================================
-- BORROWINGS TABLE
-- Tracks book borrowing history
-- ============================================
CREATE TABLE borrowings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    borrowed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    due_at DATETIME NOT NULL,
    returned_at DATETIME NULL,
    status ENUM('borrowed', 'returned', 'overdue') NOT NULL DEFAULT 'borrowed',
    
    INDEX idx_user_id (user_id),
    INDEX idx_book_id (book_id),
    INDEX idx_status (status),
    INDEX idx_due_at (due_at),
    
    -- Foreign key relationships
    CONSTRAINT fk_borrowings_user FOREIGN KEY (user_id) 
        REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_borrowings_book FOREIGN KEY (book_id) 
        REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- INSERT DEFAULT ADMIN USER
-- Password: admin123 (change this!)
-- ============================================
INSERT INTO users (name, email, password_hash, role, is_active) VALUES 
('Administrator', 'admin@library.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);
-- Note: The password hash above is for 'password'. Generate a new one with password_hash('admin123', PASSWORD_DEFAULT)

-- ============================================
-- INSERT SAMPLE BOOKS (Optional - for testing)
-- ============================================
INSERT INTO books (title, author, category, isbn, publisher, publish_year, total_copies, available_copies, description) VALUES
('To Kill a Mockingbird', 'Harper Lee', 'Fiction', '978-0061120084', 'Harper Perennial', 1960, 5, 5, 'A classic of modern American literature about racial injustice.'),
('1984', 'George Orwell', 'Science Fiction', '978-0451524935', 'Signet Classics', 1949, 3, 3, 'A dystopian novel about totalitarianism and surveillance.'),
('The Great Gatsby', 'F. Scott Fitzgerald', 'Fiction', '978-0743273565', 'Scribner', 1925, 4, 4, 'A story of wealth, love, and the American Dream.'),
('Pride and Prejudice', 'Jane Austen', 'Romance', '978-0141439518', 'Penguin Classics', 1813, 3, 3, 'A romantic novel about manners and matrimony.'),
('The Hobbit', 'J.R.R. Tolkien', 'Fantasy', '978-0547928227', 'Mariner Books', 1937, 4, 4, 'A fantasy adventure about Bilbo Baggins journey.'),
('Murder on the Orient Express', 'Agatha Christie', 'Mystery', '978-0062693662', 'William Morrow', 1934, 3, 3, 'A famous detective Hercule Poirot mystery.'),
('The Catcher in the Rye', 'J.D. Salinger', 'Fiction', '978-0316769488', 'Little Brown', 1951, 2, 2, 'A novel about teenage alienation and angst.'),
('Dune', 'Frank Herbert', 'Science Fiction', '978-0441172719', 'Ace', 1965, 3, 3, 'An epic science fiction tale set on a desert planet.'),
('The Da Vinci Code', 'Dan Brown', 'Mystery', '978-0307474278', 'Anchor', 2003, 4, 4, 'A mystery thriller involving secret societies.'),
('Harry Potter and the Philosophers Stone', 'J.K. Rowling', 'Fantasy', '978-0747532699', 'Bloomsbury', 1997, 5, 5, 'The first book in the beloved Harry Potter series.');

-- ============================================
-- INSERT SAMPLE USER (Optional - for testing)
-- Password: user123
-- ============================================
INSERT INTO users (name, email, password_hash, role, is_active) VALUES 
('John Student', 'user@library.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1);

