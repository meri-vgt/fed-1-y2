<?php
/**
 * Criminal Minds Blog - Individual Post View
 */

require_once 'includes/functions.php';

// Get post ID from URL
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$postId) {
    header('Location: ' . getBaseUrl() . '/');
    exit;
}

// Get the post
$post = getPostById($postId);

if (!$post) {
    header('Location: ' . getBaseUrl() . '/');
    exit;
}

// Only show published posts (unless coming from admin)
if ($post['status'] !== 'published' && !isset($_GET['preview'])) {
    header('Location: ' . getBaseUrl() . '/');
    exit;
}

$pageTitle = $post['title'];

include 'includes/header.php';
?>

<!-- Post Content -->
<article class="glass-card">
    <header class="post-header" style="margin-bottom: 2rem;">
        <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="post-meta" style="font-size: 1rem;">
            <span>📅 <?php echo formatDate($post['date']); ?></span>
            <span>🔍 Misdaadverslag</span>
            <?php if ($post['status'] === 'draft'): ?>
                <span class="status-badge status-draft">Concept</span>
            <?php endif; ?>
        </div>
    </header>
    
    <div class="post-content" style="font-size: 1.1rem; line-height: 1.8;">
        <?php echo $post['content']; ?>
    </div>
    
    <footer class="post-footer" style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <a href="<?php echo getBaseUrl(); ?>/" class="btn btn-secondary">← Terug naar Home</a>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="<?php echo getBaseUrl(); ?>/admin/edit.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary">Bewerk Artikel</a>
                <a href="<?php echo getBaseUrl(); ?>/search.php" class="btn btn-secondary">Meer Verslagen</a>
            </div>
        </div>
    </footer>
</article>

<!-- Related Posts -->
<?php
$allPosts = getPublishedPosts();
$relatedPosts = array_filter($allPosts, function($p) use ($post) {
    return $p['id'] !== $post['id'];
});
$relatedPosts = array_slice($relatedPosts, 0, 3);

if (!empty($relatedPosts)):
?>
<section style="margin-top: 3rem;">
    <h2 style="color: #dc2626; margin-bottom: 2rem; text-align: center;">Gerelateerde Verslagen</h2>
    
    <div class="posts-grid">
        <?php foreach ($relatedPosts as $relatedPost): ?>
            <a href="<?php echo getBaseUrl(); ?>/post.php?id=<?php echo $relatedPost['id']; ?>" class="post-preview">
                <h3><?php echo htmlspecialchars($relatedPost['title']); ?></h3>
                <div class="post-meta">
                    <span>📅 <?php echo formatDate($relatedPost['date']); ?></span>
                </div>
                <div class="post-excerpt">
                    <?php echo truncateText($relatedPost['content'], 100); ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>