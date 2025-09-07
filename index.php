<?php
require_once 'config.php';

// Get published posts, sorted by date (newest first)
$posts = getPublishedPosts();
usort($posts, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

$latestPost = !empty($posts) ? array_shift($posts) : null;
$otherPosts = $posts;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?></title>
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
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Crime Watch Blog</h1>
            <p class="page-subtitle">Latest crime reports and investigations</p>
        </div>

        <?php if ($latestPost): ?>
            <!-- Latest Post (Full Display) -->
            <article class="blog-post fade-in">
                <h2 class="post-title">
                    <a href="post.php?id=<?= htmlspecialchars($latestPost['id']) ?>" style="text-decoration: none; color: inherit;">
                        <?= htmlspecialchars($latestPost['title']) ?>
                    </a>
                </h2>
                <div class="post-meta">
                    <span>Published on <?= formatDate($latestPost['date']) ?></span>
                </div>
                <div class="post-content danger-border">
                    <?= $latestPost['content'] ?>
                </div>
                <a href="post.php?id=<?= htmlspecialchars($latestPost['id']) ?>" class="btn">Read Full Report</a>
            </article>

            <?php if (!empty($otherPosts)): ?>
                <!-- Other Posts (Previews) -->
                <div style="margin-top: 3rem;">
                    <h3 style="color: #dc2626; margin-bottom: 1.5rem; font-size: 1.5rem;">Other Recent Reports</h3>
                    <div class="posts-grid">
                        <?php foreach ($otherPosts as $post): ?>
                            <div class="post-preview fade-in" onclick="window.location.href='post.php?id=<?= htmlspecialchars($post['id']) ?>'">
                                <h4 class="post-title"><?= htmlspecialchars($post['title']) ?></h4>
                                <div class="post-meta">
                                    <?= formatDate($post['date']) ?>
                                </div>
                                <div class="post-content">
                                    <?= truncateText($post['content']) ?>
                                </div>
                                <div style="margin-top: 1rem;">
                                    <span class="btn btn-small">Read More</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- No Posts Available -->
            <div class="alert alert-error">
                <h3>No Crime Reports Available</h3>
                <p>There are currently no published crime reports. Check back soon for the latest updates.</p>
                <p><a href="admin/" class="btn">Publish Your First Report</a></p>
            </div>
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