<?php
/**
 * Setup SQLite database and migrate data from JSON
 */

require_once 'includes/functions.php';

// Check if PDO and SQLite are available
if (!extension_loaded('pdo') || !extension_loaded('pdo_sqlite')) {
    die("Error: PDO or PDO SQLite driver not available. Cannot setup SQLite database.\n");
}

try {
    // Create database directory if it doesn't exist
    $dbDir = dirname(SQLITE_DB_PATH);
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }
    
    // Connect to SQLite database
    $db = new PDO('sqlite:' . SQLITE_DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create posts table
    $db->exec('DROP TABLE IF EXISTS posts');
    $db->exec('CREATE TABLE posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT "draft",
        author TEXT NOT NULL DEFAULT "Onbekend",
        date TEXT NOT NULL,
        created_at TEXT NOT NULL,
        updated_at TEXT DEFAULT NULL,
        featured INTEGER NOT NULL DEFAULT 0
    )');
    
    // Get posts from JSON
    $posts = [];
    if (file_exists(POSTS_FILE)) {
        $posts = json_decode(file_get_contents(POSTS_FILE), true);
    }
    
    // Insert posts into SQLite database
    if (!empty($posts)) {
        $stmt = $db->prepare('INSERT INTO posts (id, title, content, status, author, date, created_at, updated_at, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        
        foreach ($posts as $post) {
            // Convert timestamps to date strings if needed
            $updatedAt = null;
            if (isset($post['updated_at'])) {
                $updatedAt = is_numeric($post['updated_at']) ? 
                    date('Y-m-d H:i:s', $post['updated_at']) : 
                    $post['updated_at'];
            }
            
            $createdAt = isset($post['created_at']) ? 
                (is_numeric($post['created_at']) ? 
                    date('Y-m-d H:i:s', $post['created_at']) : 
                    $post['created_at']) : 
                $post['date'];
            
            $stmt->execute([
                $post['id'],
                $post['title'],
                $post['content'],
                $post['status'],
                $post['author'],
                $post['date'],
                $createdAt,
                $updatedAt,
                isset($post['featured']) ? $post['featured'] : 0
            ]);
        }
    }
    
    echo "SQLite database setup complete. Data migrated from JSON.\n";
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
}