<?php
/**
 * Criminal Minds Blog - Create New Post
 */

require_once '../includes/functions.php';

$pageTitle = 'Nieuw Verslag Aanmaken';
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    
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
    
    // Create post if no errors
    if (empty($errors)) {
        $newPost = createPost($title, $content, $status);
        if ($newPost) {
            $success = true;
            // Redirect to admin or post view
            if ($status === 'published') {
                header('Location: ' . getBaseUrl() . '/post.php?id=' . $newPost['id']);
            } else {
                header('Location: ' . getBaseUrl() . '/admin/?created=1');
            }
            exit;
        } else {
            $errors[] = 'Fout bij het opslaan van het verslag';
        }
    }
}

include '../includes/header.php';
?>

<!-- Create Post Header -->
<section class="admin-header">
    <h1>📝 Nieuw Misdaadverslag Aanmaken</h1>
    <p>Voeg een nieuw verslag toe aan het Criminal Minds archief</p>
</section>

<!-- Breadcrumb -->
<nav style="margin-bottom: 2rem;">
    <a href="<?php echo getBaseUrl(); ?>/admin/" class="btn btn-secondary">← Terug naar Admin</a>
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
    
    <form action="<?php echo getBaseUrl(); ?>/admin/create.php" method="POST" id="create-form" onsubmit="return validateForm('create-form')">
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
            <label for="content" class="form-label">Verslag Inhoud</label>
            <textarea 
                id="content" 
                name="content" 
                class="form-textarea" 
                placeholder="Schrijf hier het volledige misdaadverslag... Je kunt HTML gebruiken voor opmaak zoals <p>, <strong>, <ul>, <li>, etc."
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
                        <strong>Lijst:</strong> &lt;ul&gt;&lt;li&gt;item&lt;/li&gt;&lt;/ul&gt;
                    </div>
                    <div>
                        <strong>Sneltoets:</strong> Ctrl+B (vet), Ctrl+I (cursief)
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
            <a href="<?php echo getBaseUrl(); ?>/admin/" class="btn btn-secondary">
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

<?php include '../includes/footer.php'; ?>