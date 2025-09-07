<?php
/**
 * Crime Blog Platform Configuration
 */

// Site configuration
define('SITE_TITLE', 'Crime Watch Blog');
define('SITE_DESCRIPTION', 'Latest crime reports and investigations');

// Data storage paths
define('DATA_DIR', __DIR__ . '/data');
define('POSTS_FILE', DATA_DIR . '/posts.json');

// Ensure data directory exists
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

// Initialize posts file if it doesn't exist
if (!file_exists(POSTS_FILE)) {
    file_put_contents(POSTS_FILE, json_encode([], JSON_PRETTY_PRINT));
}

/**
 * Load all blog posts from JSON file
 */
function loadPosts() {
    if (file_exists(POSTS_FILE)) {
        $content = file_get_contents(POSTS_FILE);
        return json_decode($content, true) ?: [];
    }
    return [];
}

/**
 * Save blog posts to JSON file
 */
function savePosts($posts) {
    return file_put_contents(POSTS_FILE, json_encode($posts, JSON_PRETTY_PRINT));
}

/**
 * Get post by ID
 */
function getPostById($id) {
    $posts = loadPosts();
    foreach ($posts as $post) {
        if ($post['id'] == $id) {
            return $post;
        }
    }
    return null;
}

/**
 * Get published posts only
 */
function getPublishedPosts() {
    $posts = loadPosts();
    return array_filter($posts, function($post) {
        return $post['status'] === 'published';
    });
}

/**
 * Search posts by title
 */
function searchPosts($query) {
    $posts = getPublishedPosts();
    if (empty($query)) {
        return $posts;
    }
    
    return array_filter($posts, function($post) use ($query) {
        return stripos($post['title'], $query) !== false;
    });
}

/**
 * Generate unique ID for new posts
 */
function generatePostId() {
    return uniqid('post_', true);
}

/**
 * Format date for display
 */
function formatDate($dateString) {
    return date('F j, Y', strtotime($dateString));
}

/**
 * Truncate text for previews
 */
function truncateText($text, $length = 150) {
    $text = strip_tags($text);
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}
?>