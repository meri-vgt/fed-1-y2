<?php
/**
 * Criminal Minds Blog - Single Entry Point with Simple Router
 */

require_once 'includes/functions.php';

$page = isset($_GET['page']) ? strtolower(trim($_GET['page'])) : 'home';
$pageTitle = 'Home';

function renderHomeView() {
    $featuredPost = getFeaturedPost();
    $posts = getPublishedPosts();
    if ($featuredPost) {
        $posts = array_filter($posts, function($p) use ($featuredPost) { return $p['id'] !== $featuredPost['id']; });
    }
    $recentPosts = array_slice($posts, 0, 6);

    if (!$featuredPost && empty($recentPosts)) {
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
        $featuredPost = getFeaturedPost();
        $posts = getPublishedPosts();
        if ($featuredPost) {
            $posts = array_filter($posts, function($p) use ($featuredPost) { return $p['id'] !== $featuredPost['id']; });
        }
        $recentPosts = array_slice($posts, 0, 6);
    }

    include 'includes/header.php';
    ?>
    <section class="hero">
        <h1>Criminal Minds</h1>
        <p>De laatste misdaadverslagen en onderzoeken uit Nederland en daarbuiten. Ontdek de verhalen achter de criminaliteit.</p>
    </section>

    <?php if ($featuredPost): ?>
    <article class="featured-post">
        <span class="featured-badge">Uitgelicht</span>
        <h2 class="post-title"><?php echo htmlspecialchars($featuredPost['title']); ?></h2>
        <div class="post-meta">
            <span>📅 <?php echo formatDate($featuredPost['date']); ?></span>
            <span>✍️ <?php echo htmlspecialchars($featuredPost['author'] ?? 'Onbekend'); ?></span>
            <span>🔍 Misdaadverslag</span>
        </div>
        <div class="post-content">
            <?php 
            // Convert plain text to HTML if needed before sanitizing
            $featuredContent = $featuredPost['content'];
            // Check if content has HTML block elements
            $hasHtmlBlocks = preg_match('/<(p|div|h[1-6]|ul|ol|blockquote|pre)(\s|>)/i', $featuredContent);
            
            if (!$hasHtmlBlocks && !empty(trim($featuredContent))) {
                $featuredContent = textToHtml($featuredContent);
            }
            
            echo sanitizeHtml($featuredContent); 
            ?>
        </div>
        <a href="<?php echo getBaseUrl(); ?>/?page=post&id=<?php echo $featuredPost['id']; ?>" class="btn btn-primary mt-2">Lees Volledig Artikel</a>
    </article>
    <?php endif; ?>

    <?php if (!empty($recentPosts)): ?>
    <section>
        <h2 style="color: #dc2626; margin-bottom: 2rem; text-align: center; font-size: 2rem;">Recente Misdaadverslagen</h2>
        <div class="posts-grid">
            <?php foreach ($recentPosts as $post): ?>
                <a href="<?php echo getBaseUrl(); ?>/?page=post&id=<?php echo $post['id']; ?>" class="post-preview">
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <div class="post-meta">
                        <span>📅 <?php echo formatDate($post['date']); ?></span>
                        <span>✍️ <?php echo htmlspecialchars($post['author'] ?? 'Onbekend'); ?></span>
                    </div>
                    <div class="post-excerpt">
                        <?php 
                        // Convert plain text to HTML if needed before truncating and sanitizing
                        $excerptContent = $post['content'];
                        // Check if content has HTML block elements
                        $hasHtmlBlocks = preg_match('/<(p|div|h[1-6]|ul|ol|blockquote|pre)(\s|>)/i', $excerptContent);
                        
                        if (!$hasHtmlBlocks && !empty(trim($excerptContent))) {
                            $excerptContent = textToHtml($excerptContent);
                        }
                        
                        echo truncateText(sanitizeHtml($excerptContent), 120); 
                        ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php else: ?>
    <div class="glass-card text-center">
        <h2>Geen Verslagen Beschikbaar</h2>
        <p>Er zijn momenteel geen gepubliceerde misdaadverslagen beschikbaar.</p>
        <a href="<?php echo getBaseUrl(); ?>/admin/" class="btn btn-primary">Voeg Eerste Verslag Toe</a>
    </div>
    <?php endif; ?>

    <section class="glass-card text-center" style="margin-top: 3rem;">
        <h2 style="color: #dc2626; margin-bottom: 1rem;">Blijf Op De Hoogte</h2>
        <p>Volg de laatste ontwikkelingen in misdaadonderzoeken en politieoperaties.</p>
        <div style="margin-top: 2rem;">
            <a href="<?php echo getBaseUrl(); ?>/?page=create" class="btn btn-primary">Publiceer Een Verslag</a>
            <a href="<?php echo getBaseUrl(); ?>/?page=search" class="btn btn-secondary">Doorzoek Archief</a>
        </div>
    </section>
    <?php include 'includes/footer.php'; ?>
    <?php
}

