<?php
require_once 'config.php';

$postId = $_GET['id'] ?? '';
$post = null;
$error = '';

if ($postId) {
    $post = getPostById($postId);
    if (!$post) {
        $error = 'Post not found.';
    } elseif ($post['status'] !== 'published') {
        $error = 'This post is not available for viewing.';
    }
} else {
    $error = 'No post ID specified.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $post ? htmlspecialchars($post['title']) . ' - ' . SITE_TITLE : 'Post Not Found - ' . SITE_TITLE ?></title>
    <meta name="description" content="<?= SITE_DESCRIPTION ?>">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo"><?= SITE_TITLE ?></a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="search.php">Search</a></li>
                <li><a href="admin/">Admin</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <?php if ($error): ?>
            <div class="alert alert-error">
                <h2>Error</h2>
                <p><?= htmlspecialchars($error) ?></p>
                <p><a href="index.php" class="btn">Return to Homepage</a></p>
            </div>
        <?php else: ?>
            <!-- Post Content -->
            <article class="blog-post fade-in">
                <div style="margin-bottom: 1rem;">
                    <a href="index.php" class="btn btn-secondary">← Back to Home</a>
                </div>
                
                <h1 class="post-title" style="font-size: 2.2rem; margin-bottom: 1rem;">
                    <?= htmlspecialchars($post['title']) ?>
                </h1>
                
                <div class="post-meta" style="margin-bottom: 2rem; font-size: 1rem;">
                    <span>Published on <?= formatDate($post['date']) ?></span>
                </div>
                
                <div class="post-content danger-border" style="font-size: 1.1rem; line-height: 1.8;">
                    <?= $post['content'] ?>
                </div>
            </article>

            <!-- Related Posts -->
            <?php
            $allPosts = getPublishedPosts();
            $relatedPosts = array_filter($allPosts, function($p) use ($post) {
                return $p['id'] !== $post['id'];
            });
            usort($relatedPosts, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            $relatedPosts = array_slice($relatedPosts, 0, 3);
            ?>

            <?php if (!empty($relatedPosts)): ?>
                <div style="margin-top: 3rem;">
                    <h3 style="color: #dc2626; margin-bottom: 1.5rem; font-size: 1.5rem;">Other Recent Reports</h3>
                    <div class="posts-grid">
                        <?php foreach ($relatedPosts as $relatedPost): ?>
                            <div class="post-preview fade-in" onclick="window.location.href='post.php?id=<?= htmlspecialchars($relatedPost['id']) ?>'">
                                <h4 class="post-title"><?= htmlspecialchars($relatedPost['title']) ?></h4>
                                <div class="post-meta">
                                    <?= formatDate($relatedPost['date']) ?>
                                </div>
                                <div class="post-content">
                                    <?= truncateText($relatedPost['content']) ?>
                                </div>
                                <div style="margin-top: 1rem;">
                                    <span class="btn btn-small">Read More</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        // Add fade-in animation to elements
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = (index * 0.1) + 's';
            });
        });

        // Add click handlers for post previews
        document.querySelectorAll('.post-preview').forEach(preview => {
            preview.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            preview.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>