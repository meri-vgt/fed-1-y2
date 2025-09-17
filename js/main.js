/**
 * Criminal Minds Blog - Interactive JavaScript
 */

// Loading Screen Management
document.addEventListener('DOMContentLoaded', function() {
    const loadingScreen = document.getElementById('loading-screen');
    
    // Simulate loading time for dramatic effect
    setTimeout(() => {
        loadingScreen.classList.add('fade-out');
        
        // Remove loading screen from DOM after animation
        setTimeout(() => {
            loadingScreen.style.display = 'none';
        }, 500);
        
        // Add fade-in animation to main content
        document.body.classList.add('fade-in');
    }, 1500);
});

// Search Enhancement
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    const searchForm = document.querySelector('.search-form');
    
    if (searchInput) {
        // Add search suggestions or real-time search if needed
        searchInput.addEventListener('input', function() {
            // Could add real-time search here
        });
        
        // Enhance search experience
        searchInput.addEventListener('focus', function() {
            this.parentElement.classList.add('search-focused');
        });
        
        searchInput.addEventListener('blur', function() {
            this.parentElement.classList.remove('search-focused');
        });
    }
});

// Smooth Scrolling for Anchor Links
document.addEventListener('DOMContentLoaded', function() {
    const anchors = document.querySelectorAll('a[href^="#"]');
    
    anchors.forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Enhanced Card Interactions
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.glass-card, .post-preview');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const title = form.querySelector('input[name="title"]');
    const content = form.querySelector('textarea[name="content"]');
    const author = form.querySelector('input[name="author"]');
    
    let isValid = true;
    
    // Remove previous error classes
    form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
    
    // Validate title
    if (title && title.value.trim().length < 3) {
        title.classList.add('error');
        showNotification('Titel moet minimaal 3 karakters bevatten', 'error');
        isValid = false;
    }
    
    // Validate content
    if (content && content.value.trim().length < 10) {
        content.classList.add('error');
        showNotification('Inhoud moet minimaal 10 karakters bevatten', 'error');
        isValid = false;
    }
    
    // Validate author
    if (author && author.value.trim().length < 1) {
        author.classList.add('error');
        showNotification('Auteur is verplicht', 'error');
        isValid = false;
    }
    
    return isValid;
}

// Notification System
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">&times;</button>
    `;
    
    // Add notification styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'error' ? 'rgba(220, 38, 38, 0.9)' : 'rgba(34, 197, 94, 0.9)'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        backdrop-filter: blur(10px);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 1rem;
        animation: slideIn 0.3s ease-out;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Confirm Delete
function confirmDelete(postId, postTitle) {
    const confirmed = confirm(`Weet je zeker dat je "${postTitle}" wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.`);
    
    if (confirmed) {
        const base = (window.APP_BASE_URL || '').replace(/\/$/, '');
        window.location.href = `${base}/admin/delete.php?id=${postId}`;
    }
    
    return false;
}

// Rich Text Editor Enhancement (Simple)
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea[name="content"]');
    
    textareas.forEach(textarea => {
        // Add simple formatting shortcuts
        textarea.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case 'b':
                        e.preventDefault();
                        insertFormatting(this, '**', '**');
                        break;
                    case 'i':
                        e.preventDefault();
                        insertFormatting(this, '*', '*');
                        break;
                }
            }
        });
        
        // Auto-resize textarea
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
});

function insertFormatting(textarea, start, end) {
    const startPos = textarea.selectionStart;
    const endPos = textarea.selectionEnd;
    const selectedText = textarea.value.substring(startPos, endPos);
    
    const replacement = start + selectedText + end;
    
    textarea.value = textarea.value.substring(0, startPos) + replacement + textarea.value.substring(endPos);
    
    // Restore cursor position
    textarea.selectionStart = startPos + start.length;
    textarea.selectionEnd = startPos + start.length + selectedText.length;
    textarea.focus();
}

// Dynamic Background Animation
document.addEventListener('DOMContentLoaded', function() {
    // Add subtle parallax effect on scroll
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallax = document.body;
        const speed = scrolled * 0.5;
        
        parallax.style.backgroundPosition = `center ${speed}px`;
    });
});

// Admin Panel Enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Add status toggle for posts
    const statusToggles = document.querySelectorAll('.status-toggle');
    
    statusToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const currentStatus = this.dataset.status;
            const newStatus = currentStatus === 'published' ? 'draft' : 'published';
            
            // Make AJAX request to update status
            const base = (window.APP_BASE_URL || '').replace(/\/$/, '');
            fetch(`${base}/admin/toggle-status.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: postId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showNotification('Fout bij het wijzigen van status', 'error');
                }
            })
            .catch(error => {
                showNotification('Fout bij het wijzigen van status', 'error');
            });
        });
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .error {
        border-color: #dc2626 !important;
        box-shadow: 0 0 20px rgba(220, 38, 38, 0.3) !important;
    }
    
    .search-focused {
        transform: scale(1.05);
    }
`;
document.head.appendChild(style);