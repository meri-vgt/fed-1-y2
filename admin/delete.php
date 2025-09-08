<?php
/**
 * Criminal Minds Blog - Delete Post
 */

require_once '../includes/functions.php';

// Get post ID
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$postId) {
    header('Location: ' . getBaseUrl() . '/admin/');
    exit;
}

// Get the post to verify it exists
$post = getPostById($postId);

if (!$post) {
    header('Location: ' . getBaseUrl() . '/admin/');
    exit;
}

// Handle the deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    deletePost($postId);
    header('Location: ' . getBaseUrl() . '/admin/?deleted=1');
    exit;
}

$pageTitle = 'Verslag Verwijderen';

include '../includes/header.php';
?>

<!-- Delete Confirmation -->
<section class="admin-header" style="background: rgba(220, 38, 38, 0.1); border-color: rgba(220, 38, 38, 0.3);">
    <h1>🗑️ Verslag Verwijderen</h1>
    <p>Je staat op het punt een verslag definitief te verwijderen</p>
</section>

<!-- Breadcrumb -->
<nav style="margin-bottom: 2rem;">
    <a href="<?php echo getBaseUrl(); ?>/admin/" class="btn btn-secondary">← Terug naar Admin</a>
</nav>

<!-- Confirmation Form -->
<section class="glass-card" style="border-color: rgba(220, 38, 38, 0.3);">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h2 style="color: #dc2626; margin-bottom: 1rem;">⚠️ Waarschuwing</h2>
        <p style="font-size: 1.1rem; margin-bottom: 2rem;">
            Je staat op het punt het volgende verslag <strong>definitief</strong> te verwijderen:
        </p>
    </div>
    
    <!-- Post Preview -->
    <div style="background: rgba(255, 255, 255, 0.03); padding: 2rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(220, 38, 38, 0.2);">
        <h3 style="color: #e4e4e7; margin-bottom: 1rem;"><?php echo htmlspecialchars($post['title']); ?></h3>
        
        <div class="post-meta" style="margin-bottom: 1rem;">
            <span>📅 <?php echo formatDate($post['date']); ?></span>
            <span class="status-badge status-<?php echo $post['status']; ?>">
                <?php echo $post['status'] === 'published' ? 'Gepubliceerd' : 'Concept'; ?>
            </span>
        </div>
        
        <div style="color: #a1a1aa; line-height: 1.6;">
            <?php echo truncateText($post['content'], 200); ?>
        </div>
    </div>
    
    <div style="background: rgba(220, 38, 38, 0.1); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid rgba(220, 38, 38, 0.3);">
        <h4 style="color: #dc2626; margin-bottom: 1rem;">❗ Let Op:</h4>
        <ul style="color: #dc2626; margin-left: 1rem; line-height: 1.6;">
            <li>Deze actie kan <strong>niet</strong> ongedaan worden gemaakt</li>
            <li>Het verslag wordt permanent verwijderd uit de database</li>
            <li>Alle links naar dit verslag zullen niet meer werken</li>
            <li>Er wordt geen backup bewaard</li>
        </ul>
    </div>
    
    <form action="<?php echo getBaseUrl(); ?>/admin/delete.php?id=<?php echo $postId; ?>" method="POST" style="text-align: center;">
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <button type="submit" name="confirm_delete" class="btn btn-danger" style="font-size: 1.1rem; padding: 1rem 2rem;">
                🗑️ Ja, Verwijder Definitief
            </button>
            <a href="<?php echo getBaseUrl(); ?>/admin/" class="btn btn-secondary" style="font-size: 1.1rem; padding: 1rem 2rem;">
                ❌ Nee, Annuleren
            </a>
        </div>
        
        <p style="margin-top: 1rem; color: #71717a; font-size: 0.9rem;">
            Door op "Ja, Verwijder Definitief" te klikken bevestig je dat je dit verslag permanent wilt verwijderen.
        </p>
    </form>
</section>

<!-- Alternative Actions -->
<section class="glass-card">
    <h2 style="color: #dc2626; margin-bottom: 1rem;">🤔 Overweeg Deze Alternatieven</h2>
    <p style="margin-bottom: 2rem;">In plaats van verwijderen, zou je deze opties kunnen overwegen:</p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <div style="background: rgba(249, 115, 22, 0.1); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(249, 115, 22, 0.2);">
            <h4 style="color: #f97316; margin-bottom: 1rem;">📝 Bewerken</h4>
            <p style="margin-bottom: 1rem;">Pas de inhoud aan in plaats van het volledig te verwijderen.</p>
            <a href="<?php echo getBaseUrl(); ?>/admin/edit.php?id=<?php echo $postId; ?>" class="btn btn-secondary">
                Bewerk dit Verslag
            </a>
        </div>
        
        <div style="background: rgba(249, 115, 22, 0.1); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(249, 115, 22, 0.2);">
            <h4 style="color: #f97316; margin-bottom: 1rem;">👁️ Concept Maken</h4>
            <p style="margin-bottom: 1rem;">Zet het verslag op 'concept' zodat het niet zichtbaar is voor bezoekers.</p>
            <?php if ($post['status'] === 'published'): ?>
                <form action="<?php echo getBaseUrl(); ?>/admin/edit.php?id=<?php echo $postId; ?>" method="POST" style="display: inline;">
                    <input type="hidden" name="title" value="<?php echo htmlspecialchars($post['title']); ?>">
                    <input type="hidden" name="content" value="<?php echo htmlspecialchars($post['content']); ?>">
                    <input type="hidden" name="status" value="draft">
                    <button type="submit" class="btn btn-secondary">
                        Maak Concept
                    </button>
                </form>
            <?php else: ?>
                <p style="color: #71717a; font-style: italic;">Dit verslag is al een concept</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
// Add extra confirmation for delete action
document.addEventListener('DOMContentLoaded', function() {
    const deleteForm = document.querySelector('form[action*="delete.php"]');
    if (!deleteForm) return;
    
    deleteForm.addEventListener('submit', function(e) {
        const finalConfirm = confirm('LAATSTE WAARSCHUWING: Weet je 100% zeker dat je "<?php echo addslashes($post['title']); ?>" wilt verwijderen? Deze actie kan NIET ongedaan worden gemaakt!');
        if (!finalConfirm) {
            e.preventDefault();
            return;
        }
        const deleteButton = this.querySelector('button[name="confirm_delete"]');
        if (deleteButton) {
            deleteButton.textContent = '⏳ Bezig met verwijderen...';
            // Defer disabling to next tick to avoid interfering with POST name submission
            setTimeout(() => { deleteButton.disabled = true; }, 0);
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>