if ($page === 'post') {
    $postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $post = $postId ? getPostById($postId) : null;
    if (!$post || ($post['status'] !== 'published' && !isset($_GET['preview']))) {
        header('Location: ' . getBaseUrl() . '/');
        exit;
    }
    $pageTitle = htmlspecialchars($post['title']);
    include 'includes/header.php';
    ?>
    <article class="glass-card">
        <header class="post-header" style="margin-bottom: 2rem;">
            <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="post-meta" style="font-size: 1rem;">
                <span>📅 <?php echo formatDate($post['created_at']); ?></span>
                <span>✍️ <?php echo htmlspecialchars($post['author'] ?? 'Onbekend'); ?></span>
                <span>🔍 Misdaadverslag</span>
                <?php if ($post['status'] === 'draft'): ?>
                    <span class="status-badge status-draft">Concept</span>
                <?php endif; ?>
                <?php 
                // Show last modified date if different from creation date
                // Simple approach: just check if timestamps are different
                if (isset($post['updated_at']) && isset($post['created_at'])) {
                    // Handle both numeric timestamps and date strings
                    $created = is_numeric($post['created_at']) ? (int)$post['created_at'] : strtotime($post['created_at']);
                    $updated = is_numeric($post['updated_at']) ? (int)$post['updated_at'] : strtotime($post['updated_at']);
                    
                    // Show last edited date only if different
                    if ($updated > $created) {
                        // Pass the updated_at value directly to formatDate which handles both timestamps and date strings
                        echo '<span style="color: #f97316;">🔄 Bijgewerkt: ' . formatDate($post['updated_at']) . '</span>';
                    }
                }
                ?>
            </div>
        </header>
        <div class="post-content" style="font-size: 1.1rem; line-height: 1.8;">
            <?php 
            // Convert plain text to HTML if needed before sanitizing
            $content = $post['content'];
            // Check if content has HTML block elements
            $hasHtmlBlocks = preg_match('/<(p|div|h[1-6]|ul|ol|blockquote|pre)(\s|>)/i', $content);
            
            if (!$hasHtmlBlocks && !empty(trim($content))) {
                $content = textToHtml($content);
            }
            
            echo sanitizeHtml($content); 
            ?>
        </div>
        <footer class="post-footer" style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <a href="<?php echo getBaseUrl(); ?>/" class="btn btn-secondary">← Terug naar Home</a>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <a href="<?php echo getBaseUrl(); ?>/?page=edit&id=<?php echo $post['id']; ?>" class="btn btn-secondary">Bewerk Artikel</a>
                    <a href="<?php echo getBaseUrl(); ?>/?page=search" class="btn btn-secondary">Meer Verslagen</a>
                </div>
            </div>
        </footer>
    </article>
    <?php
    $allPosts = getPublishedPosts();
    $relatedPosts = array_filter($allPosts, function($p) use ($post) { return $p['id'] !== $post['id']; });
    $relatedPosts = array_slice($relatedPosts, 0, 3);
    if (!empty($relatedPosts)) {
        ?>
        <section style="margin-top: 3rem;">
            <h2 style="color: #dc2626; margin-bottom: 2rem; text-align: center;">Gerelateerde Verslagen</h2>
            <div class="posts-grid">
                <?php foreach ($relatedPosts as $relatedPost): ?>
                    <a href="<?php echo getBaseUrl(); ?>/?page=post&id=<?php echo $relatedPost['id']; ?>" class="post-preview">
                        <h3><?php echo htmlspecialchars($relatedPost['title']); ?></h3>
                        <div class="post-meta">
                            <span>📅 <?php echo formatDate($relatedPost['date']); ?></span>
                        </div>
                        <div class="post-excerpt">
                            <?php 
                            // Convert plain text to HTML if needed before truncating and sanitizing
                            $relatedContent = $relatedPost['content'];
                            // Check if content has HTML block elements
                            $hasHtmlBlocks = preg_match('/<(p|div|h[1-6]|ul|ol|blockquote|pre)(\s|>)/i', $relatedContent);
                            
                            if (!$hasHtmlBlocks && !empty(trim($relatedContent))) {
                                $relatedContent = textToHtml($relatedContent);
                            }
                            
                            echo truncateText(sanitizeHtml($relatedContent), 100); 
                            ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    }
    include 'includes/footer.php';
} elseif ($page === 'search') {
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';
    $results = $query ? searchPosts($query) : [];
    $pageTitle = $query ? ('Zoekresultaten voor "' . htmlspecialchars($query) . '"') : 'Zoeken';
    include 'includes/header.php';
    ?>
    <section class="glass-card text-center">
        <h1 style="color: #dc2626; margin-bottom: 1rem;">Doorzoek Misdaadverslagen</h1>
        <p>Zoek naar specifieke zaken, locaties, of criminaliteitstypen in ons archief.</p>
        <form action="<?php echo getBaseUrl(); ?>/?page=search" method="GET" style="margin-top: 2rem; max-width: 500px; margin-left: auto; margin-right: auto;">
            <input type="hidden" name="page" value="search">
            <div style="display: flex; gap: 1rem;">
                <input type="text" name="q" placeholder="Bijv. drugs, Amsterdam, witwassen..." class="form-input" value="<?php echo htmlspecialchars($query); ?>" style="flex: 1; font-size: 1.1rem; padding: 1rem;">
                <button type="submit" class="btn btn-primary" style="padding: 1rem 2rem;">🔍 Zoeken</button>
            </div>
        </form>
    </section>
    <?php if ($query): ?>
        <?php if (!empty($results)): ?>
            <section>
                <h2 style="color: #dc2626; margin-bottom: 2rem;">
                    <?php echo count($results); ?> resultaten gevonden voor "<?php echo htmlspecialchars($query); ?>"
                </h2>
                <div class="posts-grid">
                    <?php foreach ($results as $post): ?>
                        <a href="<?php echo getBaseUrl(); ?>/?page=post&id=<?php echo $post['id']; ?>" class="post-preview">
                            <h3><?php $safeQuery = htmlspecialchars($query); echo str_ireplace($safeQuery, '<mark style="background: rgba(220, 38, 38, 0.3); color: #dc2626;">' . $safeQuery . '</mark>', htmlspecialchars($post['title'])); ?></h3>
                            <div class="post-meta">
                                <span>📅 <?php echo formatDate($post['date']); ?></span>
                                <span>🔍 Misdaadverslag</span>
                            </div>
                            <div class="post-excerpt">
                                <?php 
                                // Convert plain text to HTML if needed before truncating and sanitizing
                                $searchContent = $post['content'];
                                // Check if content has HTML block elements
                                $hasHtmlBlocks = preg_match('/<(p|div|h[1-6]|ul|ol|blockquote|pre)(\s|>)/i', $searchContent);
                                
                                if (!$hasHtmlBlocks && !empty(trim($searchContent))) {
                                    $searchContent = textToHtml($searchContent);
                                }
                                
                                echo truncateText(sanitizeHtml($searchContent), 150); 
                                ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php else: ?>
            <section class="glass-card text-center">
                <h2>Geen resultaten gevonden</h2>
                <p>Er zijn geen verslagen gevonden die overeenkomen met "<?php echo htmlspecialchars($query); ?>".</p>
            </section>
        <?php endif; ?>
    <?php else: ?>
        <section>
            <h2 style="color: #dc2626; margin-bottom: 2rem; text-align: center;">Populaire Zoektermen</h2>
            <div style="text-align: center; margin-bottom: 3rem;">
                <div style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
                    <?php $popularTerms = ['drugs', 'Amsterdam', 'Rotterdam', 'witwassen', 'mensenhandel', 'cocaïne', 'politie', 'arrestatie']; foreach ($popularTerms as $term): ?>
                        <a href="<?php echo getBaseUrl(); ?>/?page=search&q=<?php echo urlencode($term); ?>" class="btn btn-secondary"><?php echo htmlspecialchars($term); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <?php include 'includes/footer.php'; ?>
<?php } elseif ($page === 'admin') { ?>
    <?php $pageTitle = 'Admin Dashboard'; include 'includes/header.php'; ?>
    <?php
    $allPosts = getAllPosts();
    $publishedCount = count(getPublishedPosts());
    $draftCount = count($allPosts) - $publishedCount;
    ?>
    <section class="admin-header">
        <h1>🔐 Admin Dashboard</h1>
        <p>Beheer alle misdaadverslagen en publicaties</p>
    </section>
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
    <section class="admin-actions">
        <a href="<?php echo getBaseUrl(); ?>/" class="btn btn-secondary">🏠 Terug naar Home</a>
        <a href="<?php echo getBaseUrl(); ?>/?page=search" class="btn btn-secondary">🔍 Zoeken</a>
    </section>
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
                                    <small style="color: #71717a;">✍️ <?php echo htmlspecialchars($post['author'] ?? 'Onbekend'); ?> · <?php 
                                    // Convert plain text to HTML if needed before truncating
                                    $adminContent = $post['content'];
                                    // Check if content has HTML block elements
                                    $hasHtmlBlocks = preg_match('/<(p|div|h[1-6]|ul|ol|blockquote|pre)(\s|>)/i', $adminContent);
                                    
                                    if (!$hasHtmlBlocks && !empty(trim($adminContent))) {
                                        $adminContent = textToHtml($adminContent);
                                    }
                                    
                                    echo truncateText(strip_tags($adminContent), 60); ?></small>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $post['status']; ?>"><?php echo $post['status'] === 'published' ? 'Gepubliceerd' : 'Concept'; ?></span>
                                </td>
                                <td><?php echo formatDate($post['date']); ?></td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                                        <a href="<?php echo getBaseUrl(); ?>/?page=post&id=<?php echo $post['id']; ?><?php echo $post['status'] === 'draft' ? '&preview=1' : ''; ?>" class="btn btn-secondary" style="padding: 0.5rem; font-size: 0.8rem;">👁️ Bekijk</a>
                                        <a href="<?php echo getBaseUrl(); ?>/?page=edit&id=<?php echo $post['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem; font-size: 0.8rem;">✏️ Bewerk</a>
                                        <?php if (!empty($post['featured'])): ?>
                                            <span class="status-badge" style="background: rgba(234, 179, 8, 0.15); color: #eab308;">⭐ Uitgelicht</span>
                                        <?php else: ?>
                                            <form action="<?php echo getBaseUrl(); ?>/?page=admin-feature" method="POST" style="display:inline;">
                                                <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                                <button type="submit" class="btn btn-secondary" style="padding: 0.5rem; font-size: 0.8rem;">⭐ Maak Uitgelicht</button>
                                            </form>
                                        <?php endif; ?>
                                        <button onclick="confirmDelete(<?php echo $post['id']; ?>, '<?php echo addslashes($post['title']); ?>')" class="btn btn-danger" style="padding: 0.5rem; font-size: 0.8rem;">🗑️ Verwijder</button>
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
                <p class="text-muted" style="margin-top: 1rem;">Gebruik Admin om bestaande verslagen te bewerken of verwijderen.</p>
            </div>
        <?php endif; ?>
    </section>
    <?php include 'includes/footer.php'; ?>
<?php } elseif ($page === 'edit') {
    $pageTitle = 'Verslag Bewerken';
    $errors = [];
    $success = false;

    // Get post ID
    $postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if (!$postId) {
        header('Location: ' . getBaseUrl() . '/');
        exit;
    }

    // Get the post to edit
    $post = getPostById($postId);

    if (!$post) {
        header('Location: ' . getBaseUrl() . '/');
        exit;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status = $_POST['status'] ?? 'draft';
        $author = trim($_POST['author'] ?? '');
        
        // Validation
        if (empty($title)) {
            $errors[] = 'Titel is verplicht';
        } elseif (strlen($title) < 3) {
            $errors[] = 'Titel moet minimaal 3 karakters bevatten';
        }
        
        if (empty($content)) {
            $errors[] = 'Inhoud is verplicht';
        } elseif (strlen($content) < 10) {
            $errors[] = 'Inhoud moet minimaal 10 karakters bevatten';
        }
        
        if (!in_array($status, ['draft', 'published'])) {
            $errors[] = 'Ongeldige status';
        }
        if ($author === '') {
            $errors[] = 'Auteur is verplicht';
        }
        
        // Update post if no errors
        if (empty($errors)) {
            $updateResult = updatePost($postId, $title, $content, $status, $author);
            if ($updateResult !== false) {
                $success = true;
                // Redirect to view the updated post
                header('Location: ' . getBaseUrl() . '/?page=post&id=' . $postId . ($status === 'draft' ? '&preview=1' : ''));
                exit;
            } else {
                $errors[] = 'Fout bij het bijwerken van het verslag';
            }
        }
    }

    include 'includes/header.php';
    ?>
    
    <!-- Public Edit Post Header -->
    <section class="glass-card text-center">
        <h1 style="color: #dc2626; margin-bottom: 0.5rem;">✏️ Verslag Bewerken</h1>
        <p>Pas je misdaadverslag aan en publiceer of bewaar als concept</p>
    </section>

    <!-- Edit Post Form -->
    <section class="glass-card">
        <?php if (!empty($errors)): ?>
            <div style="background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.3); border-radius: 8px; padding: 1rem; margin-bottom: 2rem;">
                <h4 style="color: #dc2626; margin-bottom: 0.5rem;">Fouten gevonden:</h4>
                <ul style="color: #dc2626; margin-left: 1rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form action="<?php echo getBaseUrl(); ?>/?page=edit&id=<?php echo $postId; ?>" method="POST" id="edit-form" onsubmit="return validateForm('edit-form')">
            <div class="form-group">
                <label for="title" class="form-label">Titel van het Verslag</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-input" 
                    placeholder="Bijv. Grote Drugsbust in Amsterdam: 500kg Cocaïne Onderschept"
                    value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : htmlspecialchars($post['title']); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="author" class="form-label">Auteur</label>
                <input 
                    type="text" 
                    id="author" 
                    name="author" 
                    class="form-input" 
                    placeholder="Bijv. Jansen, Redactie"
                    value="<?php echo isset($_POST['author']) ? htmlspecialchars($_POST['author']) : htmlspecialchars($post['author'] ?? ''); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="content" class="form-label">Verslag Inhoud</label>
                <!-- Text Formatting Buttons -->
                <div style="margin-bottom: 0.5rem;">
                    <button type="button" class="btn btn-secondary" data-format="bold" title="Vet (Ctrl+B)" style="padding: 0.25rem 0.5rem; font-size: 0.9rem;">B</button>
                    <button type="button" class="btn btn-secondary" data-format="italic" title="Cursief (Ctrl+I)" style="padding: 0.25rem 0.5rem; font-size: 0.9rem; font-style: italic;">I</button>
                    <button type="button" class="btn btn-secondary" data-format="underline" title="Onderstrepen (Ctrl+U)" style="padding: 0.25rem 0.5rem; font-size: 0.9rem; text-decoration: underline;">U</button>
                    <button type="button" class="btn btn-secondary" data-format="strikethrough" title="Doorhalen (Ctrl+S)" style="padding: 0.25rem 0.5rem; font-size: 0.9rem; text-decoration: line-through;">S</button>
                </div>
                <textarea 
                    id="content" 
                    name="content" 
                    class="form-textarea" 
                    placeholder="Schrijf hier het volledige misdaadverslag... Je kunt HTML gebruiken voor opmaak zoals <p>, <strong>, <em>, <u>, <s>, <ul>, <li>, etc. Gebruik ook de knoppen hierboven of sneltoetsen: Ctrl+B (vet), Ctrl+I (cursief), Ctrl+U (onderstrepen), Ctrl+S (doorhalen)"
                    required
                    style="min-height: 400px;"
                ><?php 
                if (isset($_POST['content'])) {
                    echo htmlspecialchars($_POST['content']);
                } else {
                    // Show content in editor preserving line breaks
                    $editableContent = $post['content'];
                    // Convert <p> tags to double line breaks
                    $editableContent = preg_replace('/<\/p>\s*<p>/', "\n\n", $editableContent);
                    $editableContent = preg_replace('/<p>/', '', $editableContent);
                    $editableContent = preg_replace('/<\/p>/', '', $editableContent);
                    // Convert <br> tags to line breaks
                    $editableContent = preg_replace('/<br\s*\/?>/', "\n", $editableContent);
                    // Remove other HTML tags but preserve formatting tags for display
                    $editableContent = strip_tags($editableContent, '<strong><em><u><s>');
                    // Decode HTML entities
                    $editableContent = html_entity_decode($editableContent, ENT_QUOTES, 'UTF-8');
                    echo $editableContent;
                }
                ?></textarea>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="draft" <?php echo (isset($_POST['status']) && $_POST['status'] === 'draft') || ($post['status'] === 'draft') ? 'selected' : ''; ?>>
                        Concept (niet zichtbaar voor bezoekers)
                    </option>
                    <option value="published" <?php echo (isset($_POST['status']) && $_POST['status'] === 'published') || ($post['status'] === 'published') ? 'selected' : ''; ?>>
                        Gepubliceerd (direct zichtbaar)
                    </option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
                <button type="submit" class="btn btn-primary">
                    💾 Wijzigingen Opslaan
                </button>
                <a href="<?php echo getBaseUrl(); ?>/?page=post&id=<?php echo $postId; ?><?php echo $post['status'] === 'draft' ? '&preview=1' : ''; ?>" class="btn btn-secondary">
                    ❌ Annuleren
                </a>
            </div>
        </form>
    </section>

    <?php include 'includes/footer.php'; ?>
<?php } elseif ($page === 'create') { 
    $pageTitle = 'Nieuw Verslag Aanmaken';
    $errors = [];
    $success = false;

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status = $_POST['status'] ?? 'draft';
        $author = trim($_POST['author'] ?? '');
        
        // Validation
        if (empty($title)) {
            $errors[] = 'Titel is verplicht';
        } elseif (strlen($title) < 3) {
            $errors[] = 'Titel moet minimaal 3 karakters bevatten';
        }
        
        if (empty($content)) {
            $errors[] = 'Inhoud is verplicht';
        } elseif (strlen($content) < 10) {
            $errors[] = 'Inhoud moet minimaal 10 karakters bevatten';
        }
        
        if (!in_array($status, ['draft', 'published'])) {
            $errors[] = 'Ongeldige status';
        }
        if ($author === '') {
            $errors[] = 'Auteur is verplicht';
        }
        
        // Create post if no errors
        if (empty($errors)) {
            $newPost = createPost($title, $content, $status, $author);
            if ($newPost) {
                $success = true;
                // Redirect to view
                header('Location: ' . getBaseUrl() . '/?page=post&id=' . $newPost['id'] . ($status === 'draft' ? '&preview=1' : ''));
                exit;
            } else {
                $errors[] = 'Fout bij het opslaan van het verslag';
            }
        }
    }

    include 'includes/header.php';
    ?>
    
    <!-- Public Create Post Header -->
    <section class="glass-card text-center">
        <h1 style="color: #dc2626; margin-bottom: 0.5rem;">📝 Nieuw Misdaadverslag Aanmaken</h1>
        <p>Publiceer direct of sla op als concept. Auteur is verplicht.</p>
    </section>

    <!-- Create Post Form -->
    <section class="glass-card">
        <?php if (!empty($errors)): ?>
            <div style="background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.3); border-radius: 8px; padding: 1rem; margin-bottom: 2rem;">
                <h4 style="color: #dc2626; margin-bottom: 0.5rem;">Fouten gevonden:</h4>
                <ul style="color: #dc2626; margin-left: 1rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form action="<?php echo getBaseUrl(); ?>/?page=create" method="POST" id="create-form" onsubmit="return validateForm('create-form')">
            <div class="form-group">
                <label for="title" class="form-label">Titel van het Verslag</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-input" 
                    placeholder="Bijv. Grote Drugsbust in Amsterdam: 500kg Cocaïne Onderschept"
                    value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="author" class="form-label">Auteur</label>
                <input 
                    type="text" 
                    id="author" 
                    name="author" 
                    class="form-input" 
                    placeholder="Bijv. Jansen, Redactie"
                    value="<?php echo isset($_POST['author']) ? htmlspecialchars($_POST['author']) : ''; ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="content" class="form-label">Verslag Inhoud</label>
                <!-- Text Formatting Buttons -->
                <div style="margin-bottom: 0.5rem;">
                    <button type="button" class="btn btn-secondary" data-format="bold" title="Vet (Ctrl+B)" style="padding: 0.25rem 0.5rem; font-size: 0.9rem;">B</button>
                    <button type="button" class="btn btn-secondary" data-format="italic" title="Cursief (Ctrl+I)" style="padding: 0.25rem 0.5rem; font-size: 0.9rem; font-style: italic;">I</button>
                    <button type="button" class="btn btn-secondary" data-format="underline" title="Onderstrepen (Ctrl+U)" style="padding: 0.25rem 0.5rem; font-size: 0.9rem; text-decoration: underline;">U</button>
                    <button type="button" class="btn btn-secondary" data-format="strikethrough" title="Doorhalen (Ctrl+S)" style="padding: 0.25rem 0.5rem; font-size: 0.9rem; text-decoration: line-through;">S</button>
                </div>
                <textarea 
                    id="content" 
                    name="content" 
                    class="form-textarea" 
                    placeholder="Schrijf hier het volledige misdaadverslag... Je kunt HTML gebruiken voor opmaak zoals <p>, <strong>, <em>, <u>, <s>, <ul>, <li>, etc. Gebruik ook de knoppen hierboven of sneltoetsen: Ctrl+B (vet), Ctrl+I (cursief), Ctrl+U (onderstrepen), Ctrl+S (doorhalen)"
                    required
                    style="min-height: 400px;"
                ><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="draft" <?php echo (isset($_POST['status']) && $_POST['status'] === 'draft') ? 'selected' : ''; ?>>
                        Concept (niet zichtbaar voor bezoekers)
                    </option>
                    <option value="published" <?php echo (isset($_POST['status']) && $_POST['status'] === 'published') ? 'selected' : ''; ?>>
                        Gepubliceerd (direct zichtbaar)
                    </option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
                <button type="submit" class="btn btn-primary">
                    💾 Verslag Opslaan
                </button>
                <a href="<?php echo getBaseUrl(); ?>/" class="btn btn-secondary">
                    ❌ Annuleren
                </a>
            </div>
        </form>
    </section>

    <?php include 'includes/footer.php'; ?>
