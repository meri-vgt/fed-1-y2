<?php
/**
 * Setup script to create the database and tables
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'criminal_minds');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    // Connect to MySQL server without specifying database
    $dsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '" . DB_NAME . "' created or already exists.\n";
    
    // Connect to the specific database
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // Create posts table
    $sql = "
        CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
            author VARCHAR(100) NOT NULL DEFAULT 'Onbekend',
            date DATETIME NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            featured BOOLEAN NOT NULL DEFAULT FALSE,
            INDEX idx_status (status),
            INDEX idx_date (date),
            INDEX idx_featured (featured)
        )
    ";
    
    $pdo->exec($sql);
    echo "Posts table created or already exists.\n";
    
    echo "Database setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "Database setup failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
