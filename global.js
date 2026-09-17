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
        // Initialize express interest button
        initExpressInterest();
    } catch (error) {
        console.error('Error initializing express interest:', error);
    }
    
    try {
        // Initialize any other global components
        initGlobalComponents();
    } catch (error) {
        console.error('Error initializing global components:', error);
    }
    
    try {
        // Fix Simple Membership registration form
        initSimpleMembershipForm();
    } catch (error) {
        console.error('Error initializing simple membership form:', error);
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
                    } else if (file.type.startsWith('video/')) {
                        // For videos, show a video icon and filename
                        previewItem.innerHTML = `
                            <div class="video-preview-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                                </svg>
                            </div>
                            <span class="file-preview-name">${file.name}</span>
                        `;
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
                    // Skip Simple Membership registration forms - they have their own handling
                    if (form.classList.contains('swpm-registration-form')) {
                        return;
                    }

                    // Skip Contact Form 7 forms - they have their own AJAX handling
                    if (form.classList.contains('wpcf7-form')) {
                        return;
                    }

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
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        const menuClose = document.querySelector('.mobile-menu-close');
        const mainMenu = document.querySelector('.main-navigation');
        const overlay = document.querySelector('.mobile-menu-overlay');
        const siteHeader = document.getElementById('site-header');

        // Sticky Header Backdrop Darken on Scroll
        if (siteHeader) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 30) {
                    siteHeader.classList.add('is-scrolled');
                } else {
                    siteHeader.classList.remove('is-scrolled');
                }
            }, { passive: true });
        }

        function openMenu() {
            if (mainMenu) mainMenu.classList.add('active');
            if (menuToggle) {
                menuToggle.classList.add('active');
                menuToggle.setAttribute('aria-expanded', 'true');
            }
            if (overlay) overlay.classList.add('active');
            document.body.classList.add('menu-open');
            document.documentElement.classList.add('menu-open');
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        }

        function closeMenu() {
            if (mainMenu) mainMenu.classList.remove('active');
            if (menuToggle) {
                menuToggle.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
            if (overlay) overlay.classList.remove('active');
            document.body.classList.remove('menu-open');
            document.documentElement.classList.remove('menu-open');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        }

        if (menuToggle) {
            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                if (mainMenu && mainMenu.classList.contains('active')) {
                    closeMenu();
                } else {
                    openMenu();
                }
            });
        }

        if (menuClose) {
            menuClose.addEventListener('click', function(e) {
                e.stopPropagation();
                closeMenu();
            });
        }

        if (overlay) {
            overlay.addEventListener('click', closeMenu);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mainMenu && mainMenu.classList.contains('active')) {
                closeMenu();
            }
        });

        if (mainMenu) {
            const navLinks = mainMenu.querySelectorAll('a');
            navLinks.forEach(function(link) {
                link.addEventListener('click', closeMenu);
            });
        }
    } catch (error) {
        console.error('Error initializing mobile menu toggle:', error);
    }
}

/**
 * Initialize Express Interest button functionality
 */
