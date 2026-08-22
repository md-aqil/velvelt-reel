jQuery(document).ready(function($) {
    $('#velvet-newsletter-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('.velvet-newsletter-submit');
        var $msg = $form.find('.velvet-newsletter-msg');
        var emailVal = $form.find('input[name="email"]').val();
        var nonceVal = $form.find('input[name="newsletter_nonce"]').val();

        $submitBtn.prop('disabled', true).text('SUBMITTING...');
        $msg.hide().removeClass('success error');

        $.ajax({
            url: VelvetFooter.ajaxurl,
            type: 'POST',
            data: {
                action: 'velvet_subscribe_newsletter',
                email: emailVal,
                newsletter_nonce: nonceVal
            },
            success: function(response) {
                if (response.success) {
                    $msg.css('color', '#4ECA64').text(response.data.message).fadeIn();
                    $form.find('input[name="email"]').val('');
                } else {
                    $msg.css('color', '#FF4D4D').text(response.data.message || 'An error occurred.').fadeIn();
                }
            },
            error: function() {
                $msg.css('color', '#FF4D4D').text('Server error. Please try again.').fadeIn();
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text('SUBMIT');
            }
        });
    });
});
