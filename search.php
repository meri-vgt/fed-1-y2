<?php
/**
 * Criminal Minds Blog - Search Page
 */

require_once 'includes/functions.php';

$pageTitle = 'Zoeken';
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if ($query) {
    $results = searchPosts($query);
    $pageTitle = 'Zoekresultaten voor "' . htmlspecialchars($query) . '"';
}

include 'includes/header.php';
?>

<!-- Search Header -->
<section class="glass-card text-center">
    <h1 style="color: #dc2626; margin-bottom: 1rem;">Doorzoek Misdaadverslagen</h1>
    <p>Zoek naar specifieke zaken, locaties, of criminaliteitstypen in ons archief.</p>
    
    <!-- Enhanced Search Form -->
    <form action="<?php echo $baseUrl; ?>/search.php" method="GET" style="margin-top: 2rem; max-width: 500px; margin-left: auto; margin-right: auto;">
        <div style="display: flex; gap: 1rem;">
            <input 
                type="text" 
                name="q" 
                placeholder="Bijv. drugs, Amsterdam, witwassen..." 
                class="form-input" 
                value="<?php echo htmlspecialchars($query); ?>"
                style="flex: 1; font-size: 1.1rem; padding: 1rem;"
            >
            <button type="submit" class="btn btn-primary" style="padding: 1rem 2rem;">
                🔍 Zoeken
            </button>
        </div>
    </form>
</section>

<?php if ($query): ?>
    <?php if (!empty($results)): ?>
        <!-- Search Results -->
        <section>
            <h2 style="color: #dc2626; margin-bottom: 2rem;">
                <?php echo count($results); ?> resultaten gevonden voor "<?php echo htmlspecialchars($query); ?>"
            </h2>
            
            <div class="posts-grid">
                <?php foreach ($results as $post): ?>
                    <a href="<?php echo $baseUrl; ?>/post.php?id=<?php echo $post['id']; ?>" class="post-preview">
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <div class="post-meta">
                            <span>📅 <?php echo formatDate($post['date']); ?></span>
                            <span>🔍 Misdaadverslag</span>
                        </div>
                        <div class="post-excerpt">
                            <?php 
                            // Highlight search terms in excerpt
                            $excerpt = truncateText($post['content'], 150);
                            $highlighted = str_ireplace($query, '<mark style="background: rgba(220, 38, 38, 0.3); color: #dc2626;">' . $query . '</mark>', $excerpt);
                            echo $highlighted; 
                            ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php else: ?>
        <!-- No Results -->
        <section class="glass-card text-center">
            <h2>Geen resultaten gevonden</h2>
            <p>Er zijn geen verslagen gevonden die overeenkomen met "<?php echo htmlspecialchars($query); ?>".</p>
            
            <div style="margin-top: 2rem;">
                <h3 style="color: #dc2626; margin-bottom: 1rem;">Zoektips:</h3>
                <ul style="text-align: left; max-width: 400px; margin: 0 auto;">
                    <li>Controleer je spelling</li>
                    <li>Gebruik andere zoektermen</li>
                    <li>Probeer meer algemene termen</li>
                    <li>Zoek op locatie (Amsterdam, Rotterdam, etc.)</li>
                    <li>Zoek op criminaliteitstype (drugs, witwassen, etc.)</li>
                </ul>
            </div>
            
            <div style="margin-top: 2rem;">
                <a href="<?php echo $baseUrl; ?>/" class="btn btn-primary">Bekijk Alle Verslagen</a>
            </div>
        </section>
    <?php endif; ?>
<?php else: ?>
    <!-- Search Suggestions -->
    <section>
        <h2 style="color: #dc2626; margin-bottom: 2rem; text-align: center;">Populaire Zoektermen</h2>
        
        <div style="text-align: center; margin-bottom: 3rem;">
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
                <?php
                $popularTerms = ['drugs', 'Amsterdam', 'Rotterdam', 'witwassen', 'mensenhandel', 'cocaïne', 'politie', 'arrestatie'];
                foreach ($popularTerms as $term):
                ?>
                    <a href="<?php echo $baseUrl; ?>/search.php?q=<?php echo urlencode($term); ?>" class="btn btn-secondary">
                        <?php echo htmlspecialchars($term); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Recent Posts as Browse Option -->
        <?php
        $recentPosts = array_slice(getPublishedPosts(), 0, 6);
        if (!empty($recentPosts)):
        ?>
        <h3 style="color: #dc2626; margin-bottom: 2rem; text-align: center;">Of Blader Door Recente Verslagen</h3>
        
        <div class="posts-grid">
            <?php foreach ($recentPosts as $post): ?>
                <a href="<?php echo $baseUrl; ?>/post.php?id=<?php echo $post['id']; ?>" class="post-preview">
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <div class="post-meta">
                        <span>📅 <?php echo formatDate($post['date']); ?></span>
                    </div>
                    <div class="post-excerpt">
                        <?php echo truncateText($post['content'], 120); ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>