function initExpressInterest() {
    try {
        const expressInterestBtns = document.querySelectorAll('.express-interest-btn');
        
        if (expressInterestBtns.length === 0) {
            return;
        }
        
        expressInterestBtns.forEach(function(btn) {
            // Skip if button is disabled (already interested)
            if (btn.disabled) {
                return;
            }
            
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const advertisementId = btn.getAttribute('data-advertisement-id');
                
                if (!advertisementId) {
                    showExpressInterestMessage(btn, 'Invalid advertisement ID', 'error');
                    return;
                }
                
                // Show loading state
                btn.disabled = true;
                const originalText = btn.textContent;
                btn.textContent = 'Sending...';
                
                // Get the AJAX URL
                const ajaxUrl = window.wpApiSettings ? window.wpApiSettings.root : '/wp-admin/admin-ajax.php';
                const nonce = window.wpApiSettings ? window.wpApiSettings.nonce : '';
                
                // Create form data
                const formData = new FormData();
                formData.append('action', 'express_advertisement_interest');
                formData.append('advertisement_id', advertisementId);
                formData.append('nonce', nonce);
                
                fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        btn.classList.add('interested');
                        btn.textContent = '✓ Interest Expressed';
                        showExpressInterestMessage(btn, data.data.message, 'success');
                        
                        // Update interests count if present
                        const wrapper = btn.closest('.express-interest-wrapper');
                        if (wrapper && data.data.interests_count) {
                            const countSpan = wrapper.querySelector('.interests-count');
                            if (countSpan) {
                                const count = data.data.interests_count;
                                countSpan.textContent = count + ' ' + (count === 1 ? 'person has' : 'people have') + ' expressed interest';
                            } else {
                                const newCountSpan = document.createElement('span');
                                newCountSpan.className = 'interests-count';
                                newCountSpan.textContent = data.data.interests_count + ' ' + (data.data.interests_count === 1 ? 'person has' : 'people have') + ' expressed interest';
                                wrapper.appendChild(newCountSpan);
                            }
                        }
                    } else {
                        btn.disabled = false;
                        btn.textContent = originalText;
                        showExpressInterestMessage(btn, data.data.message || 'An error occurred', 'error');
                    }
                })
                .catch(function(error) {
                    console.error('Express Interest Error:', error);
                    btn.disabled = false;
                    btn.textContent = originalText;
                    showExpressInterestMessage(btn, 'Network error. Please try again.', 'error');
                });
            });
        });
    } catch (error) {
        console.error('Error initializing express interest:', error);
    }
}

/**
 * Show message for express interest action
 * @param {HTMLElement} btn - The button element
 * @param {string} message - The message to display
 * @param {string} type - The message type (success or error)
 */
function showExpressInterestMessage(btn, message, type) {
    try {
        // Remove any existing messages
        const existingMessage = document.querySelector('.express-interest-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Create message element with SVG icon
        const messageEl = document.createElement('div');
        messageEl.className = 'express-interest-message ' + type;
        
        let iconSvg = '';
        if (type === 'success') {
            iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
        } else {
            iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
        }
        
        messageEl.innerHTML = iconSvg + '<span>' + message + '</span>';
        
        // Find the express-interest-section and insert the message there
        const section = btn.closest('.express-interest-section');
        if (section) {
            section.appendChild(messageEl);
        } else {
            // Fallback: insert after the wrapper
            const wrapper = btn.closest('.express-interest-wrapper');
            if (wrapper) {
                wrapper.parentElement.insertBefore(messageEl, wrapper.nextSibling);
            } else {
                btn.parentElement.insertBefore(messageEl, btn.nextSibling);
            }
        }
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            if (messageEl.parentElement) {
                messageEl.remove();
            }
        }, 5000);
    } catch (error) {
        console.error('Error showing express interest message:', error);
    }
}

/**
 * Initialize global components
 */