<?php } elseif ($page === 'admin-create') { 
    $pageTitle = 'Nieuw Verslag Aanmaken';
    $errors = [];
    $success = false;

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status = $_POST['status'] ?? 'draft';
        $author = trim($_POST['author'] ?? '');
        
        // Validation
        if (empty($title)) {
            $errors[] = 'Titel is verplicht';
        } elseif (strlen($title) < 3) {
            $errors[] = 'Titel moet minimaal 3 karakters bevatten';
        }
        
        if (empty($content)) {
            $errors[] = 'Inhoud is verplicht';
        } elseif (strlen($content) < 10) {
            $errors[] = 'Inhoud moet minimaal 10 karakters bevatten';
        }
        
        if (!in_array($status, ['draft', 'published'])) {
            $errors[] = 'Ongeldige status';
        }
        if ($author === '') {
            $errors[] = 'Auteur is verplicht';
        }
        
        // Create post if no errors
        if (empty($errors)) {
            $newPost = createPost($title, $content, $status, $author);
            if ($newPost) {
                $success = true;
                // Redirect to admin or post view
                if ($status === 'published') {
                    header('Location: ' . getBaseUrl() . '/?page=post&id=' . $newPost['id']);
                } else {
                    header('Location: ' . getBaseUrl() . '/?page=admin&created=1');
                }
                exit;
            } else {
                $errors[] = 'Fout bij het opslaan van het verslag';
            }
        }
    }

    include 'includes/header.php';
    ?>
    
    <!-- Create Post Header -->
    <section class="admin-header">
        <h1>📝 Nieuw Misdaadverslag Aanmaken</h1>
        <p>Voeg een nieuw verslag toe aan het Criminal Minds archief</p>
    </section>

    <!-- Breadcrumb -->
    <nav style="margin-bottom: 2rem;">
        <a href="<?php echo getBaseUrl(); ?>/?page=admin" class="btn btn-secondary">← Terug naar Admin</a>
    </nav>

    <!-- Create Post Form -->
    <section class="glass-card">
        <?php if (!empty($errors)): ?>
            <div style="background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.3); border-radius: 8px; padding: 1rem; margin-bottom: 2rem;">
                <h4 style="color: #dc2626; margin-bottom: 0.5rem;">Fouten gevonden:</h4>
                <ul style="color: #dc2626; margin-left: 1rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form action="<?php echo getBaseUrl(); ?>/?page=admin-create" method="POST" id="create-form" onsubmit="return validateForm('create-form')">
            <div class="form-group">
                <label for="title" class="form-label">Titel van het Verslag</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-input" 
                    placeholder="Bijv. Grote Drugsbust in Amsterdam: 500kg Cocaïne Onderschept"
                    value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                    required
                >
                <small style="color: #71717a; display: block; margin-top: 0.5rem;">
                    Maak de titel aantrekkelijk en informatief voor lezers
                </small>
            </div>

            <div class="form-group">
                <label for="author" class="form-label">Auteur</label>
                <input 
                    type="text" 
                    id="author" 
                    name="author" 
                    class="form-input" 
                    placeholder="Bijv. Jansen, Redactie"
                    value="<?php echo isset($_POST['author']) ? htmlspecialchars($_POST['author']) : ''; ?>"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="content" class="form-label">Verslag Inhoud</label>
                <!-- Text Formatting Buttons -->
                <div style="margin-bottom: 0.5rem;">
                    <button type="button" class="btn btn-secondary" data-format="bold" title="Vet (Ctrl+B)" style="padding: 0.25rem 0.5rem; font-size: 0.9rem;">B</button>
                    <button type="button" class="btn btn-secondary" data-format="italic" title="Cursief (Ctrl+I)" style="padding: 0.25rem 0.5rem; font-size: 0.9rem; font-style: italic;">I</button>
                    <button type="button" class="btn btn-secondary" data-format="underline" title="Onderstrepen (Ctrl+U)" style="padding: 0.25rem 0.5rem; font-size: 0.9rem; text-decoration: underline;">U</button>
                    <button type="button" class="btn btn-secondary" data-format="strikethrough" title="Doorhalen (Ctrl+S)" style="padding: 0.25rem 0.5rem; font-size: 0.9rem; text-decoration: line-through;">S</button>
                </div>
                <textarea 
                    id="content" 
                    name="content" 
                    class="form-textarea" 
                    placeholder="Schrijf hier het volledige misdaadverslag... Je kunt HTML gebruiken voor opmaak zoals <p>, <strong>, <em>, <u>, <s>, <ul>, <li>, etc. Gebruik ook de knoppen hierboven of sneltoetsen: Ctrl+B (vet), Ctrl+I (cursief), Ctrl+U (onderstrepen), Ctrl+S (doorhalen)"
                    required
                    style="min-height: 400px;"
                ><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                
                <div style="margin-top: 1rem; padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 8px;">
                    <h4 style="color: #dc2626; margin-bottom: 0.5rem;">Opmaak Tips:</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.9rem; color: #a1a1aa;">
                        <div>
                            <strong>Paragraaf:</strong> &lt;p&gt;tekst&lt;/p&gt;
                        </div>
                        <div>
                            <strong>Vet:</strong> &lt;strong&gt;tekst&lt;/strong&gt;
                        </div>
                        <div>
                            <strong>Cursief:</strong> &lt;em&gt;tekst&lt;/em&gt;
                        </div>
                        <div>
                            <strong>Onderstrepen:</strong> &lt;u&gt;tekst&lt;/u&gt;
                        </div>
                        <div>
                            <strong>Doorhalen:</strong> &lt;s&gt;tekst&lt;/s&gt;
                        </div>
                        <div>
                            <strong>Lijst:</strong> &lt;ul&gt;&lt;li&gt;item&lt;/li&gt;&lt;/ul&gt;
                        </div>
                        <div>
                            <strong>Sneltoetsen:</strong> Ctrl+B (vet), Ctrl+I (cursief), Ctrl+U (onderstrepen), Ctrl+S (doorhalen)
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="draft" <?php echo (isset($_POST['status']) && $_POST['status'] === 'draft') ? 'selected' : ''; ?>>
                        Concept (niet zichtbaar voor bezoekers)
                    </option>
                    <option value="published" <?php echo (isset($_POST['status']) && $_POST['status'] === 'published') ? 'selected' : ''; ?>>
                        Gepubliceerd (direct zichtbaar)
                    </option>
                </select>
                <small style="color: #71717a; display: block; margin-top: 0.5rem;">
                    Kies "Concept" om later te publiceren, of "Gepubliceerd" om direct live te zetten
                </small>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 3rem; flex-wrap: wrap;">
                <button type="submit" class="btn btn-primary">
                    💾 Verslag Opslaan
                </button>
                <a href="<?php echo getBaseUrl(); ?>/?page=admin" class="btn btn-secondary">
                    ❌ Annuleren
                </a>
            </div>
        </form>
    </section>

    <!-- Preview Section -->
    <section class="glass-card" style="margin-top: 2rem;">
        <h2 style="color: #dc2626; margin-bottom: 1rem;">📋 Schrijf Tips voor Misdaadverslagen</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #e4e4e7; margin-bottom: 1rem;">🎯 Goede Titel</h4>
                <ul style="color: #a1a1aa; line-height: 1.6;">
                    <li>Wees specifiek en informatief</li>
                    <li>Vermeld locatie als relevant</li>
                    <li>Gebruik cijfers voor impact</li>
                    <li>Maak het nieuwsgierig</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: #e4e4e7; margin-bottom: 1rem;">📝 Sterke Inhoud</h4>
                <ul style="color: #a1a1aa; line-height: 1.6;">
                    <li>Begin met de belangrijkste feiten</li>
                    <li>Gebruik korte, duidelijke paragrafen</li>
                    <li>Voeg details toe in logische volgorde</li>
                    <li>Eindig met impact of gevolgen</li>
                </ul>
            </div>
            
            <div>
                <h4 style="color: #e4e4e7; margin-bottom: 1rem;">⚖️ Juridische Aspecten</h4>
                <ul style="color: #a1a1aa; line-height: 1.6;">
                    <li>Gebruik "verdachte" niet "dader"</li>
                    <li>Vermeld geen namen van verdachten</li>
                    <li>Alle verhalen zijn fictief</li>
                    <li>Respecteer privacy van betrokkenen</li>
                </ul>
            </div>
        </div>
    </section>

    <script>
    // Auto-save functionality (simple localStorage backup)
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const contentTextarea = document.getElementById('content');
        
        // Load from localStorage on page load
        if (localStorage.getItem('criminal_minds_draft_title')) {
            titleInput.value = localStorage.getItem('criminal_minds_draft_title');
        }
        if (localStorage.getItem('criminal_minds_draft_content')) {
            contentTextarea.value = localStorage.getItem('criminal_minds_draft_content');
        }
        
        // Save to localStorage on input
        titleInput.addEventListener('input', function() {
            localStorage.setItem('criminal_minds_draft_title', this.value);
        });
        
        contentTextarea.addEventListener('input', function() {
            localStorage.setItem('criminal_minds_draft_content', this.value);
        });
        
        // Clear localStorage on successful submit
        document.getElementById('create-form').addEventListener('submit', function() {
            localStorage.removeItem('criminal_minds_draft_title');
            localStorage.removeItem('criminal_minds_draft_content');
        });
    });
    </script>

    <?php include 'includes/footer.php'; ?>
