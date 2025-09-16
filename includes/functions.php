<?php
/**
 * Core functions for Criminal Minds blog platform
 */

// Include database configuration
require_once __DIR__ . '/db_config.php';

// Data file path for JSON storage fallback
define('POSTS_FILE', __DIR__ . '/../data/posts.json');

/**
 * Compute the base URL for the app, so links work under subfolders (e.g., /fed-1-y2)
 */
function getBaseUrl() {
    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $dirName = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $baseUrl = $dirName;
    // If we are in a nested directory like /fed-1-y2/admin, step up to project root
    if (preg_match('#/admin$#', $baseUrl)) {
        $baseUrl = rtrim(preg_replace('#/admin$#', '', $baseUrl), '/');
    }
    if ($baseUrl === '') { $baseUrl = '/'; }
    return $baseUrl;
}

/**
 * Get all posts from database or JSON file
 */
function getAllPosts() {
    // Try to use database if available
    if (isDatabaseAvailable()) {
        try {
            $connection = getDatabaseConnection();
            
            // Check if we're using PDO or MySQLi
            if ($connection instanceof PDO) {
                $stmt = $connection->query("SELECT * FROM posts ORDER BY date DESC");
                $posts = $stmt->fetchAll();
            } else {
                // MySQLi
                $result = $connection->query("SELECT * FROM posts ORDER BY date DESC");
                $posts = [];
                while ($row = $result->fetch_assoc()) {
                    $posts[] = $row;
                }
            }
            
            // Ensure all posts have required fields
            foreach ($posts as &$post) {
                if (!isset($post['author']) || $post['author'] === null) {
                    $post['author'] = 'Onbekend';
                }
                if (!isset($post['featured'])) {
                    $post['featured'] = false;
                }
            }
            
            return $posts;
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            // Fall back to JSON
        }
    }
    
    // Fall back to JSON storage
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
        
        // Ensure created_at and updated_at fields exist
        if (!isset($postRef['created_at'])) {
            // If we have a date field, use that as created_at, otherwise use current time
            $postRef['created_at'] = isset($postRef['date']) ? strtotime($postRef['date']) : time();
            $changed = true;
        }
        
        if (!isset($postRef['updated_at'])) {
            // Initially, updated_at should match created_at
            $postRef['updated_at'] = $postRef['created_at'];
            $changed = true;
        }
    }
    unset($postRef);
    
    return $posts;
}

/**
 * Get published posts only
 */
function getPublishedPosts() {
    // Try to use database if available
    if (isDatabaseAvailable()) {
        try {
            $connection = getDatabaseConnection();
            
            // Check if we're using PDO or MySQLi
            if ($connection instanceof PDO) {
                $stmt = $connection->prepare("SELECT * FROM posts WHERE status = ? ORDER BY date DESC");
                $stmt->execute(['published']);
                return $stmt->fetchAll();
            } else {
                // MySQLi
                $stmt = $connection->prepare("SELECT * FROM posts WHERE status = ? ORDER BY date DESC");
                $stmt->bind_param('s', 'published');
                $stmt->execute();
                $result = $stmt->get_result();
                $posts = [];
                while ($row = $result->fetch_assoc()) {
                    $posts[] = $row;
                }
                return $posts;
            }
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            // Fall back to JSON
        }
    }
    
    // Fall back to JSON storage
    $posts = getAllPosts();
    return array_filter($posts, function($post) {
        return $post['status'] === 'published';
    });
}

/**
 * Get the current featured post (first featured among published)
 */
