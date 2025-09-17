<?php
/**
 * Core functions for Criminal Minds blog platform
 */

// Data file path
define('POSTS_FILE', __DIR__ . '/../data/posts.json');

// Try to include database configuration if it exists
$dbConfigFile = __DIR__ . '/db_config.php';
if (file_exists($dbConfigFile)) {
    include_once $dbConfigFile;
}

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
 * Check if database is available and working
 */
function isDatabaseWorking() {
    // Check if db_config.php functions exist
    if (!function_exists('getDatabaseConnection') || !function_exists('isDatabaseAvailable')) {
        return false;
    }
    
    // Check if database is available
    if (!isDatabaseAvailable()) {
        return false;
    }
    
    // Test a simple query
    try {
        $db = getDatabaseConnection();
        if ($db) {
            if ($db instanceof PDO) {
                $stmt = $db->query("SELECT 1");
                return $stmt !== false;
            } else {
                $result = $db->query("SELECT 1");
                return $result !== false;
            }
        }
    } catch (Exception $e) {
        // Database test failed
        error_log("Database test failed: " . $e->getMessage());
    }
    
    return false;
}

/**
 * Get all posts from database (if available) or JSON file
 */
function getAllPosts() {
    // Try database first if available
    if (isDatabaseWorking()) {
        try {
            $db = getDatabaseConnection();
            if ($db) {
                if ($db instanceof PDO) {
                    $stmt = $db->query("SELECT * FROM posts ORDER BY date DESC");
                    if ($stmt) {
                        $posts = [];
                        while ($row = $stmt->fetch()) {
                            // Ensure all required fields exist and map correctly
                            $row['author'] = $row['author'] ?? 'Onbekend';
                            $row['featured'] = isset($row['featured']) ? (bool)$row['featured'] : false;
                            // Map database columns to expected format
                            $row['created_at'] = $row['created_at'] ?? $row['date'];
                            $row['date'] = $row['date'] ?? $row['created_at'];
                            $posts[] = $row;
                        }
                        return $posts;
                    }
                } else {
                    // MySQLi
                    $result = $db->query("SELECT * FROM posts ORDER BY date DESC");
                    if ($result) {
                        $posts = [];
                        while ($row = $result->fetch_assoc()) {
                            // Ensure all required fields exist and map correctly
                            $row['author'] = $row['author'] ?? 'Onbekend';
                            $row['featured'] = isset($row['featured']) ? (bool)$row['featured'] : false;
                            // Map database columns to expected format
                            $row['created_at'] = $row['created_at'] ?? $row['date'];
                            $row['date'] = $row['date'] ?? $row['created_at'];
                            $posts[] = $row;
                        }
                        return $posts;
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Database getAllPosts failed: " . $e->getMessage());
        }
    }
    
    // Fallback to JSON
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
        
        // Ensure created_at exists
        if (!isset($postRef['created_at'])) {
            $postRef['created_at'] = $postRef['date'] ?? date('Y-m-d H:i:s');
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
    // Try database first if available
    if (isDatabaseWorking()) {
        try {
            $db = getDatabaseConnection();
            if ($db) {
                if ($db instanceof PDO) {
                    $stmt = $db->query("SELECT * FROM posts WHERE status = 'published' ORDER BY date DESC");
                    if ($stmt) {
                        $posts = [];
                        while ($row = $stmt->fetch()) {
                            $row['author'] = $row['author'] ?? 'Onbekend';
                            $row['featured'] = isset($row['featured']) ? (bool)$row['featured'] : false;
                            $posts[] = $row;
                        }
                        return $posts;
                    }
                } else {
                    // MySQLi
                    $result = $db->query("SELECT * FROM posts WHERE status = 'published' ORDER BY date DESC");
                    if ($result) {
                        $posts = [];
                        while ($row = $result->fetch_assoc()) {
                            $row['author'] = $row['author'] ?? 'Onbekend';
                            $row['featured'] = isset($row['featured']) ? (bool)$row['featured'] : false;
                            $posts[] = $row;
                        }
                        return $posts;
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Database getPublishedPosts failed: " . $e->getMessage());
        }
    }
    
    // Fallback to JSON
    $posts = getAllPosts();
    return array_filter($posts, function($post) {
        return $post['status'] === 'published';
    });
}

/**
 * Get the current featured post (first featured among published)
 */
function getFeaturedPost() {
    // Try database first if available
    if (isDatabaseWorking()) {
        try {
            $db = getDatabaseConnection();
            if ($db) {
                if ($db instanceof PDO) {
                    $stmt = $db->query("SELECT * FROM posts WHERE status = 'published' AND featured = 1 ORDER BY date DESC LIMIT 1");
                    if ($stmt && $row = $stmt->fetch()) {
                        $row['author'] = $row['author'] ?? 'Onbekend';
                        $row['featured'] = isset($row['featured']) ? (bool)$row['featured'] : false;
                        return $row;
                    }
                } else {
                    // MySQLi
                    $result = $db->query("SELECT * FROM posts WHERE status = 'published' AND featured = 1 ORDER BY date DESC LIMIT 1");
                    if ($result && $row = $result->fetch_assoc()) {
                        $row['author'] = $row['author'] ?? 'Onbekend';
                        $row['featured'] = isset($row['featured']) ? (bool)$row['featured'] : false;
                        return $row;
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Database getFeaturedPost failed: " . $e->getMessage());
        }
    }
    
    // Fallback to JSON
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
    // Try database first if available
    if (isDatabaseWorking()) {
        try {
            $db = getDatabaseConnection();
            if ($db) {
                if ($db instanceof PDO) {
                    // First unfeature all posts
                    $db->query("UPDATE posts SET featured = 0");
                    // Then feature the selected post
                    $stmt = $db->prepare("UPDATE posts SET featured = 1 WHERE id = ?");
                    return $stmt->execute([$id]);
                } else {
                    // MySQLi
                    // First unfeature all posts
                    $db->query("UPDATE posts SET featured = 0");
                    // Then feature the selected post
                    $stmt = $db->prepare("UPDATE posts SET featured = 1 WHERE id = ?");
                    return $stmt->execute([$id]);
                }
            }
        } catch (Exception $e) {
            error_log("Database setFeaturedPost failed: " . $e->getMessage());
        }
    }
    
    // Fallback to JSON
    $posts = getAllPosts();
    foreach ($posts as &$post) {
        $post['featured'] = ($post['id'] == $id);
    }
    unset($post);
    return savePosts($posts);
}

/**
 * Get post by ID
 */
function getPostById($id) {
    // Try database first if available
    if (isDatabaseWorking()) {
        try {
            $db = getDatabaseConnection();
            if ($db) {
                if ($db instanceof PDO) {
                    $stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
                    $stmt->execute([$id]);
                    $result = $stmt->fetch();
                    if ($result) {
                        $result['author'] = $result['author'] ?? 'Onbekend';
                        $result['featured'] = isset($result['featured']) ? (bool)$result['featured'] : false;
                        return $result;
                    }
                } else {
                    // MySQLi
                    $stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $row['author'] = $row['author'] ?? 'Onbekend';
                        $row['featured'] = isset($row['featured']) ? (bool)$row['featured'] : false;
                        return $row;
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Database getPostById failed: " . $e->getMessage());
        }
    }
    
    // Fallback to JSON
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
    
    // Try database first if available
    if (isDatabaseWorking()) {
        try {
            $db = getDatabaseConnection();
            if ($db) {
                $date = date('Y-m-d H:i:s');
                $createdAt = $date;
                
                if ($db instanceof PDO) {
                    $stmt = $db->prepare("INSERT INTO posts (title, content, status, author, date, created_at) VALUES (?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$title, $content, $status, $author, $date, $createdAt])) {
                        // Get the ID of the inserted post
                        $id = $db->lastInsertId();
                        return getPostById($id);
                    }
                } else {
                    // MySQLi
                    $stmt = $db->prepare("INSERT INTO posts (title, content, status, author, date, created_at) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $title, $content, $status, $author, $date, $createdAt);
                    if ($stmt->execute()) {
                        // Get the ID of the inserted post
                        $id = $db->insert_id;
                        return getPostById($id);
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Database createPost failed: " . $e->getMessage());
        }
    }
    
    // Fallback to JSON
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
    
    // Try database first if available
    if (isDatabaseWorking()) {
        try {
            $db = getDatabaseConnection();
            if ($db) {
                $date = date('Y-m-d H:i:s');
                
                if ($db instanceof PDO) {
                    $stmt = $db->prepare("UPDATE posts SET title = ?, content = ?, status = ?, author = ?, date = ?, updated_at = ? WHERE id = ?");
                    return $stmt->execute([$title, $content, $status, $author, $date, $date, $id]);
                } else {
                    // MySQLi
                    $stmt = $db->prepare("UPDATE posts SET title = ?, content = ?, status = ?, author = ?, date = ?, updated_at = ? WHERE id = ?");
                    $stmt->bind_param("ssssssi", $title, $content, $status, $author, $date, $date, $id);
                    return $stmt->execute();
                }
            }
        } catch (Exception $e) {
            error_log("Database updatePost failed: " . $e->getMessage());
        }
    }
    
    // Fallback to JSON
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
    // Try database first if available
    if (isDatabaseWorking()) {
        try {
            $db = getDatabaseConnection();
            if ($db) {
                if ($db instanceof PDO) {
                    $stmt = $db->prepare("DELETE FROM posts WHERE id = ?");
                    return $stmt->execute([$id]);
                } else {
                    // MySQLi
                    $stmt = $db->prepare("DELETE FROM posts WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    return $stmt->execute();
                }
            }
        } catch (Exception $e) {
            error_log("Database deletePost failed: " . $e->getMessage());
        }
    }
    
    // Fallback to JSON
    $posts = getAllPosts();
    $posts = array_filter($posts, function($post) use ($id) {
        return $post['id'] != $id;
    });
    
    return savePosts(array_values($posts)); // Re-index array
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