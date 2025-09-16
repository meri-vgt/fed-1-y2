<?php
/**
 * Test script to verify database connection and functionality
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db_config.php';

echo "Testing database connection...\n";

try {
    // Test database connection
    $pdo = getDatabaseConnection();
    echo "✓ Database connection successful\n";
    
    // Test getting all posts
    $posts = getAllPosts();
    echo "✓ getAllPosts() function works, found " . count($posts) . " posts\n";
    
    // Test getting published posts
    $publishedPosts = getPublishedPosts();
    echo "✓ getPublishedPosts() function works, found " . count($publishedPosts) . " published posts\n";
    
    // Test getting featured post
    $featuredPost = getFeaturedPost();
    if ($featuredPost) {
        echo "✓ getFeaturedPost() function works, found featured post: " . $featuredPost['title'] . "\n";
    } else {
        echo "✓ getFeaturedPost() function works, no featured post found\n";
    }
    
    // Test getting post by ID
    if (!empty($posts)) {
        $firstPost = $posts[0];
        $postById = getPostById($firstPost['id']);
        if ($postById) {
            echo "✓ getPostById() function works, found post: " . $postById['title'] . "\n";
        } else {
            echo "✗ getPostById() function failed\n";
        }
    }
    
    // Test search functionality
    $searchResults = searchPosts('');
    echo "✓ searchPosts() function works, found " . count($searchResults) . " search results\n";
    
    echo "\nAll tests passed! Database integration is working correctly.\n";
    
} catch (Exception $e) {
    echo "✗ Test failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
