<?php
require_once 'config.php';

$query = $_GET['q'] ?? '';
$searchResults = [];

if ($query) {
    $searchResults = searchPosts($query);
    usort($searchResults, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search<?= $query ? ' - ' . htmlspecialchars($query) : '' ?> - <?= SITE_TITLE ?></title>
    <meta name="description" content="Search crime reports and investigations">
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
            <h1 class="page-title">Search Crime Reports</h1>
            <p class="page-subtitle">Find specific crime reports and investigations</p>
        </div>

        <!-- Search Form -->
        <div class="search-container">
            <form method="GET" action="search.php" class="search-form">
                <input 
                    type="text" 
                    name="q" 
                    value="<?= htmlspecialchars($query) ?>" 
                    placeholder="Search by title..." 
                    class="search-input"
                    autofocus
                >
                <button type="submit" class="btn">Search</button>
            </form>
        </div>

        <!-- Search Results -->
        <?php if ($query): ?>
            <div class="search-results">
                <h3 style="color: #dc2626; margin-bottom: 1.5rem;">
                    <?php if (empty($searchResults)): ?>
                        No results found for "<?= htmlspecialchars($query) ?>"
                    <?php else: ?>
                        <?= count($searchResults) ?> result<?= count($searchResults) !== 1 ? 's' : '' ?> found for "<?= htmlspecialchars($query) ?>"
                    <?php endif; ?>
                </h3>

                <?php if (!empty($searchResults)): ?>
                    <div class="posts-grid">
                        <?php foreach ($searchResults as $post): ?>
                            <div class="post-preview fade-in" onclick="window.location.href='post.php?id=<?= htmlspecialchars($post['id']) ?>'">
                                <h4 class="post-title"><?= htmlspecialchars($post['title']) ?></h4>
                                <div class="post-meta">
                                    <?= formatDate($post['date']) ?>
                                </div>
                                <div class="post-content">
                                    <?= truncateText($post['content']) ?>
                                </div>
                                <div style="margin-top: 1rem;">
                                    <span class="btn btn-small">Read Full Report</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-error">
                        <p>No crime reports match your search term. Try using different keywords or browse all reports on the <a href="index.php" style="color: #dc2626;">homepage</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Browse Recent Posts -->
            <?php
            $recentPosts = getPublishedPosts();
            usort($recentPosts, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            $recentPosts = array_slice($recentPosts, 0, 6);
            ?>

            <?php if (!empty($recentPosts)): ?>
                <div>
                    <h3 style="color: #dc2626; margin-bottom: 1.5rem; font-size: 1.5rem;">Recent Crime Reports</h3>
                    <div class="posts-grid">
                        <?php foreach ($recentPosts as $post): ?>
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
            <?php else: ?>
                <div class="alert alert-error">
                    <h3>No Crime Reports Available</h3>
                    <p>There are currently no published crime reports to search. Check back soon for the latest updates.</p>
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

            // Auto-focus search input
            const searchInput = document.querySelector('.search-input');
            if (searchInput && !searchInput.value) {
                searchInput.focus();
            }
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

        // Search form enhancements
        document.querySelector('.search-form').addEventListener('submit', function(e) {
            const input = this.querySelector('.search-input');
            if (!input.value.trim()) {
                e.preventDefault();
                input.focus();
                input.style.borderColor = '#dc2626';
                setTimeout(() => {
                    input.style.borderColor = '#555';
                }, 1500);
            }
        });
    </script>
</body>
</html>