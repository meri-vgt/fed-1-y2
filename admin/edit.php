<?php
/**
 * Criminal Minds Blog - Edit Post
 */

require_once '../includes/functions.php';

$pageTitle = 'Verslag Bewerken';
$errors = [];
$success = false;

// Get post ID
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$postId) {
    header('Location: ' . getBaseUrl() . '/admin/');
    exit;
}

// Get the post
$post = getPostById($postId);

if (!$post) {
    header('Location: ' . getBaseUrl() . '/admin/');
    exit;
}

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
    
    // Update post if no errors
    if (empty($errors)) {
        updatePost($postId, $title, $content, $status);
        $success = true;
        
        // Refresh post data
        $post = getPostById($postId);
        
        // Redirect based on action
        if (isset($_POST['save_and_view'])) {
            header('Location: ' . getBaseUrl() . '/post.php?id=' . $postId . ($status === 'draft' ? '&preview=1' : ''));
            exit;
        } elseif (isset($_POST['save_and_admin'])) {
            header('Location: ' . getBaseUrl() . '/admin/?updated=1');
            exit;
        }
    }
}

include '../includes/header.php';
?>

<!-- Edit Post Header -->
<section class="admin-header">
    <h1>✏️ Verslag Bewerken</h1>
    <p>Bewerk "<?php echo htmlspecialchars($post['title']); ?>"</p>
</section>

<!-- Breadcrumb -->
<nav style="margin-bottom: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
    <a href="<?php echo getBaseUrl(); ?>/admin/" class="btn btn-secondary">← Admin Dashboard</a>
    <a href="<?php echo getBaseUrl(); ?>/post.php?id=<?php echo $post['id']; ?><?php echo $post['status'] === 'draft' ? '&preview=1' : ''; ?>" class="btn btn-secondary">👁️ Bekijk Verslag</a>
</nav>

<!-- Success Message -->
<?php if ($success): ?>
    <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 8px; padding: 1rem; margin-bottom: 2rem;">
        <p style="color: #22c55e; margin: 0;">✅ Verslag succesvol bijgewerkt!</p>
    </div>
<?php endif; ?>

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
    
    <form action="<?php echo getBaseUrl(); ?>/admin/edit.php?id=<?php echo $postId; ?>" method="POST" id="edit-form" onsubmit="return validateForm('edit-form')">
        <div class="form-group">
            <label for="title" class="form-label">Titel van het Verslag</label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                class="form-input" 
                placeholder="Bijv. Grote Drugsbust in Amsterdam: 500kg Cocaïne Onderschept"
                value="<?php echo htmlspecialchars(isset($_POST['title']) ? $_POST['title'] : $post['title']); ?>"
                required
            >
        </div>
        
        <div class="form-group">
            <label for="content" class="form-label">Verslag Inhoud</label>
            <textarea 
                id="content" 
                name="content" 
                class="form-textarea" 
                placeholder="Schrijf hier het volledige misdaadverslag..."
                required
                style="min-height: 400px;"
            ><?php echo htmlspecialchars(isset($_POST['content']) ? $_POST['content'] : $post['content']); ?></textarea>
            
            <div style="margin-top: 1rem; padding: 1rem; background: rgba(255, 255, 255, 0.03); border-radius: 8px;">
                <h4 style="color: #dc2626; margin-bottom: 0.5rem;">Opmaak Tips:</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.9rem; color: #a1a1aa;">
                    <div><strong>Paragraaf:</strong> &lt;p&gt;tekst&lt;/p&gt;</div>
                    <div><strong>Vet:</strong> &lt;strong&gt;tekst&lt;/strong&gt;</div>
                    <div><strong>Lijst:</strong> &lt;ul&gt;&lt;li&gt;item&lt;/li&gt;&lt;/ul&gt;</div>
                    <div><strong>Sneltoets:</strong> Ctrl+B (vet), Ctrl+I (cursief)</div>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="draft" <?php echo (isset($_POST['status']) ? $_POST['status'] : $post['status']) === 'draft' ? 'selected' : ''; ?>>
                    Concept (niet zichtbaar voor bezoekers)
                </option>
                <option value="published" <?php echo (isset($_POST['status']) ? $_POST['status'] : $post['status']) === 'published' ? 'selected' : ''; ?>>
                    Gepubliceerd (zichtbaar voor bezoekers)
                </option>
            </select>
        </div>
        
        <!-- Post Meta Information -->
        <div style="background: rgba(255, 255, 255, 0.03); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <h4 style="color: #dc2626; margin-bottom: 1rem;">Verslag Informatie</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; color: #a1a1aa;">
                <div>
                    <strong>ID:</strong> <?php echo $post['id']; ?>
                </div>
                <div>
                    <strong>Aangemaakt:</strong> <?php echo formatDate($post['date']); ?>
                </div>
                <div>
                    <strong>Huidige Status:</strong> 
                    <span class="status-badge status-<?php echo $post['status']; ?>">
                        <?php echo $post['status'] === 'published' ? 'Gepubliceerd' : 'Concept'; ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 3rem; flex-wrap: wrap;">
            <button type="submit" name="save_and_view" class="btn btn-primary">
                💾 Opslaan & Bekijken
            </button>
            <button type="submit" name="save_and_admin" class="btn btn-secondary">
                💾 Opslaan & Terug naar Admin
            </button>
            <a href="<?php echo getBaseUrl(); ?>/admin/" class="btn btn-secondary">
                ❌ Annuleren
            </a>
        </div>
    </form>
