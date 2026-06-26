/**
 * Share Profile Functionality
 * Handles generating and copying shareable profile URLs
 */

(function($) {
    'use strict';

    // Get AJAX URL from localized script or fallback
    var ajaxUrl = (typeof shareProfileData !== 'undefined' && shareProfileData.ajaxurl) 
        ? shareProfileData.ajaxurl 
        : '/wp-admin/admin-ajax.php';

    // Share button handler
    $(document).on('click', '.share-profile-btn', function(e) {
        e.preventDefault();
        
        const talentId = $(this).data('talent-id');
        const nonce = $(this).data('nonce');
        
        if (!talentId || !nonce) {
            console.error('Missing talent ID or nonce');
            alert('Error: Missing required data. Please refresh the page.');
            return;
        }
        
        // Show loading state
        const $btn = $(this);
        const originalText = $btn.text();
        $btn.text('Generating...').prop('disabled', true);
        
        // Generate shareable token
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'generate_shareable_token',
                talent_id: talentId,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    // Show share modal with URL
                    showShareModal(response.data.public_url, response.data.token);
                    // Change button to "View Link" after successful generation
                    $btn.text('View Link').prop('disabled', false).css({
                        'background': '#df1d3d',
                        'cursor': 'pointer'
                    });
                } else {
                    alert('Error: ' + (response.data.message || 'Failed to generate share link'));
                    $btn.text(originalText).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                alert('An error occurred while generating the share link. Please try again.');
                $btn.text(originalText).prop('disabled', false);
            }
        });
    });
    
    // Toggle sharing switch handler (iOS-style Switch)
    $(document).on('change', '.sharing-toggle-switch', function() {
        const talentId = $(this).data('talent-id');
        const enabled = this.checked; // Checkbox checked state
        const nonce = $(this).data('nonce');
        
        if (!talentId || !nonce) {
            console.error('Missing talent ID or nonce');
            alert('Error: Missing required data. Please refresh the page.');
            return;
        }
        
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'toggle_public_sharing',
                talent_id: talentId,
                enabled: enabled,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    if (enabled) {
                        // Enable sharing - update UI
                        $('.share-profile-btn[data-talent-id="' + talentId + '"]').prop('disabled', false).text('Share Profile').css({
                            'background': '#df1d3d',
                            'cursor': 'pointer'
                        });
                        // Update switch appearance
                        $(`.sharing-toggle-switch[data-talent-id="${talentId}"]`).parent().find('span:last-child').css({
                            'background-color': '#df1d3d'
                        });
                        $(`.sharing-toggle-switch[data-talent-id="${talentId}"]`).parent().find('span span').css({
                            'left': '28px'
                        });
                        if (response.data.public_url) {
                            showShareModal(response.data.public_url, response.data.token);
                        }
                    } else {
                        // Disable sharing - update UI
                        $('.share-profile-btn[data-talent-id="' + talentId + '"]').prop('disabled', true).text('Enable Sharing First').css({
                            'background': '#555',
                            'cursor': 'not-allowed'
                        });
                        // Update switch appearance
                        $(`.sharing-toggle-switch[data-talent-id="${talentId}"]`).parent().find('span:last-child').css({
                            'background-color': '#ccc'
                        });
                        $(`.sharing-toggle-switch[data-talent-id="${talentId}"]`).parent().find('span span').css({
                            'left': '4px'
                        });
                        hideShareModal();
                    }
                } else {
                    alert('Error: ' + (response.data.message || 'Failed to update sharing status'));
                    // Revert the switch on error
                    $('.sharing-toggle-switch[data-talent-id="' + talentId + '"]').prop('checked', !enabled);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                alert('An error occurred while updating sharing status. Please try again.');
                // Revert the switch on error
                $('.sharing-toggle-switch[data-talent-id="' + talentId + '"]').prop('checked', !enabled);
            }
        });
    });
    
    // Copy URL button handler
    $(document).on('click', '.copy-share-url-btn', function() {
        const $input = $('#share-profile-url');
        $input.select();
        document.execCommand('copy');
        
        const $btn = $(this);
        const originalText = $btn.text();
        $btn.text('Copied!').addClass('copied');
        
        setTimeout(function() {
            $btn.text(originalText).removeClass('copied');
        }, 2000);
    });
    
    // Close modal handler
    $(document).on('click', '.close-share-modal, .share-modal-overlay', function() {
        hideShareModal();
    });
    
    // Regenerate token handler
    $(document).on('click', '.regenerate-token-btn', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to generate a new share link? The old link will stop working.')) {
            return;
        }
        
        const talentId = $(this).data('talent-id');
        const nonce = $(this).data('nonce');
        
        if (!talentId || !nonce) {
            console.error('Missing talent ID or nonce');
            alert('Error: Missing required data. Please refresh the page.');
            return;
        }
        
        const $btn = $(this);
        const originalText = $btn.text();
        $btn.text('Regenerating...').prop('disabled', true);
        
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'generate_shareable_token',
                talent_id: talentId,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    showShareModal(response.data.public_url, response.data.token);
                } else {
                    alert('Error: ' + (response.data.message || 'Failed to regenerate link'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                alert('An error occurred while regenerating the link. Please try again.');
            },
            complete: function() {
                $btn.text(originalText).prop('disabled', false);
            }
        });
    });
    
    // Show share modal
    function showShareModal(url, token) {
        $('#share-profile-url').val(url);
        $('.share-modal').fadeIn(300);
        $('.share-modal-overlay').fadeIn(300);
    }
    
    // Hide share modal
    function hideShareModal() {
        $('.share-modal').fadeOut(300);
        $('.share-modal-overlay').fadeOut(300);
    }
    
    // Initialize on page load
    $(document).ready(function() {
        // Check existing sharing status and update button states
        $('.sharing-toggle-switch').each(function() {
            const talentId = $(this).data('talent-id');
            const is_enabled = $(this).prop('checked');
            
            if (!is_enabled) {
                $('.share-profile-btn[data-talent-id="' + talentId + '"]').prop('disabled', true).text('Enable Sharing First');
            } else {
                $('.share-profile-btn[data-talent-id="' + talentId + '"]').prop('disabled', false).text('Share Profile');
            }
        });
    });
    
})(jQuery);
