<?php
require_once '../config.php';

$postId = $_GET['id'] ?? '';
$post = null;
$error = '';
$success = false;

// Load post for deletion
if ($postId) {
    $post = getPostById($postId);
    if (!$post) {
        $error = 'Post not found.';
    }
} else {
    $error = 'No post ID specified.';
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $post && isset($_POST['confirm_delete'])) {
    $posts = loadPosts();
    $posts = array_filter($posts, function($p) use ($post) {
        return $p['id'] !== $post['id'];
    });
    
    if (savePosts(array_values($posts))) {
        $success = true;
        // Redirect after successful deletion
        header('Location: index.php?deleted=1');
        exit;
    } else {
        $error = 'Error deleting the crime report. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Report - Admin - <?= SITE_TITLE ?></title>
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
            <h1 class="page-title">Delete Crime Report</h1>
            <p class="page-subtitle">Permanently remove crime incident report</p>
        </div>

        <!-- Back Button -->
        <div style="margin-bottom: 2rem;">
            <a href="index.php" class="btn btn-secondary">← Back to Admin Panel</a>
            <?php if ($post): ?>
                <a href="edit.php?id=<?= htmlspecialchars($post['id']) ?>" class="btn btn-secondary">Edit Instead</a>
            <?php endif; ?>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <h2>Error</h2>
                <p><?= htmlspecialchars($error) ?></p>
                <p><a href="index.php" class="btn">Return to Admin Panel</a></p>
            </div>
        <?php elseif ($success): ?>
            <div class="alert alert-success">
                <h2>Report Deleted Successfully</h2>
                <p>The crime report has been permanently deleted.</p>
                <p><a href="index.php" class="btn">Return to Admin Panel</a></p>
            </div>
        <?php else: ?>
            <!-- Deletion Confirmation -->
            <div class="alert alert-error">
                <h2>⚠️ Warning: Permanent Deletion</h2>
                <p><strong>You are about to permanently delete this crime report. This action cannot be undone.</strong></p>
            </div>

            <!-- Post Preview -->
            <div class="blog-post">
                <h3 style="color: #dc2626; margin-bottom: 1rem;">Report to be deleted:</h3>
                
                <div style="margin-bottom: 1rem; padding: 1rem; background-color: #333; border-radius: 4px;">
                    <strong>Report Details:</strong><br>
                    <span style="color: #888;">
                        Created: <?= formatDate($post['date']) ?>
                        <?php if (isset($post['modified'])): ?>
                            | Last modified: <?= formatDate($post['modified']) ?>
                        <?php endif; ?>
                        | Status: <span class="status-<?= $post['status'] ?>"><?= ucfirst($post['status']) ?></span>
                    </span>
                </div>

                <h4 style="color: #e0e0e0; margin-bottom: 0.5rem;">
                    <?= htmlspecialchars($post['title']) ?>
                </h4>
                
                <div style="margin-bottom: 1.5rem; padding: 1rem; background-color: #111; border-radius: 4px; border-left: 4px solid #dc2626;">
                    <?= truncateText($post['content'], 300) ?>
                    <?php if (strlen(strip_tags($post['content'])) > 300): ?>
                        <br><em style="color: #888;">...and more content</em>
                    <?php endif; ?>
                </div>

                <!-- Deletion Form -->
                <form method="POST" action="delete.php?id=<?= htmlspecialchars($postId) ?>" id="deleteForm" style="border-top: 2px solid #dc2626; padding-top: 1.5rem;">
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; color: #e0e0e0; cursor: pointer;">
                            <input type="checkbox" id="confirmCheckbox" style="margin-right: 0.5rem; transform: scale(1.2);" required>
                            I understand that this action is permanent and cannot be undone
                        </label>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                        <a href="edit.php?id=<?= htmlspecialchars($postId) ?>" class="btn">Edit Instead</a>
                        <button type="submit" name="confirm_delete" value="1" class="btn btn-danger" id="deleteBtn" disabled>
                            <span id="deleteText">Delete Permanently</span>
                            <span id="deletingText" style="display: none;">Deleting...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Alternative Actions -->
            <div class="blog-post" style="margin-top: 2rem; background-color: #1a1a2e;">
                <h3 style="color: #10b981; margin-bottom: 1rem;">Alternative Actions</h3>
                <p style="margin-bottom: 1rem;">Instead of deleting, you might want to:</p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="edit.php?id=<?= htmlspecialchars($post['id']) ?>" class="btn btn-secondary">Edit the Report</a>
                    <?php if ($post['status'] === 'published'): ?>
                        <form method="POST" action="edit.php?id=<?= htmlspecialchars($post['id']) ?>" style="display: inline;">
                            <input type="hidden" name="title" value="<?= htmlspecialchars($post['title']) ?>">
                            <input type="hidden" name="content" value="<?= htmlspecialchars($post['content']) ?>">
                            <input type="hidden" name="status" value="draft">
                            <button type="submit" class="btn" onclick="return confirm('Change this report to draft status?')">
                                Convert to Draft
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const confirmCheckbox = document.getElementById('confirmCheckbox');
            const deleteBtn = document.getElementById('deleteBtn');
            const deleteForm = document.getElementById('deleteForm');
            const deleteText = document.getElementById('deleteText');
            const deletingText = document.getElementById('deletingText');

            // Enable/disable delete button based on checkbox
            if (confirmCheckbox && deleteBtn) {
                confirmCheckbox.addEventListener('change', function() {
                    deleteBtn.disabled = !this.checked;
                    if (this.checked) {
                        deleteBtn.style.opacity = '1';
                    } else {
                        deleteBtn.style.opacity = '0.5';
                    }
                });
            }

            // Form submission handler
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    if (!confirmCheckbox.checked) {
                        e.preventDefault();
                        alert('Please confirm that you understand this action is permanent.');
                        return false;
                    }

                    // Final confirmation
                    if (!confirm('Are you absolutely sure you want to delete this crime report? This action CANNOT be undone.')) {
                        e.preventDefault();
                        return false;
                    }

                    // Show loading state
                    deleteBtn.disabled = true;
                    deleteText.style.display = 'none';
                    deletingText.style.display = 'inline';
                });
            }

            // Prevent accidental navigation
            window.addEventListener('beforeunload', function(e) {
                if (confirmCheckbox && confirmCheckbox.checked) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        });

        // Add warning styling
        document.querySelectorAll('.btn-danger').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 0 20px rgba(220, 38, 38, 0.6)';
            });
            btn.addEventListener('mouseleave', function() {
                this.style.boxShadow = '0 4px 10px rgba(220, 38, 38, 0.4)';
            });
        });
    </script>
</body>
</html>