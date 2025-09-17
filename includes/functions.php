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

        // Ensure author field exists
        if (!isset($postRef['author']) || $postRef['author'] === null) {
            $postRef['author'] = 'Onbekend';
            $changed = true;
        }

        // Ensure featured flag exists
        if (!isset($postRef['featured'])) {
            $postRef['featured'] = false;
            $changed = true;
        }
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
 * Get the current featured post (first featured among published)
 */
function getFeaturedPost() {
    $posts = getPublishedPosts();
    foreach ($posts as $post) {
        if (!empty($post['featured'])) {
            return $post;
        }
    }
    return null;
}

/**
 * Set a post as featured; unfeature all others
 */
function setFeaturedPost($id) {
    $posts = getAllPosts();
    foreach ($posts as &$post) {
        $post['featured'] = ($post['id'] == $id);
    }
    unset($post);
    savePosts($posts);
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
function createPost($title, $content, $status = 'draft', $author = 'Onbekend') {
    // Convert plain text to HTML if it doesn't already contain significant HTML tags
    // Check for block-level elements or common inline formatting tags
    $hasHtmlTags = preg_match('/<(p|div|h[1-6]|ul|ol|li|strong|em|u|s|br|blockquote|pre)>/i', $content);
    
    if (!$hasHtmlTags && !empty(trim($content))) {
        $content = textToHtml($content);
    }
    
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
        'author' => $author ?: 'Onbekend',
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
function updatePost($id, $title, $content, $status, $author = null) {
    // Always convert content to HTML to ensure line breaks work
    if (!empty(trim($content))) {
        $content = textToHtml($content);
    }
    
    $posts = getAllPosts();
    
    foreach ($posts as &$post) {
        if ($post['id'] == $id) {
            $post['title'] = $title;
            $post['content'] = $content;
            $post['status'] = $status;
            if ($author !== null && $author !== '') {
                $post['author'] = $author;
            } elseif (!isset($post['author'])) {
                $post['author'] = 'Onbekend';
            }
            // ONLY update timestamp, NOT published date
            $post['updated_at'] = time();
            break;
        }
    }
    
    return savePosts($posts);
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
/**
 * Format date for display in ISO 8601 format (without T separator)
 */
function formatDate($dateString) {
    // Handle numeric timestamps (convert to string format first)
    if (is_numeric($dateString)) {
        $dateString = date('Y-m-d H:i:s', $dateString);
    }
    
    $date = new DateTime($dateString);
    return $date->format('Y-m-d H:i:s'); // ISO 8601 format without T
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
 * Sanitize user-provided HTML to prevent XSS while allowing basic formatting
 */
function sanitizeHtml($html) {
    if ($html === null || $html === '') {
        return '';
    }

    // Allow a safe subset of tags
    $allowedTags = '<p><br><strong><em><u><s><ul><ol><li><a><blockquote><code><pre><h2><h3><h4><hr>'; 
    $clean = strip_tags($html, $allowedTags);

    // Remove event handler attributes like onclick=, onerror=, etc.
    $clean = preg_replace('/\s+on[a-zA-Z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean);

    // Disallow javascript: and data: URLs in href/src
    $clean = preg_replace_callback('/<(a|img)\b([^>]*)>/i', function ($matches) {
        $tag = $matches[1];
        $attrs = $matches[2];

        // Neutralize dangerous protocols in href/src
        $attrs = preg_replace_callback('/\s(href|src)\s*=\s*("|\'|)([^"\'>\s]+)\2/i', function ($m) {
            $attrName = $m[1];
            $quote = $m[2] ?: '"';
            $url = $m[3];
            if (preg_match('/^(javascript:|data:)/i', $url)) {
                return ' ' . $attrName . '=' . $quote . '#' . $quote;
            }
            return ' ' . $attrName . '=' . $quote . $url . $quote;
        }, $attrs);

        // Ensure rel on links
        if (strtolower($tag) === 'a') {
            if (!preg_match('/\srel\s*=\s*/i', $attrs)) {
                $attrs .= ' rel="noopener noreferrer"';
            }
        }

        return '<' . $tag . $attrs . '>';
    }, $clean);

    return $clean;
}

/**
 * Convert plain text with newlines to HTML paragraphs
 */
function textToHtml($text) {
    if ($text === null || trim($text) === '') {
        return '';
    }
    
    // Normalize line endings
    $text = str_replace(["\r\n", "\r"], "\n", $text);
    
    // If it already has proper HTML block elements, leave it alone
    if (preg_match('/<(p|div|h[1-6]|ul|ol|blockquote|pre)(\s|>)/i', $text)) {
        return $text;
    }
    
    // Split into paragraphs (separated by double newlines)
    $paragraphs = preg_split('/\n\s*\n/', $text, -1, PREG_SPLIT_NO_EMPTY);
    
    // Process each paragraph with nl2br
    $htmlParagraphs = [];
    foreach ($paragraphs as $paragraph) {
        $paragraph = trim($paragraph);
        if ($paragraph !== '') {
            $htmlParagraphs[] = "<p>" . nl2br($paragraph) . "</p>";
        }
    }
    
    return implode("\n", $htmlParagraphs);
}