function getFeaturedPost() {
    // Try to use database if available
    if (isDatabaseAvailable()) {
        try {
            $connection = getDatabaseConnection();
            
            // Check if we're using PDO or MySQLi
            if ($connection instanceof PDO) {
                $stmt = $connection->prepare("SELECT * FROM posts WHERE status = ? AND featured = ? ORDER BY date DESC LIMIT 1");
                $stmt->execute(['published', true]);
                return $stmt->fetch();
            } else {
                // MySQLi
                $stmt = $connection->prepare("SELECT * FROM posts WHERE status = ? AND featured = ? ORDER BY date DESC LIMIT 1");
                $stmt->bind_param('si', 'published', 1);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            // Fall back to JSON
        }
    }
    
    // Fall back to JSON storage
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
    // Try to use database if available
    if (isDatabaseAvailable()) {
        try {
            $connection = getDatabaseConnection();
            
            // Check if we're using PDO or MySQLi
            if ($connection instanceof PDO) {
                // First unfeature all posts
                $stmt = $connection->prepare("UPDATE posts SET featured = ? WHERE featured = ?");
                $stmt->execute([false, true]);
                
                // Then feature the selected post
                $stmt = $connection->prepare("UPDATE posts SET featured = ? WHERE id = ?");
                $stmt->execute([true, $id]);
            } else {
                // MySQLi
                // First unfeature all posts
                $connection->query("UPDATE posts SET featured = 0 WHERE featured = 1");
                
                // Then feature the selected post
                $stmt = $connection->prepare("UPDATE posts SET featured = 1 WHERE id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            // Fall back to JSON
        }
    }
    
    // Fall back to JSON storage
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
    // Try to use database if available
    if (isDatabaseAvailable()) {
        try {
            $connection = getDatabaseConnection();
            
            // Check if we're using PDO or MySQLi
            if ($connection instanceof PDO) {
                $stmt = $connection->prepare("SELECT * FROM posts WHERE id = ?");
                $stmt->execute([$id]);
                return $stmt->fetch();
            } else {
                // MySQLi
                $stmt = $connection->prepare("SELECT * FROM posts WHERE id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            // Fall back to JSON
        }
    }
    
    // Fall back to JSON storage
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
    // Try to use database if available
    if (isDatabaseAvailable()) {
        try {
            $connection = getDatabaseConnection();
            
            // Check if we're using PDO or MySQLi
            if ($connection instanceof PDO) {
                $stmt = $connection->prepare("
                    INSERT INTO posts (title, content, status, author, date, created_at, featured) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                $date = date('Y-m-d H:i:s');
                $createdAt = date('Y-m-d H:i:s');
                $featured = false;
                
                $stmt->execute([
                    $title,
                    $content,
                    $status,
                    $author ?: 'Onbekend',
                    $date,
                    $createdAt,
                    $featured
                ]);
                
                $postId = $connection->lastInsertId();
                
                // Return the created post
                return getPostById($postId);
            } else {
                // MySQLi
                $stmt = $connection->prepare("
                    INSERT INTO posts (title, content, status, author, date, created_at, featured) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                $date = date('Y-m-d H:i:s');
                $createdAt = date('Y-m-d H:i:s');
                $featured = 0;
                $authorValue = $author ?: 'Onbekend';
                
                $stmt->bind_param('ssssssi', $title, $content, $status, $authorValue, $date, $createdAt, $featured);
                $stmt->execute();
                
                $postId = $connection->insert_id;
                
                // Return the created post
                return getPostById($postId);
            }
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            // Fall back to JSON
        }
    }
    
    // Fall back to JSON storage
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
        'created_at' => time(),
        'updated_at' => time()
    ];
    
    array_unshift($posts, $newPost); // Add to beginning for latest first
    savePosts($posts);
    
    return $newPost;
}

/**
 * Update existing post
 */
function updatePost($id, $title, $content, $status, $author = null) {
    // Try to use database if available
    if (isDatabaseAvailable()) {
        try {
            $connection = getDatabaseConnection();
            
            // Get existing post to preserve author if not provided
            $existingPost = getPostById($id);
            if (!$existingPost) {
                return false;
            }
            
            $authorValue = $author;
            if ($authorValue === null || $authorValue === '') {
                $authorValue = $existingPost['author'] ?? 'Onbekend';
            }
            
            // Check if we're using PDO or MySQLi
            if ($connection instanceof PDO) {
                $stmt = $connection->prepare("
                    UPDATE posts 
                    SET title = ?, content = ?, status = ?, author = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $title,
                    $content,
                    $status,
                    $authorValue,
                    $id
                ]);
            } else {
                // MySQLi
                $stmt = $connection->prepare("
                    UPDATE posts 
                    SET title = ?, content = ?, status = ?, author = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?
                ");
                
                $stmt->bind_param('ssssi', $title, $content, $status, $authorValue, $id);
                $stmt->execute();
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            // Fall back to JSON
        }
    }
    
    // Fall back to JSON storage
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
            // Don't update the date field - keep the original creation date
            // Update the updated_at timestamp
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
    // Try to use database if available
    if (isDatabaseAvailable()) {
        try {
            $connection = getDatabaseConnection();
            
            // Check if we're using PDO or MySQLi
            if ($connection instanceof PDO) {
                $stmt = $connection->prepare("DELETE FROM posts WHERE id = ?");
                $stmt->execute([$id]);
                return $stmt->rowCount() > 0;
            } else {
                // MySQLi
                $stmt = $connection->prepare("DELETE FROM posts WHERE id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                return $stmt->affected_rows > 0;
            }
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            // Fall back to JSON
        }
    }
    
    // Fall back to JSON storage
    $posts = getAllPosts();
    $posts = array_filter($posts, function($post) use ($id) {
        return $post['id'] != $id;
    });
    
    return savePosts(array_values($posts)); // Re-index array
}

/**
 * Search posts by title
 */
function searchPosts($query) {
    // Try to use database if available
    if (isDatabaseAvailable()) {
        try {
            if (empty($query)) {
                return getPublishedPosts();
            }
            
            $connection = getDatabaseConnection();
            
            // Check if we're using PDO or MySQLi
            if ($connection instanceof PDO) {
                $stmt = $connection->prepare("
                    SELECT * FROM posts 
                    WHERE status = ? AND title LIKE ? 
                    ORDER BY date DESC
                ");
                
                $stmt->execute(['published', '%' . $query . '%']);
                return $stmt->fetchAll();
            } else {
                // MySQLi
                $stmt = $connection->prepare("
                    SELECT * FROM posts 
                    WHERE status = ? AND title LIKE ? 
                    ORDER BY date DESC
                ");
                
                $searchQuery = '%' . $query . '%';
                $stmt->bind_param('ss', 'published', $searchQuery);
                $stmt->execute();
                
                $result = $stmt->get_result();
                $posts = [];
                while ($row = $result->fetch_assoc()) {
                    $posts[] = $row;
                }
                return $posts;
            }
        } catch (Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            // Fall back to JSON
        }
    }
    
    // Fall back to JSON storage
    $posts = getPublishedPosts();
    
    if (empty($query)) {
        return $posts;
    }
    
    return array_filter($posts, function($post) use ($query) {
        return stripos($post['title'], $query) !== false;
    });
}

/**
 * Save posts to JSON file (for fallback)
 */
function savePosts($posts) {
    return file_put_contents(POSTS_FILE, json_encode($posts, JSON_PRETTY_PRINT));
}

/**
 * Format date for display
 */
function formatDate($dateString) {
    // Handle numeric timestamps (convert to string format first)
    if (is_numeric($dateString)) {
        $dateString = date('Y-m-d H:i:s', $dateString);
    }
    
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
    $allowedTags = '<p><br><strong><em><ul><ol><li><a><blockquote><code><pre><h2><h3><h4><hr>'; 
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