function initGlobalComponents() {
    try {
        // Add any other global component initializations here
        
        // Initialize image protection
        initImageProtection();
        
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
    
    try {
        // Initialize portfolio gallery
        initPortfolioGallery();
    } catch (error) {
        console.error('Error initializing portfolio gallery:', error);
    }
    
    try {
        // Initialize talent sticky quick links navigation
        initTalentStickyNav();
    } catch (error) {
        console.error('Error initializing talent sticky nav:', error);
    }
}

/**
 * Initialize image protection to prevent downloading
 */
function initImageProtection() {
    try {
        // Disable right-click on images
        document.addEventListener('contextmenu', function(e) {
            if (e.target.tagName === 'IMG') {
                e.preventDefault();
                return false;
            }
        });
        
        // Disable drag-and-drop for images
        document.addEventListener('dragstart', function(e) {
            if (e.target.tagName === 'IMG') {
                e.preventDefault();
                return false;
            }
        });
        
        // Disable image selection
        document.addEventListener('selectstart', function(e) {
            if (e.target.tagName === 'IMG') {
                e.preventDefault();
                return false;
            }
        });
        
        // Disable keyboard shortcuts that might affect images
        document.addEventListener('keydown', function(e) {
            // Disable F12 (Developer Tools), Ctrl+Shift+I (Inspect), Ctrl+U (View Source)
            if (
                e.keyCode === 123 || // F12
                (e.ctrlKey && e.shiftKey && e.keyCode === 73) || // Ctrl+Shift+I
                (e.ctrlKey && e.shiftKey && e.keyCode === 74) || // Ctrl+Shift+J
                (e.ctrlKey && e.keyCode === 85) || // Ctrl+U
                (e.ctrlKey && e.keyCode === 83) // Ctrl+S
            ) {
                e.preventDefault();
                return false;
            }
        });

        // Additional protection: Prevent copying images
        document.addEventListener('copy', function(e) {
            const selection = window.getSelection();
            if (selection) {
                const range = selection.getRangeAt(0);
                if (range) {
                    const img = range.startContainer.parentElement.querySelector('img');
                    if (img) {
                        e.preventDefault();
                    }
                }
            }
        });

        // Enhanced protection: Add protection attributes to all images on the page
        function protectAllImages() {
            const allImages = document.querySelectorAll('img');
            allImages.forEach(img => {
                img.setAttribute('oncontextmenu', 'return false;');
                img.setAttribute('ondragstart', 'return false;');
                img.setAttribute('onselectstart', 'return false;');
                img.style.userSelect = 'none';
                img.style.webkitUserSelect = 'none';
                img.style.mozUserSelect = 'none';
                img.style.msUserSelect = 'none';
                img.style.webkitTouchCallout = 'none';
                img.style.webkitUserDrag = 'none';
                
                // Add event listeners to prevent saving via right-click context menu
                img.addEventListener('mousedown', function(e) {
                    if (e.button === 2) { // Right mouse button
                        e.preventDefault();
                        return false;
                    }
                });
            });
        }

        // Run initially
        protectAllImages();

        // Run again after a short delay to catch any images that loaded later
        setTimeout(protectAllImages, 1000);

        // Watch for new images added to the page dynamically
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            if (node.tagName === 'IMG') {
                                // Newly added image
                                node.setAttribute('oncontextmenu', 'return false;');
                                node.setAttribute('ondragstart', 'return false;');
                                node.setAttribute('onselectstart', 'return false;');
                                node.style.userSelect = 'none';
                                node.style.webkitUserSelect = 'none';
                                node.style.mozUserSelect = 'none';
                                node.style.msUserSelect = 'none';
                                node.style.webkitTouchCallout = 'none';
                                node.style.webkitUserDrag = 'none';
                                
                                node.addEventListener('mousedown', function(e) {
                                    if (e.button === 2) { // Right mouse button
                                        e.preventDefault();
                                        return false;
                                    }
                                });
                            } else {
                                // Check if the added node contains images
                                const images = node.querySelectorAll('img');
                                images.forEach(img => {
                                    img.setAttribute('oncontextmenu', 'return false;');
                                    img.setAttribute('ondragstart', 'return false;');
                                    img.setAttribute('onselectstart', 'return false;');
                                    img.style.userSelect = 'none';
                                    img.style.webkitUserSelect = 'none';
                                    img.style.mozUserSelect = 'none';
                                    img.style.msUserSelect = 'none';
                                    img.style.webkitTouchCallout = 'none';
                                    img.style.webkitUserDrag = 'none';
                                    
                                    img.addEventListener('mousedown', function(e) {
                                        if (e.button === 2) { // Right mouse button
                                            e.preventDefault();
                                            return false;
                                        }
                                    });
                                });
                            }
                        }
                    });
                }
            });
        });

        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    } catch (error) {
        console.error('Error initializing image protection:', error);
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
 * Initialize portfolio gallery
 */
function initPortfolioGallery() {
    try {
        // Check if we're on a single talent page
        if (!document.querySelector('.talent-single-page')) {
            return;
        }
        
        const portfolioItems = document.querySelectorAll('.portfolio-item');
        const modal = document.getElementById('portfolio-modal');
        const modalImage = document.getElementById('portfolio-modal-image');
        const modalVideo = document.getElementById('portfolio-modal-video');
        const modalVideoPlayer = document.getElementById('portfolio-modal-player');
        const modalIframe = document.getElementById('portfolio-modal-iframe');
        const modalClose = document.querySelector('.portfolio-modal-close');
        const modalPrev = document.querySelector('.portfolio-modal-prev');
        const modalNext = document.querySelector('.portfolio-modal-next');
        const modalCurrent = document.getElementById('portfolio-modal-current');
        const modalTotal = document.getElementById('portfolio-modal-total');
        
        if (!modal || !modalImage || !modalVideo || !modalClose || !modalPrev || !modalNext) {
            console.log('Some modal elements are missing, skipping gallery initialization');
            return;
        }
        
        let currentIndex = 0;
        const items = Array.from(portfolioItems);
        let touchStartX = 0;
        let touchEndX = 0;
        
        // Set total items count
        if (modalTotal) {
            modalTotal.textContent = items.length;
        }
        
        // Hide navigation if there's only one item
        if (items.length <= 1) {
            modalPrev.style.display = 'none';
            modalNext.style.display = 'none';
        }
        
        // Open modal when clicking on a portfolio item
        items.forEach((item, index) => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                currentIndex = index;
                openModal();
            });
        });
        
        // Close modal when clicking on the close button
        modalClose.addEventListener('click', closeModal);
        
        // Close modal when clicking outside the content
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
        
        // Navigation
        modalPrev.addEventListener('click', showPrevItem);
        modalNext.addEventListener('click', showNextItem);
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (modal.classList.contains('active')) {
                if (e.key === 'Escape') {
                    closeModal();
                } else if (e.key === 'ArrowLeft') {
                    showPrevItem();
                } else if (e.key === 'ArrowRight') {
                    showNextItem();
                }
            }
        });
        
        // Touch swipe support for mobile
        modal.addEventListener('touchstart', function(e) {
            if (e.target === modalImage || (modalVideo && modalVideo.contains(e.target))) {
                touchStartX = e.changedTouches[0].screenX;
            }
        }, { passive: true });
        
        modal.addEventListener('touchend', function(e) {
            if (e.target === modalImage || (modalVideo && modalVideo.contains(e.target))) {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            }
        }, { passive: true });
        
        function handleSwipe() {
            const swipeThreshold = 50;
            if (touchStartX - touchEndX > swipeThreshold) {
                showNextItem();
            } else if (touchEndX - touchStartX > swipeThreshold) {
                showPrevItem();
            }
        }

        function stopVideoPlayback() {
            if (modalVideoPlayer) {
                try {
                    modalVideoPlayer.pause();
                    modalVideoPlayer.currentTime = 0;
                    modalVideoPlayer.src = '';
                    modalVideoPlayer.removeAttribute('src');
                    modalVideoPlayer.load();
                } catch (e) {}
                modalVideoPlayer.style.display = 'none';
            }
            if (modalIframe) {
                modalIframe.src = '';
                modalIframe.style.display = 'none';
            }
        }

        function renderModalMedia(currentItem) {
            if (!currentItem) return;
            const itemType = currentItem.getAttribute('data-type');

            // Reset video players first
            stopVideoPlayback();

            if (itemType === 'video') {
                let videoUrl = currentItem.getAttribute('data-video-url');
                if (!videoUrl) {
                    const innerVid = currentItem.querySelector('video');
                    if (innerVid) {
                        videoUrl = innerVid.src || (innerVid.querySelector('source') ? innerVid.querySelector('source').src : '');
                    }
                }
                // Strip hash if needed for clean playback
                if (videoUrl) {
                    videoUrl = videoUrl.replace(/#t=[\d\.]+$/, '');
                }

                modalImage.style.display = 'none';
                modalImage.src = '';
                modalVideo.style.display = 'flex';

                if (videoUrl) {
                    if (modalVideoPlayer) {
                        modalVideoPlayer.style.display = 'block';
                        modalVideoPlayer.src = videoUrl;
                        modalVideoPlayer.load();
                        var playPromise = modalVideoPlayer.play();
                        if (playPromise !== undefined) {
                            playPromise.catch(function(err) {
                                console.log('Autoplay prevented or waiting user interaction:', err);
                            });
                        }
                    }
                } else {
                    const videoId = currentItem.getAttribute('data-video-id');
                    const platform = currentItem.getAttribute('data-platform');
                    let embedUrl = '';
                    if (platform === 'youtube' && videoId) {
                        embedUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0&modestbranding=1';
                    } else if (platform === 'vimeo' && videoId) {
                        embedUrl = 'https://player.vimeo.com/video/' + videoId + '?autoplay=1';
                    } else if (videoId) {
                        embedUrl = 'https://player.vimeo.com/video/' + videoId + '?autoplay=1';
                    }

                    if (embedUrl && modalIframe) {
                        modalIframe.style.display = 'block';
                        modalIframe.src = embedUrl;
                    }
                }
            } else {
                modalVideo.style.display = 'none';
                const imgTag = currentItem.querySelector('img');
                if (imgTag) {
                    modalImage.src = imgTag.src;
                    modalImage.style.display = 'block';
                }
            }
        }

        function openModal() {
            const currentItem = items[currentIndex];
            renderModalMedia(currentItem);
            
            if (modalCurrent) {
                modalCurrent.textContent = currentIndex + 1;
            }
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            if (items.length > 1) {
                modalPrev.style.display = 'flex';
                modalNext.style.display = 'flex';
            } else {
                modalPrev.style.display = 'none';
                modalNext.style.display = 'none';
            }
        }
        
        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = '';
            modalImage.src = '';
            modalImage.style.display = 'none';
            if (modalVideo) {
                modalVideo.style.display = 'none';
            }
            stopVideoPlayback();
        }
        
        function showPrevItem() {
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            updateModalItem();
        }
        
        function showNextItem() {
            currentIndex = (currentIndex + 1) % items.length;
            updateModalItem();
        }
        
        function updateModalItem() {
            const currentItem = items[currentIndex];
            renderModalMedia(currentItem);
            if (modalCurrent) {
                modalCurrent.textContent = currentIndex + 1;
            }
        }

        // Initialize video preview frames in portfolio grid
        document.querySelectorAll('.portfolio-item.video-item video').forEach(function(vid) {
            const initFrame = function() {
                if (vid.currentTime === 0) {
                    try { vid.currentTime = 0.001; } catch (e) {}
                }
            };
            vid.addEventListener('loadedmetadata', initFrame);
            if (vid.readyState >= 1) {
                initFrame();
            }
        });
        
    } catch (error) {
        console.error('Error initializing portfolio gallery:', error);
    }
}

