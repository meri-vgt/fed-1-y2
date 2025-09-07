<?php
require_once '../config.php';

// Get all posts (including drafts) for admin view
$posts = loadPosts();
usort($posts, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?= SITE_TITLE ?></title>
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
        <!-- Admin Header -->
        <div class="admin-header">
            <div>
                <h1 class="page-title">Admin Panel</h1>
                <p class="page-subtitle">Manage crime reports and blog content</p>
            </div>
            <div>
                <a href="create.php" class="btn">Create New Report</a>
            </div>
        </div>

        <!-- Posts Management -->
        <?php if (empty($posts)): ?>
            <div class="alert alert-error">
                <h3>No Reports Created Yet</h3>
                <p>Start by creating your first crime report.</p>
                <a href="create.php" class="btn">Create First Report</a>
            </div>
        <?php else: ?>
            <div class="posts-table-container">
                <table class="posts-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($post['title']) ?></strong>
                                    <br>
                                    <small style="color: #888;">
                                        <?= truncateText($post['content'], 80) ?>
                                    </small>
                                </td>
                                <td><?= formatDate($post['date']) ?></td>
                                <td>
                                    <span class="status-<?= $post['status'] ?>">
                                        <?= ucfirst($post['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <?php if ($post['status'] === 'published'): ?>
                                            <a href="../post.php?id=<?= htmlspecialchars($post['id']) ?>" 
                                               class="btn btn-small btn-secondary" target="_blank">View</a>
                                        <?php endif; ?>
                                        <a href="edit.php?id=<?= htmlspecialchars($post['id']) ?>" 
                                           class="btn btn-small">Edit</a>
                                        <a href="delete.php?id=<?= htmlspecialchars($post['id']) ?>" 
                                           class="btn btn-small btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Statistics -->
            <div style="margin-top: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <?php
                $publishedCount = count(array_filter($posts, fn($p) => $p['status'] === 'published'));
                $draftCount = count(array_filter($posts, fn($p) => $p['status'] === 'draft'));
                $totalCount = count($posts);
                ?>
                
                <div class="blog-post" style="text-align: center; padding: 1.5rem;">
                    <h3 style="color: #10b981; margin-bottom: 0.5rem;"><?= $publishedCount ?></h3>
                    <p>Published Reports</p>
                </div>
                
                <div class="blog-post" style="text-align: center; padding: 1.5rem;">
                    <h3 style="color: #f59e0b; margin-bottom: 0.5rem;"><?= $draftCount ?></h3>
                    <p>Draft Reports</p>
                </div>
                
                <div class="blog-post" style="text-align: center; padding: 1.5rem;">
                    <h3 style="color: #dc2626; margin-bottom: 0.5rem;"><?= $totalCount ?></h3>
                    <p>Total Reports</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Add confirmation for delete actions
        document.querySelectorAll('.btn-danger').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this crime report? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });

        // Add fade-in animation
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.querySelector('.posts-table');
            if (table) {
                table.style.opacity = '0';
                table.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    table.style.transition = 'all 0.5s ease';
                    table.style.opacity = '1';
                    table.style.transform = 'translateY(0)';
                }, 100);
            }
        });
    </script>
</body>
</html>