<?php } elseif ($page === 'admin-delete') { 
    // Handle admin delete functionality
    require_once 'includes/functions.php';
    
    // Get post ID
    $postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if (!$postId) {
        header('Location: ' . getBaseUrl() . '/?page=admin');
        exit;
    }

    // Get the post to verify it exists
    $post = getPostById($postId);

    if (!$post) {
        header('Location: ' . getBaseUrl() . '/?page=admin');
        exit;
    }

    // Handle the deletion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
        deletePost($postId);
        header('Location: ' . getBaseUrl() . '/?page=admin&deleted=1');
        exit;
    }

    $pageTitle = 'Verslag Verwijderen';

    include 'includes/header.php';
    ?>
    
    <!-- Delete Confirmation -->
    <section class="admin-header" style="background: rgba(220, 38, 38, 0.1); border-color: rgba(220, 38, 38, 0.3);">
        <h1>🗑️ Verslag Verwijderen</h1>
        <p>Je staat op het punt een verslag definitief te verwijderen</p>
    </section>

    <!-- Breadcrumb -->
    <nav style="margin-bottom: 2rem;">
        <a href="<?php echo getBaseUrl(); ?>/?page=admin" class="btn btn-secondary">← Terug naar Admin</a>
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
                <?php 
                // Convert plain text to HTML if needed before truncating
                $deleteContent = $post['content'];
                // Check if content has HTML block elements
                $hasHtmlBlocks = preg_match('/<(p|div|h[1-6]|ul|ol|blockquote|pre)(\s|>)/i', $deleteContent);
                
                if (!$hasHtmlBlocks && !empty(trim($deleteContent))) {
                    $deleteContent = textToHtml($deleteContent);
                }
                
                echo truncateText($deleteContent, 200); 
                ?>
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
        
        <form action="<?php echo getBaseUrl(); ?>/?page=admin-delete&id=<?php echo $postId; ?>" method="POST" style="text-align: center;">
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <button type="submit" name="confirm_delete" class="btn btn-danger" style="font-size: 1.1rem; padding: 1rem 2rem;">
                    🗑️ Ja, Verwijder Definitief
                </button>
                <a href="<?php echo getBaseUrl(); ?>/?page=admin" class="btn btn-secondary" style="font-size: 1.1rem; padding: 1rem 2rem;">
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
                <a href="<?php echo getBaseUrl(); ?>/?page=admin" class="btn btn-secondary">
                    Bewerk dit Verslag
                </a>
            </div>
            
            <div style="background: rgba(249, 115, 22, 0.1); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(249, 115, 22, 0.2);">
                <h4 style="color: #f97316; margin-bottom: 1rem;">👁️ Concept Maken</h4>
                <p style="margin-bottom: 1rem;">Zet het verslag op 'concept' zodat het niet zichtbaar is voor bezoekers.</p>
                <?php if ($post['status'] === 'published'): ?>
                    <form action="<?php echo getBaseUrl(); ?>/?page=admin" method="POST" style="display: inline;">
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
        const deleteForm = document.querySelector('form[action*="admin-delete"]');
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

    <?php include 'includes/footer.php'; ?>
