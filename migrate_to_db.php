<?php
/**
 * Migration script to transfer data from JSON to SQL database
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db_config.php';

// Data file path
define('POSTS_FILE', __DIR__ . '/data/posts.json');

echo "Starting migration from JSON to SQL database...
";

try {
    // Check if JSON file exists
    if (!file_exists(POSTS_FILE)) {
        echo "No JSON data file found. Migration complete.
";
        exit(0);
    }
    
    // Read JSON data
    $content = file_get_contents(POSTS_FILE);
    $posts = json_decode($content, true) ?: [];
    
    if (empty($posts)) {
        echo "No posts found in JSON file. Migration complete.
";
        exit(0);
    }
    
    echo "Found " . count($posts) . " posts in JSON file.
";
    
    // Connect to database
    $pdo = getDatabaseConnection();
    
    // Begin transaction
    $pdo->beginTransaction();
    
    try {
        // Check if posts already exist in database
        $stmt = $pdo->query("SELECT COUNT(*) FROM posts");
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "Posts already exist in database. Skipping migration.
";
            echo "If you want to re-run the migration, please empty the posts table first.
";
            $pdo->rollBack();
            exit(0);
        }
        
        // Prepare insert statement
        $stmt = $pdo->prepare("
            INSERT INTO posts (id, title, content, status, author, date, created_at, updated_at, featured) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NULL, ?)
        ");
        
        $insertedCount = 0;
        
        // Insert each post
        foreach ($posts as $post) {
            // Ensure all required fields exist
            $id = isset($post['id']) ? (int)$post['id'] : null;
            $title = isset($post['title']) ? $post['title'] : 'Untitled';
            $content = isset($post['content']) ? $post['content'] : '';
            $status = isset($post['status']) ? $post['status'] : 'draft';
            $author = isset($post['author']) ? $post['author'] : 'Onbekend';
            $date = isset($post['date']) ? $post['date'] : date('Y-m-d H:i:s');
            $createdAt = isset($post['created_at']) ? date('Y-m-d H:i:s', $post['created_at']) : $date;
            $featured = isset($post['featured']) ? (bool)$post['featured'] : false;
            
            // Insert the post
            $stmt->execute([
                $id,
                $title,
                $content,
                $status,
                $author,
                $date,
                $createdAt,
                null,  // updated_at is NULL for new migrations
                $featured
            ]);
            
            $insertedCount++;
            echo "Inserted post: $title
";
        }
        
        // Commit transaction
        $pdo->commit();
        
        echo "Successfully migrated $insertedCount posts to database.
";
        
        // Optionally backup the JSON file
        $backupFile = POSTS_FILE . '.backup';
        if (copy(POSTS_FILE, $backupFile)) {
            echo "JSON file backed up to: $backupFile
";
            echo "You can now safely remove the original JSON file if desired.
";
        } else {
            echo "Warning: Could not create backup of JSON file.
";
        }
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "
";
    exit(1);
}

echo "Migration completed successfully!
";
?>