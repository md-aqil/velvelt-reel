console.log('Testing global.js syntax');
/**
 * Global JavaScript file for the theme
 * This file contains common functionality used across the site
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize global functionality
    initializeGlobalScripts();
});

/**
 * Initialize all global scripts
 */
function initializeGlobalScripts() {
    // Initialize file upload previews
    initFileUploadPreviews();
    
    // Initialize form enhancements
    initFormEnhancements();
    
    // Initialize mobile menu toggle
    initMobileMenuToggle();
    
    // Initialize any other global components
    initGlobalComponents();
}

/**
 * Initialize file upload previews
 */
function initFileUploadPreviews() {
    const fileUploadAreas = document.querySelectorAll('.file-upload-area');
    
    fileUploadAreas.forEach(function(area) {
        const fileInput = area.querySelector('input[type="file"]');
        const previewContainer = area.querySelector('.file-upload-preview');
        
        if (fileInput && previewContainer) {
            fileInput.addEventListener('change', function(e) {
                handleFilePreview(e.target, previewContainer);
            });
        }
    });
}

/**
 * Handle file preview display
 * @param {HTMLInputElement} fileInput - The file input element
 * @param {HTMLElement} previewContainer - The container for previews
 */
function handleFilePreview(fileInput, previewContainer) {
    previewContainer.innerHTML = '';
    
    if (fileInput.files && fileInput.files.length > 0) {
        Array.from(fileInput.files).forEach(function(file) {
            const previewItem = document.createElement('div');
            previewItem.className = 'file-preview-item';
            
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <span class="file-preview-name">${file.name}</span>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                previewItem.innerHTML = `<span class="file-preview-name">${file.name}</span>`;
            }
            
            previewContainer.appendChild(previewItem);
        });
    }
}

/**
 * Initialize form enhancements
 */
function initFormEnhancements() {
    // Add confirmation to delete forms
    const deleteForms = document.querySelectorAll('form[data-confirm]');
    deleteForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const message = form.getAttribute('data-confirm');
            if (!confirm(message || 'Are you sure you want to delete this?')) {
                e.preventDefault();
            }
        });
    });
    
    // Handle form loading states
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function() {
            const submitButton = form.querySelector('input[type="submit"], button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.value = 'Processing...';
            }
        });
    });
}

/**
 * Initialize mobile menu toggle
 */
function initMobileMenuToggle() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mainMenu = document.querySelector('.main-navigation');
    
    if (menuToggle && mainMenu) {
        menuToggle.addEventListener('click', function() {
            mainMenu.classList.toggle('toggled');
            menuToggle.classList.toggle('toggled');
            
            const expanded = menuToggle.getAttribute('aria-expanded') === 'true' || false;
            menuToggle.setAttribute('aria-expanded', !expanded);
        });
    }
}

/**
 * Initialize global components
 */
function initGlobalComponents() {
    // Add any other global component initializations here
    
    // Example: Initialize tooltips
    initTooltips();
    
    // Example: Initialize modals
    initModals();
}

/**
 * Initialize tooltips
 */
function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(function(element) {
        element.addEventListener('mouseenter', function() {
            // Tooltip implementation would go here
        });
    });
}

/**
 * Initialize modals
 */
function initModals() {
    const modalTriggers = document.querySelectorAll('[data-modal-trigger]');
    modalTriggers.forEach(function(trigger) {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = trigger.getAttribute('data-modal-trigger');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
            }
        });
    });
    
    const modalClosers = document.querySelectorAll('[data-modal-close]');
    modalClosers.forEach(function(closer) {
        closer.addEventListener('click', function() {
            const modal = closer.closest('.modal');
            if (modal) {
                modal.classList.remove('active');
            }
        });
    });
}

/**
 * Utility function to show a notification
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, error, warning, info)
 */
function showNotification(message, type = 'info') {
    // Remove any existing notifications
    const existingNotification = document.querySelector('.global-notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `global-notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Add close functionality
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', function() {
        notification.remove();
    });
    
    // Auto remove after 5 seconds
    setTimeout(function() {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

/**
 * Utility function to make AJAX requests
 * @param {string} url - The URL to send the request to
 * @param {object} data - The data to send
 * @param {string} method - The HTTP method (GET, POST, etc.)
 * @returns {Promise}
 */
function ajaxRequest(url, data = {}, method = 'POST') {
    const formData = new FormData();
    
    // Add data to form
    for (const key in data) {
        if (data.hasOwnProperty(key)) {
            formData.append(key, data[key]);
        }
    }
    
    // Add nonce for WordPress security if available
    const nonce = document.querySelector('input[name="_wpnonce"]');
    if (nonce) {
        formData.append('_wpnonce', nonce.value);
    }
    
    return fetch(url, {
        method: method,
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .catch(error => {
        console.error('AJAX Error:', error);
        throw error;
    });
}