<?php } elseif ($page === 'disclaimer') { ?>
    <?php $pageTitle = 'Disclaimer & Veiligheidsregels'; include 'includes/header.php'; ?>
    <section class="page-header">
        <h1>Disclaimer & Veiligheidsregels</h1>
        <p>Lees deze regels zorgvuldig door om veilig en verantwoordelijk met onze site om te gaan.</p>
        <hr>
        <p><a href="<?php echo getBaseUrl(); ?>/">&#8592; Terug naar Home</a></p>
        <br>
    </section>
    <section class="content-section">
        <h2>Algemeen</h2>
        <ul>
            <li>De inhoud van deze website is bedoeld voor educatieve en informatieve doeleinden.</li>
            <li>Wij garanderen niet dat alle informatie volledig, actueel of foutloos is.</li>
            <li>Gebruik de informatie op eigen verantwoordelijkheid.</li>
        </ul>
        <h2>Veiligheid & Gedragsregels</h2>
        <ul>
            <li>Deel geen persoonlijke gegevens van anderen zonder hun expliciete toestemming.</li>
            <li>Plaats geen gevoelige of vertrouwelijke informatie die iemand kan schaden of in gevaar kan brengen.</li>
            <li>Respecteer de privacy en waardigheid van alle personen; geen doxing, pesten of intimidatie.</li>
            <li>Gebruik geen discriminerende, haatdragende of gewelddadige taal.</li>
            <li>Deel geen content die illegale activiteiten promoot of instructies geeft om wetten te overtreden.</li>
            <li>Meld onveilige of ongepaste inhoud via de beschikbare contactkanalen.</li>
        </ul>
        <h2>Gebruikersbijdragen</h2>
        <ul>
            <li>Door content te plaatsen bevestig je dat je het recht hebt om die content te delen.</li>
            <li>Wij behouden ons het recht voor om bijdragen te modereren, aan te passen of te verwijderen die in strijd zijn met deze regels.</li>
        </ul>
        <h2>Aansprakelijkheidsbeperking</h2>
        <p>Wij zijn niet aansprakelijk voor schade die direct of indirect voortvloeit uit het gebruik van deze website of de informatie daarop.</p>
        <h2>Wijzigingen</h2>
        <p>We kunnen deze regels en disclaimer van tijd tot tijd bijwerken. Controleer deze pagina regelmatig.</p>
        <h2>Contact</h2>
        <p>Heb je vragen of wil je een probleem melden? Neem contact op via de kanalen op de website.</p>
    </section>
    <?php include 'includes/footer.php'; ?>
<?php } elseif ($page === 'admin-feature') { 
    // Handle admin feature functionality
    require_once 'includes/functions.php';
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . getBaseUrl() . '/?page=admin');
        exit;
    }
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        setFeaturedPost($id);
    }
    
    header('Location: ' . getBaseUrl() . '/?page=admin&featured=' . urlencode((string)$id));
    exit;
?>
<?php } else { ?>
    <?php $pageTitle = 'Home'; renderHomeView(); ?>
<?php } ?>


