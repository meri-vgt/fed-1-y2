<?php
require_once '../config.php';

$postId = $_GET['id'] ?? '';
$post = null;
$message = '';
$messageType = '';
$error = '';

// Load post for editing
if ($postId) {
    $post = getPostById($postId);
    if (!$post) {
        $error = 'Post not found.';
    }
} else {
    $error = 'No post ID specified.';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $post) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    
    // Validate input
    if (empty($title)) {
        $message = 'Title is required.';
        $messageType = 'error';
    } elseif (empty($content)) {
        $message = 'Content is required.';
        $messageType = 'error';
    } else {
        // Update post
        $posts = loadPosts();
        foreach ($posts as &$p) {
            if ($p['id'] === $post['id']) {
                $p['title'] = $title;
                $p['content'] = $content;
                $p['status'] = $status;
                // Keep original date, add modified date if needed
                if (!isset($p['modified'])) {
                    $p['modified'] = date('Y-m-d H:i:s');
                } else {
                    $p['modified'] = date('Y-m-d H:i:s');
                }
                break;
            }
        }
        
        if (savePosts($posts)) {
            $message = 'Crime report updated successfully!';
            $messageType = 'success';
            
            // Reload the post data
            $post = getPostById($postId);
            
            // Redirect after successful update
            if ($status === 'published') {
                header('Location: ../post.php?id=' . $post['id']);
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $message = 'Error updating the crime report. Please try again.';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Report - Admin - <?= SITE_TITLE ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo"><?= SITE_TITLE ?></a>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="../search.php">Search</a></li>
                <li><a href="index.php">Admin</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Edit Crime Report</h1>
            <p class="page-subtitle">Modify existing crime incident report</p>
        </div>

        <!-- Back Button -->
        <div style="margin-bottom: 2rem;">
            <a href="index.php" class="btn btn-secondary">← Back to Admin Panel</a>
            <?php if ($post && $post['status'] === 'published'): ?>
                <a href="../post.php?id=<?= htmlspecialchars($post['id']) ?>" class="btn btn-secondary" target="_blank">View Live Post</a>
            <?php endif; ?>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <h2>Error</h2>
                <p><?= htmlspecialchars($error) ?></p>
                <p><a href="index.php" class="btn">Return to Admin Panel</a></p>
            </div>
        <?php else: ?>
            <!-- Message Display -->
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Edit Form -->
            <div class="blog-post">
                <div style="margin-bottom: 1rem; padding: 1rem; background-color: #333; border-radius: 4px;">
                    <strong>Report Info:</strong><br>
                    <span style="color: #888;">
                        Created: <?= formatDate($post['date']) ?>
                        <?php if (isset($post['modified'])): ?>
                            | Last modified: <?= formatDate($post['modified']) ?>
                        <?php endif; ?>
                        | Status: <span class="status-<?= $post['status'] ?>"><?= ucfirst($post['status']) ?></span>
                    </span>
                </div>

                <form method="POST" action="edit.php?id=<?= htmlspecialchars($postId) ?>" id="editForm">
                    <div class="form-group">
                        <label for="title" class="form-label">Report Title *</label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            class="form-input" 
                            placeholder="e.g., Drug Bust in Downtown District"
                            value="<?= htmlspecialchars($_POST['title'] ?? $post['title']) ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="content" class="form-label">Report Content *</label>
                        <textarea 
                            id="content" 
                            name="content" 
                            class="form-textarea" 
                            placeholder="Enter the full crime report details here. You can use basic HTML tags for formatting."
                            required
                        ><?= htmlspecialchars($_POST['content'] ?? $post['content']) ?></textarea>
                        <small style="color: #888; display: block; margin-top: 0.5rem;">
                            Tip: You can use HTML tags for formatting: &lt;p&gt; for paragraphs, &lt;strong&gt; for bold, &lt;em&gt; for italic, &lt;br&gt; for line breaks.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Publication Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="draft" <?= ($_POST['status'] ?? $post['status']) === 'draft' ? 'selected' : '' ?>>
                                Save as Draft (Preview only)
                            </option>
                            <option value="published" <?= ($_POST['status'] ?? $post['status']) === 'published' ? 'selected' : '' ?>>
                                Publish (Visible to public)
                            </option>
                        </select>
                    </div>

                    <div class="form-group" style="display: flex; gap: 1rem; justify-content: flex-end;">
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                        <a href="delete.php?id=<?= htmlspecialchars($postId) ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                        <button type="submit" class="btn" id="submitBtn">
                            <span id="submitText">Update Report</span>
                            <span id="loadingText" style="display: none;">Updating...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- HTML Formatting Help -->
            <div class="blog-post" style="margin-top: 2rem;">
                <h3 style="color: #dc2626; margin-bottom: 1rem;">HTML Formatting Guide</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; font-size: 0.9rem;">
                    <div>
                        <strong>Basic Formatting:</strong><br>
                        <code>&lt;p&gt;Paragraph text&lt;/p&gt;</code><br>
                        <code>&lt;strong&gt;Bold text&lt;/strong&gt;</code><br>
                        <code>&lt;em&gt;Italic text&lt;/em&gt;</code><br>
                        <code>&lt;br&gt;</code> - Line break
                    </div>
                    <div>
                        <strong>Lists:</strong><br>
                        <code>&lt;ul&gt;&lt;li&gt;Item 1&lt;/li&gt;&lt;li&gt;Item 2&lt;/li&gt;&lt;/ul&gt;</code><br>
                        <code>&lt;ol&gt;&lt;li&gt;Item 1&lt;/li&gt;&lt;li&gt;Item 2&lt;/li&gt;&lt;/ol&gt;</code>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Form enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loadingText = document.getElementById('loadingText');

            // Form submission handler
            if (form) {
                form.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitText.style.display = 'none';
                    loadingText.style.display = 'inline';
                });

                // Auto-resize textarea
                const textarea = document.getElementById('content');
                function resizeTextarea() {
                    textarea.style.height = 'auto';
                    textarea.style.height = textarea.scrollHeight + 'px';
                }
                
                textarea.addEventListener('input', resizeTextarea);
                // Initial resize
                resizeTextarea();

                // Keyboard shortcuts
                document.addEventListener('keydown', function(e) {
                    // Ctrl+S to save as draft
                    if (e.ctrlKey && e.key === 's') {
                        e.preventDefault();
                        document.getElementById('status').value = 'draft';
                        form.submit();
                    }
                    // Ctrl+Enter to publish
                    if (e.ctrlKey && e.key === 'Enter') {
                        e.preventDefault();
                        document.getElementById('status').value = 'published';
                        form.submit();
                    }
                });
            }
        });

        // Warn about unsaved changes
        let formChanged = false;
        const originalData = {
            title: document.getElementById('title').value,
            content: document.getElementById('content').value,
            status: document.getElementById('status').value
        };

        document.querySelectorAll('input, textarea, select').forEach(input => {
            input.addEventListener('change', function() {
                const currentData = {
                    title: document.getElementById('title').value,
                    content: document.getElementById('content').value,
                    status: document.getElementById('status').value
                };
                
                formChanged = JSON.stringify(currentData) !== JSON.stringify(originalData);
            });
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Reset form changed flag on submit
        const form = document.getElementById('editForm');
        if (form) {
            form.addEventListener('submit', () => formChanged = false);
        }
    </script>
</body>
</html>