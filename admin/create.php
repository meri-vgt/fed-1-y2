<?php
require_once '../config.php';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        // Create new post
        $posts = loadPosts();
        $newPost = [
            'id' => generatePostId(),
            'title' => $title,
            'content' => $content,
            'date' => date('Y-m-d H:i:s'),
            'status' => $status
        ];
        
        $posts[] = $newPost;
        
        if (savePosts($posts)) {
            $message = 'Crime report created successfully!';
            $messageType = 'success';
            
            // Redirect after successful creation
            if ($status === 'published') {
                header('Location: ../post.php?id=' . $newPost['id']);
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $message = 'Error saving the crime report. Please try again.';
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
    <title>Create New Report - Admin - <?= SITE_TITLE ?></title>
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
            <h1 class="page-title">Create New Crime Report</h1>
            <p class="page-subtitle">Document and publish crime incidents</p>
        </div>

        <!-- Back Button -->
        <div style="margin-bottom: 2rem;">
            <a href="index.php" class="btn btn-secondary">← Back to Admin Panel</a>
        </div>

        <!-- Message Display -->
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Create Form -->
        <div class="blog-post">
            <form method="POST" action="create.php" id="createForm">
                <div class="form-group">
                    <label for="title" class="form-label">Report Title *</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-input" 
                        placeholder="e.g., Drug Bust in Downtown District"
                        value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="content" class="form-label">Report Content *</label>
                    <textarea 
                        id="content" 
                        name="content" 
                        class="form-textarea" 
                        placeholder="Enter the full crime report details here. You can use basic HTML tags for formatting like &lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, etc."
                        required
                    ><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                    <small style="color: #888; display: block; margin-top: 0.5rem;">
                        Tip: You can use HTML tags for formatting: &lt;p&gt; for paragraphs, &lt;strong&gt; for bold, &lt;em&gt; for italic, &lt;br&gt; for line breaks.
                    </small>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Publication Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="draft" <?= ($_POST['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>
                            Save as Draft (Preview only)
                        </option>
                        <option value="published" <?= ($_POST['status'] ?? '') === 'published' ? 'selected' : '' ?>>
                            Publish Immediately (Visible to public)
                        </option>
                    </select>
                </div>

                <div class="form-group" style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn" id="submitBtn">
                        <span id="submitText">Create Report</span>
                        <span id="loadingText" style="display: none;">Creating...</span>
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
    </div>

    <script>
        // Form enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loadingText = document.getElementById('loadingText');

            // Auto-focus title input
            document.getElementById('title').focus();

            // Form submission handler
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitText.style.display = 'none';
                loadingText.style.display = 'inline';
            });

            // Auto-resize textarea
            const textarea = document.getElementById('content');
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });

            // Preview functionality for content
            let previewTimeout;
            textarea.addEventListener('input', function() {
                clearTimeout(previewTimeout);
                previewTimeout = setTimeout(() => {
                    // Could add live preview here if needed
                }, 500);
            });

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
        });

        // Warn about unsaved changes
        let formChanged = false;
        document.querySelectorAll('input, textarea, select').forEach(input => {
            input.addEventListener('change', () => formChanged = true);
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Reset form changed flag on submit
        document.getElementById('createForm').addEventListener('submit', () => formChanged = false);
    </script>
</body>
</html>