/**
 * Initialize talent sticky quick links navigation
 */
function initTalentStickyNav() {
    try {
        const stickyNav = document.querySelector('.talent-sticky-nav');
        if (!stickyNav) return;

        const navLinks = stickyNav.querySelectorAll('.nav-pill');
        if (!navLinks.length) return;

        // Smooth scroll with offset for sticky nav bar
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (!targetId || !targetId.startsWith('#')) return;

                const targetEl = document.querySelector(targetId);
                if (targetEl) {
                    e.preventDefault();
                    const navHeight = stickyNav.offsetHeight || 60;
                    const elPosition = targetEl.getBoundingClientRect().top + window.pageYOffset;
                    const offsetPosition = elPosition - navHeight - 20;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });

                    // Update active class
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // IntersectionObserver for scroll spy
        const sectionIds = Array.from(navLinks)
            .map(link => link.getAttribute('href'))
            .filter(href => href && href.startsWith('#') && href.length > 1);

        const sections = sectionIds
            .map(id => document.querySelector(id))
            .filter(el => el !== null);

        if ('IntersectionObserver' in window && sections.length) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const activeId = '#' + entry.target.id;
                        navLinks.forEach(link => {
                            if (link.getAttribute('href') === activeId) {
                                link.classList.add('active');
                            } else {
                                link.classList.remove('active');
                            }
                        });
                    }
                });
            }, {
                rootMargin: '-20% 0px -65% 0px'
            });

            sections.forEach(sec => observer.observe(sec));
        }
    } catch (error) {
        console.error('Error in initTalentStickyNav:', error);
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
        
        // For success messages, show a popup with View Portfolios button
        if (type === 'success') {
            showSuccessPopup(message);
            return;
        }
        
        // Create notification element for non-success types
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
 * Utility function to show a success popup with View Portfolios button
 * @param {string} message - The message to display
 */
function showSuccessPopup(message) {
    try {
        // Remove any existing popups
        const existingPopup = document.querySelector('.success-popup-overlay');
        if (existingPopup) {
            existingPopup.remove();
        }
        
        // Create popup element using the existing success popup style
        const popup = document.createElement('div');
        popup.className = 'success-popup-overlay';
        
        popup.innerHTML = `
            <div class="success-popup">
                <div class="success-card">
                    <div class="success-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <h2 class="success-title">Thank You!</h2>
                    <p class="success-message">${message}</p>
                    <a href="/talent/" class="btn btn-primary">View Portfolios</a>
                </div>
            </div>
        `;
        
        // Add to document
        document.body.appendChild(popup);
        
        // Close when clicking outside the popup
        popup.addEventListener('click', function(e) {
            if (e.target === popup) {
                if (popup.parentNode) {
                    popup.remove();
                }
            }
        });
        
        // No auto removal - user must click View Portfolios or outside to close
    } catch (error) {
        console.error('Error showing success popup:', error);
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

/**
 * Initialize Simple Membership Form enhancements
 */
function initSimpleMembershipForm() {
    try {
        // Fix terms and privacy policy links
        const termsRow = document.querySelector('.swpm-terms-row a');
        if (termsRow) {
            termsRow.href = '/terms-and-conditions/';
            termsRow.target = '_blank';
        }

        const ppRow = document.querySelector('.swpm-pp-row a');
        if (ppRow) {
            ppRow.href = '/privacy-policy/';
            ppRow.target = '_blank';
        }

        // Add login link after the submit button
        const submitSection = document.querySelector('.swpm-registration-submit-section');
        if (submitSection && !document.querySelector('.swpm-login-link-row')) {
            const loginRow = document.createElement('div');
            loginRow.className = 'swpm-login-link-row';
            loginRow.innerHTML = '<p>Already A Member? <a href="/membership-login/">Login</a></p>';
            submitSection.appendChild(loginRow);
        }
        
        // Remove the swpm-form-username-input-wrap class from username field
        // This class is being added by the plugin but we don't want it
        const usernameInput = document.querySelector('input[name="username"], input#username');
        if (usernameInput) {
            usernameInput.closest('.swpm-form-username-input-wrap')?.classList.remove('swpm-form-username-input-wrap');
            // Also remove from parent element
            const parentWrap = usernameInput.closest('[class*="swpm-form-"]');
            if (parentWrap && parentWrap.classList.contains('swpm-form-username-input-wrap')) {
                parentWrap.classList.remove('swpm-form-username-input-wrap');
            }
        }
        
        // Use MutationObserver to remove swpm-form-username-input-wrap class when it gets added
        const usernameField = document.querySelector('.swpm-form-username-input-wrap');
        if (usernameField) {
            usernameField.classList.remove('swpm-form-username-input-wrap');
        }
        
        // Also watch for the class being added later
        const usernameObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const target = mutation.target;
                    if (target.classList.contains('swpm-form-username-input-wrap')) {
                        target.classList.remove('swpm-form-username-input-wrap');
                    }
                }
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && node.classList && node.classList.contains('swpm-form-username-input-wrap')) {
                            node.classList.remove('swpm-form-username-input-wrap');
                        }
                    });
                }
            });
        });
        
        // Start observing the body for class changes
        const bodyForObserver = document.querySelector('body');
        if (bodyForObserver) {
            usernameObserver.observe(bodyForObserver, { 
                childList: true, 
                subtree: true,
                attributes: true,
                attributeFilter: ['class']
            });
        }
        
        // Enhanced registration form handling with proper validation before submission
        const registrationForm = document.querySelector('form.swpm-registration-form');
        if (registrationForm) {
            // Create preloader element
            const preloader = document.createElement('div');
            preloader.className = 'swpm-form-preloader';
            preloader.innerHTML = `
                <div class="swpm-preloader-spinner"></div>
                <span class="swpm-preloader-text">Processing...</span>
            `;
            preloader.style.cssText = 'display: none; align-items: center; justify-content: center; gap: 10px; padding: 10px; color: #666;';
            
            // Add spinner CSS if not already added
            if (!document.getElementById('swpm-preloader-styles')) {
                const style = document.createElement('style');
                style.id = 'swpm-preloader-styles';
                style.textContent = `
                    .swpm-preloader-spinner {
                        width: 20px;
                        height: 20px;
                        border: 2px solid #f3f3f3;
                        border-top: 2px solid #b2122d;
                        border-radius: 50%;
                        animation: swpm-spin 0.8s linear infinite;
                    }
                    @keyframes swpm-spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                    .swpm-submit-btn-loading {
                        position: relative !important;
                        color: transparent !important;
                    }
                `;
                document.head.appendChild(style);
            }
            
            const submitButton = registrationForm.querySelector('.swpm-submit, .swpm-registration-submit-button');
            
            // Store original button text
            if (submitButton && !submitButton.getAttribute('data-original-text')) {
                submitButton.setAttribute('data-original-text', submitButton.value || submitButton.textContent || 'Register');
            }
            
            // Override form submit to check for required fields first
            registrationForm.addEventListener('submit', function(e) {
                // Check all required fields in the form (including dynamically shown ones)
                const requiredFields = registrationForm.querySelectorAll('[required]:not([disabled])');
                let allFilled = true;
                let firstMissingField = null;
                let phoneValid = true;
                
                for (let field of requiredFields) {
                    // Skip hidden fields that aren't visible to the user
                    if (field.offsetParent === null) {
                        continue;
                    }
                    
                    if (!field.value || field.value.trim() === '') {
                        allFilled = false;
                        if (!firstMissingField) {
                            firstMissingField = field;
                        }
                        // Add error indication to the field
                        field.style.borderColor = '#e74c3c';
                    } else {
                        // If this is a phone field, validate its format
                        if (field.type === 'tel' && field.value) {
                            // Simple phone validation - check if it has at least 7 digits
                            const phoneRegex = /[\d\-\+\(\)\s]{7,}/;
                            if (!phoneRegex.test(field.value)) {
                                phoneValid = false;
                                field.style.borderColor = '#e74c3c';
                                if (!firstMissingField) {
                                    firstMissingField = field;
                                }
                            } else {
                                // Remove error indication if field is valid
                                field.style.borderColor = '';
                            }
                        } else {
                            // Remove error indication if field is filled
                            field.style.borderColor = '';
                        }
                    }
                }
                
                // Check if "I agree to the Terms and Conditions" checkbox is required and checked
                const agreementCheckbox = registrationForm.querySelector('input[type="checkbox"][name*="agreement" i], input[type="checkbox"][name*="terms" i], input[type="checkbox"][name*="policy" i]');
                if (agreementCheckbox && agreementCheckbox.hasAttribute('required') && !agreementCheckbox.checked) {
                    allFilled = false;
                    agreementCheckbox.style.borderColor = '#e74c3c';
                } else if (agreementCheckbox) {
                    agreementCheckbox.style.borderColor = '';
                }
                
                if (!allFilled || !phoneValid) {
                    e.preventDefault(); // Stop form submission
                    
                    // Focus on the first missing or invalid field
                    if (firstMissingField) {
                        firstMissingField.focus();
                        
                        // Scroll to the field if needed
                        firstMissingField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    
                    // Show appropriate error message
                    if (!phoneValid) {
                        if (typeof showNotification === 'function') {
                            showNotification('Please enter a valid phone number.', 'error');
                        } else {
                            alert('Please enter a valid phone number.');
                        }
                    } else {
                        if (typeof showNotification === 'function') {
                            showNotification('Please fill in all required fields.', 'error');
                        } else {
                            alert('Please fill in all required fields.');
                        }
                    }
                    
                    // Don't show preloader or disable button since form didn't submit
                    return false;
                }
                
                // If all required fields are filled and valid, proceed with submission
                if (submitButton) {
                    // Add loading class for visual feedback
                    submitButton.classList.add('swpm-submit-btn-loading');
                    
                    // Store original value before changing
                    const originalText = submitButton.getAttribute('data-original-text') || 'Register';
                    submitButton.setAttribute('data-original-value', originalText);
                    
                    // Show preloader in the form
                    const formParent = submitButton.closest('.swpm-registration-form') || submitButton.parentElement;
                    if (formParent) {
                        // Insert preloader after the submit button
                        submitButton.insertAdjacentElement('afterend', preloader);
                        preloader.style.display = 'flex';
                    }
                    
                    // Allow form to submit normally - the page will navigate away
                    // The button state will be reset when the page reloads (in case of errors)
                }
            });
            
            // Add event listener for account type changes to handle conditional fields
            const accountTypeSelect = registrationForm.querySelector('#account_type');
            if (accountTypeSelect) {
                accountTypeSelect.addEventListener('change', function() {
                    // Small delay to allow any conditional fields to render
                    setTimeout(() => {
                        // Re-validate the form when account type changes
                        const requiredFields = registrationForm.querySelectorAll('[required]:not([disabled])');
                        let allFilled = true;
                        
                        for (let field of requiredFields) {
                            // Skip hidden fields that aren't visible to the user
                            if (field.offsetParent === null) {
                                continue;
                            }
                            
                            if (!field.value || field.value.trim() === '') {
                                allFilled = false;
                                field.style.borderColor = '#e74c3c';
                            } else {
                                // Remove error indication if field is filled
                                field.style.borderColor = '';
                            }
                        }
                        
                        // Ensure button is always enabled - never disabled
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.classList.remove('swpm-submit-btn-loading');
                            
                            // Restore original text
                            const originalText = submitButton.getAttribute('data-original-text') || 'Register';
                            submitButton.value = originalText;
                            if (submitButton.tagName.toLowerCase() !== 'input') {
                                submitButton.textContent = originalText;
                            }
                            
                            // Hide preloader if it exists
                            if (preloader) {
                                preloader.style.display = 'none';
                            }
                        }
                    }, 300); // 300ms delay to allow conditional fields to appear
                });
            }
            
        // Use MutationObserver to ensure the submit button is never disabled
        // The plugin might try to disable it, so we need to constantly watch and re-enable it
        if (submitButton) {
            // First, ensure it's not disabled
            submitButton.disabled = false;
            
            // Use MutationObserver to watch for the disabled attribute
            const buttonObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'disabled') {
                        const target = mutation.target;
                        // If button becomes disabled, immediately re-enable it
                        if (target.disabled) {
                            target.disabled = false;
                            target.removeAttribute('disabled');
                        }
                    }
                });
            });
            
            buttonObserver.observe(submitButton, {
                attributes: true,
                attributeFilter: ['disabled']
            });
            
            // Also use setInterval as a fallback to constantly ensure button is enabled
            const buttonCheckInterval = setInterval(function() {
                if (submitButton && submitButton.disabled) {
                    submitButton.disabled = false;
                    submitButton.removeAttribute('disabled');
                }
            }, 100);
            
            // Stop the interval when the form is submitted (page will reload)
            registrationForm.addEventListener('submit', function() {
                // Just before submit, ensure button is enabled
                submitButton.disabled = false;
                submitButton.removeAttribute('disabled');
                
                // Clear the interval after submission
                clearInterval(buttonCheckInterval);
                buttonObserver.disconnect();
            });
        }
            
            // Listen for form validation errors - re-enable button if there are errors after submission
            // This handles the case where the server sends back validation errors
            if (submitButton) {
                // Check if the page loaded with errors (indicates server-side validation failed)
                const errorElements = registrationForm.querySelectorAll('.swpm-errors, .error, .chaitu-error-messages');
                if (errorElements.length > 0) {
                    // Re-enable the button if there are server-side errors
                    submitButton.disabled = false;
                    submitButton.classList.remove('swpm-submit-btn-loading');
                    
                    // Restore original text
                    const originalText = submitButton.getAttribute('data-original-text') || 'Register';
                    submitButton.value = originalText;
                    if (submitButton.tagName.toLowerCase() !== 'input') {
                        submitButton.textContent = originalText;
                    }
                    
                    // Hide preloader if it exists
                    preloader.style.display = 'none';
                }
            }
        }
    } catch (error) {
        console.error('Error in initSimpleMembershipForm function:', error);
    }
}