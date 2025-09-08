<?php
/**
 * Core functions for Criminal Minds blog platform
 */

// Data file path
define('POSTS_FILE', __DIR__ . '/../data/posts.json');

/**
 * Compute the base URL for the app, so links work under subfolders (e.g., /fed-1-y2)
 */
function getBaseUrl() {
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $dirName = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $baseUrl = $dirName;
    if (preg_match('#/admin$#', $baseUrl)) {
        $baseUrl = rtrim(preg_replace('#/admin$#', '', $baseUrl), '/');
    }
    if ($baseUrl === '') { $baseUrl = '/'; }
    return $baseUrl;
}

/**
 * Get all posts from JSON file
 */
function getAllPosts() {
    if (!file_exists(POSTS_FILE)) {
        return [];
    }
    
    $content = file_get_contents(POSTS_FILE);
    $posts = json_decode($content, true) ?: [];
    
    // Normalize IDs if there are duplicates or non-numeric values
    $changed = false;
    $seenIds = [];
    $maxId = 0;
    foreach ($posts as $post) {
        $numericId = is_numeric($post['id']) ? (int)$post['id'] : 0;
        if ($numericId > $maxId) {
            $maxId = $numericId;
        }
    }
    foreach ($posts as &$postRef) {
        $numericId = is_numeric($postRef['id']) ? (int)$postRef['id'] : 0;
        if ($numericId <= 0 || isset($seenIds[$numericId])) {
            $maxId += 1;
            $postRef['id'] = $maxId;
            $changed = true;
        }
        $seenIds[(int)$postRef['id']] = true;
    }
    unset($postRef);
    if ($changed) {
        savePosts($posts);
    }
    
    return $posts;
}

/**
 * Save posts to JSON file
 */
function savePosts($posts) {
    return file_put_contents(POSTS_FILE, json_encode($posts, JSON_PRETTY_PRINT));
}

/**
 * Get published posts only
 */
function getPublishedPosts() {
    $posts = getAllPosts();
    return array_filter($posts, function($post) {
        return $post['status'] === 'published';
    });
}

/**
 * Get post by ID
 */
function getPostById($id) {
    $posts = getAllPosts();
    foreach ($posts as $post) {
        if ($post['id'] == $id) {
            return $post;
        }
    }
    return null;
}

/**
 * Create new post
 */
function createPost($title, $content, $status = 'draft') {
    $posts = getAllPosts();
    
    // Generate unique incremental ID
    $maxId = 0;
    foreach ($posts as $p) {
        if (isset($p['id']) && is_numeric($p['id'])) {
            $maxId = max($maxId, (int)$p['id']);
        }
    }
    $newId = $maxId + 1;

    $newPost = [
        'id' => $newId,
        'title' => $title,
        'content' => $content,
        'status' => $status,
        'date' => date('Y-m-d H:i:s'),
        'created_at' => time()
    ];
    
    array_unshift($posts, $newPost); // Add to beginning for latest first
    savePosts($posts);
    
    return $newPost;
}

/**
 * Update existing post
 */
function updatePost($id, $title, $content, $status) {
    $posts = getAllPosts();
    
    foreach ($posts as &$post) {
        if ($post['id'] == $id) {
            $post['title'] = $title;
            $post['content'] = $content;
            $post['status'] = $status;
            $post['date'] = date('Y-m-d H:i:s');
            break;
        }
    }
    
    savePosts($posts);
}

/**
 * Delete post by ID
 */
function deletePost($id) {
    $posts = getAllPosts();
    $posts = array_filter($posts, function($post) use ($id) {
        return $post['id'] != $id;
    });
    
    savePosts(array_values($posts)); // Re-index array
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
 * Format date for display
 */
function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('d M Y');
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

/**
 * Generate slug from title for SEO-friendly URLs
 */
function generateSlug($title) {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/\s+/', '-', $slug);
    return trim($slug, '-');
}