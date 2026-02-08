<?php
/**
 * Book Recommendation System
 * 
 * Provides personalized book recommendations using PHP + MySQL.
 * No external AI API - purely based on user's borrowing history.
 * 
 * Logic:
 * 1. If user has borrowing history:
 *    - Find their most borrowed categories
 *    - Find their most borrowed authors
 *    - Recommend books in those categories/by those authors that they haven't read
 * 2. If user has little/no history:
 *    - Show popular books (most borrowed overall)
 */

require_once __DIR__ . '/../config/db.php';

/**
 * Get personalized book recommendations for a user
 * 
 * @param int $userId The user's ID
 * @param int $limit Maximum number of recommendations to return
 * @return array Array with 'books' and 'reason' keys
 */
function getRecommendations($userId, $limit = 6) {
    try {
        $db = getDB();
        
        // First, check if user has any borrowing history
        $historyStmt = $db->prepare("
            SELECT COUNT(*) as count, 
                   GROUP_CONCAT(DISTINCT bk.category) as categories,
                   GROUP_CONCAT(DISTINCT bk.author) as authors
            FROM borrowings b
            JOIN books bk ON b.book_id = bk.id
            WHERE b.user_id = ?
        ");
        $historyStmt->execute([$userId]);
        $history = $historyStmt->fetch();
        
        // If user has borrowed at least 2 books, use personalized recommendations
        if ($history['count'] >= 2) {
            return getPersonalizedRecommendations($db, $userId, $limit);
        } else {
            return getPopularBooks($db, $userId, $limit);
        }
        
    } catch (PDOException $e) {
        return [
            'books' => [],
            'reason' => 'Unable to load recommendations.',
            'error' => true
        ];
    }
}

/**
 * Get personalized recommendations based on user's borrowing history
 */
function getPersonalizedRecommendations($db, $userId, $limit) {
    // Get user's top categories (by frequency)
    $catStmt = $db->prepare("
        SELECT bk.category, COUNT(*) as borrow_count
        FROM borrowings b
        JOIN books bk ON b.book_id = bk.id
        WHERE b.user_id = ?
        GROUP BY bk.category
        ORDER BY borrow_count DESC
        LIMIT 3
    ");
    $catStmt->execute([$userId]);
    $topCategories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get user's top authors (by frequency)
    $authorStmt = $db->prepare("
        SELECT bk.author, COUNT(*) as borrow_count
        FROM borrowings b
        JOIN books bk ON b.book_id = bk.id
        WHERE b.user_id = ?
        GROUP BY bk.author
        ORDER BY borrow_count DESC
        LIMIT 3
    ");
    $authorStmt->execute([$userId]);
    $topAuthors = $authorStmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get books user has already borrowed (to exclude)
    $borrowedStmt = $db->prepare("SELECT DISTINCT book_id FROM borrowings WHERE user_id = ?");
    $borrowedStmt->execute([$userId]);
    $borrowedIds = $borrowedStmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($borrowedIds)) {
        $borrowedIds = [0]; // Prevent SQL error with empty IN clause
    }
    
    // Build recommendation query
    // Prioritize: books in favorite categories OR by favorite authors, not yet borrowed
    $placeholders = implode(',', array_fill(0, count($borrowedIds), '?'));
    $catPlaceholders = !empty($topCategories) 
        ? implode(',', array_fill(0, count($topCategories), '?')) 
        : "'__none__'";
    $authorPlaceholders = !empty($topAuthors) 
        ? implode(',', array_fill(0, count($topAuthors), '?')) 
        : "'__none__'";
    
    $sql = "
        SELECT *, 
               CASE 
                   WHEN category IN ($catPlaceholders) AND author IN ($authorPlaceholders) THEN 3
                   WHEN category IN ($catPlaceholders) THEN 2
                   WHEN author IN ($authorPlaceholders) THEN 1
                   ELSE 0
               END as relevance_score
        FROM books
        WHERE id NOT IN ($placeholders)
          AND available_copies > 0
          AND (category IN ($catPlaceholders) OR author IN ($authorPlaceholders))
        ORDER BY relevance_score DESC, RAND()
        LIMIT ?
    ";
    
    // Build parameters array
    $params = array_merge(
        $topCategories ?: [],
        $topAuthors ?: [],
        $borrowedIds,
        $topCategories ?: [],
        $topAuthors ?: [],
        [$limit]
    );
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll();
    
    $categoryList = implode(', ', array_slice($topCategories, 0, 2));
    
    return [
        'books' => $books,
        'reason' => "Based on your interest in $categoryList books",
        'error' => false
    ];
}

/**
 * Get popular books for users with little/no history
 */
function getPopularBooks($db, $userId, $limit) {
    // Get books user has already borrowed
    $borrowedStmt = $db->prepare("SELECT DISTINCT book_id FROM borrowings WHERE user_id = ?");
    $borrowedStmt->execute([$userId]);
    $borrowedIds = $borrowedStmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($borrowedIds)) {
        $borrowedIds = [0];
    }
    
    $placeholders = implode(',', array_fill(0, count($borrowedIds), '?'));
    
    // Get most borrowed books overall
    $sql = "
        SELECT bk.*, COUNT(b.id) as borrow_count
        FROM books bk
        LEFT JOIN borrowings b ON bk.id = b.book_id
        WHERE bk.id NOT IN ($placeholders)
          AND bk.available_copies > 0
        GROUP BY bk.id
        ORDER BY borrow_count DESC, bk.created_at DESC
        LIMIT ?
    ";
    
    $params = array_merge($borrowedIds, [$limit]);
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll();
    
    return [
        'books' => $books,
        'reason' => 'Popular books in our library',
        'error' => false
    ];
}

