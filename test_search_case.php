<?php
/**
 * Test script to verify case-insensitive search functionality
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db_config.php';

echo "Testing search case-insensitivity...\n";

try {
    // Test database connection
    $pdo = getDatabaseConnection();
    echo "✓ Database connection successful\n";
    
    // Get all posts to find a title to search for
    $posts = getAllPosts();
    
    if (count($posts) > 0) {
        // Get the first post title
        $firstPostTitle = $posts[0]['title'];
        echo "Using post title for test: {$firstPostTitle}\n";
        
        // Create mixed case version of the title
        $mixedCaseQuery = '';
        for ($i = 0; $i < strlen($firstPostTitle); $i++) {
            $mixedCaseQuery .= ($i % 2 == 0) ? 
                strtoupper($firstPostTitle[$i]) : 
                strtolower($firstPostTitle[$i]);
        }
        
        echo "Mixed case search query: {$mixedCaseQuery}\n";
        
        // Search with mixed case
        $searchResults = searchPosts($mixedCaseQuery);
        
        // Check if original post is in results
        $found = false;
        foreach ($searchResults as $result) {
            if ($result['id'] == $posts[0]['id']) {
                $found = true;
                break;
            }
        }
        
        if ($found) {
            echo "✓ Case-insensitive search works! Found the post using mixed case query.\n";
        } else {
            echo "✗ Case-insensitive search failed! Could not find the post using mixed case query.\n";
        }
    } else {
        echo "No posts found to test with.\n";
    }
    
} catch (Exception $e) {
    echo "✗ Test failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>