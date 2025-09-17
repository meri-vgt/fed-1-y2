/**
 * Text formatting functions for Criminal Minds blog platform
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize text editor features only if we're on a page with content textareas
    const textareas = document.querySelectorAll('textarea[name="content"]');
    if (textareas.length > 0) {
        initializeTextEditor();
        initializeTextEditorButtons();
    }
});

/**
 * Initialize text editor with formatting buttons and keyboard shortcuts
 */
function initializeTextEditor() {
    // Find all textareas that should have formatting features
    const textareas = document.querySelectorAll('textarea[name="content"]');
    
    textareas.forEach(textarea => {
        // Add event listeners for keyboard shortcuts
        textarea.addEventListener('keydown', handleKeyboardShortcuts);
    });
}

/**
 * Initialize formatting buttons with direct event listeners
 */
function initializeTextEditorButtons() {
    // Add event listeners to all formatting buttons (if they exist)
    const buttons = document.querySelectorAll('[data-format]');
    if (buttons.length > 0) {
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const formatType = this.getAttribute('data-format');
                formatSelection(formatType);
            });
        });
    }
}

/**
 * Handle keyboard shortcuts for text formatting
 */
function handleKeyboardShortcuts(e) {
    // Check if Ctrl or Cmd key is pressed
    if (e.ctrlKey || e.metaKey) {
        switch (e.key) {
            case 'b': // Bold
            case 'B':
                e.preventDefault();
                formatSelection('bold');
                break;
            case 'i': // Italic
            case 'I':
                e.preventDefault();
                formatSelection('italic');
                break;
            case 'u': // Underline
            case 'U':
                e.preventDefault();
                formatSelection('underline');
                break;
            case 's': // Strikethrough
            case 'S':
                e.preventDefault();
                formatSelection('strikethrough');
                break;
        }
    }
}

/**
 * Apply formatting to selected text
 */
function formatSelection(formatType) {
    const textarea = document.activeElement;
    
    // If not focused on a textarea, find the content textarea
    let targetTextarea = textarea;
    if (textarea.tagName !== 'TEXTAREA' || textarea.name !== 'content') {
        targetTextarea = document.querySelector('textarea[name="content"]');
        if (!targetTextarea) return;
    }
    
    const start = targetTextarea.selectionStart;
    const end = targetTextarea.selectionEnd;
    const selectedText = targetTextarea.value.substring(start, end);
    const beforeText = targetTextarea.value.substring(0, start);
    const afterText = targetTextarea.value.substring(end);
    
    // Don't format if no text is selected
    if (start === end) {
        // Only show alert on actual button clicks, not keyboard shortcuts
        if (event && event.type === 'click') {
            alert('Selecteer tekst om te formatteren');
        }
        return;
    }
    
    let formattedText = '';
    let tag = '';
    
    switch (formatType) {
        case 'bold':
            tag = 'strong';
            break;
        case 'italic':
            tag = 'em';
            break;
        case 'underline':
            tag = 'u';
            break;
        case 'strikethrough':
            tag = 's';
            break;
        default:
            return;
    }
    
    // Create the formatted text with tags
    formattedText = `<${tag}>${selectedText}</${tag}>`;
    
    // Update the textarea content
    targetTextarea.value = beforeText + formattedText + afterText;
    
    // Set cursor position after the formatted text
    const newCursorPos = start + formattedText.length;
    targetTextarea.setSelectionRange(newCursorPos, newCursorPos);
    
    // Focus back on textarea
    targetTextarea.focus();
}