</section>

<!-- Danger Zone -->
<section class="glass-card" style="border-color: rgba(220, 38, 38, 0.3); background: rgba(220, 38, 38, 0.05);">
    <h2 style="color: #dc2626; margin-bottom: 1rem;">⚠️ Gevaarlijke Acties</h2>
    <p style="margin-bottom: 2rem;">Deze acties kunnen niet ongedaan worden gemaakt.</p>
    
    <button onclick="confirmDelete(<?php echo $post['id']; ?>, '<?php echo addslashes($post['title']); ?>')" 
            class="btn btn-danger">
        🗑️ Verslag Definitief Verwijderen
    </button>
</section>

<script>
// Auto-save functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('edit-form');
    const inputs = form.querySelectorAll('input, textarea, select');
    
    let autoSaveTimeout;
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                // Save to localStorage as backup
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                localStorage.setItem('criminal_minds_edit_backup_<?php echo $postId; ?>', JSON.stringify(data));
                
                // Show auto-save indicator
                showAutoSaveIndicator();
            }, 2000); // Save after 2 seconds of no input
        });
    });
    
    // Load backup on page load
    const backup = localStorage.getItem('criminal_minds_edit_backup_<?php echo $postId; ?>');
    if (backup && confirm('Er is een automatische backup gevonden. Wil je deze herstellen?')) {
        const data = JSON.parse(backup);
        Object.keys(data).forEach(key => {
            const element = form.querySelector(`[name="${key}"]`);
            if (element) {
                element.value = data[key];
            }
        });
    }
    
    // Clear backup on successful submit
    form.addEventListener('submit', function() {
        localStorage.removeItem('criminal_minds_edit_backup_<?php echo $postId; ?>');
    });
});

function showAutoSaveIndicator() {
    // Create or update auto-save indicator
    let indicator = document.getElementById('auto-save-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'auto-save-indicator';
        indicator.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(34, 197, 94, 0.9);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.8rem;
            z-index: 10000;
        `;
        document.body.appendChild(indicator);
    }
    
    indicator.textContent = '💾 Automatisch opgeslagen';
    indicator.style.display = 'block';
    
    setTimeout(() => {
        indicator.style.display = 'none';
    }, 3000);
}
</script>

<?php include '../includes/footer.php'; ?>