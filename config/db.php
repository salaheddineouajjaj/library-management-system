<?php
/**
 * Database Connection
 * 
 * Creates a PDO connection to MySQL database.
 * Uses prepared statements for security.
 */

require_once __DIR__ . '/config.php';

/**
 * Get PDO database connection
 * 
 * @return PDO Database connection object
 * @throws PDOException If connection fails
 */
function getDB() {
    static $pdo = null;
    
    // Return existing connection if available (singleton pattern)
    if ($pdo !== null) {
        return $pdo;
    }
    
    // Build DSN (Data Source Name)
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    // PDO options for better error handling and security
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Return associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                   // Use real prepared statements
    ];
    
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // In production, log this error instead of displaying
        die("Database connection failed: " . $e->getMessage());
    }
}

