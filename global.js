/**
 * Global JavaScript file for the theme
 * This file contains common functionality used across the site
 */

// Wrap everything in a try-catch to prevent errors from breaking the entire script
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Initialize global functionality
        initializeGlobalScripts();
    } catch (error) {
        console.error('Error initializing global scripts:', error);
    }
});

/**
 * Initialize all global scripts
 */
function initializeGlobalScripts() {
    try {
        // Initialize file upload previews
        initFileUploadPreviews();
    } catch (error) {
        console.error('Error initializing file upload previews:', error);
    }
    
    try {
        // Initialize form enhancements
        initFormEnhancements();
    } catch (error) {
        console.error('Error initializing form enhancements:', error);
    }
    
    try {
        // Initialize mobile menu toggle
        initMobileMenuToggle();
    } catch (error) {
        console.error('Error initializing mobile menu toggle:', error);
    }
    
    try {
        // Initialize any other global components
        initGlobalComponents();
    } catch (error) {
        console.error('Error initializing global components:', error);
    }
}

/**
 * Initialize file upload previews
 */
function initFileUploadPreviews() {
    try {
        const fileUploadAreas = document.querySelectorAll('.file-upload-area');
        
        fileUploadAreas.forEach(function(area) {
            try {
                const fileInput = area.querySelector('input[type="file"]');
                const previewContainer = area.querySelector('.file-upload-preview');
                
                if (fileInput && previewContainer) {
                    fileInput.addEventListener('change', function(e) {
                        handleFilePreview(e.target, previewContainer);
                    });
                }
            } catch (error) {
                console.error('Error initializing file upload area:', error);
            }
        });
    } catch (error) {
        console.error('Error initializing file upload previews:', error);
    }
}

/**
 * Handle file preview display
 * @param {HTMLInputElement} fileInput - The file input element
 * @param {HTMLElement} previewContainer - The container for previews
 */
function handleFilePreview(fileInput, previewContainer) {
    try {
        previewContainer.innerHTML = '';
        
        if (fileInput.files && fileInput.files.length > 0) {
            Array.from(fileInput.files).forEach(function(file) {
                try {
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
                } catch (error) {
                    console.error('Error creating file preview:', error);
                }
            });
        }
    } catch (error) {
        console.error('Error handling file preview:', error);
    }
}

/**
 * Initialize form enhancements
 */
function initFormEnhancements() {
    try {
        // Add confirmation to delete forms
        const deleteForms = document.querySelectorAll('form[data-confirm]');
        deleteForms.forEach(function(form) {
            try {
                form.addEventListener('submit', function(e) {
                    const message = form.getAttribute('data-confirm');
                    if (!confirm(message || 'Are you sure you want to delete this?')) {
                        e.preventDefault();
                    }
                });
            } catch (error) {
                console.error('Error adding confirmation to form:', error);
            }
        });
    } catch (error) {
        console.error('Error initializing delete form confirmations:', error);
    }
    
    try {
        // Handle form loading states
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            try {
                form.addEventListener('submit', function() {
                    const submitButton = form.querySelector('input[type="submit"], button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.value = 'Processing...';
                    }
                });
            } catch (error) {
                console.error('Error adding loading state to form:', error);
            }
        });
    } catch (error) {
        console.error('Error initializing form loading states:', error);
    }
}

/**
 * Initialize mobile menu toggle
 */
function initMobileMenuToggle() {
    try {
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
    } catch (error) {
        console.error('Error initializing mobile menu toggle:', error);
    }
}

/**
 * Initialize global components
 */
function initGlobalComponents() {
    try {
        // Add any other global component initializations here
        
        // Example: Initialize tooltips
        initTooltips();
    } catch (error) {
        console.error('Error initializing tooltips:', error);
    }
    
    try {
        // Example: Initialize modals
        initModals();
    } catch (error) {
        console.error('Error initializing modals:', error);
    }
}

/**
 * Initialize tooltips
 */
function initTooltips() {
    try {
        const tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(function(element) {
            try {
                element.addEventListener('mouseenter', function() {
                    // Tooltip implementation would go here
                });
            } catch (error) {
                console.error('Error adding tooltip to element:', error);
            }
        });
    } catch (error) {
        console.error('Error initializing tooltips:', error);
    }
}

/**
 * Initialize modals
 */
function initModals() {
    try {
        const modalTriggers = document.querySelectorAll('[data-modal-trigger]');
        modalTriggers.forEach(function(trigger) {
            try {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    const modalId = trigger.getAttribute('data-modal-trigger');
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.add('active');
                    }
                });
            } catch (error) {
                console.error('Error adding modal trigger:', error);
            }
        });
    } catch (error) {
        console.error('Error initializing modal triggers:', error);
    }
    
    try {
        const modalClosers = document.querySelectorAll('[data-modal-close]');
        modalClosers.forEach(function(closer) {
            try {
                closer.addEventListener('click', function() {
                    const modal = closer.closest('.modal');
                    if (modal) {
                        modal.classList.remove('active');
                    }
                });
            } catch (error) {
                console.error('Error adding modal closer:', error);
            }
        });
    } catch (error) {
        console.error('Error initializing modal closers:', error);
    }
}

/**
 * Utility function to show a notification
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, error, warning, info)
 */
function showNotification(message, type = 'info') {
    try {
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
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                if (notification.parentNode) {
                    notification.remove();
                }
            });
        }
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    } catch (error) {
        console.error('Error showing notification:', error);
    }
}

/**
 * Utility function to make AJAX requests
 * @param {string} url - The URL to send the request to
 * @param {object} data - The data to send
 * @param {string} method - The HTTP method (GET, POST, etc.)
 * @returns {Promise}
 */
function ajaxRequest(url, data = {}, method = 'POST') {
    try {
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
    } catch (error) {
        console.error('Error in ajaxRequest function:', error);
        throw error;
    }
}