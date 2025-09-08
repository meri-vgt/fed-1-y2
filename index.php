<?php
/**
 * Criminal Minds Blog - Homepage
 */

require_once 'includes/functions.php';

$pageTitle = 'Home';

// Get published posts
$posts = getPublishedPosts();
$featuredPost = !empty($posts) ? array_shift($posts) : null;
$recentPosts = array_slice($posts, 0, 6); // Show 6 recent posts

// Sample data if no posts exist
if (empty($posts) && !$featuredPost) {
    // Create sample posts for demonstration
    createPost(
        'Grote Drugsbust in Amsterdam: 500kg Cocaïne Onderschept',
        '<p>In een grootschalige operatie heeft de politie vandaag 500 kilogram cocaïne onderschept in de haven van Amsterdam. De drugs waren verstopt in een container met bananen uit Zuid-Amerika.</p>

<p>De operatie, codenaam "Witte Sneeuw", was het resultaat van maanden onderzoek door de Nationale Politie in samenwerking met internationale partners. Drie verdachten zijn aangehouden.</p>

<p><strong>Details van de operatie:</strong></p>
<ul>
<li>Straatwaarde: €37,5 miljoen</li>
<li>Container herkomst: Colombia</li>
<li>Arrestaties: 3 personen</li>
<li>Onderzoeksduur: 8 maanden</li>
</ul>

<p>Dit is een van de grootste drugsvangsten van dit jaar in Nederland. De politie verwacht dat deze inbeslagname een significante impact zal hebben op de lokale drugshandel.</p>',
        'published'
    );
    
    createPost(
        'Witwaspraktijken Ontdekt bij Luxe Autohandel',
        '<p>Onderzoek naar verdachte transacties bij een luxe autohandel in Rotterdam heeft geleid tot de ontdekking van een uitgebreid witwasnnetwerk.</p>

<p>De zaak kwam aan het licht toen meerdere klanten contant grote bedragen betaalden voor dure sportwagens zonder duidelijke inkomstenbron.</p>

<p>Geschatte omvang witwasoperatie: €2,3 miljoen over de afgelopen twee jaar.</p>',
        'published'
    );
    
    createPost(
        'Internationale Mensenhandeloperatie Opgerold',
        '<p>Een internationale operatie heeft geleid tot de arrestatie van 12 verdachten in een uitgebreide mensenhandelnetwerk dat actief was in Nederland, België en Duitsland.</p>

<p>Slachtoffers werden gedwongen tot arbeid in de agrarische sector onder erbarmelijke omstandigheden.</p>',
        'published'
    );
    
    // Refresh data
    $posts = getPublishedPosts();
    $featuredPost = !empty($posts) ? array_shift($posts) : null;
    $recentPosts = array_slice($posts, 0, 6);
}

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <h1>Criminal Minds</h1>
    <p>De laatste misdaadverslagen en onderzoeken uit Nederland en daarbuiten. Ontdek de verhalen achter de criminaliteit.</p>
</section>

<?php if ($featuredPost): ?>
<!-- Featured Post -->
<article class="featured-post">
    <span class="featured-badge">Uitgelicht</span>
    <h2 class="post-title"><?php echo htmlspecialchars($featuredPost['title']); ?></h2>
    <div class="post-meta">
        <span>📅 <?php echo formatDate($featuredPost['date']); ?></span>
        <span>🔍 Misdaadverslag</span>
    </div>
    <div class="post-content">
        <?php echo $featuredPost['content']; ?>
    </div>
    <a href="/post.php?id=<?php echo $featuredPost['id']; ?>" class="btn btn-primary mt-2">Lees Volledig Artikel</a>
</article>
<?php endif; ?>

<?php if (!empty($recentPosts)): ?>
<!-- Recent Posts Grid -->
<section>
    <h2 style="color: #dc2626; margin-bottom: 2rem; text-align: center; font-size: 2rem;">Recente Misdaadverslagen</h2>
    
    <div class="posts-grid">
        <?php foreach ($recentPosts as $post): ?>
            <a href="/post.php?id=<?php echo $post['id']; ?>" class="post-preview">
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
</section>
<?php else: ?>
<!-- No Posts Message -->
<div class="glass-card text-center">
    <h2>Geen Verslagen Beschikbaar</h2>
    <p>Er zijn momenteel geen gepubliceerde misdaadverslagen beschikbaar.</p>
    <a href="/admin/" class="btn btn-primary">Voeg Eerste Verslag Toe</a>
</div>
<?php endif; ?>

<!-- Call to Action -->
<section class="glass-card text-center" style="margin-top: 3rem;">
    <h2 style="color: #dc2626; margin-bottom: 1rem;">Blijf Op De Hoogte</h2>
    <p>Volg de laatste ontwikkelingen in misdaadonderzoeken en politieoperaties.</p>
    <div style="margin-top: 2rem;">
        <a href="/admin/" class="btn btn-primary">Publiceer Een Verslag</a>
        <a href="/search.php" class="btn btn-secondary">Doorzoek Archief</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>