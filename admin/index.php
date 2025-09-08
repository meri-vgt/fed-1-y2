<?php
/**
 * Criminal Minds Blog - Admin Dashboard
 */

require_once '../includes/functions.php';

$pageTitle = 'Admin Dashboard';

// Get all posts (including drafts)
$allPosts = getAllPosts();
$publishedCount = count(getPublishedPosts());
$draftCount = count($allPosts) - $publishedCount;

include '../includes/header.php';
?>

<!-- Admin Header -->
<section class="admin-header">
    <h1>🔐 Admin Dashboard</h1>
    <p>Beheer alle misdaadverslagen en publicaties</p>
</section>

<!-- Statistics -->
<section class="glass-card">
    <h2 style="color: #dc2626; margin-bottom: 2rem;">Statistieken</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
        <div class="stat-card" style="background: rgba(34, 197, 94, 0.1); padding: 1.5rem; border-radius: 12px; text-align: center; border: 1px solid rgba(34, 197, 94, 0.2);">
            <h3 style="color: #22c55e; font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $publishedCount; ?></h3>
            <p>Gepubliceerde Verslagen</p>
        </div>
        
        <div class="stat-card" style="background: rgba(249, 115, 22, 0.1); padding: 1.5rem; border-radius: 12px; text-align: center; border: 1px solid rgba(249, 115, 22, 0.2);">
            <h3 style="color: #f97316; font-size: 2rem; margin-bottom: 0.5rem;"><?php echo $draftCount; ?></h3>
            <p>Concepten</p>
        </div>
        
        <div class="stat-card" style="background: rgba(220, 38, 38, 0.1); padding: 1.5rem; border-radius: 12px; text-align: center; border: 1px solid rgba(220, 38, 38, 0.2);">
            <h3 style="color: #dc2626; font-size: 2rem; margin-bottom: 0.5rem;"><?php echo count($allPosts); ?></h3>
            <p>Totaal Verslagen</p>
        </div>
    </div>
</section>

<!-- Admin Actions -->
<section class="admin-actions">
    <a href="<?php echo getBaseUrl(); ?>/admin/create.php" class="btn btn-primary">📝 Nieuw Verslag</a>
    <a href="<?php echo getBaseUrl(); ?>/" class="btn btn-secondary">🏠 Terug naar Home</a>
    <a href="<?php echo getBaseUrl(); ?>/search.php" class="btn btn-secondary">🔍 Zoeken</a>
</section>

<!-- Posts Management -->
<section class="glass-card">
    <h2 style="color: #dc2626; margin-bottom: 2rem;">Alle Verslagen Beheren</h2>
    
    <?php if (!empty($allPosts)): ?>
        <div style="overflow-x: auto;">
            <table class="posts-table">
                <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Status</th>
                        <th>Datum</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allPosts as $post): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                                <br>
                                <small style="color: #71717a;">
                                    <?php echo truncateText(strip_tags($post['content']), 60); ?>
                                </small>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $post['status']; ?>">
                                    <?php echo $post['status'] === 'published' ? 'Gepubliceerd' : 'Concept'; ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($post['date']); ?></td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a href="<?php echo getBaseUrl(); ?>/post.php?id=<?php echo $post['id']; ?><?php echo $post['status'] === 'draft' ? '&preview=1' : ''; ?>" 
                                       class="btn btn-secondary" style="padding: 0.5rem; font-size: 0.8rem;">
                                        👁️ Bekijk
                                    </a>
                                    <a href="<?php echo getBaseUrl(); ?>/admin/edit.php?id=<?php echo $post['id']; ?>" 
                                       class="btn btn-secondary" style="padding: 0.5rem; font-size: 0.8rem;">
                                        ✏️ Bewerk
                                    </a>
                                    <button onclick="confirmDelete(<?php echo $post['id']; ?>, '<?php echo addslashes($post['title']); ?>')" 
                                            class="btn btn-danger" style="padding: 0.5rem; font-size: 0.8rem;">
                                        🗑️ Verwijder
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center" style="padding: 3rem;">
            <h3>Geen verslagen gevonden</h3>
            <p>Er zijn nog geen verslagen aangemaakt.</p>
            <a href="<?php echo getBaseUrl(); ?>/admin/create.php" class="btn btn-primary" style="margin-top: 1rem;">
                📝 Maak je eerste verslag
            </a>
        </div>
    <?php endif; ?>
</section>

<!-- Quick Tips -->
<section class="glass-card">
    <h2 style="color: #dc2626; margin-bottom: 2rem;">Admin Tips</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <div>
            <h4 style="color: #e4e4e7; margin-bottom: 1rem;">📝 Nieuw Verslag</h4>
            <p>Klik op "Nieuw Verslag" om een nieuw misdaadverslag toe te voegen. Je kunt het opslaan als concept om later te publiceren.</p>
        </div>
        
        <div>
            <h4 style="color: #e4e4e7; margin-bottom: 1rem;">🔍 Zoeken</h4>
            <p>Gebruik de zoekfunctie om snel specifieke verslagen te vinden in je archief.</p>
        </div>
        
        <div>
            <h4 style="color: #e4e4e7; margin-bottom: 1rem;">✏️ Bewerken</h4>
            <p>Klik op "Bewerk" naast elk verslag om de inhoud aan te passen of de status te wijzigen.</p>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>